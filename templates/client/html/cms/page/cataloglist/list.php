<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021-2022
 */


/** client/html/cms/page/basket-add
 * Display the "add to basket" button for each product item
 *
 * Enables the button for adding products to the basket for the products in CMS
 * pages. This works for all type of products, even for selection products
 * with product variants and product bundles. By default, also optional attributes
 * are displayed if they have been associated to a product.
 *
 * To fetch the variant articles of selection products too, add this setting to
 * your configuration:
 *
 * mshop/common/manager/maxdepth = 3
 *
 * @param boolean True to display the button, false to hide it
 * @since 2021.07
 * @see client/html/catalog/home/basket-add
 * @see client/html/catalog/lists/basket-add
 * @see client/html/catalog/detail/basket-add
 * @see client/html/catalog/product/basket-add
 * @see client/html/basket/related/basket-add
 */

$enc = $this->encoder();


?>
<div class="catalog-list swiffy-slider slider-nav-outside">
	<div class="catalog-list-items product-list slider-container">

		<?= $this->partial(
			$this->config( 'client/html/common/partials/products', 'common/partials/products' ),
			array(
				'require-stock' => (int) $this->config( 'client/html/basket/require-stock', true ),
				'basket-add' => $this->config( 'client/html/cms/page/basket-add', false ),
				'products' => $this->get( 'products', map() ),
			)
		) ?>

	</div>

	<button type="button" class="slider-nav" aria-label="Go to previous"></button>
	<button type="button" class="slider-nav slider-nav-next" aria-label="Go to next"></button>

	<?php if( isset( $this->itemsStockUrl ) ) : ?>
		<?php foreach( $this->itemsStockUrl as $url ) : ?>
			<script class="items-stock" defer src="<?= $enc->attr( $url ) ?>"></script>
		<?php endforeach ?>
	<?php endif ?>

</div>
