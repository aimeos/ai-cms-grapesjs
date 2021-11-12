<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\Admin\JQAdm\Cms\Content;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperJqadm::view();
		$this->context = \TestHelperJqadm::getContext();

		$langManager = \Aimeos\MShop::create( $this->context, 'locale/language' );

		$this->view->pageLanguages = $langManager->search( $langManager->filter() );
		$this->view->item = \Aimeos\MShop::create( $this->context, 'cms' )->create();

		$this->object = new \Aimeos\Admin\JQAdm\Cms\Content\Standard( $this->context );
		$this->object = new \Aimeos\Admin\JQAdm\Common\Decorator\Page( $this->object, $this->context );
		$this->object->setAimeos( \TestHelperJqadm::getAimeos() );
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

		$this->assertStringContainsString( 'item-content', $result );
		$this->assertEmpty( $this->view->get( 'errors' ) );
	}


	public function testCopy()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'cms' );

		$this->view->item = $manager->find( '/contact', ['text'] );
		$result = $this->object->copy();

		$this->assertEmpty( $this->view->get( 'errors' ) );
		$this->assertStringContainsString( 'Hello', $result );
	}


	public function testDelete()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'cms' );

		$this->view->item = $manager->create();
		$result = $this->object->delete();

		$this->assertEmpty( $this->view->get( 'errors' ) );
		$this->assertEmpty( $result );
	}


	public function testGet()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'cms' );

		$this->view->item = $manager->find( '/contact', ['text'] );
		$result = $this->object->get();

		$this->assertEmpty( $this->view->get( 'errors' ) );
		$this->assertStringContainsString( 'Hello', $result );
	}


	public function testSave()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'cms' );
		$item = $manager->create();

		$param = array(
			'site' => 'unittest',
			'content' => array(
				array(
					'text.id' => '',
					'text.languageid' => 'en',
					'text.content' => '<h1>Hello!</h1>',
					'cms.lists.type' => 'default',
					'cms.lists.position' => 0,
				),
				array(
					'text.id' => '',
					'text.languageid' => 'de',
					'text.content' => '<h1>Hallo!</h1>',
					'cms.lists.type' => 'default',
					'cms.lists.position' => 1,
				),
			),
		);

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );
		$this->view->item = $item;

		$result = $this->object->save();
		$listItems = $item->getListItems();

		$this->assertEmpty( $this->view->get( 'errors' ) );
		$this->assertEmpty( $result );
		$this->assertEquals( 2, count( $listItems ) );

		$this->assertEquals( [0, 1], $listItems->getPosition()->values()->toArray() );
		$this->assertEquals( ['text', 'text'], $listItems->getDomain()->values()->toArray() );

		$refItems = $listItems->getRefItem();

		$this->assertEquals( ['en', 'de'], $refItems->getLanguageId()->values()->toArray() );
		$this->assertEquals( ['<h1>Hello!</h1>', '<h1>Hallo!</h1>'], $refItems->getContent()->values()->toArray() );
	}


	public function testSaveException()
	{
		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Cms\Content\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'fromArray' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \RuntimeException() ) );

		$this->view = \TestHelperJqadm::view();
		$this->view->item = \Aimeos\MShop::create( $this->context, 'cms' )->create();

		$object->setView( $this->view );

		$this->expectException( \RuntimeException::class );
		$object->save();
	}


	public function testSaveMShopException()
	{
		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Cms\Content\Standard::class )
			->setConstructorArgs( array( $this->context, \TestHelperJqadm::getTemplatePaths() ) )
			->setMethods( array( 'fromArray' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$this->view = \TestHelperJqadm::view();
		$this->view->item = \Aimeos\MShop::create( $this->context, 'cms' )->create();

		$object->setView( $this->view );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$object->save();
	}


	public function testSearch()
	{
		$this->assertEmpty( $this->object->search() );
	}


	public function testGetSubClient()
	{
		$this->expectException( \Aimeos\Admin\JQAdm\Exception::class );
		$this->object->getSubClient( 'unknown' );
	}
}
