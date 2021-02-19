<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package Controller
 * @subpackage Frontend
 */


namespace Aimeos\Controller\Frontend\Cms;


/**
 * Default implementation of the cms frontend controller
 *
 * @package Controller
 * @subpackage Frontend
 */
class Standard
	extends \Aimeos\Controller\Frontend\Base
	implements Iface, \Aimeos\Controller\Frontend\Common\Iface
{
	private $conditions = [];
	private $domains = [];
	private $filter;
	private $manager;


	/**
	 * Common initialization for controller classes
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Common MShop context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$this->manager = \Aimeos\MShop::create( $context, 'cms' );
		$this->filter = $this->manager->filter( true );
		$this->conditions[] = $this->filter->getConditions();
	}


	/**
	 * Clones objects in controller and resets values
	 */
	public function __clone()
	{
		$this->filter = clone $this->filter;
	}


	/**
	 * Adds generic condition for filtering
	 *
	 * @param string $operator Comparison operator, e.g. "==", "!=", "<", "<=", ">=", ">", "=~", "~="
	 * @param string $key Search key defined by the cms manager, e.g. "cms.status"
	 * @param array|string $value Value or list of values to compare to
	 * @return \Aimeos\Controller\Frontend\Cms\Iface Cms controller for fluent interface
	 * @since 2021.04
	 */
	public function compare( string $operator, string $key, $value ) : Iface
	{
		$this->conditions[] = $this->filter->compare( $operator, $key, $value );
		return $this;
	}


	/**
	 * Returns the cms for the given cms code
	 *
	 * @param string $code Unique cms code
	 * @return \Aimeos\MShop\Cms\Item\Iface Cms item including the referenced domains items
	 * @since 2021.04
	 */
	public function find( string $code ) : \Aimeos\MShop\Cms\Item\Iface
	{
		return $this->manager->find( $code, $this->domains, null, null, true );
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
		return $this->filter->make( $name, $params );
	}


	/**
	 * Returns the cms for the given cms ID
	 *
	 * @param string $id Unique cms ID
	 * @return \Aimeos\MShop\Cms\Item\Iface Cms item including the referenced domains items
	 * @since 2021.04
	 */
	public function get( string $id ) : \Aimeos\MShop\Cms\Item\Iface
	{
		return $this->manager->get( $id, $this->domains, true );
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
	public function has( string $domain, string $type = null, string $refId = null ) : Iface
	{
		$params = [$domain];
		!$type ?: $params[] = $type;
		!$refId ?: $params[] = $refId;

		$func = $this->filter->make( 'cms:has', $params );
		$this->conditions[] = $this->filter->compare( '!=', $func, null );
		return $this;
	}


	/**
	 * Parses the given array and adds the conditions to the list of conditions
	 *
	 * @param array $conditions List of conditions, e.g. ['=~' => ['cms.label' => 'test']]
	 * @return \Aimeos\Controller\Frontend\Cms\Iface Cms controller for fluent interface
	 * @since 2021.04
	 */
	public function parse( array $conditions ) : Iface
	{
		if( ( $cond = $this->filter->parse( $conditions ) ) !== null ) {
			$this->conditions[] = $cond;
		}

		return $this;
	}


	/**
	 * Returns the cmss filtered by the previously assigned conditions
	 *
	 * @param int &$total Parameter where the total number of found cmss will be stored in
	 * @return \Aimeos\Map Ordered list of cms items implementing \Aimeos\MShop\Cms\Item\Iface
	 * @since 2021.04
	 */
	public function search( int &$total = null ) : \Aimeos\Map
	{
		$this->filter->setConditions( $this->filter->and( $this->conditions ) );
		return $this->manager->search( $this->filter, $this->domains, $total );
	}


	/**
	 * Sets the start value and the number of returned cms items for slicing the list of found cms items
	 *
	 * @param int $start Start value of the first cms item in the list
	 * @param int $limit Number of returned cms items
	 * @return \Aimeos\Controller\Frontend\Cms\Iface Cms controller for fluent interface
	 * @since 2021.04
	 */
	public function slice( int $start, int $limit ) : Iface
	{
		$this->filter->slice( $start, $limit );
		return $this;
	}


	/**
	 * Sets the sorting of the result list
	 *
	 * @param string|null $key Sorting key of the result list like "cms.label", null for no sorting
	 * @return \Aimeos\Controller\Frontend\Cms\Iface Cms controller for fluent interface
	 * @since 2021.04
	 */
	public function sort( string $key = null ) : Iface
	{
		$sort = [];
		$list = ( $key ? explode( ',', $key ) : [] );

		foreach( $list as $sortkey )
		{
			$direction = ( $sortkey[0] === '-' ? '-' : '+' );
			$sort[] = $this->filter->sort( $direction, ltrim( $sortkey, '+-' ) );
		}

		$this->filter->setSortations( $sort );
		return $this;
	}


	/**
	 * Sets the referenced domains that will be fetched too when retrieving items
	 *
	 * @param array $domains Domain names of the referenced items that should be fetched too
	 * @return \Aimeos\Controller\Frontend\Cms\Iface Cms controller for fluent interface
	 * @since 2021.04
	 */
	public function uses( array $domains ) : Iface
	{
		$this->domains = $domains;
		return $this;
	}
}
