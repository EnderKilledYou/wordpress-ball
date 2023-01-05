<?php

class WPBallMachine extends PostTypeClass {



	public function __construct( $post ) {
		$this->post = $post;
	}

	public function get_post_data(): array {
		return array(
			'comment_status' => 'closed',
			'post_name'      => $this->post->post_name,
			'post_title'     => $this->post->post_title,
			'post_type'      => WPBallMachineRepository::POST_TYPE,
			'post_status'    => $this->post->post_status,
		);
	}

	public function get_post_meta(): array {
		return array();
	}

}

