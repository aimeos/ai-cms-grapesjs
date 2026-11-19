<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */

namespace Aimeos\MShop\Cms;


/**
 * Sanitizes HTML and restricts metadata interpreted by the CMS editor and storefront.
 */
class Html
{
	/** @var list<string> */
	private const ACTIVE_ELEMENTS = ['base', 'link', 'meta', 'script', 'style'];

	/** @var \Masterminds\HTML5|null */
	private static $html5;


	/**
	 * Normalizes configured CMS HTML elements and URI prefixes.
	 *
	 * @param \Aimeos\Base\Config\Iface $config Configuration object
	 * @return array<string, bool|list<string>> Allowed elements and URI prefixes
	 */
	public static function getAllow( \Aimeos\Base\Config\Iface $config ) : array
	{
		$result = [];

		foreach( (array) $config->get( 'admin/cms/allow', [] ) as $key => $value )
		{
			if( !is_string( $key ) || ( !is_bool( $value ) && !is_array( $value ) ) ) {
				continue;
			}

			$result[$key] = is_array( $value )
				? array_values( array_filter( $value, 'is_string' ) )
				: $value;
		}

		return $result;
	}


	/**
	 * @param string $content HTML content
	 * @param array<string, bool|list<string>> $allow Requested elements and URI prefixes;
	 *  active document elements are ignored
	 * @return string Sanitized HTML
	 */
	public static function sanitize( string $content, array $allow = [] ) : string
	{
		// These elements can execute code, load CSS or alter document-wide URL and
		// navigation behavior. Stored content must never opt them back in.
		foreach( self::ACTIVE_ELEMENTS as $element ) {
			unset( $allow[$element] );
		}

		$content = \Aimeos\Sanitizer\Sane::html( $content, $allow );

		if( stripos( $content, 'data-' ) === false ) {
			return $content;
		}

		// Use the HTML5 parser supplied by sanitizer 0.4, after its input limits
		// and HTML policy have run. Do not filter HTML attributes with a regex.
		$doc = self::dom( $content );
		$changed = false;

		foreach( $doc->getElementsByTagName( '*' ) as $node )
		{
			foreach( iterator_to_array( $node->attributes ) as $attr )
			{
				$name = strtolower( $attr->name );

				// CMS-authored HTML must not configure storefront widgets or active editor metadata.
				if( in_array( $name, ['data-counturl', 'data-infiniteurl', 'data-url', 'data-rmurl', 'data-options'], true )
					|| str_starts_with( $name, 'data-gjs-' ) && !self::allowed( $name, $attr->value ) )
				{
					$node->removeAttributeNode( $attr );
					$changed = true;
				}
			}
		}

		if( !$changed ) {
			return $content;
		}

		// Keep the HTML sanitizer authoritative after parsing/serializing again.
		return \Aimeos\Sanitizer\Sane::html( self::html( $doc ), $allow );
	}


	/**
	 * Replaces the elements with the given tag name by the HTML returned by the closure
	 *
	 * Elements inside forms are removed because browsers ignore nested forms and would
	 * add the fields of the new HTML, e.g. CSRF tokens, to the outer form instead.
	 *
	 * @param string $content HTML content
	 * @param string $tag Name of the elements which should be replaced
	 * @param \Closure $fcn Closure receiving the DOM element and returning the new HTML or NULL to keep the element
	 * @return string HTML content with replaced elements
	 */
	public static function replace( string $content, string $tag, \Closure $fcn ) : string
	{
		if( stripos( $content, '<' . $tag ) === false ) {
			return $content;
		}

		$doc = self::dom( $content );

		foreach( iterator_to_array( $doc->getElementsByTagName( $tag ) ) as $node )
		{
			if( ( $parent = $node->parentNode ) === null ) {
				continue;
			}

			if( self::skip( $node ) ) {
				$parent->removeChild( $node );
			} elseif( is_string( $html = $fcn( $node ) ) ) {
				$parent->replaceChild( self::html5()->loadHTMLFragment( $html, ['target_document' => $doc] ), $node );
			}
		}

		return self::html( $doc );
	}


	/**
	 * Parses the HTML content into a DOM document
	 *
	 * @param string $content HTML content
	 * @return \DOMDocument DOM document with the content as children of the body element
	 */
	private static function dom( string $content ) : \DOMDocument
	{
		return self::html5()->loadHTML( '<!DOCTYPE html><html><body>' . $content . '</body></html>' );
	}


	/**
	 * Serializes the children of the body element of the DOM document
	 *
	 * @param \DOMDocument $doc DOM document created by dom()
	 * @return string HTML content
	 */
	private static function html( \DOMDocument $doc ) : string
	{
		$result = '';

		foreach( $doc->getElementsByTagName( 'body' )->item( 0 )->childNodes ?? [] as $node ) {
			$result .= self::html5()->saveHTML( $node );
		}

		return $result;
	}


	/**
	 * Returns the HTML5 parser and serializer
	 *
	 * @return \Masterminds\HTML5 HTML5 object
	 */
	private static function html5() : \Masterminds\HTML5
	{
		if( self::$html5 === null ) {
			self::$html5 = new \Masterminds\HTML5( ['disable_html_ns' => true] );
		}

		return self::$html5;
	}


	/**
	 * Tests if the node is inside a form or has been removed together with a replaced element
	 *
	 * @param \DOMNode $node DOM node
	 * @return bool TRUE if the node must not be replaced, FALSE if not
	 */
	private static function skip( \DOMNode $node ) : bool
	{
		while( $node = $node->parentNode )
		{
			if( $node instanceof \DOMDocument ) {
				return false;
			}

			if( strtolower( $node->nodeName ) === 'form' ) {
				return true;
			}
		}

		return true;
	}


	/**
	 * Only passive metadata used by bundled layouts may become component props.
	 * Keep this policy in sync with ai-cms-grapesjs Aimeos.CMSContent.sanitizeAttributes().
	 */
	private static function allowed( string $name, string $value ) : bool
	{
		switch( $name )
		{
			case 'data-gjs-name':
				return preg_match( '/\A[\p{L}\p{N} ._-]{1,128}\z/u', $value ) === 1;
			case 'data-gjs-draggable':
			case 'data-gjs-droppable':
				return strlen( $value ) <= 256
					&& preg_match( '/\A[.#]?[a-zA-Z0-9_-]+(?: *, *[.#]?[a-zA-Z0-9_-]+)*\z/', $value ) === 1;
		}

		return false;
	}
}
