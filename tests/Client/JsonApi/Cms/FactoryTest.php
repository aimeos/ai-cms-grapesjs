<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2022
 */


namespace Aimeos\Client\JsonApi\Cms;


class FactoryTest extends \PHPUnit\Framework\TestCase
{
	public function testCreateClient()
	{
		$context = \TestHelper::context();

		$client = \Aimeos\Client\JsonApi\Cms\Factory::create( $context, 'cms' );
		$this->assertInstanceOf( \Aimeos\Client\JsonApi\Iface::class, $client );
	}


	public function testCreateClientEmpty()
	{
		$context = \TestHelper::context();

		$this->expectException( \Aimeos\Client\JsonApi\Exception::class );
		\Aimeos\Client\JsonApi\Cms\Factory::create( $context, '' );
	}


	public function testCreateClientInvalidPath()
	{
		$context = \TestHelper::context();

		$this->expectException( \Aimeos\Client\JsonApi\Exception::class );
		\Aimeos\Client\JsonApi\Cms\Factory::create( $context, '%^' );
	}


	public function testCreateClientInvalidName()
	{
		$context = \TestHelper::context();

		$this->expectException( \Aimeos\Client\JsonApi\Exception::class );
		\Aimeos\Client\JsonApi\Cms\Factory::create( $context, 'cms', '%^' );
	}
}
