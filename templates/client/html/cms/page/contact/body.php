<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */

$enc = $this->encoder();


?>
<form class="contact-form" method="POST">
	<!-- cms.page.contact.csrf -->
	<?= $this->csrf()->formfield() ?>
	<!-- cms.page.contact.csrf -->
	<div class="form-group row contact-name">
		<label class="col-sm-4 form-control-label"><?= $enc->html( $this->translate( 'client', 'Name' ) ) ?></label>
		<div class="col-sm-8"><input class="form-control" name="<?= $enc->attr( $this->formparam( ['contact', 'name'] ) ) ?>"></div>
	</div>
	<div class="form-group row contact-email">
		<label class="col-sm-4 form-control-label"><?= $enc->html( $this->translate( 'client', 'E-Mail' ) ) ?></label>
		<div class="col-sm-8"><input class="form-control" name="<?= $enc->attr( $this->formparam( ['contact', 'email'] ) ) ?>" type="email" required></div>
	</div>
	<div class="form-group row contact-message">
		<label class="col-sm-4 form-control-label"><?= $enc->html( $this->translate( 'client', 'Message' ) ) ?></label>
		<div class="col-sm-8"><textarea class="form-control" name="<?= $enc->attr( $this->formparam( ['contact', 'message'] ) ) ?>" required rows="6"></textarea></div>
	</div>
	<div class="contact-pot" style="display: none">
		<input name="<?= $enc->attr( $this->formparam( ['contact', 'url'] ) ) ?>" tabindex="-1" autocomplete="off">
	</div>
	<div class="form-group contact-button">
		<button type="submit" class="btn btn-primary d-block mx-auto"><?= $enc->html( $this->translate( 'client', 'Submit' ) ) ?></button>
	</div>
</form>
