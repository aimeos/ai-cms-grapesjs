<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */

/* Available data:
 * - pageErrorList : List of errors
 */


$enc = $this->encoder();


?>
<section class="aimeos cms-page" data-jsonurl="<?= $enc->attr( $this->link( 'client/jsonapi/url' ) ) ?>">

	<h1 class="error-header"><?= $enc->html( $this->translate( 'client', 'Error' ) ) ?></h1>
	<p class="error-message"><?= $enc->html( $this->translate( 'client', 'The page is not available' ) ) ?></p>

	<div class="button-group">
		<a class="btn btn-primary" href="/"><?= $enc->html( $this->translate( 'client', 'Back' ) ) ?></a>
	</div>
</section>
