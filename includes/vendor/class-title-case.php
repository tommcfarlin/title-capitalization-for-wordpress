<?php

/**
 * Class TitleCase
 *
 * original Title Case script © John Gruber <daringfireball.net>
 * javascript port © David Gouch <individed.com>
 * PHP port of the above by Kroc Camen <camendesign.com>
 * refactor and mods for contractions and markdown headers by Andy Fragen <andy@thefragens.com>
 */
class TitleCase {

	/**
	 * Convert string to title case.
	 *
	 * @param $title
	 *
	 * @return mixed|string
	 */
	public function toTitleCase( $title ) {
		//remove HTML, storing it for later
		//       HTML elements to ignore    | tags  | entities
		$regx = '/<(code|var)[^>]*>.*?<\/\1>|<[^>]+>|&\S+;|(#+ )/';
		preg_match_all( $regx, $title, $html, PREG_OFFSET_CAPTURE );
		$title = preg_replace( $regx, '', $title );

		//find each word (including punctuation attached)
		preg_match_all( '/[\w\p{L}&`\'‘’"“\.@:\/\{\(\[<>_]+-? */u', $title, $m1, PREG_OFFSET_CAPTURE );

		foreach ( $m1[0] as &$m2 ) {

			//shorthand these- "match" and "index"
			list ( $m, $i ) = $m2;

			//correct offsets for multi-byte characters (`PREG_OFFSET_CAPTURE` returns *byte*-offset)
			//we fix this by recounting the text before the offset using multi-byte aware `strlen`
			$i = mb_strlen( substr( $title, 0, $i ), 'UTF-8' );

			//try to internationalize a bit.
			$locale       = get_locale();
			$short_locale = preg_replace( '/_.*$/', '', $locale );

			/**
			 * don't know if this is useful -- yet
			 *
			 * @link http://stackoverflow.com/questions/3191664/list-of-all-locales-and-their-short-codes
			 */
			$locales = json_decode( file_get_contents( __DIR__ . '/locales.json' ) );

			switch ( $short_locale ) {
				case 'en': // English
					$m = $this->english( $m, $i, $title );
					break;
				default:
					$m = $this->english( $m, $i, $title );
			}

			//re-splice the title with the change (`substr_replace` is not multi-byte aware)
			$title = mb_substr( $title, 0, $i, 'UTF-8' ) . $m .
			         mb_substr( $title, $i + mb_strlen( $m, 'UTF-8' ), mb_strlen( $title, 'UTF-8' ), 'UTF-8' );
		}

		//restore the HTML
		foreach ( $html[0] as &$tag ) {
			$title = substr_replace( $title, $tag[0], $tag[1], 0 );
		}

		return $title;
	}

	/**
	 * Capitalize based upon English rules.
	 *
	 * @access private
	 *
	 * @param $m string
	 * @param $i
	 * @param $title
	 *
	 * @return string
	 */
	private function english( $m, $i, $title ) {
		//find words that should always be lowercase…
		//(never on the first word, and never if preceded by a colon)
		if (
			$i > 0 && mb_substr( $title, max( 0, $i - 2 ), 1, 'UTF-8' ) !== ':' &&
			! preg_match( '/[\x{2014}\x{2013}] ?/u', mb_substr( $title, max( 0, $i - 2 ), 2, 'UTF-8' ) ) &&
			preg_match( '/^(a(nd?|s|t)?|b(ut|y)|en|for|i[fn]|o[fnr]|t(he|o)|vs?\.?|via)[ \-]/i', $m ) ||

			// Don't capitalize contractions
			preg_match( '/\'(s|t|ll|re|ve|d|m|em)/i', $m )
		) {
			//…and convert them to lowercase
			$m = mb_strtolower( $m, 'UTF-8' );

		} elseif (
			//else:	brackets and other wrappers
		preg_match( '/[\'"_{(\[‘“]/u', mb_substr( $title, max( 0, $i - 1 ), 3, 'UTF-8' ) )
		) {
			//convert first letter within wrapper to uppercase
			$m = mb_substr( $m, 0, 1, 'UTF-8' );

			//if single letter word, capitalize it
			if ( 1 === mb_strlen( $m, 'UTF-8' ) ) {
				$m = mb_strtoupper( $m, 'UTF-8' );
			} else {
				$m .= mb_strtoupper( mb_substr( $m, 1, 1, 'UTF-8' ), 'UTF-8' ) .
				      mb_substr( $m, 2, mb_strlen( $m, 'UTF-8' ) - 2, 'UTF-8' );
			}
		} elseif (
			//else:	do not uppercase these cases
			preg_match( '/[\])}]/', mb_substr( $title, max( 0, $i - 1 ), 3, 'UTF-8' ) ) ||
			preg_match( '/[A-Z]+|&|\w+[._]\w+/u', mb_substr( $m, 1, mb_strlen( $m, 'UTF-8' ) - 1, 'UTF-8' ) )
		) {
			$m = $m;
		} else {
			//if all else fails, then no more fringe-cases; uppercase the word
			$m = mb_strtoupper( mb_substr( $m, 0, 1, 'UTF-8' ), 'UTF-8' ) .
			     mb_substr( $m, 1, mb_strlen( $m, 'UTF-8' ), 'UTF-8' );
		}

		return $m;
	}

}
