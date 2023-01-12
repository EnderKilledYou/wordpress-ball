<?php

class BallShortCodeHandler {
	public static function match_table( $atts ): string {
		$atts = shortcode_atts(
			array(
				'game_id' => '0',
			),
			$atts,
			'match_table'
		);


		if ( $atts['game_id'] !== 0 ) {
			$game_id = $atts['game_id'];
		} else {
			$game_id = get_the_ID();
			if ( $game_id === 0 ) {
				return "No Such Match";
			}
		}
		$player_one      = GameHelper::get_player1_ID( $game_id );
		$player1         = get_post( $player_one );
		$player_two      = GameHelper::get_player1_ID( $game_id );
		$player2         = get_post( $player_two );
		$total_games     = GameHelper::get_game_count( $game_id );
		$left_col_count  = $total_games / 2;
		$right_col_count = $total_games / 2;
		if ( $total_games % 2 === 0 ) {
			$left_col_count ++;
		}
		$player_1_name = $player1->post_title;
		$player_2_name = $player1->post_title;
		$header        = '<tr>	<th scope="col" id="title" class="manage-column column-title column-primary sortable desc" colspan="' . $left_col_count . '">
			<span>Player</span>
		</th>';


		for ( $i = 0; $i < $total_games; $i ++ ) {
			$header .= '<th>Game ' . ( $i + 1 ) . '</th>';

		}
		$header.="<th> GW </th>";
		$p1_game_scores = "<tr><td>$player_1_name</td>";
		for ( $i = 0; $i < $total_games; $i ++ ) {
			$score          = GameHelper::get_player1_score( $game_id, $i );
			$p1_game_scores .= "<td>$score</td>";
		}
		$p1_game_scores .= '</tr>';
		$p2_game_scores = "<tr><td>$player_2_name</td>";
		for ( $i = 0; $i < $total_games; $i ++ ) {
			$score          = GameHelper::get_player2_score( $game_id, $i );
			$p2_game_scores .= "<td>$score</td>";
		}
		$p2_game_scores .= '</tr>';
		$tbl            = '<table class="wp-list-table widefat fixed striped table-view-excerpt posts">
	<thead>
	<tr>' . $header . '</tr>
		
	</thead>

	<tbody id="the-list">' . $p1_game_scores . $p2_game_scores . '</tbody></table>';


		return $tbl;
	}
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
