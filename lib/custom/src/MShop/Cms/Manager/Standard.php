<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
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
class Standard extends Base
    implements Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{

    private $searchConfig = array(
        'id' => array(
            'code' => 'cms.id',
            'internalcode' => 'mcms."id"',
            'label' => 'ID',
            'type' => 'integer',
            'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
            'public' => false,
        ),
        'cms.siteid' => array(
            'code' => 'cms.siteid',
            'internalcode' => 'mcms."siteid"',
            'label' => 'Site ID',
            'type' => 'string',
            'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
            'public' => false,
        ),
        'parentid' => array(
            'code' => 'cms.parentid',
            'internalcode' => 'mcms."parentid"',
            'label' => 'Parent ID',
            'type' => 'integer',
            'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
            'public' => false,
        ),
        'cms.url' => array(
            'code' => 'cms.url',
            'internalcode' => 'mcms."url"',
            'label' => 'Type',
            'type' => 'string',
            'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
        ),
        'cms.target' => array(
            'code' => 'cms.target',
            'internalcode' => 'mcms."target"',
            'label' => 'URL target',
            'type' => 'string',
            'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
        ),
        'label' => array(
            'code' => 'cms.label',
            'internalcode' => 'mcms."label"',
            'label' => 'Label',
            'type' => 'string',
            'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
        ),
        'code' => array(
            'code' => 'cms.code',
            'internalcode' => 'mcms."code"',
            'label' => 'Code',
            'type' => 'string',
            'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
        ),
        'status' => array(
            'code' => 'cms.status',
            'internalcode' => 'mcms."status"',
            'label' => 'Status',
            'type' => 'integer',
            'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
        ),
        'level' => array(
            'code' => 'cms.level',
            'internalcode' => 'mcms."level"',
            'label' => 'Tree level',
            'type' => 'integer',
            'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
            'public' => false,
        ),
        'left' => array(
            'code' => 'cms.left',
            'internalcode' => 'mcms."nleft"',
            'label' => 'Left value',
            'type' => 'integer',
            'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
            'public' => false,
        ),
        'right' => array(
            'code' => 'cms.right',
            'internalcode' => 'mcms."nright"',
            'label' => 'Right value',
            'type' => 'integer',
            'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_INT,
            'public' => false,
        ),
        'cms.ctime' => array(
            'code' => 'cms.ctime',
            'internalcode' => 'mcms."ctime"',
            'label' => 'create date/time',
            'type' => 'datetime',
            'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
            'public' => false,
        ),
        'cms.mtime' => array(
            'code' => 'cms.mtime',
            'internalcode' => 'mcms."mtime"',
            'label' => 'modify date/time',
            'type' => 'datetime',
            'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
            'public' => false,
        ),
        'cms.editor' => array(
            'code' => 'cms.editor',
            'internalcode' => 'mcms."editor"',
            'label' => 'editor',
            'type' => 'string',
            'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_STR,
            'public' => false,
        ),
        'cms:has' => array(
            'code' => 'cms:has()',
            'internalcode' => ':site AND :key AND mcmsli."id"',
            'internaldeps' => ['LEFT JOIN "mshop_cms_list" AS mcmsli ON ( mcmsli."parentid" = mcms."id" )'],
            'label' => 'Cms has list item, parameter(<domain>[,<list type>[,<reference ID>)]]',
            'type' => 'null',
            'internaltype' => 'null',
            'public' => false,
        ),
    );


    /**
     * Initializes the object.
     *
     * @param \Aimeos\MShop\Context\Item\Iface $context Context object
     */
    public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
    {
        parent::__construct( $context, $this->searchConfig );
        $this->setResourceName( 'db-cms' );

        $level = \Aimeos\MShop\Locale\Manager\Base::SITE_ALL;
        $level = $context->getConfig()->get( 'mshop/cms/manager/sitemode', $level );


        $this->searchConfig['cms:has']['function'] = function( &$source, array $params ) use ( $level ) {

            $keys = [];

            foreach( (array) ( $params[1] ?? '' ) as $type ) {
                foreach( (array) ( $params[2] ?? '' ) as $id ) {
                    $keys[] = $params[0] . '|' . ( $type ? $type . '|' : '' ) . $id;
                }
            }

            $sitestr = $this->getSiteString( 'mcmsli."siteid"', $level );
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
        foreach( $this->getContext()->getConfig()->get( $path, ['lists'] ) as $domain ) {
            $this->getObject()->getSubManager( $domain )->clear( $siteids );
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
        $values['siteid'] = $this->getContext()->getLocale()->getSiteId();
        return $this->createItemBase( $values );
    }


    /**
     * Updates an item object.
     *
     * @param \Aimeos\MShop\Cms\Item\Iface $item Item object whose data should be saved
     * @param bool $fetch True if the new ID should be returned in the item
     * @return \Aimeos\MShop\Cms\Item\Iface $item Updated item including the generated ID
     */
    public function saveItem( \Aimeos\MShop\Cms\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Cms\Item\Iface
    {
        if( !$item->isModified() )
        {
            $item = $this->saveListItems( $item, 'cms', $fetch );
            return $this->saveChildren( $item );
        }

        $node = $item->getNode();
        $siteid = $this->getContext()->getLocale()->getSiteId();

        $this->createTreeManager( $siteid )->saveNode( $node );
        $this->updateUsage( $node->getId(), $item );

        $item = $this->saveListItems( $item, 'cms', $fetch );
        return $this->saveChildren( $item );
    }

    /**
     * Returns a list of items starting with the given category that are in the path to the root node
     *
     * @param string $id ID of item to get the path for
     * @param string[] $ref List of domains to fetch list items and referenced items for
     * @return \Aimeos\Map Associative list of cms items implementing \Aimeos\MShop\Cms\Item\Iface with IDs as keys
     */
    public function getPath( string $id, array $ref = [] ) : \Aimeos\Map
    {
        $sitePath = array_reverse( (array) $this->getContext()->getLocale()->getSitePath() );

        foreach( $sitePath as $siteId )
        {
            try {
                $path = $this->createTreeManager( $siteId )->getPath( $id );
            } catch( \Exception $e ) {
                continue;
            }

            if( !empty( $path ) )
            {
                $itemMap = [];

                foreach( $path as $node ) {
                    $itemMap[$node->getId()] = $node;
                }

                return $this->buildItems( $itemMap, $ref, 'cms' );
            }
        }

        throw new \Aimeos\MShop\Cms\Exception( sprintf( 'Cms path for ID "%1$s" not found', $id ) );
    }

    /**
     * Moves an existing item to the new parent in the storage.
     *
     * @param string $id ID of the item that should be moved
     * @param string|null $oldParentId ID of the old parent item which currently contains the item that should be removed
     * @param string|null $newParentId ID of the new parent item where the item should be moved to
     * @param string|null $refId ID of the item where the item should be inserted before (null to append)
     * @return \Aimeos\MShop\Cms\Manager\Iface Manager object for chaining method calls
     */
    public function move( string $id, string $oldParentId = null, string $newParentId = null,
                          string $refId = null ) : \Aimeos\MShop\Cms\Manager\Iface
    {
        $this->begin();
        $this->lock();

        try
        {
            $item = $this->getObject()->get( $id );
            $siteid = $this->getContext()->getLocale()->getSiteId();

            $this->createTreeManager( $siteid )->moveNode( $id, $oldParentId, $newParentId, $refId );
            $this->updateUsage( $id, $item );
            $this->unlock();
            $this->commit();
        }
        catch( \Exception $e )
        {
            $this->unlock();
            $this->rollback();
            throw $e;
        }

        return $this;
    }


    /**
     * @param string|null $id
     * @param array $ref
     * @param int $level
     * @param \Aimeos\MW\Criteria\Iface|null $criteria
     * @return \Aimeos\MShop\Cms\Item\Iface
     * @throws \Aimeos\MShop\Cms\Exception
     */
    public function getTree( string $id = null, array $ref = [], int $level = \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE,
                             \Aimeos\MW\Criteria\Iface $criteria = null ) : \Aimeos\MShop\Cms\Item\Iface
    {
        $sitePath = array_reverse( (array) $this->getContext()->getLocale()->getSitePath() );

        foreach( $sitePath as $siteId )
        {
            try {
                $node = $this->createTreeManager( $siteId )->getNode( $id, $level, $criteria );
            } catch( \Aimeos\MW\Tree\Exception $e ) {
                continue;
            }

            $listItems = $listItemMap = $refIdMap = [];
            $nodeMap = $this->getNodeMap( $node );

            if( count( $ref ) > 0 ) {
                $listItems = $this->getListItems( array_keys( $nodeMap ), $ref, 'cms' );
            }

            foreach( $listItems as $listItem )
            {
                $domain = $listItem->getDomain();
                $parentid = $listItem->getParentId();

                $listItemMap[$parentid][$domain][$listItem->getId()] = $listItem;
                $refIdMap[$domain][$listItem->getRefId()][] = $parentid;
            }

            $refItemMap = $this->getRefItems( $refIdMap, $ref );
            $nodeid = $node->getId();

            $listItems = [];
            if( array_key_exists( $nodeid, $listItemMap ) ) {
                $listItems = $listItemMap[$nodeid];
            }

            $refItems = [];
            if( array_key_exists( $nodeid, $refItemMap ) ) {
                $refItems = $refItemMap[$nodeid];
            }

            if( $item = $this->applyFilter( $this->createItemBase( [], $listItems, $refItems, [], $node ) ) )
            {
                $this->createTree( $node, $item, $listItemMap, $refItemMap );
                return $item;
            }
        }

        throw new \Aimeos\MShop\Cms\Exception( sprintf( 'No cms node for ID "%1$s"', $id ) );
    }

    /**
     * @param string|null $id
     * @param array $ref
     * @param int $level
     * @param \Aimeos\MW\Criteria\Iface|null $criteria
     * @return []\Aimeos\MShop\Cms\Item\Iface
     * @throws \Aimeos\MShop\Cms\Exception
     */
    public function getAllTree( string $id = null, array $ref = [], int $level = \Aimeos\MW\Tree\Manager\Base::LEVEL_TREE,
                                \Aimeos\MW\Criteria\Iface $criteria = null ) : array
    {
        $sitePath = array_reverse( (array) $this->getContext()->getLocale()->getSitePath() );

        if(!$id) {
            $search = $this->filter();
            $search->setConditions($search->compare('==', 'cms.level', 0));
            $map = $this->search($search);

            $ids = $map->keys()->toArray();
        } else {
            $ids = (array) $id;
        }

        $nodes = [];

        foreach( $sitePath as $siteId )
        {

            foreach ($ids as $id) {

                try {
                    /** @var \Aimeos\MW\Tree\Manager\DBNestedSet $treeManager */
                    $treeManager = $this->createTreeManager( $siteId );
                    $node = $treeManager->getNode( $id, $level, $criteria );
                } catch( \Aimeos\MW\Tree\Exception $e ) {
                    continue;
                }

                $listItems = $listItemMap = $refIdMap = [];
                $nodeMap = $this->getNodeMap( $node );

                if( count( $ref ) > 0 ) {
                    $listItems = $this->getListItems( array_keys( $nodeMap ), $ref, 'cms' );
                }

                foreach( $listItems as $listItem )
                {
                    $domain = $listItem->getDomain();
                    $parentid = $listItem->getParentId();

                    $listItemMap[$parentid][$domain][$listItem->getId()] = $listItem;
                    $refIdMap[$domain][$listItem->getRefId()][] = $parentid;
                }

                $refItemMap = $this->getRefItems( $refIdMap, $ref );
                $nodeid = $node->getId();

                $listItems = [];
                if( array_key_exists( $nodeid, $listItemMap ) ) {
                    $listItems = $listItemMap[$nodeid];
                }

                $refItems = [];
                if( array_key_exists( $nodeid, $refItemMap ) ) {
                    $refItems = $refItemMap[$nodeid];
                }

                if( $item = $this->applyFilter( $this->createItemBase( [], $listItems, $refItems, [], $node ) ) )
                {
                    $this->createTree( $node, $item, $listItemMap, $refItemMap );
                    $nodes[] = $item;
//                    return $item;
                }
            }

//            if(count($nodes) > 0) {
//                return $nodes;
//            }


        }

        return $nodes;

//        throw new \Aimeos\MShop\Cms\Exception( sprintf( 'No cms node for ID "%1$s"', $id ) );
    }


    /**
     * @param \Aimeos\MShop\Cms\Item\Iface $item
     * @param string|null $parentId
     * @param string|null $refId
     * @return \Aimeos\MShop\Cms\Item\Iface
     * @throws \Exception
     */
    public function insert( \Aimeos\MShop\Cms\Item\Iface $item, string $parentId = null,
                            string $refId = null ) : \Aimeos\MShop\Cms\Item\Iface
    {
        $this->begin();
        $this->lock();

        try
        {
            $node = $item->getNode();
            $siteid = $this->getContext()->getLocale()->getSiteId();

            $this->createTreeManager( $siteid )->insertNode( $node, $parentId, $refId );
            $this->updateUsage( $node->getId(), $item, true );
            $this->unlock();
            $this->commit();
        }
        catch( \Exception $e )
        {
            $this->unlock();
            $this->rollback();
            throw $e;
        }

        $item = $this->saveListItems( $item, 'cms' );
        return $this->saveChildren( $item );
    }


    /**
     * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Iface|array|string $items
     * @return \Aimeos\MShop\Common\Manager\Iface
     * @throws \Exception
     */
    public function delete( $items ) : \Aimeos\MShop\Common\Manager\Iface
    {
        if( is_map( $items ) ) { $items = $items->toArray(); }
        if( !is_array( $items ) ) { $items = [$items]; }
        if( empty( $items ) ) { return $this; }

        $this->begin();
        $this->lock();

        try
        {
            $siteid = $this->getContext()->getLocale()->getSiteId();

            foreach( $items as $item ) {
                $this->createTreeManager( $siteid )->deleteNode( (string) $item );
            }

            $this->unlock();
            $this->commit();
        }
        catch( \Exception $e )
        {
            $this->unlock();
            $this->rollback();
            throw $e;
        }

        return $this->deleteRefItems( $items );
    }


    /**
     * Returns the item specified by its URL
     *
     * @param string $code URL of the item
     * @param string[] $ref List of domains to fetch list items and referenced items for
     * @param string|null $domain Domain of the item if necessary to identify the item uniquely
     * @param string|null $type Type code of the item if necessary to identify the item uniquely
     * @param bool $default True to add default criteria
     * @return \Aimeos\MShop\Common\Item\Iface Item object
     */
    public function find( string $code, array $ref = [], string $domain = null, string $type = null,
                          bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
    {
        return $this->findBase( array( 'cms.url' => $code ), $ref, $default );
    }


    /**
     * Returns the cms item object specified by the given ID.
     *
     * @param string $id Id of the cms item
     * @param string[] $ref List of domains to fetch list items and referenced items for
     * @param bool $default Add default criteria
     * @return \Aimeos\MShop\Cms\Item\Iface Returns the cms item of the given id
     * @throws \Aimeos\MShop\Exception If item couldn't be found
     */
    public function get( string $id, array $ref = [], bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
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
     * @return \Aimeos\MW\Criteria\Attribute\Iface[] List of search attribute items
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
     * Searches for all items matching the given critera.
     *
     * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
     * @param string[] $ref List of domains to fetch list items and referenced items for
     * @param int|null &$total Number of items that are available in total
     * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Cms\Item\Iface with ids as keys
     */
    public function search( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
    {
        $nodeMap = $siteMap = [];
        $context = $this->getContext();

        $dbname = $this->getResourceName();
        $dbm = $context->getDatabaseManager();
        $conn = $dbm->acquire( $dbname );

        try
        {
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
             * @since 2018.01
             * @see mshop/locale/manager/sitelevel
             */
            $level = \Aimeos\MShop\Locale\Manager\Base::SITE_PATH;
            $level = $context->getConfig()->get( 'mshop/cms/manager/sitemode', $level );

            /** mshop/cms/manager/search-item/mysql
             * Retrieves the records matched by the given criteria in the database
             *
             * @see mshop/cms/manager/search-item/ansi
             */

            /** mshop/cms/manager/search-item/ansi
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
             * @since 2014.03
             * @category Developer
             * @see mshop/cms/manager/delete/ansi
             * @see mshop/cms/manager/get/ansi
             * @see mshop/cms/manager/insert/ansi
             * @see mshop/cms/manager/update/ansi
             * @see mshop/cms/manager/newid/ansi
             * @see mshop/cms/manager/search/ansi
             * @see mshop/cms/manager/count/ansi
             * @see mshop/cms/manager/move-left/ansi
             * @see mshop/cms/manager/move-right/ansi
             * @see mshop/cms/manager/update-parentid/ansi
             */
            $cfgPathSearch = 'mshop/cms/manager/search-item';

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
             * @since 2014.03
             * @category Developer
             * @see mshop/cms/manager/delete/ansi
             * @see mshop/cms/manager/get/ansi
             * @see mshop/cms/manager/insert/ansi
             * @see mshop/cms/manager/update/ansi
             * @see mshop/cms/manager/newid/ansi
             * @see mshop/cms/manager/search/ansi
             * @see mshop/cms/manager/search-item/ansi
             * @see mshop/cms/manager/move-left/ansi
             * @see mshop/cms/manager/move-right/ansi
             * @see mshop/cms/manager/update-parentid/ansi
             */
            $cfgPathCount = 'mshop/cms/manager/count';

            if( $search->getSortations() === [] ) {
                $search->setSortations( [$search->sort( '+', 'cms.left' )] );
            }

            $results = $this->searchItemsBase( $conn, $search, $cfgPathSearch, $cfgPathCount, $required, $total, $level );

            while( ( $row = $results->fetch() ) !== null ) {
                $siteMap[(string) $row['siteid']][(string) $row['id']] = new \Aimeos\MW\Tree\Node\Standard( $row );
            }

            $sitePath = array_reverse( (array) $this->getContext()->getLocale()->getSitePath() );

            foreach( $sitePath as $siteId )
            {
                if( isset( $siteMap[$siteId] ) && !empty( $siteMap[$siteId] ) )
                {
                    $nodeMap = $siteMap[$siteId];
                    break;
                }
            }

            $dbm->release( $conn, $dbname );
        }
        catch( \Exception $e )
        {
            $dbm->release( $conn, $dbname );
            throw $e;
        }

        return $this->buildItems( $nodeMap, $ref, 'cms' );
    }


    /**
     * Saves the children of the given node
     *
     * @param \Aimeos\MShop\Cms\Item\Iface $item Cms item object incl. child items
     * @return \Aimeos\MShop\Cms\Item\Iface Cms item with saved child items
     */
    protected function saveChildren( \Aimeos\MShop\Cms\Item\Iface $item ) : \Aimeos\MShop\Cms\Item\Iface
    {
        $rmIds = [];
        foreach( $item->getChildrenDeleted() as $child ) {
            $rmIds[] = $child->getId();
        }

        $this->delete( $rmIds );

        foreach( $item->getChildren() as $child )
        {
            if( $child->getId() !== null )
            {
                $this->save( $child );

                if( $child->getParentId() !== $item->getParentId() ) {
                    $this->move( $child->getId(), $item->getParentId(), $child->getParentId() );
                }
            }
            else
            {
                $this->insert( $child, $item->getId() );
            }
        }

        return $item;
    }

    /**
     * Locks the cms table against modifications from other connections
     *
     * @return \Aimeos\MShop\Cms\Manager\Iface Manager object for chaining method calls
     */
    protected function lock() : \Aimeos\MShop\Cms\Manager\Iface
    {
        /** mshop/cms/manager/lock/mysql
         * SQL statement for locking the cms table
         *
         * @see mshop/cms/manager/lock/ansi
         */

        /** mshop/cms/manager/lock/ansi
         * SQL statement for locking the cms table
         *
         * Updating the nested set of categories in the cms table requires locking
         * the whole table to avoid data corruption. This statement will be followed by
         * insert or update statements and closed by an unlock statement.
         *
         * @param string Lock SQL statement
         * @since 2019.04
         * @category Developer
         */
        $path = 'mshop/cms/manager/lock';

        if( ( $sql = $this->getSqlConfig( $path ) ) !== $path )
        {
            $dbname = $this->getResourceName();
            $dbm = $this->getContext()->getDatabaseManager();

            $conn = $dbm->acquire( $dbname );
            $conn->create( $sql )->execute()->finish();
            $dbm->release( $conn, $dbname );
        }

        return $this;
    }


    /**
     * Unlocks the cms table for modifications from other connections
     *
     * @return \Aimeos\MShop\Cms\Manager\Iface Manager object for chaining method calls
     */
    protected function unlock() : \Aimeos\MShop\Cms\Manager\Iface
    {
        /** mshop/cms/manager/unlock/mysql
         * SQL statement for unlocking the cms table
         *
         * @see mshop/cms/manager/unlock/ansi
         */

        /** mshop/cms/manager/unlock/ansi
         * SQL statement for unlocking the cms table
         *
         * Updating the nested set of categories in the cms table requires locking
         * the whole table to avoid data corruption. This statement will be executed
         * after the table is locked and insert or update statements have been sent to
         * the database.
         *
         * @param string Lock SQL statement
         * @since 2019.04
         * @category Developer
         */
        $path = 'mshop/cms/manager/unlock';

        if( ( $sql = $this->getSqlConfig( $path ) ) !== $path )
        {
            $dbname = $this->getResourceName();
            $dbm = $this->getContext()->getDatabaseManager();

            $conn = $dbm->acquire( $dbname );
            $conn->create( $sql )->execute()->finish();
            $dbm->release( $conn, $dbname );
        }

        return $this;
    }

    /**
     * Returns a new manager for cms extensions
     *
     * @param string $manager Name of the sub manager type in lower case
     * @param string|null $name Name of the implementation, will be from configuration (or Default) if null
     * @return \Aimeos\MShop\Common\Manager\Iface Manager for different extensions, e.g types, lists etc.
     */
    public function getSubManager( string $manager, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface
    {
        return $this->getSubManagerBase( 'cms', $manager, $name );
    }


    /**
     * Creates a filter object.
     *
     * @param bool $default Add default criteria
     * @param bool $site TRUE for adding site criteria to limit items by the site of related items
     * @return \Aimeos\MW\Criteria\Iface Returns the filter object
     */
    public function filter( bool $default = false, bool $site = false ) : \Aimeos\MW\Criteria\Iface
    {
        if( $default === true ) {
            return $this->filterBase( 'cms' );
        }

        return parent::filter();
    }

    /**
     * Updates the usage information of a node.
     *
     * @param string $id Id of the record
     * @param \Aimeos\MShop\Cms\Item\Iface $item Cms item
     * @param bool $case True if the record shoud be added or false for an update
     * @return \Aimeos\MShop\Cms\Manager\Iface Manager object for chaining method calls
     */
    private function updateUsage( string $id, \Aimeos\MShop\Cms\Item\Iface $item, bool $case = false ) : \Aimeos\MShop\Cms\Manager\Iface
    {
        $date = date( 'Y-m-d H:i:s' );
        $context = $this->getContext();

        $dbm = $context->getDatabaseManager();
        $dbname = $this->getResourceName();
        $conn = $dbm->acquire( $dbname );

        try
        {
            $siteid = $context->getLocale()->getSiteId();
            $columns = $this->getObject()->getSaveAttributes();

            if( $case !== true )
            {
                /** mshop/cms/manager/update-usage/mysql
                 * Updates the config, editor and mtime value of an updated record
                 *
                 * @see mshop/cms/manager/update-usage/ansi
                 */

                /** mshop/cms/manager/update-usage/ansi
                 * Updates the config, editor and mtime value of an updated record
                 *
                 * Each record contains some usage information like when it was
                 * created, last modified and by whom. These information are part
                 * of the cms items and the generic tree manager doesn't care
                 * about this information. Thus, they are updated after the tree
                 * manager saved the basic record information.
                 *
                 * The SQL statement must be a string suitable for being used as
                 * prepared statement. It must include question marks for binding
                 * the values from the cms item to the statement before they are
                 * sent to the database server. The order of the columns must
                 * correspond to the order in the method using this statement,
                 * so the correct values are bound to the columns.
                 *
                 * The SQL statement should conform to the ANSI standard to be
                 * compatible with most relational database systems. This also
                 * includes using double quotes for table and column names.
                 *
                 * @param string SQL statement for updating records
                 * @since 2014.03
                 * @category Developer
                 * @see mshop/cms/manager/delete/ansi
                 * @see mshop/cms/manager/get/ansi
                 * @see mshop/cms/manager/insert/ansi
                 * @see mshop/cms/manager/newid/ansi
                 * @see mshop/cms/manager/search/ansi
                 * @see mshop/cms/manager/search-item/ansi
                 * @see mshop/cms/manager/count/ansi
                 * @see mshop/cms/manager/move-left/ansi
                 * @see mshop/cms/manager/move-right/ansi
                 * @see mshop/cms/manager/update-parentid/ansi
                 * @see mshop/cms/manager/insert-usage/ansi
                 */
                $path = 'mshop/cms/manager/update-usage';
                $sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ), false );
            }
            else
            {
                /** mshop/cms/manager/insert-usage/mysql
                 * Updates the config, editor, ctime and mtime value of an inserted record
                 *
                 * @see mshop/cms/manager/insert-usage/ansi
                 */

                /** mshop/cms/manager/insert-usage/ansi
                 * Updates the config, editor, ctime and mtime value of an inserted record
                 *
                 * Each record contains some usage information like when it was
                 * created, last modified and by whom. These information are part
                 * of the cms items and the generic tree manager doesn't care
                 * about this information. Thus, they are updated after the tree
                 * manager inserted the basic record information.
                 *
                 * The SQL statement must be a string suitable for being used as
                 * prepared statement. It must include question marks for binding
                 * the values from the cms item to the statement before they are
                 * sent to the database server. The order of the columns must
                 * correspond to the order in the method using this statement,
                 * so the correct values are bound to the columns.
                 *
                 * The SQL statement should conform to the ANSI standard to be
                 * compatible with most relational database systems. This also
                 * includes using double quotes for table and column names.
                 *
                 * @param string SQL statement for updating records
                 * @since 2014.03
                 * @category Developer
                 * @see mshop/cms/manager/delete/ansi
                 * @see mshop/cms/manager/get/ansi
                 * @see mshop/cms/manager/insert/ansi
                 * @see mshop/cms/manager/newid/ansi
                 * @see mshop/cms/manager/search/ansi
                 * @see mshop/cms/manager/search-item/ansi
                 * @see mshop/cms/manager/count/ansi
                 * @see mshop/cms/manager/move-left/ansi
                 * @see mshop/cms/manager/move-right/ansi
                 * @see mshop/cms/manager/update-parentid/ansi
                 * @see mshop/cms/manager/update-usage/ansi
                 */
                $path = 'mshop/cms/manager/insert-usage';
                $sql = $this->addSqlColumns( array_keys( $columns ), $this->getSqlConfig( $path ) );
            }

            $idx = 1;
            $stmt = $this->getCachedStatement( $conn, $path, $sql );

            foreach( $columns as $name => $entry ) {
                $stmt->bind( $idx++, $item->get( $name ), $entry->getInternalType() );
            }

            $stmt->bind( $idx++, $item->getUrl() );
            $stmt->bind( $idx++, json_encode( $item->getConfig() ) );
            $stmt->bind( $idx++, $date ); // mtime
            $stmt->bind( $idx++, $context->getEditor() );
            $stmt->bind( $idx++, $item->getTarget() );

            if( $case !== true )
            {
                $stmt->bind( $idx++, $siteid );
                $stmt->bind( $idx++, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
            }
            else
            {
                $stmt->bind( $idx++, $date ); // ctime
                $stmt->bind( $idx++, $siteid );
                $stmt->bind( $idx++, $id, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
            }

            $stmt->execute()->finish();

            $dbm->release( $conn, $dbname );
        }
        catch( \Exception $e )
        {
            $dbm->release( $conn, $dbname );
            throw $e;
        }

        return $this;
    }

}
