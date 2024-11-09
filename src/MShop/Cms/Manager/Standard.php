<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
 * @package MShop
 * @subpackage Cms
 */


namespace Aimeos\MShop\Cms\Manager;


/**
 * Default cms manager implementation
 *
 * @package MShop
 * @subpackage Cms
 */
class Standard
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Cms\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	/** mshop/cms/manager/name
	 * Class name of the used cms manager implementation
	 *
	 * Each default manager can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the manager factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\MShop\Cms\Manager\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\MShop\Cms\Manager\Mymanager
	 *
	 * then you have to set the this configuration option:
	 *
	 *  mshop/cms/manager/name = Mymanager
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyManager"!
	 *
	 * @param string Last part of the class name
	 * @since 2020.10
	 * @category Developer
	 */

	/** mshop/cms/manager/decorators/excludes
	 * Excludes decorators added by the "common" option from the cms manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "mshop/common/manager/decorators/default" before they are wrapped
	 * around the cms manager.
	 *
	 *  mshop/cms/manager/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\MShop\Common\Manager\Decorator\*") added via
	 * "mshop/common/manager/decorators/default" for the cms manager.
	 *
	 * @param array List of decorator names
	 * @since 2020.10
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/cms/manager/decorators/global
	 * @see mshop/cms/manager/decorators/local
	 */

	/** mshop/cms/manager/decorators/global
	 * Adds a list of globally available decorators only to the cms manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\MShop\Common\Manager\Decorator\*") around the cms manager.
	 *
	 *  mshop/cms/manager/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" only to the cms
	 * manager.
	 *
	 * @param array List of decorator names
	 * @since 2020.10
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/cms/manager/decorators/excludes
	 * @see mshop/cms/manager/decorators/local
	 */

	/** mshop/cms/manager/decorators/local
	 * Adds a list of local decorators only to the cms manager
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\MShop\Cms\Manager\Decorator\*") around the cms manager.
	 *
	 *  mshop/cms/manager/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\MShop\Cms\Manager\Decorator\Decorator2" only to the cms
	 * manager.
	 *
	 * @param array List of decorator names
	 * @since 2020.10
	 * @category Developer
	 * @see mshop/common/manager/decorators/default
	 * @see mshop/cms/manager/decorators/excludes
	 * @see mshop/cms/manager/decorators/global
	 */


	use \Aimeos\MShop\Common\Manager\ListsRef\Traits;


	private array $searchConfig = array(
		'cms.id' => array(
			'code' => 'cms.id',
			'internalcode' => 'mcms."id"',
			'label' => 'ID',
			'type' => 'int',
			'public' => false,
		),
		'cms.siteid' => array(
			'code' => 'cms.siteid',
			'internalcode' => 'mcms."siteid"',
			'label' => 'Site ID',
			'type' => 'string',
			'public' => false,
		),
		'cms.url' => array(
			'code' => 'cms.url',
			'internalcode' => 'mcms."url"',
			'label' => 'Type',
			'type' => 'string',
		),
		'cms.label' => array(
			'code' => 'cms.label',
			'internalcode' => 'mcms."label"',
			'label' => 'Label',
			'type' => 'string',
		),
		'cms.status' => array(
			'code' => 'cms.status',
			'internalcode' => 'mcms."status"',
			'label' => 'Status',
			'type' => 'int',
		),
		'cms.ctime' => array(
			'code' => 'cms.ctime',
			'internalcode' => 'mcms."ctime"',
			'label' => 'create date/time',
			'type' => 'datetime',
			'public' => false,
		),
		'cms.mtime' => array(
			'code' => 'cms.mtime',
			'internalcode' => 'mcms."mtime"',
			'label' => 'modify date/time',
			'type' => 'datetime',
			'public' => false,
		),
		'cms.editor' => array(
			'code' => 'cms.editor',
			'internalcode' => 'mcms."editor"',
			'label' => 'editor',
			'type' => 'string',
			'public' => false,
		),
		'cms:has' => array(
			'code' => 'cms:has()',
			'internalcode' => ':site AND :key AND mcmsli."id"',
			'internaldeps' => ['LEFT JOIN "mshop_cms_list" AS mcmsli ON ( mcmsli."parentid" = mcms."id" )'],
			'label' => 'Cms has list item, parameter(<domain>[,<list type>[,<reference ID>)]]',
			'type' => 'null',
			'public' => false,
		),
	);


	/**
	 * Initializes the object.
	 *
	 * @param \Aimeos\MShop\ContextIface $context Context object
	 */
	public function __construct( \Aimeos\MShop\ContextIface $context )
	{
		parent::__construct( $context );

		/** madmin/cms/manager/resource
		 * Name of the database connection resource to use
		 *
		 * You can configure a different database connection for each data domain
		 * and if no such connection name exists, the "db" connection will be used.
		 * It's also possible to use the same database connection for different
		 * data domains by configuring the same connection name using this setting.
		 *
		 * @param string Database connection name
		 * @since 2023.04
		 */
		$this->setResourceName( $context->config()->get( 'mshop/cms/manager/resource', 'db-cms' ) );

		$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ONE;
		$level = $context->config()->get( 'mshop/cms/manager/sitemode', $level );


		$this->searchConfig['cms:has']['function'] = function( &$source, array $params ) use ( $level ) {

			$keys = [];

			foreach( (array) ( $params[1] ?? '' ) as $type ) {
				foreach( (array) ( $params[2] ?? '' ) as $id ) {
					$keys[] = $params[0] . '|' . ( $type ? $type . '|' : '' ) . $id;
				}
			}

			$sitestr = $this->siteString( 'mcmsli."siteid"', $level );
			$keystr = $this->toExpression( 'mcmsli."key"', $keys, ( $params[2] ?? null ) ? '==' : '=~' );
			$source = str_replace( [':site', ':key'], [$sitestr, $keystr], $source );

			return $params;
		};
	}


	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Cms\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		$path = 'mshop/cms/manager/submanagers';
		foreach( $this->context()->config()->get( $path, ['lists'] ) as $domain ) {
			$this->object()->getSubManager( $domain )->clear( $siteids );
		}

		return $this->clearBase( $siteids, 'mshop/cms/manager/delete' );
	}


	/**
	 * Creates a new empty item instance
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Cms\Item\Iface New cms item object
	 */
	public function create( array $values = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values['cms.siteid'] = $this->context()->locale()->getSiteId();
		return $this->createItemBase( $values );
	}


	/**
	 * Adds or updates an item object or a list of them.
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface[]|\Aimeos\MShop\Common\Item\Iface $items Item or list of items whose data should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface Saved item or items
	 */
	public function save( $items, bool $fetch = true )
	{
		$items = parent::save( $items, $fetch );

		$this->context()->cache()->deleteByTags( map( $items )->getId()->prefix( 'cms-' ) );

		return $items;
	}


	/**
	 * Updates or adds a cms item object.
	 * This method doesn't update the type string that belongs to the type ID
	 *
	 * @param \Aimeos\MShop\Cms\Item\Iface $item Cms item which should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Cms\Item\Iface Updated item including the generated ID
	 */
	protected function saveItem( \Aimeos\MShop\Cms\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Cms\Item\Iface
	{
		if( !$item->isModified() ) {
			return $this->saveListItems( $item, 'cms', $fetch );
		}

		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

		$id = $item->getId();
		$date = date( 'Y-m-d H:i:s' );
		$columns = $this->object()->getSaveAttributes();

		if( $id === null )
		{
			/** mshop/cms/manager/insert/mysql
			 * Inserts a new cms record into the database table
			 *
			 * @see mshop/cms/manager/insert/ansi
			 */

			/** mshop/cms/manager/insert/ansi
			 * Inserts a new cms record into the database table
			 *
			 * Items with no ID yet (i.e. the ID is NULL) will be created in
			 * the database and the newly created ID retrieved afterwards
			 * using the "newid" SQL statement.
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the cms item to the statement before they are
			 * sent to the database server. The number of question marks must
			 * be the same as the number of columns listed in the INSERT
			 * statement. The order of the columns must correspond to the
			 * order in the save() method, so the correct values are
			 * bound to the columns.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for inserting records
			 * @since 2020.10
			 * @category Developer
			 * @see mshop/cms/manager/update/ansi
			 * @see mshop/cms/manager/newid/ansi
			 * @see mshop/cms/manager/delete/ansi
			 * @see mshop/cms/manager/search/ansi
			 * @see mshop/cms/manager/count/ansi
			 */
			$path = 'mshop/cms/manager/insert';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
		}
		else
		{
			/** mshop/cms/manager/update/mysql
			 * Updates an existing cms record in the database
			 *
			 * @see mshop/cms/manager/update/ansi
			 */

			/** mshop/cms/manager/update/ansi
			 * Updates an existing cms record in the database
			 *
			 * Items which already have an ID (i.e. the ID is not NULL) will
			 * be updated in the database.
			 *
			 * The SQL statement must be a string suitable for being used as
			 * prepared statement. It must include question marks for binding
			 * the values from the cms item to the statement before they are
			 * sent to the database server. The order of the columns must
			 * correspond to the order in the save() method, so the
			 * correct values are bound to the columns.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for updating records
			 * @since 2020.10
			 * @category Developer
			 * @see mshop/cms/manager/insert/ansi
			 * @see mshop/cms/manager/newid/ansi
			 * @see mshop/cms/manager/delete/ansi
			 * @see mshop/cms/manager/search/ansi
			 * @see mshop/cms/manager/count/ansi
			 */
			$path = 'mshop/cms/manager/update';
			$sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
		}

		$idx = 1;
		$stmt = $this->getCachedStatement( $conn, $path, $sql );

		foreach( $columns as $name => $entry ) {
			$stmt->bind( $idx++, $item->get( $name ), \Aimeos\Base\Criteria\SQL::type( $entry->getType() ) );
		}

		$stmt->bind( $idx++, $item->getUrl() );
		$stmt->bind( $idx++, $item->getLabel() );
		$stmt->bind( $idx++, $item->getStatus(), \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( $idx++, $date ); // mtime
		$stmt->bind( $idx++, $context->editor() );

		if( $id !== null ) {
			$stmt->bind( $idx++, $context->locale()->getSiteId() . '%' );
			$stmt->bind( $idx++, $id, \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		} else {
			$stmt->bind( $idx++, $this->siteId( $item->getSiteId(), \Aimeos\MShop\Locale\Manager\Base::SITE_SUBTREE ) );
			$stmt->bind( $idx++, $date ); // ctime
		}

		$stmt->execute()->finish();

		if( $id === null )
		{
			/** mshop/cms/manager/newid/mysql
			 * Retrieves the ID generated by the database when inserting a new record
			 *
			 * @see mshop/cms/manager/newid/ansi
			 */

			/** mshop/cms/manager/newid/ansi
			 * Retrieves the ID generated by the database when inserting a new record
			 *
			 * As soon as a new record is inserted into the database table,
			 * the database server generates a new and unique identifier for
			 * that record. This ID can be used for retrieving, updating and
			 * deleting that specific record from the table again.
			 *
			 * For MySQL:
			 *  SELECT LAST_INSERT_ID()
			 * For PostgreSQL:
			 *  SELECT currval('seq_mcms_id')
			 * For SQL Server:
			 *  SELECT SCOPE_IDENTITY()
			 * For Oracle:
			 *  SELECT "seq_mcms_id".CURRVAL FROM DUAL
			 *
			 * There's no way to retrive the new ID by a SQL statements that
			 * fits for most database servers as they implement their own
			 * specific way.
			 *
			 * @param string SQL statement for retrieving the last inserted record ID
			 * @since 2020.10
			 * @category Developer
			 * @see mshop/cms/manager/insert/ansi
			 * @see mshop/cms/manager/update/ansi
			 * @see mshop/cms/manager/delete/ansi
			 * @see mshop/cms/manager/search/ansi
			 * @see mshop/cms/manager/count/ansi
			 */
			$path = 'mshop/cms/manager/newid';
			$id = $this->newId( $conn, $path );
		}

		$item->setId( $id );

		return $this->saveListItems( $item, 'cms', $fetch );
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Cms\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		/** mshop/cms/manager/delete/mysql
		 * Deletes the items matched by the given IDs from the database
		 *
		 * @see mshop/cms/manager/delete/ansi
		 */

		/** mshop/cms/manager/delete/ansi
		 * Deletes the items matched by the given IDs from the database
		 *
		 * Removes the records specified by the given IDs from the cms database.
		 * The records must be from the site that is configured via the
		 * context item.
		 *
		 * The ":cond" placeholder is replaced by the name of the ID column and
		 * the given ID or list of IDs while the site ID is bound to the question
		 * mark.
		 *
		 * The SQL statement should conform to the ANSI standard to be
		 * compatible with most relational database systems. This also
		 * includes using double quotes for table and column names.
		 *
		 * @param string SQL statement for deleting items
		 * @since 2020.10
		 * @category Developer
		 * @see mshop/cms/manager/insert/ansi
		 * @see mshop/cms/manager/update/ansi
		 * @see mshop/cms/manager/newid/ansi
		 * @see mshop/cms/manager/search/ansi
		 * @see mshop/cms/manager/count/ansi
		 */
		$path = 'mshop/cms/manager/delete';

		return $this->deleteItemsBase( $itemIds, $path )->deleteRefItems( $itemIds );
	}


	/**
	 * Returns the item specified by its URL
	 *
	 * @param string $code URL of the item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param string|null $domain Domain of the item if necessary to identify the item uniquely
	 * @param string|null $type Type code of the item if necessary to identify the item uniquely
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Common\Item\Iface Item object
	 */
	public function find( string $code, array $ref = [], ?string $domain = null, ?string $type = null,
		?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->findBase( array( 'cms.url' => $code ), $ref, $default );
	}


	/**
	 * Returns the cms item object specified by the given ID.
	 *
	 * @param string $id Id of the cms item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Cms\Item\Iface Returns the cms item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		return $this->getItemBase( 'cms.id', $id, $ref, $default );
	}


	/**
	 * Returns the available manager types
	 *
	 * @param bool $withsub Return also the resource type of sub-managers if true
	 * @return string[] Type of the manager and submanagers, subtypes are separated by slashes
	 */
	public function getResourceType( bool $withsub = true ) : array
	{
		$path = 'mshop/cms/manager/submanagers';
		return $this->getResourceTypeBase( 'cms', $path, ['lists'], $withsub );
	}


	/**
	 * Returns the attributes that can be used for searching.
	 *
	 * @param bool $withsub Return also attributes of sub-managers if true
	 * @return \Aimeos\Base\Criteria\Attribute\Iface[] List of search attribute items
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		/** mshop/cms/manager/submanagers
		 * List of manager names that can be instantiated by the cms manager
		 *
		 * Managers provide a generic interface to the underlying storage.
		 * Each manager has or can have sub-managers caring about particular
		 * aspects. Each of these sub-managers can be instantiated by its
		 * parent manager using the getSubManager() method.
		 *
		 * The search keys from sub-managers can be normally used in the
		 * manager as well. It allows you to search for items of the manager
		 * using the search keys of the sub-managers to further limit the
		 * retrieved list of items.
		 *
		 * @param array List of sub-manager names
		 * @since 2020.10
		 * @category Developer
		 */
		$path = 'mshop/cms/manager/submanagers';

		return $this->getSearchAttributesBase( $this->searchConfig, $path, [], $withsub );
	}


	/**
	 * Searches for all cms items matching the given critera.
	 *
	 * @param \Aimeos\Base\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Cms\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\Base\Criteria\Iface $search, array $ref = [], ?int &$total = null ) : \Aimeos\Map
	{
		$map = [];
		$context = $this->context();
		$conn = $context->db( $this->getResourceName() );

			$required = array( 'cms' );

			/** mshop/cms/manager/sitemode
			 * Mode how items from levels below or above in the site tree are handled
			 *
			 * By default, only items from the current site are fetched from the
			 * storage. If the ai-sites extension is installed, you can create a
			 * tree of sites. Then, this setting allows you to define for the
			 * whole cms domain if items from parent sites are inherited,
			 * sites from child sites are aggregated or both.
			 *
			 * Available constants for the site mode are:
			 * * 0 = only items from the current site
			 * * 1 = inherit items from parent sites
			 * * 2 = aggregate items from child sites
			 * * 3 = inherit and aggregate items at the same time
			 *
			 * You also need to set the mode in the locale manager
			 * (mshop/locale/manager/sitelevel) to one of the constants.
			 * If you set it to the same value, it will work as described but you
			 * can also use different modes. For example, if inheritance and
			 * aggregation is configured the locale manager but only inheritance
			 * in the domain manager because aggregating items makes no sense in
			 * this domain, then items wil be only inherited. Thus, you have full
			 * control over inheritance and aggregation in each domain.
			 *
			 * @param int Constant from Aimeos\MShop\Locale\Manager\Base class
			 * @category Developer
			 * @since 2020.10
			 * @see mshop/locale/manager/sitelevel
			 */
			$level = \Aimeos\MShop\Locale\Manager\Base::SITE_ONE;
			$level = $context->config()->get( 'mshop/cms/manager/sitemode', $level );

			/** mshop/cms/manager/search/mysql
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * @see mshop/cms/manager/search/ansi
			 */

			/** mshop/cms/manager/search/ansi
			 * Retrieves the records matched by the given criteria in the database
			 *
			 * Fetches the records matched by the given criteria from the cms
			 * database. The records must be from one of the sites that are
			 * configured via the context item. If the current site is part of
			 * a tree of sites, the SELECT statement can retrieve all records
			 * from the current site and the complete sub-tree of sites.
			 *
			 * As the records can normally be limited by criteria from sub-managers,
			 * their tables must be joined in the SQL context. This is done by
			 * using the "internaldeps" property from the definition of the ID
			 * column of the sub-managers. These internal dependencies specify
			 * the JOIN between the tables and the used columns for joining. The
			 * ":joins" placeholder is then replaced by the JOIN strings from
			 * the sub-managers.
			 *
			 * To limit the records matched, conditions can be added to the given
			 * criteria object. It can contain comparisons like column names that
			 * must match specific values which can be combined by AND, OR or NOT
			 * operators. The resulting string of SQL conditions replaces the
			 * ":cond" placeholder before the statement is sent to the database
			 * server.
			 *
			 * If the records that are retrieved should be ordered by one or more
			 * columns, the generated string of column / sort direction pairs
			 * replaces the ":order" placeholder. In case no ordering is required,
			 * the complete ORDER BY part including the "\/*-orderby*\/...\/*orderby-*\/"
			 * markers is removed to speed up retrieving the records. Columns of
			 * sub-managers can also be used for ordering the result set but then
			 * no index can be used.
			 *
			 * The number of returned records can be limited and can start at any
			 * number between the begining and the end of the result set. For that
			 * the ":size" and ":start" placeholders are replaced by the
			 * corresponding values from the criteria object. The default values
			 * are 0 for the start and 100 for the size value.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for searching items
			 * @since 2020.10
			 * @category Developer
			 * @see mshop/cms/manager/insert/ansi
			 * @see mshop/cms/manager/update/ansi
			 * @see mshop/cms/manager/newid/ansi
			 * @see mshop/cms/manager/delete/ansi
			 * @see mshop/cms/manager/count/ansi
			 */
			$cfgPathSearch = 'mshop/cms/manager/search';

			/** mshop/cms/manager/count/mysql
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * @see mshop/cms/manager/count/ansi
			 */

			/** mshop/cms/manager/count/ansi
			 * Counts the number of records matched by the given criteria in the database
			 *
			 * Counts all records matched by the given criteria from the cms
			 * database. The records must be from one of the sites that are
			 * configured via the context item. If the current site is part of
			 * a tree of sites, the statement can count all records from the
			 * current site and the complete sub-tree of sites.
			 *
			 * As the records can normally be limited by criteria from sub-managers,
			 * their tables must be joined in the SQL context. This is done by
			 * using the "internaldeps" property from the definition of the ID
			 * column of the sub-managers. These internal dependencies specify
			 * the JOIN between the tables and the used columns for joining. The
			 * ":joins" placeholder is then replaced by the JOIN strings from
			 * the sub-managers.
			 *
			 * To limit the records matched, conditions can be added to the given
			 * criteria object. It can contain comparisons like column names that
			 * must match specific values which can be combined by AND, OR or NOT
			 * operators. The resulting string of SQL conditions replaces the
			 * ":cond" placeholder before the statement is sent to the database
			 * server.
			 *
			 * Both, the strings for ":joins" and for ":cond" are the same as for
			 * the "search" SQL statement.
			 *
			 * Contrary to the "search" statement, it doesn't return any records
			 * but instead the number of records that have been found. As counting
			 * thousands of records can be a long running task, the maximum number
			 * of counted records is limited for performance reasons.
			 *
			 * The SQL statement should conform to the ANSI standard to be
			 * compatible with most relational database systems. This also
			 * includes using double quotes for table and column names.
			 *
			 * @param string SQL statement for counting items
			 * @since 2020.10
			 * @category Developer
			 * @see mshop/cms/manager/insert/ansi
			 * @see mshop/cms/manager/update/ansi
			 * @see mshop/cms/manager/newid/ansi
			 * @see mshop/cms/manager/delete/ansi
			 * @see mshop/cms/manager/search/ansi
			 */
			$cfgPathCount = 'mshop/cms/manager/count';

			$results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

			while( ( $row = $results->fetch() ) !== null ) {
				$map[$row['cms.id']] = $row;
			}

		return $this->buildItems( $map, $ref, 'cms' );
	}


	/**
	 * Returns a new manager for cms extensions
	 *
	 * @param string $manager Name of the sub manager type in lower case
	 * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g types, lists etc.
	 */
	public function getSubManager( string $manager, ?string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this->getSubManagerBase( 'cms', $manager, $name );
	}


	/**
	 * Creates a filter object.
	 *
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @param bool $site TRUE for adding site criteria to limit items by the site of related items
	 * @return \Aimeos\Base\Criteria\Iface Returns the filter object
	 */
	public function filter( ?bool $default = false, bool $site = false ) : \Aimeos\Base\Criteria\Iface
	{
		return $this->filterBase( 'cms', $default );
	}


	/**
	 * Creates a new cms item instance.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface $refItems List of referenced items
	 * @return \Aimeos\MShop\Cms\Item\Iface New cms item
	 */
	protected function createItemBase( array $values = [], array $listItems = [], array $refItems = [] ) : \Aimeos\MShop\Common\Item\Iface
	{
		return new \Aimeos\MShop\Cms\Item\Standard( $values, $listItems, $refItems );
	}
}
