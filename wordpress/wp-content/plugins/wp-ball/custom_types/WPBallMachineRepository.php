<?php

class WPBallMachineRepository extends WPBallObjectsRepository {

	public function LoadById( $id ) {
		$post = get_post( $id );

		return $post ? new WPBallMachine( $post ) : null;
	}

	public const POST_TYPE = 'Machine';

	public function LoadBySlug( $slug ) {
		$args         = array(
			'name'      => $slug,
			'post_type' => self::POST_TYPE,

			'numberposts' => 1
		);
		$custom_posts = get_posts( $args );
		if ( count( $custom_posts ) > 0 ) {
			return new WPBallMachine( $custom_posts[0] );
		}

		return null;

	}
}