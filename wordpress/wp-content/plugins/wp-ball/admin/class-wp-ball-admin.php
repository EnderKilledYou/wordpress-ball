<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://goodcode.shop
 * @since      1.0.0
 *
 * @package    Wp_Ball
 * @subpackage Wp_Ball/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Ball
 * @subpackage Wp_Ball/admin
 * @author     Sterling Kooshesh <sterling@goodcode.shop>
 */

function test_init() {
	include_once ("partials/wp-ball-admin-display.php");
}

class Wp_Ball_Admin {
	public function test_plugin_setup_menu() {
		add_menu_page( 'Pinball Test Data Helper', 'Pinball Test Data Helper', 'manage_options', 'test-plugin', 'test_init' );
	}

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Ball_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Ball_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-ball-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Ball_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Ball_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-ball-admin.js', array( 'jquery' ), $this->version, false );

	}

}
