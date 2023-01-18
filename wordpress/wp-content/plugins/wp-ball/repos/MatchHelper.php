<?php

class MatchHelper {
	private static string $season_id = 'season_id';
	private static string $week = 'week';
	private static string $date = 'date';
	private static string $match_count = 'match_count'; // how many matches (4)
	private static string $match_size = 'match_size'; //how big is each match (5)

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
		return (int) get_post_meta( $match_id, self::$week, true );
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

	public static function create_match( int $season_id, int $week, $txt, $date, $match_count, $match_size ) {

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
		update_post_meta( $stat, self::$match_count, $match_count );
		update_post_meta( $stat, self::$match_size, $match_size );
		wp_publish_post( $stat );
//		update_post_meta( $stat, self::$player_2, $player2->ID );
//		update_post_meta( $stat, self::$player_1_score, "0" );
//		update_post_meta( $stat, self::$player_2_score, "0" );
//		update_post_meta( $stat, self::$player_2_score, "0" );
//		update_post_meta( $stat, self::$game_state, 'pending' );

		return $stat;
	}

	public static function get_match_count( int $match_id ) {
		return (int) get_post_meta( $match_id, self::$match_count, true );
	}

	public static function get_match_size( int $match_id ) {
		return (int) get_post_meta( $match_id, self::$match_size, true );
	}

	public static function get_current_match_count( int $match_id, $match_index ): int {
		$games = GameHelper::get_match_games_by_group($match_id,$match_index);
		return count($games);
	}


}