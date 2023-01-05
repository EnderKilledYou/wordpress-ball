<?php

abstract class PostTypeClass {

	protected WP_Post $post;

	abstract public function get_post_data();


	public function SetName( $name ) {
		$this->post->post_name = $name;

		return $this;
		//update_post_meta( $this->post->ID, "$key", $value );
	}

	public function SetTitle( $title ) {
		$this->post->post_title = $title;

		return $this;
	}

	public function SetStatus( $active ) {
		if ( $active ) {
			$this->post->post_status = 'publish';
		} else {
			$this->post->post_status = 'draft';
		}

		return $this;
	}

}
