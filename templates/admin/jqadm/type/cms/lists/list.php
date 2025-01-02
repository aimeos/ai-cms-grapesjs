<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2025
 */

$enc = $this->encoder();


$target = $this->config( 'admin/jqadm/url/search/target' );
$controller = $this->config( 'admin/jqadm/url/search/controller', 'Jqadm' );
$action = $this->config( 'admin/jqadm/url/search/action', 'search' );
$config = $this->config( 'admin/jqadm/url/search/config', [] );

$newTarget = $this->config( 'admin/jqadm/url/create/target' );
$newCntl = $this->config( 'admin/jqadm/url/create/controller', 'Jqadm' );
$newAction = $this->config( 'admin/jqadm/url/create/action', 'create' );
$newConfig = $this->config( 'admin/jqadm/url/create/config', [] );

$getTarget = $this->config( 'admin/jqadm/url/get/target' );
$getCntl = $this->config( 'admin/jqadm/url/get/controller', 'Jqadm' );
$getAction = $this->config( 'admin/jqadm/url/get/action', 'get' );
$getConfig = $this->config( 'admin/jqadm/url/get/config', [] );

$copyTarget = $this->config( 'admin/jqadm/url/copy/target' );
$copyCntl = $this->config( 'admin/jqadm/url/copy/controller', 'Jqadm' );
$copyAction = $this->config( 'admin/jqadm/url/copy/action', 'copy' );
$copyConfig = $this->config( 'admin/jqadm/url/copy/config', [] );

$delTarget = $this->config( 'admin/jqadm/url/delete/target' );
$delCntl = $this->config( 'admin/jqadm/url/delete/controller', 'Jqadm' );
$delAction = $this->config( 'admin/jqadm/url/delete/action', 'delete' );
$delConfig = $this->config( 'admin/jqadm/url/delete/config', [] );


/** admin/jqadm/type/cms/lists/fields
 * List of cms list type columns that should be displayed in the list view
 *
 * Changes the list of cms list type columns shown by default in the cms
 * list type list view. The columns can be changed by the editor as required within the
 * administraiton interface.
 *
 * The names of the colums are in fact the search keys defined by the managers,
 * e.g. "cms.lists.type.id" for the cms type ID.
 *
 * @param array List of field names, i.e. search keys
 * @since 2020.10
 * @category Developer
 */
$default = ['cms.lists.type.domain', 'cms.lists.type.status', 'cms.lists.type.code', 'cms.lists.type.label'];
$default = $this->config( 'admin/jqadm/type/cms/lists/fields', $default );
$fields = $this->session( 'aimeos/admin/jqadm/type/cms/lists/fields', $default );

$searchParams = $params = $this->get( 'pageParams', [] );
$searchParams['page']['start'] = 0;

$searchAttributes = map( $this->get( 'filterAttributes', [] ) )->filter( function( $item ) {
	return $item->isPublic();
} )->call( 'toArray' )->each( function( &$val ) {
	$val = $this->translate( 'admin/ext', $val['label'] ?? ' ' );
} )->all();

$operators = map( $this->get( 'filterOperators/compare', [] ) )->flip()->map( function( $val, $key ) {
	return $this->translate( 'admin/ext', $key );
} )->all();

$columnList = [
	'cms.lists.type.id' => $this->translate( 'admin', 'ID' ),
	'cms.lists.type.domain' => $this->translate( 'admin', 'Domain' ),
	'cms.lists.type.status' => $this->translate( 'admin', 'Status' ),
	'cms.lists.type.code' => $this->translate( 'admin', 'Code' ),
	'cms.lists.type.label' => $this->translate( 'admin', 'Label' ),
	'cms.lists.type.position' => $this->translate( 'admin', 'Position' ),
	'cms.lists.type.ctime' => $this->translate( 'admin', 'Created' ),
	'cms.lists.type.mtime' => $this->translate( 'admin', 'Modified' ),
	'cms.lists.type.editor' => $this->translate( 'admin', 'Editor' ),
];

?>
<?php $this->block()->start( 'jqadm_content' ) ?>

<?= $this->partial( $this->config( 'admin/jqadm/partial/navsearch', 'navsearch' ) ) ?>
<?= $this->partial( $this->config( 'admin/jqadm/partial/columns', 'columns' ) ) ?>

<div class="list-view"
	data-domain="cms/lists/type"
	data-items="<?= $enc->attr( $this->get( 'items', map() )->call( 'toArray' )->all() ) ?>">

<nav class="main-navbar">

	<span class="navbar-brand">
		<?= $enc->html( $this->translate( 'admin', 'CMS Lists Types' ) ) ?>
		<span class="navbar-secondary">(<?= $enc->html( $this->site()->label() ) ?>)</span>
	</span>

	<div class="btn icon act-search" v-on:click="search = true"
		title="<?= $enc->attr( $this->translate( 'admin', 'Show search form' ) ) ?>"
		aria-label="<?= $enc->attr( $this->translate( 'admin', 'Show search form' ) ) ?>">
	</div>
</nav>

<nav-search v-bind:show="search" v-on:close="search = false"
	v-bind:url="`<?= $enc->js( $this->link( 'admin/jqadm/url/search', map( $searchParams )->except( 'filter' )->all() ) ) ?>`"
	v-bind:filter="<?= $enc->attr( $this->session( 'aimeos/admin/jqadm/type/cms/lists/filter', [] ) ) ?>"
	v-bind:operators="<?= $enc->attr( $operators ) ?>"
	v-bind:name="`<?= $enc->js( $this->formparam( ['filter', '_key_', '0'] ) ) ?>`"
	v-bind:attributes="<?= $enc->attr( $searchAttributes ) ?>">
</nav-search>

<?= $this->partial(
		$this->config( 'admin/jqadm/partial/pagination', 'pagination' ),
		['pageParams' => $params, 'pos' => 'top', 'total' => $this->get( 'total' ),
		'page' => $this->session( 'aimeos/admin/jqadm/type/cms/lists/page', [] )]
	);
?>

<form ref="form" class="list list-cms-lists-type" method="POST"
	action="<?= $enc->attr( $this->url( $target, $controller, $action, $searchParams, [], $config ) ) ?>"
	data-deleteurl="<?= $enc->attr( $this->url( $delTarget, $delCntl, $delAction, $params, [], $delConfig ) ) ?>">

	<?= $this->csrf()->formfield() ?>

	<column-select tabindex="<?= $this->get( 'tabindex', 1 ) ?>"
		name="<?= $enc->attr( $this->formparam( ['fields', ''] ) ) ?>"
		v-bind:titles="<?= $enc->attr( $columnList ) ?>"
		v-bind:fields="<?= $enc->attr( $fields ) ?>"
		v-bind:show="columns"
		v-on:close="columns = false">
	</column-select>

	<div class="table-responsive">
		<table class="list-items table table-hover table-striped">
			<thead class="list-header">
				<tr>
					<th class="select">
						<a href="#" class="btn act-delete icon" tabindex="1"
							v-on:click.prevent.stop="askDelete()"
							title="<?= $enc->attr( $this->translate( 'admin', 'Delete selected entries' ) ) ?>"
							aria-label="<?= $enc->attr( $this->translate( 'admin', 'Delete' ) ) ?>">
						</a>
					</th>

					<?= $this->partial(
							$this->config( 'admin/jqadm/partial/listhead', 'listhead' ),
							['fields' => $fields, 'params' => $params, 'data' => $columnList, 'sort' => $this->session( 'aimeos/admin/jqadm/type/cms/lists/sort' )]
						);
					?>

					<th class="actions">
						<a class="btn icon act-add" tabindex="1"
							href="<?= $enc->attr( $this->url( $newTarget, $newCntl, $newAction, $params, [], $newConfig ) ) ?>"
							title="<?= $enc->attr( $this->translate( 'admin', 'Insert new entry (Ctrl+I)' ) ) ?>"
							aria-label="<?= $enc->attr( $this->translate( 'admin', 'Add' ) ) ?>">
						</a>

						<a class="btn act-columns icon" href="#" tabindex="<?= $this->get( 'tabindex', 1 ) ?>"
							title="<?= $enc->attr( $this->translate( 'admin', 'Columns' ) ) ?>"
							v-on:click.prevent.stop="columns = true">
						</a>
					</th>
				</tr>
			</thead>
			<tbody>

				<?= $this->partial(
					$this->config( 'admin/jqadm/partial/listsearch', 'listsearch' ), [
						'fields' => array_merge( $fields, ['select'] ), 'filter' => $this->session( 'aimeos/admin/jqadm/type/cms/lists/filter', [] ),
						'data' => [
							'cms.lists.type.id' => ['op' => '=='],
							'cms.lists.type.domain' => ['op' => '==', 'type' => 'select', 'val' => [
								'media' => $this->translate( 'admin', 'media' ),
								'text' => $this->translate( 'admin', 'text' ),
							]],
							'cms.lists.type.status' => ['op' => '==', 'type' => 'select', 'val' => [
								'1' => $this->translate( 'mshop/code', 'status:1' ),
								'0' => $this->translate( 'mshop/code', 'status:0' ),
								'-1' => $this->translate( 'mshop/code', 'status:-1' ),
								'-2' => $this->translate( 'mshop/code', 'status:-2' ),
							]],
							'cms.lists.type.code' => [],
							'cms.lists.type.label' => [],
							'cms.lists.type.position' => ['op' => '>=', 'type' => 'number'],
							'cms.lists.type.ctime' => ['op' => '-', 'type' => 'datetime-local'],
							'cms.lists.type.mtime' => ['op' => '-', 'type' => 'datetime-local'],
							'cms.lists.type.editor' => [],
						]
					] );
				?>

				<?php foreach( $this->get( 'items', [] ) as $id => $item ) : ?>
					<?php $url = $enc->attr( $this->url( $getTarget, $getCntl, $getAction, ['id' => $id] + $params, [], $getConfig ) ) ?>
					<tr class="list-item <?= $this->site()->mismatch( $item->getSiteId() ) ?>" data-label="<?= $enc->attr( $item->getLabel() ) ?>">
						<td class="select"><input v-on:click="toggle(`<?= $enc->js( $id ) ?>`)" v-bind:checked="items[`<?= $enc->js( $id ) ?>`].checked" class="form-check-input" type="checkbox" tabindex="1" name="<?= $enc->attr( $this->formparam( ['id', ''] ) ) ?>" value="<?= $enc->attr( $item->getId() ) ?>"></td>
						<?php if( in_array( 'cms.lists.type.id', $fields ) ) : ?>
							<td class="cms-type-id"><a class="items-field" href="<?= $url ?>"><?= $enc->html( $item->getId() ) ?></a></td>
						<?php endif ?>
						<?php if( in_array( 'cms.lists.type.domain', $fields ) ) : ?>
							<td class="cms-type-domain"><a class="items-field" href="<?= $url ?>"><?= $enc->html( $item->getDomain() ) ?></a></td>
						<?php endif ?>
						<?php if( in_array( 'cms.lists.type.status', $fields ) ) : ?>
							<td class="cms-type-status"><a class="items-field" href="<?= $url ?>"><div class="icon status-<?= $enc->attr( $item->getStatus() ) ?>"></div></a></td>
						<?php endif ?>
						<?php if( in_array( 'cms.lists.type.code', $fields ) ) : ?>
							<td class="cms-type-code"><a class="items-field" href="<?= $url ?>" tabindex="1"><?= $enc->html( $item->getCode() ) ?></a></td>
						<?php endif ?>
						<?php if( in_array( 'cms.lists.type.label', $fields ) ) : ?>
							<td class="cms-type-label"><a class="items-field" href="<?= $url ?>"><?= $enc->html( $item->getLabel() ) ?></a></td>
						<?php endif ?>
						<?php if( in_array( 'cms.lists.type.position', $fields ) ) : ?>
							<td class="cms-type-position"><a class="items-field" href="<?= $url ?>"><?= $enc->html( $item->getPosition() ) ?></a></td>
						<?php endif ?>
						<?php if( in_array( 'cms.lists.type.ctime', $fields ) ) : ?>
							<td class="cms-type-ctime"><a class="items-field" href="<?= $url ?>"><?= $enc->html( $item->getTimeCreated() ) ?></a></td>
						<?php endif ?>
						<?php if( in_array( 'cms.lists.type.mtime', $fields ) ) : ?>
							<td class="cms-type-mtime"><a class="items-field" href="<?= $url ?>"><?= $enc->html( $item->getTimeModified() ) ?></a></td>
						<?php endif ?>
						<?php if( in_array( 'cms.lists.type.editor', $fields ) ) : ?>
							<td class="cms-type-editor"><a class="items-field" href="<?= $url ?>"><?= $enc->html( $item->editor() ) ?></a></td>
						<?php endif ?>

						<td class="actions">
							<a class="btn act-copy icon" tabindex="1"
								href="<?= $enc->attr( $this->url( $copyTarget, $copyCntl, $copyAction, ['id' => $id] + $params, [], $copyConfig ) ) ?>"
								title="<?= $enc->attr( $this->translate( 'admin', 'Copy this entry' ) ) ?>"
								aria-label="<?= $enc->attr( $this->translate( 'admin', 'Copy' ) ) ?>">
							</a>
							<?php if( !$this->site()->readonly( $item->getSiteId() ) ) : ?>
								<a class="btn act-delete icon" tabindex="1"
									v-on:click.prevent.stop="askDelete(`<?= $enc->js( $id ) ?>`, $event)"
									href="<?= $enc->attr( $this->link( 'admin/jqadm/url/delete', $params ) ) ?>"
									title="<?= $enc->attr( $this->translate( 'admin', 'Delete this entry' ) ) ?>"
									aria-label="<?= $enc->attr( $this->translate( 'admin', 'Delete' ) ) ?>">
								</a>
							<?php endif ?>
						</td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</div>

	<?php if( $this->get( 'items', map() )->isEmpty() ) : ?>
		<div class="noitems"><?= $enc->html( sprintf( $this->translate( 'admin', 'No items found' ) ) ) ?></div>
	<?php endif ?>
</form>

<?= $this->partial(
		$this->config( 'admin/jqadm/partial/pagination', 'pagination' ),
		['pageParams' => $params, 'pos' => 'bottom', 'total' => $this->get( 'total' ),
		'page' => $this->session( 'aimeos/admin/jqadm/type/cms/lists/page', [] )]
	);
?>

<confirm-delete v-bind:items="unconfirmed" v-bind:show="dialog"
	v-on:close="confirmDelete(false)" v-on:confirm="confirmDelete(true)"></confirm-delete>

</div>
<?php $this->block()->stop() ?>

<?= $this->render( $this->config( 'admin/jqadm/template/page', 'page' ) ) ?>
