<?php

class MatchHelper {
	private static string $player_1 = 'player_1';
	private static string $player_2 = 'player_2';
	private static string $player_1_score = 'player_1_score';
	private static string $player_2_score = 'player_2_score';
	private static string $game_state = 'player_2_state';

	/**
	 * @param $match_id
	 *
	 * @return mixed
	 */
	public static function get_player1_ID( $match_id ) {
		return get_post_meta( $match_id, self::$player_1, true );
	}

	/**
	 * @param $match_id
	 *
	 * @return mixed
	 */
	public static function get_player2_ID( $match_id ) {
		return get_post_meta( $match_id, self::$player_2, true );
	}

	/**
	 * @param $match_id
	 *
	 * @return mixed
	 */
	public static function get_player1_score( $match_id ) {
		return get_post_meta( $match_id, self::$player_1_score, true );
	}

	/**
	 * @param $match_id
	 *
	 * @return mixed
	 */
	public static function get_player2_score( $match_id ) {
		return get_post_meta( $match_id, self::$player_2_score, true );
	}

	/**
	 * @return WP_Post[]
	 */
	public static function get_matches(): array {
		return get_posts( [
			'post_type' => WPBallObjectsRepository::MATCH_POST_TYPE,

			'post_status' => BallPostSaveHandler::$all_posts
		] );

	}

	/**
	 * @return WP_Post[]
	 */
	public static function get_player_matches( $player_id ): array {
		return get_posts( [
			'post_type' => WPBallObjectsRepository::MATCH_POST_TYPE,

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

	public static function get_season_matches( $season_id ): array {
		return get_posts( [
			'post_type'   => WPBallObjectsRepository::MATCH_POST_TYPE,
			'parent_post' => $season_id,
			'post_status' => BallPostSaveHandler::$all_posts,

		] );
	}

	public static function create_match( WP_Post $season, WP_Post $player1, WP_Post $player2 ) {


		$stat = wp_insert_post( [
			'post_type'   => WPBallObjectsRepository::MATCH_POST_TYPE,
			'post_name'   => $player1->post_title . ' vs. ' . $player2->post_title,
			'parent_post' => $season->ID
		] );
		update_post_meta( $stat, self::$player_1, $player1->ID );
		update_post_meta( $stat, self::$player_2, $player2->ID );
		update_post_meta( $stat, self::$player_1_score, 0 );
		update_post_meta( $stat, self::$player_2_score, 0 );
		update_post_meta( $stat, self::$player_2_score, 0 );
		update_post_meta( $stat, self::$game_state, 'pending' );

		return $stat;
	}

	public static function update_game_complete( $match_id ) {
		update_post_meta( $match_id, self::$game_state, 'complete' );
	}

	public static function update_game_started( $match_id ) {
		update_post_meta( $match_id, self::$game_state, 'started' );
	}

	public static function update_player2_score( $match_id, $player2_score ) {
		update_post_meta( $match_id, self::$player_2_score, $player2_score );
	}

	public static function update_player1_score( $match_id, $player1_score ) {
		update_post_meta( $match_id, self::$player_1_score, $player1_score );
	}
}

class PlayerHelper {
	private static string $player_total_wins = 'player_total_wins';
	private static string $player_total_loss = 'player_total_loss';
	private static string $player_total_matches = 'player_total_matches';

	/**
	 * @return WP_Post[]
	 */
	public static function get_players(): array {
		return get_posts( [
			'post_type' => WPBallObjectsRepository::PLAYER_POST_TYPE,

			'post_status' => BallPostSaveHandler::$all_posts
		] );

	}

	public static function create_player_stat( WP_Post $player ) {
		$stat = self::get_player_statistic( $player );

		if ( ! $stat ) {
			$stat = wp_insert_post( [
				'post_type'   => WPBallObjectsRepository::STATISTIC_POST_TYPE,
				'post_name'   => $player->post_title . '\'s Stats',
				'parent_post' => $player->ID
			] );
			update_post_meta( $stat, self::$player_total_wins, 0 );
			update_post_meta( $stat, self::$player_total_loss, 0 );
			update_post_meta( $stat, self::$player_total_matches, 0 );
		}

		return $stat;
	}


	public static function get_player_statistic( $playerID ): ?WP_Post {
		return get_post( [
			'post_type'   => WPBallObjectsRepository::STATISTIC_POST_TYPE,
			'parent_post' => $playerID,
			'post_status' => BallPostSaveHandler::$all_posts
		] );
	}

	public static function update_match_outcome( $player_id, $winner_id ): void {
		$total_matches = get_post_meta( $player_id, self::$player_total_matches, true );
		$total_matches = (int) $total_matches + 1;
		update_post_meta( $player_id, self::$player_total_matches, $total_matches );

		if ( (int) $winner_id === (int) $player_id ) {
			$wins = get_post_meta( $player_id, self::$player_total_wins, true );
			$wins = (int) $wins + 1;
			update_post_meta( $player_id, self::$player_total_wins, $wins );
		} else {
			$loss = get_post_meta( $player_id, self::$player_total_loss, true );
			$loss = (int) $loss + 1;
			update_post_meta( $player_id, self::$player_total_loss, $loss );
		}
	}
}

class MachineHelper {

	/**
	 * @return WP_Post[]
	 */
	public static function get_machines(): array {
		return get_posts( [
			'post_type' => WPBallObjectsRepository::MACHINE_POST_TYPE,

			'post_status' => BallPostSaveHandler::$all_posts
		] );

	}
}

class ScoreHelper {
	public static string $score_player_meta_name = 'score_player_id';
	private static string $player_season_wins = 'player_season_wins';
	private static string $player_season_loss = 'player_season_loss';
	private static string $player_season_matches = 'player_season_matches';

	public static function create_player_score_for_season( int $id, WP_Post $player, string $season_name ): int {
		$score_post = wp_insert_post( [
			'post_type'   => WPBallObjectsRepository::SCORE_POST_TYPE,
			'post_name'   => $season_name . ': ' . $player->post_title,
			'parent_post' => $id
		] );

		update_post_meta( $score_post, self::$score_player_meta_name, $player->ID );
		update_post_meta( $score_post, self::$player_season_wins, 0 );
		update_post_meta( $score_post, self::$player_season_loss, 0 );
		update_post_meta( $score_post, self::$player_season_matches, 0 );

		return $score_post;
	}

	public static function update_match_outcome(  $score_id,  $winner_id ): void {
		$total_matches = get_post_meta( $score_id, self::$player_season_matches, true );
		$total_matches = (int) $total_matches + 1;
		update_post_meta( $score_id, self::$player_season_matches, $total_matches );
		$player_id = self::get_player_id_from_score( $score_id );
		if ( (int) $winner_id === $player_id ) {
			$wins = get_post_meta( $score_id, self::$player_season_wins, true );
			$wins = (int) $wins + 1;
			update_post_meta( $score_id, self::$player_season_wins, $wins );
		} else {
			$loss = get_post_meta( $score_id, self::$player_season_loss, true );
			$loss = (int) $loss + 1;
			update_post_meta( $score_id, self::$player_season_loss, $loss );
		}
	}

	public static function get_player_score_for_season( int $id, int $player_id ): ?WP_Post {
		return get_post( [
			'post_type'   => WPBallObjectsRepository::SCORE_POST_TYPE,
			'parent_id'   => $id,
			'meta_query'  => array(
				// meta query takes an array of arrays, watch out for this!
				array(
					'key'   => self::$score_player_meta_name,
					'value' => $player_id,
					//   'compare' => 'IN'
				)
			),
			'post_status' => BallPostSaveHandler::$all_posts
		] );


	}

	public static function get_player_id_from_score( $player_score_id ): int {
		return (int) get_post_meta( $player_score_id, self::$score_player_meta_name, true );
	}

	/**
	 * @param $player_id
	 *
	 * @return int[]|WP_Post[]
	 */
	public static function get_player_scores( $player_id ): array {

		return get_posts( [
			'post_type'   => WPBallObjectsRepository::SCORE_POST_TYPE,
			'meta_query'  => array(

				// meta query takes an array of arrays, watch out for this!
				array(
					'key'   => 'score_player_id',
					'value' => $player_id,
					//   'compare' => 'IN'
				)
			),
			'post_status' => BallPostSaveHandler::$all_posts
		] );


	}

	/**
	 * @param $player_id
	 *
	 * @return int[]|WP_Post[]
	 */
	public static function get_season_scores( $id ): array {

		return get_posts( [
			'post_type'   => WPBallObjectsRepository::SCORE_POST_TYPE,
			'parent_id'   => $id,
			'post_status' => BallPostSaveHandler::$all_posts
		] );


	}
}