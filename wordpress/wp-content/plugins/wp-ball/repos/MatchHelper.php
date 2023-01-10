<?php

class MatchHelper {
	private static string $season_id = 'season_id';


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
	 * @param $season_id
	 *
	 * @return WP_Post[]
	 */
	public static function get_season_matches( $season_id ): array {
		return get_posts( [
			'post_type'   => WPBallObjectsRepository::MATCH_POST_TYPE,
			'parent_post' => $season_id,
			'post_status' => BallPostSaveHandler::$all_posts,

		] );
	}

	public static function create_match( int $season_id, int $week, $date ) {


		$stat = wp_insert_post( [
			'post_type' => WPBallObjectsRepository::MATCH_POST_TYPE,
			'post_name' => "Week $week $date",

			'post_status ' => 'publish'
		] );
		update_post_meta( $stat, self::$season_id, $season_id );
//		update_post_meta( $stat, self::$player_2, $player2->ID );
//		update_post_meta( $stat, self::$player_1_score, "0" );
//		update_post_meta( $stat, self::$player_2_score, "0" );
//		update_post_meta( $stat, self::$player_2_score, "0" );
//		update_post_meta( $stat, self::$game_state, 'pending' );

		return $stat;
	}




}