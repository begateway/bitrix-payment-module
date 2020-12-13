<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
Bitrix\Main\Loader::includeModule('sale');

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
  if (!$order)
    return false;

  $paymentCollection = $order->getPaymentCollection();
  $payment = $paymentCollection->getItemById($payment_id);

  $token = $payment->getField('PS_INVOICE_ID');

  if (is_null($token))
    return false;

  $query = new \beGateway\QueryByPaymentToken();
  $query->setToken($token);
  $response = $query->submit();
  $arResponse = $response->getResponseArray();
  $response = $response->getResponse();

  if(!isset($response->checkout))
    return false;

  if ($response->checkout->order->tracking_id != $order_id . ':' . $payment_id)
    return false;

  $money = new \BeGateway\Money;
  $money->setCents($response->checkout->order->amount);
  $money->setCurrency($response->checkout->order->currency);

  if ($response && $response->checkout->status == 'successful') {
    $transaction_type = $arResponse['checkout']['transaction_type'];
    $uid = $arResponse['checkout']['gateway_response'][$transaction_type]['uid'];
    $paid_at = $arResponse['checkout']['gateway_response'][$transaction_type]['paid_at'];

    $message = array();
    $message []= Loc::getMessage("SALE_BEGATEWAY_STATUS_MESSAGE_UID") . ' ' . $uid . ". " . Loc::getMessage("SALE_BEGATEWAY_STATUS_MESSAGE_TIME") . ' ' . $paid_at;

    $arFields = array(
      "PS_STATUS" => "Y",
      "PS_STATUS_DESCRIPTION" => implode("\n",$message),
      "PS_INVOICE_ID" => implode(':', array($uid, $token)),
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
