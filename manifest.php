<?php

return array(
	'name' => 'ai-cms-grapesjs',
	'depends' => array(
		'aimeos-core',
		'ai-admin-jqadm',
		'ai-admin-jsonadm',
		'ai-client-html',
		'ai-client-jsonapi',
		'ai-controller-frontend',
	),
	'include' => array(
		'lib/custom/src',
		'client/html/src',
		'client/jsonapi/src',
		'controller/frontend/src',
		'admin/jsonadm/src',
		'admin/jqadm/src',
	),
	'i18n' => array(
		'admin' => 'admin/i18n',
		'admin/jsonadm' => 'admin/jsonadm/i18n',
		'client' => 'client/i18n',
		'client/code' => 'client/i18n/code',
		'controller/frontend' => 'controller/frontend/i18n',
		'mshop' => 'lib/custom/i18n',
	),
	'config' => array(
		'config',
	),
	'custom' => array(
		'admin/jqadm' => array(
			'admin/jqadm/manifest.jsb2',
		),
		'admin/jqadm/templates' => array(
			'admin/jqadm/templates',
		),
		'admin/jsonadm/templates' => array(
			'admin/jsonadm/templates',
		),
		'client/html/templates' => array(
			'client/html/templates',
		),
		'client/jsonapi/templates' => array(
			'client/jsonapi/templates',
		),
	),
	'setup' => array(
		'lib/custom/setup',
	),
);
