<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2022
 */


namespace Aimeos\Upscheme\Task;


/**
 * Adds CMS records to tables.
 */
class MShopAddTypeDataCms extends MShopAddTypeData
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function after() : array
	{
		return ['TablesCreateCms', 'MShopSetLocale', 'MShopAddTypeData'];
	}


	/**
	 * Executes the task for adding CMS records to tables.
	 */
	public function up()
	{
		$context = $this->context();
		$editor = $context->editor();
		$sitecode = $this->context()->locale()->getSiteItem()->getCode();

		$this->info( sprintf( 'Adding CMS type data for site "%1$s"', $sitecode ), 'v' );


		$ds = DIRECTORY_SEPARATOR;
		$filename = __DIR__ . $ds . 'default' . $ds . 'data' . $ds . 'type.php';

		if( ( $testdata = include( $filename ) ) == false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'No type file found in "%1$s"', $filename ) );
		}

		$context->setEditor( 'ai-cms-grapesjs:lib/custom' );

		$this->processFile( $testdata );

		$context->setEditor( $editor );
	}
}
