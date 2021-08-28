/*
 * Custom ai-cms-grapesjs JS
 */


Vue.component('grapesjs', {
	template: `<div class="grapesjs-editor">
		<input type="hidden" v-bind:name="name" v-bind:value="value" />
		<div v-if="!readonly" class="gjs cms-preview"></div>
		<iframe v-else v-bind:srcdoc="'<html><head>' + style + '</head><body>' + value + '</body></html>'" v-bind:tabindex="tabindex"></iframe>
	</div>`,
	props: ['setup', 'name', 'value', 'readonly', 'tabindex', 'update', 'media', 'mediaurl'],

	data: function() {
		return {
			instance: null
		}
	},

	beforeDestroy: function() {
		if(this.instance) {
			this.instance.destroy();
			this.instance = null;
		}
	},

	computed: {
		style: function() {
			let result = '';
			(this.setup.config.canvas.styles || []).forEach(item => {
				result += '<link rel="stylesheet" src="' + item + '" />';
			});
			return result;
		}
	},

	methods: {
		setData(val) {
			try {
				const json = JSON.parse(val);
				this.instance.setComponents(json.html || '');
				this.instance.setStyle((json.css || this.setup.styles));
			} catch(e) {
				this.instance.setComponents(val);
			}
		}
	},

	mounted: function() {
		if(this.readonly) {
			return;
		}

		this.setup.config.container = this.$el.querySelector('.gjs');
		this.instance = grapesjs.init(this.setup.config);

		this.setup.initialize(this.instance, this.setup, this.media);
		this.setData(this.value);
	},

	watch: {
		value: function(val, oldval) {
			if(val !== oldval) {
				this.setData(val);
			}
		},
		update: function() {
			if(this.instance) {
				this.$emit('input', JSON.stringify({
					css: this.instance.getCss({avoidProtected: true}),
					html: this.instance.getHtml()
				}));
			}
		}
	}
});


Aimeos.CMSContent = {

	GrapesJS: {
		config: {
			container: null,
			components: '',
			fromElement: false,
			noticeOnUnload: false,
			height: 'calc(100vh - 10rem)',
			width: '100%',
			plugins: ['grapesjs-plugin-header'],
			pluginsOpts: {
				'grapesjs-table': {
					classTable: 'table',
					tableProps: {
						footer: false
					}
				}
			},
			canvas: {
				styles: [
					'https://cdn.jsdelivr.net/npm/bootstrap@4/dist/css/bootstrap.min.css',
					'/vendor/shop/themes/default/aimeos.css'
				],
			},
			i18n: {
				locale: 'en',
				localeFallback: 'en',
				messages: {}
			},
			assetManager: {
				showUrlInput: false
			},
			deviceManager: {
				devices: [{
					name: 'Desktop',
					width: '', // default size
				}, {
					name: 'Tablet',
					width: '768px', // this value will be used on canvas width
					widthMedia: '992px', // this value will be used in CSS @media
				}, {
					name: 'Mobile',
					width: '320px', // this value will be used on canvas width
					widthMedia: '480px', // this value will be used in CSS @media
				}]
			},
			storageManager: {
				type: null
			}
		},

		panels: [{
			id: 'device',
			buttons: [{
				id: 'desktop',
				className: 'fa fa-desktop',
				command: e => e.setDevice('Desktop'),
/*			},{
				id: 'tablet',
				className: 'fa fa-tablet',
				command: e => e.setDevice('Tablet')
*/			},{
				id: 'mobile',
				className: 'fa fa-mobile',
				command: e => e.setDevice('Mobile'),
			}],
		},{
			id: 'show',
			buttons: [{
				id: 'preview',
				className: 'fa fa-eye',
				command: e => e.runCommand('preview'),
				context: 'preview',
			},{
				id: 'fullscreen',
				className: 'fa fa-arrows-alt',
				command: 'fullscreen',
				context: 'fullscreen',
			}]
		},{
			id: 'edit',
			buttons: [{
				id: 'undo',
				className: 'fa fa-undo',
				command: e => e.runCommand('core:undo'),
			},{
				id: 'redo',
				className: 'fa fa-repeat',
				command: e => e.runCommand('core:redo'),
			}]
/*		},{
			id: 'impexp',
			buttons: [{
				id: 'export-template',
				className: 'fa fa-code',
				command: e => e.runCommand('export-template'),
			},{
				id: 'gjs-open-import-webpage',
				className: 'fa fa-upload',
				command: e => e.runCommand('gjs-open-import-webpage'),
			},{
				id: 'canvas-clear',
				className: 'fa fa-trash',
				command: e => e.runCommand('canvas-clear'),
			}],
*/		},{
			id: 'views',
			buttons  : [{
/*				id: 'open-sm',
				command: 'open-sm',
				className: 'fa fa-paint-brush',
			},{
*/				id: 'open-tm',
				command: 'open-tm',
				className: 'fa fa-cog',
			},{
/*				id: 'open-layers',
				command: 'open-layers',
				className: 'fa fa-bars',
			},{
*/				id: 'open-blocks',
				command: 'open-blocks',
				className: 'fa fa-th-large',
			}],
		}],

		blocks: {
			'text': {
				category: 'Basic',
				label: 'Text',
				attributes: { class: 'fa fa-font' },
				content: {
					type: 'text',
					content: '<span data-gjs-name="Text">Insert text here</span>',
					activeOnRender: 1
				}
			},
			'paragraph': {
				category: 'Basic',
				label: 'Paragraph',
				attributes: { class: 'fa fa-paragraph' },
				content: {
					type: 'paragraph',
					content: '<p data-gjs-name="Paragraph">Insert paragraph here</p>',
					activeOnRender: 1
				}
			},
			'button': {
				category: 'Basic',
				label: 'Button',
				attributes: { class: 'fa fa-external-link' },
				content: {
					type: 'btn',
					content: 'More',
					classes: 'btn'
				}
			},
			'link-block': {
				category: 'Basic',
				label: 'Link block',
				attributes: { class: 'fa fa-link' },
				content: {
					type: 'link',
					editable: false,
					droppable: true,
					attributes: { class: 'space' },
					style: {
						display: 'inline-block',
						'min-height': '50px',
						width: '100%'
					}
				}
			},
			'image': {
				category: 'Basic',
				label: 'Image',
				attributes: { class: 'fa fa-picture-o' },
				content: {
					type: 'image',
					activeOnRender: 1,
					attributes: { loading: "lazy" }
				}
			},
			'video': {
				category: 'Basic',
				label: 'Video',
				attributes: { class: 'fa fa-youtube-play' },
				content: {
					type: 'video',
					src: '',
					style: {'max-width': '100%'}
				}
			},
			'background': {
				category: 'Basic',
				label: 'Background',
				attributes: { class: 'fa fa-film' },
				content: `<div class="background"></div>`
			},
			'container': {
				category: 'Columns',
				label: '<svg width="40" height="36" viewBox="0 0 40 36"><rect style="fill:none;stroke-width:2" width="24" height="18" x="10" y="9" ry="2" ry="3"></rect></svg><div class="gjs-block-label">Container</div>',
				attributes: { class: 'fa' },
				content: {
					type: 'container'
				}
			},
			'col-2': {
				category: 'Columns',
				label: '<svg width="40" height="36" viewBox="0 0 40 36"><rect style="fill:none;stroke-width:2" width="12" height="18" x="8" y="9" ry="2" ry="1"></rect><rect style="fill:none;stroke-width:2" width="12" height="18" x="20" y="9" ry="2" ry="1"></rect></svg><div class="gjs-block-label">2 cols</div>',
				attributes: { class: 'fa' },
				content: {
					type: 'cols',
					cols: 2
				},
			},
			'col-1:2': {
				category: 'Columns',
				label: '<svg width="40" height="36" viewBox="0 0 40 36"><rect style="fill:none;stroke-width:2" width="9" height="18" x="8" y="9" ry="2" ry="1"></rect><rect style="fill:none;stroke-width:2" width="15" height="18" x="17" y="9" ry="2" ry="1"></rect></svg><div class="gjs-block-label">1:2 cols</div>',
				attributes: { class: 'fa' },
				content: {
					type: 'cols',
					cols: 2,
					widths: [4, 8]
				},
			},
			'col-2:1': {
				category: 'Columns',
				label: '<svg width="40" height="36" viewBox="0 0 40 36"><rect style="fill:none;stroke-width:2" width="15" height="18" x="8" y="9" ry="2" ry="1"></rect><rect style="fill:none;stroke-width:2" width="9" height="18" x="23" y="9" ry="2" ry="1"></rect></svg><div class="gjs-block-label">2:1 cols</div>',
				attributes: { class: 'fa' },
				content: {
					type: 'cols',
					cols: 2,
					widths: [8, 4]
				},
			},
			'col-3': {
				category: 'Columns',
				label: '<svg width="40" height="36" viewBox="0 0 40 36"><rect style="fill:none;stroke-width:2" width="8" height="18" x="8" y="9" ry="2" ry="1"></rect><rect style="fill:none;stroke-width:2" width="8" height="18" x="16" y="9" ry="2" ry="1"></rect><rect style="fill:none;stroke-width:2" width="8" height="18" x="24" y="9" ry="2" ry="1"></rect></svg><div class="gjs-block-label">3 cols</div>',
				attributes: { class: 'fa' },
				content: {
					type: 'cols',
					cols: 3
				},
			},
			'cataloglist': {
				category: 'Extra',
				label: 'Products',
				attributes: { class: 'fa fa-cubes' },
				content: `<cataloglist limit="3" type="default">
					<div class="product" data-gjs-name="Product"></div>
					<div class="product" data-gjs-name="Product"></div>
					<div class="product" data-gjs-name="Product"></div>
				</cataloglist>`
			},
			'contact': {
				category: 'Extra',
				label: 'Contact',
				attributes: { class: 'fa fa-envelope-o' },
				content: `<form class="contact-form" method="POST" action="">
					<!-- cms.page.contact.csrf -->
					<input class="csrf-token" type="hidden" name="%csrf.name%" value="%csrf.value%" />
					<!-- cms.page.contact.csrf -->
					<div class="form-group row contact-name">
						<label class="col-sm-4 form-control-label">Name</label>
						<div class="col-sm-8"><input class="form-control" name="contact[name]" required /></div>
					</div>
					<div class="form-group row contact-email">
						<label class="col-sm-4 form-control-label">E-Mail</label>
						<div class="col-sm-8"><input class="form-control" name="contact[email]" type="email" required /></div>
					</div>
					<div class="form-group row contact-message">
						<label class="col-sm-4 form-control-label">Text</label>
						<div class="col-sm-8"><textarea class="form-control" name="contact[message]" required rows="6"></textarea></div>
					</div>
					<div class="contact-pot" style="display: none">
						<input name="contact[url]" />
					</div>
					<div class="form-group contact-button">
						<button type="submit" class="btn btn-primary d-block mx-auto">Submit</button>
					</div>
				</form>`
			},
		},

		components: {
			'btn': function(editor) {
				editor.DomComponents.addType('btn', {
					extend: 'link',
					isComponent: el => el.tagName === 'A' && el.classList.contains('btn') ? {type: 'btn'} : false,
					extendFn: ['init'],
					model: {
						defaults: {
							tagName: 'a',
							traits: [{
								type: 'select',
								label: 'Type',
								name: 'data-type',
								options: [
									{id: '', name: 'Standard'},
									{id: 'btn-primary', name: 'Primary'},
									{id: 'btn-secondary', name: 'Secondary'},
								],
							},
							...editor.DomComponents.getType('link').model.prototype.defaults.traits
							]
						},
						init() {
							this.on('change:attributes:data-type', this.onTypeChange);
						},
						onTypeChange() {
							this.setClass('btn ' + this.getAttributes()['data-type']);
						}
					},
				});
			},

			'background': function(editor) {
				editor.DomComponents.addType('background', {
					isComponent: el => el.tagName === 'DIV' && el.classList.contains('background') ? {type: 'background'} : false,
					model: {
						defaults: {
							tagName: 'div',
							attributes: {
								class: 'background',
							},
							traits: [{
								type: 'select',
								label: 'Background',
								name: 'data-background'
							}]
						},
						init() {
							const options = [{id: '', name: ''}];
							const bg = this.getTrait('data-background');

							editor.AssetManager.getAll().each(function(item) {
								options.push({id: item.attributes.srcset || item.attributes.src, name: item.attributes.name});
							});

							bg && bg.set('options', options);
							this.on('change:attributes:data-background', this.onBackgroundChange);
						},
						onBackgroundChange() {
							const bg = this.getAttributes()['data-background'];
							const url = (bg.split(',').pop() || '').trim().split(' ').shift();

							this.setStyle({'background-image': 'none'}) && this.removeClass( 'lazy-image' )
							url && this.setStyle({'background-image': `url('${url.replace(/&|<|>|"|`|'/g, '')}')`}) && this.addClass( 'lazy-image' );
						}
					}
				});
			},

			'container': function(editor) {
				editor.DomComponents.addType('container', {
					isComponent: el => el.tagName === 'DIV' && el.classList.contains('container-xl') ? {type: 'container'} : false,
					model: {
						defaults: {
							tagName: 'div',
							attributes: {
								class: 'container-xl',
								'data-gjs-name': 'Container'
							},
						}
					}
				});
			},

			'cols': function(editor) {
				editor.DomComponents.addType('cols', {
					isComponent: el => el.tagName === 'DIV' && el.classList.contains('row') ? {type: 'cols'} : false,
					model: {
						defaults: {
							tagName: 'div',
							draggable: '.container-xl, .container-fluid, .col',
							droppable: true,
							attributes: {
								class: 'row',
								'data-gjs-droppable': '.col',
								'data-gjs-name': 'Row'
							},
							components: model => {
								const cols = model.props().cols || 1;
								let result = '';

								for(let i=0; i<cols; i++) {
									result += '<div class="col" data-gjs-draggable=".row" data-gjs-name="Column"></div>';
								}
								return result;
							},
							traits: [{
								type: 'select',
								label: 'Breakpoint',
								name: 'data-break',
								options: [
									{id: 'col', name: 'None'},
									{id: 'col-sm', name: 'S (576px)'},
									{id: 'col-md', name: 'M (768px)'},
									{id: 'col-lg', name: 'L (992px)'},
									{id: 'col-xl', name: 'XL (1200px)'},
								]
							},{
								type: 'select',
								label: 'Spacing',
								name: 'data-gutters',
								options: [
									{id: 'no-gutters', name: 'No'},
									{id: '', name: 'Yes'},
								]
							}]
						},
						init() {
							this.on('change:attributes:data-break', this.onBreakpointChange);
							this.on('change:attributes:data-gutters', this.onGutterChange);
						},
						onBreakpointChange() {
							const bsclass = this.getAttributes()['data-break'] || 'col';

							this.attributes.components.models.forEach(function(item, idx) {
								if(item.attributes.tagName === 'div') {
									item.removeClass('col');
									item.removeClass('col-sm');
									item.removeClass('col-md');
									item.removeClass('col-lg');
									item.removeClass('col-xl');
									item.addClass(bsclass);
								}
							});
						},
						onGutterChange() {
							this.removeClass('no-gutters');
							this.addClass(this.getAttributes()['data-gutters'] || '');
						}
					}
				});
			},

			'cataloglist': function(editor) {
				editor.DomComponents.addType('cataloglist', {
					isComponent: el => el.tagName === 'CATALOGLIST' ? {type: 'cataloglist'} : false,
					model: {
						defaults: {
							tagName: 'cataloglist',
							draggable: true,
							droppable: true,
							attributes: {
								class: 'cataloglist',
							},
							components: model => {
								const limit = model.props().limit || 3;
								let result = '';

								for(let i=0; i<limit; i++) {
									result += '<div class="product" data-gjs-name="Product"></div>';
								}
								return result;
							},
							traits: [{
								type: 'select',
								label: 'Category',
								name: 'catid'
							},{
								type: 'select',
								label: 'List type',
								name: 'type',
								options: [
									{id: 'default', name: 'Default'},
									{id: 'promotion', name: 'Promotion'},
								]
							},{
								type: 'number',
								label: 'Limit',
								name: 'limit',
								min: 1,
								max: 100,
								step: 1
							}]
						},
						init() {
							this.on('change:attributes:limit', this.onLimitChange);
							this.fetch();
						},
						fetch() {
							Aimeos.options.done(options => {
								if(options.meta && options.meta.resources && options.meta.resources['catalog'] ) {
									const args = {
										fields: {catalog: 'catalog.id,catalog.label,catalog.level'},
										page: {limit: 100}
									};

									const config = {
										'paramsSerializer': function(params) {
											return jQuery.param(params); // workaround, Axios and QS fail on [==]
										},
										'params': {}
									};

									if(options.meta.prefix && options.meta.prefix) {
										config['params'][options.meta.prefix] = args;
									} else {
										config['params'] = args;
									}

									axios.get(options.meta.resources['catalog'], config).then(response => {
										const items = [{id: '', name: ''}];

										(response.data.data || []).forEach(entry => {
											if(entry['id'] && entry['attributes']) {
												items.push({id: entry['id'], name: '-'.repeat(entry['attributes']['catalog.level'] || 0) + ' ' + (entry['attributes']['catalog.label'] || '')});
											}
										});

										const catid = this.get('traits').where({name: 'catid'})[0];
										catid.set('options', items);
									});
								}
							});
						},
						onLimitChange() {
							let items = '';
							const view = this.getView();
							const limit = this.getAttributes().limit;

							for(let i=0; i<limit; i++) {
								items += '<div class="product" data-gjs-name="Product"></div>';
							}

							this.empty().append(items);
							view && view.render();
						},
						updated(property, value) {
							if(property === 'components' && this.getAttributes().limit !== value.length) {
								this.getTrait('limit').set('value', value.length);
							}
						}
					}
				});
			},

			'paragraph': function(editor) {
				editor.DomComponents.addType('paragraph', {
					extend: 'text',
					isComponent: el => el.tagName === 'P' ? {type: 'paragraph'} : false,
				});
			},
		},

		styles: `
			::-webkit-scrollbar {
				background-color: var(--bs-bg, #f8fafc); width: 0.25rem;
			}
			::-webkit-scrollbar-thumb {
				background-color: #505860; outline: none;
			}
			body {
				background-image: none; background-color: #F8FAFC; scrollbar-color: #505860 transparent; scrollbar-width: thin;
			}
			img {
				max-width: 100%;
			}
			form {
				padding-top: 10px;
			}
			.container-xl, .row, .col, [class^="col-"] {
				min-height: 2.5rem !important;
			}
			.row {
				display: flex; width: auto;
			}
			.gjs-dashed .container-xl, .gjs-dashed .row, .gjs-dashed .space {
				padding: 10px 0;
			}
			.table {
				border-collapse: initial;
			}
			.table .row {
				display: table-row;
			}
			.table .cell {
				width: auto; height: auto;
			}
			.cataloglist {
				display: block; max-height: 350px; overflow: hidden; white-space: nowrap;
			}
			.product {
				display: inline-block; width: 240px; height: 320px; margin: 14px; background-color: #E8ECEF;
			}
			.contact-form .contact-pot {
				display: none;
			}
		`,


		initialize: function(editor, setup, media) {

			editor.Components.addType('image', {
				view: {
					onActive(ev) {
						ev && ev.stopPropagation();
						const { model } = this;
						editor.runCommand('core:open-assets', {
							target: model,
							types: ['image'],
							onClick(asset) {
								const srcset = asset.get('srcset');
								srcset && model.addAttributes({ srcset })
							},
							onSelect(asset) {
								const srcset = asset.get('srcset');
								srcset && model.addAttributes({ srcset })
							}
						});
					}
				}
			});

			editor.Canvas.getFrames().forEach(frame => frame.view.getBody().classList.add('aimeos'));
			editor.I18n.setLocale(document.querySelector('.aimeos').attributes.lang.nodeValue);
			editor.AssetManager.add(media);

			for(const cmp in setup.components) {
				setup.components[cmp](editor);
			}

			// only add own panels
			editor.Panels.getPanels().reset(setup.panels);

			// add custom blocks
			for(const block in setup.blocks) {
				editor.BlockManager.add(block, setup.blocks[block]);
			}

			// load plugins after blocks to enforce order
			const opts = setup.config.pluginsOpts;
			grapesjs.plugins.add('grapesjs-table', window['grapesjs-table'].default(editor, opts['grapesjs-table']));

			// add custom styles
			editor.DomComponents.getWrapper().set('attributes', {'class': 'container-fluid'});
			editor.getComponents().add('<style>' + setup.styles + '</style>');

			// Show Blocks Manager by default
			const blocks = editor.Panels.getButton('views', 'open-blocks');
			editor.on('load', () => blocks && blocks.set('active', 1));

			// On component delete show the Blocks Manager
			editor.on('component:deselected', () => {
				const btn = editor.Panels.getButton('views', 'open-blocks');
				btn && btn.set('active', 1);
			});

			// On component change show the Traits Manager
			editor.on('component:selected', () => {
				if(editor.getSelected()) {
					const btn = editor.Panels.getButton('views', 'open-tm');
					btn && btn.set('active', 1);
				}
			});
		}
	},

	init: function() {

		Aimeos.components['cms-content'] = new Vue({
			el: '#item-content-group',
			data: {
				items: [],
				media: [],
				siteid: null,
				domain: null,
				version: 0
			},
			beforeMount() {
				this.Aimeos = Aimeos;
			},
			mounted: function() {
				this.items = JSON.parse(this.$el.dataset.items || '{}');
				this.media = JSON.parse(this.$el.dataset.media || '{}');
				this.siteid = this.$el.dataset.siteid;
				this.domain = this.$el.dataset.domain;

				if(this.items[0]) {
					this.$set(this.items[0], '_show', true);
				}
			},
			mixins: [this.mixins]
		});

		document.querySelectorAll('.btn').forEach(function(el) {
			el.addEventListener('mousedown', function() {
				Aimeos.components['cms-content'].change();
			})
		});
	},

	mixins: {
		methods: {
			active: function(idx) {
				return this.items[idx] && this.items[idx]['text.status'] > 0;
			},


			add: function(data) {
				let entry = {};

				entry[this.domain + '.lists.id'] = null;
				entry[this.domain + '.lists.type'] = 'default';
				entry[this.domain + '.lists.siteid'] = this.siteid;
				entry[this.domain + '.lists.datestart'] = null;
				entry[this.domain + '.lists.dateend'] = null;

				entry['text.id'] = null;
				entry['text.type'] = 'content';
				entry['text.languageid'] = '';
				entry['text.siteid'] = this.siteid;
				entry['text.content'] = '';
				entry['text.label'] = '';
				entry['text.status'] = 1;

				entry['property'] = [];
				entry['config'] = [];
				entry['_show'] = true;
				entry['_nosort'] = true;

				this.items.push(Object.assign(entry, data));
			},


			change: function() {
				this.version++;
			},


			duplicate: function(idx) {
				if(idx < this.items.length) {
					let entry = JSON.parse(JSON.stringify(this.items[idx]));
					entry['text.id'] = null;
					this.$set(this.items, this.items.length, entry);
				}
			},


			label: function(idx) {
				return (this.items[idx]['text.languageid'] ? this.items[idx]['text.languageid'].toUpperCase() : '***');
			},


			remove: function(idx) {
				this.items.splice(idx, 1);
			},


			toggle: function(what, idx) {
				this.$set(this.items[idx], what, (!this.items[idx][what] ? true : false));
			},
		}
	}
};


$(function() {
	Aimeos.CMSContent.init();
});
