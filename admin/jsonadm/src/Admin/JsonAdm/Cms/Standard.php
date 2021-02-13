<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package Admin
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm\Cms;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


/**
 * JSON API catalog client
 *
 * @package Admin
 * @subpackage JsonAdm
 */
class Standard
	extends \Aimeos\Admin\JsonAdm\Standard
	implements \Aimeos\Admin\JsonAdm\Common\Iface
{
	/**
	 * Returns the items with parent/child relationships
	 *
	 * @param \Aimeos\Map $items List of items implementing \Aimeos\MShop\Common\Item\Iface
	 * @param array $include List of resource types that should be fetched
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	protected function getChildItems( \Aimeos\Map $items, array $include ) : \Aimeos\Map
	{
		$list = map();

		if( in_array( 'cms', $include ) )
		{
			foreach( $items as $item ) {
				$list = $list->push( $item )->merge( $this->getChildItems( map( $item->getChildren() ), $include ) );
			}
		}

		return $list;
	}

    /**
     * @param \Aimeos\MW\View\Iface $view
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Aimeos\MShop\Cms\Exception
     * @throws \Aimeos\MShop\Exception
     */
	protected function getItems( \Aimeos\MW\View\Iface $view, ServerRequestInterface $request, ResponseInterface $response ) : \Psr\Http\Message\ResponseInterface
	{
	    /** @var \Aimeos\MShop\Cms\Manager\Standard $manager */
		$manager = \Aimeos\MShop::create( $this->getContext(), 'cms' );

		if( ( $key = $view->param( 'aggregate' ) ) !== null )
		{
			$search = $this->initCriteria( $manager->filter(), $view->param() );
			$view->data = $manager->aggregate( $search, explode( ',', $key ) );
			return $response;
		}

		$include = ( ( $include = $view->param( 'include' ) ) !== null ? explode( ',', $include ) : [] );
		$search = $this->initCriteria( $manager->filter(), $view->param() );
		$total = 1;

		if( ( $id = $view->param( 'id' ) ) == null )
		{
			$view->data = $manager->search( $search, $include, $total );
			$view->listItems = $this->getListItems( $view->data, $include );
			$view->childItems = map();
		}
		else
		{
            $level = \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE;
			if( in_array( 'cms', $include ) ) {
				$level = \Aimeos\MW\Tree\Manager\Base::LEVEL_LIST;
			}

			if ($id === '0') {
			    $id = null;
            }

			$view->data = $manager->getAllTree( $id, $include, $level, $search );
			$view->listItems = $this->getListItems( map( [$id => $view->data] ), $include );

			$childItems = map();

			/** @var \Aimeos\MShop\Cms\Item\Standard $item */
            foreach ($view->data as $item) {
                $itemsMap = $this->getChildItems( map( $item->getChildren() ), $include );
                foreach ($itemsMap as $cmsItem) {
                   $childItems->push($cmsItem);
                }
            }

            $view->childItems = $childItems;
		}

		$view->refItems = $this->getRefItems( $view->listItems );
		$view->total = $total;

		return $response;
	}


	/**
	 * Returns the list items for association relationships
	 *
	 * @param \Aimeos\Map $items List of items implementing \Aimeos\MShop\Common\Item\Iface
	 * @param array $include List of resource types that should be fetched
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 */
	protected function getListItems( \Aimeos\Map $items, array $include ) : \Aimeos\Map
	{
		return $items->getListItems( null, null, null, false )->collapse();
	}


	/**
	 * Saves and returns the new or updated item
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager responsible for the items
	 * @param \stdClass $entry Object including "id" and "attributes" elements
	 * @return \Aimeos\MShop\Common\Item\Iface New or updated item
	 */
	protected function saveEntry( \Aimeos\MShop\Common\Manager\Iface $manager, \stdClass $entry ) : \Aimeos\MShop\Common\Item\Iface
	{
		$targetId = ( isset( $entry->targetid ) ? $entry->targetid : null );
		$refId = ( isset( $entry->refid ) ? $entry->refid : null );

		if( isset( $entry->id ) )
		{
			$item = $manager->get( $entry->id );

			if( isset( $entry->attributes ) && ( $attr = (array) $entry->attributes ) ) {
				$item = $item->fromArray( $attr, true );
			}

			$item = $manager->save( $item );

			if( isset( $entry->parentid ) && $targetId !== null ) {
				$manager->move( $item->getId(), $entry->parentid, $targetId, $refId );
			}
		}
		else
		{
			$item = $manager->create();

			if( isset( $entry->attributes ) && ( $attr = (array) $entry->attributes ) ) {
				$item = $item->fromArray( $attr, true );
			}

			$item = $manager->insert( $item, $targetId, $refId );
		}

		if( isset( $entry->relationships ) ) {
			$this->saveRelationships( $manager, $item, $entry->relationships );
		}

		return $manager->get( $item->getId() );
	}
}
