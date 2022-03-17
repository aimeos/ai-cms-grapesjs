<?php

return [
	'name' => 'ai-cms-grapesjs',
	'depends' => [
		'aimeos-core',
		'ai-admin-jqadm',
		'ai-admin-jsonadm',
		'ai-client-html',
		'ai-client-jsonapi',
		'ai-controller-frontend',
	],
	'include' => [
		'src',
	],
	'i18n' => [
		'admin' => 'i18n',
		'client' => 'i18n',
	],
	'config' => [
		'config',
	],
	'setup' => [
		'setup',
	],
	'template' => [
		'admin/jqadm/templates' => [
			'templates/admin/jqadm',
		],
		'client/html/templates' => [
			'templates/client/html',
		],
		'client/jsonapi/templates' => [
			'templates/client/jsonapi',
		],
	],
	'custom' => [
		'admin/jqadm' => [
			'manifest.jsb2',
		],
	],
];
