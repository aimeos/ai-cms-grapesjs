/*
 * Custom ai-cms-grapesjs JS
 */


Aimeos.components['grapesjs'] = {
	template: `<div class="grapesjs-editor">
		<input type="hidden" v-bind:name="name" v-bind:value="value" />
		<div v-if="!readonly" class="gjs cms-preview"></div>
		<iframe v-else v-bind:srcdoc="'<html><body><style>' + (parsed['css'] || '') + '</style>' + (parsed['html'] || '') + '</body></html>'" v-bind:tabindex="tabindex"></iframe>
	</div>`,
	props: ['setup', 'name', 'value', 'readonly', 'tabindex', 'update', 'media', 'mediaurl', 'config'],

	data: function() {
		return {
			instance: null,
			parsed: {}
		}
	},

	beforeDestroy: function() {
		if(this.instance) {
			this.instance.destroy();
			this.instance = null;
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
			this.parsed = JSON.parse(this.value);
			return;
		}

		this.setup.config = Object.assign(this.setup.config, this.config, {
			container: this.$el.querySelector('.gjs'),
		});

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
				this.$emit('update:modelValue', JSON.stringify({
					css: this.instance.getCss({avoidProtected: true}),
					html: this.instance.getHtml()
				}));
			}
		}
	}
};


Aimeos.CMSContent = {

	GrapesJS: {
		config: {
			container: null,
			components: '',
			fromElement: false,
			showDevices: false,
			noticeOnUnload: false,
			height: 'calc(100vh - 10rem)',
			width: '100%',
			plugins: ['grapesjs-plugin-header'],
			pluginsOpts: {
				'grapesjs-table': {
					classTable: 'table',
					style: `
						.table .row {
							display: table-row;
						}
						.table .cell {
							width: auto; height: auto;
						}
					`,
					tableProps: {
						footer: false
					}
				}
			},
			canvasCss: `
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
				table {
					border-collapse: separate;
					border-spacing: 0;
					width: 100%;
				}
				table .cell {
					width: auto;
				}
				.row {
					display: flex; width: auto;
					min-height: 2.5rem !important;
				}
				.gjs-dashed .row, .gjs-dashed .space, form, table, table .cell {
					padding: 10px 0;
				}
			`,
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
			},
			styleManager: {
				sectors: []
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
				id: 'open-tm',
				command: 'open-tm',
				className: 'fa fa-cog',
			},{
/*				id: 'open-sm',
				command: 'open-sm',
				className: 'fa fa-paint-brush',
			},{
				id: 'open-layers',
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
					content: 'Insert text here',
					activeOnRender: 1
				}
			},
			'paragraph': {
				category: 'Basic',
				label: 'Paragraph',
				attributes: { class: 'fa fa-paragraph' },
				content: {
					type: 'paragraph',
					content: 'Insert paragraph here',
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
					type: 'link-block',
					editable: false,
					droppable: true,
					attributes: { class: 'link-block space' }
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
						<div class="col-sm-8"><input class="form-control" name="contact[name]" /></div>
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
			'slider': {
				category: 'Extra',
				label: `
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left-right" viewBox="0 0 16 16">
						<path fill-rule="evenodd" d="M1 11.5a.5.5 0 0 0 .5.5h11.793l-3.147 3.146a.5.5 0 0 0 .708.708l4-4a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 11H1.5a.5.5 0 0 0-.5.5zm14-7a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 1 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H14.5a.5.5 0 0 1 .5.5z"/>
					</svg>
					<div class="gjs-block-label">Slider</div>`,
				attributes: { class: 'fa' },
				content: {
					type: 'slider'
				},
			}
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
							styles: `
								.background {
									min-height: 2.5rem !important;
									padding: 10px 0 !important;
								}
							`,
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

							this.setStyle({'background-image': 'none'})
							url && this.setStyle({'background-image': `url('${url.replace(/&|<|>|"|`|'/g, '')}')`});
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
							styles: `
								.container-xl {
									min-height: 2.5rem !important;
								}
								.gjs-dashed .container-xl {
									padding: 10px 0;
								}
							`
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
								const widths = [...model.props().widths || []];
								const cols = model.props().cols || 1;
								let result = '';

								for(let i=0; i<cols; i++) {
									const size = widths.shift();
									result += '<div class="col' + (size ? '-' + size : '') + '" data-gjs-draggable=".row" data-gjs-name="Column"></div>';
								}
								return result;
							},
							styles: `
								.col, [class^="col-"] {
									min-height: 2.5rem !important;
								}
							`,
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
									{id: 'g-0', name: 'No'},
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
							const widths = [...this.props().widths || []];

							this.attributes.components.models.forEach(function(model, idx) {
								if(model.attributes.tagName === 'div') {
									const size = widths.shift();
									model.setClass(bsclass + (size ? '-' + size : ''));
								}
							});
						},
						onGutterChange() {
							this.removeClass('g-0');
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
							styles: `
								.cataloglist {
									display: block; max-height: 350px; overflow: hidden; white-space: nowrap;
								}
								.product {
									display: inline-block; width: 240px; height: 320px; margin: 14px; background-color: #E8ECEF;
								}
							`,
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
							const filter = {'>': {'catalog.status': 0}}

							Aimeos.query(`query {
								searchCatalogs(filter: ` + JSON.stringify(JSON.stringify(filter)) + `, limit: 250) {
									items {
										id
										code
										level
										label
									}
								}
							  }
							`).then(result => {
								const list = (result?.searchCatalogs?.items || []).map(item => {
									return {id: item.id, name: '-'.repeat(item.level) + ' ' + item.label + ' (' + item.code + ')'}
								})
								list.unshift({id: '', name: ' '})

								const catid = this.get('traits').where({name: 'catid'})[0];
								catid.set('options', list);
							})
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


			'link-block': function(editor) {
				editor.DomComponents.addType('link-block', {
					extend: 'link',
					isComponent: el => el.tagName === 'A' && el.classList.contains('link-block') ? {type: 'link-block'} : false,
					model: {
						defaults: {
							tagName: 'a',
							draggable: true,
							droppable: true,
							styles: `
								a.link-block {
									display: inline-block; min-height: 50px; width: 100%;
								}
							`
						}
					}
				});
			},


			'paragraph': function(editor) {
				editor.DomComponents.addType('paragraph', {
					extend: 'text',
					isComponent: el => el.tagName === 'P' ? {type: 'paragraph'} : false,
					model: {
						defaults: {
							tagName: 'p',
							draggable: true,
							droppable: true,
							styles: `
								p { min-height: 1.5rem }
							`
						}
					}
				});
			},


			'slider': function(editor) {
				editor.DomComponents.addType('slider', {
					isComponent: el => el.tagName === 'DIV' && el.classList.contains('swiffy-slider') ? {type: 'slider'} : false,
					model: {
						defaults: {
							tagName: 'div',
							attributes: {
								'class': 'swiffy-slider slider-item-nogap slider-nav-animation slider-nav-autoplay slider-nav-autopause',
								'data-slider-nav-autoplay-interval': 4000,
								'data-gjs-name': 'Slider'
							},
							components: model => {
								const slides = model.props().slides || 1;
								let result = '<div class="slider-container">';

								for(let i=0; i<slides; i++) {
									result += '<div class="swiffy-slide" data-gjs-name="Slide"></div>';
								}
								return result + `
									</div>
									<button type="button" class="slider-nav" aria-label="Go to previous"></button>
									<button type="button" class="slider-nav slider-nav-next" aria-label="Go to next"></button>
								`;
							},
							styles: `
								.swiffy-slider {min-height:2.5rem !important; padding:10px 0}
								.slider-container {display:block; height: auto}
							`,
							traits: [{
								type: 'number',
								label: 'Slides',
								name: 'slides',
								min: 1,
								max: 10,
								step: 1
							},{
								type: 'number',
								label: 'Interval',
								name: 'data-slider-nav-autoplay-interval',
								min: 500,
								step: 500
							}]
						},
						init() {
							this.on('change:attributes:slides', this.onSlidesChange);
						},
						onSlidesChange() {
							const size = this.getAttributes().slides || 1;
							const container = this.components().first();
							const slides = container.components();

							if(slides.length < size) {
								for(let i=0; i<size - slides.length; i++) {
									slides.add('<div class="swiffy-slide" data-gjs-name="Slide"></div>');
								}
							} else {
								for(let i=0; i<slides.length - size; i++) {
									slides.pop();
								}
							}

							this.getView()?.render();
						}
					}
				});
			},


			'slide': function(editor) {
				editor.DomComponents.addType('slide', {
					isComponent: el => el.tagName === 'DIV' && el.classList.contains('swiffy-slide') ? {type: 'slide'} : false,
					model: {
						defaults: {
							tagName: 'div',
							droppable: true,
							attributes: {
								class: 'swiffy-slide',
							},
							styles: `
								.swiffy-slide {
									min-height: 2.5rem !important;
									padding: 10px 0 !important;
								}
							`,
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

							this.setStyle({'background-image': 'none'})
							url && this.setStyle({'background-image': `url('${url.replace(/&|<|>|"|`|'/g, '')}')`});
						}
					}
				});
			},


			'text': function(editor) {
				editor.DomComponents.addType('text', {
					extend: 'text',
					isComponent: el => el.tagName === 'SPAN' ? {type: 'text'} : false,
					model: {
						defaults: {
							tagName: 'span',
							draggable: true,
							droppable: true,
							styles: `
								span { min-height: 1.5rem }
							`
						}
					}
				});
			},
		},


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
					},
					onError() {
						const fallback = this.model.getSrcResult({ fallback: 1 });
						if (fallback) this.el.src = fallback;
						if (fallback) this.el.removeAttribute('srcset');
					}
				}
			});

			editor.Canvas.getFrames().forEach(frame => {
				const body = frame.view.getBody();
				body.setAttribute('dir', editor.getConfig('langDir'));
				body.classList.add('aimeos', 'cms-page', 'gjs-dashed');
			});
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
		const node = document.querySelector('#item-content-group');

		if(node) {
			Aimeos.apps['cms-content'] = Aimeos.app({
				props: {
					data: {type: String, default: '{}'},
					images: {type: String, default: '{}'},
					siteid: {type: String, default: ''},
					domain: {type: String, default: ''},
				},
				data() {
					return {
						items: {},
						media: [],
						version: 0
					}
				},
				beforeMount() {
					this.Aimeos = Aimeos;
					this.items = JSON.parse(this.data);
					this.media = JSON.parse(this.images);

					if(this.items[0]) {
						this.items[0]['_show'] = true;
					}
				},
				mixins: [this.mixins]
			}, {...node.dataset || {}}).mount(node);

			document.querySelectorAll('.btn').forEach(function(el) {
				el.addEventListener('mousedown', function() {
					Aimeos.apps['cms-content'].change();
				})
			});
		}
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


			can(action, idx) {
				if(action === 'move' && this.items[idx]['_nosort']) return false
				return Aimeos.can(action, this.items[idx][this.domain + '.lists.siteid'] || null, this.siteid)
			},


			change: function() {
				this.version++;
			},


			duplicate: function(idx) {
				if(idx < this.items.length) {
					let entry = JSON.parse(JSON.stringify(this.items[idx]));
					entry['text.id'] = null;
					this.items[this.items.length] = entry;
				}
			},


			label: function(idx) {
				return (this.items[idx]['text.languageid'] ? this.items[idx]['text.languageid'].toUpperCase() : '***');
			},


			remove: function(idx) {
				this.items.splice(idx, 1);
			},


			toggle: function(what, idx) {
				this.items[idx][what] = (!this.items[idx][what] ? true : false);
			},
		}
	}
};


$(function() {
	Aimeos.CMSContent.init();
});
