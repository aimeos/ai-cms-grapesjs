<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2025
 */


namespace Aimeos\Admin\JQAdm\Cms\Media\Property;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelper::view();
		$this->context = \TestHelper::context();

		$this->object = new \Aimeos\Admin\JQAdm\Cms\Media\Property\Standard( $this->context );
		$this->object = new \Aimeos\Admin\JQAdm\Common\Decorator\Page( $this->object, $this->context );
		$this->object->setAimeos( \TestHelper::getAimeos() );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->view, $this->context );
	}


	public function testCreate()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'cms' );

		$this->view->item = $manager->create();
		$result = $this->object->create();

		$this->assertStringContainsString( 'Media properties', $result );
		$this->assertEmpty( $this->view->get( 'errors' ) );
	}


	public function testCopy()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'cms' );

		$this->view->item = $manager->find( '/kontakt', ['media'] );
		$result = $this->object->copy();

		$this->assertEmpty( $this->view->get( 'errors' ) );
		$this->assertStringContainsString( 'Media properties', $result );
	}


	public function testDelete()
	{
		$result = $this->object->delete();

		$this->assertEmpty( $this->view->get( 'errors' ) );
		$this->assertEmpty( $result );
	}


	public function testGet()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'cms' );

		$this->view->item = $manager->find( '/kontakt', ['media'] );
		$result = $this->object->get();

		$this->assertEmpty( $this->view->get( 'errors' ) );
		$this->assertStringContainsString( 'Media properties', $result );
	}


	public function testSave()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'cms' );
		$typeManager = \Aimeos\MShop::create( $this->context, 'media/property/type' );

		$item = $manager->find( '/kontakt', ['media'] );
		$item->setUrl( '/jqadm-test-media-property' );
		$item->setId( null );

		$this->view->item = $manager->save( $item );

		$param = array(
			'site' => 'unittest',
			'media' => array(
				0 => array(
					'property' => array(
						0 => array(
							'media.property.id' => '',
							'media.property.type' => 'size',
						),
					),
				),
			),
		);

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );


		$result = $this->object->save();


		$manager->delete( $this->view->item->getId() );

		$this->assertEmpty( $this->view->get( 'errors' ) );
		$this->assertEmpty( $result );

		$mediaItems = $this->view->item->getRefItems( 'media' )->toArray();
		$this->assertEquals( 1, count( $mediaItems ) );
		$this->assertEquals( 1, count( reset( $mediaItems )->getPropertyItems() ) );
	}


	public function testSaveException()
	{
		$templates = \TestHelper::getAimeos()->getTemplatePaths( 'admin/jqadm/templates' );

		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Cms\Media\Property\Standard::class )
			->setConstructorArgs( array( $this->context, $templates ) )
			->onlyMethods( array( 'fromArray' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \RuntimeException() ) );

		$this->view = \TestHelper::view();
		$this->view->item = \Aimeos\MShop::create( $this->context, 'cms' )->create();

		$object->setView( $this->view );

		$this->expectException( \RuntimeException::class );
		$object->save();
	}


	public function testSearch()
	{
		$this->assertEmpty( $this->object->search() );
	}


	public function testGetSubClient()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubClient( 'unknown' );
	}
}
