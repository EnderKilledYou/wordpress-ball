<?php


function create_default_users() {
	$player_names  = [
		'Jon Ucran',
		'Derek Kurze',
		'Mike Ramos',
		'Dave Crudele',
		'Paul Allen',
		'John MacDonald',
		'Melissa Brailsford',
		'Bob Andreoli',
		'Tony DiGregorio',
		'John Shanahan',
		'John MacDonald',
		'Wayne Malm',
		'Steve Fratelli',
		'Matt Szewczyk',
		'Jenny Szewczyk'
	];
	$machine_names = [
		'Deadpool',
		'Godzilla',
		'Elvira',
		'Iron Maiden',
		'Medieval Madness',
		'Metallica',
		'Lord of the Rings',
		'Dr. Dude',
		'Genie'
	];

	$players  = [];
	$machines = [];
	foreach ( $machine_names as $machine_name ) {
		$player_already_created = get_posts( [
			'post_type'   => WPBallObjectsRepository::MACHINE_POST_TYPE,
			"title"       => $machine_name,
			'post_status' => BallPostSaveHandler::$all_posts
		] );

		if ( count( $player_already_created ) === 0 ) {
			$machines[] = $tmp_id = wp_insert_post( [
				'post_type'    => WPBallObjectsRepository::MACHINE_POST_TYPE,
				'post_title'   => $machine_name,
				'post_name'    => $machine_name,
				'post_content' => '',

			] );
			wp_publish_post( $tmp_id );

		}


	}
	foreach ( $player_names as $player_name ) {
		$player_already_created = get_posts( [
			'post_type'   => WPBallObjectsRepository::PLAYER_POST_TYPE,
			"title"       => $player_name,
			'post_status' => BallPostSaveHandler::$all_posts
		] );

		if ( count( $player_already_created ) === 0 ) {
			$user_id = wp_create_user( $player_name, $player_name, "{$player_name}@silverballers.com" );
			$u       = new WP_User( $user_id );

			$u->set_role( 'author' );
			$players[] = $tmp_id = wp_insert_post( [
				'post_type'    => WPBallObjectsRepository::PLAYER_POST_TYPE,
				'post_title'   => $player_name,
				'post_name'    => $player_name,
				'post_content' => '[playerwins]',
				'post_date'    => null
			] );
			wp_publish_post( $tmp_id );
			PlayerHelper::set_player_user_id( $tmp_id, $user_id );

		} else {
			$players[] = $player_already_created[0]->ID;

		}


	}


}

function create_season() {
	$players_wp = PlayerHelper::get_players();

	$_REQUEST['players'] = array_map( static function ( $e ) {
		return $e->ID;
	}, $players_wp );
	$name                 = 'test season ' . random_int( 1, 1000 );
	$season_id           = wp_insert_post( [
		'post_type'  => WPBallObjectsRepository::SEASON_POST_TYPE,
		'post_title' => $name,

		'post_name' => $name,
	] );
	unset( $_REQUEST['players'] );
	$_REQUEST['generate_matches'] = true;
	$_REQUEST['start_date']       = date( 'm/d/Y' );
	wp_publish_post( $season_id );

	return $season_id;
}

function fill_scores( $season_id ) {
	$player_avg_score = [ 1000, 1000, 10000, 10000, 10000, 100000, 1000000, 1000000 ];

	$players_wp = PlayerHelper::get_players();

	$players = array_map( static function ( $e ) {
		return $e->ID;
	}, $players_wp );
	$sgames  = GameHelper::get_games_by_season( $season_id );

	$i = 0;

	foreach ( $players as $player_id ) {
		$games = GameHelper::get_player_games_by_season( $player_id, $season_id );
		foreach ( $games as $game ) {
			$game_id    = $game->ID;
			$game_count = GameHelper::get_game_count( $game_id );
			for ( $game_index = 0; $game_index < $game_count; $game_index ++ ) {
				if ( $player_id === (int) GameHelper::get_player1_ID( $game_id ) ) {
					$variance = random_int( 1, 10000 );
					GameHelper::update_player1_score( $game_id, 100000 + $variance );
				} else {
					$variance = random_int( 1, 10000 );
					GameHelper::update_player2_score( $game_id, 100000 + $variance );
				}
			}
		}

	}
	foreach ( $sgames as $game ) {
		$game_id    = $game->ID;
		$game_count = GameHelper::get_game_count( $game_id );
		$player1    = GameHelper::get_player1_ID( $game_id );
		$player2    = GameHelper::get_player1_ID( $game_id );
		for ( $game_index = 0; $game_index < $game_count; $game_index ++ ) {

			$player_1_score = GameHelper::get_player_score_from_game( $player1, $game_id, $game_index );

			$player_2_score = GameHelper::get_player_score_from_game( $player2, $game_id, $game_index );
			if ( $player_1_score > $player_2_score ) {
				$winner_id = $player1;
			} else {
				$winner_id = $player2;
			}


			$bwinner_player_1 = $player1 === $winner_id;

			GameHelper::update_game_complete( $game_id, $bwinner_player_1, $game_index );

		}
		$player_1_score = GameHelper::get_player_win_count_for_game( $player1, $game_id );

		$player_2_score = GameHelper::get_player_win_count_for_game( $player2, $game_id );
		if ( $player_1_score > $player_2_score ) {
			$winner_id = $player1;
		} else {
			$winner_id = $player2;
		}
		$bwinner_player_1 = $winner_id === $player1;
		GameHelper::update_game_complete( $game_id, $bwinner_player_1, - 1 );
	}

}


function test_save_season(): void {
	$players = PlayerHelper::get_players();

	$player                 = $players[0];
	$_REQUEST['players']    = [ $player->ID ];
	$_REQUEST['post_title'] = "test abc";
	$seasons                = PlayerHelper::get_seasons();
	$season                 = $seasons[0];
	BallPostSaveHandler::SaveSeason( $season->ID );
	$player_score = ScoreHelper::get_player_score_for_season( $season->ID, $player->ID );
	assertNotNull( $player_score );
}

$add_users = false;
if ( isset( $_REQUEST['add_users'] ) ) {
	create_default_users();
	$add_users = true;
}

$added_season = false;
if ( isset( $_REQUEST['add_season'] ) ) {
	create_season();
	$add_users = true;
}

$added_season = false;
if ( isset( $_REQUEST['add_scores'] ) ) {
	fill_scores( (int) $_REQUEST['add_scores'] );
	$add_users = true;
}