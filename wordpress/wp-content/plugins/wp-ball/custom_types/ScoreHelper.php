<?php
class PlayerHelper {
	/**
	 * @return WP_Post[]
	 */
	public static function get_players(  ) :array {
		return get_posts( [
			'post_type'   => WPBallObjectsRepository::PLAYER_POST_TYPE,

			'post_status' => BallPostSaveHandler::$all_posts
		] );

	}
}

class ScoreHelper {

	public static function create_player_score_for_season( int $id, WP_Post $player, string $season_name ): int {
		$score_post = wp_insert_post( [
			'post_type'   => WPBallObjectsRepository::SCORE_POST_TYPE,
			'post_name'   => $season_name . ': ' . $player->post_title,
			'parent_post' => $id
		] );
		update_post_meta( $score_post, 'score_player_id', $player->ID );

		return $score_post;
	}


	public static function get_player_score_for_season( int $id, int $player_id ): ?WP_Post {
		$score_post = get_posts( [
			'post_type'   => WPBallObjectsRepository::SCORE_POST_TYPE,
			'parent_id'   => $id,
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
		if ( count( $score_post ) > 0 ) {
			return $score_post[0];
		}

		return null;
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


	}/**
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