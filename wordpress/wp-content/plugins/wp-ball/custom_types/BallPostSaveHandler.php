<?php

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
		if ( isset( $_REQUEST['player1_score'] ) && ctype_digit( $_REQUEST['player1_score'] ) ) {
			GameHelper::update_player1_score( $id, (int) $_REQUEST['player1_score'] );
		}
		if ( isset( $_REQUEST['player2_score'] ) && ctype_digit( $_REQUEST['player1_score'] ) ) {
			GameHelper::update_player2_score( $id, (int) $_REQUEST['player2_score'] );
		}
		if ( isset( $_REQUEST['game_complete'] ) ) {
			GameHelper::update_game_complete( $id );
		}
		if ( isset( $_REQUEST['game_start'] ) ) {
			GameHelper::update_game_started( $id );
		}
	}

	public static function SaveSeason( $id ): void {

		if ( self::CheckIfAutoDraft( $id ) === null ) {
			return;
		}
		if ( isset( $_REQUEST['players'] ) ) {


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
				ScoreHelper::create_player_score_for_season( $id, $player, $_REQUEST['post_title'] );

			}
		}

		if ( isset( $_REQUEST['generate_matches'] ) ) {
			$matches    = MatchHelper::get_season_matches( $id );
			$first_date = strtotime( $_REQUEST['start_date'] );
			$timestamp  = $first_date;
			for ( $i = 0; $i < 7; $i ++ ) {
				if ( ! self::find_match_by_week( $matches, $timestamp ) ) {
					$week = date( "W", $timestamp );
					MatchHelper::create_match( $id, $week, date( "m/dd/yy", $timestamp ) );
				}
				$timestamp = strtotime( "+ 7 days", $timestamp );
			}

			ScheduleHelper::RebuildSchedule( $id );
		}

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
			$stats      = PlayerHelper::create_player_stat( $player );
			$stats_post = get_post_permalink( $stats );
			BallAdminNoticeHandler::AddNotice( "Player Statistics is:  {$stats} $stats_post" );
		}


	}


	public static function SaveScore( $id, $post, $update ) {

	}

	public static function SaveMatch( $match_id ) {
		if ( ! isset( $_REQUEST['players_1_score'], $_REQUEST['players_2_score'] ) || ! ctype_digit( $_REQUEST['players_1_score'] ) || ! ctype_digit( $_REQUEST['players_2_score'] ) ) {

			return;
		}

		$player1_id = MatchHelper::get_player1_ID( $match_id );
		$player2_id = MatchHelper::get_player2_ID( $match_id );
		$match      = get_post( $match_id );
//		$player_1_score = MatchHelper::get_player1_score( $match_id );
//		$player_2_score = MatchHelper::get_player2_score( $match_id );
//		$player_1       = get_post( $player1_id );
//		$player_2        = get_post( $player_2_id );
		$player1_score_id = ScoreHelper::get_player_score_for_season( $match->post_parent, $player1_id );
		$player2_score_id = ScoreHelper::get_player_score_for_season( $match->post_parent, $player2_id );
		$player1_score    = $_REQUEST['players_1_score'];
		$player2_score    = $_REQUEST['players_2_score'];
		MatchHelper::update_player1_score( $match_id, $player1_score );
		MatchHelper::update_player2_score( $match_id, $player2_score );
		if ( isset( $_REQUEST['complete_match'] ) && ctype_digit( $_REQUEST['complete_match'] ) ) {
			MatchHelper::update_game_complete( $match_id );
			$winner_id = (int) $_REQUEST['complete_match'];
			PlayerHelper::update_match_outcome( $player1_id, $winner_id );
			PlayerHelper::update_match_outcome( $player2_id, $winner_id );
			ScoreHelper::update_match_outcome( $player1_score_id, $winner_id );
			ScoreHelper::update_match_outcome( $player2_score_id, $winner_id );

		}
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
			if ( date( "W", $match->post_date->getTimestamp() ) === $date_week ) {
				return true;
			}
		}

		return false;
	}

}
