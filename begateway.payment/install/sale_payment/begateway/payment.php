<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
use Bitrix\Main\Localization\Loc;
require_once dirname(__FILE__) . '/common.php';

$module_id = "begateway.payment";
if ( ! \Bitrix\Main\Loader::includeModule($module_id) ) {
  ShowMessage(Loc::getMessage('SALE_BEGATEWAY_MODULE_ERROR'));
  return;
}

Loc::loadMessages(__FILE__);

\BeGateway\Settings::$shopId = CSalePaySystemAction::GetParamValue("SHOP_ID");
\BeGateway\Settings::$shopKey = CSalePaySystemAction::GetParamValue("SHOP_KEY");
\BeGateway\Settings::$checkoutBase = "https://" . CSalePaySystemAction::GetParamValue("DOMAIN_PAYMENT_PAGE");

$out_summ = number_format(floatval(strval($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"])), 2, ".", "");
$currency = $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"];
$order_id = (strlen(CSalePaySystemAction::GetParamValue("ORDER_ID")) > 0) ? CSalePaySystemAction::GetParamValue("ORDER_ID") : $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"];
$order_id = IntVal($order_id);
$payment_id = CSalePaySystemAction::GetParamValue("ORDER_PAYMENT_ID");
$arReturnParams = array('order_id' => $order_id, 'payment_id' => $payment_id);
$description = $APPLICATION->ConvertCharset(Loc::getMessage("SALE_BEGATEWAY_ORDER_ID") . " #" .$order_id, SITE_CHARSET, 'utf-8');

$transaction = new \BeGateway\GetPaymentToken;

if (CSalePaySystemAction::GetParamValue("ENABLE_CREDIT_CARD") == 'Y') {
  $transaction->addPaymentMethod(new \BeGateway\PaymentMethod\CreditCard);
}

if (CSalePaySystemAction::GetParamValue("ENABLE_CREDIT_CARD_HALVA") == 'Y') {
  $transaction->addPaymentMethod(new \BeGateway\PaymentMethod\CreditCardHalva);
}

if (CSalePaySystemAction::GetParamValue("ENABLE_ERIP") == 'Y') {
  $erip = new \BeGateway\PaymentMethod\Erip(array(
    'order_id' => $order_id,
    'account_number' => StrVal($order_id),
    'service_info' => array($description)
  ));
  $transaction->addPaymentMethod($erip);
}

$transaction->money->setCurrency($currency);
$transaction->money->setAmount($out_summ);
$transaction->setTrackingId($order_id . ":" . $payment_id);
$transaction->setDescription($description);
$transaction->setLanguage(LANGUAGE_ID);

if( CSalePaySystemAction::GetParamValue("TRANSACTION_TYPE") == "authorization" )
{
  $transaction->setAuthorizationTransactionType();
}
else
{
  $transaction->setPaymentTransactionType();
}

$notification_url = CSalePaySystemAction::GetParamValue("NOTIFICATION_URL");
$notification_url = str_replace('bitrix.local', 'bitrix.webhook.begateway.com:8443', $notification_url);
$notification_url = str_replace('0.0.0.0', 'webhook.begateway.com:8443', $notification_url);

$transaction->setNotificationUrl( $notification_url );
$transaction->setSuccessUrl( _build_return_url(CSalePaySystemAction::GetParamValue("SUCCESS_URL"), $arReturnParams) );
$transaction->setDeclineUrl( _build_return_url(CSalePaySystemAction::GetParamValue("DECLINE_URL"), $arReturnParams) );
$transaction->setFailUrl( _build_return_url(CSalePaySystemAction::GetParamValue("FAIL_URL"), $arReturnParams) );

$firstName = CSalePaySystemAction::GetParamValue("FIRST_NAME");
$middleName = CSalePaySystemAction::GetParamValue("MIDDLE_NAME");
$lastName = CSalePaySystemAction::GetParamValue("LAST_NAME");
$email = CSalePaySystemAction::GetParamValue("EMAIL");
$address = CSalePaySystemAction::GetParamValue("ADDRESS");
$city = CSalePaySystemAction::GetParamValue("CITY");
$zip = CSalePaySystemAction::GetParamValue("ZIP");
$phone = CSalePaySystemAction::GetParamValue("PHONE");
$state = CSalePaySystemAction::GetParamValue("STATE");
$country = CSalePaySystemAction::GetParamValue("COUNTRY");
$testmode = CSalePaySystemAction::GetParamValue("TESTMODE");

if (strlen($middleName) > 0 && strlen($firstName . ' ' . $middleName) < 31) {
  $firstName = $firstName . ' ' . $middleName;
}

# if firstName is set to FIO property
if (strpos($firstName, ' ') > 0 && strlen($lastName) == 0) {
  list($firtName, $lastName) = explode(' ', $firstName, 2);
}

if ($firstName)$transaction->customer->setFirstName($APPLICATION->ConvertCharset($firstName, SITE_CHARSET, 'utf-8'));
if ($lastName) $transaction->customer->setLastName($APPLICATION->ConvertCharset($lastName, SITE_CHARSET, 'utf-8'));
if ($address)  $transaction->customer->setAddress($APPLICATION->ConvertCharset($address, SITE_CHARSET, 'utf-8'));
if ($city)     $transaction->customer->setCity($APPLICATION->ConvertCharset($city, SITE_CHARSET, 'utf-8'));
if ($zip)      $transaction->customer->setZip($APPLICATION->ConvertCharset($zip, SITE_CHARSET, 'utf-8'));
if ($email)    $transaction->customer->setEmail($APPLICATION->ConvertCharset($email, SITE_CHARSET, 'utf-8'));
if ($phone)    $transaction->customer->setPhone($APPLICATION->ConvertCharset($phone, SITE_CHARSET, 'utf-8'));
if ($state)    $transaction->customer->setState($APPLICATION->ConvertCharset($state, SITE_CHARSET, 'utf-8'));
if ($country)  $transaction->customer->setCountry($APPLICATION->ConvertCharset($country, SITE_CHARSET, 'utf-8'));
if ($testmode == 'Y')  $transaction->setTestMode(true);

$response = $transaction->submit();

if(!$response->isSuccess())
{
  ShowMessage(Loc::getMessage("SALE_BEGATEWAY_GET_TOKEN_ERROR") . htmlspecialcharsbx($response->getMessage()));
  die;
}

$_SESSION["token"] = $response->getToken();

$domain_gateway = CSalePaySystemAction::GetParamValue("DOMAIN_PAYMENT_PAGE");
list($subdomain,$jsdomain) = explode('.', $domain_gateway, 2);

$jsurl = 'https://js.' . $jsdomain . '/widget/be_gateway.js';

$GLOBALS["APPLICATION"]->AddHeadScript( $jsurl );
?>
<div id="begateway-wrapper">
  <script type="text/javascript">
    this.payment = function() {
      var params = {
        checkout_url: "https://<?= CSalePaySystemAction::GetParamValue("DOMAIN_PAYMENT_PAGE"); ?>",
        token: "<?= $response->getToken(); ?>",
        style: {
          <?= CSalePaySystemAction::GetParamValue("FORM_CSS");?>
        }
      };
      new BeGateway(params).createWidget();
    };
  </script>
  <button class="btn btn-primary" onclick="payment();"><?= Loc::getMessage("SALE_BEGATEWAY_BUY_BUTTON"); ?></button>
</div>
