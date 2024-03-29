<?php
add_filter( 'wp_dropdown_pages', 'wp_ball_make_multiple_select_pages' );
function wp_ball_make_multiple_select_pages( $output ) {
	return str_replace( '<select ', '<select multiple="multiple" ', $output );
}

class BallRegister {
	public static function RegisterPostTypes(): void {
		self::RegisterGames();
		self::RegisterPLAYER();
		self::RegisterMATCH();
		self::RegisterSCORE();

		self::RegisterSEASON();
		self::RegisterMachine();
		self::RegisterAdminNotice();
		//generic multi ones
	//	add_filter( 'user_has_cap', 'BallRegister::prevent_post_delete', 10, 3 );
		add_filter( 'user_has_cap', 'BallRegister::allow_co_author', 10, 3 );
		add_action( "edit_form_after_title", "BallPostFormHandler::Edit_form_after_titles" );

	}

	public static function allow_co_author( $allcaps, $caps, $args ) {
		if ( 'edit_post' !== $args[0] ) {
			return $allcaps;
		}

		if ( isset( $allcaps['edit_others_posts'] ) ) {


			return $allcaps;
		}

		$user_id = $args[1];
		$post_id = $args[2];
		$post    = get_post( $post_id );
		if ( $args[1] === $post->post_author ) {
			return $allcaps;
		}
		if ( $post->post_type !== strtolower( WPBallObjectsRepository::GAME_POST_TYPE ) ) {
			return $allcaps;
		}
		$player1_id = GameHelper::get_player1_ID( $post_id );
		$player2_id = GameHelper::get_player2_ID( $post_id );


		$player1_user_id = PlayerHelper::get_player_user_id( $player1_id );
		$player2_user_id = PlayerHelper::get_player_user_id( $player2_id );
		$match_id        = GameHelper::get_match_id( $post_id );
		$match_date_week = MatchHelper::get_week( $match_id );
		$date_week       = date( "W" );
		if ( $user_id === $player2_user_id || $user_id === $player1_user_id ) {
			if ( (int) $match_date_week === (int) $date_week ) {

				$allcaps[ $caps[0] ] = true;
			}
		}

		return $allcaps;
	}

	public static function prevent_post_delete( $allcaps, $caps, $args ) {
		if ( isset( $args[0], $args[2] ) && $args[0] === 'delete_post' ) {

			$allcaps[ $caps[0] ] = false;
		}

		return $allcaps;
	}

	public static function RegisterAdminNotice(): void {

		add_action( 'admin_notices', 'BallAdminNoticeHandler::BallAdminNotice' );
	}

	/**
	 * @return WP_Error|WP_Post_Type
	 */
	public static function RegisterMachine() {
		$args           = self::get_default_args();
		$args['labels'] = array(
			'name'          => __( 'Machines' ),
			'singular_name' => __( 'Machine' )
		);


		return register_post_type( WPBallObjectsRepository::MACHINE_POST_TYPE, $args );
	}

	/**
	 * @return WP_Error|WP_Post_Type
	 */
	public static function RegisterPLAYER() {
		$args                = self::get_default_args();
		$args['description'] = "The Player Data";
		$args['labels']      = array(
			'name'          => __( 'Players' ),
			'singular_name' => __( 'Player' )
		);

		//add_shortcode( 'playerscore', 'BallShortCodeHandler' );
		add_shortcode( 'playerscore', 'BallShortCodeHandler::PlayerTotalScore' );
		add_shortcode( 'playerwins', 'BallShortCodeHandler::PlayerTotalLoses' );
		add_shortcode( 'playerlosses', 'BallShortCodeHandler::PlayerTotalWins' );

		add_shortcode( 'season_table', 'BallShortCodeHandler::season_table' );
		add_shortcode( 'match_table', 'BallShortCodeHandler::match_table' );

		add_shortcode( 'player_stats', 'BallShortCodeHandler::player_stats' );
		add_shortcode( 'season_leader_board', 'BallShortCodeHandler::season_leader_board' );
		add_shortcode( 'leader_board', 'BallShortCodeHandler::leader_board' );
//		add_shortcode( 'playermatch', 'BallShortCodeHandler' );
//		add_shortcode( 'playerloses', 'BallShortCodeHandler' );
		$player_type = strtolower( WPBallObjectsRepository::PLAYER_POST_TYPE );
		add_action( "save_post_{$player_type}", "BallPostSaveHandler::SavePlayer" );

		return register_post_type( WPBallObjectsRepository::PLAYER_POST_TYPE, $args );
	}

	/**
	 * @return WP_Error|WP_Post_Type
	 */
	public static function RegisterSCORE() {
		$args                = self::get_default_args();
		$args['description'] = "Score Data for matches";
		$args['labels']      = array(
			'name'          => __( 'Scores' ),
			'singular_name' => __( 'Score' )
		);


		return register_post_type( WPBallObjectsRepository::SCORE_POST_TYPE, $args );
	}

	/**
	 * @return WP_Error|WP_Post_Type
	 */
	public static function RegisterGames() {
		$args                = self::get_default_args();
		$args['description'] = "Game Data ";
		$args['labels']      = array(
			'name'          => __( 'Games' ),
			'singular_name' => __( 'Game' )
		);
		$game_type           = strtolower( WPBallObjectsRepository::GAME_POST_TYPE );
		add_action( "save_post_" . $game_type, "BallPostSaveHandler::SaveGame" );
		add_shortcode( 'game_table', 'BallShortCodeHandler::game_table' );
		add_filter( 'the_title', 'BallTitleHandler::game_title', 10, 2 );
//		$args['capabilities'] = array(
//			'edit_post'          => 'edit_post',
//			'read_post'          => 'read_post',
//			'delete_post'        => 'delete_post',
//			'edit_posts'         => 'edit_posts',
//			'edit_others_posts'  => 'edit_others_posts',
//			'publish_posts'      => 'publish_games',
//			'read_private_posts' => 'read_private_books',
//			'create_posts'       => 'create_posts',
//		);
		return register_post_type( WPBallObjectsRepository::GAME_POST_TYPE, $args );
	}

	/**
	 * @return WP_Error|WP_Post_Type
	 */
	public static function RegisterMATCH() {
		$args                = self::get_default_args();
		$args['description'] = "Match Data ";
		$args['labels']      = array(
			'name'          => __( 'Matches' ),
			'singular_name' => __( 'Match' )
		);
		add_action( "save_post_" . WPBallObjectsRepository::MATCH_POST_TYPE, "BallPostSaveHandler::SaveMatch" );
		add_filter( 'the_excerpt', 'BallExcerptHandler::MatchHandler' );


		return register_post_type( WPBallObjectsRepository::MATCH_POST_TYPE, $args );
	}

	/**
	 * @return WP_Error|WP_Post_Type
	 */
	public static function RegisterSEASON() {
		$args                = self::get_default_args();
		$args['description'] = "Season Data ";
		$args['labels']      = array(
			'name'          => __( 'Seasons' ),
			'singular_name' => __( 'Season' )
		);
		$SEASON_POST_TYPE    = strtolower( WPBallObjectsRepository::SEASON_POST_TYPE );
		add_action( "save_post_{$SEASON_POST_TYPE}", "BallPostSaveHandler::SaveSeason" );

		add_action( "save_post_{$SEASON_POST_TYPE}", "BallPostSaveHandler::RebuildSchedule" );

		return register_post_type( WPBallObjectsRepository::SEASON_POST_TYPE, $args );

	}

	/**
	 * @return WP_Error|WP_Post_Type
	 */
	public static function RegisterStats() {
		$args                = self::get_default_args();
		$args['description'] = "Player Stat Data ";
		$args['labels']      = array(
			'name'          => __( 'Player Stats' ),
			'singular_name' => __( 'Player Stat' )
		);
		add_action( "save_post_" . WPBallObjectsRepository::STATISTIC_POST_TYPE, "BallPostSaveHandler" );

		return register_post_type( WPBallObjectsRepository::STATISTIC_POST_TYPE, $args );
	}

	/**
	 * @return array
	 */
	public static function get_default_args(): array {
		$args = array(


			'supports' => array(
				'custom-fields',
				'title',
				'editor',
				'page-attributes',
				'author',
				'thumbnail',
				'excerpt'
			),

			'public' => true, // bool (default is FALSE)


			'publicly_queryable'  => true, // bool (defaults to 'public').

			/**
			 * Whether to exclude posts with this post type from front end search results.
			 */
			'exclude_from_search' => false, // bool (defaults to 'public')

			/**
			 * Whether individual post type items are available for selection in navigation menus.
			 */
			'show_in_nav_menus'   => true, // bool (defaults to 'public')

			/**
			 * Whether to generate a default UI for managing this post type in the admin. You'll have
			 * more control over what's shown in the admin with the other arguments.  To build your
			 * own UI, set this to FALSE.
			 */
			'show_ui'             => true, // bool (defaults to 'public')

			/**
			 * Whether to show post type in the admin menu. 'show_ui' must be true for this to work.
			 */
			'show_in_menu'        => true

		);

		return $args;
	}
}