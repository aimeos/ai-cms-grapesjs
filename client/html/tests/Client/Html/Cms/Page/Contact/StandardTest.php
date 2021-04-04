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
		$this->object->setView( \TestHelperHtml::getView() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetBody()
	{
		$this->object->setView( $this->object->addData( $this->object->getView() ) );
		$output = $this->object->getBody();
		$this->assertEquals( '', $output );
	}


	public function testGetSubClient()
	{
		$this->expectException( '\\Aimeos\\Client\\Html\\Exception' );
		$this->object->getSubClient( 'invalid', 'invalid' );
	}


	public function testProcess()
	{
		$this->context->getConfig()->set( 'resource/email/from-address', 'rcpt@localhost' );

		$view = $this->object->getView();
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

		$this->object->process();

		$this->assertEquals( 1, count( $view->pageErrorList ) );
	}


	public function testProcessHoneypot()
	{
		$this->context->getConfig()->set( 'resource/email/from-address', 'rcpt@localhost' );

		$view = $this->object->getView();
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

		$this->object->process();

		$this->assertEquals( 0, count( $view->get( 'pageErrorList', [] ) ) );
	}


	public function testProcessError()
	{
		$view = $this->object->getView();
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

		$this->object->process();

		$this->assertEquals( 1, count( $view->pageErrorList ) );
	}
}
