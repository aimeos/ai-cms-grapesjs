<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
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


?>
<div class="catalog-list">
	<div class="catalog-list-items">

		<?= $this->partial(
			$this->config( 'client/html/common/partials/products', 'common/partials/products-standard' ),
			array(
				'require-stock' => (int) $this->config( 'client/html/basket/require-stock', true ),
				'basket-add' => $this->config( 'client/html/cms/page/basket-add', false ),
				'products' => $this->get( 'products', map() ),
			)
		) ?>

	</div>
</div>
