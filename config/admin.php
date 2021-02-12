<?php

return [
	'jqadm' => [
		'cms' => [
			'domains' => [
				'text' => 'text',
			],
			'subparts' => [
				'text' => 'text',
			],
		],
		'navbar' => [
			55 => 'cms'
		],
		'navbar-limit' => 7,
		'resource' => [
			'cms' => [
				/** admin/jqadm/resource/cms/groups
				 * List of user groups that are allowed to access the CMS panel
				 *
				 * @param array List of user group names
				 * @since 2020.10
				 */
				'groups' => ['admin', 'editor', 'super'],

				/** admin/jqadm/resource/cms/key
				 * Shortcut key to switch to the CMS panel by using the keyboard
				 *
				 * @param string Single character in upper case
				 * @since 2020.10
				 */
				'key' => 'M',
			],
		]
	],
	'jsonadm' => [
        'domains' => [
            'cms' => 'cms',
        ],
        'resources' => [
            'cms/lists/type' => 'cms/lists/type',
        ],
	],
];
