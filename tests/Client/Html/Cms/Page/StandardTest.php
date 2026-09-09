<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2026
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


	public function testContentSanitizesLegacyHtml()
	{
		$method = new \ReflectionMethod( Standard::class, 'content' );

		foreach( [
			'<p onclick="alert(1)">Safe</p><script>alert(2)</script>',
			'{"html":"\u003cp onclick=alert(1)\u003eSafe\u003c/p\u003e"}',
		] as $content ) {
			$this->assertSame( '<p>Safe</p>', $method->invoke( $this->object, $content ) );
		}
	}


	public function testDataSanitizesMismatchedTextDomain()
	{
		$text = new \Aimeos\MShop\Text\Item\Standard( 'text.', [
			'text.type' => 'content',
			'text.domain' => 'product',
			'text.content' => '{"html":"\u003cp onclick=alert(1)\u003eSafe\u003c/p\u003e"}',
		] );
		$manager = \Aimeos\MShop::create( $this->context, 'cms' );
		$page = $manager->create()->addListItem( 'text', $manager->createListItem(), $text );
		$controller = $this->getMockBuilder( \Aimeos\Controller\Frontend\Cms\Standard::class )
			->setConstructorArgs( [$this->context] )->onlyMethods( ['uses', 'compare', 'search'] )->getMock();
		$controller->expects( $this->once() )->method( 'uses' )->willReturnSelf();
		$controller->expects( $this->once() )->method( 'compare' )->willReturnSelf();
		$controller->expects( $this->once() )->method( 'search' )->willReturn( map( [$page] ) );

		\Aimeos\Controller\Frontend::cache( true );
		\Aimeos\Controller\Frontend::inject( \Aimeos\Controller\Frontend\Cms\Standard::class, $controller );

		try
		{
			$view = $this->object->data( $this->view );
			$this->assertSame( 'product', $text->getDomain() );
			$this->assertSame( ['<div class="cms-content"><p>Safe</p></div>'], array_values( array_map( 'trim', $view->pageContent ) ) );
		}
		finally
		{
			\Aimeos\Controller\Frontend::cache( false );
		}
	}


	public function testContentRejectsInvalidJsonShapes()
	{
		$method = new \ReflectionMethod( Standard::class, 'content' );

		foreach( ['{"css":".safe{}"}', '"hello"', '[]', 'null', '{"html":[]}', '{"html":false}'] as $content ) {
			$this->assertSame( '', $method->invoke( $this->object, $content ) );
		}
	}


	public function testContentPreservesAllowedIframe()
	{
		$method = new \ReflectionMethod( Standard::class, 'content' );
		$content = '{"html":"<iframe src=\"https://www.youtube.com/embed/test\"></iframe>"}';
		$result = $method->invoke( $this->object, $content );

		$this->assertStringContainsString( '<iframe src="https://www.youtube.com/embed/test"', $result );
		$this->assertStringContainsString( 'sandbox=', $result );
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
