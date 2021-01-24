<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\MShop\Cms\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'cms.id' => 10,
			'cms.siteid' => 99,
			'cms.url' => '/test',
			'cms.label' => 'unittest label',
			'cms.status' => 2,
			'cms.mtime' => '2011-01-01 00:00:02',
			'cms.ctime' => '2011-01-01 00:00:01',
			'cms.editor' => 'unitTestUser',
		);

		$this->object = new \Aimeos\MShop\Cms\Item\Standard( $this->values );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetId()
	{
		$this->assertEquals( '10', $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Cms\Item\Iface::class, $return );
		$this->assertNull( $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}


	public function testGetUrl()
	{
		$this->assertEquals( '/test', $this->object->getUrl() );
	}


	public function testSetUrl()
	{
		$return = $this->object->setUrl( '/test2' );

		$this->assertInstanceOf( \Aimeos\MShop\Cms\Item\Iface::class, $return );
		$this->assertEquals( '/test2', $this->object->getUrl() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetUrlTransform()
	{
		$return = $this->object->setUrl( '/Äpfel/Birnen' );

		$this->assertInstanceOf( \Aimeos\MShop\Cms\Item\Iface::class, $return );
		$this->assertEquals( '/Apfel/Birnen', $this->object->getUrl() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'unittest label', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'unittest set label' );

		$this->assertInstanceOf( \Aimeos\MShop\Cms\Item\Iface::class, $return );
		$this->assertEquals( 'unittest set label', $this->object->getLabel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 2, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$return = $this->object->setStatus( 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Cms\Item\Iface::class, $return );
		$this->assertEquals( 0, $this->object->getStatus() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTimeModified()
	{
		$this->assertEquals( '2011-01-01 00:00:02', $this->object->getTimeModified() );
	}


	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->object->getTimeCreated() );
	}


	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->object->getEditor() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'cms', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Cms\Item\Standard();

		$list = $entries = array(
			'cms.id' => 1,
			'cms.url' => '/test',
			'cms.label' => 'test item',
			'cms.status' => 0,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( '', $item->getSiteId() );
		$this->assertEquals( $list['cms.id'], $item->getId() );
		$this->assertEquals( $list['cms.url'], $item->getUrl() );
		$this->assertEquals( $list['cms.label'], $item->getLabel() );
		$this->assertEquals( $list['cms.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$data = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $data ) );

		$this->assertEquals( $this->object->getId(), $data['cms.id'] );
		$this->assertEquals( $this->object->getSiteId(), $data['cms.siteid'] );
		$this->assertEquals( $this->object->getUrl(), $data['cms.url'] );
		$this->assertEquals( $this->object->getLabel(), $data['cms.label'] );
		$this->assertEquals( $this->object->getStatus(), $data['cms.status'] );
		$this->assertEquals( $this->object->getTimeCreated(), $data['cms.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $data['cms.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $data['cms.editor'] );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setAvailable( false );
		$this->assertFalse( $this->object->isAvailable() );
	}


	public function testIsAvailableOnStatus()
	{
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setStatus( 0 );
		$this->assertFalse( $this->object->isAvailable() );
		$this->object->setStatus( -1 );
		$this->assertFalse( $this->object->isAvailable() );
	}
}
