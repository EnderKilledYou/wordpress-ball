<?php

class PlayerHelper {

	private static string $player_user_id = 'player_id';

	/**
	 * @return WP_Post[]
	 */
	public static function get_seasons(): array {
		return get_posts( [
			'post_type'      => WPBallObjectsRepository::SEASON_POST_TYPE,
			'posts_per_page' => - 1,
			'post_status'    => BallPostSaveHandler::$all_posts
		] );

	}

	/**
	 * @return WP_Post[]
	 */
	public static function get_players(): array {
		return get_posts( [
			'post_type'      => WPBallObjectsRepository::PLAYER_POST_TYPE,
			'posts_per_page' => - 1,
			'post_status'    => BallPostSaveHandler::$all_posts
		] );

	}

	public static function get_player( $playerid ) {
		return get_post( $playerid );
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
				BallAdminNoticeHandler::AddError( "No such player for score $score->ID, was it deleted? If so Delete score {$score->ID}" );

				return [];
			}
			$players[] = $player;
		}

		return $players;
	}


	public static function set_player_user_id( $player1_id, $user_id ) {
		update_post_meta( $player1_id, self::$player_user_id, $user_id );
	}

	public static function get_player_user_id( $player1_id ) {
		return (int) get_post_meta( $player1_id, self::$player_user_id, true );
	}

	public static function get_player_with_least_games_in_season( $season_id, $players ): int {
		$min    = 0;
		$lowest = $players[0];
		$pCount = count( $players );
		for ( $i = 1; $i < $pCount; $i ++ ) {
			$games_player = count( GameHelper::get_player_games_by_season( $players[ $i ]->ID, $season_id ) );
			if ( $min > $games_player ) {
				$min    = $games_player;
				$lowest = $players[ $i ]->ID;

			}
		}

		return $lowest->ID;
	}
}