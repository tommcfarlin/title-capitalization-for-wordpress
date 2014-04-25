<?php
/**
 * Properly capitalizes titles and headers
 *
 * @link    http://tommcfarlin.com/title-capitalization-for-wordpress/
 * @since   1.0.0
 *
 * @package TitleCaps/admin
 */

/**
 * Properly capitalizes titles and headers.
 *
 * The Title Capitalizer is the core class of the plugin. It's responsible for
 * handing post titles and content over to the TitleCase library for processing.
 *
 * The class uses two functions - one for the post title, one for the post content -
 * to capitalize the titles, but will not fire during a revision or a
 * WordPress autosave.
 *
 * It also manages the save_post hook by temporarily unhooking the action, processing
 * the title and the content, updating the post, then rehooking the action to resume
 * the rest of the execution.
 *
 * @package   TitleCaps/admin
 * @author    Tom McFarlin <tom@tommcfarlin.com>
 * @license   GPL-2.0+
 * @link      http://tommcfarlin.com/title-capitalization-for-wordpress/
 * @copyright 2014 Tom McFarlin
 */
class TitleCapitalizer {

	/**
	 * Reference to the library responsible for capitalizing its input.
	 *
	 * @var    TitleCase
	 * @access protected
	 * @since  1.0.0
	 */
	protected $title_case;

	/**
	 * Define a reference to the third-party library for processing the post title and post content.
	 *
	 * @param    TitleCase    $title_case    The third-party library that correctly capitalizes a given string.
	 * @since    1.0.0
	 */
	public function __construct( $title_case ) {
		$this->title_case = $title_case;
	}

	/**
	 * Capitalizes the post title for the post with the specified post ID.
	 *
	 * Checks to see if this post should be saved. If so, uses the incoming
	 * post ID to retrieve the post title for capitalization, then updates
	 * the post.
	 *
	 * @since 1.0.0
	 *
	 * @param    integer    $post_id    The ID of the post being processed and saved.
	 */
	public function title_caps_post_title( $post_id ) {

		if ( ! $this->should_save( $post_id ) ) {
			return;
		}

		$post = array(
			'ID'         => $post_id,
			'post_title' => $this->title_case->toTitleCase( get_the_title( $post_id ) )
		);

		$this->update_post( $post, 'title_caps_post_title' );

	}

	/**
	 * Capitalize the headings in the post content for the post with the specified post ID.
	 *
	 * Check to see if this post should be saved. If so, load the post object and applies
	 * the the_content filter to the post content.
	 *
	 * Loop over the content six times (looking
	 * for heading elements 1 through 6) and locate heading elements using a regular expression.
	 *
	 * For each of the matches the expression found, replace the content with the properly
	 * capitalized value.
	 *
	 * Once all processing is done, the post is updated and saved.
	 *
	 * @since 1.0.0
	 *
	 * @param    array      $data       The sanitized post data
	 * @param    array      $arr_post	The raw post data
	 * @return   array      $data       The sanitized post data with properly capitalized elements
	 */
	public function capitalize_post_content( $data, $arr_post ) {

		if ( ! $this->should_save( $arr_post['post_ID'] ) ) {
			return;
		}

		$content = $data['post_content'];
		for ( $i = 1; $i <= 6; $i++ ) {

			$regex = "#(<h$i>)(.*)(</h$i>)#i";
			preg_match_all( $regex, $content, $matches );
			$matches = $matches[0];

			for ( $j = 0; $j < count( $matches ); $j++ ) {
				$content = str_ireplace( $matches[ $j ], $this->title_case->toTitleCase( $matches[ $j ] ), $content );
			}

		}

		$data['post_content'] = $content;

		return $data;

	}

	/**
	 * Determine whether or not the post should be saved.
	 *
	 * Check to see if the action being called is a post revision or post autosave,
	 * then return true if the save action was triggered by the user.
	 *
	 * @since    1.0.0
	 * @access   private
	 *
	 * @param    integer    $post_id    The ID of the post being processed
 	 * @return   bool                   True if the post should be saved; false, if this is a revision or autosave.
	 */
	private function should_save( $post_id ) {
		return ! ( wp_is_post_revision( $post_id ) && wp_is_post_autosave( $post_id ) );
	}

	/**
	 * Update the incoming post object.
	 *
	 * Temporarily unhook the save_post action for the specified action,
	 * update the post, then reinstate the save_post action for the
	 * specified action.
	 *
	 * @since    1.0.0
	 * @access   private
	 *
	 * @param    WP_Post    $post         The instance of WP_Post the is meant to be updated
 	 * @param    string     $func_name    The name of the function calling this function.
	 */
	private function update_post( $post, $func_name ) {

		remove_action( 'save_post', array( $this, $func_name ) );
		wp_update_post( $post );
		add_action( 'save_post', array( $this, $func_name ) );

	}

}