<?php

class BallExcerptHandler {
	public static function MatchHandler( $orig ) {
		$id = get_the_ID();
		echo "SDFASDF";

		return $id;
	}
}

class BallPostSaveHandler {
	public static function CheckIfAutoDraft( $id ): ?WP_Post {
		$season = get_post( $id );

		if ( $season->post_status === 'auto-draft' ) {
			return null;
		}

		return $season;
	}

	public static function SaveGame( $id ): void {
		if ( self::CheckIfAutoDraft( $id ) === null ) {
			return;
		}
		if ( isset( $_REQUEST['player1_score'], $_REQUEST['game_count'] ) && ctype_digit( $_REQUEST['player1_score'] ) && ctype_digit( $_REQUEST['game_count'] ) ) {
			GameHelper::update_player1_score( $id, (int) $_REQUEST['player1_score'], (int) $_REQUEST['game_count'] );
		}
		if ( isset( $_REQUEST['player2_score'], $_REQUEST['game_count'] ) && ctype_digit( $_REQUEST['player1_score'] ) && ctype_digit( $_REQUEST['game_count'] ) ) {
			GameHelper::update_player2_score( $id, (int) $_REQUEST['player2_score'], (int) $_REQUEST['game_count'] );
		}
		if ( isset( $_REQUEST['game_complete'], $_REQUEST['winner_id'], $_REQUEST['game_count'] ) && ctype_digit( $_REQUEST['game_complete'] ) && ctype_digit( $_REQUEST['winner_id'] ) && ctype_digit( $_REQUEST['game_count'] ) ) {
			$player1          = GameHelper::get_player1_ID( $id );
			$winner_id        = (int) $_REQUEST['winner_id'];
			$bwinner_player_1 = $player1 === $winner_id;
			GameHelper::update_game_complete( $id, $bwinner_player_1, $_REQUEST['game_count'] );

		}

	}

	public static function SaveSeason( $id ): void {

		if ( self::CheckIfAutoDraft( $id ) === null ) {
			wp_update_post( [ 'ID' => $id, 'post_content' => '[season_table]' ] );
			return;
		}
		$post_title = get_the_title( $id );
		if ( ! isset( $_REQUEST['players'] ) ) {
			return;
		}
		foreach ( $_REQUEST['players'] as $player_id ) {
			$score_post = ScoreHelper::get_player_score_for_season( $id, $player_id );
			if ( $score_post ) {
				continue;
			}
			$player = get_post( $player_id );
			if ( ! $player ) {
				BallAdminNoticeHandler::AddError( "No such player $id" );

				return;

			}
			ScoreHelper::create_player_score_for_season( $id, $player, $post_title );

		}
		unset( $_REQUEST['players'] );
	}

	public static function RebuildSchedule( $id ): void {
		if ( ! isset( $_REQUEST['generate_matches'], $_REQUEST['start_date'] ) ) {
			return;
		}
		$season      = get_post( $id );
		$matches     = MatchHelper::get_season_matches( $id );
		$first_date  = strtotime( $_REQUEST['start_date'] );
		$timestamp   = $first_date;
		$total_weeks = 7;
		if ( isset( $_REQUEST['total_weeks'] ) && ctype_digit( $_REQUEST['total_weeks'] ) ) {
			$total_weeks = abs( (int) $_REQUEST['total_weeks'] );
		}
		for ( $i = 0; $i < $total_weeks; $i ++ ) {
			if ( ! self::find_match_by_week( $matches, $timestamp ) ) {

				$match_id = MatchHelper::create_match( $id, $i + 1, $season->post_title, date( "m/d/Y", $timestamp ) );
			}
			$timestamp = strtotime( "+ 7 days", $timestamp );
		}

		ScheduleHelper::RebuildSchedule( $id );

	}

	public static function SavePlayer( $id ): void {

		$player = self::CheckIfAutoDraft( $id );
		if ( $player === null ) {
			return;
		}

		if ( ! $player ) {
			BallAdminNoticeHandler::AddError( "No such player $id" );

			return;

		}
		$stat = PlayerHelper::get_player_statistic( $id );
		if ( ! $stat ) {
			$stats      = PlayerHelper::create_player_stat( $player->ID, $player->post_title, $player->post_content );
			$stats_post = get_post_permalink( $stats );
			BallAdminNoticeHandler::AddNotice( "Player Statistics is:  {$stats} $stats_post" );
		}


	}


	public static function SaveScore( $id, $post, $update ) {

	}

	public static function SaveMatch( $match_id ) {

	}

	public static function SaveStat( $id, $post, $update ) {

	}

	public static array $all_posts_but_draft = array(
		'publish',
		'pending',


		'future',
		'private',
		'inherit',

	);
	public static array $all_posts = array(
		'publish',
		'pending',
		'draft',

		'future',
		'private',
		'inherit',

	);

	/**
	 * @param array $matches
	 * @param $first_date
	 *
	 * @return void
	 */
	public static function find_match_by_week( array $matches, int $timestamp ): bool {
		$date_week = date( "W", $timestamp );

		foreach ( $matches as $match ) {
			if ( date( "W", strtotime( $match->post_date ) ) === $date_week ) {
				return true;
			}
		}

		return false;
	}

}
