<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2023
 */


namespace Aimeos\Upscheme\Task;


class CmsRemoveIndexes extends Base
{
	public function after() : array
	{
		return ['Cms'];
	}


	public function up()
	{
		$this->info( 'Remove cms indexes with siteid column first', 'vv' );

		$this->db( 'db-cms' )
			->dropIndex( 'mshop_cms', 'unq_mscms_sid_url' )
			->dropIndex( 'mshop_cms', 'unq_mscms_sid_label' )
			->dropIndex( 'mshop_cms_list', 'unq_mscmsli_pid_dm_sid_ty_rid' )
			->dropIndex( 'mshop_cms_list_type', 'unq_mscmslity_sid_dom_code' )
			->dropIndex( 'mshop_cms_list_type', 'idx_mscmslity_sid_status_pos' )
			->dropIndex( 'mshop_cms_list_type', 'idx_mscmslity_sid_label' )
			->dropIndex( 'mshop_cms_list_type', 'idx_mscmslity_sid_code' );
	}
}
