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
}