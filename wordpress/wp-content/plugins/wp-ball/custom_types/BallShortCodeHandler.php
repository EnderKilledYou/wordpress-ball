<?php

class HtmlPrinter {
	public static function AHref( $text, $link, $classes = [] ) {
		$class_string = implode( " ", $classes );

		return "<a href='$link' class='$class_string'>$text	</a>";
	}
}

class TablePrinter {
	public static function TableStart( $classes = [] ) {
		$class_string = implode( " ", $classes );

		return "<table class=\"$class_string\">";
	}

	public static function TableHeading( $text, $classes = [] ) {
		$class_string = implode( " ", $classes );

		return "<h2 class=\"$class_string\">$text</h2>";
	}

	public static function HeaderStart() {
		return "<thead><tr>";

	}

	public static function AddHeader(
		$name, $col_span = 1, $classes = [
		'manage-column',
		'column-title',
		'column-primary',
		'sortable',
		'desc'
	]
	) {
		$class_str = implode( ' ', $classes );

		return "<th scope=\"col\" id=\"title\" class=\"$class_str\" colspan=\"$col_span\">
			<span>$name</span>
		</th>";
	}

	public static function HeaderEnd() {
		return "</tr></thead>";
	}

	public static function BodyStart() {
		return "<tbody>";

	}

	public static function RowStart() {
		return "<tr>";

	}

	public static function AddColumn( $name, $col_span = 1, $classes = [ 'row-title' ] ) {
		$class_str = implode( ' ', $classes );

		return "<td   class=\"$class_str\" colspan=\"$col_span\">
			<span>$name</span>
		</td>";
	}

	public static function RowEnd() {
		return "</tr>";

	}

	public static function BodyEnd() {
		return "</tbody>";

	}

	public static function TableEnd() {
		return "</table>";

	}
}

class ShortCodeHelpers {
	//todo: finish
	public static function create_season_table( array $season, array $matches ) {
		return 'not  ';
	}

	public static function create_match_table( WP_Post $match ) {
		$date  = MatchHelper::get_date( $match->ID );
		$games = GameHelper::get_match_games( $match->ID );

		$tbl = TablePrinter::TableHeading( $date );
		$tbl .= TablePrinter::TableStart();
		$tbl .= TablePrinter::BodyStart();
		foreach ( $games as $game ) {

			$game_id    = $game->ID;
			$player_1id = GameHelper::get_player1_ID( $game_id );
			$player_2id = GameHelper::get_player2_ID( $game_id );
			$machine_id = GameHelper::get_game_machine( $game_id );
			$machine    = get_post( $machine_id );
			$player1    = get_post( $player_1id );
			$player2    = get_post( $player_2id );

			$tbl         .= TablePrinter::AddColumn( $player1->post_title );
			$tbl         .= TablePrinter::AddColumn( $player2->post_title );
			$tbl         .= TablePrinter::AddColumn( $machine->post_title );
			$player_one  = GameHelper::get_player1_ID( $game_id );
			$player1     = get_post( $player_one );
			$player_two  = GameHelper::get_player1_ID( $game_id );
			$player2     = get_post( $player_two );
			$total_games = GameHelper::get_game_count( $game_id );
			$game_table  = self::create_game_table( $total_games, $player1->post_title, $game_id, $player2->post_title );
			$tbl         .= TablePrinter::AddColumn( $game_table, 3 );
		}
		$tbl .= TablePrinter::BodyEnd();
		$tbl .= TablePrinter::TableEnd();

		return $tbl;
	}

	public static function leader_board_table( array $players ): string {
		$tbl = TablePrinter::TableStart();
		$tbl .= TablePrinter::HeaderStart();
		$tbl .= TablePrinter::AddHeader( "Position" );
		$tbl .= TablePrinter::AddHeader( "Player" );
		$tbl .= TablePrinter::AddHeader( "Score" );
		$tbl .= TablePrinter::HeaderEnd();
		$tbl .= TablePrinter::BodyStart();
		$i   = 1;
		foreach ( $players as $pdata ) {
			[ $pId, $score ] = $pdata[0];
			$player = get_post( $pId );
			$link   = get_post_permalink( $pId );
			$tbl    .= TablePrinter::AddColumn( $i );
			$tbl    .= TablePrinter::AddColumn( HtmlPrinter::AHref( $player->post_title, $link ) );
			$tbl    .= TablePrinter::AddColumn( $score );
		}
		$tbl .= TablePrinter::BodyEnd();
		$tbl .= TablePrinter::TableEnd();

		return $tbl;
	}


	/**
	 * @param int $total_games
	 * @param string $player_1_name
	 * @param $game_id
	 * @param string $player_2_name
	 *
	 * @return string
	 */
	public static function create_game_table( int $total_games, string $player_1_name, $game_id, string $player_2_name ): string {
		$tbl = TablePrinter::TableStart();
		$tbl .= TablePrinter::HeaderStart();
		$tbl .= TablePrinter::AddHeader( "Player", $total_games );
		$tbl .= TablePrinter::AddHeader( "GW" );
		for ( $i = 0; $i < $total_games; $i ++ ) {
			$index = $i + 1;
			$tbl   .= TablePrinter::AddHeader( "Game $index" );
		}
		$tbl .= TablePrinter::AddHeader( "GW" );
		$tbl .= TablePrinter::HeaderEnd();
		$tbl .= TablePrinter::BodyStart();
		$tbl .= TablePrinter::RowStart();
		$tbl .= TablePrinter::AddColumn( $player_1_name );
		for ( $i = 0; $i < $total_games; $i ++ ) {
			$score = GameHelper::get_player1_score( $game_id, $i );
			$tbl   .= TablePrinter::AddColumn( $score );
		}
		$tbl .= TablePrinter::RowEnd();

		$tbl .= TablePrinter::RowStart();
		$tbl .= TablePrinter::AddColumn( $player_2_name );
		for ( $i = 0; $i < $total_games; $i ++ ) {
			$score = GameHelper::get_player2_score( $game_id, $i );
			$tbl   .= TablePrinter::AddColumn( $score );
		}
		$tbl .= TablePrinter::RowEnd();
		$tbl .= TablePrinter::TableEnd();

		return $tbl;
	}

	public static function create_basic_stat_table( $player_id, $highest_score, $highest_single_wl_game_id, int $games_won, int $games_lost ) {
		$player_name   = get_the_title( $player_id );
		$link          = get_post_permalink( $player_id );
		$high_w_l      = get_the_title( $player_id );
		$high_w_l_link = get_post_permalink( $player_id );
		$tbl           = TablePrinter::TableStart();
		$tbl           .= TablePrinter::HeaderStart();
		$tbl           .= TablePrinter::AddHeader( "Player" );
		$tbl           .= TablePrinter::AddHeader( "Games Won" );
		$tbl           .= TablePrinter::AddHeader( "Games Lost" );
		$tbl           .= TablePrinter::AddHeader( "Highest Score" );
		$tbl           .= TablePrinter::AddHeader( "Best GW" );
		$tbl           .= TablePrinter::HeaderEnd();
		$tbl           .= TablePrinter::BodyStart();
		$tbl           .= TablePrinter::AddColumn( HtmlPrinter::AHref( $player_name, $link ) );
		$tbl           .= TablePrinter::AddColumn( $games_won );
		$tbl           .= TablePrinter::AddColumn( $games_lost );
		$tbl           .= TablePrinter::AddColumn( $highest_score );
		$tbl           .= TablePrinter::AddColumn( HtmlPrinter::AHref( $high_w_l, $high_w_l_link ) );

		$tbl .= TablePrinter::BodyEnd();
		$tbl .= TablePrinter::TableEnd();

		return $tbl;
	}
}

class BallShortCodeHandler {

	public static function leader_board( $atts ): string {
		$atts = shortcode_atts(
			array(),
			$atts,
			'leader_board'
		);

		$seasons = get_posts( [
			'post_type'      => WPBallObjectsRepository::SEASON_POST_TYPE,
			'posts_per_page' => - 1,
			'post_status'    => BallPostSaveHandler::$all_posts,
		] );
		$players = [];
		foreach ( $seasons as $season ) {
			$season_id = $season->ID;
			$scores    = ScoreHelper::get_season_scores( $season_id );
			$splayers  = PlayerHelper::map_scores_to_players( $scores );
			foreach ( $splayers as $player ) {
				if ( ! isset( $players[ $player->ID ] ) ) {
					$players[ $player->ID ] = 0;
				}
				$players[ $player->ID ] += ScoreHelper::get_total_player_points_for_season( $season_id );
			}

		}
		$ptmp = [];
		foreach ( array_keys( $players ) as $player_id ) {
			$ptmp[] = [ $player_id, $players[ $player_id ] ];
		}
		uasort( $ptmp, static function ( $a, $b ) use ( $ptmp ) {
			return $ptmp[ $a ][1] - $ptmp[ $b ][1];
		} );


		return ShortCodeHelpers::leader_board_table( $ptmp );

	}

	public static function season_leader_board( $atts ): string {
		$atts = shortcode_atts(
			array(
				'season_id' => '0',
			),
			$atts,
			'season_leader_board'
		);


		if ( $atts['season_id'] !== 0 ) {
			$season_id = $atts['season_id'];
		} else {
			$season_id = get_the_ID();
			if ( $season_id === 0 ) {
				return "No Such Season";
			}
		}

		$players  = [];
		$scores   = ScoreHelper::get_season_scores( $season_id );
		$splayers = PlayerHelper::map_scores_to_players( $scores );
		foreach ( $splayers as $player ) {
			if ( ! isset( $players[ $player->ID ] ) ) {
				$players[ $player->ID ] = 0;
			}
			$players[ $player->ID ] += ScoreHelper::get_total_player_points_for_season( $season_id );
		}

		$ptmp = [];
		foreach ( array_keys( $players ) as $player_id ) {
			$ptmp[] = [ $player_id, $players[ $player_id ] ];
		}
		uasort( $ptmp, static function ( $a, $b ) use ( $ptmp ) {
			return $ptmp[ $a ][1] - $ptmp[ $b ][1];
		} );


		return ShortCodeHelpers::leader_board_table( $ptmp );

	}


//todo: finish
	public static function player_season_stats( $atts ): string {
		$atts = shortcode_atts(
			array(
				'game_id' => '0',
			),
			$atts,
			'game_table'
		);


		if ( $atts['game_id'] !== 0 ) {
			$game_id = $atts['game_id'];
		} else {
			$game_id = get_the_ID();
			if ( $game_id === 0 ) {
				return "No Such Game";
			}
		}
		$player_one  = GameHelper::get_player1_ID( $game_id );
		$player1     = get_post( $player_one );
		$player_two  = GameHelper::get_player1_ID( $game_id );
		$player2     = get_post( $player_two );
		$total_games = GameHelper::get_game_count( $game_id );


		$player_1_name = $player1->post_title;
		$player_2_name = $player2->post_title;

		return ShortCodeHelpers::create_game_table( $total_games, $player_1_name, $game_id, $player_2_name );

	}

//todo: finish
	public static function player_stats( $atts ): string {
		$atts = shortcode_atts(
			array(
				'player_id' => '0',
			),
			$atts,
			'player_stats'
		);


		if ( $atts['game_id'] !== 0 ) {
			$player_id = $atts['player_id'];
		} else {
			$player_id = get_the_ID();
			if ( $player_id === 0 ) {
				return "No Such Game";
			}
		}

		$games_lost    = count( GameHelper::get_won_games( $player_id ) );
		$games_won     = count( GameHelper::get_won_games( $player_id ) );
		$highest_score = GameHelper::get_highest_score( $player_id );
		$game_id_wl    = GameHelper::get_best_win_loss( $player_id );

		return ShortCodeHelpers::create_basic_stat_table( $player_id, $highest_score, $game_id_wl, $games_won, $games_lost );

	}

	public static function game_table( $atts ): string {
		$atts = shortcode_atts(
			array(
				'game_id' => '0',
			),
			$atts,
			'game_table'
		);


		if ( $atts['game_id'] !== 0 ) {
			$game_id = $atts['game_id'];
		} else {
			$game_id = get_the_ID();
			if ( $game_id === 0 ) {
				return "No Such Game";
			}
		}
		$player_one  = GameHelper::get_player1_ID( $game_id );
		$player1     = get_post( $player_one );
		$player_two  = GameHelper::get_player1_ID( $game_id );
		$player2     = get_post( $player_two );
		$total_games = GameHelper::get_game_count( $game_id );


		$player_1_name = $player1->post_title;
		$player_2_name = $player2->post_title;

		return ShortCodeHelpers::create_game_table( $total_games, $player_1_name, $game_id, $player_2_name );

	}

	public static function match_table( $atts ): string {
		$atts = shortcode_atts(
			array(
				'match_id' => '0',
			),
			$atts,
			'match_table'
		);


		if ( $atts['match_id'] !== 0 ) {
			$match_id = $atts['match_id'];
		} else {
			$match_id = get_the_ID();
			if ( $match_id === 0 ) {
				return "No Such Match";
			}
		}
		$match = get_post( $match_id );

		return ShortCodeHelpers::create_match_table( $match );

	}

	public static function season_table( $atts ): string {
		$atts = shortcode_atts(
			array(
				'season_id' => '0',
			),
			$atts,
			'season_table'
		);


		if ( $atts['season_id'] !== 0 ) {
			$season_id = $atts['season_id'];
		} else {
			$season_id = get_the_ID();
			if ( $season_id === 0 ) {
				return "No Such Season";
			}
		}
		$season  = get_post( $season_id );
		$matches = MatchHelper::get_season_matches( $season_id );


		return ShortCodeHelpers::create_season_table( $season, $matches );

	}

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


