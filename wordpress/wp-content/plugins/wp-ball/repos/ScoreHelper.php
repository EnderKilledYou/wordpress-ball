<?php

class ScoreHelper {
	public static string $score_player_meta_name = 'score_player_id';
	private static string $player_season_wins = 'player_season_wins';
	private static string $player_season_loss = 'player_season_loss';
	private static string $player_season_matches = 'player_season_matches';
	private static string $season_id = 'season_id';

	public static function create_player_score_for_season( int $season_id, WP_Post $player, string $season_name ): int {
		$score_post = wp_insert_post( [
			'post_type'  => WPBallObjectsRepository::SCORE_POST_TYPE,
			'post_title' => $season_name . ': ' . $player->post_title,

		] );

		update_post_meta( $score_post, self::$season_id, $season_id );
		update_post_meta( $score_post, self::$score_player_meta_name, $player->ID );

		wp_publish_post( $score_post );

		return $score_post;
	}


	public static function get_player_score_for_season( int $season_id, int $player_id ): ?WP_Post {
		$scores = get_posts( [
			'post_type'      => WPBallObjectsRepository::SCORE_POST_TYPE,
			'posts_per_page' => - 1,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'   => self::$season_id,
					'value' => $season_id,
					//   'compare' => 'IN'
				),

				array(
					'key'   => self::$score_player_meta_name,
					'value' => $player_id,
					//   'compare' => 'IN'
				)
			),
			'post_status'    => BallPostSaveHandler::$all_posts

		] );
		if ( count( $scores ) > 0 ) {
			return $scores[0];
		}

		return null;


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
			'post_type'      => WPBallObjectsRepository::SCORE_POST_TYPE,
			'posts_per_page' => - 1,
			'meta_query'     => array(

				// meta query takes an array of arrays, watch out for this!
				array(
					'key'   => self::$score_player_meta_name,
					'value' => $player_id,
					//   'compare' => 'IN'
				)
			),
			'post_status'    => BallPostSaveHandler::$all_posts
		] );


	}

	/**
	 * @param $player_id
	 *
	 * @return  WP_Post[]
	 */
	public static function get_season_scores( $season_id ): array {

		return get_posts( [
			'post_type'      => WPBallObjectsRepository::SCORE_POST_TYPE,
			'posts_per_page' => - 1,
			'meta_query'     => array(

				array(
					'key'   => self::$season_id,
					'value' => $season_id,

				)
			),

			'post_status' => BallPostSaveHandler::$all_posts,

		] );


	}

	public static function get_total_player_points( int $score_id ): int {
		$player_id = self::get_player_id_from_score( $score_id );
		$games     = GameHelper::get_all_player_games( $player_id );
		$sum       = 0;
		foreach ( $games as $game ) {
			$sum += GameHelper::get_winner_score_by_id( $game->ID );
		}

		return $sum;
	}


	public static function get_total_player_points_for_season( int $season_id ): int {
		$scores = self::get_season_scores( $season_id );
		$sum    = 0;
		foreach ( $scores as $score ) {
			$score_id  = $score->ID;
			$player_id = self::get_player_id_from_score( $score_id );
			$games     = GameHelper::get_all_player_games( $player_id );

			foreach ( $games as $game ) {
				$game_count = GameHelper::get_game_count( $game->ID );
				for ( $i = 0; $i < $game_count; $i ++ ) {
					$sum += GameHelper::get_player1_score( $game->ID, $i );
				}
			}
		}

		return $sum;
	}

	public static function get_player_losses( int $score_id ): int {
		$player_id = self::get_player_id_from_score( $score_id );
		$games     = GameHelper::get_player_games_lost( $player_id );

		return count( $games );

	}

	public static function get_player_wins( int $score_id ): int {
		$player_id = self::get_player_id_from_score( $score_id );
		$games     = GameHelper::get_won_games( $player_id );

		return count( $games );
	}

	public static function get_player_games( int $score_id ): int {
		$player_id = self::get_player_id_from_score( $score_id );
		$games     = GameHelper::get_all_player_games( $player_id );

		return count( $games );
	}

	public static function get_player_season_losses( int $score_id ): int {
		$player_id = self::get_player_id_from_score( $score_id );
		$games     = GameHelper::get_player_games_lost( $player_id );

		return count( $games );

	}

	public static function get_player_season_wins( int $score_id ): int {
		$player_id = self::get_player_id_from_score( $score_id );
		$games     = GameHelper::get_won_games( $player_id );

		return count( $games );
	}

	public static function get_player_season_matches( int $score_id ): int {
		$player_id = self::get_player_id_from_score( $score_id );
		$games     = GameHelper::get_all_player_games( $player_id );

		return count( $games );
	}
}