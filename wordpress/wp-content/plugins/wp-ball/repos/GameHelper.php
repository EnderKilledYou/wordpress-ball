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
	public static function get_player_games( $player_id ): array {
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

	private static string $player_1 = 'player_1';
	private static string $match_id = 'match_id';
	private static string $player_2 = 'player_2';
	private static string $player_1_score = 'player_1_score';
	private static string $player_2_score = 'player_2_score';
	private static string $game_state = 'player_2_state';

	public static function update_game_complete( $game_id ): void {
		update_post_meta( $game_id, self::$game_state, 'complete' );
	}

	public static function update_game_started( $game_id ): void {
		update_post_meta( $game_id, self::$game_state, 'started' );
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
	public static function get_player2_score( $game_id ) {
		return get_post_meta( $game_id, self::$player_2_score, true );
	}

	public static function create_game( int $match_id, $player1_id, $player2_id ): void {
		$player1 = PlayerHelper::get_player( $player1_id );
		$player2 = PlayerHelper::get_player( $player1_id );
		$stat    = wp_insert_post( [
			'post_type'    => WPBallObjectsRepository::GAME_POST_TYPE,
			'post_name'    => "Game for Match: $match_id, $player1->post_title vs $player2->post_title",
			'post_status ' => 'publish'
		] );

		update_post_meta( $stat, self::$match_id, $match_id );
		update_post_meta( $stat, self::$player_1, $player1_id );
		update_post_meta( $stat, self::$player_2, $player2_id );
	}

}