<?php

class ScheduleHelper {

	public static function RebuildSchedule( $id ) {
		$total_games = 4;
		if ( isset( $_REQUEST['total_games'] ) && ctype_digit( $_REQUEST['total_games'] ) ) {
			$total_games = abs( (int) $_REQUEST['total_games'] );
		}
		$scores      = ScoreHelper::get_season_scores( $id );
		$players     = PlayerHelper::map_scores_to_players( $scores );
		$matches     = MatchHelper::get_season_matches( $id );
		$match_count = count( $matches );
		foreach ( $matches as $match ) {

			$lineup = self::get_player_vs_count( $matches, $players );
			foreach ( $players as $player ) {

				$player_id      = $player->ID;
				$all_but        = array_values( array_filter( $players, static function ( $e ) use ( $player_id ) {
					return $player_id !== $e->ID;
				} ) );
				$avg            = self::get_player_score_avg( $player_id );
				$lowest         = self::find_lowest_played( $lineup[ $player_id ], $all_but, $avg, -- $match_count === 0 );
				$lowest_machine = MachineHelper::get_lowest_machine( $player_id, $lowest );

				GameHelper::create_game( $id, $match->ID, $total_games, $player_id, $lowest, $lowest_machine );
				MachineHelper::increment_total_games_played( $lowest_machine );
			}

		}

		unset( $_REQUEST['generate_matches'] );
		wp_update_post( [
			'post_content' =>  '[season_matches]',
			'ID'           => $id,
		] );
		//	$matches_table = self::create_match_table( MatchHelper::get_season_matches( $id ) );


	}

	public static function filter_no_game_players( $match_id, $players ): array {

		$found_players = [];
		$games         = GameHelper::get_match_games( $match_id );
		$player_ids    = [];
		foreach ( $players as $key => $p ) {
			$player_ids[] = $p->ID;
		}

		foreach ( $games as $game ) {
			$p1_id           = GameHelper::get_player1_ID( $game->ID );
			$p2_id           = GameHelper::get_player2_ID( $game->ID );
			$found_players[] = $p1_id;
			$found_players[] = $p2_id;
		}

		return array_diff( array_unique( $player_ids, SORT_NUMERIC ), array_unique( $found_players, SORT_NUMERIC ) ); //get the missing ids

	}

	/**
	 * @param array $matches
	 *
	 * @return array
	 */
	public static function get_player_vs_count( array $matches, array $players ): array {
		$lineup = [];
		foreach ( $matches as $match ) {
			$games = GameHelper::get_match_games( $match->ID );
			foreach ( $games as $game ) {
				$player1_id = GameHelper::get_player1_ID( $game->ID );
				$player2_id = GameHelper::get_player2_ID( $game->ID );
				if ( ! array_key_exists( $player1_id, $lineup ) ) {
					$lineup[ $player1_id ] = [];
				}
				if ( ! array_key_exists( $player2_id, $lineup ) ) {
					$lineup[ $player2_id ] = [];
				}
				if ( ! array_key_exists( $player2_id, $lineup[ $player1_id ] ) ) {
					$lineup[ $player1_id ][ $player2_id ] = 0;
				}
				if ( ! array_key_exists( $player1_id, $lineup[ $player2_id ] ) ) {
					$lineup[ $player2_id ][ $player1_id ] = 0;
				}
				$lineup[ $player1_id ][ $player2_id ] ++;
				$lineup[ $player2_id ][ $player1_id ] ++;

			}

		}
		foreach ( $players as $player_ ) {

			$lineup[ $player_->ID ] = [];
			foreach ( $players as $player ) {
				if ( ! isset( $lineup[ $player_->ID ][ $player->ID ] ) && $player_->ID !== $player->ID ) {
					$lineup[ $player_->ID ][ $player->ID ] = 0;
				}
			}
		}

		return $lineup;
	}

	/**
	 * @param array $counter
	 * @param WP_Post[] $playable
	 *
	 * @return int
	 */


	private static function find_lowest_played( array $counter, array $playable, $avg, bool $final_week ): int {

		$first_id     = $playable[0]->ID;
		$lowest       = $counter[ $first_id ];
		$lowest_index = 0;
		$i            = 0;
		$distance     =
		$lowest_dist = abs( $avg - self::get_player_score_avg( $first_id ) );
		foreach ( $playable as $play ) {
			if ( $i === 0 ) {
				$i ++;
				continue;
			}

			if ( $counter[ $play->ID ] < $lowest ) {
				$lowest       = $counter[ $play->ID ];
				$lowest_index = $i;
				$lowest_dist  = abs( $avg - self::get_player_score_avg( $play->ID ) );
				$i ++;
				continue;
			}
			if ( ( $counter[ $play->ID ] === $lowest ) && $final_week ) {
				$dist = abs( $avg - self::get_player_score_avg( $play->ID ) );
				if ( $dist < $lowest_dist ) {
					$lowest_dist  = $dist;
					$lowest_index = $i;
					$i ++;
					continue;
				}
			}
			$i ++;

		}

		return $playable[ $lowest_index ]->ID;
	}

	/**
	 * @param int $player_id
	 *
	 * @return float|int
	 */
	public static function get_player_score_avg( int $player_id ) {
		$score       = GameHelper::get_total_player_points( $player_id );
		$total_games = count( GameHelper::get_all_player_complete_games( $player_id ) );
		if ( $total_games > 0 ) {
			$avg = $score / $total_games;
		} else {
			$avg = 0;
		}

		return $avg;
	}

	private static function create_game_table( WP_POST $game ): string {
		$tbl_header  = '<h2 class="screen-reader-text">Matches </h2>';
		$game_count  = GameHelper::get_game_count( $game->ID );
		$game_header = "";
		$game_score1 = "";
		$game_score2 = "";

		for ( $i = 0; $i < $game_count; $i ++ ) {
			$index       = $i + 1;
			$game_score1 .= "<td>" . GameHelper::get_player1_score( $game->ID, $i ) . "</td>";
			$game_score2 .= "<td>" . GameHelper::get_player2_score( $game->ID, $i ) . "</td>";
			$game_header .= "<th scope='col' id='game' class=' column-title column-primary  '>
			<span>Game $index</span>
		</th>";
		}
		$tbl = '<table class="wp-list-table widefat fixed striped table-view-excerpt posts">
	<thead>
	<tr>
 
		<th scope="col" id="game" class=" column-title column-primary  ">
			<span></span>
		</th>' .
		       "
		$game_header 

		"

		       .
		       '.
		</tr>
	</thead>

	<tbody id="the-list">
	';

		$link = get_post_permalink( $game->ID );
		$tbl  .= '<tr><td><a href="' . $link . '">' . $game->post_title . '</a></td></tr>';

		$tbl .= '
			 </tbody>

	 

</table>';

		return $tbl_header . $tbl;

	}

	private static function create_match_table( WP_POST $match ): string {

		$tbl = '<table class="wp-list-table widefat fixed striped table-view-excerpt posts">
	<thead>
	<tr>
 
		<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
			<span>Match</span>
		</th>
		</tr>
	</thead>

	<tbody id="the-list">
	';

		$link = get_post_permalink( $match->ID );
		$tbl  .= '<tr><td><a href="' . $link . '">' . $match->post_title . '</a></td></tr>';

		$tbl .= '
			 </tbody>

	 

</table>';

		return $tbl;

	}

}