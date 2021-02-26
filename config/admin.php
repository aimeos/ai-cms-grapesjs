<?php

return [
	'jqadm' => [
		'cms' => [
			'domains' => [
				'text' => 'text',
			],
			'subparts' => [
				'content' => 'content',
				'seo' => 'seo',
			],
		],
		'navbar' => [
			55 => 'cms'
		],
		'resource' => [
			'cms' => [
				/** admin/jqadm/resource/cms/groups
				 * List of user groups that are allowed to access the CMS panel
				 *
				 * @param array List of user group names
				 * @since 2021.04
				 */
				'groups' => ['admin', 'editor', 'super'],

				/** admin/jqadm/resource/cms/key
				 * Shortcut key to switch to the CMS panel by using the keyboard
				 *
				 * @param string Single character in upper case
				 * @since 2021.04
				 */
				'key' => 'M',
			],
		]
	],
	'jsonadm' => [
	],
];
