<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2025
 */


$enc = $this->encoder();


?>
<div v-show="element['_ext']" class="col-xl-12 secondary">

	<property-table v-if="element['property'] && element['property'].length"
		v-bind:index="index" v-bind:domain="'media'"
		v-bind:siteid="`<?= $enc->js( $this->site()->siteid() ) ?>`" v-bind:tabindex="`<?= $enc->js( $this->get( 'tabindex' ) ) ?>`"
		v-bind:types="<?= $enc->attr( $this->get( 'propertyTypes', map() )->col( 'media.property.type.label', 'media.property.type.code' )->toJson( JSON_FORCE_OBJECT ) ) ?>"
		v-bind:languages="<?= $enc->attr( $this->get( 'pageLangItems', map() )->col( 'locale.language.label', 'locale.language.id' )->toJson( JSON_FORCE_OBJECT ) ) ?>"
		v-bind:name="`<?= $enc->js( $this->formparam( ['media', '_idx_', 'property', '_propidx_', '_key_'] ) ) ?>`"
		v-bind:items="element['property']" v-on:update:property="element['property'] = $event"
		v-bind:i18n="{
			all: `<?= $enc->js( $this->translate( 'admin', 'All' ) ) ?>`,
			delete: `<?= $enc->js( $this->translate( 'admin', 'Delete this entry' ) ) ?>`,
			header: `<?= $enc->js( $this->translate( 'admin', 'Media properties' ) ) ?>`,
			help: `<?= $enc->js( $this->translate( 'admin', 'Non-shared properties for the media item' ) ) ?>`,
			insert: `<?= $enc->js( $this->translate( 'admin', 'Insert new entry (Ctrl+I)' ) ) ?>`,
			placeholder: `<?= $enc->js( $this->translate( 'admin', 'Property value (required)' ) ) ?>`,
			select: `<?= $enc->js( $this->translate( 'admin', 'Please select' ) ) ?>`
		}">
	</property-table>

	<?= $this->get( 'propertyBody' ) ?>

</div>
