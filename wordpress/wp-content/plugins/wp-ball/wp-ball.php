<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://goodcode.shop
 * @since             1.0.0
 * @package           Wp_Ball
 *
 * @wordpress-plugin
 * Plugin Name:       Wordpress Ball
 * Plugin URI:        https://goodcode.shop
 * Description:       a
 * Version:           1.0.0
 * Author:            Sterling Kooshesh
 * Author URI:        https://goodcode.shop
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-ball
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 ------
 * and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_BALL_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-ball-activator.php
 */

include_once( "custom_types/PinballObject.php" );
include_once ("custom_types/BallPostSaveHandler.php");
include_once( "custom_types/BallPostFormHandler.php" );
include_once( "custom_types/BallRegister.php" );
include_once( "repos/ScoreHelper.php" );
include_once( "repos/MachineHelper.php" );
include_once( "repos/MatchHelper.php" );
include_once( "repos/PlayerHelper.php" );
include_once( "repos/GameHelper.php" );
include_once( "logic/ScheduleHelper.php" );
include_once( "custom_types/WPBallMachine.php" );
include_once( "custom_types/WPBallObjectsRepository.php" );
include_once( "custom_types/BallAdminNoticeHandler.php" );
include_once( "custom_types/BallShortCodeHandler.php" );


function activate_wp_ball() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-ball-activator.php';


}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-ball-deactivator.php
 */
function deactivate_wp_ball() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-ball-deactivator.php';
	Wp_Ball_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_ball' );
register_deactivation_hook( __FILE__, 'deactivate_wp_ball' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-ball.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_ball() {

	$plugin = new Wp_Ball();
	$plugin->run();

}


/**
 * Retrieves or displays a list of pages as a dropdown (select list).
 *
 * @since 2.1.0
 * @since 4.2.0 The `$value_field` argument was added.
 * @since 4.3.0 The `$class` argument was added.
 *
 * @see get_pages()
 *
 * @param array|string $args {
 *     Optional. Array or string of arguments to generate a page dropdown. See get_pages() for additional arguments.
 *
 *     @type int          $depth                 Maximum depth. Default 0.
 *     @type int          $child_of              Page ID to retrieve child pages of. Default 0.
 *     @type int|string   $selected              Value of the option that should be selected. Default 0.
 *     @type bool|int     $echo                  Whether to echo or return the generated markup. Accepts 0, 1,
 *                                               or their bool equivalents. Default 1.
 *     @type string       $name                  Value for the 'name' attribute of the select element.
 *                                               Default 'page_id'.
 *     @type string       $id                    Value for the 'id' attribute of the select element.
 *     @type string       $class                 Value for the 'class' attribute of the select element. Default: none.
 *                                               Defaults to the value of `$name`.
 *     @type string       $show_option_none      Text to display for showing no pages. Default empty (does not display).
 *     @type string       $show_option_no_change Text to display for "no change" option. Default empty (does not display).
 *     @type string       $option_none_value     Value to use when no page is selected. Default empty.
 *     @type string       $value_field           Post field used to populate the 'value' attribute of the option
 *                                               elements. Accepts any valid post field. Default 'ID'.
 * }
 * @return string HTML dropdown list of pages.
 */
function wp_dropdown_posts( $args = '',$fieldname ) {
	$defaults = array(
		'depth'                 => 0,
		'child_of'              => 0,
		'selected'              => 0,
		'echo'                  => 1,
		'name'                  => 'page_id',
		'id'                    => '',
		'class'                 => '',
		'show_option_none'      => '',
		'show_option_no_change' => '',
		'option_none_value'     => '',
		'value_field'           => 'ID',
	);

	$parsed_args = wp_parse_args( $args, $defaults );

	$pages  = get_posts( $args );
	$output = '';
	// Back-compat with old system where both id and name were based on $name argument.
	if ( empty( $parsed_args['id'] ) ) {
		$parsed_args['id'] = $parsed_args['name'];
	}

	if ( ! empty( $pages ) ) {
		$class = '';
		if ( ! empty( $parsed_args['class'] ) ) {
			$class = " class='" . esc_attr( $parsed_args['class'] ) . "'";
		}

		$output = "<select name='" . $fieldname. "'" . $class . " id='" . esc_attr( $parsed_args['id'] ) . "'>\n";
		if ( $parsed_args['show_option_no_change'] ) {
			$output .= "\t<option value=\"-1\">" . $parsed_args['show_option_no_change'] . "</option>\n";
		}
		if ( $parsed_args['show_option_none'] ) {
			$output .= "\t<option value=\"" . esc_attr( $parsed_args['option_none_value'] ) . '">' . $parsed_args['show_option_none'] . "</option>\n";
		}
		$output .= walk_page_dropdown_tree( $pages, $parsed_args['depth'], $parsed_args );
		$output .= "</select>\n";
	}

	/**
	 * Filters the HTML output of a list of pages as a dropdown.
	 *
	 * @since 2.1.0
	 * @since 4.4.0 `$parsed_args` and `$pages` added as arguments.
	 *
	 * @param string    $output      HTML output for dropdown list of pages.
	 * @param array     $parsed_args The parsed arguments array. See wp_dropdown_pages()
	 *                               for information on accepted arguments.
	 * @param WP_Post[] $pages       Array of the page objects.
	 */
	$html = apply_filters( 'wp_dropdown_pages', $output, $parsed_args, $pages );

	if ( $parsed_args['echo'] ) {
		echo $html;
	}

	return $html;
}
run_wp_ball();

