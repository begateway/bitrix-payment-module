<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Sale\Order;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$module_id = "begateway.payment";
if( ! \Bitrix\Main\Loader::includeModule($module_id) ) return;

$payment_id = CSalePaySystemAction::GetParamValue("ORDER_PAYMENT_ID");
$order_id = IntVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]);
$arOrder = CSaleOrder::GetByID($order_id);
$order = Order::load($order_id);
$payment = $order->getPaymentCollection()->getItemById($payment_id);
CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"], '', array(), $payment->getFieldValues());

\BeGateway\Settings::$shopId = CSalePaySystemAction::GetParamValue("SHOP_ID");
\BeGateway\Settings::$shopKey = CSalePaySystemAction::GetParamValue("SHOP_KEY");
\BeGateway\Settings::$checkoutBase = "https://" . CSalePaySystemAction::GetParamValue("DOMAIN_PAYMENT_PAGE");

try {
  if (is_null($arOrder)) {
  	throw new Exception( Loc::getMessage("SALE_BEGATEWAY_FAIL_TOKEN_QUERY") );
  }

  list($uid, $token) = explode(':', $arOrder['PS_INVOICE_ID']);

  if (is_null($token)) {
  	throw new Exception( Loc::getMessage("SALE_BEGATEWAY_FAIL_TOKEN_QUERY") );
  }

  $query = new \beGateway\QueryByPaymentToken();
  $query->setToken($token);
  $response = $query->submit()->getResponse();

  if( ! isset( $response->checkout ) )
  	throw new Exception( Loc::getMessage("SALE_BEGATEWAY_FAIL_TOKEN_QUERY") );

  if ($response->checkout->order->tracking_id != $order_id . ':' . $payment_id)
  	throw new Exception( Loc::getMessage("SALE_BEGATEWAY_WRONG_TRACKING_ID") );

  $money = new \BeGateway\Money;
  $money->setCents($response->checkout->order->amount);
  $money->setCurrency($response->checkout->order->currency);

  if ($response && $response->checkout->status == 'successful') {
    $message = array();
    $message []= Loc::getMessage("SALE_BEGATEWAY_STATUS_MESSAGE_UID") . ' ' . $response->checkout->gateway_response->uid. ". " . Loc::getMessage("SALE_BEGATEWAY_STATUS_MESSAGE_TIME") . ' ' . $response->checkout->gateway_response->paid_at;

    $arFields = array(
      "PS_STATUS" => "Y",
      "PS_STATUS_DESCRIPTION" => implode("\n",$message),
      "PS_INVOICE_ID" => implode(':', array($response->checkout->gateway_response->uid, $arOrder['PS_INVOICE_ID'])),
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
      return true;
    }
  }
} catch(Exception $e) {
	ShowError( $e->getMessage() );
}
return false;
