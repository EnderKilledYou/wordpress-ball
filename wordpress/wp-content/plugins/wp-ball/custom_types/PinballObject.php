<?php

  class PinballObject {

	public WP_Post $post;

	/**
	 * @var string
	 */
	private string $type;

	public function __construct( $post, $type ) {
		$this->post = $post;
		$this->type = $type;
	}

	public function get_post_data(): array {
		return array(
			'comment_status' => 'closed',
			'post_name'      => $this->post->post_name,
			'post_title'     => $this->post->post_title,
			'post_type'      => $this->type,
			'post_status'    => $this->post->post_status,
		);
	}

	public function SetName( $name ) {
		$this->post->post_name = $name;

		return $this;

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

	public string $text_domain = "wp_pinball_";



}
