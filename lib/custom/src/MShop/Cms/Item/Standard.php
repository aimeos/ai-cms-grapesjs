<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package MShop
 * @subpackage Cms
 */


namespace Aimeos\MShop\Cms\Item;


/**
 * Default cms manager implementation.
 *
 * @package MShop
 * @subpackage Cms
 */
class Standard
	extends \Aimeos\MShop\Common\Item\Base
	implements \Aimeos\MShop\Cms\Item\Iface
{
	use \Aimeos\MShop\Common\Item\ListsRef\Traits;


	/**
	 * Initializes the cms item object with the given values.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 */
	public function __construct( array $values = [], array $listItems = [], array $refItems = [] )
	{
		parent::__construct( 'cms.', $values );

		$this->initListItems( $listItems, $refItems );
	}


	/**
	 * Returns the URL of the cms item.
	 *
	 * @return string URL of the cms item
	 */
	public function getUrl() : string
	{
		return $this->get( 'cms.url', '' );
	}


	/**
	 * Sets the URL of the cms item.
	 *
	 * @param string $value URL of the cms item
	 * @return \Aimeos\MShop\Cms\Item\Iface Cms item for chaining method calls
	 */
	public function setUrl( string $value ) : \Aimeos\MShop\Cms\Item\Iface
	{
		$url = \Aimeos\Map::explode( '/', trim( $value, '/' ) )->map( function( $segment ) {
			return \Aimeos\MW\Str::slug( $segment );
		} )->join( '/' );

		return $this->set( 'cms.url', '/' . $url );
	}


	/**
	 * Returns the name of the attribute item.
	 *
	 * @return string Label of the attribute item
	 */
	public function getLabel() : string
	{
		return $this->get( 'cms.label', '' );
	}


	/**
	 * Sets the new label of the attribute item.
	 *
	 * @param string $label Type label of the attribute item
	 * @return \Aimeos\MShop\Cms\Item\Iface Cms item for chaining method calls
	 */
	public function setLabel( ?string $label ) : \Aimeos\MShop\Cms\Item\Iface
	{
		return $this->set( 'cms.label', (string) $label );
	}


	/**
	 * Returns the status of the cms item.
	 *
	 * @return int Status of the cms item
	 */
	public function getStatus() : int
	{
		return $this->get( 'cms.status', 1 );
	}


	/**
	 * Sets the status of the cms item.
	 *
	 * @param int $status true/false for enabled/disabled
	 * @return \Aimeos\MShop\Cms\Item\Iface Cms item for chaining method calls
	 */
	public function setStatus( int $status ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->set( 'cms.status', $status );
	}


	/**
	 * Returns the item type
	 *
	 * @return string Item type, subtypes are separated by slashes
	 */
	public function getResourceType() : string
	{
		return 'cms';
	}


	/**
	 * Tests if the item is available based on status, time, language and currency
	 *
	 * @return bool True if available, false if not
	 */
	public function isAvailable() : bool
	{
		return parent::isAvailable() && $this->getStatus() > 0;
	}


	/**
	 * Sets the item values from the given array and removes that entries from the list
	 *
	 * @param array &$list Associative list of item keys and their values
	 * @param bool True to set private properties too, false for public only
	 * @return \Aimeos\MShop\Cms\Item\Iface Cms item for chaining method calls
	 */
	public function fromArray( array &$list, bool $private = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$item = parent::fromArray( $list, $private );

		foreach( $list as $key => $value )
		{
			switch( $key )
			{
				case 'cms.url': $item = $item->setUrl( $value ); break;
				case 'cms.label': $item = $item->setLabel( $value ); break;
				case 'cms.status': $item = $item->setStatus( (int) $value ); break;
				default: continue 2;
			}

			unset( $list[$key] );
		}

		return $item;
	}


	/**
	 * Returns the item values as array.
	 *
	 * @param bool True to return private properties, false for public only
	 * @return array Associative list of item properties and their values
	 */
	public function toArray( bool $private = false ) : array
	{
		$list = parent::toArray( $private );

		$list['cms.url'] = $this->getUrl();
		$list['cms.label'] = $this->getLabel();
		$list['cms.status'] = $this->getStatus();

		return $list;
	}
}
