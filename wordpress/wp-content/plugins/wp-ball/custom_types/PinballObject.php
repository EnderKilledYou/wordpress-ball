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

			'post_title'     => $this->post->post_title,
			'post_type'      => $this->type,
			'post_status'    => $this->post->post_status,
		);
	}





	public string $text_domain = "wp_pinball_";



}
