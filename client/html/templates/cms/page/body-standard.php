<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */

/* Available data:
 * - pageCmsItem : Cms page item incl. referenced items
 */


$enc = $this->encoder();


?>
<section class="aimeos cms-page container-fluid px-0" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ); ?>">

	<?php if( isset( $this->pageErrorList ) ) : ?>
		<ul class="error-list">
			<?php foreach( (array) $this->pageErrorList as $errmsg ) : ?>
				<li class="error-item"><?= $enc->html( $errmsg ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php foreach( $this->get( 'pageContent', [] ) as $content ) : ?>
		<?= $content ?>
	<?php endforeach ?>

</section>
