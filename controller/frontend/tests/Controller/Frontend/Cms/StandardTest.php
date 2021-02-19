<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\Controller\Frontend\Cms;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperFrontend::getContext();
		$this->context->getLocale()->setLanguageId( 'en' );

		$this->object = new \Aimeos\Controller\Frontend\Cms\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testCompare()
	{
		$this->assertEquals( 1, count( $this->object->compare( '=~', 'cms.label', 'Con' )->search() ) );
	}


	public function testFind()
	{
		$item = $this->object->uses( ['text'] )->find( '/contact' );

		$this->assertInstanceOf( \Aimeos\MShop\Cms\Item\Iface::class, $item );
		$this->assertEquals( 3, count( $item->getRefItems( 'text' ) ) );
	}


	public function testFunction()
	{
		$str = $this->object->function( 'cms:has', ['domain', 'type', 'refid'] );
		$this->assertEquals( 'cms:has("domain","type","refid")', $str );
	}


	public function testGet()
	{
		$item = \Aimeos\MShop::create( $this->context, 'cms' )->find( '/contact' );
		$item = $this->object->uses( ['text'] )->get( $item->getId() );

		$this->assertInstanceOf( \Aimeos\MShop\Cms\Item\Iface::class, $item );
		$this->assertEquals( 3, count( $item->getRefItems( 'text' ) ) );
	}


	public function testHas()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'text' );
		$textId = $manager->search( $manager->filter()->add( ['text.domain' => 'cms'] ) )->firstKey();

		$this->assertEquals( 1, count( $this->object->has( 'text', 'default', $textId )->search() ) );
	}


	public function testParse()
	{
		$cond = ['&&' => [['==' => ['cms.status' => 1]], ['=~' => ['cms.label' => 'Con']]]];
		$this->assertEquals( 1, count( $this->object->parse( $cond )->search() ) );
	}


	public function testSearch()
	{
		$total = 0;
		$items = $this->object->uses( ['text'] )->compare( '=~', 'cms.url', '/con' )
			->sort( 'cms.url' )->search( $total );

		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 1, $total );
		$this->assertEquals( 3, count( $items->first()->getRefItems( 'text' ) ) );
	}


	public function testSlice()
	{
		$this->assertEquals( 1, count( $this->object->slice( 0, 1 )->search() ) );
	}


	public function testSort()
	{
		$this->assertGreaterThanOrEqual( 1, count( $this->object->sort()->search() ) );
	}


	public function testSortGeneric()
	{
		$this->assertGreaterThanOrEqual( 1, count( $this->object->sort( 'cms.label' )->search() ) );
	}


	public function testSortMultiple()
	{
		$this->assertGreaterThanOrEqual( 1, count( $this->object->sort( 'cms.label,-cms.id' )->search() ) );
	}


	public function testUses()
	{
		$this->assertSame( $this->object, $this->object->uses( ['text'] ) );
	}
}
