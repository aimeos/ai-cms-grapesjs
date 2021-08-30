<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


return [
	'table' => [
		'mshop_cms' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_cms' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', ['autoincrement' => true] );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'url', 'string', ['length' => 255] );
			$table->addColumn( 'label', 'string', ['length' => 255] );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', ['length' => 255] );

			$table->setPrimaryKey( ['id'], 'pk_mscms_id' );
			$table->addUniqueIndex( ['siteid', 'url'], 'unq_mscms_sid_url' );
			$table->addIndex( ['siteid', 'status'], 'unq_mscms_sid_status' );
			$table->addIndex( ['siteid', 'label'], 'unq_mscms_sid_label' );

			return $schema;
		},

		'mshop_cms_list_type' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_cms_list_type' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'code', 'string', array( 'length' => 64, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'label', 'string', array( 'length' => 255 ) );
			$table->addColumn( 'pos', 'integer', ['default' => 0] );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mscmslity_id' );
			$table->addUniqueIndex( array( 'siteid', 'domain', 'code' ), 'unq_mscmslity_sid_dom_code' );
			$table->addIndex( array( 'siteid', 'status', 'pos' ), 'idx_mscmslity_sid_status_pos' );
			$table->addIndex( array( 'siteid', 'label' ), 'idx_mscmslity_sid_label' );
			$table->addIndex( array( 'siteid', 'code' ), 'idx_mscmslity_sid_code' );

			return $schema;
		},

		'mshop_cms_list' => function( \Doctrine\DBAL\Schema\Schema $schema ) {

			$table = $schema->createTable( 'mshop_cms_list' );
			$table->addOption( 'engine', 'InnoDB' );

			$table->addColumn( 'id', 'integer', array( 'autoincrement' => true ) );
			$table->addColumn( 'parentid', 'integer', [] );
			$table->addColumn( 'siteid', 'string', ['length' => 255] );
			$table->addColumn( 'key', 'string', array( 'length' => 134, 'default' => '', 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'type', 'string', array( 'length' => 64, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'domain', 'string', array( 'length' => 32 ) );
			$table->addColumn( 'refid', 'string', array( 'length' => 36, 'customSchemaOptions' => ['charset' => 'binary'] ) );
			$table->addColumn( 'start', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'end', 'datetime', array( 'notnull' => false ) );
			$table->addColumn( 'config', 'text', array( 'length' => 0xffff ) );
			$table->addColumn( 'pos', 'integer', [] );
			$table->addColumn( 'status', 'smallint', [] );
			$table->addColumn( 'mtime', 'datetime', [] );
			$table->addColumn( 'ctime', 'datetime', [] );
			$table->addColumn( 'editor', 'string', array( 'length' => 255 ) );

			$table->setPrimaryKey( array( 'id' ), 'pk_mscmsli_id' );
			$table->addUniqueIndex( array( 'parentid', 'domain', 'siteid', 'type', 'refid' ), 'unq_mscmsli_pid_dm_sid_ty_rid' );
			$table->addIndex( array( 'key', 'siteid' ), 'idx_mscmsli_key_sid' );
			$table->addIndex( array( 'parentid' ), 'fk_mscmsli_pid' );

			$table->addForeignKeyConstraint( 'mshop_cms', array( 'parentid' ), array( 'id' ),
				array( 'onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE' ), 'fk_mscmsli_pid' );

			return $schema;
		},
	],
];
