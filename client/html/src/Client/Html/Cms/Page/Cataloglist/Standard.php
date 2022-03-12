<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
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
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/cms/page/cataloglist/subparts
	 * List of HTML sub-clients rendered within the cms page cataloglist section
	 *
	 * The output of the frontend is composed of the code generated by the HTML
	 * clients. Each HTML client can consist of serveral (or none) sub-clients
	 * that are responsible for rendering certain sub-parts of the output. The
	 * sub-clients can contain HTML clients themselves and therefore a
	 * hierarchical tree of HTML clients is composed. Each HTML client creates
	 * the output that is placed inside the container of its parent.
	 *
	 * At first, always the HTML code generated by the parent is printed, then
	 * the HTML code of its sub-clients. The order of the HTML sub-clients
	 * determines the order of the output of these sub-clients inside the parent
	 * container. If the configured list of clients is
	 *
	 *  array( "subclient1", "subclient2" )
	 *
	 * you can easily change the order of the output by reordering the subparts:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1", "subclient2" )
	 *
	 * You can also remove one or more parts if they shouldn't be rendered:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1" )
	 *
	 * As the clients only generates structural HTML, the layout defined via CSS
	 * should support adding, removing or reordering content by a fluid like
	 * design.
	 *
	 * @param array List of sub-client names
	 * @since 2021.07
	 * @category Developer
	 */
	private $subPartPath = 'client/html/cms/page/cataloglist/subparts';
	private $subPartNames = [];


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function getBody( string $uid = '' ) : string
	{
		return '';
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Client\Html\Iface Sub-client object
	 */
	public function getSubClient( string $type, string $name = null ) : \Aimeos\Client\Html\Iface
	{
		/** client/html/cms/page/cataloglist/decorators/excludes
		 * Excludes decorators added by the "common" option from the cms page cataloglist html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "client/html/common/decorators/default" before they are wrapped
		 * around the html client.
		 *
		 *  client/html/cms/page/cataloglist/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
		 * "client/html/common/decorators/default" to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2021.07
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/cms/page/cataloglist/decorators/global
		 * @see client/html/cms/page/cataloglist/decorators/local
		 */

		/** client/html/cms/page/cataloglist/decorators/global
		 * Adds a list of globally available decorators only to the cms page cataloglist html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
		 *
		 *  client/html/cms/page/cataloglist/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2021.07
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/cms/page/cataloglist/decorators/excludes
		 * @see client/html/cms/page/cataloglist/decorators/local
		 */

		/** client/html/cms/page/cataloglist/decorators/local
		 * Adds a list of local decorators only to the cms page cataloglist html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Client\Html\Cms\Decorator\*") around the html client.
		 *
		 *  client/html/cms/page/cataloglist/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Client\Html\Cms\Decorator\Decorator2" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2021.07
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/cms/page/cataloglist/decorators/excludes
		 * @see client/html/cms/page/cataloglist/decorators/global
		 */

		return $this->createSubClient( 'cms/page/cataloglist/' . $type, $name );
	}


	/**
	 * Modifies the cached content to replace content based on sessions or cookies.
	 *
	 * @param string $content Cached content
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string Modified content
	 */
	public function modifyBody( string $content, string $uid ) : string
	{
		$content = parent::modifyBody( $content, $uid );

		return $this->replaceSection( $content, $this->getView()->csrf()->formfield(), 'catalog.lists.items.csrf' );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param \Aimeos\MW\View\Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return \Aimeos\MW\View\Iface Modified view object
	 */
	public function addData( \Aimeos\MW\View\Iface $view, array &$tags = [], string &$expire = null ) : \Aimeos\MW\View\Iface
	{
		if( !isset( $view->pageContent ) ) {
			return parent::addData( $view, $tags, $expire );
		}

		$texts = [];
		$context = $this->getContext();
		$config = $context->getConfig();
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
		$template = $config->get( 'client/html/cms/page/template-cataloglist', 'cms/page/cataloglist/list-standard' );
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

				$tview = $context->getView()->set( 'products', $products );

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

		return parent::addData( $view, $tags, $expire );
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of HTML client names
	 */
	protected function getSubClientNames() : array
	{
		return $this->getContext()->getConfig()->get( $this->subPartPath, $this->subPartNames );
	}
}
