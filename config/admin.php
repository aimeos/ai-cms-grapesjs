<?php

return [
	'graphql' => [
		'resource' => [
			'cms' => [
				/** admin/graphql/resource/attribute/delete
				 * List of user groups that are allowed to delete attribute items
				 *
				 * @param array List of user group names
				 * @since 2022.10
				 */
				'delete' => ['admin', 'editor', 'super'],

				/** admin/graphql/resource/attribute/save
				 * List of user groups that are allowed to create and update attribute items
				 *
				 * @param array List of user group names
				 * @since 2022.10
				 */
				'save' => ['admin', 'editor', 'super'],

				/** admin/graphql/resource/attribute/get
				 * List of user groups that are allowed to retrieve attribute items
				 *
				 * @param array List of user group names
				 * @since 2022.10
				 */
				'get' => ['admin', 'editor', 'super'],
			]
		]
	],
	'jqadm' => [
		'cms' => [
			'domains' => [
				'text' => 'text',
				'media' => 'media',
			],
			'subparts' => [
				'content' => 'content',
				'seo' => 'seo',
				'media' => 'media',
			],
		],
		'navbar' => [
			45 => 'cms',
			80 => [
				45 => 'type/cms/lists'
			]
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
		'resource' => [
			'cms' => [
				/** admin/jqadm/resource/cms/groups
				 * List of user groups that are allowed to access the CMS panel
				 *
				 * @param array List of user group names
				 * @since 2021.04
				 */
				'groups' => ['admin', 'editor', 'super'],
			],
		]
	],
];
