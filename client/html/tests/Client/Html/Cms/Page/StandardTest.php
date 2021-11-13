<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\Client\Html\Cms\Page;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();
		$this->context->getLocale()->setLanguageId( 'en' );

		$this->object = new \Aimeos\Client\Html\Cms\Page\Standard( $this->context );
		$this->object->setView( $this->view );
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

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, ['path' => 'contact'] );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $view, $tags, $expire ) );
		$output = $this->object->header();

		$this->assertStringContainsString( '<title>Contact page | Aimeos</title>', $output );
		$this->assertEquals( null, $expire );
		$this->assertEquals( 2, count( $tags ) );
	}


	public function testHeaderException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Cms\Page\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'data' ) )
			->getMock();

		$mock->setView( $this->view );

		$mock->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \RuntimeException() ) );

		$mock->header();
	}


	public function testBody()
	{
		$tags = [];
		$expire = null;
		$view = $this->view;

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, ['path' => 'contact'] );
		$view->addHelper( 'param', $helper );

		$this->object->setView( $this->object->data( $view, $tags, $expire ) );
		$output = $this->object->body();

		$this->assertStringStartsWith( '<section class="aimeos cms-page', $output );
		$this->assertStringContainsString( '<h1>Hello!</h1>', $output );

		$this->assertEquals( null, $expire );
		$this->assertEquals( 2, count( $tags ) );
	}


	public function testBodyClientHtmlException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Cms\Page\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'data' ) )
			->getMock();

		$mock->setView( $this->view );

		$mock->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \Aimeos\Client\Html\Exception() ) );

		$mock->body();
	}


	public function testBodyControllerFrontendException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Cms\Page\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'data' ) )
			->getMock();

		$mock->setView( $this->view );

		$mock->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \Aimeos\Controller\Frontend\Exception() ) );

		$mock->body();
	}


	public function testBodyMShopException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Cms\Page\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'data' ) )
			->getMock();

		$mock->setView( $this->view );

		$mock->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \Aimeos\MShop\Exception() ) );

		$mock->body();
	}


	public function testBodyException()
	{
		$mock = $this->getMockBuilder( \Aimeos\Client\Html\Cms\Page\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( array( 'data' ) )
			->getMock();

		$mock->setView( $this->view );

		$mock->expects( $this->once() )->method( 'data' )
			->will( $this->throwException( new \RuntimeException() ) );

		$mock->body();
	}


	public function testGetSubClientInvalid()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testGetSubClientInvalidName()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( '$$$', '$$$' );
	}


	public function testInit()
	{
		$this->object->init();

		$this->assertEmpty( $this->view->get( 'pageErrorList' ) );
	}
}
