<?php
/**
 * Attach functions for capitalization to the save_post action.
 *
 * @package   Title_Capitalization
 * @author    Tom McFarlin <tom@tommcfarlin.com>
 * @license   GPL-2.0+
 * @link      https://tommcfarlin.com/title-capitalization-for-wordpress/
 * @copyright 2014 - 2016 Tom McFarlin
 */


/**
 * Attach functions for capitalization to the save_post action.
 *
 * Define two callback functions - one for capitalization the post title, one
 * for capitalizing heading elements in the post content - and invoke them
 * whenever the save_post action is triggered.
 *
 * @package   Title_Capitalization
 * @author    Tom McFarlin <tom@tommcfarlin.com>
 */
class Title_Capitalization_Loader {

	/**
	 * Attach callback functions to the save_post hook that will capitalize the
	 * post title and all of the headings in the post content.
	 *
	 * @param  Title_Capitalizer $title_capitalizer A reference to the class that
	 *                                              provides proper capitalization.
	 */
	public function run( Title_Capitalizer $title_capitalizer ) {

		// If it's not a new post, then handle the updating of a post.
		if ( ! $this->is_new_post() ) {

			add_filter(
				'wp_insert_post_data',
				array( $title_capitalizer, 'capitalize_post_content' ),
				99, 2
			);

		}

		// Otherwise, we're dealing with a brand new post.
		add_action(
			'save_post',
			array( $title_capitalizer, 'capitalize_post_title' )
		);

	}

	/**
	 * Determines if the current post is a new post.
	 *
	 * @return bool    True if the post being saved is new; otherwise, false.
	 */
	protected function is_new_post() {

		$is_new_post = false;

		// TODO Introduce nonce verification.

		/* Retrieve the post ID from the post information in the GET collection.
		 * Returns null if the post is invalid.
		 */
		$is_new_post = isset( $_GET['post'] ) ? // Input var okay.
			get_post(
				sanitize_text_field(
					wp_unslash( $_GET['post'] ) // Input var okay.
				)
			) : false ;

		/* If the post ID in the POST collection is st, then retrieve the post
		 * information; otherwise, sue the value previously determined.
		 */
		$is_new_post = isset( $_POST['post_ID'] ) ? // Input var okay.
			get_post(
				sanitize_text_field(
					wp_unslash(
						$_POST['post_ID']
					)
				)
			) :
			$is_new_post;

		return empty( $is_new_post );

	}
}
