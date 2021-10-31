<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
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

			$table->unique( ['siteid', 'url'], 'unq_mscms_sid_url' );
			$table->index( ['siteid', 'status'], 'unq_mscms_sid_status' );
			$table->index( ['siteid', 'label'], 'unq_mscms_sid_label' );
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

			$table->unique( ['siteid', 'domain', 'code'], 'unq_mscmslity_sid_dom_code' );
			$table->index( ['siteid', 'status', 'pos'], 'idx_mscmslity_sid_status_pos' );
			$table->index( ['siteid', 'label'], 'idx_mscmslity_sid_label' );
			$table->index( ['siteid', 'code'], 'idx_mscmslity_sid_code' );
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

			$table->unique( ['parentid', 'domain', 'siteid', 'type', 'refid'], 'unq_mscmsli_pid_dm_sid_ty_rid' );
			$table->index( ['key', 'siteid'], 'idx_mscmsli_key_sid' );
			$table->index( ['parentid'], 'fk_mscmsli_pid' );

			$table->foreign( 'parentid', 'mshop_cms', 'id', 'fk_mscmsli_pid' );
		},
	],
];
