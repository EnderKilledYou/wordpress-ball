<?php

class ScheduleHelper {

	public static function RebuildSchedule( $id ) {
		set_time_limit( 0 );
		$total_games = 3;

		if ( isset( $_REQUEST['total_games'] ) && is_numeric( $_REQUEST['total_games'] ) ) {
			$total_games = abs( (int) $_REQUEST['total_games'] );
		}
		$machines = MachineHelper::get_assignable_machines();
		if ( count( $machines ) === 0 ) {
			BallAdminNoticeHandler::AddError( "You need to add a machine first or remove one as draft" );

			return;
		}
		if ( ! isset( $_REQUEST['seasons'] ) || ! is_array( $_REQUEST['seasons'] ) ) {
			$seasons = [ $id ];
		} else {
			$seasons   = $_REQUEST['seasons'];
			$seasons[] = $id;
		}
		$scores = ScoreHelper::get_season_scores( $id );
		if ( count( $scores ) === 0 ) {
			BallAdminNoticeHandler::AddError( "You can't rebuild the schedule if there are no players." );

			return;

		}
		$players = PlayerHelper::map_scores_to_players( $scores );
		if ( count( $scores ) !== count( $players ) ) {
			BallAdminNoticeHandler::AddError( "Player season score / player count mismatch. Have you deleted something?" );

			return;

		}
		$finals = false;
		if ( isset( $_REQUEST['generate_finals'] ) && $_REQUEST['generate_finals'] === 'yes' ) {
			$finals = true;
		}
		$matches = MatchHelper::get_season_matches( $id );
		$lineup  = self::get_player_vs_count( $players, $seasons );

		foreach ( $matches as $match ) {
			$match_count = MatchHelper::get_match_count( $match->ID );
			$match_size  = MatchHelper::get_match_size( $match->ID );


			for ( $match_size_index = 0; $match_size_index < $match_size; $match_size_index ++ ) {
				$already_played = [];
				for ( $match_count_index = 0; $match_count_index < $match_count; $match_count_index ++ ) {

					$filter  = array_filter( $players, static function ( $e ) use ( $already_played ) {
						return ! in_array( $e->ID, $already_played, true );
					} );
					$player_id = PlayerHelper::get_player_with_least_games_in_season( $id, $filter );

					$all_but = array_values( array_filter( $players, static function ( $e ) use ( $already_played, $player_id ) {
						return $player_id !== $e->ID && !in_array( $e->ID, $already_played, false );
					} ) );


					if ( ! $finals ) {
						$lowest = self::find_lowest_played( $id, $lineup[ $player_id ], $all_but );
					} else {
						$lowest = self::find_lowest_played_finals( $player_id, $lineup, $all_but, $id );
					}
					if ( ! $lowest ) {
						continue;
					}
					$lowest_machine = MachineHelper::get_lowest_machine( $player_id, $lowest );

					GameHelper::create_game( $id, $match->ID, $total_games, $player_id, $lowest, $lowest_machine, $match_size_index, $match_count_index );
					MachineHelper::increment_total_games_played( $lowest_machine );
					if ( ! isset( $lineup[ $player_id ][ $lowest ] ) ) {
						$lineup[ $player_id ][ $lowest ] = 0;
					}
					if ( ! isset( $lineup[ $lowest ][ $player_id ] ) ) {
						$lineup[ $lowest ][ $player_id ] = 0;
					}
					$lineup[ $player_id ][ $lowest ] ++;
					$lineup[ $lowest ][ $player_id ] ++;
					$already_played[] = $player_id;
					$already_played[] = $lowest;


				}
			}


		}

		unset( $_REQUEST['generate_matches'] );


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
	 * @param WP_Post $matches
	 * @param WP_Post $players
	 * @param WP_Post $seasons
	 *
	 * @return array
	 */
	public static function get_player_vs_count( array $players, array $seasons ): array {
		$lineup = [];
		foreach ( $seasons as $season ) {
			$matches = MatchHelper::get_season_matches( $season );
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
	private static function find_lowest_played_finals( $player_id, array $counter, array $playable, $season_id ): int {
		$first_id     = $playable[0]->ID;
		$lowest       = $counter[ $first_id ];
		$lowest_index = 0;
		$avg          = self::get_player_score_avg( $player_id, $season_id );
		$lowest_dist  = abs( $avg - self::get_player_score_avg( $first_id, $season_id ) );
		$playerCount  = count( $playable );
		for ( $i = 1; $i < $playerCount; $i ++ ) {
			$play = $playable[ $i ];
			if ( ( $counter[ $play->ID ] === $lowest ) ) {
				$dist = abs( $avg - self::get_player_score_avg( $play->ID, $season_id ) );
				if ( $dist < $lowest_dist ) {
					$lowest_dist  = $dist;
					$lowest_index = $i;
					$i ++;

				}
			}
		}

		return $playable[ $lowest_index ]->ID;
	}

	private static function find_lowest_played( $season_id, array $counter, array $playable ): int {

		$first_id     = $playable[0]->ID;
		$lowest       = $counter[ $first_id ];
		$lowest_index = 0;

		$playerCount = count( $playable );
		for ( $i = 1; $i < $playerCount; $i ++ ) {
			$play = $playable[ $i ];
			if ( $counter[ $play->ID ] < $lowest ) {
				$lowest       = $counter[ $play->ID ];
				$lowest_index = $i;

			}

		}
		$total_same_lowest = array_filter( $playable, static function ( $play ) use ( $lowest, $counter ) {
			return $counter[ $play->ID ] === $lowest;
		} );
		if ( count( $total_same_lowest ) > 0 ) {
			return PlayerHelper::get_player_with_least_games_in_season( $season_id, $total_same_lowest );
		}

		return $playable[ $lowest_index ]->ID;
	}

	/**
	 * @param int $player_id
	 *
	 * @return float|int
	 */
	public static function get_player_score_avg( int $player_id, $season_id ) {
		$games       = GameHelper::get_player_games_by_season( $player_id, $season_id );
		$total_games = 0;
		foreach ( $games as $game ) {
			if ( ! GameHelper::is_game_complete( $game->ID, - 1 ) ) {
				continue;
			}
			$total_games ++;
		}
		if ( $total_games === 0 ) {
			return 0;
		}
		$score = GameHelper::get_total_player_points_for_season( $player_id, $season_id );

		return $score / $total_games;
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