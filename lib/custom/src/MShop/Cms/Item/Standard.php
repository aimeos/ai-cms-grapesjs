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
    use \Aimeos\MShop\Common\Item\Config\Traits;
    use \Aimeos\MShop\Common\Item\ListsRef\Traits;

    private $node;
    private $children;
    private $deletedItems = [];


    /**
     * Initializes the cms item.
     *
     * @param \Aimeos\MW\Tree\Node\Iface $node Tree node
     * @param \Aimeos\MShop\Cms\Item\Iface[] $children List of children of the item
     * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
     * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
     */
    public function __construct( \Aimeos\MW\Tree\Node\Iface $node, array $children = [],
                                 array $listItems = [], array $refItems = [] )
    {
        parent::__construct( '', [] );

        \Aimeos\MW\Common\Base::checkClassList( \Aimeos\MShop\Cms\Item\Iface::class, $children );

        $this->initListItems( $listItems, $refItems );
        $this->children = $children;
        $this->node = $node;
    }

    /**
     * Sets the config property of the cms item.
     *
     * @param array $options Options to be set for the cms node
     * @return \Aimeos\MShop\Cms\Item\Iface Cms item for chaining method calls
     */
    public function setConfig( array $options ) : \Aimeos\MShop\Common\Item\Iface
    {
        $this->node->config = $options;
        return $this;
    }

    /**
     * Returns the level of the item in the tree
     *
     * For internal use only!
     *
     * @return int Level of the item starting with "0" for the root node
     */
    public function getLevel() : int
    {
        return ( $this->node->__isset( 'level' ) ? $this->node->__get( 'level' ) : 0 );
    }

    /**
     * Adds a child node to this node.
     *
     * @param \Aimeos\MShop\Common\Item\Tree\Iface $item Child node to add
     * @return \Aimeos\MShop\Common\Item\Tree\Iface Tree item for chaining method calls
     */
    public function addChild( \Aimeos\MShop\Common\Item\Tree\Iface $item ) : \Aimeos\MShop\Common\Item\Tree\Iface
    {
        // don't set the modified flag as it's only for the values
        $this->children[] = $item;

        return $this;
    }

    /**
     * Removes a child node from this node.
     *
     * @param \Aimeos\MShop\Common\Item\Tree\Iface $item Child node to remove
     * @return \Aimeos\MShop\Common\Item\Tree\Iface Tree item for chaining method calls
     */
    public function deleteChild( \Aimeos\MShop\Common\Item\Tree\Iface $item ) : \Aimeos\MShop\Common\Item\Tree\Iface
    {
        foreach( $this->children as $idx => $child )
        {
            if( $child === $item )
            {
                $this->deletedItems[] = $item;
                unset( $this->children[$idx] );
            }
        }

        return $this;
    }

    /**
     * Returns a child of this node identified by its index.
     *
     * @param int $index Index of child node
     * @return \Aimeos\MShop\Cms\Item\Iface Selected node
     */
    public function getChild( int $index ) : \Aimeos\MShop\Common\Item\Tree\Iface
    {
        if( isset( $this->children[$index] ) ) {
            return $this->children[$index];
        }

        throw new \Aimeos\MShop\Cms\Exception( sprintf( 'Child node with index "%1$d" not available', $index ) );
    }

    /**
     * Returns all children of this node.
     *
     * @return \Aimeos\Map Numerically indexed list of children implementing \Aimeos\MShop\Cms\Item\Iface
     */
    public function getChildren() : \Aimeos\Map
    {
        return map( $this->children );
    }

    /**
     * Returns the deleted children.
     *
     * @return \Aimeos\Map List of removed children implementing \Aimeos\MShop\Cms\Item\Iface
     */
    public function getChildrenDeleted() : \Aimeos\Map
    {
        return map( $this->deletedItems );
    }

    /**
     * Tests if a node has children.
     *
     * @return bool True if node has children, false if not
     */
    public function hasChildren() : bool
    {
        if( count( $this->children ) > 0 ) {
            return true;
        }

        return $this->node->hasChildren();
    }

    /**
     * Returns the site ID of the item.
     *
     * @return string Site ID of the item
     */
    public function getSiteId() : string
    {
        return ( $this->node->__isset( 'siteid' ) ? (string) $this->node->__get( 'siteid' ) : '' );
    }

    /**
     * Returns the URL segment for the catalog item.
     *
     * @return string URL segment of the catalog item
     */
    public function getUrl() : string
    {
        return $this->node->__get( 'url' ) ?: \Aimeos\MW\Str::slug( $this->getLabel() );
    }

    /**
     * @param null|string $url
     * @return Iface
     */
    public function setUrl( ?string $url ) : Iface
    {
        $this->node->url = (string) $url;
        return $this;
    }

    /**
     * Returns the ID of the parent category
     *
     * For internal use only!
     *
     * @return string|null Unique ID of the parent category
     */
    public function getParentId() : ?string
    {
        return ( $this->node->__isset( 'parentid' ) ? $this->node->__get( 'parentid' ) : null );
    }


    /**
     * Returns the internal name of the item.
     *
     * @return string Name of the item
     */
    public function getLabel() : string
    {
        return $this->node->getLabel();
    }


    /**
     * Sets the new internal name of the item.
     *
     * @param string $name New name of the item
     * @return \Aimeos\MShop\Cms\Item\Iface Cms item for chaining method calls
     */
    public function setLabel( string $name ) : \Aimeos\MShop\Common\Item\Tree\Iface
    {
        $this->node->setLabel( $name );
        return $this;
    }

    /**
     * Returns the internal node.
     *
     * For internal use only!
     *
     * @return \Aimeos\MW\Tree\Node\Iface Internal node object
     */
    public function getNode() : \Aimeos\MW\Tree\Node\Iface
    {
        return $this->node;
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
     * Returns the unique ID of the node.
     *
     * @return string|null Unique ID of the node
     */
    public function getId() : ?string
    {
        return $this->node->getId();
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
     * Returns the config property of the cms.
     *
     * @return array Returns the config of the cms node
     */
    public function getConfig() : array
    {
        return ( $this->node->__isset( 'config' ) ? (array) $this->node->__get( 'config' ) : [] );
    }

    /**
     * Returns the code of the item.
     *
     * @return string Code of the item
     */
    public function getCode() : string
    {
        return $this->node->getCode();
    }


    /**
     * Sets the new code of the item.
     *
     * @param string $code New code of the item
     * @return \Aimeos\MShop\Cms\Item\Iface Cms item for chaining method calls
     */
    public function setCode( string $code ) : \Aimeos\MShop\Common\Item\Tree\Iface
    {
        $this->node->setCode( $this->checkCode( $code ) );
        return $this;
    }

    /**
     * Returns the URL target specific for that category
     *
     * @return string URL target specific for that category
     */
    public function getTarget() : string
    {
        return ( $this->node->__isset( 'target' ) ? $this->node->__get( 'target' ) : '' );
    }

    /**
     * @param null|string $value
     * @return Iface
     */
    public function setTarget( ?string $value ) : Iface
    {
        $this->node->target = (string) $value;
        return $this;
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
                case 'cms.code': $item = $item->setCode( $value ); break;
                case 'cms.target': $item = $item->setTarget( $value ); break;
                case 'cms.config': $item = $item->setConfig( (array) $value ); break;
                case 'cms.id': !$private ?: $item = $item->setId( $value ); break;

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
        $list = [
            'cms.url' => $this->getUrl(),
            'cms.code' => $this->getCode(),
            'cms.label' => $this->getLabel(),
            'cms.config' => $this->getConfig(),
            'cms.status' => $this->getStatus(),
            'cms.target' => $this->getTarget(),
            'cms.hasChildren' => $this->hasChildren(),
        ];

        if( $private === true )
        {
            $list['cms.id'] = $this->getId();
            $list['cms.level'] = $this->getLevel();
            $list['cms.siteid'] = $this->getSiteId();
            $list['cms.parentid'] = $this->getParentId();
            $list['cms.ctime'] = $this->getTimeCreated();
            $list['cms.mtime'] = $this->getTimeModified();
            $list['cms.editor'] = $this->getEditor();
        }

        return $list;
    }

    /**
     * Returns the node and its children as list
     *
     * @return \Aimeos\Map List of IDs as keys and items implementing \Aimeos\MShop\Cms\Item\Iface
     */
    public function toList() : \Aimeos\Map
    {
        $list = map( [$this->getId() => $this] );

        foreach( $this->getChildren() as $child ) {
            $list = $list->union( $child->toList() );
        }

        return $list;
    }
}
