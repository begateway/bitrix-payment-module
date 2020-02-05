<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Sale\Order;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$module_id = "begateway.payment";
if( ! \Bitrix\Main\Loader::includeModule($module_id) ) return;

\BeGateway\Settings::$shopId = CSalePaySystemAction::GetParamValue("SHOP_ID");
\BeGateway\Settings::$shopKey = CSalePaySystemAction::GetParamValue("SHOP_KEY");
\BeGateway\Settings::$gatewayBase = "https://" . CSalePaySystemAction::GetParamValue("DOMAIN_GATEWAY");
\BeGateway\Settings::$checkoutBase = "https://" . CSalePaySystemAction::GetParamValue("DOMAIN_PAYMENT_PAGE");

$payment_id = CSalePaySystemAction::GetParamValue("ORDER_PAYMENT_ID");
$order_id = IntVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]);

$tracking_id = $order_id . ":" . $payment_id;

set_time_limit(0);

$query = new \BeGateway\QueryByTrackingId;
$query->setTrackingId($tracking_id);

$response = $query->submit();

if ($response && $response != 'error') {
  foreach ($response->getResponse()->transactions as $transaction) {
    $money = new \BeGateway\Money;
    $money->setCurrency($transaction->currency);
    $money->setCents($transaction->amount);

  	if ($transaction->tracking_id == $tracking_id &&
        ($transaction->type == 'payment' || $transaction->type == 'authorization')) {
  		$arOrder = CSaleOrder::GetByID($order_id);
      $message = array();
      $message []= Loc::getMessage("SALE_BEGATEWAY_STATUS_MESSAGE_UID") . ' ' . $transaction->uid. ". " . Loc::getMessage("SALE_BEGATEWAY_STATUS_MESSAGE_TIME") . ' ' . $transaction->paid_at;

      if(isset($transaction->three_d_secure_verification->pa_status)) {
        $message[] = "3-D Secure: " .$transaction->three_d_secure_verification->pa_status;
      }

      $arFields = array(
        "PS_STATUS" => ($transaction->status == 'successful') ? "Y" : "N",
        "PS_STATUS_DESCRIPTION" => implode("\n",$message),
        "PS_SUM" => $money->getAmount(),
        "PS_CURRENCY" => $money->getCurrency(),
        "PS_RESPONSE_DATE" => new \Bitrix\Main\Type\DateTime(),
        "USER_ID" => $arOrder["USER_ID"]
      );

      if ($arOrder["PAYED"] != "Y" &&
          $arFields["PS_STATUS"] == "Y" &&
          $arOrder["PRICE"] == $money->getAmount()) {
        CSaleOrder::PayOrder($arOrder["ID"], "Y", True, True, 0, $arFields);
        CSaleOrder::StatusOrder($arOrder["ID"], "P");
      }

      return true;
    }
	}
}

return false;
