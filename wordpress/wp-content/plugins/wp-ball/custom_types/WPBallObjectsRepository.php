<?php



class WPBallObjectsRepository {
	public static function Update( PinballObject $post, $wp_error = false ) {
		return wp_update_post( $post->get_post_data(), $wp_error );
	}

	public function Set( PinballObject $post, $name, $value ) {
		return update_post_meta( $post->post->ID, "{$post->text_domain}{$name}", $value );
	}

	public static function Save( PinballObject $post, $wp_error = false ) {
		return wp_insert_post( $post->get_post_data(), $wp_error );
	}

	public static function LoadById( $id, $type ) {
		$post = get_post( $id );
		return $post ? new PinballObject( $post, $type ) : null;
	}

	public const MACHINE_POST_TYPE = 'Machine';
	public const PLAYER_POST_TYPE = 'Player';

	public const SCORE_POST_TYPE = 'Score';
	public const MATCH_POST_TYPE = 'Match';
	public const SEASON_POST_TYPE = 'Season';

	public const STATISTIC_POST_TYPE = 'Statistic';

	public static function LoadBySlug( $slug, $type ) {

		$args         = array(
			'name'      => $slug,
			'post_type' => $type,

			'numberposts' => 1
		);
		$custom_posts = get_posts( $args );
		if ( count( $custom_posts ) > 0 ) {
			return new PinballObject( $custom_posts[0], $type );
		}

		return null;

	}


}