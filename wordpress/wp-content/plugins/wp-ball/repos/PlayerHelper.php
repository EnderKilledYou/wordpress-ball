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

			'post_status' => BallPostSaveHandler::$all_posts
		] );

	}

	/**
	 * @return WP_Post[]
	 */
	public static function get_players(): array {
		return get_posts( [
			'post_type' => WPBallObjectsRepository::PLAYER_POST_TYPE,

			'post_status' => BallPostSaveHandler::$all_posts
		] );

	}

	public static function get_player( $playerid ) {
		return get_post( $playerid );
	}


	public static function get_player_total_wins( int $player_id ) {
		$stat = self::get_player_statistic( $player_id );

		if ( ! $stat ) {
			BallAdminNoticeHandler::AddError( "No player statistics for $player_id, did it get deleted?" );

			return null;
		}

		return get_post_meta( $stat->ID, self::$player_total_wins, true );
	}


	public static function get_player_total_matches( int $player_id ) {
		$stat = self::get_player_statistic( $player_id );
		if ( ! $stat ) {
			BallAdminNoticeHandler::AddNotice( "No player statistics for $player_id, did it get deleted?" );

			return null;
		}

		return get_post_meta( $stat->ID, self::$player_total_matches, true );

	}


	public static function get_player_total_losses( int $player_id ) {
		$stat = self::get_player_statistic( $player_id );
		if ( ! $stat ) {
			BallAdminNoticeHandler::AddNotice( "No player statistics for $player_id, did it get deleted?" );

			return null;
		}

		return get_post_meta( $stat->ID, self::$player_total_loss, true );

	}

	public static function create_player_stat( WP_Post $player ) {


		$stat = wp_insert_post( [
			'post_type'      => strtolower( WPBallObjectsRepository::STATISTIC_POST_TYPE ),
			'post_author'    => 1,
			'post_status '   => 'publish',
			'post_title'     => $player->post_title . '\'s Stats',
			'post_content'   => $player->post_title,
			'comment_status' => 'closed',
			'ping_status'    => 'closed',


		] );
		update_post_meta( $stat, self::$player_id, $player->ID );
		update_post_meta( $stat, self::$player_total_wins, "0" );
		update_post_meta( $stat, self::$player_total_loss, "0" );
		update_post_meta( $stat, self::$player_total_matches, "0" );


		return $stat;
	}


	public static function get_player_statistic( $playerID ): ?WP_Post {
		$post_list = get_posts( [
			'post_type'   => WPBallObjectsRepository::STATISTIC_POST_TYPE,
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

	public static function update_match_outcome( $player_id, $winner_id ): void {
		$total_matches = get_post_meta( $player_id, self::$player_total_matches, true );
		$total_matches = (int) $total_matches + 1;
		update_post_meta( $player_id, self::$player_total_matches, (string) $total_matches );

		if ( (int) $winner_id === (int) $player_id ) {
			$wins = get_post_meta( $player_id, self::$player_total_wins, true );
			$wins = (int) $wins + 1;
			update_post_meta( $player_id, self::$player_total_wins, (string) $wins );
		} else {
			$loss = get_post_meta( $player_id, self::$player_total_loss, true );
			$loss = (int) $loss + 1;
			update_post_meta( $player_id, self::$player_total_loss, (string) $loss );
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

				return null;
			}
			$players[] = $player;
		}
		return $players;
	}
}