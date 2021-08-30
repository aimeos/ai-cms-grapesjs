<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */

return [
	'cms' => [[
		'cms.url' => '/contact', 'cms.label' => 'Contact', 'cms.status' => 1,
		'lists' => [
			'text' => [[
				'cms.lists.type' => 'default', 'cms.lists.position' => 0,
				'text.languageid' => 'en', 'text.type' => 'name', 'text.domain' => 'cms',
				'text.label' => 'Contact page', 'text.content' => 'Contact page'
			], [
				'cms.lists.type' => 'default', 'cms.lists.position' => 1,
				'text.languageid' => 'en', 'text.type' => 'meta-keywords', 'text.domain' => 'cms',
				'text.label' => 'contact', 'text.content' => 'contact, about us'
			], [
				'cms.lists.type' => 'default', 'cms.lists.position' => 1,
				'text.languageid' => 'en', 'text.type' => 'content', 'text.domain' => 'cms',
				'text.label' => 'Hello', 'text.content' => '<h1>Hello!</h1>'
			]],
			'media' => [[
				'cms.lists.type' => 'default', 'cms.lists.position' => 0,
				'media.languageid' => null, 'media.type' => 'default', 'media.domain' => 'cms',
				'media.label' => 'Test image', 'media.url' => 'path/to/image.jpg',
				'media.previews' => [1 => 'path/to/image-small.jpg']
			]]
		]
	], [
		'cms.url' => '/kontakt', 'cms.label' => 'Kontakt', 'cms.status' => 0,
		'lists' => [
			'text' => [[
				'cms.lists.type' => 'default', 'cms.lists.position' => 0,
				'text.languageid' => 'de', 'text.type' => 'name', 'text.domain' => 'cms',
				'text.label' => 'Kontaktseite', 'text.content' => 'Kontaktseite'
			], [
				'cms.lists.type' => 'default', 'cms.lists.position' => 1,
				'text.languageid' => 'de', 'text.type' => 'meta-keywords', 'text.domain' => 'cms',
				'text.label' => 'kontakt', 'text.content' => 'kontakt, über uns'
			], [
				'cms.lists.type' => 'default', 'cms.lists.position' => 1,
				'text.languageid' => 'de', 'text.type' => 'content', 'text.domain' => 'cms',
				'text.label' => 'Hallo', 'text.content' => '<h1>Hallo!</h1>'
			]],
			'media' => [[
				'cms.lists.type' => 'default', 'cms.lists.position' => 0,
				'media.languageid' => null, 'media.type' => 'default', 'media.domain' => 'cms',
				'media.label' => 'Test image', 'media.url' => 'path/to/image-2.jpg',
				'media.previews' => [1 => 'path/to/image-small-2.jpg']
			]]
		]
	], [
		'cms.url' => '/catlist', 'cms.label' => 'With catalog list', 'cms.status' => 1,
		'lists' => [
			'text' => [[
				'cms.lists.type' => 'default', 'cms.lists.position' => 1,
				'text.languageid' => 'de', 'text.type' => 'content', 'text.domain' => 'cms',
				'text.label' => 'catlist test', 'text.content' => '
					<div>
						<h1>Catalog list example</h1>
						<div>
							<div>
								<cataloglist class="cataloglist" catid="_cat1_" type="promotion" limit="1">
									<div class="product"></div>
								</cataloglist>
							</div>
							<div></div>
						</div>
						<h2>All products</h2>
						<cataloglist class="cataloglist" catid="_cat2_" type="default" limit="3">
							<div class="product"></div>
						</cataloglist>
					</div>
				'
			]]
		]
	]]
];
