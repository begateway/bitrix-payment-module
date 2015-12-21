<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
$module_id = "devtm.begateway";
if( ! \Bitrix\Main\Loader::includeModule($module_id) ) return;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

\beGateway\Settings::$shopId = (int)\Bitrix\Main\Config\Option::get( $module_id, "shop_id" );
\beGateway\Settings::$shopKey = \Bitrix\Main\Config\Option::get( $module_id, "shop_key" );
\beGateway\Settings::$gatewayBase = "https://". \Bitrix\Main\Config\Option::get( $module_id, "domain_gateway" );
\beGateway\Settings::$checkoutBase = "https://". \Bitrix\Main\Config\Option::get( $module_id, "domain_payment_page" );


$webhook = new \beGateway\Webhook;

list($site_id, $tracking_id) = explode('_', $webhook->getTrackingId());

$arOrder = CSaleOrder::GetByID($tracking_id);

if($arOrder && $webhook->isAuthorized()) {

  if($webhook->isSuccess() &&
    $arOrder["PAYED"] != "Y") {

      CSaleOrder::PayOrder($arOrder["ID"], "Y");
      CSaleOrder::StatusOrder($arOrder["ID"], "P");

      $message = array();
      if( isset( $webhook->getResponse()->transaction->three_d_secure_verification ) ) 
	  {
        $message[] = "3-D Secure: " .$webhook->getResponse()->transaction->three_d_secure_verification->pa_status;
      }

	  $message[] = $webhook->getResponse()->transaction->description;
	  
      $money = new \beGateway\Money;
      $money->setCurrency($webhook->getResponse()->transaction->currency);
      $money->setCents($webhook->getResponse()->transaction->amount);

      $arFields = array(
        "PS_STATUS" => "Y",
        "PS_STATUS_MESSAGE" => implode("\n",$message),
        "PS_SUM" => $money->getAmount(),
        "PS_CURRENCY" => $webhook->getResponse()->transaction->currency,
        "PS_RESPONSE_DATE" => date("d.m.Y H:i:s", strtotime($webhook->getResponse()->transaction->created_at)),
		"PS_STATUS_DESCRIPTION" => json_encode(array("uids" => array($webhook->getUid() => $webhook->getResponse()->transaction->type)));
      );

	  
	  \Bitrix\Main\Config\Option::set("main", "~sale_converted_15", "N"); //Костыль из - за совместимости битрикс с ядром D7
	  CSaleOrder::Update($arOrder["ID"], $arFields);
      \Bitrix\Main\Config\Option::set("main", "~sale_converted_15", "Y");
     
      echo "OK " .$webhook->getUid(); 
  }
}
$APPLICATION->RestartBuffer();
die;
?>
