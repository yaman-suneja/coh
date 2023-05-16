jQuery(document).ready( function($) {

	if( '' !== wcgb_checkout.order_key && '' !== wcgb_checkout.order_token ) {
		let checkoutToken  = wcgb_checkout.order_token;
		let order_id       = wcgb_checkout.order_id;
		let order_key      = wcgb_checkout.order_key;

		let do_redirect    = true;

		return window.blnceCheckout
			.create({
				// The token that returned from the server API
				checkoutToken,
				url: 'https://checkout.sandbox.getbalance.com/checkout.html',
				type: 'checkout',
				hideBackOnFirstScreen: false,
				logoURL: 'https://my.marketplace.com/logo.png',
				isAuth: false,

				onComplete: (result) => {
					// This method is called with the checkout result
					console.log('checkout status: ', result.status); // checkout status: completed
				},
				onError: (error) => {
					console.error('error', error);
				},
				callback: (err, msg) => {
					console.log('msg from the iframe -', msg);
					console.log('err', err);
				},
				onClose: () => {
					if ( do_redirect ) {
						window.blnceCheckout.destroy();
						let redirection_url = wcgb_checkout.url+'/order-pay/'+order_id+'/?pay_for_order=true&key='+order_key;
						window.location.replace(redirection_url);
					}
				},
				onSuccess: () => {
					$.ajax({
						type: 'POST',
						url: wcgb_checkout.wcgb_ajax_url,
						async: false,
						data: {
							'action': 'wcgb_mark_order_payment',
							'checkoutToken': checkoutToken,
							'order_key': order_key,
						},
						success: (response) => {
							
							do_redirect = false; //doing this to disable on close redirection once the order is paid.

							let redirection_url = wcgb_checkout.url+'/order-received/'+order_id+'/?key='+order_key+'&status=paid';
							window.location.replace(redirection_url);
						}
					});
				}
			})
			// This element should exist on the dom
			.render('#blnce-checkout');
	}
});
