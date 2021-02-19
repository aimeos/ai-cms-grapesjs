<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package Controller
 * @subpackage Frontend
 */


namespace Aimeos\Controller\Frontend\Cms\Decorator;


/**
 * Base for cms frontend controller decorators
 *
 * @package Controller
 * @subpackage Frontend
 */
abstract class Base
	extends \Aimeos\Controller\Frontend\Base
	implements \Aimeos\Controller\Frontend\Common\Decorator\Iface, \Aimeos\Controller\Frontend\Cms\Iface
{
	private $controller;


	/**
	 * Initializes the controller decorator.
	 *
	 * @param \Aimeos\Controller\Frontend\Iface $controller Controller object
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object with required objects
	 */
	public function __construct( \Aimeos\Controller\Frontend\Iface $controller, \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$iface = \Aimeos\Controller\Frontend\Cms\Iface::class;
		$this->controller = \Aimeos\MW\Common\Base::checkClass( $iface, $controller );
	}


	/**
	 * Passes unknown methods to wrapped objects.
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @return mixed Returns the value of the called method
	 * @throws \Aimeos\Controller\Frontend\Exception If method call failed
	 */
	public function __call( string $name, array $param )
	{
		return @call_user_func_array( array( $this->controller, $name ), $param );
	}


	/**
	 * Clones objects in controller and resets values
	 */
	public function __clone()
	{
		$this->controller = clone $this->controller;
	}


	/**
	 * Adds generic condition for filtering
	 *
	 * @param string $operator Comparison operator, e.g. "==", "!=", "<", "<=", ">=", ">", "=~", "~="
	 * @param string $key Search key defined by the cms manager, e.g. "cms.status"
	 * @param array|string $value Value or list of values to compare to
	 * @return \Aimeos\Controller\Frontend\Cms\Iface Cms controller for fluent interface
	 * @since 2019.04
	 */
	public function compare( string $operator, string $key, $value ) : \Aimeos\Controller\Frontend\Cms\Iface
	{
		$this->controller->compare( $operator, $key, $value );
		return $this;
	}


	/**
	 * Returns the cms for the given cms code
	 *
	 * @param string $code Unique cms code
	 * @return \Aimeos\MShop\Cms\Item\Iface Cms item including the referenced domains items
	 * @since 2019.04
	 */
	public function find( string $code ) : \Aimeos\MShop\Cms\Item\Iface
	{
		return $this->controller->find( $code );
	}


	/**
	 * Creates a search function string for the given name and parameters
	 *
	 * @param string $name Name of the search function without parenthesis, e.g. "cms:has"
	 * @param array $params List of parameters for the search function with numeric keys starting at 0
	 * @return string Search function string that can be used in compare()
	 */
	public function function( string $name, array $params ) : string
	{
		return $this->controller->function( $name, $params );
	}


	/**
	 * Returns the cms for the given cms ID
	 *
	 * @param string $id Unique cms ID
	 * @return \Aimeos\MShop\Cms\Item\Iface Cms item including the referenced domains items
	 * @since 2019.04
	 */
	public function get( string $id ) : \Aimeos\MShop\Cms\Item\Iface
	{
		return $this->controller->get( $id );
	}


	/**
	 * Adds a filter to return only items containing a reference to the given ID
	 *
	 * @param string $domain Domain name of the referenced item, e.g. "product"
	 * @param string|null $type Type code of the reference, e.g. "default" or null for all types
	 * @param string|null $refId ID of the referenced item of the given domain or null for all references
	 * @return \Aimeos\Controller\Frontend\Cms\Iface Cms controller for fluent interface
	 * @since 2019.10
	 */
	public function has( string $domain, string $type = null, string $refId = null ) : \Aimeos\Controller\Frontend\Cms\Iface
	{
		$this->controller->has( $domain, $type, $refId );
		return $this;
	}


	/**
	 * Parses the given array and adds the conditions to the list of conditions
	 *
	 * @param array $conditions List of conditions, e.g. ['>' => ['cms.dateback' => '2000-01-01 00:00:00']]
	 * @return \Aimeos\Controller\Frontend\Cms\Iface Cms controller for fluent interface
	 * @since 2019.04
	 */
	public function parse( array $conditions ) : \Aimeos\Controller\Frontend\Cms\Iface
	{
		$this->controller->parse( $conditions );
		return $this;
	}


	/**
	 * Returns the cmss filtered by the previously assigned conditions
	 *
	 * @param int &$total Parameter where the total number of found cmss will be stored in
	 * @return \Aimeos\Map Ordered list of items implementing \Aimeos\MShop\Cms\Item\Iface
	 * @since 2019.04
	 */
	public function search( int &$total = null ) : \Aimeos\Map
	{
		return $this->controller->search( $total );
	}


	/**
	 * Sets the start value and the number of returned cms items for slicing the list of found cms items
	 *
	 * @param int $start Start value of the first cms item in the list
	 * @param int $limit Number of returned cms items
	 * @return \Aimeos\Controller\Frontend\Cms\Iface Cms controller for fluent interface
	 * @since 2019.04
	 */
	public function slice( int $start, int $limit ) : \Aimeos\Controller\Frontend\Cms\Iface
	{
		$this->controller->slice( $start, $limit );
		return $this;
	}


	/**
	 * Sets the sorting of the result list
	 *
	 * @param string|null $key Sorting key of the result list like "cms.label", null for no sorting
	 * @return \Aimeos\Controller\Frontend\Cms\Iface Cms controller for fluent interface
	 * @since 2019.04
	 */
	public function sort( string $key = null ) : \Aimeos\Controller\Frontend\Cms\Iface
	{
		$this->controller->sort( $key );
		return $this;
	}


	/**
	 * Sets the referenced domains that will be fetched too when retrieving items
	 *
	 * @param array $domains Domain names of the referenced items that should be fetched too
	 * @return \Aimeos\Controller\Frontend\Cms\Iface Cms controller for fluent interface
	 * @since 2019.04
	 */
	public function uses( array $domains ) : \Aimeos\Controller\Frontend\Cms\Iface
	{
		$this->controller->uses( $domains );
		return $this;
	}


	/**
	 * Injects the reference of the outmost object
	 *
	 * @param \Aimeos\Controller\Frontend\Iface $object Reference to the outmost controller or decorator
	 * @return \Aimeos\Controller\Frontend\Iface Controller object for chaining method calls
	 */
	public function setObject( \Aimeos\Controller\Frontend\Iface $object ) : \Aimeos\Controller\Frontend\Iface
	{
		parent::setObject( $object );

		$this->controller->setObject( $object );

		return $this;
	}


	/**
	 * Returns the frontend controller
	 *
	 * @return \Aimeos\Controller\Frontend\Cms\Iface Frontend controller object
	 */
	protected function getController() : \Aimeos\Controller\Frontend\Cms\Iface
	{
		return $this->controller;
	}
}
