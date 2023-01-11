<?php

class GameHelper {
	/**
	 * @return WP_Post[]
	 */
	public static function get_match_games( int $match_id ): array {
		return get_posts( [
			'post_type' => WPBallObjectsRepository::GAME_POST_TYPE,

			'post_status' => BallPostSaveHandler::$all_posts,
			'meta_query'  => array(

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
			'post_type' => WPBallObjectsRepository::GAME_POST_TYPE,

			'post_status' => BallPostSaveHandler::$all_posts,
			'meta_query'  => array(
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
			'post_type' => WPBallObjectsRepository::GAME_POST_TYPE,

			'post_status' => BallPostSaveHandler::$all_posts,
			'meta_query'  => array(

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
			'post_type' => WPBallObjectsRepository::GAME_POST_TYPE,

			'post_status' => BallPostSaveHandler::$all_posts,
			'meta_query'  => array(

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
			'post_type' => WPBallObjectsRepository::GAME_POST_TYPE,

			'post_status' => BallPostSaveHandler::$all_posts,
			'meta_query'  => array(

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
			'post_type' => WPBallObjectsRepository::GAME_POST_TYPE,

			'post_status' => BallPostSaveHandler::$all_posts,
			'meta_query'  => array(

				// meta query takes an array of arrays, watch out for this!
				array(
					'key'   => self::$winner_id,
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
				'post_type' => WPBallObjectsRepository::GAME_POST_TYPE,

				'post_status' => BallPostSaveHandler::$all_posts,
				'meta_query'  => array(

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
			'post_type' => WPBallObjectsRepository::GAME_POST_TYPE,

			'post_status' => BallPostSaveHandler::$all_posts,
			'meta_query'  => array(
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
			'post_type' => WPBallObjectsRepository::GAME_POST_TYPE,

			'post_status' => BallPostSaveHandler::$all_posts,
			'meta_query'  => array(
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
	public static function get_all_player_games( $player_id ): array {
		return get_posts( [
			'post_type' => WPBallObjectsRepository::GAME_POST_TYPE,

			'post_status' => BallPostSaveHandler::$all_posts,
			'meta_query'  => array(
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
	private static string $player_1 = 'player_1';
	private static string $match_id = 'match_id';
	private static string $season_id = 'season_id';
	private static string $total_games = 'total_games';
	private static string $player_2 = 'player_2';
	private static string $player_1_score = 'player_1_score';
	private static string $player_2_score = 'player_2_score';
	private static string $game_state = 'game_state';
	private static string $game_state_complete = 'complete';
	private static string $game_state_pending = 'pending';


	public static function update_game_complete( $game_id, bool $is_winner_player1 ): void {
		$player1_id = self::get_player1_ID( $game_id );
		$player2_id = self::get_player1_ID( $game_id );
		if ( $is_winner_player1 ) {
			update_post_meta( $game_id, self::$winner_id, $player1_id );
			update_post_meta( $game_id, self::$loser_id, $player2_id );
		} else {
			update_post_meta( $game_id, self::$winner_id, $player2_id );
			update_post_meta( $game_id, self::$loser_id, $player1_id );
		}
		update_post_meta( $game_id, self::$game_state, self::$game_state_complete );
	}


	public static function update_player2_score( $game_id, $player2_score ): void {
		update_post_meta( $game_id, self::$player_2_score, $player2_score );
	}

	public static function update_player1_score( $game_id, $player1_score ): void {
		update_post_meta( $game_id, self::$player_1_score, $player1_score );
	}

	/**
	 * @param $game_id
	 *
	 * @return mixed
	 */
	public static function get_player1_ID( $game_id ) {
		return get_post_meta( $game_id, self::$player_1, true );
	}

	/**
	 * @param $game_id
	 *
	 * @return mixed
	 */
	public static function get_player2_ID( $game_id ) {
		return get_post_meta( $game_id, self::$player_2, true );
	}

	/**
	 * @param $game_id
	 *
	 * @return mixed
	 */
	public static function get_player1_score( $game_id ) {
		return get_post_meta( $game_id, self::$player_1_score, true );
	}

	/**
	 * @param $game_id
	 *
	 * @return mixed
	 */
	public static function get_winner_id( $game_id ) {
		return get_post_meta( $game_id, self::$winner_id, true );
	}

	/**
	 * @param $game_id
	 *
	 * @return mixed
	 */
	public static function get_winner_score_by_id( $game_id ) {
		$player1_id = self::get_player1_ID( $game_id );
		$winner_id  = self::get_winner_id( $game_id );
		if ( (int) $winner_id === (int) $player1_id ) {
			return self::get_player1_score( $game_id );
		}

		return self::get_player2_score( $game_id );;
	}

	/**
	 * @param $game_id
	 *
	 * @return mixed
	 */
	public static function get_player_score_from_game( int $player_id, int $game_id ) {
		$player1_id = self::get_player1_ID( $game_id );

		if ( $player_id === (int) $player1_id ) {
			return self::get_player1_score( $game_id );
		}

		return self::get_player2_score( $game_id );
	}

	/**
	 * @param $game_id
	 *
	 * @return mixed
	 */
	public static function get_player2_score( $game_id ) {
		return get_post_meta( $game_id, self::$player_2_score, true );
	}

	public static function create_game( int $season_id, int $match_id, $total_games,$player1_id, $player2_id ): void {
		$player1 = PlayerHelper::get_player( $player1_id );
		$player2 = PlayerHelper::get_player( $player2_id );
		$stat    = wp_insert_post( [
			'post_type'    => WPBallObjectsRepository::GAME_POST_TYPE,
			'post_title'   => "$player1->post_title vs $player2->post_title",
			'post_content' => '0 to 0',
			'post_status ' => 'publish'
		] );
		wp_publish_post( $stat );
		update_post_meta( $stat, self::$season_id, $season_id );
		update_post_meta( $stat, self::$total_games, $total_games );
		update_post_meta( $stat, self::$match_id, $match_id );
		update_post_meta( $stat, self::$player_1, $player1_id );
		update_post_meta( $stat, self::$player_2, $player2_id );
		self::update_player1_score( $stat, 0 );
		self::update_player2_score( $stat, 0 );
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
				$sum .= self::get_player_score_from_game( $season_id, $player_id );
			}
		}

		return $sum;
	}

	public static function get_season_of_game( int $ID ): int {
		return (int) get_post_meta( $ID, self::$season_id, true );
	}

}