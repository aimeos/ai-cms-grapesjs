<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */

$selected = function( $key, $code ) {
	return ( $key == $code ? 'selected="selected"' : '' );
};


$enc = $this->encoder();

$target = $this->config( 'admin/jqadm/url/save/target' );
$cntl = $this->config( 'admin/jqadm/url/save/controller', 'Jqadm' );
$action = $this->config( 'admin/jqadm/url/save/action', 'save' );
$config = $this->config( 'admin/jqadm/url/save/config', [] );

$getTarget = $this->config( 'admin/jqadm/url/get/target' );
$getCntl = $this->config( 'admin/jqadm/url/get/controller', 'Jqadm' );
$getAction = $this->config( 'admin/jqadm/url/get/action', 'get' );
$getConfig = $this->config( 'admin/jqadm/url/get/config', [] );

$listTarget = $this->config( 'admin/jqadm/url/search/target' );
$listCntl = $this->config( 'admin/jqadm/url/search/controller', 'Jqadm' );
$listAction = $this->config( 'admin/jqadm/url/search/action', 'search' );
$listConfig = $this->config( 'admin/jqadm/url/search/config', [] );

$newTarget = $this->config( 'admin/jqadm/url/create/target' );
$newCntl = $this->config( 'admin/jqadm/url/create/controller', 'Jqadm' );
$newAction = $this->config( 'admin/jqadm/url/create/action', 'create' );
$newConfig = $this->config( 'admin/jqadm/url/create/config', [] );

$jsonTarget = $this->config( 'admin/jsonadm/url/target' );
$jsonCntl = $this->config( 'admin/jsonadm/url/controller', 'Jsonadm' );
$jsonAction = $this->config( 'admin/jsonadm/url/action', 'get' );
$jsonConfig = $this->config( 'admin/jsonadm/url/config', [] );

$params = $this->get( 'pageParams', [] );


?>
<?php $this->block()->start( 'jqadm_content' ) ?>

<form class="item item-cms item-tree form-horizontal container-fluid" method="POST" enctype="multipart/form-data"
	action="<?= $enc->attr( $this->url( $target, $cntl, $action, $params, [], $config ) ) ?>"
	data-rootid="<?= $enc->attr( $this->get( 'itemRootId' ) ) ?>"
	data-geturl="<?= $enc->attr( $this->url( $getTarget, $getCntl, $getAction, ['resource' => 'cms', 'id' => '_ID_'] + $params, [], $getConfig ) ) ?>"
	data-createurl="<?= $enc->attr( $this->url( $newTarget, $newCntl, $newAction, ['resource' => 'cms', 'id' => '_ID_'] + $params, [], $newConfig ) ) ?>"
	data-jsonurl="<?= $enc->attr( $this->url( $jsonTarget, $jsonCntl, $jsonAction, ['resource' => 'cms'], [], $jsonConfig ) ) ?>"
	data-idname="<?= $this->formparam( 'id' ) ?>" >

	<input id="item-id" type="hidden" name="<?= $enc->attr( $this->formparam( array( 'item', 'cms.id' ) ) ) ?>"
		value="<?= $enc->attr( $this->get( 'itemData/cms.id' ) ) ?>" />
	<input id="item-parentid" type="hidden" name="<?= $enc->attr( $this->formparam( array( 'item', 'cms.parentid' ) ) ) ?>"
		value="<?= $enc->attr( $this->get( 'itemData/cms.parentid', $this->param( 'parentid', $this->param( 'id', $this->get( 'itemRootId' ) ) ) ) ) ?>" />
	<input id="item-next" type="hidden" name="<?= $enc->attr( $this->formparam( array( 'next' ) ) ) ?>" value="get" />
	<?= $this->csrf()->formfield() ?>

	<nav class="main-navbar">
		<h1 class="navbar-brand">
			<span class="navbar-title"><?= $enc->html( $this->translate( 'admin', 'CMS page' ) ) ?></span>
			<span class="navbar-id"><?= $enc->html( $this->get( 'itemData/cms.id' ) ) ?></span>
			<span class="navbar-label"><?= $enc->html( $this->get( 'itemData/cms.label' ) ?: $this->translate( 'admin', 'New' ) ) ?></span>
			<span class="navbar-site"><?= $enc->html( $this->site()->match( $this->get( 'itemData/cms.siteid' ) ) ) ?></span>
		</h1>
		<div class="item-actions">
			<?php if( isset( $this->itemData ) ) : ?>
				<?= $this->partial( $this->config( 'admin/jqadm/partial/itemactions', 'common/partials/itemactions-standard' ), ['params' => $params] ) ?>
			<?php else : ?>
				<span class="placeholder">&nbsp;</span>
			<?php endif ?>
		</div>
	</nav>

	<div class="row item-container">

		<?php if( isset( $this->itemData ) ) : ?>
			<div class="col-xl-12 cms-content">
				<div class="row">

					<div class="col-xl-12 item-navbar">
						<div class="navbar-content">
							<ul class="nav nav-tabs flex-row flex-wrap d-flex box" role="tablist">
								<li class="nav-item basic">
									<a class="nav-link active" href="#basic" data-bs-toggle="tab" role="tab" aria-expanded="true" aria-controls="basic" tabindex="1">
										<?= $enc->html( $this->translate( 'admin', 'Basic' ) ) ?>
									</a>
								</li>

								<?php foreach( array_values( $this->get( 'itemSubparts', [] ) ) as $idx => $subpart ) : ?>
									<li class="nav-item <?= $enc->attr( $subpart ) ?>">
										<a class="nav-link" href="#<?= $enc->attr( $subpart ) ?>" data-bs-toggle="tab" role="tab" tabindex="<?= ++$idx + 1 ?>">
											<?= $enc->html( $this->translate( 'admin', $subpart ) ) ?>
										</a>
									</li>
								<?php endforeach ?>
							</ul>

							<div class="item-meta text-muted">
								<small>
									<?= $enc->html( $this->translate( 'admin', 'Modified' ) ) ?>:
									<span class="meta-value"><?= $enc->html( $this->get( 'itemData/cms.mtime' ) ) ?></span>
								</small>
								<small>
									<?= $enc->html( $this->translate( 'admin', 'Created' ) ) ?>:
									<span class="meta-value"><?= $enc->html( $this->get( 'itemData/cms.ctime' ) ) ?></span>
								</small>
								<small>
									<?= $enc->html( $this->translate( 'admin', 'Editor' ) ) ?>:
									<span class="meta-value"><?= $enc->html( $this->get( 'itemData/cms.editor' ) ) ?></span>
								</small>
							</div>

							<div class="more"></div>
						</div>
					</div>

					<div class="col-xl-12 item-content tab-content">

						<div id="basic" class="item-basic tab-pane fade show active" role="tabpanel" aria-labelledby="basic">

							<div class="box">
								<div class="row">
									<div class="col-xl-6 <?= $this->site()->readonly( $this->get( 'itemData/cms.siteid' ) ) ?>">
										<div class="form-group row mandatory">
											<label class="col-sm-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Status' ) ) ?></label>
											<div class="col-sm-8">
												<select class="form-select item-status" required="required" tabindex="1"
													name="<?= $enc->attr( $this->formparam( array( 'item', 'cms.status' ) ) ) ?>"
													<?= $this->site()->readonly( $this->get( 'itemData/cms.siteid' ) ) ?> >
													<option value="">
														<?= $enc->html( $this->translate( 'admin', 'Please select' ) ) ?>
													</option>
													<option value="1" <?= $selected( $this->get( 'itemData/cms.status', 1 ), 1 ) ?> >
														<?= $enc->html( $this->translate( 'mshop/code', 'status:1' ) ) ?>
													</option>
													<option value="0" <?= $selected( $this->get( 'itemData/cms.status', 1 ), 0 ) ?> >
														<?= $enc->html( $this->translate( 'mshop/code', 'status:0' ) ) ?>
													</option>
													<option value="-1" <?= $selected( $this->get( 'itemData/cms.status', 1 ), -1 ) ?> >
														<?= $enc->html( $this->translate( 'mshop/code', 'status:-1' ) ) ?>
													</option>
													<option value="-2" <?= $selected( $this->get( 'itemData/cms.status', 1 ), -2 ) ?> >
														<?= $enc->html( $this->translate( 'mshop/code', 'status:-2' ) ) ?>
													</option>
												</select>
											</div>
										</div>
										<div class="form-group row mandatory">
											<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'URL' ) ) ?></label>
											<div class="col-sm-8">
												<input class="form-control item-url" type="text" required="required" tabindex="1"
													name="<?= $enc->attr( $this->formparam( array( 'item', 'cms.url' ) ) ) ?>"
													placeholder="<?= $enc->attr( $this->translate( 'admin', 'Unique page URL (required)' ) ) ?>"
													value="<?= $enc->attr( $this->get( 'itemData/cms.url' ) ) ?>"
													<?= $this->site()->readonly( $this->get( 'itemData/cms.siteid' ) ) ?> />
											</div>
											<div class="col-sm-12 form-text text-muted help-text">
												<?= $enc->html( $this->translate( 'admin', 'Unique page URL, e.g. "/page-name"' ) ) ?>
											</div>
										</div>
										<div class="form-group row mandatory">
											<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Title' ) ) ?></label>
											<div class="col-sm-8">
												<input class="form-control item-label" type="text" required="required" tabindex="1"
													name="<?= $this->formparam( array( 'item', 'cms.label' ) ) ?>"
													placeholder="<?= $enc->attr( $this->translate( 'admin', 'Internal name (required)' ) ) ?>"
													value="<?= $enc->attr( $this->get( 'itemData/cms.label' ) ) ?>"
													<?= $this->site()->readonly( $this->get( 'itemData/cms.siteid' ) ) ?> />
											</div>
											<div class="col-sm-12 form-text text-muted help-text">
												<?= $enc->html( $this->translate( 'admin', 'Page title, will be used on the web site if no title for the language is available' ) ) ?>
											</div>
										</div>
									</div>

								</div>
							</div>
						</div>

						<?= $this->get( 'itemBody' ) ?>

					</div>

					<div class="item-actions">
						<?= $this->partial( $this->config( 'admin/jqadm/partial/itemactions', 'common/partials/itemactions-standard' ), ['params' => $params] ) ?>
					</div>
				</div>

			</div>

		<?php endif ?>

	</div>
</form>

<?php $this->block()->stop() ?>


<?= $this->render( $this->config( 'admin/jqadm/template/page', 'common/page-standard' ) ) ?>
