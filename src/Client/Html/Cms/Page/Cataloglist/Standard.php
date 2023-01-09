<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Cms\Page\Cataloglist;


/**
 * Default implementation for CMS cataloglist.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Catalog\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function body( string $uid = '' ) : string
	{
		return '';
	}


	/**
	 * Modifies the cached content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string Modified content
	 */
	public function modify( string $content, string $uid ) : string
	{
		return $this->replaceSection( $content, $this->view()->csrf()->formfield(), 'catalog.lists.items.csrf' );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param \Aimeos\Base\View\Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return \Aimeos\Base\View\Iface Modified view object
	 */
	public function data( \Aimeos\Base\View\Iface $view, array &$tags = [], string &$expire = null ) : \Aimeos\Base\View\Iface
	{
		if( !isset( $view->pageContent ) ) {
			return parent::data( $view, $tags, $expire );
		}

		$texts = [];
		$context = $this->context();
		$config = $context->config();
		$cntl = \Aimeos\Controller\Frontend::create( $context, 'product' );

		/** client/html/cms/page/template-cataloglist
		 * Relative path to the HTML template of the page catalog list client.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the HTML code that is inserted into the HTML page
		 * of the rendered page in the frontend. The configuration string is the
		 * path to the template file relative to the templates directory (usually
		 * in client/html/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "standard" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "standard"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating code for the catalog list
		 * @since 2021.07
		 * @category Developer
		 * @see client/html/cms/page/template-body
		 * @see client/html/cms/page/template-header
		 */
		$template = $config->get( 'client/html/cms/page/template-cataloglist', 'cms/page/cataloglist/list' );
		$domains = $config->get( 'client/html/catalog/lists/domains', ['media', 'media/property', 'price', 'text'] );

		if( $view->config( 'client/html/cms/page/basket-add', false ) ) {
			$domains = array_merge_recursive( $domains, ['product' => ['default'], 'attribute' => ['variant', 'custom', 'config']] );
		}

		libxml_use_internal_errors( true );

		foreach( $view->pageContent as $content )
		{
			$dom = new \DOMDocument( '1.0', 'UTF-8' );
			$dom->loadHTML( '<?xml encoding="utf-8" ?>' . $content, LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD );
			$nodes = $dom->getElementsByTagName( 'cataloglist' );

			while( $nodes->length > 0 )
			{
				$node = $nodes->item( 0 );
				$catid = $node->hasAttribute( 'catid' ) ? $node->getAttribute( 'catid' ) : null;
				$type = $node->hasAttribute( 'type' ) ? $node->getAttribute( 'type' ) : 'default';
				$limit = $node->hasAttribute( 'limit' ) ? $node->getAttribute( 'limit' ) : 3;

				$products = ( clone $cntl )->uses( $domains )
					->category( $catid, $type )
					->slice( 0, $limit )
					->search();

				$this->addMetaItems( $products, $expire, $tags );

				$tview = $context->view()->set( 'products', $products );

				if( !$products->isEmpty() && (bool) $config->get( 'client/html/catalog/lists/stock/enable', true ) === true ) {
					$tview->itemsStockUrl = $this->getStockUrl( $tview, $products );
				}

				$pdom = new \DOMDocument( '1.0', 'UTF-8' );
				$pdom->loadHTML( '<?xml encoding="utf-8" ?>' . $tview->render( $template ), LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD );

				$pnode = $dom->importNode( $pdom->documentElement, true );
				$node->parentNode->replaceChild( $pnode, $node );
			}

			$texts[] = substr( $dom->saveHTML(), 25 );
		}

		libxml_clear_errors();

		$view->pageContent = $texts;

		return parent::data( $view, $tags, $expire );
	}
}
