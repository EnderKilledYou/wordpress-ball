<?php

class BallShortCodeHandler {
	// Create Shortcode playerscore
// Shortcode: [playerscore playerid="0"]
	public static function PlayerTotalScore( $atts ): string {

		$atts = shortcode_atts(
			array(
				'playerid' => '0',
			),
			$atts,
			'playerscore'
		);


		$id = get_the_ID();
		if ( $atts['playerid'] !== 0 ) {
			$id = $atts['playerid'];
		}


		return GameHelper::get_total_player_points( $id );
	}

	public static function PlayerTotalLoses( $atts ): string {

		$atts = shortcode_atts(
			array(
				'playerid' => '0',
			),
			$atts,
			'playerlosses'
		);
		$id   = get_the_ID();
		if ( $atts['playerid'] !== 0 ) {
			$id = $atts['playerid'];
		}

		return count( GameHelper::get_player_games_lost( $id ) );


	}

	public static function PlayerTotalMatches( $atts ): string {

		$atts = shortcode_atts(
			array(
				'playerid' => '0',
			),
			$atts,
			'playermatch'
		);
		$id   = get_the_ID();
		if ( $atts['playerid'] !== 0 ) {
			$id = $atts['playerid'];
		}

		return count( GameHelper::get_all_player_games( $id ) );


	}

	public static function PlayerTotalWins( $atts ): string {

		$atts = shortcode_atts(
			array(
				'playerid' => '0',
			),
			$atts,
			'playerwins'
		);
		$id   = get_the_ID();
		if ( $atts['playerid'] !== 0 ) {
			$id = $atts['playerid'];
		}

		return PlayerHelper::get_player_total_wins( $id );


	}
}