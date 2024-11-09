<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2023
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html\Cms\Page;


/**
 * Default implementation of cms page section HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Standard
	extends \Aimeos\Client\Html\Common\Client\Factory\Base
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	/** client/html/cms/page/name
	 * Class name of the used cms page client implementation
	 *
	 * Each default HTML client can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the client factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\Client\Html\Cms\Page\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\Client\Html\Cms\Page\Mypage
	 *
	 * then you have to set the this configuration option:
	 *
	 *  client/html/cms/page/name = Mypage
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyPage"!
	 *
	 * @param string Last part of the class name
	 * @since 2021.04
	 * @category Developer
	 */

	/** client/html/cms/page/subparts
	 * List of HTML sub-clients rendered within the cms page section
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
	 * @since 2021.04
	 * @category Developer
	 */
	private string $subPartPath = 'client/html/cms/page/subparts';
	private array $subPartNames = ['contact', 'cataloglist'];

	private array $tags = [];
	private ?string $expire = null;
	private ?\Aimeos\Base\View\Iface $view = null;


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function body( string $uid = '' ) : string
	{
		/** client/html/cms/page/cache
		 * Enables or disables caching only for the cms page component
		 *
		 * Disable caching for components can be useful if you would have too much
		 * entries to cache or if the component contains non-cacheable parts that
		 * can't be replaced using the modify() method.
		 *
		 * @param boolean True to enable caching, false to disable
		 * @category Developer
		 * @category User
		 * @see client/html/cms/page/cache
		 * @see client/html/cms/filter/cache
		 * @see client/html/cms/lists/cache
		 */

		/** client/html/cms/page
		 * All parameters defined for the cms page component and its subparts
		 *
		 * This returns all settings related to the page component.
		 * Please refer to the single settings for pages.
		 *
		 * @param array Associative list of name/value settings
		 * @category Developer
		 * @see client/html/cms#page
		 */
		$confkey = 'client/html/cms/page';
		$prefixes = [];

		$path = $this->view()->request()->getUri()->getPath();

		if( $html = $this->cached( 'body', $uid . '-' . $path, $prefixes, $confkey ) ) {
			return $this->modify( $html, $uid );
		}

		$view = $this->view = $this->view ?? $this->object()->data( $this->view(), $this->tags, $this->expire );

		if( !isset( $view->pageCmsItem ) ) {
			return '';
		}

		$html = '';
		foreach( $this->getSubClients() as $subclient ) {
			$html .= $subclient->setView( $view )->body( $uid );
		}

		$template = $this->context()->config()->get( 'client/html/cms/page/template-body', 'cms/page/body' );
		$html = $view->set( 'body', $html )->render( $template );

		$this->cache( 'body', $uid . '-' . $path, $prefixes, $confkey, $html, $this->tags, $this->expire );

		return $this->modify( $html, $uid );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string|null String including HTML tags for the header on error
	 */
	public function header( string $uid = '' ) : ?string
	{
		$path = $this->view()->request()->getUri()->getPath();
		$confkey = 'client/html/cms/page';
		$prefixes = [];

		if( $html = $this->cached( 'header', $uid . '-' . $path, $prefixes, $confkey ) ) {
			return $this->modify( $html, $uid );
		}

		$view = $this->view = $this->view ?? $this->object()->data( $this->view(), $this->tags, $this->expire );

		if( !isset( $view->pageCmsItem ) ) {
			return '';
		}

		$template = $this->context()->config()->get( 'client/html/cms/page/template-header', 'cms/page/header' );
		$html = $view->render( $template );

		$this->cache( 'header', $uid . '-' . $path, $prefixes, $confkey, $html, $this->tags, $this->expire );

		return $this->modify( $html, $uid );
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Client\Html\Iface Sub-client object
	 */
	public function getSubClient( string $type, ?string $name = null ) : \Aimeos\Client\Html\Iface
	{
		/** client/html/cms/page/decorators/excludes
		 * Excludes decorators added by the "common" option from the cms page html client
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
		 *  client/html/cms/page/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Client\Html\Common\Decorator\*") added via
		 * "client/html/common/decorators/default" to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2021.04
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/cms/page/decorators/global
		 * @see client/html/cms/page/decorators/local
		 */

		/** client/html/cms/page/decorators/global
		 * Adds a list of globally available decorators only to the cms page html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Client\Html\Common\Decorator\*") around the html client.
		 *
		 *  client/html/cms/page/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Client\Html\Common\Decorator\Decorator1" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2021.04
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/cms/page/decorators/excludes
		 * @see client/html/cms/page/decorators/local
		 */

		/** client/html/cms/page/decorators/local
		 * Adds a list of local decorators only to the cms page html client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Client\Html\Cms\Decorator\*") around the html client.
		 *
		 *  client/html/cms/page/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Client\Html\Cms\Decorator\Decorator2" only to the html client.
		 *
		 * @param array List of decorator names
		 * @since 2021.04
		 * @category Developer
		 * @see client/html/common/decorators/default
		 * @see client/html/cms/page/decorators/excludes
		 * @see client/html/cms/page/decorators/global
		 */
		return $this->createSubClient( 'cms/page/' . $type, $name );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param \Aimeos\Base\View\Iface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return \Aimeos\Base\View\Iface Modified view object
	 */
	public function data( \Aimeos\Base\View\Iface $view, array &$tags = [], ?string &$expire = null ) : \Aimeos\Base\View\Iface
	{
		$context = $this->context();
		$controller = \Aimeos\Controller\Frontend::create( $context, 'cms' );

		/** client/html/cms/page/domains
		 * A list of domain names whose items should be available in the cms page view template
		 *
		 * The templates rendering the cms page section use the texts and
		 * maybe images and attributes associated to the categories. You can
		 * configure your own list of domains (attribute, media, price, product,
		 * text, etc. are domains) whose items are fetched from the storage.
		 * Please keep in mind that the more domains you add to the configuration,
		 * the more time is required for fetching the content!
		 *
		 * @param array List of domain names
		 * @since 2021.04
		 */
		$domains = $context->config()->get( 'client/html/cms/page/domains', ['text'] );

		$path = '/' . trim( $view->param( 'path', $view->request()->getUri()->getPath() ), '/' );

		if( $page = $controller->uses( $domains )->compare( '==', 'cms.url', $path )->search()->first() )
		{
			$this->addMetaItems( $page, $expire, $tags );

			$view->pageCmsItem = $page;
			$view->pageContent = $page->getRefItems( 'text', 'content' )->map( function( $item ) {
				$data = ( $json = json_decode( $item->getContent(), true ) ) ? $json['html'] : $item->getContent();
				return '<div class="cms-content">' . $data . '</div>';
			} )->all();
		}

		return parent::data( $view, $tags, $expire );
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of HTML client names
	 */
	protected function getSubClientNames() : array
	{
		return $this->context()->config()->get( $this->subPartPath, $this->subPartNames );
	}


	/** client/html/cms/page/template-body
	 * Relative path to the HTML body template of the cms page client.
	 *
	 * The template file contains the HTML code and processing instructions
	 * to generate the result shown in the body of the frontend. The
	 * configuration string is the path to the template file relative
	 * to the templates directory (usually in client/html/templates).
	 *
	 * You can overwrite the template file configuration in extensions and
	 * provide alternative templates. These alternative templates should be
	 * named like the default one but with the string "standard" replaced by
	 * an unique name. You may use the name of your project for this. If
	 * you've implemented an alternative client class as well, "standard"
	 * should be replaced by the name of the new class.
	 *
	 * @param string Relative path to the template creating code for the HTML page body
	 * @since 2021.04
	 * @category Developer
	 * @see client/html/cms/page/template-header
	 */

	/** client/html/cms/page/template-header
	 * Relative path to the HTML header template of the cms page client.
	 *
	 * The template file contains the HTML code and processing instructions
	 * to generate the HTML code that is inserted into the HTML page header
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
	 * @param string Relative path to the template creating code for the HTML page head
	 * @since 2021.04
	 * @category Developer
	 * @see client/html/cms/page/template-body
	 */
}
