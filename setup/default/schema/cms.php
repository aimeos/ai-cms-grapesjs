<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2023
 */


return [
	'table' => [
		'mshop_cms' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id( 'id' )->primary( 'pk_mscms_id' );
			$table->string( 'siteid' );
			$table->string( 'url' );
			$table->string( 'label' );
			$table->smallint( 'status' );
			$table->meta();

			$table->unique( ['url', 'siteid'], 'unq_mscms_url_sid' );
			$table->index( ['label', 'siteid'], 'unq_mscms_label_sid' );
			$table->index( ['siteid', 'status'], 'unq_mscms_sid_status' );
		},

		'mshop_cms_list_type' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id( 'id' )->primary( 'pk_mscmslity_id' );
			$table->string( 'siteid' );
			$table->string( 'domain', 32 );
			$table->code();
			$table->string( 'label' );
			$table->int( 'pos' )->default( 0 );
			$table->smallint( 'status' );
			$table->meta();

			$table->unique( ['domain', 'code', 'siteid'], 'unq_mscmslity_dom_code_sid' );
			$table->index( ['status', 'siteid', 'pos'], 'idx_mscmslity_status_sid_pos' );
			$table->index( ['label', 'siteid'], 'idx_mscmslity_label_sid' );
			$table->index( ['code', 'siteid'], 'idx_mscmslity_code_sid' );
		},

		'mshop_cms_list' => function( \Aimeos\Upscheme\Schema\Table $table ) {

			$table->engine = 'InnoDB';

			$table->id( 'id' )->primary( 'pk_mscmsli_id' );
			$table->int( 'parentid' );
			$table->string( 'siteid' );
			$table->string( 'key', 134 )->default( '' );
			$table->type();
			$table->string( 'domain', 32 );
			$table->refid();
			$table->startend();
			$table->text( 'config' );
			$table->int( 'pos' );
			$table->smallint( 'status' );
			$table->meta();

			$table->unique( ['parentid', 'domain', 'type', 'refid', 'siteid'], 'unq_mscmsli_pid_dm_ty_rid_sid' );
			$table->index( ['key', 'siteid'], 'idx_mscmsli_key_sid' );
			$table->index( ['parentid'], 'fk_mscmsli_pid' );

			$table->foreign( 'parentid', 'mshop_cms', 'id', 'fk_mscmsli_pid' );
		},
	],
];
