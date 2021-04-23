<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\MW\Setup\Task;


/**
 * Creates all CMS tables.
 */
class TablesCreateCms extends \Aimeos\MW\Setup\Task\TablesCreateMShop
{
	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return string[] List of task names
	 */
	public function getPostDependencies() : array
	{
		return ['MShopAddLocaleLangCurData'];
	}


	/**
	 * Creates the CMS tables
	 */
	public function migrate()
	{
		$this->msg( 'Creating CMS tables', 0, '' );

		$ds = DIRECTORY_SEPARATOR;
		$this->setupSchema( ['db-cms' => 'default' . $ds . 'schema' . $ds . 'cms.php'] );
	}
}
