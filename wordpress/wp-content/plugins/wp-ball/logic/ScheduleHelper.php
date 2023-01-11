<?php

class ScheduleHelper {

	public static function RebuildSchedule( $id ) {

		$matches = MatchHelper::get_season_matches( $id );
		$scores  = ScoreHelper::get_season_scores( $id );
		$players = PlayerHelper::map_scores_to_players( $scores );
		if ( ! $players ) {
			return;
		}
		$lineup = self::get_player_vs_count( $matches, $players );

		foreach ( $matches as $match ) {
			$missing_game_player_ids = self::filter_no_game_players( $match->ID, $players );
			while ( count( $missing_game_player_ids ) > 0 ) {
				$player_id = array_shift( $missing_game_player_ids );
				if ( count( $missing_game_player_ids ) === 0 ) {
					break; //no one to play with odd number
				}

				$lowest = self::find_lowest_played( $lineup[ $player_id ], $missing_game_player_ids );
				GameHelper::create_game( $id, $match->ID, $player_id, $lowest );
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
	 * @param array $players
	 * @param array $lineup
	 *
	 * @return int
	 */


	private static function find_lowest_played( array $counter, $playable ) {

		$lowest       = $counter[ $playable[0] ];
		$lowest_index = 0;
		$i            = 0;

		foreach ( $playable as $play ) {

			if ( $counter[ $play ] < $lowest ) {
				$lowest       = $counter[ $play ];
				$lowest_index = $i;
			}
			$i ++;
		}

		return $playable[ $lowest_index ];
	}
}