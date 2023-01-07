<?php

class BallPostSaveHandler {
	public static function SaveSeason( $id ) {
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
				$_SESSION['admin_error'] = "No such player $id";

				return;

			}
			ScoreHelper::create_player_score_for_season( $id, $player, $_REQUEST['post_title'] );

		}

	}

	public static function SavePlayer( $id ) {
		$player = get_post( $id );
		if ( ! $player ) {
			$_SESSION['admin_error'] = "No such player $id";

			return;

		}
		PlayerHelper::create_player_stat( $player );
	}


	public static function SaveScore( $id, $post, $update ) {

	}

	public static function SaveMatch( $match_id ) {
		if ( ! isset( $_REQUEST['players_1_score'], $_REQUEST['players_2_score'] ) || ! ctype_digit( $_REQUEST['players_1_score'] ) || ! ctype_digit( $_REQUEST['players_2_score'] ) ) {

			return;
		}

		$player1_id = MatchHelper::get_player1_ID( $match_id );
		$player2_id = MatchHelper::get_player2_ID( $match_id );
        $match       = get_post( $match_id );
//		$player_1_score = MatchHelper::get_player1_score( $match_id );
//		$player_2_score = MatchHelper::get_player2_score( $match_id );
//		$player_1       = get_post( $player1_id );
//		$player_2        = get_post( $player_2_id );
		$player1_score_id = ScoreHelper::get_player_score_for_season($match->post_parent,$player1_id);
		$player2_score_id = ScoreHelper::get_player_score_for_season($match->post_parent,$player2_id);
		$player1_score = $_REQUEST['players_1_score'];
		$player2_score = $_REQUEST['players_2_score'];
		MatchHelper::update_player1_score( $match_id, $player1_score );
		MatchHelper::update_player2_score( $match_id, $player2_score );
		if ( isset( $_REQUEST['complete_match'] ) && ctype_digit( $_REQUEST['complete_match'] ) ) {
			MatchHelper::update_game_complete( $match_id );
			$winner_id = (int) $_REQUEST['complete_match'];
			PlayerHelper::update_match_outcome( $player1_id, $winner_id );
			PlayerHelper::update_match_outcome( $player2_id, $winner_id );
			ScoreHelper::update_match_outcome($player1_score_id,$winner_id);
			ScoreHelper::update_match_outcome($player2_score_id,$winner_id);

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

}
