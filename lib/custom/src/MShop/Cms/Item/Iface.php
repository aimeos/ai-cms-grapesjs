<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package MShop
 * @subpackage Cms
 */


namespace Aimeos\MShop\Cms\Item;


/**
 * Generic interface for cms pages created and saved by cms managers.
 *
 * @package MShop
 * @subpackage Cms
 */
interface Iface
	extends \Aimeos\MShop\Common\Item\Iface, \Aimeos\MShop\Common\Item\ListsRef\Iface, \Aimeos\MShop\Common\Item\Status\Iface
{
	/**
	 * Returns the URL of the cms page.
	 *
	 * @return string URL of the cms page
	 */
	public function getUrl() : string;

	/**
	 * Sets the URL of the cms page.
	 *
	 * @param string $value URL of the cms page
	 * @return \Aimeos\MShop\Cms\Item\Iface Cms page for chaining method calls
	 */
	public function setUrl( string $value ) : \Aimeos\MShop\Cms\Item\Iface;

	/**
	 * Returns the name of the attribute page.
	 *
	 * @return string Label of the attribute page
	 */
	public function getLabel() : string;

	/**
	 * Sets the new label of the attribute page.
	 *
	 * @param string $label Type label of the attribute page
	 * @return \Aimeos\MShop\Cms\Item\Iface Cms page for chaining method calls
	 */
	public function setLabel( ?string $label ) : \Aimeos\MShop\Cms\Item\Iface;
}
