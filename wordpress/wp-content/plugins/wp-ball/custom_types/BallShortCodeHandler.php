<?php

class BallTitleHandler {
	public static function game_title( $title, $id ) {

		if ( ! $id ) {
			return $title;
		}
		$post = get_post( $id );
		if ( ! $post ) {
			return $title;
		}

		if ( $post->post_type === strtolower( WPBallObjectsRepository::GAME_POST_TYPE ) ) {

			$player1 = get_post( GameHelper::get_player1_ID( $id ) );
			$player2 = get_post( GameHelper::get_player2_ID( $id ) );
			$machine = get_post( GameHelper::get_game_machine( $id ) );

			return "$player1->post_title vs $player2->post_title on " . $machine->post_title;
		}

		return $title;
	}
}

class HtmlPrinter {
	public static function AHref( $text, $link, $classes = [] ) {
		$class_string = implode( " ", $classes );

		return "<a href='$link' class='$class_string'>$text	</a>";
	}
}

class TablePrinter {
	public static function TableStart( $classes = [ 'wp-list-table' ] ) {
		$class_string = implode( " ", $classes );

		return "<table class=\"$class_string\">";
	}

	public static function TableHeading( $text, $classes = [] ) {
		$class_string = implode( " ", $classes );

		return "<h2 class=\"$class_string\">$text</h2>";
	}

	public static function HeaderStart( $classes = [] ) {
		$class_string = implode( " ", $classes );

		return "<thead><tr class=\"$class_string\">";

	}

	public static function AddHeader(
		$name, $col_span = 1, $classes = [

		'column-title',
		'column-primary',
		'sortable',
		'desc'
	]
	) {
		$class_str = implode( ' ', $classes );

		return "<th scope=\"col\"   class=\"$class_str\" colspan=\"$col_span\">
			<span>$name</span>
		</th>";
	}

	public static function HeaderEnd() {
		return "</tr></thead>";
	}

	public static function BodyStart() {
		return "<tbody>";

	}

	public static function RowStart( $classes = [] ) {
		$class_str = implode( ' ', $classes );

		return "<tr  class=\"$class_str\" >";

	}

	public static function AddColumn( $name, $col_span = 1, $classes = [] ) {
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

	public static function create_season_table( $season_id ) {
		$matches     = MatchHelper::get_season_matches( $season_id );
		$matches_str = '';
		foreach ( $matches as $match ) {
			$matches_str .= self::create_match_table( $match );
		}

		return $matches_str;
	}

	/**
	 * @param $game_id
	 *
	 * @return string
	 */
	public static function get_table_from_game_id( $game_id ): string {
		$player_one  = GameHelper::get_player1_ID( $game_id );
		$player1     = get_post( $player_one );
		$player_two  = GameHelper::get_player2_ID( $game_id );
		$player2     = get_post( $player_two );
		$total_games = GameHelper::get_game_count( $game_id );
		$winner      = GameHelper::get_match_winner( $game_id );

		$player_2_winner  = $player_two === $winner;
		$player_1_winner  = $player_one === $winner;
		$player_1_name    = $player1->post_title;
		$player_2_name    = $player2->post_title;
		$machine_name     = GameHelper::get_game_machine( $game_id );
		$machine_name     = get_the_title( $machine_name );
		$get_player1_wins = GameHelper::get_player_win_loss_of_game( $player_one, $game_id );
		$get_player2_wins = GameHelper::get_player_win_loss_of_game( $player_two, $game_id );

		return ShortCodeHelpers::create_game_table( $total_games, $player_1_name, $game_id, $player_2_name, $machine_name, $get_player1_wins[0], $get_player2_wins[0], $player_1_winner, $player_2_winner );
	}

	public static function create_match_table( WP_Post $match, $match_index, $game_index ) {
		$date  = MatchHelper::get_date( $match->ID );
		$games = GameHelper::get_match_games( $match->ID );

		$tbl = TablePrinter::TableHeading( $date );
		$tbl .= TablePrinter::TableStart();
		$tbl .= TablePrinter::BodyStart();
		$tbl .= TablePrinter::BodyEnd();
		$tbl .= TablePrinter::TableEnd();
		$curr_match_index = 0;

		foreach ( $games as $game ) {
			$game_group_index = GameHelper::get_match_count_index( $game->ID );
			//the group or all groups
			if ( ( $match_index >= 0 ) && $game_group_index !== $match_index ) {
				continue;
			}
			if ( $game_index >= 0 ) { // the game in the group
				$game_match_index = GameHelper::get_match_size_index( $game->ID );
				if ( $game_match_index !== $game_index ) {
					continue;
				}
			}
			if($game_group_index !== $curr_match_index) {
				$match_printed_index = $game_group_index + 1;
				$tbl .= "<h2>Match $match_printed_index</h2>";
			}
			$game_id = $game->ID;
			$tbl     .= self::get_table_from_game_id( $game_id );

		}


		return $tbl;
	}

	public static function leader_board_table( array $players ): string {
		$tbl = TablePrinter::TableStart( [ 'leader_table' ] );
		$tbl .= TablePrinter::HeaderStart();
		$tbl .= TablePrinter::AddHeader( "Position" );
		$tbl .= TablePrinter::AddHeader( "Player" );
		$tbl .= TablePrinter::AddHeader( "Wins" );
		$tbl .= TablePrinter::AddHeader( "Game wins" );
		$tbl .= TablePrinter::AddHeader( "Last Week" );
		$tbl .= TablePrinter::HeaderEnd();
		$tbl .= TablePrinter::BodyStart();
		$i   = 1;
		foreach ( $players as $pdata ) {
			$tbl .= TablePrinter::RowStart();
			[ $pId, $score, $total_gw ] = $pdata;
			$player    = get_post( $pId );
			$link      = get_post_permalink( $pId );
			$last_game = GameHelper::get_last_game( $pId );


			if ( $last_game !== 0 ) {
				$last_week = GameHelper::get_player_win_loss_of_game( $pId, $last_game );
			} else {
				$last_week = [ 0, 0 ];

			}

			$tbl .= TablePrinter::AddColumn( $i ++ );
			$tbl .= TablePrinter::AddColumn( HtmlPrinter::AHref( $player->post_title, $link ) );
			$tbl .= TablePrinter::AddColumn( $score );
			$tbl .= TablePrinter::AddColumn( $total_gw );
			$tbl .= TablePrinter::AddColumn( "{$last_week[0]} - {$last_week[1]}" );
			$tbl .= TablePrinter::RowEnd();
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
	public static function create_game_table( int $total_games, string $player_1_name, $game_id, string $player_2_name, $machine_name, int $player1_win_count, int $player2_win_count, $player_1_winner, $player_2_winner ): string {
		$tbl = TablePrinter::TableStart( [ 'game_table' ] );
		$tbl .= TablePrinter::HeaderStart();
		$tbl .= TablePrinter::AddHeader( "Player" );


		for ( $i = 0; $i < $total_games; $i ++ ) {
			$index = $i + 1;
			$tbl   .= TablePrinter::AddHeader( "Game $index" );
		}
		$tbl .= TablePrinter::AddHeader( "GW", );
		$tbl .= TablePrinter::HeaderEnd();
		$tbl .= TablePrinter::BodyStart();
		if ( $player_1_winner ) {
			$tbl .= TablePrinter::RowStart( [ 'winner' ] );
		} else {
			$tbl .= TablePrinter::RowStart();
		}
		$tbl .= TablePrinter::AddColumn( $player_1_name );
		for ( $i = 0; $i < $total_games; $i ++ ) {
			$score = GameHelper::get_player1_score( $game_id, $i );
			$tbl   .= TablePrinter::AddColumn( $score );
		}
		$tbl .= TablePrinter::AddColumn( $player1_win_count );
		$tbl .= TablePrinter::RowEnd();

		$tbl .= TablePrinter::RowStart();
		$tbl .= TablePrinter::AddColumn( $player_2_name );
		for ( $i = 0; $i < $total_games; $i ++ ) {
			$score = GameHelper::get_player2_score( $game_id, $i );
			$tbl   .= TablePrinter::AddColumn( $score );

		}
		$tbl .= TablePrinter::AddColumn( $player2_win_count );
		$tbl .= TablePrinter::RowEnd();


		$tbl .= TablePrinter::RowStart( [ 'machine' ] );
		$tbl .= TablePrinter::AddColumn( $machine_name );
		$tbl .= str_repeat( TablePrinter::AddColumn( '' ), $total_games );
		$tbl .= TablePrinter::AddColumn( '' );
		$tbl .= TablePrinter::RowEnd();


		$tbl .= TablePrinter::TableEnd();

		return $tbl;
	}

	public static function create_basic_stat_table( $player_id, $highest_score, $highest_single_wl_game_id, int $games_won, int $games_lost ) {
		$player_name   = get_the_title( $player_id );
		$link          = get_post_permalink( $player_id );
		$high_w_l      = get_the_title( $highest_single_wl_game_id );
		$high_w_l_link = get_post_permalink( $highest_single_wl_game_id );
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
				$players[ $player->ID ] = ScoreHelper::get_player_season_wins( $season_id );
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
				'season_id' => 0,
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
			$players[ $player->ID ] = count( GameHelper::get_won_games_by_season( $player->ID, $season_id ) );
		}

		$ptmp = [];
		foreach ( array_keys( $players ) as $player_id ) {
			$total_game_wins  = GameHelper::get_won_games_by_season( $player_id, $season_id );
			$total_game_count = 0;
			foreach ( $total_game_wins as $game_win ) {
				$total_game_count += GameHelper::get_player_win_count_for_game( $player_id, $game_win->ID );
			}
			$ptmp[] = [ $player_id, $total_game_count, $players[ $player_id ] ];
		}
		uasort( $ptmp, static function ( $a, $b ) {
			return $a [1] - $b [1];
		} );


		return ShortCodeHelpers::leader_board_table( $ptmp );

	}


//todo: finish
	public static function player_season_stats( $atts ): string {
		$atts = shortcode_atts(
			array(
				'game_id' => 0,
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
		$winner        = GameHelper::get_match_winner( $game_id );

		$player_2_winner = $player2 === $winner;
		$player_1_winner = $player1 === $winner;
		$machine_name    = GameHelper::get_game_machine( $game_id );
		$machine_name    = get_the_title( $machine_name );
//int $total_games, string $player_1_name, $game_id, string $player_2_name, $machine_name, int $player1_win_count, int $player2_win_count, $player_1_winner, $player_2_winner
		//return ShortCodeHelpers::create_game_table( $total_games, $player_1_name, $game_id, $player_2_name, $machine_name $player_1_winner, $player_2_winner,$player_1_winner,$player_2_winner );
		return "";
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


		if ( $atts['player_id'] !== 0 ) {
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
				'game_id' => 0,
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

		return ShortCodeHelpers::get_table_from_game_id( $game_id );

	}

	public static function match_table( $atts ): string {
		$atts = shortcode_atts(
			array(
				'match_id'    => 0,
				'match_index' => - 1,
				'game_index'  => - 1,
			),
			$atts,
			'match_table'
		);

		$id          = (int) $atts['match_id'];
		$match_index = (int) $atts['match_index'];
		$game_index  = (int) $atts['game_index'];
		if ( $id !== 0 ) {
			$match_id = $id;
		} else {
			$match_id = get_the_ID();
			if ( $match_id === 0 ) {
				return "No Such Match";
			}
		}
		$match = get_post( $match_id );

		//create_game_tablez
		return ShortCodeHelpers::create_match_table( $match, $match_index, $game_index );

	}

	public static function season_table( $atts ): string {
		$atts = shortcode_atts(
			array(
				'season_id' => 0,
			),
			$atts,
			'season_table'
		);


		$id = (int) $atts['season_id'];
		if ( $id !== 0 ) {
			$season_id = $id;
		} else {
			$season_id = get_the_ID();
			if ( $season_id === (int) 0 ) {
				return "No Such Season";
			}
		}


		return ShortCodeHelpers::create_season_table( $season_id );


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


