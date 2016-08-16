<?php
/**
 * Title Capitalization For WordPress
 *
 * Title Capitalization For WordPress is a plugin that converts post titles and
 * post content heading elements (that is, `h1`, `h2`, ..., `h6`) into properly
 * capitalized headings.
 *
 * This is based on the work of John Gruber, David Gouch, and Kroc Camen that
 * has been adapted into a WordPress plugin.
 *
 * @package   Title_Capitalization
 * @author    Tom McFarlin <tom@tommcfarlin.com>
 * @license   GPL-2.0+
 * @link      https://tommcfarlin.com/title-capitalization-for-wordpress/
 * @copyright 2014 - 2016 Tom McFarlin
 *
 * @wordpress-plugin
 * Plugin Name:       Title Capitalization For WordPress
 * Plugin URI:        http://tommcfarlin.com/title-capitalization-for-wordpress/
 * Description:       Converts post and page titles and post content headings into proper capitalization.
 * Version:           1.3.0
 * Author:            Tom McFarlin
 * Author URI:        http://tommcfarlin.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * Text Domain:       title-capitalization-for-wordpress
 * Network:           false
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Load the Title Case Library (if it has not already been loaded).
if ( ! class_exists( 'TitleCase' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'includes/vendor/class-title-case.php' );
}

// Load the class responsible for defining actions and their callbacks.
require_once( plugin_dir_path( __FILE__ ) . 'admin/class-title-capitalization-loader.php' );

// Load the core plugin file responsible for processing titles and headings.
require_once( plugin_dir_path( __FILE__ ) . 'admin/class-title-capitalizer.php' );

// Opting to use `admin_init` here since it's administration-screen specified.
add_action( 'admin_init', 'tm_title_capitalization_start' );
/**
 * Start the machine.
 * https://www.youtube.com/watch?v=ysoMOefPyRs
 */
function tm_title_capitalization_start() {

	$plugin = new Title_Capitalization_Loader();
	$plugin->run( new Title_Capitalizer( new TitleCase() ) );

}
