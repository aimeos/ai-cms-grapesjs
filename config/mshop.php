<?php

return [
	'cms' => [
		'manager' => [
			'lists' => [
				'submanagers' => [
					'type' => 'type',
				]
			],
			'submanagers' => [
				'lists' => 'lists',
			],
			'sitemode' => \Aimeos\MShop\Locale\Manager\Base::SITE_ONE,
		],
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
