<?php

class BallShortCodeHandler {
	// Create Shortcode playerscore
// Shortcode: [playerscore playerid="0"]
	public static function Create_PlayerScore( $atts ): string {

		$atts = shortcode_atts(
			array(
				'playerid' => '0',
			),
			$atts,
			'playerscore'
		);


		$id = get_the_ID();
		if($atts['playerid'] !== 0){
			$id = $atts['playerid'];
		}
		$wins    = PlayerHelper::get_player_total_wins( $id );
		$matches = PlayerHelper::get_player_total_matches( $id );
		$loses   = PlayerHelper::get_player_total_losses( $id );
		return "<span>$wins</span><span>$matches</span><span>$loses</span>";
	}

	public static function Create_PlayerLoses( $atts ): string {

		$atts = shortcode_atts(
			array(
				'playerid' => '0',
			),
			$atts,
			'playerscoreloss'
		);
		$id = get_the_ID();
		if($atts['playerid'] !== 0){
			$id = $atts['playerid'];
		}
		return PlayerHelper::get_player_total_losses($id);


	}

	public static function Create_PlayerMatches( $atts ): string {

		$atts = shortcode_atts(
			array(
				'playerid' => '0',
			),
			$atts,
			'playermatch'
		);
		$id = get_the_ID();
		if($atts['playerid'] !== 0){
			$id = $atts['playerid'];
		}

		return PlayerHelper::get_player_total_matches( $id );


	}

	public static function Create_PlayerWins( $atts ): string {

		$atts = shortcode_atts(
			array(
				'playerid' => '0',
			),
			$atts,
			'playerwins'
		);
		$id = get_the_ID();
		if($atts['playerid'] !== 0){
			$id = $atts['playerid'];
		}

		return   PlayerHelper::get_player_total_wins( $id );


	}
}