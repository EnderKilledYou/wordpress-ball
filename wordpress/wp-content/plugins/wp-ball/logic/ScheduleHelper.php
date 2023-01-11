<?php

class ScheduleHelper {

	public static function RebuildSchedule( $id ) {
		$scores  = ScoreHelper::get_season_scores( $id );
		$players = PlayerHelper::map_scores_to_players( $scores );
		$matches = MatchHelper::get_season_matches( $id );
		foreach ( $matches as $match ) {
			for ( $i = 0; $i < 4; $i ++ ) {
				$lineup = self::get_player_vs_count( $matches, $players );
				foreach ( $players as $player ) {
					$existing_games = GameHelper::get_player_games_for_match( $player->ID, $match->ID );
					if ( count( $existing_games ) > $i ) {
						continue;
					}
					$player_id = $player->ID;
					$all_but   = array_filter( $players, static function ( $e ) use ( $player_id ) {
						return $player_id !== $e->ID;
					} );
					$avg       = self::get_player_score_avg( $player_id );
					$lowest    = self::find_lowest_played( $lineup[ $player_id ], $all_but, $avg );
					GameHelper::create_game( $id, $match->ID, $player_id, $lowest );

				}
			}
		}


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


	private static function find_lowest_played( array $counter, array $playable, $avg ): int {

		$lowest       = $counter[ $playable[0]->ID ];
		$lowest_index = 0;
		$i            = 0;
		$distance     =
		$lowest_dist = abs( $avg - self::get_player_score_avg( $playable[0]->ID ) );
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
			if ( $counter[ $play->ID ] === $lowest ) {
				$dist = abs( $avg - self::get_player_score_avg( $play->ID ) );
				if ( $dist < $lowest_dist ) {
					$lowest_dist  = $dist;
					$lowest_index = $i;
					$i ++;
					continue;
				}
				$i ++;
			}

		}

		return $playable[ $lowest_index ];
	}

	/**
	 * @param int $player_id
	 *
	 * @return float|int
	 */
	public static function get_player_score_avg( int $player_id ) {
		$score       = GameHelper::get_total_player_points( $player_id );
		$total_games = count( GameHelper::get_all_player_games( $player_id ) );
		if ( $total_games > 0 ) {
			$avg = $score / $total_games;
		} else {
			$avg = 0;
		}

		return $avg;
	}
}