=== Sezzle Woocommerce Payment ===
Contributors: sezzledev
Tags: sezzle, installments, payments, paylater
Requires at least: 5.3.2
Version: 4.0.8
Stable tag: 4.0.8
Tested up to: 6.0.3
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Sezzle is an alternative payment platform that increases sales and basket sizes by enabling your customers to 'buy now and pay later' with interest-free installment plans. Sezzle collects 25% of the purchase price from the consumer at the time of purchase, but pays the merchant the full purchase price upfront, less their fee. The merchant assumes no credit or fraud risk. Sezzle then schedules three additional installments of 25% to be automatically debited from the consumer every two weeks, completely interest- and fee-free.

Sezzle is ideal for merchants that cater to young consumers, with order values typically between $50-$750, although we do accept orders up to $1,000. Sezzle increases consumers' purchasing power and is most heavily used by those without a credit card (63% of millennials do not own a credit card), or those with a very low credit limit on their card. However, a wide range of consumers use Sezzle, both in terms of age and financial situation.

Our extension includes a payment gateway, which will enable Sezzle as a payment option in your checkout. Once a customer selects Sezzle as their payment option, they are redirected to our secure checkout, after which they are taken back to your store to view purchase details. The extension also includes a widget that displays a dynamic installment amount on your product and cart pages, which is proven to increase conversions and basket sizes.


## Installation

1. Signup for Sezzle at [US/CA](https://dashboard.sezzle.com/merchant/signup/) or [EU](https://dashboard.eu.sezzle.com/merchant/signup/). Login to your dashboard and keep your API Keys page open. You will need it in step `5`.
2. Make sure you have WooCommerce plugin installed.
3. Install the Sezzle Payment plugin and activate.
4. Go to admin > WooCommerce > Settings > Payments > Sezzle.
5. Fill the form according to the instructions given in the form and save it.


### Your store is ready to use Sezzle as a payment gateway.

For more information, please visit [Sezzle Docs](https://docs.sezzle.com/sezzle-integration/docs/woocommerce).

== Changelog ==

= 4.0.8 =
* Send admin configuration details to sezzle.

= 4.0.7 =
* Replaced installment widget static JS with CDN JS.

= 4.0.6 =
* FIX: Widget default configurations updated.

= 4.0.5 =
* FIX: Order total rounding.

= 4.0.4 =
* FIX: Checkout widget not appearing on shipping option change.

= 4.0.3 =
* Sending platform and plugin details(name and version) to Sezzle for tracking/debugging purpose.

= 4.0.2 =
* Plugin description updated.

= 4.0.1 =
* WordPress 5.9 and WooCommerce 6.1.1 compatibility added.

= 4.0.0 =
* FEATURE: Multi site support.

= 3.1.15 =
* MODIFY: Updated the description to showcase US, CA and EU.
* MODIFY: Update FR translations

= 3.1.14 =
* FEATURE: Add support for internationalization
* FEATURE: Add translation support for DE, ES and FR languages

= 3.1.13 =
* FEATURE: Add support for internationalization
* FEATURE: Add translation support for DE, ES and FR languages

= 3.1.12 =
* MODIFY: Sending Sezzle checkout URL to WooCommerce for instant redirection to Sezzle Checkout.

= 3.1.11 =
* FIX: Merchants receiving error "Sezzle: Unable to authenticate." on WooCommerce plugin.

= 3.1.10 =
* FIX: Checkout breaking while WooCommerce PayPal Payments is active.

= 3.1.9 =
* FIX: Sezzle URL Fix.

= 3.1.8 =
* FEATURE: Default Widget configs.

= 3.1.7 =
* FIX: Complete URL mismatch for multi network sites like https://example.com/ca/fr etc.
* FIX: Gateway region management improvisation.

= 3.1.6 =
* FEATURE: Compatibility for EU region.
* FEATURE: Widget Script will not be served if merchant id is missing.

= 3.1.5 =
* FIX: Assigning order currency to checkout and refund.
* FEATURE: Ability to turn on/off syncing analytical data.

= 3.1.4 =
* FIX: Sqaure and Stripe Payment Method Form blocking.
* FEATURE: Ability to turn on/off installment widget plan from Sezzle settings.

= 3.1.3 =
* FIX: Multiple Installment Widget.

= 3.1.2 =
* FEATURE: Installment Plan Widget under Sezzle Payment Option in Checkout Page.
* FIX: Admin check added in gateway hiding function.

= 3.1.1 =
* FIX: Failing of sudden orders being already captured.
* FEATURE: Ability to turn on/off logging.

= 3.1.0 =
* MODIFY: Transaction Mode added instead of Sezzle API URL.

= 3.0.5 =
* FIX: Undefined index:Authorization during redirection to Sezzle.

= 3.0.4 =
* MODIFY: Updated User Guide.

= 3.0.3 =
* MODIFY: Updated Widget Script URL.

= 3.0.2 =
* FIX: Order key property access through function instead of direct access.

= 3.0.1 =
* FIX: Return URL from Sezzle Checkout changed to Checkout URL of merchant website.
* FEATURE: Added logs for checking API functions.
* FIX: Check payment capture status before capturing the payment so that already captured orders does not fall into the process.

= 3.0.0 =
* FIX: Downgraded to previous stable version due to some conflicts arising in few versions.
* MODIFY: Delayed capture has been removed.
* MODIFY: Widget in Cart has been removed.

= 2.0.9 =
* FIX: Added check to include settings class when not available.

= 2.0.8 =
* MODIFY: Wordpress support version has been changed to 4.4.0 or higher.

= 2.0.7 =
* FEATURE: Hiding of Sezzle Pay based on cart total.
* FEATURE: Sezzle Widget and Sezzle Payment merged into one plugin.
* FIX : Amount converted to cents while refund.

= 2.0.6 =
* FIX: Page hanging issue during order status change for other payment methods.

= 2.0.5 =
* FIX: Security fix and quality improvements.

= 2.0.4 =
* FEATURE: Delayed Capture.
* FEATURE: Sezzle Widget for Cart Page.
* FEATURE: New settings for managing Sezzle Widget.

== Upgrade Notice ==

= 4.0.8 =
* Sending sezzle enable status, widget status to Sezzle to better assist any issues.

= 4.0.7 =
* Installment widget under Sezzle payment option in checkout page will now be served from Sezzle CDN JS.

= 4.0.6 =
* Revamped widget configurations covering multiple edge cases.

= 4.0.5 =
* Order total mismatch will now get fixed for certain scenarios.

= 4.0.4 =
* Checkout widget will now update on shipping option change.

= 4.0.3 =
* No major changes in the checkout flow. The platform details will help us debug any issue quickly.

= 4.0.2 =
* Merchants will mow see the updated description in the WordPress marketplace.

= 4.0.1 =
* Merchants can now enable Sezzle in WordPress 5.1.1 and WooCommerce 6.1.1.

= 4.0.0 =
* Merchants can now enable Sezzle in a multi site wordpress network.

= 3.1.15 =
* Merchants will read the description now supporting US, CA and EU.
* Update FR translations

= 3.1.14 =
* Users using Sezzle on stores with FR, ES or DE languages enabled will see a localized gateway.

= 3.1.13 =
* Users using Sezzle on stores with FR, ES or DE languages enabled will see a localized gateway.

= 3.1.12 =
* Users will experience a instant redirection to Sezzle Checkout on starting a checkout with Sezzle.

= 3.1.11 =
* Merchants won't be receiving any error like "Sezzle: Unable to authenticate." on WooCommerce plugin.

= 3.1.10 =
* Checkout will work as expected while WooCommerce PayPal Payments is active.

= 3.1.9 =
* Sezzle Gateway and Widget URL Fix.

= 3.1.8 =
* Default widget configurations for woocommerce merchant installed.

= 3.1.7 =
* Redirection to store from Sezzle Checkout should work fine now for multi network sites like https://example.com/ca/fr, https://example.com/ca/en etc.

= 3.1.6 =
* EU users will now be able to use this plugin.
* Merchant ID is required for widget script to show up in PDP.

= 3.1.5 =
* This will fix issues where store has multi currency.
* User can turn on/off analytical data sync.

= 3.1.4 =
* This will fix the Sqaure and Stripe Payment Method Form blocking issue in the Checkout Page.
* User can decide on turning on/off the installment widget plan from Sezzle settings in WooCommerce Dashboard.

= 3.1.3 =
* User will not see multiple installment widget on changing shipping addresses.

= 3.1.2 =
* User will be able to visualize Sezzle installment plan while in Checkout Page.
* Admin Check added for Gateway removal process.

= 3.1.1 =
* Orders will not get failed if it is found captured.
* User can turn on/off Sezzle logging.

= 3.1.0 =
* User can select between LIVE and SANDBOX mode instead of adding the URL.

= 3.0.5 =
* The fix is on the logging of data and mainly linked to checkout process. Checkout should work fine now for those who were experiencing issues while redirection.

= 3.0.4 =
* Updated User Guide.

= 3.0.3 =
* Sezzle will control the configuration of widget if you upgrade to this version.

= 3.0.2 =
* This is reflected in the order confirmation url.

= 3.0.1 =
* When user will try to get back from Sezzle Checkout, they will get a seamless redirection to Checkout Page.
* Logs will be generated from now onwards to track the Checkout Flow.
* Capture status will be checked programmatically to avoid recapturing of captured orders.

= 3.0.0 =
* Downgraded to previous stable version due to some conflicts arising in few versions.
* Delayed capture has been removed.
* Widget in Cart has been removed.

= 2.0.8 =
* Wordpress support version has been changed to 4.4.0 or higher.

= 2.0.7 =
* If you have a requirement of hiding Sezzle Pay based on cart total, upgrade to this version.
* Fixes on amount conversion during refund.
* Conflict issue with Aweber plugin and as a result, Sezzle Widget and Sezzle Payment merged into one plugin. No need to activate two plugins now. Only one will show up as Sezzle WooCommerce Payment.

= 2.0.6 =
* This version fixes a major bug.  Upgrade immediately.

= 2.0.5 =
* This version fixes security bug.  Upgrade immediately.

= 2.0.4 =
This version has the following major features included.
* Delayed Capture.
* Sezzle Widget for Cart Page.
* New settings for managing Sezzle Widget.

