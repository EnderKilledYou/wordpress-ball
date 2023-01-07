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
				echo "No such palyer";
				exit;
				//todo:error no such player
				continue;
			}
			ScoreHelper::create_player_score_for_season( $id, $player, $_REQUEST['post_title'] );

		}

	}

	public static function SavePlayer( $id, $post, $update ) {

	}


	public static function SaveScore( $id, $post, $update ) {

	}

	public static function SaveMatch( $id, $post, $update ) {

	}

	public static function SaveStat( $id, $post, $update ) {

	}

	public static array $all_posts = array(
		'publish',
		'pending',
		'draft',

		'future',
		'private',
		'inherit',
		'trash'
	);
}