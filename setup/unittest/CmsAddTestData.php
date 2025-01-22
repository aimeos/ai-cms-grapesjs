<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2025
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds cms test data and all items from other domains.
 */
class CmsAddTestData extends BaseAddTestData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['MShopSetLocale'];
	}


	/**
	 * Adds cms test data.
	 */
	public function up()
	{
		$this->info( 'Adding cms test data', 'vv' );
		$this->context()->setEditor( 'ai-cms-grapesjs' );

		$path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'cms.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No file "%1$s" found for cms domain', $path ) );
		}

		$manager = $this->getManager( 'cms' );
		$manager->begin();

		foreach( $testdata['cms'] as $entry )
		{
			$item = $manager->create()->fromArray( $entry );
			$item = $this->addListData( $manager, $item, $entry );

			$manager->save( $item );
		}

		$manager->commit();
	}
}
