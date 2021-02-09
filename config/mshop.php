<?php

return [
	'cms' => [
		'manager' => array(
			'lists' => array(
				'type' => array(
					'delete' => array(
						'ansi' => '
							DELETE FROM "mshop_cms_list_type"
							WHERE :cond AND siteid = ?
						'
					),
					'insert' => array(
						'ansi' => '
							INSERT INTO "mshop_cms_list_type" ( :names
								"code", "domain", "label", "pos", "status",
								"mtime", "editor", "siteid", "ctime"
							) VALUES ( :values
								?, ?, ?, ?, ?, ?, ?, ?, ?
							)
						'
					),
					'update' => array(
						'ansi' => '
							UPDATE "mshop_cms_list_type"
							SET :names
								"code" = ?, "domain" = ?, "label" = ?, "pos" = ?,
								"status" = ?, "mtime" = ?, "editor" = ?
							WHERE "siteid" = ? AND "id" = ?
						'
					),
					'search' => array(
						'ansi' => '
							SELECT :columns
								mcmslity."id" AS "cms.lists.type.id", mcmslity."siteid" AS "cms.lists.type.siteid",
								mcmslity."code" AS "cms.lists.type.code", mcmslity."domain" AS "cms.lists.type.domain",
								mcmslity."label" AS "cms.lists.type.label", mcmslity."status" AS "cms.lists.type.status",
								mcmslity."mtime" AS "cms.lists.type.mtime", mcmslity."editor" AS "cms.lists.type.editor",
								mcmslity."ctime" AS "cms.lists.type.ctime", mcmslity."pos" AS "cms.lists.type.position"
							FROM "mshop_cms_list_type" AS mcmslity
							:joins
							WHERE :cond
							ORDER BY :order
							OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
						',
						'mysql' => '
							SELECT :columns
								mcmslity."id" AS "cms.lists.type.id", mcmslity."siteid" AS "cms.lists.type.siteid",
								mcmslity."code" AS "cms.lists.type.code", mcmslity."domain" AS "cms.lists.type.domain",
								mcmslity."label" AS "cms.lists.type.label", mcmslity."status" AS "cms.lists.type.status",
								mcmslity."mtime" AS "cms.lists.type.mtime", mcmslity."editor" AS "cms.lists.type.editor",
								mcmslity."ctime" AS "cms.lists.type.ctime", mcmslity."pos" AS "cms.lists.type.position"
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
						WHERE :cond AND siteid = ?
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
						WHERE "siteid" = ? AND "id" = ?
					'
				),
				'search' => array(
					'ansi' => '
						SELECT :columns
							mcmsli."id" AS "cms.lists.id", mcmsli."parentid" AS "cms.lists.parentid",
							mcmsli."siteid" AS "cms.lists.siteid", mcmsli."type" AS "cms.lists.type",
							mcmsli."domain" AS "cms.lists.domain", mcmsli."refid" AS "cms.lists.refid",
							mcmsli."start" AS "cms.lists.datestart", mcmsli."end" AS "cms.lists.dateend",
							mcmsli."config" AS "cms.lists.config", mcmsli."pos" AS "cms.lists.position",
							mcmsli."status" AS "cms.lists.status", mcmsli."mtime" AS "cms.lists.mtime",
							mcmsli."editor" AS "cms.lists.editor", mcmsli."ctime" AS "cms.lists.ctime"
						FROM "mshop_cms_list" AS mcmsli
						:joins
						WHERE :cond
						ORDER BY :order
						OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
					',
					'mysql' => '
						SELECT :columns
							mcmsli."id" AS "cms.lists.id", mcmsli."parentid" AS "cms.lists.parentid",
							mcmsli."siteid" AS "cms.lists.siteid", mcmsli."type" AS "cms.lists.type",
							mcmsli."domain" AS "cms.lists.domain", mcmsli."refid" AS "cms.lists.refid",
							mcmsli."start" AS "cms.lists.datestart", mcmsli."end" AS "cms.lists.dateend",
							mcmsli."config" AS "cms.lists.config", mcmsli."pos" AS "cms.lists.position",
							mcmsli."status" AS "cms.lists.status", mcmsli."mtime" AS "cms.lists.mtime",
							mcmsli."editor" AS "cms.lists.editor", mcmsli."ctime" AS "cms.lists.ctime"
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
					WHERE :cond AND siteid = ?
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
					WHERE "siteid" = ? AND "id" = ?
				'
			),
			'search' => array(
				'ansi' => '
					SELECT :columns
						mcms."id" AS "cms.id", mcms."siteid" AS "cms.siteid",
						mcms."url" AS "cms.url", mcms."label" AS "cms.label",
						mcms."status" AS "cms.status", mcms."mtime" AS "cms.mtime",
						mcms."editor" AS "cms.editor", mcms."ctime" AS "cms.ctime"
					FROM "mshop_cms" AS mcms
					:joins
					WHERE :cond
					GROUP BY :columns :group
						mcms."id", mcms."siteid", mcms."url", mcms."label",
						mcms."status", mcms."mtime", mcms."editor", mcms."ctime"
					ORDER BY :order
					OFFSET :start ROWS FETCH NEXT :size ROWS ONLY
				',
				'mysql' => '
					SELECT :columns
						mcms."id" AS "cms.id", mcms."siteid" AS "cms.siteid",
						mcms."url" AS "cms.url", mcms."label" AS "cms.label",
						mcms."status" AS "cms.status", mcms."mtime" AS "cms.mtime",
						mcms."editor" AS "cms.editor", mcms."ctime" AS "cms.ctime"
					FROM "mshop_cms" AS mcms
					:joins
					WHERE :cond
					GROUP BY :group mcms."id"
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
