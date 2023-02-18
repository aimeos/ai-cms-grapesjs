<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
 */


namespace Aimeos\Admin\JQAdm\Type\Cms\Lists;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelper::view();
		$this->context = \TestHelper::context();

		$this->object = new \Aimeos\Admin\JQAdm\Type\Cms\Lists\Standard( $this->context );
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
		$result = $this->object->create();

		$this->assertStringContainsString( 'item-cms-lists-type', $result );
		$this->assertEmpty( $this->view->get( 'errors' ) );
	}


	public function testCreateException()
	{
		$templates = \TestHelper::getAimeos()->getTemplatePaths( 'admin/jqadm/templates' );

		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Type\Cms\Lists\Standard::class )
			->setConstructorArgs( array( $this->context, $templates ) )
			->onlyMethods( array( 'getSubClients' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->getViewNoRender() );

		$object->create();
	}


	public function testCopy()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'cms/lists/type' );

		$param = ['type' => 'unittest', 'id' => $manager->find( 'default', [], 'text' )->getId()];
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$result = $this->object->copy();

		$this->assertStringContainsString( 'default', $result );
	}


	public function testCopyException()
	{
		$templates = \TestHelper::getAimeos()->getTemplatePaths( 'admin/jqadm/templates' );

		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Type\Cms\Lists\Standard::class )
			->setConstructorArgs( array( $this->context, $templates ) )
			->onlyMethods( array( 'getSubClients' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->getViewNoRender() );

		$object->copy();
	}


	public function testDelete()
	{
		$this->assertNull( $this->getClientMock( ['redirect'], false )->delete() );
	}


	public function testDeleteException()
	{
		$templates = \TestHelper::getAimeos()->getTemplatePaths( 'admin/jqadm/templates' );

		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Type\Cms\Lists\Standard::class )
			->setConstructorArgs( array( $this->context, $templates ) )
			->onlyMethods( array( 'getSubClients', 'search' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->getViewNoRender() );

		$object->delete();
	}


	public function testGet()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'cms/lists/type' );

		$param = ['type' => 'unittest', 'id' => $manager->find( 'default', [], 'text' )->getId()];
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$result = $this->object->get();

		$this->assertStringContainsString( 'default', $result );
	}


	public function testGetException()
	{
		$templates = \TestHelper::getAimeos()->getTemplatePaths( 'admin/jqadm/templates' );

		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Type\Cms\Lists\Standard::class )
			->setConstructorArgs( array( $this->context, $templates ) )
			->onlyMethods( array( 'getSubClients' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getSubClients' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->getViewNoRender() );

		$object->get();
	}


	public function testSave()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'cms/lists/type' );

		$param = array(
			'type' => 'unittest',
			'item' => array(
				'cms.lists.type.id' => '',
				'cms.lists.type.status' => '1',
				'cms.lists.type.domain' => 'text',
				'cms.lists.type.code' => 'jqadm@test',
				'cms.lists.type.label' => 'jqadm test',
			),
		);

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$result = $this->object->save();

		$manager->delete( $manager->find( 'jqadm@test', [], 'text' )->getId() );

		$this->assertEmpty( $this->view->get( 'errors' ) );
		$this->assertNull( $result );
	}


	public function testSaveException()
	{
		$templates = \TestHelper::getAimeos()->getTemplatePaths( 'admin/jqadm/templates' );

		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Type\Cms\Lists\Standard::class )
			->setConstructorArgs( array( $this->context, $templates ) )
			->onlyMethods( array( 'fromArray' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'fromArray' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->getViewNoRender() );

		$object->save();
	}


	public function testSearch()
	{
		$param = array(
			'type' => 'unittest', 'lang' => 'de',
			'filter' => array(
				'key' => array( 0 => 'cms.lists.type.code' ),
				'op' => array( 0 => '==' ),
				'val' => array( 0 => 'default' ),
			),
			'sort' => array( '-cms.lists.type.id' ),
		);
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $param );
		$this->view->addHelper( 'param', $helper );

		$result = $this->object->search();

		$this->assertStringContainsString( '>default<', $result );
	}


	public function testSearchException()
	{
		$templates = \TestHelper::getAimeos()->getTemplatePaths( 'admin/jqadm/templates' );

		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Type\Cms\Lists\Standard::class )
			->setConstructorArgs( array( $this->context, $templates ) )
			->onlyMethods( array( 'initCriteria' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'initCriteria' )
			->will( $this->throwException( new \RuntimeException() ) );

		$object->setView( $this->getViewNoRender() );

		$object->search();
	}


	public function testGetSubClientInvalid()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubClient( '$unknown$' );
	}


	public function testGetSubClientUnknown()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubClient( 'unknown' );
	}


	public function getClientMock( $methods, $real = true )
	{
		$templates = \TestHelper::getAimeos()->getTemplatePaths( 'admin/jqadm/templates' );

		$object = $this->getMockBuilder( \Aimeos\Admin\JQAdm\Type\Cms\Lists\Standard::class )
			->setConstructorArgs( array( $this->context, $templates ) )
			->onlyMethods( (array) $methods )
			->getMock();

		$object->setAimeos( \TestHelper::getAimeos() );
		$object->setView( $this->getViewNoRender( $real ) );

		return $object;
	}


	protected function getViewNoRender( $real = true )
	{
		$view = $this->getMockBuilder( \Aimeos\Base\View\Standard::class )
			->setConstructorArgs( array( [] ) )
			->onlyMethods( array( 'render' ) )
			->getMock();

		$manager = \Aimeos\MShop::create( $this->context, 'cms/lists/type' );

		$param = ['site' => 'unittest', 'id' => $real ? $manager->find( 'default', [], 'text' )->getId() : -1];
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$helper = new \Aimeos\Base\View\Helper\Config\Standard( $view, $this->context->config() );
		$view->addHelper( 'config', $helper );

		$helper = new \Aimeos\Base\View\Helper\Access\Standard( $view, [] );
		$view->addHelper( 'access', $helper );

		return $view;
	}

}
