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
	private $view;


	protected function setUp() : void
	{
		$this->view = \TestHelperHtml::view();
		$this->context = \TestHelperHtml::getContext();

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


	public function testGetSubClient()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testInit()
	{
		$this->context->getConfig()->set( 'resource/email/from-address', 'rcpt@localhost' );

		$view = $this->view;
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

		$view = $this->view;
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
		$view = $this->view;
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
