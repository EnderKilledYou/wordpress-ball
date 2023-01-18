<?php
/**
 * Class SampleTest
 *
 * @package Wp_Ball
 */

/**
 * Sample test case.
 */

use PHPUnit\Framework\TestCase;

include_once( '/var/www/html/wp-load.php' );

final class StackTest extends TestCase {
//	public function test_create_player_score(): void {
//		//	$player = get_post( 12 );
//		//	ScoreHelper::create_player_score_for_season( 28, $player, 'season 1' );
//	}
//	public function test_create_player(): void {
//	 BallPostSaveHandler::SavePlayer(16);
//	}
//	public function test_update_season(): void {
//		$player_names = [ 'john', 'tim', 'doug', 'frank', 'bilbo', 'tommy', 'francis', 'johhny' ];
//		$players      = [];
//
//		foreach ( $player_names as $player_name ) {
//			$player_already_created = get_posts( [
//				'post_type'   => WPBallObjectsRepository::PLAYER_POST_TYPE,
//				"title"       => $player_name,
//				'post_status' => BallPostSaveHandler::$all_posts
//			] );
//
//			if ( count( $player_already_created ) === 0 ) {
//				$players[] = $tmp_id = wp_insert_post( [
//					'post_type'    => WPBallObjectsRepository::PLAYER_POST_TYPE,
//					'post_title'   => $player_name,
//					'post_status ' => 'publish',
//					'post_content' => '[playerwins]',
//					'post_date'    => null
//				] );
//				wp_publish_post( $tmp_id );
//
//			} else {
//				$players[] = $player_already_created[0]->ID;
//
//			}
//
//
//		}
//		$_REQUEST['players'] = $players;
//		$season_id           = wp_insert_post( [
//			'post_type'    => WPBallObjectsRepository::SEASON_POST_TYPE,
//			'post_title'   => 'test season',
//			'post_status ' => 'publish'
//		] );
//		unset( $_REQUEST['players'] );
//		$_REQUEST['generate_matches'] = true;
//		$_REQUEST['start_date']       = date( 'm/d/Y' );
//		wp_publish_post( $season_id );
//	}

	public function test_short_codes(): void {


		$season_id                    = create_first_default_season();
		$_REQUEST['generate_matches'] = true;
		$_REQUEST['start_date']       = date( 'm/d/Y', strtotime( "+ 10 weeks" ) );
		//ScheduleHelper::RebuildSchedule( $season_id );

		return;
		$last_game = GameHelper::get_last_game( 12 );

		$games = GameHelper::get_games_by_season( $season_id );
		foreach ( $games as $game ) {
			$id         = $game->ID;
			$game_count = GameHelper::get_game_count( $id );
			for ( $game_index = 0; $game_index < $game_count; $game_index ++ ) {
				GameHelper::update_player1_score( $id, 10000, $game_index );
				GameHelper::update_player2_score( $id, 1000, $game_index );
				GameHelper::update_game_complete( $id, true, $game_index );
			}
			GameHelper::update_player1_score( $id, 10000, - 1 );
			GameHelper::update_player2_score( $id, 1000, - 1 );
			GameHelper::update_game_complete( $id, true, - 1 );
		}


		///$tbl       = BallShortCodeHandler::season_table( [ 'season_id' => $season_id ] );
	}

//	public function test_update_2sseason(): void {
//		$player_avg_score = [ 1000, 1000, 10000, 10000, 10000, 100000, 1000000, 1000000 ];
//		$season_id        = create_first_default_season();
//		$players          = $_REQUEST['players'];
//		$sgames           = GameHelper::get_games_by_season( $season_id );
//		$i                = 0;
//		foreach ( $players as $player_id ) {
//			$games = GameHelper::get_player_games_by_season( $player_id, $season_id );
//			foreach ( $games as $game ) {
//				if ( $player_id === (int) GameHelper::get_player1_ID( $game->ID ) ) {
//					$variance = random_int( 1, $player_avg_score[ $i ] * .1 );
//					if ( random_int( 0, 4 ) === 1 ) {
//						$variance *= - 1;
//					}
//					GameHelper::update_player1_score( $game->ID, $player_avg_score[ $i ] + $variance );
//				}
//
//			}
//			$i ++;
//		}
//		foreach ( $sgames as $game ) {
//			$player1_score = GameHelper::get_player1_score( $game->ID );
//			$player2_score = GameHelper::get_player2_score( $game->ID );
//
//			$bwinner_player_1 = $player1_score > $player2_score;
//			GameHelper::update_game_complete( $game->ID, $bwinner_player_1 );
//
//		}
//		$_REQUEST['players'] = $players;
//		$season_id2          = wp_insert_post( [
//			'post_type'    => WPBallObjectsRepository::SEASON_POST_TYPE,
//			'post_title'   => 'test season 2',
//			'post_status ' => 'publish'
//		] );
//		unset( $_REQUEST['players'] );
//		$_REQUEST['generate_matches'] = true;
//		$_REQUEST['start_date']       = date( 'm/d/Y', strtotime( "+ 10 weeks" ) );
//		wp_publish_post( $season_id2 );
//	}
	//	public function test_save_season(): void {
//		$players = PlayerHelper::get_players();
//
//		$player                 = $players[0];
//		$_REQUEST['players']    = [ $player->ID ];
//		$_REQUEST['post_title'] = "test abc";
//		$seasons                = PlayerHelper::get_seasons();
//		$season                 = $seasons[0];
//		BallPostSaveHandler::SaveSeason( $season->ID );
//		$player_score = ScoreHelper::get_player_score_for_season( $season->ID, $player->ID );
//		assertNotNull($player_score);
//	}


}

//
function create_first_default_season() {
	$player_names  = PlayerHelper::get_players();
	$machine_names = MachineHelper::get_machines();

	$players  = array_map( static function ( $p ) {
		return $p->ID;
	}, $player_names );
	$machines = [];
//	$players = generate_players_and_machines( $machine_names, $machines, $player_names, $players );
	$_REQUEST['players']     = $players;
	$_REQUEST['match_size']  = 5;
	$_REQUEST['match_count'] = 4;
	$season_id               = wp_insert_post( [
		'post_type'  => WPBallObjectsRepository::SEASON_POST_TYPE,
		'post_title' => 'test season',

		'post_name' => 'test season',
	] );
	unset( $_REQUEST['players'] );
	$_REQUEST['generate_matches'] = true;
	$_REQUEST['start_date']       = date( 'm/d/Y' );
	wp_publish_post( $season_id );

	return $season_id;

}
//
///**
// * @param array $machine_names
// * @param array $machines
// * @param array $player_names
// * @param array $players
// *
// * @return array
// */
//function generate_players_and_machines( array $machine_names, array $machines, array $player_names, array $players ): array {
//	foreach ( $machine_names as $machine_name ) {
//		$player_already_created = get_posts( [
//			'post_type'   => WPBallObjectsRepository::MACHINE_POST_TYPE,
//			"title"       => $machine_name,
//			'post_status' => BallPostSaveHandler::$all_posts
//		] );
//
//		if ( count( $player_already_created ) === 0 ) {
//			$machines[] = $tmp_id = wp_insert_post( [
//				'post_type'    => WPBallObjectsRepository::MACHINE_POST_TYPE,
//				'post_title'   => $machine_name,
//				'post_name'    => $machine_name,
//				'post_content' => '',
//
//			] );
//			wp_publish_post( $tmp_id );
//
//		}
//
//
//	}
//	foreach ( $player_names as $player_name ) {
//		$player_already_created = get_posts( [
//			'post_type'   => WPBallObjectsRepository::PLAYER_POST_TYPE,
//			"title"       => $player_name,
//			'post_status' => BallPostSaveHandler::$all_posts
//		] );
//
//		if ( count( $player_already_created ) === 0 ) {
//			$players[] = $tmp_id = wp_insert_post( [
//				'post_type'    => WPBallObjectsRepository::PLAYER_POST_TYPE,
//				'post_title'   => $player_name,
//				'post_name'    => $player_name,
//				'post_content' => '[playerwins]',
//				'post_date'    => null
//			] );
//			wp_publish_post( $tmp_id );
//
//		} else {
//			$players[] = $player_already_created[0]->ID;
//
//		}
//
//
//	}
//
//	return $players;
//}
