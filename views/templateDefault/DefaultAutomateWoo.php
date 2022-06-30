<?php

namespace YayMailAutomateWoo\templateDefault;

defined( 'ABSPATH' ) || exit;

class DefaultAutomateWoo {

	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function getTemplates( $customOrder, $emailHeading ) {
		/*
		@@@ Html default send email.
		@@@ Note: Add characters '\' before special characters in a string.
		@@@ Example: font-family: \'Helvetica Neue\'...
		*/

		$emailTitle        = __( $emailHeading, 'woocommerce' );
		$customText        = '';
		$emailtext         = esc_html__( 'Email content', 'woocommerce' );
		$additionalContent = __( 'Thanks for reading.', 'woocommerce' );

		$textShippingAddress = __( 'Shipping Address', 'woocommerce' );
		$textBillingAddress  = __( 'Billing Address', 'woocommerce' );

		$refer_header = __( '{{ advocate.first_name }} has sent you $20 to spend', 'automatewoo-referrals' );

		$refer_content =
		sprintf(
			__(
				"Hi there! <br/><br/>You have been invited to shop at %s and you've got a $20 discount waiting for you when you spend $100. Use the coupon code below to claim your offer.",
				'automatewoo-referrals'
			),
			'[yaymail_site_name]'
		) . '<br/> {{ coupon_code }}';
		/*
		@@@ Elements default when reset template.
		@@@ Note 1: Add characters '\' before special characters in a string.
		@@@ example 1: "family": "\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif",

		@@@ Note 2: Add characters '\' before special characters in a string.
		@@@ example 2: "<h1 style=\"font-family: \'Helvetica Neue\',...."
		*/

		// Elements
		$elements =
		'[{
			"id": "8ffa62b5-7258-42cc-ba53-7ae69638c1fe",
			"type": "Logo",
			"nameElement": "Logo",
			"settingRow": {
				"backgroundColor": "#ECECEC",
				"align": "center",
				"pathImg": "",
				"paddingTop": "15",
				"paddingRight": "50",
				"paddingBottom": "15",
				"paddingLeft": "50",
				"width": "172",
				"url": "#"
			}
		}, ';
		if ( 'AutomateWoo_Referrals_Email' === $customOrder ) {
			$elements .= '{
				"id": "802bfe24-7af8-48af-ac5e-6560a81345b3",
				"type": "ElementText",
				"nameElement": "Email Heading",
				"settingRow": {
					"content": "<h1 style=\"font-size: 30px; font-weight: 300; line-height: normal; margin: 0; color: inherit;\">' . $refer_header . '</h1>",
					"backgroundColor": "#96588A",
					"textColor": "#ffffff",
					"family": "Helvetica,Roboto,Arial,sans-serif",
					"paddingTop": "36",
					"paddingRight": "48",
					"paddingBottom": "36",
					"paddingLeft": "48"
				}
			}, {
				"id": "b035d1f1-0cfe-41c5-b79c-0478f144ef5f",
				"type": "ElementText",
				"nameElement": "Text",
				"settingRow": {
					"content": "<p style=\"margin: 0px;\"><span style=\"font-size: 14px;\">' . $refer_content . '</span></p>",
					"backgroundColor": "#fff",
					"textColor": "#636363",
					"family": "Helvetica,Roboto,Arial,sans-serif",
					"paddingTop": "47",
					"paddingRight": "50",
					"paddingBottom": "30",
					"paddingLeft": "50"
				}
			},';
		} else {
			$elements .= '{
				"id": "802bfe24-7af8-48af-ac5e-6560a81345b3",
				"type": "ElementText",
				"nameElement": "Email Heading",
				"settingRow": {
					"content": "<h1 style=\"font-size: 30px; font-weight: 300; line-height: normal; margin: 0; color: inherit;\">' . $emailTitle . '</h1>",
					"backgroundColor": "#96588A",
					"textColor": "#ffffff",
					"family": "Helvetica,Roboto,Arial,sans-serif",
					"paddingTop": "36",
					"paddingRight": "48",
					"paddingBottom": "36",
					"paddingLeft": "48"
				}
			}, {
				"id": "b035d1f1-0cfe-41c5-b79c-0478f144ef5f",
				"type": "ElementText",
				"nameElement": "Text",
				"settingRow": {
					"content": "<p style=\"margin: 0px;\"><span style=\"font-size: 14px;\">' . $emailtext . '</span></p>",
					"backgroundColor": "#fff",
					"textColor": "#636363",
					"family": "Helvetica,Roboto,Arial,sans-serif",
					"paddingTop": "47",
					"paddingRight": "50",
					"paddingBottom": "0",
					"paddingLeft": "50"
				}
			},
			{
				"id": "ad422370-f762-4a26-92de-c4cf3878h0oi",
				"type": "OrderItem",
				"nameElement": "Order Item",
				"settingRow": {
					"contentBefore": "[yaymail_items_border_before]",
					"contentAfter": "[yaymail_items_border_after]",
					"contentTitle": "[yaymail_items_border_title]",
					"content": "[yaymail_items_border_content]",
					"backgroundColor": "#fff",
					"titleColor" : "#96588a",
					"textColor": "#636363",
					"borderColor": "#e5e5e5",
					"family": "Helvetica,Roboto,Arial,sans-serif",
					"paddingTop": "15",
					"paddingRight": "50",
					"paddingBottom": "15",
					"paddingLeft": "50"
				}
			},
			{
				"id": "de242956-a617-4213-9107-138842oi4tch",
				"type": "BillingAddress",
				"nameElement": "Billing Shipping Address",
				"settingRow": {
					"nameColumn": "BillingShippingAddress",
					"contentTitle": "[yaymail_billing_shipping_address_title]",
					"checkBillingShipping": "[yaymail_billing_shipping_address_title]",
					"titleBilling": "' . $textBillingAddress . '",
					"titleShipping": "' . $textShippingAddress . '",
					"content": "[yaymail_billing_shipping_address_content]",
					"titleColor" : "#96588a",
					"backgroundColor": "#fff",
					"borderColor": "#e5e5e5",
					"textColor": "#636363",
					"family": "Helvetica,Roboto,Arial,sans-serif",
					"paddingTop": "15",
					"paddingRight": "50",
					"paddingBottom": "15",
					"paddingLeft": "50"
				}
			},
			{
				"id": "b39bf2e6-8c1a-4384-a5ec-37663da27c8d",
				"type": "ElementText",
				"nameElement": "Text",
				"settingRow": {
					"content": "<p><span style=\"font-size: 14px;\">' . $additionalContent . '</span></p>",
					"backgroundColor": "#fff",
					"textColor": "#636363",
					"family": "Helvetica,Roboto,Arial,sans-serif",
					"paddingTop": "0",
					"paddingRight": "50",
					"paddingBottom": "38",
					"paddingLeft": "50"
				}
			},';
		}
		$elements .= '
		{
			"id": "b39bf2e6-8c1a-4384-a5ec-37663da27c8ds",
			"type": "ElementText",
			"nameElement": "Footer",
			"settingRow": {
				"content": "<p style=\"font-size: 14px;margin: 0px 0px 16px; text-align: center;\">[yaymail_site_name]&nbsp;- Built with <a style=\"color: #96588a; font-weight: normal; text-decoration: underline;\" href=\"https://woocommerce.com\" target=\"_blank\" rel=\"noopener\">WooCommerce</a></p>",
				"backgroundColor": "#ececec",
				"textColor": "#8a8a8a",
				"family": "Helvetica,Roboto,Arial,sans-serif",
				"paddingTop": "15",
				"paddingRight": "50",
				"paddingBottom": "15",
				"paddingLeft": "50"
			}
		}]';

		// Templates Subscription
		$templates = array(
			$customOrder => array(),
		);

		$templates[ $customOrder ]['elements'] = $elements;
		return $templates;
	}


}
