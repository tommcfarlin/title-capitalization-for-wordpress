<?php
/**
 * Title Capitalization For WordPress
 *
 * Title Capitalization For WordPress is a plugin that converts post titles and post content
 * heading elements (that is, `h1`, `h2`, ..., `h6`) into properly capitalized headings.
 *
 * This is based on the work of John Gruber, David Gouch, and Kroc Camen that has been adapted
 * into a WordPress plugin.
 *
 * @package   TitleCapitalizer
 * @author    Tom McFarlin <tom@tommcfarlin.com>
 * @license   GPL-2.0+
 * @link      http://tommcfarlin.com/title-capitalization-for-wordpress/
 * @copyright 2014 Tom McFarlin
 *
 * @wordpress-plugin
 * Plugin Name:       Title Capitalization For WordPress
 * Plugin URI:        http://tommcfarlin.com/title-capitalization-for-wordpress/
 * Description:       Converts post and page titles and post content headings into proper capitalization.
 * Version:           1.2.0
 * Author:            Tom McFarlin
 * Author URI:        http://tommcfarlin.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * Text Domain:       title-capitalization-for-wordpress
 * Network:           false
 * GitHub Plugin URI: tommcfarlin/title-capitalization-for-wordpress
 * GitHub Branch:     master
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Only run this plugin if we're in the dashboard.
if ( is_admin() ) {

	/**
	 * Load the Title Case Library (if it has not already been loaded)
	 */
	if ( ! class_exists( 'TitleCase' ) ) {
		require_once( plugin_dir_path( __FILE__ ) . 'includes/vendor/class-title-case.php' );
	}

	/**
	 * Load the class responsible for defining actions and their callbacks.
	 */
	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-title-capitalizer-loader.php' );

	/**
	 * Load the core plugin file responsible for processing titles and headings.
	 */
	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-title-capitalizer.php' );

	$title_caps_loader = new Title_Capitalizer_Loader();
	$title_caps_loader->run( new Title_Capitalizer( new TitleCase() ) );

}
