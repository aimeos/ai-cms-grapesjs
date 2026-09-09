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
		$html5 = new \Masterminds\HTML5( ['disable_html_ns' => true] );
		$doc = $html5->loadHTML( '<!DOCTYPE html><html><body>' . $content . '</body></html>' );
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

		$result = '';
		foreach( $doc->getElementsByTagName( 'body' )->item( 0 )->childNodes ?? [] as $node ) {
			$result .= $html5->saveHTML( $node );
		}

		// Keep the HTML sanitizer authoritative after parsing/serializing again.
		return \Aimeos\Sanitizer\Sane::html( $result, $allow );
	}


	/**
	 * Only passive metadata used by bundled layouts may become component props.
	 * Keep this policy in sync with ai-cms-grapesjs Aimeos.CMSContent.sanitizeAttributes().
	 */
	private static function allowed( string $name, string $value ) : bool
	{
		return match( $name ) {
			'data-gjs-name' => preg_match( '/\A[\p{L}\p{N} ._-]{1,128}\z/u', $value ) === 1,
			'data-gjs-draggable', 'data-gjs-droppable' => strlen( $value ) <= 256
				&& preg_match( '/\A[.#]?[a-zA-Z0-9_-]+(?: *, *[.#]?[a-zA-Z0-9_-]+)*\z/', $value ) === 1,
			default => false,
		};
	}
}
