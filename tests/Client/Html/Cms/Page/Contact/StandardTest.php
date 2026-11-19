<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2024
 */


namespace Aimeos\Client\Html\Cms\Page\Contact;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->view = $this->context->view();

		$this->object = new \Aimeos\Client\Html\Cms\Page\Contact\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testBody()
	{
		$this->object->setView( $this->object->data( $this->view ) );
		$output = $this->object->body();
		$this->assertEquals( '', $output );
	}


	public function testDataContact()
	{
		$this->view->pageContent = ['<p>Text</p><contact></contact>'];

		$content = $this->object->data( $this->view )->pageContent[0];
		$output = $this->object->modify( $content, '' );

		$this->assertStringContainsString( '<p>Text</p><form class="contact-form" method="POST">', $content );
		$this->assertStringContainsString( '<!-- cms.page.contact.csrf -->', $content );
		$this->assertStringNotContainsString( '<contact', $content );
		$this->assertEquals( 1, substr_count( $output, 'value="' . $this->view->csrf()->value() . '"' ) );
	}


	public function testDataNested()
	{
		$this->view->pageContent = ['<form method="POST" action="/admin/default/jqadm/save/settings"><contact></contact><button>Go</button></form>'];

		$content = $this->object->data( $this->view )->pageContent[0];
		$output = $this->object->modify( $content, '' );

		$this->assertStringNotContainsString( 'contact', $output );
		$this->assertStringNotContainsString( $this->view->csrf()->value(), $output );
	}


	public function testDataPlaceholder()
	{
		$this->view->pageContent = ['<form method="POST" action="/admin"><input name="%csrf.name%" value="%csrf.value%"></form>'];

		$content = $this->object->data( $this->view )->pageContent[0];
		$output = $this->object->modify( $content, '' );

		$this->assertEquals( $this->view->pageContent[0], $output );
		$this->assertStringNotContainsString( $this->view->csrf()->value(), $output );
	}


	public function testInit()
	{
		$this->context->config()->set( 'resource/email/from-email', 'rcpt@localhost' );

		$view = $this->view;
		$param = [
			'contact' => [
				'name' => 'test',
				'email' => 'test@localhost',
				'message' => 'Test msg',
				'url' => ''
			]
		];

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->init();

		$this->assertEquals( 1, count( $view->get( 'infos', [] ) ) );
		$this->assertEquals( 0, count( $view->get( 'errors', [] ) ) );
	}


	public function testInitHoneypot()
	{
		$view = $this->view;
		$param = [
			'contact' => [
				'name' => 'test',
				'email' => 'test@localhost',
				'message' => 'Test msg',
				'url' => 'http://localhost'
			]
		];

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->init();

		$this->assertEquals( 0, count( $view->get( 'infos', [] ) ) );
		$this->assertEquals( 0, count( $view->get( 'errors', [] ) ) );
	}


	public function testInitError()
	{
		$this->context->config()->set( 'resource/email/from-email', '' );

		$view = $this->view;
		$param = [
			'contact' => [
				'name' => 'test',
				'email' => 'test@localhost',
				'message' => 'Test msg',
				'url' => ''
			]
		];

		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->init();

		$this->assertEquals( 1, count( $view->get( 'errors', [] ) ) );
		$this->assertEquals( 0, count( $view->get( 'infos', [] ) ) );
	}
}
