<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$psTitle = Loc::getMessage("SALE_BEGATEWAY_TITLE");
$psDescription = Loc::getMessage("SALE_BEGATEWAY_DESCRIPTION");

$arPSCorrespondence = array(
		"DOMAIN_GATEWAY" => array(
				"NAME" => GetMessage("SALE_BEGATEWAY_DOMAIN_GATEWAY_NAME"),
				"DESCR" => GetMessage("SALE_BEGATEWAY_DOMAIN_GATEWAY_DESC"),
				"VALUE" => "gateway.domain.com",
				"TYPE" => ""
			),
		"DOMAIN_PAYMENT_PAGE" => array(
				"NAME" => GetMessage("SALE_BEGATEWAY_DOMAIN_PAYMENT_PAGE_NAME"),
				"DESCR" => GetMessage("SALE_BEGATEWAY_DOMAIN_PAYMENT_PAGE_DESC"),
				"VALUE" => "checkout.domain.com",
				"TYPE" => ""
			),
		"SHOP_ID" => array(
				"NAME" => GetMessage("SALE_BEGATEWAY_SHOP_ID_NAME"),
				"DESCR" => GetMessage("SALE_BEGATEWAY_SHOP_ID_DESC"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"SHOP_KEY" => array(
				"NAME" => GetMessage("SALE_BEGATEWAY_SHOP_KEY_NAME"),
				"DESCR" => GetMessage("SALE_BEGATEWAY_SHOP_KEY_DESC"),
				"VALUE" => "",
				"TYPE" => ""
			),
		"TRANSACTION_TYPE" => array(
				"NAME" => GetMessage("SALE_BEGATEWAY_TRANSACTION_TYPE_NAME"),
				"DESCR" => "",
				"TYPE" => "SELECT",
				"VALUE" => array(
          "payment" => array(
            "NAME" => GetMessage("SALE_BEGATEWAY_TRANSACTION_TYPE_PAYMENT_NAME")
          ),
          "authorization" => array(
            "NAME" => GetMessage("SALE_BEGATEWAY_TRANSACTION_TYPE_AUTHORIZATION_NAME")
          )
        )
			),
		"SUCCESS_URL" => array(
				"NAME" => GetMessage("SALE_BEGATEWAY_SUCCESS_URL_NAME"),
				"DESCR" => GetMessage("SALE_BEGATEWAY_SUCCESS_URL_DESC"),
				"VALUE" => "http://www.yoursite.com/sale/payment_success.php",
				"TYPE" => ""
			),
    "DECLINE_URL" => array(
        "NAME" => GetMessage("SALE_BEGATEWAY_DECLINE_URL_NAME"),
        "DESCR" => GetMessage("SALE_BEGATEWAY_DECLINE_URL_DESC"),
        "VALUE" => "http://www.yoursite.com/personal/order/",
        "TYPE" => ""
      ),
		"FAIL_URL" => array(
				"NAME" => GetMessage("SALE_BEGATEWAY_FAIL_URL_NAME"),
				"DESCR" => GetMessage("SALE_BEGATEWAY_FAIL_URL_DESC"),
				"VALUE" => "http://www.yoursite.com/sale/payment_fail.php",
				"TYPE" => ""
			),
    "NOTIFICATION_URL" => array(
        "NAME" => GetMessage("SALE_BEGATEWAY_NOTIFICATION_URL_NAME"),
        "DESCR" => GetMessage("SALE_BEGATEWAY_NOTIFICATION_URL_DESC"),
        "VALUE" => "http://www.yoursite.com/sale/payment_notification.php",
        "TYPE" => ""
      ),
    "CANCEL_URL" => array(
				"NAME" => GetMessage("SALE_BEGATEWAY_CANCEL_URL_NAME"),
				"DESCR" => GetMessage("SALE_BEGATEWAY_CANCEL_URL_DESC"),
				"VALUE" => "http://www.yoursite.com/personal/order/",
				"TYPE" => ""
			),
    "FORM_TYPE" => array(
        "NAME" => GetMessage("SALE_BEGATEWAY_FORM_TYPE_NAME"),
        "DESCR" => GetMessage("SALE_BEGATEWAY_FORM_TYPE_DESCR"),
        "TYPE" => "SELECT",
        "VALUE" => array(
          "redirect" => array(
            "NAME" => GetMessage("SALE_BEGATEWAY_FORM_TYPE_REDIRECT_NAME")
          ),
          "inline" => array(
            "NAME" => GetMessage("SALE_BEGATEWAY_FORM_TYPE_INLINE_NAME")
          ),
          "overlay" => array(
            "NAME" => GetMessage("SALE_BEGATEWAY_FORM_TYPE_OVERLAY_NAME")
          )
        )
      ),
    "FORM_CSS" => array(
        "NAME" => GetMessage("SALE_BEGATEWAY_CSS_FORM_NAME"),
        "DESCR" => GetMessage("SALE_BEGATEWAY_CSS_FORM_DESC"),
        "VALUE" => "",
        "TYPE" => ""
      ),
		"ORDER_ID" => array(
				"NAME" => GetMessage("SALE_BEGATEWAY_ORDER_ID"),
				"DESCR" => GetMessage("SALE_BEGATEWAY_ORDER_ID_DESC"),
				"VALUE" => "ID",
				"TYPE" => "ORDER"
			),
		"FIRST_NAME" => array(
				"NAME" => GetMessage("SALE_BEGATEWAY_FIRST_NAME_NAME"),
				"DESCR" => GetMessage("SALE_BEGATEWAY_FIRST_NAME_DESC"),
				"VALUE" => "FIRST_NAME",
				"TYPE" => "PROPERTY"
			),
		"MIDDLE_NAME" => array(
				"NAME" => GetMessage("SALE_BEGATEWAY_MIDDLE_NAME_NAME"),
				"DESCR" => GetMessage("SALE_BEGATEWAY_MIDDLE_NAME_DESC"),
				"VALUE" => "MIDDLE_NAME",
				"TYPE" => "PROPERTY"
			),
		"LAST_NAME" => array(
				"NAME" => GetMessage("SALE_BEGATEWAY_LAST_NAME_NAME"),
				"DESCR" => GetMessage("SALE_BEGATEWAY_LAST_NAME_DESC"),
				"VALUE" => "LAST_NAME",
				"TYPE" => "PROPERTY"
			),
		"EMAIL" => array(
				"NAME" => GetMessage("SALE_BEGATEWAY_EMAIL_NAME"),
				"DESCR" => GetMessage("SALE_BEGATEWAY_EMAIL_DESC"),
				"VALUE" => "EMAIL",
				"TYPE" => "PROPERTY"
			),
		"ADDRESS" => array(
				"NAME" => GetMessage("SALE_BEGATEWAY_ADDRESS_NAME"),
				"DESCR" => GetMessage("SALE_BEGATEWAY_ADDRESS_DESC"),
				"VALUE" => "ADDRESS",
				"TYPE" => "PROPERTY"
			),
    "CITY" => array(
        "NAME" => GetMessage("SALE_BEGATEWAY_CITY_NAME"),
        "DESCR" => GetMessage("SALE_BEGATEWAY_CITY_DESCR"),
        "VALUE" => "CITY",
        "TYPE" => "PROPERTY"
      ),
    "ZIP" => array(
        "NAME" => GetMessage("SALE_BEGATEWAY_ZIP_NAME"),
        "DESCR" => GetMessage("SALE_BEGATEWAY_ZIP_DESC"),
        "VALUE" => "ZIP",
        "TYPE" => "PROPERTY"
      ),
    "STATE" => array(
        "NAME" => GetMessage("SALE_BEGATEWAY_STATE_NAME"),
        "DESCR" => GetMessage("SALE_BEGATEWAY_STATE_DESC"),
        "VALUE" => "STATE",
        "TYPE" => "PROPERTY"
      ),
    "COUNTRY" => array(
        "NAME" => GetMessage("SALE_BEGATEWAY_COUNTRY_NAME"),
        "DESCR" => GetMessage("SALE_BEGATEWAY_COUNTRY_DESC"),
        "VALUE" => "COUNTRY",
        "TYPE" => "PROPERTY"
      ),
		"PHONE" => array(
				"NAME" => GetMessage("SALE_BEGATEWAY_PHONE_NAME"),
				"DESCR" => GetMessage("SALE_BEGATEWAY_PHONE_DESC"),
				"VALUE" => "PHONE",
				"TYPE" => "PROPERTY"
			),
		"TESTMODE" => array(
				"NAME" => GetMessage("SALE_BEGATEWAY_DEMO_NAME"),
				"DESCR" => GetMessage("SALE_BEGATEWAY_DEMO_DESC"),
        "TYPE" => "SELECT",
        "VALUE" => array(
          "Y" => array(
            "NAME" => GetMessage("SALE_BEGATEWAY_DEMO_YES")
          ),
          "N" => array(
            "NAME" => GetMessage("SALE_BEGATEWAY_DEMO_NO")
          )
        )
			)
	);
