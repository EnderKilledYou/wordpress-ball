<?php

class MatchHelper {
	private static string $season_id = 'season_id';
	private static string $week = 'week';
	private static string $date = 'date';


	/**
	 * @return WP_Post[]
	 */
	public static function get_matches(): array {
		return get_posts( [
			'post_type'      => WPBallObjectsRepository::MATCH_POST_TYPE,
			'posts_per_page' => - 1,
			'post_status'    => BallPostSaveHandler::$all_posts
		] );

	}

	public static function get_date( $match_id ) {
		return get_post_meta( $match_id, self::$date, true );
	}

	public static function get_week( $match_id ) {
		return get_post_meta( $match_id, self::$week, true );
	}


	/**
	 * @param $season_id
	 *
	 * @return WP_Post[]
	 */
	public static function get_season_matches( $season_id ): array {
		return get_posts( [
			'post_type'      => WPBallObjectsRepository::MATCH_POST_TYPE,
			'posts_per_page' => - 1,
			'meta_query'     => array(

				array(
					'key'   => self::$season_id,
					'value' => $season_id,

				)
			),
		] );
	}

	public static function create_match( int $season_id, int $week, $txt, $date ) {

		$post_title = "Week $week: $txt $date";
		$stat       = wp_insert_post( [
			'post_type'    => WPBallObjectsRepository::MATCH_POST_TYPE,
			'post_title'   => $post_title,
			'post_content' => '[match_table]',
			'post_name'    => $post_title,

		] );
		update_post_meta( $stat, self::$season_id, $season_id );
		update_post_meta( $stat, self::$week, $week );
		update_post_meta( $stat, self::$date, $date );
		wp_publish_post( $stat );
//		update_post_meta( $stat, self::$player_2, $player2->ID );
//		update_post_meta( $stat, self::$player_1_score, "0" );
//		update_post_meta( $stat, self::$player_2_score, "0" );
//		update_post_meta( $stat, self::$player_2_score, "0" );
//		update_post_meta( $stat, self::$game_state, 'pending' );

		return $stat;
	}


}