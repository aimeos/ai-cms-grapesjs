<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2025
 */


namespace Aimeos\Client\Html\Cms\Page\Cataloglist;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $view;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->view = $this->context->view();

		$this->object = new \Aimeos\Client\Html\Cms\Page\Cataloglist\Standard( $this->context );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->view );
	}


	public function testData()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'catalog' );
		$catId1 = $manager->find( 'cafe' )->getId();
		$catId2 = $manager->find( 'new' )->getId();

		$view = $this->view;
		$view->pageCmsItem = \Aimeos\MShop::create( $this->context, 'cms' )->find( '/catlist', ['text'] );

		$textItems = $view->pageCmsItem->getRefItems( 'text', 'content' )->map( function( $item ) {
			$data = ( $json = json_decode( $item->getContent(), true ) ) ? $json['html'] : $item->getContent();
			return '<div class="cms-content>' . $data . '</div>';
		} )->all();

		$this->assertEquals( 1, count( $textItems ) );

		foreach( $textItems as $textItem ) {
			$textItem = str_replace( ['_cat1_', '_cat2_'], [$catId1, $catId2], $textItem );
		}

		$view->pageContent = $textItems;

		$view = $this->object->data( $view );

		foreach( $view->pageContent as $text ) {
			$this->assertStringContainsString( '<div class="catalog-list', $text );
		}
	}


	public function testModify()
	{
		$output = 'BEFORE<!-- catalog.lists.items.csrf -->CSRF<!-- catalog.lists.items.csrf -->AFTER';

		$output = $this->object->modify( $output, 1 );

		$this->assertStringContainsString( '<input class="csrf-token" type="hidden" name="_csrf_token" value="_csrf_value"', $output );
	}
}
