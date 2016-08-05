<?php
/**
 * Attach functions for capitalization to the save_post action.
 *
 * @package   TitleCapitalizer
 * @author    Tom McFarlin <tom@tommcfarlin.com>
 * @license   GPL-2.0+
 * @link      http://tommcfarlin.com/title-capitalization-for-wordpress/
 * @copyright 2014 Tom McFarlin
 */


/**
 * Attach functions for capitalization to the save_post action.
 *
 * Define two callback functions - one for capitalization the post title, one for
 * capitalizing heading elements in the post content - and invoke them whenever
 * the save_post action is triggered.
 *
 * @package   TitleCapitalizer
 * @author    Tom McFarlin <tom@tommcfarlin.com>
 */
class Title_Capitalizer_Loader {

	/**
	 * Attach callback functions to the save_post hook that will capitalize the
	 * post title and all of the headings in the post content.
	 *
	 * @param  Title_Capitalizer $title_capitalizer A reference to the class that provides proper capitalization.
	 */
	public function run( Title_Capitalizer $title_capitalizer ) {

		// Fixes issue when new post
		if ( isset( $_GET['post'] ) ) {
			$new_post = get_post( $_GET['post'] );
		} elseif ( isset( $_POST['post_ID'] ) ) {
			$new_post = get_post( $_POST['post_ID'] );
		}
		$new_post = empty( $new_post ) ? true : false;


		add_action( 'save_post', array( $title_capitalizer, 'capitalize_post_title' ) );
		if ( ! $new_post ) {
			add_filter( 'wp_insert_post_data', array( $title_capitalizer, 'capitalize_post_content' ), 99, 2 );
		}

	}

}
