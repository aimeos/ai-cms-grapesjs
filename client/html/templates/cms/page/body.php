<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2022
 */

/* Available data:
 * - pageCmsItem : Cms page item incl. referenced items
 */


$enc = $this->encoder();


?>
<section class="aimeos cms-page container-fluid" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ); ?>">

	<?php foreach( $this->get( 'pageContent', [] ) as $content ) : ?>
		<?= $content ?>
	<?php endforeach ?>

</section>
