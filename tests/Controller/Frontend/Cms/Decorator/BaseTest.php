<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2024
 */


namespace Aimeos\Controller\Frontend\Cms\Decorator;


class Example extends Base
{
}


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $stub;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();

		$this->stub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Cms\Standard::class )
			->disableOriginalConstructor()
			->getMock();

		$this->object = new \Aimeos\Controller\Frontend\Cms\Decorator\Example( $this->stub, $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->context, $this->object, $this->stub );
	}


	public function testCall()
	{
		$stub = $this->getMockBuilder( \Aimeos\Controller\Frontend\Cms\Standard::class )
			->disableOriginalConstructor()
			->onlyMethods( ['__call'] )
			->getMock();

		$object = new \Aimeos\Controller\Frontend\Cms\Decorator\Example( $stub, $this->context );

		$stub->expects( $this->once() )->method( '__call' )->willReturn( true );

		$this->assertTrue( $object->invalid() );
	}


	public function testCompare()
	{
		$this->assertSame( $this->object, $this->object->compare( '==', 'cms.status', 1 ) );
	}


	public function testFind()
	{
		$item = \Aimeos\MShop::create( $this->context, 'cms' )->create();
		$expected = \Aimeos\MShop\Cms\Item\Iface::class;

		$this->stub->expects( $this->once() )->method( 'find' )->willReturn( $item );

		$this->assertInstanceOf( $expected, $this->object->find( 'test' ) );
	}


	public function testFunction()
	{
		$this->stub->expects( $this->once() )->method( 'function' )
			->willReturn( 'cms:has("domain","type","refid")' );

		$str = $this->object->function( 'cms:has', ['domain', 'type', 'refid'] );
		$this->assertEquals( 'cms:has("domain","type","refid")', $str );
	}


	public function testGet()
	{
		$item = \Aimeos\MShop::create( $this->context, 'cms' )->create();
		$expected = \Aimeos\MShop\Cms\Item\Iface::class;

		$this->stub->expects( $this->once() )->method( 'get' )->willReturn( $item );

		$this->assertInstanceOf( $expected, $this->object->get( 1 ) );
	}


	public function testHas()
	{
		$this->assertSame( $this->object, $this->object->has( 'product', 'default', -1 ) );
	}


	public function testParse()
	{
		$this->assertSame( $this->object, $this->object->parse( [] ) );
	}


	public function testSearch()
	{
		$item = \Aimeos\MShop::create( $this->context, 'cms' )->create();

		$this->stub->expects( $this->once() )->method( 'search' )->willReturn( map( [$item] ) );

		$this->assertEquals( [$item], $this->object->search()->toArray() );
	}


	public function testSlice()
	{
		$this->assertSame( $this->object, $this->object->slice( 0, 100 ) );
	}


	public function testSort()
	{
		$this->assertSame( $this->object, $this->object->sort( 'cms.label' ) );
	}


	public function testUses()
	{
		$this->assertSame( $this->object, $this->object->uses( ['text'] ) );
	}


	public function testGetController()
	{
		$result = $this->access( 'getController' )->invokeArgs( $this->object, [] );

		$this->assertSame( $this->stub, $result );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( \Aimeos\Controller\Frontend\Cms\Decorator\Base::class );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}
}
