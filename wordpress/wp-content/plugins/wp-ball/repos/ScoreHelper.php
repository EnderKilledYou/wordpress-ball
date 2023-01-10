<?php

class ScoreHelper {
	public static string $score_player_meta_name = 'score_player_id';
	private static string $player_season_wins = 'player_season_wins';
	private static string $player_season_loss = 'player_season_loss';
	private static string $player_season_matches = 'player_season_matches';

	public static function create_player_score_for_season( int $id, WP_Post $player, string $season_name ): int {
		$score_post = wp_insert_post( [
			'post_type'   => WPBallObjectsRepository::SCORE_POST_TYPE,
			'post_name'   => $season_name . ': ' . $player->post_title,
			'parent_post' => $id,
			'post_status '=>'Publish'
		] );

		update_post_meta( $score_post, self::$score_player_meta_name, $player->ID );
		update_post_meta( $score_post, self::$player_season_wins, "0" );
		update_post_meta( $score_post, self::$player_season_loss, "0" );
		update_post_meta( $score_post, self::$player_season_matches, "0" );

		return $score_post;
	}

	public static function update_match_outcome( $score_id, $winner_id ): void {
		$total_matches = get_post_meta( $score_id, self::$player_season_matches, true );
		$total_matches = (int) $total_matches + 1;
		update_post_meta( $score_id, self::$player_season_matches, (string)$total_matches );
		$player_id = self::get_player_id_from_score( $score_id );
		if ( (int) $winner_id === $player_id ) {
			$wins = get_post_meta( $score_id, self::$player_season_wins, true );
			$wins = (int) $wins + 1;
			update_post_meta( $score_id, self::$player_season_wins, (string)$wins );
		} else {
			$loss = get_post_meta( $score_id, self::$player_season_loss, true );
			$loss = (int) $loss + 1;
			update_post_meta( $score_id, self::$player_season_loss, (string)$loss );
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