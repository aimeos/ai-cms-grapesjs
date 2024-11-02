<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024
 */


namespace Aimeos\Client\Html\Cms\Page\Decorator;


class RecaptchaTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->view = $this->context->view();

		$this->object = new \Aimeos\Client\Html\Cms\Page\Standard( $this->context );
		$this->object = new \Aimeos\Client\Html\Cms\Page\Decorator\Recaptcha( $this->object, $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\Controller\Frontend::cache( true );

		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$output = $this->object->body();
		$this->assertEquals( '', $output );

		$this->context->config()->set( 'resource/recaptcha/sitekey', 'abcd' );
		$output = $this->object->body();
		$this->assertStringContainsString( 'abcd', $output );
	}


	public function testInit()
	{
		$this->object->init();
	}


	public function testInitTokenMissing()
	{
		$this->context->config()->set( 'resource/recaptcha/secretkey', 'abcd' );

		$helper = new \Aimeos\Base\View\Helper\Request\Standard( $this->view, $this->view->request()->withMethod( 'POST' ) );
		$this->view->addHelper( 'request', $helper );

		$this->expectException( \Aimeos\Client\Html\Exception::class );
		$this->object->init();
	}


	public function testInitTokenInvalid()
	{
		$this->context->config()->set( 'resource/recaptcha/secretkey', 'abcd' );

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, ['g-recaptcha-response' => 'test'] );
		$this->view->addHelper( 'param', $helper );

		$helper = new \Aimeos\Base\View\Helper\Request\Standard( $this->view, $this->view->request()->withMethod( 'POST' ) );
		$this->view->addHelper( 'request', $helper );

		$this->expectException( \Aimeos\Client\Html\Exception::class );
		$this->object->init();
	}
}
