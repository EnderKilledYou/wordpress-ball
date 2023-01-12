<?php

class MachineHelper {

	/**
	 * @return WP_Post[]
	 */
	public static function get_machines(): array {
		return get_posts( [
			'post_type' => WPBallObjectsRepository::MACHINE_POST_TYPE,

			'post_status' => BallPostSaveHandler::$all_posts
		] );

	}

	public static function get_lowest_machine( int $p1, int $p2 ): int {

		$games = GameHelper::get_all_vs_player_games( $p1, $p2 );

		$machines = get_posts( [
			'post_type' => WPBallObjectsRepository::MACHINE_POST_TYPE,

			'post_status' => BallPostSaveHandler::$all_posts
		] );
		$counter  = [];
		foreach ( $machines as $machine ) {
			if ( ! isset( $counter[ $machine->ID ] ) ) {
				$counter[ $machine->ID ] = 0;
			}
			$counter[ $machine->ID ] ++;
		}
		foreach ( $games as $game ) {
			$machine_id = GameHelper::get_game_machine( $game->ID );
			if ( $machine_id === 0 || ! isset( $counter[ $machine_id ] ) ) {
				continue;
			}
			$counter[ $machine_id ] ++;
		}
		$items = array_map( static function ( $id ) use ( $counter ) {
			return [ $id, $counter[ $id ], MachineHelper::get_total_games_played( $id ) ];
		}, array_keys( $counter ) );
		usort( $items, static function ( $a, $b ) {
			if ( $a[1] === $b[1] ) {
				return $a[2] - $b[2];
			}

			return $a[1] - $b[1];
		} );
		$machine = $items[0];

		return $machine[0];

	}

	private static string $total_games = 'total_games';
	private static string $play_count = 'play_count_for_';

	/**
	 * @param int $p1
	 * @param int $p2
	 *
	 * @return string
	 */
	private static function get_ordered_id_str( int $p1, int $p2 ): string {
		if ( $p1 < $p2 ) {
			$str = "{$p2}_$p1";
		} else {
			$str = "{$p1}_$p2";
		}

		return $str;
	}

	public static function add_play_count( $machine_id, int $p1, int $p2 ) {
		$str   = self::get_ordered_id_str( $p1, $p2 );
		$count = self::get_play_count( $machine_id, $str );
		update_post_meta( $machine_id, self::$play_count . $str, (int) $count + 1 );


	}

	private static function get_play_count( $machine_id, string $str ) {
		$count = get_post_meta( $machine_id, self::$play_count . $str, true );
		if ( ! $count ) {
			return 0;
		}

		return (int) $count;
	}

	public static function increment_total_games_played( int $machine_id ) {
		$count = get_post_meta( $machine_id, self::$total_games, true );
		if ( ! $count ) {

			$count = 0;
		}
		update_post_meta( $machine_id, self::$total_games, $count + 1 );

	}

	public static function get_total_games_played( int $machine_id ) {
		$count = get_post_meta( $machine_id, self::$total_games, true );
		if ( ! $count ) {
			update_post_meta( $machine_id, self::$total_games, 0 );
			$count = 0;
		}

		return (int) $count;

	}
}