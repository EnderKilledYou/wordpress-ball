<?php

abstract class WPBallObjectsRepository {
	abstract public function LoadById($id);

	abstract public function LoadBySlug($slug);

	public static function Update( PostTypeClass $post, $wp_error = false ) {
		return wp_update_post( $post->get_post_data(), $wp_error );


	}

	public static function Save( PostTypeClass $post, $wp_error = false ) {
		return wp_insert_post( $post->get_post_data(), $wp_error );



	}
}