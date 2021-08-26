<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */

$enc = $this->encoder();

$target = $this->config( 'client/html/cms/page/url/target' );
$cntl = $this->config( 'client/html/cms/page/url/controller', 'cms' );
$action = $this->config( 'client/html/cms/page/url/action', 'page' );
$config = $this->config( 'client/html/cms/page/url/config', [] );


/** client/html/cms/page/metatags
 * Adds the title, meta and link tags to the HTML header
 *
 * By default, each instance of the cms list component adds some HTML meta
 * tags to the page head section, like page title, meta keywords and description
 * as well as some link tags to support browser navigation. If several instances
 * are placed on one page, this leads to adding several title and meta tags used
 * by search engine. This setting enables you to suppress these tags in the page
 * header and maybe add your own to the page manually.
 *
 * @param boolean True to display the meta tags, false to hide it
 * @since 2021.01
 * @category Developer
 * @category User
 * @see client/html/cms/lists/metatags
 */


?>
<?php if( isset( $this->pageCmsItem ) ) : ?>

	<?php if( (bool) $this->config( 'client/html/cms/page/metatags', true ) === true ) : ?>

		<title><?= $enc->html( strip_tags( $this->pageCmsItem->getName() ) ) ?> | <?= $enc->html( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?></title>

		<link rel="canonical" href="<?= $enc->attr( $this->url( $target, $cntl, $action, ['path' => $this->pageCmsItem->getUrl()], $config + ['absoluteUri' => true] ) ); ?>" />

		<meta property="og:type" content="article" />
		<meta property="og:title" content="<?= $enc->attr( $this->pageCmsItem->getName() ); ?>" />
		<meta property="og:url" content="<?= $enc->attr( $this->url( $target, $cntl, $action, ['path' => $this->pageCmsItem->getUrl()], $config + ['absoluteUri' => true] ) ); ?>" />

		<?php foreach( $this->pageCmsItem->getRefItems( 'media', 'default', 'default' ) as $mediaItem ) : ?>
			<meta property="og:image" content="<?= $enc->attr( $this->content( $mediaItem->getUrl() ) ) ?>" />
		<?php endforeach ?>

		<?php foreach( $this->pageCmsItem->getRefItems( 'text', 'meta-description', 'default' ) as $textItem ) : ?>
			<meta property="og:description" content="<?= $enc->attr( $textItem->getContent() ) ?>" />
			<meta name="description" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ); ?>" />
		<?php endforeach ?>

		<?php foreach( $this->pageCmsItem->getRefItems( 'text', 'meta-keyword', 'default' ) as $textItem ) : ?>
			<meta name="keywords" content="<?= $enc->attr( strip_tags( $textItem->getContent() ) ); ?>" />
		<?php endforeach; ?>

		<meta name="twitter:card" content="summary_large_image" />

	<?php endif; ?>

<?php elseif( strcmp( $this->param( 'path', '/' ), '/' ) > 0 ) : ?>

	<title><?= $enc->html( $this->get( 'contextSiteLabel', 'Aimeos' ) ) ?></title>

	<meta name="application-name" content="Aimeos" />
	<meta http-equiv="refresh" content="0;URL='/'" />
	<meta name="robots" content="noindex" />

<?php endif; ?>

<?= $this->get( 'pageHeader' ); ?>
