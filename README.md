# woocommerce-moreplease

## Description

WooCommerce plugin for [moreplease.io](https://moreplease.io).

## Product Synchronization

Products and product variations are pulled into MorePlease on a regular basis. MorePlease connects to this plugin to routinely fetch product/variant details, updating its database as needed.

### Product Synchronization: Controlling Products

MorePlease product synchronization can be controlled by adjusting the product and product variation status and visibility settings in WordPress. These settings are controlled within the WP admin, under the "Publish" section of the product edit page.

- **Draft Status:** Products/Variable Products in draft status are not synched with MorePlease. If a product was previously synched with MP then put into draft status, it will be removed from MP, along with all associated variations. Customer subscriptions that previously had this product in them will be updated to remove this product and its variations completely, with no notice to customers.

- **Publish Status:** Products/Variable Products in publish status will be synched with MorePlease and set to have an "Active" status. By default this includes all product variations, but can be overridden at the variation level by unchecking a variants "Enabled" option. If a product variation is disabled by unchecking the "Enabled" option, it will still be synched into MP, but will be set with a status of "Inactive".

- **Private Visibility:** Products/Variable Products with private visibility will be synched into MorePlease and set to have a status of "Inactive". This includes all variants and cannot be overridden at the variant level (ie. toggling the "Enabled" checkbox will have no effect). Variants will be synched with MP in "Inactive" status as well.

### Product Synchronization: Subscription Scenarios

1. A product was synched into MorePlease and added to customer subscriptions, that should never have been released. We need it completely removed from all subscriptions, along with any variants, right away.

Solution: Put the top level product in draft status in WordPress (or remove it from WP completely). This will completely remove it and all variants from MorePlease when the synch runs next.

2. A specific product variation that several customers have added to their subscriptions, has been discontinued.

Solution: Mark the individual product variation as disabled in the WP admin by unchecking the "Enabled" checkbox for that specific variation. When the MP product synch runs, it will mark this specific product variation as "Inactive" in its database. The next time customers view their subscription editor, they will still see the previously selected variation, but it will have a zeroed amount, and will show a note mentioning they should pick a new variation.

3. A product is no longer available so we don't want it to be available in our store. We still want customers to have that product listed on their subscription and be billed for it however, at which point we'll suggest or provide an alternative when we pack their order.

Solution: First set the top level product to have "Private" visibility in the WP admin (this will completely hide the product from the storefront), but keep it published. Then in the MorePlease admin under Settings > Store, make sure the "Bill for inactive products" option is checked. When this option is checked products marked as inactive will still show in a customers subscription editor with the normal price, with no indication that this product is no longer available. When the subscription renews customers will be billed for this product.
