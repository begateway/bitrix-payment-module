<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
use Bitrix\Sale\Order;
use Bitrix\Main\Localization\Loc;

$module_id = "devtm.begateway";
if( ! \Bitrix\Main\Loader::includeModule($module_id) ) return;

require_once dirname(__FILE__) . '/common.php';
Loc::loadMessages(__FILE__);

$webhook = new \beGateway\Webhook;

list($site_id, $order_id, $payment_id) = explode('_', $webhook->getTrackingId());

$order = Order::load($order_id);

if (!$order) {
  _output_message('ERROR: INVALID ORDER ID ' . $order_id);
}

$payment = $order->getPaymentCollection()->getItemById($payment_id);

if (!$payment) {
  _output_message('ERROR: INVALID PAYMENT ID ' . $payment_id);
}

$arOrder = CSaleOrder::GetByID($order_id);
CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"], '', array(), $payment->getFieldValues());

\beGateway\Settings::$shopId = CSalePaySystemAction::GetParamValue("SHOP_ID");
\beGateway\Settings::$shopKey = CSalePaySystemAction::GetParamValue("SHOP_KEY");
\beGateway\Settings::$gatewayBase = "https://" . CSalePaySystemAction::GetParamValue("DOMAIN_GATEWAY");
\beGateway\Settings::$checkoutBase = "https://" . CSalePaySystemAction::GetParamValue("DOMAIN_PAYMENT_PAGE");

if (!$webhook->isAuthorized()) {
  _output_message('ERROR: WEBHOOK IS NOT AUTHORIZED');
}

if (!$webhook->isAuthorized()) {
  _output_message('ERROR: WEBHOOK IS NOT AUTHORIZED');
}

CSaleOrder::PayOrder($arOrder["ID"], "Y");
CSaleOrder::StatusOrder($arOrder["ID"], "P");

$message = array();
if(isset($webhook->getResponse()->transaction->three_d_secure_verification)) {
  $message[] = "3-D Secure: " .$webhook->getResponse()->transaction->three_d_secure_verification->pa_status;
}

$message[] = $webhook->getResponse()->transaction->description;

$money = new \beGateway\Money;
$money->setCurrency($webhook->getResponse()->transaction->currency);
$money->setCents($webhook->getResponse()->transaction->amount);

$arFields = array(
  "PS_STATUS" => ($webhook->isSuccess() ? "Y" : "N"),
  "PS_STATUS_MESSAGE" => implode("\n",$message),
  "PS_SUM" => $money->getAmount(),
  "PS_CURRENCY" => $money->getCurrency(),
  "PS_RESPONSE_DATE" => new \Bitrix\Main\Type\DateTime(),
  "PS_STATUS_DESCRIPTION" => json_encode(array($webhook->getUid() => $webhook->getResponse()->transaction->type))
);

if (CSalePaySystemAction::GetParamValue("PAYED") != "Y" && $arFields["PS_STATUS"] == "Y" && Doubleval(CSalePaySystemAction::GetParamValue("SHOULD_PAY")) == DoubleVal($money->getAmount()) {
  $payment->setField('PAID', 'Y');
}

if(!empty($arFields)) {
  $result = $payment->setFields($arFields);
  if ($result->isSuccess())
    $order->save();
}

_output_message("OK " . $webhook->getUid());
