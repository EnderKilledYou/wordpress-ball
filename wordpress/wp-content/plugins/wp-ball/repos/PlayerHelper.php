<?php

class PlayerHelper {
	private static string $player_id = 'player_id';
	private static string $player_total_wins = 'player_total_wins';
	private static string $player_total_loss = 'player_total_loss';
	private static string $player_total_matches = 'player_total_matches';

	/**
	 * @return WP_Post[]
	 */
	public static function get_seasons(): array {
		return get_posts( [
			'post_type' => WPBallObjectsRepository::SEASON_POST_TYPE,
			'posts_per_page' => - 1,
			'post_status' => BallPostSaveHandler::$all_posts
		] );

	}

	/**
	 * @return WP_Post[]
	 */
	public static function get_players(): array {
		return get_posts( [
			'post_type' => WPBallObjectsRepository::PLAYER_POST_TYPE,
			'posts_per_page' => - 1,
			'post_status' => BallPostSaveHandler::$all_posts
		] );

	}

	public static function get_player( $playerid ) {
		return get_post( $playerid );
	}


	public static function get_player_total_wins( int $player_id ): ?int {
		$stat = self::get_player_statistic( $player_id );

		if ( ! $stat ) {
			BallAdminNoticeHandler::AddError( "No player statistics for $player_id, did it get deleted?" );

			return null;
		}

		return (int) get_post_meta( $stat->ID, self::$player_total_wins, true );
	}


	public static function get_player_total_matches( int $player_id ): ?int {
		$stat = self::get_player_statistic( $player_id );
		if ( ! $stat ) {
			BallAdminNoticeHandler::AddNotice( "No player statistics for $player_id, did it get deleted?" );

			return null;
		}

		return (int) get_post_meta( $stat->ID, self::$player_total_matches, true );

	}


	public static function get_player_total_losses( int $player_id ): ?int {
		$stat = self::get_player_statistic( $player_id );
		if ( ! $stat ) {
			BallAdminNoticeHandler::AddNotice( "No player statistics for $player_id, did it get deleted?" );

			return null;
		}

		return (int) get_post_meta( $stat->ID, self::$player_total_loss, true );

	}

	public static function create_player_stat( int $player_id, string $post_title, string $post_content ) {


		$stat = wp_insert_post( [
			'post_type'      =>  WPBallObjectsRepository::STATISTIC_POST_TYPE ,

			'post_status '   => 'publish',
			'post_title'     => $post_title,
			'post_content'   => $post_content,
			'comment_status' => 'closed',
			'ping_status'    => 'closed',


		] );
		wp_publish_post($stat);
		update_post_meta( $stat, self::$player_id,$player_id );
		self::update_statistics_total_wins( $stat, 0 );
		self::update_statistics_total_losses( $stat, 0 );
		self::update_statistics_total_matches( $stat, 0 );

		return $stat;
	}


	public static function get_player_statistic( $playerID ): ?WP_Post {
		$post_list = get_posts( [
			'post_type'   => WPBallObjectsRepository::STATISTIC_POST_TYPE,
			'posts_per_page' => - 1,
			'meta_query'  => array(
				// meta query takes an array of arrays, watch out for this!
				array(
					'key'   => self::$player_id,
					'value' => $playerID,
					//   'compare' => 'IN'
				)
			),
			'post_status' => BallPostSaveHandler::$all_posts
		] );
		if ( count( $post_list ) === 0 ) {
			return null;
		}

		return $post_list[0];
	}

	public static function update_game_outcome( $player_id, $winner_id ): void {


		self::update_statistics_total_matches( $player_id, self::get_player_total_matches( $player_id ) + 1 );

		if ( (int) $winner_id === (int) $player_id ) {
			self::update_statistics_total_wins( $player_id, self::get_player_total_wins( $player_id ) + 1 );

		} else {
			self::update_statistics_total_losses( $player_id, self::get_player_total_losses( $player_id ) + 1 );
		}
	}

	/**
	 * @param WP_Post[] $scores
	 *
	 * @return WP_Post[]
	 */
	public static function map_scores_to_players( array $scores ): ?array {
		$players = [];
		foreach ( $scores as $score ) {
			$player = get_post( ScoreHelper::get_player_id_from_score( $score->ID ) );
			if ( ! $player ) {
				BallAdminNoticeHandler::AddError( "No such player for score $score->ID, was it deleted? " );

				return [];
			}
			$players[] = $player;
		}

		return $players;
	}

	/**
	 * @param $stat
	 *
	 * @return void
	 */
	public static function update_statistics_total_wins( $stat, int $value ): void {
		update_post_meta( $stat, self::$player_total_wins, (string) $value );
	}

	/**
	 * @param $stat
	 *
	 * @return void
	 */
	public static function update_statistics_total_matches( $stat, int $value ): void {
		update_post_meta( $stat, self::$player_total_matches, (string) $value );
	}

	/**
	 * @param $stat
	 *
	 * @return void
	 */
	public static function update_statistics_total_losses( $stat, int $value ): void {
		update_post_meta( $stat, self::$player_total_loss, (string) $value );
	}
}