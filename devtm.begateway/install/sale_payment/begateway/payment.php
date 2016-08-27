<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

if (!function_exists('mb_convert_encoding')) {
  ShowMessage(Loc::getMessage("SALE_BEGATEWAY_MB_CONVERT_ENCODING_ERROR"));
  die;
}

\beGateway\Settings::$shopId = CSalePaySystemAction::GetParamValue("SHOP_ID");
\beGateway\Settings::$shopKey = CSalePaySystemAction::GetParamValue("SHOP_KEY");
\beGateway\Settings::$gatewayBase = "https://" . CSalePaySystemAction::GetParamValue("DOMAIN_GATEWAY");
\beGateway\Settings::$checkoutBase = "https://" . CSalePaySystemAction::GetParamValue("DOMAIN_PAYMENT_PAGE");

$out_summ = number_format(floatval(strval($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"])), 2, ".", "");
$currency = $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"];
$order_id = (strlen(CSalePaySystemAction::GetParamValue("ORDER_ID")) > 0) ? CSalePaySystemAction::GetParamValue("ORDER_ID") : $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"];
$order_id = IntVal($order_id);
$payment_id = CSalePaySystemAction::GetParamValue("ORDER_PAYMENT_ID");

$transaction = new \beGateway\GetPaymentToken;

$transaction->money->setCurrency($currency);
$transaction->money->setAmount($out_summ);
$transaction->setTrackingId(SITE_ID . ":" . $order_id . ":" . $payment_id);
$transaction->setDescription($APPLICATION->ConvertCharset(Loc::getMessage("SALE_BEGATEWAY_ORDER_ID") . " #" .$order_id, SITE_CHARSET, 'utf-8'));
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

$transaction->setNotificationUrl( $notification_url );
$transaction->setSuccessUrl( CSalePaySystemAction::GetParamValue("NOTIFICATION_URL") );
$transaction->setFailUrl( CSalePaySystemAction::GetParamValue("FAIL_URL") );
$transaction->setDeclineUrl( CSalePaySystemAction::GetParamValue("CANCEL_URL") );
$transaction->setCancelUrl( CSalePaySystemAction::GetParamValue("CANCEL_URL") );

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

if (strlen($middleName) > 0 && strlen($firstName . ' ' . $middleName) < 31) {
  $firstName = $firstName . ' ' . $middleName;
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

$transaction->setAddressHidden();

$response = $transaction->submit();

if(!$response->isSuccess())
{
  ShowMessage(Loc::getMessage("SALE_BEGATEWAY_GET_TOKEN_ERROR") . htmlspecialcharsbx($response->getMessage()));
  die;
}

$_SESSION["token"] = $response->getToken();

$form_type = CSalePaySystemAction::GetParamValue("FORM_TYPE");

if( $form_type == "inline" || $form_type == "overlay" ):

  $domain_gateway = CSalePaySystemAction::GetParamValue("DOMAIN_GATEWAY");
  list($subdomain,$jsdomain) = explode('.', $domain_gateway, 2);

  $jsurl = 'https://js.' . $jsdomain . '/begateway-1-latest.min.js';

	$GLOBALS["APPLICATION"]->AddHeadScript( $jsurl );
	$css = CSalePaySystemAction::GetParamValue("FORM_CSS");
	$id = "begateway-order-" . $order_id;
	if( $form_type == "overlay" )
		echo "<button id=\"$id\" >" . Loc::getMessage("SALE_BEGATEWAY_BUY_BUTTON") . "</button>";
	else
		echo "<div id=\"$id\"></div>";
?>
	<script type="text/javascript">
		var options = {
			type: "<?= $form_type?>",
			id: "<?= $id?>",
			url: "<?= $response->getRedirectUrl()?>",
			style: "<?= $css?>",
			size: { width: 500, height: 500 }
		}

		var pf = new BeGateway(options);
		pf.buildForm();
	</script>
<?else:?>
<form method="GET" action="<?= $response->getRedirectUrlScriptName();?>">
  <input type="hidden" value="<?= $response->getToken();?>" name="token">
  <input type="submit" value="<?= Loc::getMessage("SALE_BEGATEWAY_BUY_BUTTON")?>">
</form>
<?endif?>
