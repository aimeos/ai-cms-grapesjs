<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package Admin
 * @subpackage JQAdm
 */


namespace Aimeos\Admin\JQAdm\Cms\Seo;

sprintf( 'seo' ); // for translation


/**
 * Default implementation of cms SEO JQAdm client.
 *
 * @package Admin
 * @subpackage JQAdm
 */
class Standard
	extends \Aimeos\Admin\JQAdm\Common\Admin\Factory\Base
	implements \Aimeos\Admin\JQAdm\Common\Admin\Factory\Iface
{
	/** admin/jqadm/cms/seo/name
	 * Name of the SEO subpart used by the JQAdm cms implementation
	 *
	 * Use "Myname" if your class is named "\Aimeos\Admin\Jqadm\Cms\Text\Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the JQAdm class name
	 * @since 2020.10
	 * @category Developer
	 */


	/**
	 * Copies a resource
	 *
	 * @return string|null HTML output
	 */
	public function copy() : ?string
	{
		$view = $this->getObject()->addData( $this->view() );
		$view->seoData = $this->toArray( $view->item, true );
		$view->seoBody = parent::copy();

		return $this->render( $view );
	}


	/**
	 * Creates a new resource
	 *
	 * @return string|null HTML output
	 */
	public function create() : ?string
	{
		$view = $this->getObject()->addData( $this->view() );
		$siteid = $this->getContext()->getLocale()->getSiteId();
		$data = $view->param( 'seo', [] );

		foreach( $data as $idx => $entry )
		{
			$data[$idx]['cms.lists.siteid'] = $siteid;
			$data[$idx]['text.siteid'] = $siteid;
		}

		$view->seoData = $data;
		$view->seoBody = parent::create();

		return $this->render( $view );
	}


	/**
	 * Deletes a resource
	 *
	 * @return string|null HTML output
	 */
	public function delete() : ?string
	{
		parent::delete();

		$item = $this->view()->item;

		$listItems = $item->getListItems( 'text', null, null, false )->filter( function( $item ) {
			return $item->getRefItem() === null || $item->getRefItem()->getType() !== 'content';
		} );

		$item->deleteListItems( $listItems, true );

		return null;
	}


	/**
	 * Returns a single resource
	 *
	 * @return string|null HTML output
	 */
	public function get() : ?string
	{
		$view = $this->getObject()->addData( $this->view() );
		$view->seoData = $this->toArray( $view->item );
		$view->seoBody = parent::get();

		return $this->render( $view );
	}


	/**
	 * Saves the data
	 *
	 * @return string|null HTML output
	 */
	public function save() : ?string
	{
		$view = $this->view();

		$view->item = $this->fromArray( $view->item, $view->param( 'seo', [] ) );
		$view->seoBody = parent::save();

		return null;
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return \Aimeos\Admin\JQAdm\Iface Sub-client object
	 */
	public function getSubClient( string $type, string $name = null ) : \Aimeos\Admin\JQAdm\Iface
	{
		/** admin/jqadm/cms/seo/decorators/excludes
		 * Excludes decorators added by the "common" option from the cms JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to remove a decorator added via
		 * "admin/jqadm/common/decorators/default" before they are wrapped
		 * around the JQAdm client.
		 *
		 *  admin/jqadm/cms/seo/decorators/excludes = array( 'decorator1' )
		 *
		 * This would remove the decorator named "decorator1" from the list of
		 * common decorators ("\Aimeos\Admin\JQAdm\Common\Decorator\*") added via
		 * "admin/jqadm/common/decorators/default" to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2020.10
		 * @category Developer
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/cms/seo/decorators/global
		 * @see admin/jqadm/cms/seo/decorators/local
		 */

		/** admin/jqadm/cms/seo/decorators/global
		 * Adds a list of globally available decorators only to the cms JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap global decorators
		 * ("\Aimeos\Admin\JQAdm\Common\Decorator\*") around the JQAdm client.
		 *
		 *  admin/jqadm/cms/seo/decorators/global = array( 'decorator1' )
		 *
		 * This would add the decorator named "decorator1" defined by
		 * "\Aimeos\Admin\JQAdm\Common\Decorator\Decorator1" only to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2020.10
		 * @category Developer
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/cms/seo/decorators/excludes
		 * @see admin/jqadm/cms/seo/decorators/local
		 */

		/** admin/jqadm/cms/seo/decorators/local
		 * Adds a list of local decorators only to the cms JQAdm client
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to wrap local decorators
		 * ("\Aimeos\Admin\JQAdm\Cms\Decorator\*") around the JQAdm client.
		 *
		 *  admin/jqadm/cms/seo/decorators/local = array( 'decorator2' )
		 *
		 * This would add the decorator named "decorator2" defined by
		 * "\Aimeos\Admin\JQAdm\Cms\Decorator\Decorator2" only to the JQAdm client.
		 *
		 * @param array List of decorator names
		 * @since 2020.10
		 * @category Developer
		 * @see admin/jqadm/common/decorators/default
		 * @see admin/jqadm/cms/seo/decorators/excludes
		 * @see admin/jqadm/cms/seo/decorators/global
		 */
		return $this->createSubClient( 'cms/seo/' . $type, $name );
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of JQAdm client names
	 */
	protected function getSubClientNames() : array
	{
		/** admin/jqadm/cms/seo/subparts
		 * List of JQAdm sub-clients rendered within the cms seo section
		 *
		 * The output of the frontend is composed of the code generated by the JQAdm
		 * clients. Each JQAdm client can consist of serveral (or none) sub-clients
		 * that are responsible for rendering certain sub-parts of the output. The
		 * sub-clients can contain JQAdm clients themselves and therefore a
		 * hierarchical tree of JQAdm clients is composed. Each JQAdm client creates
		 * the output that is placed inside the container of its parent.
		 *
		 * At first, always the JQAdm code generated by the parent is printed, then
		 * the JQAdm code of its sub-clients. The order of the JQAdm sub-clients
		 * determines the order of the output of these sub-clients inside the parent
		 * container. If the configured list of clients is
		 *
		 *  array( "subclient1", "subclient2" )
		 *
		 * you can easily change the order of the output by reordering the subparts:
		 *
		 *  admin/jqadm/<clients>/subparts = array( "subclient1", "subclient2" )
		 *
		 * You can also remove one or more parts if they shouldn't be rendered:
		 *
		 *  admin/jqadm/<clients>/subparts = array( "subclient1" )
		 *
		 * As the clients only generates structural JQAdm, the layout defined via CSS
		 * should support adding, removing or reordering content by a fluid like
		 * design.
		 *
		 * @param array List of sub-client names
		 * @since 2020.10
		 * @category Developer
		 */
		return $this->getContext()->getConfig()->get( 'admin/jqadm/cms/seo/subparts', [] );
	}


	/**
	 * Adds the required data used in the seo template
	 *
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @return \Aimeos\MW\View\Iface View object with assigned parameters
	 */
	public function addData( \Aimeos\MW\View\Iface $view ) : \Aimeos\MW\View\Iface
	{
		$context = $this->getContext();

		$textTypeManager = \Aimeos\MShop::create( $context, 'text/type' );
		$listTypeManager = \Aimeos\MShop::create( $context, 'cms/lists/type' );

		$search = $textTypeManager->filter( true )->slice( 0, 10000 );
		$search->add( $search->and( [
			$search->is( 'text.type.domain', '==', 'cms' ),
			$search->is( 'text.type.code', '!=', 'content' )
		] ) );
		$search->setSortations( [$search->sort( '+', 'text.type.position' )] );

		$listSearch = $listTypeManager->filter( true )->slice( 0, 10000 );
		$listSearch->setConditions( $listSearch->compare( '==', 'cms.lists.type.domain', 'text' ) );
		$listSearch->setSortations( [$listSearch->sort( '+', 'cms.lists.type.position' )] );

		$view->seoTypes = $textTypeManager->search( $search );
		$view->seoListTypes = $listTypeManager->search( $listSearch );

		return $view;
	}


	/**
	 * Creates new and updates existing items using the data array
	 *
	 * @param \Aimeos\MShop\Cms\Item\Iface $item Cms item object without referenced domain items
	 * @param array $data Data array
	 * @return \Aimeos\MShop\Cms\Item\Iface Modified cms item
	 */
	protected function fromArray( \Aimeos\MShop\Cms\Item\Iface $item, array $data ) : \Aimeos\MShop\Cms\Item\Iface
	{
		$context = $this->getContext();

		$textManager = \Aimeos\MShop::create( $context, 'text' );
		$listManager = \Aimeos\MShop::create( $context, 'cms/lists' );

		$listItems = $item->getListItems( 'text', null, null, false )->filter( function( $item ) {
			return $item->getRefItem() === null || $item->getRefItem()->getType() !== 'content';
		} );


		foreach( $data as $idx => $entry )
		{
			if( trim( $this->getValue( $entry, 'text.content', '' ) ) === '' ) {
				continue;
			}

			$listType = $entry['cms.lists.type'] ?? 'default';

			if( ( $listItem = $item->getListItem( 'text', $listType, $entry['text.id'], false ) ) === null ) {
				$listItem = $listManager->create();
			}

			if( ( $refItem = $listItem->getRefItem() ) === null ) {
				$refItem = $textManager->create();
			}

			$refItem->fromArray( $entry, true );
			$conf = [];

			foreach( (array) $this->getValue( $entry, 'config', [] ) as $cfg )
			{
				if( ( $key = trim( $cfg['key'] ?? '' ) ) !== '' ) {
					$conf[$key] = trim( $cfg['val'] ?? '' );
				}
			}

			$listItem->fromArray( $entry, true );
			$listItem->setPosition( $idx );
			$listItem->setConfig( $conf );

			$item->addListItem( 'text', $listItem, $refItem );

			unset( $listItems[$listItem->getId()] );
		}

		return $item->deleteListItems( $listItems->toArray(), true );
	}


	/**
	 * Constructs the data array for the view from the given item
	 *
	 * @param \Aimeos\MShop\Cms\Item\Iface $item Cms item object including referenced domain items
	 * @param bool $copy True if items should be copied, false if not
	 * @return string[] Multi-dimensional associative list of item data
	 */
	protected function toArray( \Aimeos\MShop\Cms\Item\Iface $item, bool $copy = false ) : array
	{
		$data = [];
		$siteId = $this->getContext()->getLocale()->getSiteId();

		$listItems = $item->getListItems( 'text', null, null, false )->filter( function( $item ) {
			return $item->getRefItem() === null || $item->getRefItem()->getType() !== 'content';
		} );

		foreach( $listItems as $listItem )
		{
			if( ( $refItem = $listItem->getRefItem() ) === null || $refItem->getType() === 'content' ) {
				continue;
			}

			$list = $listItem->toArray( true ) + $refItem->toArray( true );

			if( $copy === true )
			{
				$list['cms.lists.siteid'] = $siteId;
				$list['cms.lists.id'] = '';
				$list['text.siteid'] = $siteId;
				$list['text.id'] = null;
			}

			$list['cms.lists.datestart'] = str_replace( ' ', 'T', $list['cms.lists.datestart'] );
			$list['cms.lists.dateend'] = str_replace( ' ', 'T', $list['cms.lists.dateend'] );
			$list['config'] = [];

			foreach( $listItem->getConfig() as $key => $value ) {
				$list['config'][] = ['key' => $key, 'val' => $value];
			}

			$data[] = $list;
		}

		return $data;
	}


	/**
	 * Returns the rendered template including the view data
	 *
	 * @param \Aimeos\MW\View\Iface $view View object with data assigned
	 * @return string HTML output
	 */
	protected function render( \Aimeos\MW\View\Iface $view ) : string
	{
		/** admin/jqadm/cms/seo/template-item
		 * Relative path to the HTML body template of the seo subpart for cmss.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the result shown in the body of the frontend. The
		 * configuration string is the path to the template file relative
		 * to the templates directory (usually in admin/jqadm/templates).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating the HTML code
		 * @since 2020.10
		 * @category Developer
		 */
		$tplconf = 'admin/jqadm/cms/seo/template-item';
		$default = 'cms/item-seo-standard';

		return $view->render( $view->config( $tplconf, $default ) );
	}
}
