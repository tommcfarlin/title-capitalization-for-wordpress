<?php
/**
 * Attach functions for capitalization to the save_post action.
 *
 * @since   1.0.0
 *
 * @package TitleCaps/admin
 */


/**
 * Attach functions for capitalization to the save_post action.
 *
 * Define two callback functions - one for capitalization the post title, one for
 * capitalizing heading elements in the post content - and invoke them whenever
 * the save_post action is triggered.
 *
 * @package   TitleCaps/admin
 * @author    Tom McFarlin <tom@tommcfarlin.com>
 * @license   GPL-2.0+
 * @link      http://tommcfarlin.com/title-capitalization-for-wordpress/
 * @copyright 2014 Tom McFarlin
 */
class TitleCapsLoader {

	/**
	 * Attach callback functions to the save_post hook that will capitalize the
	 * post title and all of the headings in the post content.
	 *
	 * @since    1.0.0
	 *
	 * @param    TitleCaps    $title_capitalizer    A reference to the class that provides proper capitalization.
	 */
	public function init( $title_capitalizer ) {

		add_action( 'save_post', array( $title_capitalizer, 'title_caps_post_title' ) );
		add_action( 'save_post', array( $title_capitalizer, 'title_caps_post_content' ) );

	}

}