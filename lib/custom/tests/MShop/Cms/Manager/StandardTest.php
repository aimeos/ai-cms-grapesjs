<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */

namespace Aimeos\MShop\Cms\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelper::getContext()->getEditor();
		$this->object = new \Aimeos\MShop\Cms\Manager\Standard( \TestHelper::getContext() );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testCreateItem()
	{
		$cmsItem = $this->object->create();
		$this->assertInstanceOf( \Aimeos\MShop\Cms\Item\Iface::class, $cmsItem );
	}


	public function testCreateItemType()
	{
		$item = $this->object->create( ['cms.url' => '/test'] );
		$this->assertEquals( '/test', $item->getUrl() );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'cms', $result );
		$this->assertContains( 'cms/lists', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testSearchItems()
	{
		$item = $this->object->find( '/contact', ['text'] );
		$listItem = $item->getListItems( 'text', 'default' )->first( new \RuntimeException( 'No list item found' ) );

		$total = 0;
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'cms.id', null );
		$expr[] = $search->compare( '!=', 'cms.siteid', null );
		$expr[] = $search->compare( '==', 'cms.url', '/contact' );
		$expr[] = $search->compare( '==', 'cms.label', 'Contact' );
		$expr[] = $search->compare( '==', 'cms.status', 1 );
		$expr[] = $search->compare( '>=', 'cms.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'cms.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'cms.editor', $this->editor );

		$param = ['text', 'default', $listItem->getRefId()];
		$expr[] = $search->compare( '!=', $search->make( 'cms:has', $param ), null );

		$param = ['text', 'default'];
		$expr[] = $search->compare( '!=', $search->make( 'cms:has', $param ), null );

		$param = ['text'];
		$expr[] = $search->compare( '!=', $search->make( 'cms:has', $param ), null );

		$search->setConditions( $search->and( $expr ) );
		$result = $this->object->search( $search, [], $total )->toArray();
		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );
	}


	public function testSearchItemsAll()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'cms.editor', $this->editor ) );
		$this->assertEquals( 3, count( $this->object->search( $search )->toArray() ) );
	}


	public function testSearchItemsBase()
	{
		$total = 0;
		$search = $this->object->filter( true );
		$conditions = array(
			$search->compare( '==', 'cms.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->and( $conditions ) );
		$search->slice( 0, 1 );
		$results = $this->object->search( $search, [], $total )->toArray();
		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 2, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetItem()
	{
		$item = $this->object->find( '/contact' );

		$actual = $this->object->get( $item->getId() );
		$this->assertEquals( $item, $actual );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->object->find( '/contact' );

		$item->setId( null );
		$item->setUrl( '/contact-test' );
		$item->setLabel( 'Contact test' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setLabel( 'Contact test 2' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getUrl(), $itemSaved->getUrl() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getUrl(), $itemUpd->getUrl() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'lists' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'lists', 'Standard' ) );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'lists', 'unknown' );
	}
}
