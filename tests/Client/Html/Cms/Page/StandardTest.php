<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
 */


namespace Aimeos\Client\Html\Cms\Page;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->context->locale()->setLanguageId( 'en' );
		$this->view = $this->context->view();

		$this->object = new \Aimeos\Client\Html\Cms\Page\Standard( $this->context );
		$this->object->setView( $this->context->view() );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testHeader()
	{
		$tags = [];
		$expire = null;
		$view = $this->view;

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $view, ['path' => 'contact'] );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $view, $tags, $expire ) );
		$output = $this->object->header();

		$this->assertStringContainsString( '<title>Contact page | Aimeos</title>', $output );
		$this->assertEquals( null, $expire );
		$this->assertEquals( 1, count( $tags ) );
	}


	public function testBody()
	{
		$tags = [];
		$expire = null;
		$view = $this->view;

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $view, ['path' => 'contact'] );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $view, $tags, $expire ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="aimeos cms-page', $output );
		$this->assertStringContainsString( '<h1>Hello!</h1>', $output );

		$this->assertEquals( null, $expire );
		$this->assertEquals( 1, count( $tags ) );
	}


	public function testGetSubClientInvalid()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubClient( 'unknown', 'unknown' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	public function testInit()
	{
		$this->object->init();

		$this->assertEmpty( $this->view->get( 'errors' ) );
	}
}
