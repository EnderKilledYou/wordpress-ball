<?php

class GameHelper {
	/**
	 * @return WP_Post[]
	 */
	public static function get_match_games( int $match_id ): array {
		return get_posts( [
			'post_type'      => WPBallObjectsRepository::GAME_POST_TYPE,
			'posts_per_page' => - 1,
			'post_status'    => BallPostSaveHandler::$all_posts,
			'meta_query'     => array(

				array(
					'key'   => self::$match_id,
					'value' => $match_id,
					//   'compare' => 'IN'
				)
			),
		] );
	}

	public static function get_match_games_by_group( int $match_id, int $group_index ): array {
		return get_posts( [
			'post_type'      => WPBallObjectsRepository::GAME_POST_TYPE,
			'posts_per_page' => - 1,
			'post_status'    => BallPostSaveHandler::$all_posts,
			'meta_query'     => array(
				array(
					'key'   => self::$match_count_index,
					'value' => $group_index
				),
				array(
					'key'   => self::$match_id,
					'value' => $match_id,
					//   'compare' => 'IN'
				)
			),
		] );
	}

	/**
	 * @return WP_Post[]
	 */
	public static function get_player_games_for_match( $player_id, $match_id ): array {
		return get_posts( [
			'post_type'      => WPBallObjectsRepository::GAME_POST_TYPE,
			'posts_per_page' => - 1,
			'post_status'    => BallPostSaveHandler::$all_posts,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'   => self::$match_id,
					'value' => $match_id,
				),

				array(
					'relation' => 'OR',
					// meta query takes an array of arrays, watch out for this!
					array(
						'key'   => self::$player_1,
						'value' => $player_id,
						//   'compare' => 'IN'
					),
					array(
						'key'   => self::$player_2,
						'value' => $player_id,
						//   'compare' => 'IN'
					)
				)
			),
		] );
	}

	/**
	 * @return WP_Post[]
	 */
	public static function get_player_games_lost( $player_id ): array {
		return get_posts( [
			'post_type'      => WPBallObjectsRepository::GAME_POST_TYPE,
			'posts_per_page' => - 1,
			'post_status'    => BallPostSaveHandler::$all_posts,
			'meta_query'     => array(

				// meta query takes an array of arrays, watch out for this!
				array(
					'key'   => self::$loser_id,
					'value' => $player_id,
					//   'compare' => 'IN'
				),

			),
		] );
	}

	/**
	 * @return WP_Post[]
	 */
	public static function get_won_games( $player_id ): array {
		return get_posts( [
			'post_type'      => WPBallObjectsRepository::GAME_POST_TYPE,
			'posts_per_page' => - 1,
			'post_status'    => BallPostSaveHandler::$all_posts,
			'meta_query'     => array(

				// meta query takes an array of arrays, watch out for this!
				array(
					'key'   => self::$winner_id,
					'value' => $player_id,
					//   'compare' => 'IN'
				),

			),
		] );
	}

	/**
	 * @return WP_Post[]
	 */
	public static function get_lost_games_by_season( $player_id, $season_id ): array {
		return get_posts( [
			'post_type'      => WPBallObjectsRepository::GAME_POST_TYPE,
			'posts_per_page' => - 1,
			'post_status'    => BallPostSaveHandler::$all_posts,
			'meta_query'     => array(

				// meta query takes an array of arrays, watch out for this!
				array(
					'key'   => self::$loser_id,
					'value' => $player_id,
					//   'compare' => 'IN'
				),
				array( 'key' => self::$season_id, 'value' => $season_id )

			),
		] );
	}

	/**
	 * @return WP_Post[]
	 */
	public static function get_won_games_by_season( $player_id, $season_id ): array {
		return get_posts( [
			'post_type'      => WPBallObjectsRepository::GAME_POST_TYPE,
			'posts_per_page' => - 1,
			'post_status'    => BallPostSaveHandler::$all_posts,
			'meta_query'     => array(

				// meta query takes an array of arrays, watch out for this!
				array(
					'key'   => self::$match_winner,
					'value' => $player_id,
					//   'compare' => 'IN'
				),
				array( 'key' => self::$season_id, 'value' => $season_id )

			),
		] );
	}

	/**
	 * @return WP_Post[]
	 */
	public static function get_games_by_season( $season_id ): array {
		return get_posts( [
				'post_type'      => WPBallObjectsRepository::GAME_POST_TYPE,
				'posts_per_page' => - 1,
				'post_status'    => BallPostSaveHandler::$all_posts,
				'meta_query'     => array(

					array( 'key' => self::$season_id, 'value' => $season_id )
				)
			]
		);

	}

	/**
	 * @return WP_Post[]
	 */
	public static function get_player_games_by_season( $player_id, $season_id ): array {
		return get_posts( [
			'post_type'      => WPBallObjectsRepository::GAME_POST_TYPE,
			'posts_per_page' => - 1,
			'post_status'    => BallPostSaveHandler::$all_posts,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'relation' => 'OR',
					// meta query takes an array of arrays, watch out for this!
					array(
						'key'   => self::$player_1,
						'value' => $player_id,
						//   'compare' => 'IN'
					),
					array(
						'key'   => self::$player_2,
						'value' => $player_id,
						//   'compare' => 'IN'
					),
					array( 'key' => self::$season_id, 'value' => $season_id )
				),
			)
		] );
	}

	/**
	 * @return WP_Post[]
	 */
	public static function get_all_player_complete_games( $player_id ): array {
		return get_posts( [
			'post_type'      => WPBallObjectsRepository::GAME_POST_TYPE,
			'posts_per_page' => - 1,
			'post_status'    => BallPostSaveHandler::$all_posts,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'   => self::$game_state,
					'value' => 'complete',
				),
				array(
					'relation' => 'OR',
					// meta query takes an array of arrays, watch out for this!
					array(
						'key'   => self::$player_1,
						'value' => $player_id,
						//   'compare' => 'IN'
					),
					array(
						'key'   => self::$player_2,
						'value' => $player_id,
						//   'compare' => 'IN'
					)
				)
			),
		] );
	}

	/**
	 * @return WP_Post[]
	 */
	public static function get_all_vs_player_games( $player1_id, $player2_id ): array {
		return get_posts( [
			'post_type'      => WPBallObjectsRepository::GAME_POST_TYPE,
			'posts_per_page' => - 1,
			'post_status'    => BallPostSaveHandler::$all_posts,
			'meta_query'     => array(
				array(
					'relation' => 'OR',
					// meta query takes an array of arrays, watch out for this!
					array(
						'key'   => self::$player_1,
						'value' => $player1_id,
						//   'compare' => 'IN'
					),
					array(
						'key'   => self::$player_2,
						'value' => $player2_id,
						//   'compare' => 'IN'
					),
					array(
						'relation' => 'OR',
						// meta query takes an array of arrays, watch out for this!
						array(
							'key'   => self::$player_1,
							'value' => $player2_id,
							//   'compare' => 'IN'
						),
						array(
							'key'   => self::$player_2,
							'value' => $player1_id,
							//   'compare' => 'IN'
						)
					)
				)
			),
		] );
	}

	/**
	 * @return WP_Post[]
	 */
	public static function get_all_player_games( $player_id ): array {
		return get_posts( [
			'post_type'      => WPBallObjectsRepository::GAME_POST_TYPE,
			'posts_per_page' => - 1,
			'post_status'    => BallPostSaveHandler::$all_posts,
			'meta_query'     => array(
				'relation' => 'OR',
				// meta query takes an array of arrays, watch out for this!
				array(
					'key'   => self::$player_1,
					'value' => $player_id,
					//   'compare' => 'IN'
				),
				array(
					'key'   => self::$player_2,
					'value' => $player_id,
					//   'compare' => 'IN'
				)
			),
		] );
	}

	private static string $loser_id = 'loser_id';
	private static string $winner_id = 'winner_id';
	private static string $match_winner = 'match_winner';
	private static string $match_loser = 'match_loser';
	private static string $player_1 = 'player_1';
	private static string $match_id = 'match_id';
	private static string $machine_id = 'machine_id';
	private static string $season_id = 'season_id';
	private static string $total_games = 'total_games';
	private static string $match_size_index = 'match_size_index';
	private static string $match_count_index = 'match_count_index';

	private static string $player_2 = 'player_2';
	private static string $player_1_score = 'player_1_score';
	private static string $player_2_score = 'player_2_score';
	private static string $game_state = 'game_state';
	private static string $game_state_complete = 'complete';
	private static string $game_state_pending = 'pending';

	public static function is_game_complete( $game_id, $game_count ): bool {


		return self::get_game_complete( $game_id, $game_count ) === self::$game_state_complete;
	}

	public static function get_game_complete( $game_id, $game_count ) {
		if ( $game_count === - 1 ) {

			$game_state = self::$game_state;
		} else {

			$game_state = self::get_game_state_key( $game_count );
		}

		return get_post_meta( $game_id, $game_state, true );
	}

	public static function update_game_complete( $game_id, bool $is_winner_player1, $game_count ): void {
		$player1_id = self::get_player1_ID( $game_id );
		$player2_id = self::get_player1_ID( $game_id );
		if ( $game_count === - 1 ) {
			$winner_key = self::$match_winner;
			$loser_key  = self::$match_loser;
			$game_state = self::$game_state;
		} else {
			$winner_key = self::get_winner_key( $game_count );
			$loser_key  = self::get_loser_key( $game_count );
			$game_state = self::get_game_state_key( $game_count );

		}
		if ( $is_winner_player1 ) {
			update_post_meta( $game_id, $winner_key, $player1_id );
			update_post_meta( $game_id, $loser_key, $player2_id );
		} else {
			update_post_meta( $game_id, $winner_key, $player2_id );
			update_post_meta( $game_id, $loser_key, $player1_id );
		}
		update_post_meta( $game_id, $game_state, self::$game_state_complete );
	}


	public static function update_player2_score( $game_id, $player2_score, $game_count = 0 ): void {
		update_post_meta( $game_id, self::get_player2_key( $game_count ), $player2_score );
	}

	public static function update_player1_score( $game_id, $player1_score, $game_count = 0 ): void {
		update_post_meta( $game_id, self::get_player1_key( $game_count ), $player1_score );
	}

	/**
	 * @param $game_id
	 *
	 * @return mixed
	 */
	public static function get_player1_ID( $game_id ) {
		return (int) get_post_meta( $game_id, self::$player_1, true );
	}

	/**
	 * @param $game_id
	 *
	 * @return mixed
	 */
	public static function get_player2_ID( $game_id ) {
		return (int) get_post_meta( $game_id, self::$player_2, true );
	}

	/**
	 * @param $game_id
	 *
	 * @return mixed
	 */
	public static function get_player1_score( $game_id, $game_count = 0 ) {
		return (int) get_post_meta( $game_id, self::get_player1_key( $game_count ), true );
	}


	/**
	 * @param $game_id
	 *
	 * @return mixed
	 */
	public static function get_winner_id( $game_id, $game_count ) {

		return (int) get_post_meta( $game_id, self::get_winner_key( $game_count ), true );
	}

	/**
	 * @param $game_id
	 *
	 * @return mixed
	 */
	public static function get_winner_score_by_id( $game_id, $game_count ) {
		$player1_id = self::get_player1_ID( $game_id );
		$winner_id  = self::get_winner_id( $game_id, $game_count );
		if ( (int) $winner_id === (int) $player1_id ) {
			return self::get_player1_score( $game_id );
		}

		return self::get_player2_score( $game_id );
	}

	/**
	 * @param $game_id
	 *
	 * @return mixed
	 */
	public static function get_player_score_from_game( int $player_id, int $game_id, $game_count ) {
		$player1_id = self::get_player1_ID( $game_id );

		if ( $player_id === (int) $player1_id ) {
			return self::get_player1_score( $game_id, $game_count );
		}

		return self::get_player2_score( $game_id, $game_count );
	}

	/**
	 * @param $game_id
	 *
	 * @return mixed
	 */
	public static function get_player2_score( $game_id, $game_count = 0 ) {
		return (int) get_post_meta( $game_id, self::get_player2_key( $game_count ), true );
	}

	/**
	 * @param $game_id
	 * the index of the game in the group,
	 *
	 * @return mixed
	 */
	public static function get_match_size_index( $game_id ) {
		return (int) get_post_meta( $game_id, self::$match_size_index, true );
	}

	/**
	 * @param $game_id
	 * the index of which group of matches
	 *
	 * @return mixed
	 */
	public static function get_match_count_index( $game_id ) {
		return (int) get_post_meta( $game_id, self::$match_size_index, true );
	}

	public static function create_game( int $season_id, int $match_id, $total_games, $player1_id, $player2_id, $lowest_machine_id, $match_size_index, $match_count_index ): void {
		$player1 = PlayerHelper::get_player( $player1_id );
		$player2 = PlayerHelper::get_player( $player2_id );
		$machine = get_post( $lowest_machine_id );
		$title   = "$player1->post_title vs $player2->post_title on " . $machine->post_title;
		$stat    = wp_insert_post( [
			'post_type'    => WPBallObjectsRepository::GAME_POST_TYPE,
			'post_name'    => $title,
			'post_title'   => $title,
			'post_content' => '[game_table]',
			'post_status ' => 'publish'
		] );
		wp_publish_post( $stat );
		update_post_meta( $stat, self::$season_id, $season_id );
		update_post_meta( $stat, self::$total_games, $total_games );
		update_post_meta( $stat, self::$match_size_index, $match_size_index );
		update_post_meta( $stat, self::$match_count_index, $match_count_index );
		update_post_meta( $stat, self::$match_id, $match_id );
		update_post_meta( $stat, self::$player_1, $player1_id );
		update_post_meta( $stat, self::$machine_id, $lowest_machine_id );
		update_post_meta( $stat, self::$player_2, $player2_id );
		for ( $i = 0; $i < $total_games; $i ++ ) {

			update_post_meta( $stat, self::get_game_state_key( $i ), self::$game_state_pending );
			self::update_player1_score( $stat, 0, $i );
			self::update_player2_score( $stat, 0, $i );
		}
		update_post_meta( $stat, self::$game_state, self::$game_state_pending );
	}

	public static function get_total_player_points( int $player_id ): int {

		$games = self::get_all_player_games( $player_id );
		$tmp   = [];
		foreach ( $games as $game ) {
			if ( get_post_meta( $game->ID, self::$game_state, true ) !== self::$game_state_complete ) {
				continue;
			}
			$season_id         = self::get_season_of_game( $game->ID );
			$tmp[ $season_id ] = "";
		}
		$season_ids = array_keys( $tmp );
		$sum        = 0;
		foreach ( $season_ids as $season_id ) {
			$games = self::get_player_games_by_season( $player_id, $season_id );
			foreach ( $games as $game ) {
				$game_count = self::get_game_count( $game->ID );
				for ( $i = 0; $i < $game_count; $i ++ ) {
					$sum .= self::get_player_score_from_game( $season_id, $player_id, $game_count );
				}

			}
		}

		return $sum;
	}

	public static function get_season_of_game( int $ID ): int {
		return (int) get_post_meta( $ID, self::$season_id, true );
	}

	public static function get_game_count( $game_id ): int {
		return (int) get_post_meta( $game_id, self::$total_games, true );
	}

	public static function get_game_machine( int $game_id ): int {
		return (int) get_post_meta( $game_id, self::$machine_id, true );

	}


	public static function get_highest_score( $player_id ): int {
		$games = self::get_all_player_games( $player_id );
		$top   = 0;
		$best  = 0;
		foreach ( $games as $game ) {
			$highest = self::get_highest_score_for_player_of_game( $player_id, $game->ID );
			if ( $highest > $top ) {
				$best = $game->ID;
				$top  = $highest;
			}
		}

		return $best;
	}

	public static function get_player_win_loss_of_game( $player_id, $game_id ): array {
		$win_count  = 0;
		$loss_count = 0;
		$game_count = self::get_game_count( $game_id );

		for ( $i = 0; $i < $game_count; $i ++ ) {


			if ( self::get_winner_id( $game_id, $i ) === $player_id ) {

				$win_count ++;
			} else {
				$loss_count ++;
			}
		}

		return [ $win_count, $loss_count ];
	}

	public static function get_best_win_loss( $player_id ) {
		$games = self::get_all_player_games( $player_id );
		$top   = 0;
		$best  = 0;
		foreach ( $games as $game ) {
			$game_id = $game->ID;
			[ $win_count, $loss_count ] = self::get_player_win_loss_of_game( $player_id, $game_id );


			if ( $win_count > $top ) {

				$best = $game_id;
				$top  = $win_count;
			}
		}

		return $best;
	}

	/**
	 * @param $game_count
	 *
	 * @return string
	 */
	public static function get_loser_key( $game_count ): string {
		return self::$loser_id . '_' . $game_count;
	}

	/**
	 * @param $game_count
	 *
	 * @return string
	 */
	public static function get_winner_key( $game_count ): string {
		return self::$winner_id . '_' . $game_count;
	}

	/**
	 * @param $game_count
	 *
	 * @return string
	 */
	public static function get_game_state_key( $game_count ): string {
		return self::$game_state . '_' . $game_count;
	}

	public static function get_match_winner( $game_id ) {
		return (int) get_post_meta( $game_id, self::$match_winner, true );
	}

	/**
	 * @param $game_count
	 *
	 * @return string
	 */
	public static function get_player1_key( $game_count ): string {
		return self::$player_1_score . '_' . $game_count;
	}

	/**
	 * @param $game_count
	 *
	 * @return string
	 */
	public static function get_player2_key( $game_count ): string {
		return self::$player_2_score . '_' . $game_count;
	}

	public static function get_match_id( int $game_id ) {
		return (int) get_post_meta( $game_id, self::$match_id, true );
	}

	public static function get_player_win_count_for_game( $player_id, int $game_id ): int {
		$total_games = self::get_game_count( $game_id );
		$wins        = 0;
		for ( $i = 0; $i < $total_games; $i ++ ) {
			if ( self::get_winner_id( $game_id, $i ) === $player_id ) {
				$wins ++;
			}
		}

		return $wins;
	}

	private static function get_highest_score_for_player_of_game( $player_id, int $game_id ) {
		$total_games = self::get_game_count( $game_id );
		$highest     = 0;
		for ( $i = 0; $i < $total_games; $i ++ ) {
			$game_score = self::get_player_score_from_game( $player_id, $game_id, $i );
			if ( $game_score > $highest ) {
				$highest = $game_score;
			}
		}

		return $highest;
	}

	public static function get_last_game( $player_id ): int {
		$games = self::get_all_player_games( $player_id );

		$complete_games = array_filter( $games, function ( $item ) {
			return self::get_match_winner( $item->ID );

		} );
		if ( count( $complete_games ) === 0 ) {
			return 0;
		}

		return $complete_games[ count( $complete_games ) - 1 ]->ID;
	}


}