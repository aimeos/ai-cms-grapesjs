<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */

return [
	'cms/lists/type' => [
		['domain' => 'text', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
		['domain' => 'media', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
	],

	'text/type' => [
		['domain' => 'cms', 'code' => 'name', 'label' => 'Name', 'status' => 1],
		['domain' => 'cms', 'code' => 'meta-keyword', 'label' => 'Meta keywords', 'status' => 1],
		['domain' => 'cms', 'code' => 'meta-description', 'label' => 'Meta description', 'status' => 1],
		['domain' => 'cms', 'code' => 'content', 'label' => 'Content', 'status' => 1],
	],

	'media/type' => [
		['domain' => 'cms', 'code' => 'default', 'label' => 'Standard', 'status' => 1],
	]
];
