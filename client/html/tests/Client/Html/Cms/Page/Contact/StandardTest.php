<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\Client\Html\Cms\Page\Contact;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelperHtml::getContext();

		$this->object = new \Aimeos\Client\Html\Cms\Page\Contact\Standard( $this->context );
		$this->object->setView( \TestHelperHtml::view() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testBody()
	{
		$this->object->setView( $this->object->data( $this->object->view() ) );
		$output = $this->object->body();
		$this->assertEquals( '', $output );
	}


	public function testGetSubClient()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testInit()
	{
		$this->context->getConfig()->set( 'resource/email/from-address', 'rcpt@localhost' );

		$view = $this->object->view();
		$param = [
			'contact' => [
				'name' => 'test',
				'email' => 'test@localhost',
				'message' => 'Test msg',
				'url' => ''
			]
		];

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->init();

		$this->assertEquals( 1, count( $view->pageErrorList ) );
	}


	public function testInitHoneypot()
	{
		$this->context->getConfig()->set( 'resource/email/from-address', 'rcpt@localhost' );

		$view = $this->object->view();
		$param = [
			'contact' => [
				'name' => 'test',
				'email' => 'test@localhost',
				'message' => 'Test msg',
				'url' => 'http://localhost'
			]
		];

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->init();

		$this->assertEquals( 0, count( $view->get( 'pageErrorList', [] ) ) );
	}


	public function testInitError()
	{
		$view = $this->object->view();
		$param = [
			'contact' => [
				'name' => 'test',
				'email' => 'test@localhost',
				'message' => 'Test msg',
				'url' => ''
			]
		];

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object->init();

		$this->assertEquals( 1, count( $view->pageErrorList ) );
	}
}
