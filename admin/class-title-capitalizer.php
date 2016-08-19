<?php
/**
 * Properly capitalizes titles and headers
 *
 * @package   Title_Capitalization
 * @author    Tom McFarlin <tom@tommcfarlin.com>
 * @license   GPL-2.0+
 * @link      http://tommcfarlin.com/title-capitalization-for-wordpress/
 * @copyright 2014 - 2016 Tom McFarlin
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
 * @package   Title_Capitalization
 * @author    Tom McFarlin <tom@tommcfarlin.com>
 */
class Title_Capitalizer {

	/**
	 * Reference to the library responsible for capitalizing its input.
	 *
	 * @var TitleCase
	 */
	protected $title_case;

	/**
	 * Define a reference to the third-party library for processing the post title
	 * and post content.
	 *
	 * @param  TitleCase $title_case The third-party library that correctly
	 *                               capitalizes a given string.
	 */
	public function __construct( TitleCase $title_case ) {
		$this->title_case = $title_case;
	}

	/**
	 * Capitalizes the post title for the post with the specified post ID.
	 *
	 * Checks to see if this post should be saved. If so, uses the incoming
	 * post ID to retrieve the post title for capitalization, then updates
	 * the post.
	 *
	 * @param  integer $post_id The ID of the post being processed and saved.
	 */
	public function capitalize_post_title( $post_id ) {

		if ( ! $this->should_save_post( $post_id ) ) {
			return;
		}

		$post = array(
			'ID'         => $post_id,
			'post_title' => $this->title_case->toTitleCase( get_the_title( $post_id ) ),
		);

		$this->update_post( $post, 'capitalize_post_title' );

	}

	/**
	 * Capitalize the headings in the post content for the post with the specified
	 * post ID.
	 *
	 * Check to see if this post should be saved. If so, load the post object and
	 * applies the the_content filter to the post content.
	 *
	 * Loop over the content six times (looking
	 * for heading elements 1 through 6) and locate heading elements using a
	 * regular expression.
	 *
	 * For each of the matches the expression found, replace the content with the
	 * properly capitalized value.
	 *
	 * Once all processing is done, the post is updated and saved.
	 *
	 * @param   array       $data     The sanitized post data
	 * @param   array       $arr_post The raw post data
	 *
	 * @return  bool|array  $data The sanitized post data with properly capitalized elements
	 */
	public function capitalize_post_content( $data, $arr_post ) {

		if ( isset( $arr_post['post_ID'] ) && ! $this->should_save_post( $arr_post['post_ID'] ) ) {
			return false;
		}

		$content          = $data['post_content'];
		$content_filtered = $this->get_content_filtered( $data, $arr_post );
		$header_matches   = array();

		$html_headers     = $this->find_html_headings( $content );
		$markdown_headers = $this->find_markdown_headings( $content, $content_filtered );
		$header_matches   = array_merge( $html_headers, $markdown_headers );

		foreach ( $header_matches as $matched ) {

			$content = str_ireplace(
				$matched,
				$this->title_case->toTitleCase( $matched ),
				$content
			);

			$content_filtered = str_ireplace(
				$matched,
				$this->title_case->toTitleCase( $matched ),
				$content_filtered
			);

		}

		$data['post_content']          = $content;
		$data['post_content_filtered'] = $content_filtered;

		return $data;
	}

	/**
	 * [get_content_filtered description]
	 * @param  [type] $data     [description]
	 * @param  [type] $arr_post [description]
	 * @return [type]           [description]
	 */
	protected function get_content_filtered( $data, $arr_post ) {

		$content_filtered = null;

		// Start by getting from $data.
		if ( ! empty( $data['post_content_filtered'] ) ) {
			$content_filtered = $data['post_content_filtered'];
		}

		// When $data and $arr_post disagree use $arr_post.
		if ( ! empty( $arr_post['post_content_filtered'] ) &&
		     $arr_post['post_content_filtered'] != $data['post_content_filtered'] ||
		     isset( $_POST['content'] ) && ( $_POST['content'] === $arr_post['post_content_filtered'] )
		) {
			$content_filtered = $arr_post['post_content_filtered'];
		}

		// Use $data when $_POST and $data are same, like when updating.
		if ( isset( $_POST['content'] ) && ( $_POST['content'] === $data['post_content_filtered'] ) ) {
			$content_filtered = $data['post_content_filtered'];
		}

		return $content_filtered;

	}

	/**
	 * [find_html_headings description]
	 * @param  [type] $content [description]
	 * @return [type]          [description]
	 */
	protected function find_html_headings( $content ) {

		$regex = "#<h([1-6])(.*)>(.*)</h\\1>#i";
		preg_match_all( $regex, $content, $matches );

		return $matches[0];

	}

	/**
	 * [find_markdown_headings description]
	 * @param  [type] $content          [description]
	 * @param  [type] $content_filtered [description]
	 * @return [type]                   [description]
	 */
	protected function find_markdown_headings( $content, $content_filtered ) {

		$regex = "/(?<!\\S)(?:^)#(.*)(\r|\n)?/m";
		preg_match_all( $regex, $content, $matches );
		if ( empty( $matches[0] ) ) {
			preg_match_all( $regex, $content_filtered, $matches );
		}

		return $matches[0];

	}

	/**
	 * Determine whether or not the post should be saved.
	 *
	 * Check to see if the action being called is a post revision or post autosave. If so,
	 * then return false. Only permit a save if the user has initiated the save action.
	 *
	 * @param   integer $post_id The ID of the post being processed
	 *
	 * @return  bool               True if the post should be saved; false, if this is a revision or autosave
	 */
	protected function should_save_post( $post_id ) {

		$post     = get_post( $post_id );
		$add_post = ( 'auto-draft' === $post->post_status ) ? true : false;

		// 'closedpostboxesnonce' was the only nonce I could consistently find.
		if ( ( isset( $_POST['closedpostboxesnonce'] ) &&
		       ! wp_verify_nonce( $_POST['closedpostboxesnonce'], 'closedpostboxes' ) ) ||
		     ( isset( $_POST['_nonce'] ) && ! wp_verify_nonce( $_POST['_nonce'], 'heartbeat-nonce' ) )
		) {
			return false;
		}

		return ! ( wp_is_post_revision( $post_id ) &&
		           wp_is_post_autosave( $post_id ) ) &&
		       ! ( $add_post || $this->is_heartbeat() );

	}

	/**
	 * Checks to see if a heartbeat is resulting in activity.
	 *
	 * @return bool
	 */
	protected function is_heartbeat() {
		return ( isset( $_POST['action'] ) && 'heartbeat' === $_POST['action'] );
	}

	/**
	 * Update the incoming post object.
	 *
	 * Temporarily unhook the save_post action for the specified action,
	 * update the post, then reinstate the save_post action for the
	 * specified action.
	 *
	 * @param  array  $arr_post  The array of the is post data to be updated.
	 * @param  string $func_name The name of the function calling this function.
	 */
	protected function update_post( $arr_post, $func_name ) {

		remove_action( 'save_post', array( $this, $func_name ) );
		wp_update_post( $arr_post );
		add_action( 'save_post', array( $this, $func_name ) );

	}
}
