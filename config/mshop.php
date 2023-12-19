<?php

return [
	'cms' => [
		'manager' => array(
			'lists' => array(
				'type' => array(
					'delete' => array(
						'ansi' => '
							DELETE FROM "mshop_cms_list_type"
							WHERE :cond AND siteid LIKE ?
						'
					),
					'insert' => array(
						'ansi' => '
							INSERT INTO "mshop_cms_list_type" ( :names
								"code", "domain", "label", "i18n", "pos", "status",
								"mtime","editor", "siteid", "ctime"
							) VALUES ( :values
								?, ?, ?, ?, ?, ?, ?, ?, ?, ?
							)
						'
					),
					'update' => array(
						'ansi' => '
							UPDATE "mshop_cms_list_type"
							SET :names
								"code" = ?, "domain" = ?, "label" = ?, "i18n" = ?,
								"pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
							WHERE "siteid" LIKE ? AND "id" = ?
						'
					),
					'search' => array(
						'ansi' => '
							SELECT :columns
							FROM "mshop_cms_list_type" AS mcmslity
							:joins
							WHERE :cond
							ORDER BY :order
							OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
						',
						'mysql' => '
							SELECT :columns
							FROM "mshop_cms_list_type" AS mcmslity
							:joins
							WHERE :cond
							ORDER BY :order
							LIMIT :size OFFSET :start
						'
					),
					'count' => array(
						'ansi' => '
							SELECT COUNT(*) AS "count"
							FROM (
								SELECT mcmslity."id"
								FROM "mshop_cms_list_type" as mcmslity
								:joins
								WHERE :cond
								ORDER BY mcmslity."id"
								OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
							) AS list
						',
						'mysql' => '
							SELECT COUNT(*) AS "count"
							FROM (
								SELECT mcmslity."id"
								FROM "mshop_cms_list_type" as mcmslity
								:joins
								WHERE :cond
								ORDER BY mcmslity."id"
								LIMIT 10000 OFFSET 0
							) AS list
						'
					),
					'newid' => array(
						'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
						'mysql' => 'SELECT LAST_INSERT_ID()',
						'oracle' => 'SELECT mshop_cms_list_type_seq.CURRVAL FROM DUAL',
						'pgsql' => 'SELECT lastval()',
						'sqlite' => 'SELECT last_insert_rowid()',
						'sqlsrv' => 'SELECT @@IDENTITY',
						'sqlanywhere' => 'SELECT @@IDENTITY',
					),
				),
				'aggregate' => array(
					'ansi' => '
						SELECT :keys, COUNT("id") AS "count"
						FROM (
							SELECT :acols, mcmsli."id" AS "id"
							FROM "mshop_cms_list" AS mcmsli
							:joins
							WHERE :cond
							GROUP BY :cols, mcmsli."id"
							ORDER BY :order
							OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
						) AS list
						GROUP BY :keys
					',
					'mysql' => '
						SELECT :keys, COUNT("id") AS "count"
						FROM (
							SELECT :acols, mcmsli."id" AS "id"
							FROM "mshop_cms_list" AS mcmsli
							:joins
							WHERE :cond
							GROUP BY :cols, mcmsli."id"
							ORDER BY :order
							LIMIT :size OFFSET :start
						) AS list
						GROUP BY :keys
					'
				),
				'delete' => array(
					'ansi' => '
						DELETE FROM "mshop_cms_list"
						WHERE :cond AND siteid LIKE ?
					'
				),
				'insert' => array(
					'ansi' => '
						INSERT INTO "mshop_cms_list" ( :names
							"parentid", "key", "type", "domain", "refid", "start", "end",
							"config", "pos", "status", "mtime", "editor", "siteid", "ctime"
						) VALUES ( :values
							?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
						)
					'
				),
				'update' => array(
					'ansi' => '
						UPDATE "mshop_cms_list"
						SET :names
							"parentid"=?, "key" = ?, "type" = ?, "domain" = ?, "refid" = ?, "start" = ?,
							"end" = ?, "config" = ?, "pos" = ?, "status" = ?, "mtime" = ?, "editor" = ?
						WHERE "siteid" LIKE ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
						FROM "mshop_cms_list" AS mcmsli
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
						FROM "mshop_cms_list" AS mcmsli
						:joins
						WHERE :cond
						ORDER BY :order
						LIMIT :size OFFSET :start
					'
				),
				'count' => array(
					'ansi' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mcmsli."id"
							FROM "mshop_cms_list" AS mcmsli
							:joins
							WHERE :cond
							ORDER BY mcmsli."id"
							OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
						) AS list
					',
					'mysql' => '
						SELECT COUNT(*) AS "count"
						FROM (
							SELECT mcmsli."id"
							FROM "mshop_cms_list" AS mcmsli
							:joins
							WHERE :cond
							ORDER BY mcmsli."id"
							LIMIT 10000 OFFSET 0
						) AS list
					'
				),
				'newid' => array(
					'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
					'mysql' => 'SELECT LAST_INSERT_ID()',
					'oracle' => 'SELECT mshop_cms_list_seq.CURRVAL FROM DUAL',
					'pgsql' => 'SELECT lastval()',
					'sqlite' => 'SELECT last_insert_rowid()',
					'sqlsrv' => 'SELECT @@IDENTITY',
					'sqlanywhere' => 'SELECT @@IDENTITY',
				),
			),
			'delete' => array(
				'ansi' => '
					DELETE FROM "mshop_cms"
					WHERE :cond AND siteid LIKE ?
				'
			),
			'insert' => array(
				'ansi' => '
					INSERT INTO "mshop_cms" ( :names
						"url", "label", "status", "mtime", "editor", "siteid", "ctime"
					) VALUES ( :values
						?, ?, ?, ?, ?, ?, ?
					)
				'
			),
			'update' => array(
				'ansi' => '
					UPDATE "mshop_cms"
					SET :names
						"url" = ?, "label" = ?, "status" = ?, "mtime" = ?, "editor" = ?
					WHERE "siteid" LIKE ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
					FROM "mshop_cms" AS mcms
					:joins
					WHERE :cond
					GROUP BY :group
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
					FROM "mshop_cms" AS mcms
					:joins
					WHERE :cond
					GROUP BY mcms."id"
					ORDER BY :order
					LIMIT :size OFFSET :start
				'
			),
			'count' => array(
				'ansi' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mcms."id"
						FROM "mshop_cms" AS mcms
						:joins
						WHERE :cond
						GROUP BY mcms."id"
						ORDER BY mcms."id"
						OFFSET 0 ROWS FETCH NEXT 10000 ROWS ONLY
					) AS list
				',
				'mysql' => '
					SELECT COUNT(*) AS "count"
					FROM (
						SELECT mcms."id"
						FROM "mshop_cms" AS mcms
						:joins
						WHERE :cond
						GROUP BY mcms."id"
						ORDER BY mcms."id"
						LIMIT 10000 OFFSET 0
					) AS list
				'
			),
			'newid' => array(
				'db2' => 'SELECT IDENTITY_VAL_LOCAL()',
				'mysql' => 'SELECT LAST_INSERT_ID()',
				'oracle' => 'SELECT mshop_cms_seq.CURRVAL FROM DUAL',
				'pgsql' => 'SELECT lastval()',
				'sqlite' => 'SELECT last_insert_rowid()',
				'sqlsrv' => 'SELECT @@IDENTITY',
				'sqlanywhere' => 'SELECT @@IDENTITY',
			),
		),
	],
	'locale' => [
		'manager' => [
			'site' => [
				'cleanup' => [
					'shop' => [
						'domains' => [
							'cms' => 'cms'
						]
					]
				]
			]
		]
	]
];
