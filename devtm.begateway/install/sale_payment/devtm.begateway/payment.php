<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
global $APPLICATION;

$module_id = "devtm.begateway";
if( ! \Bitrix\Main\Loader::includeModule($module_id) || ! $GLOBALS["USER"]->IsAuthorized() ) return;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

if (!function_exists('mb_convert_encoding')) {
  ShowMessage('Enabe mb_convert_encoding');
  die;
}

\beGateway\Settings::$shopId = (int)\Bitrix\Main\Config\Option::get( $module_id, "shop_id" );
\beGateway\Settings::$shopKey = \Bitrix\Main\Config\Option::get( $module_id, "shop_key" );
\beGateway\Settings::$gatewayBase = "https://". \Bitrix\Main\Config\Option::get( $module_id, "domain_gateway" );
\beGateway\Settings::$checkoutBase = "https://". \Bitrix\Main\Config\Option::get( $module_id, "domain_payment_page" );


$out_summ = number_format(floatval($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["SHOULD_PAY"]), 2, ".", "");
$currency = $GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["CURRENCY"];
$order_id = IntVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]);

$transaction = new \beGateway\GetPaymentToken;

$transaction->money->setCurrency($currency);
$transaction->money->setAmount($out_summ);
$transaction->setTrackingId(SITE_ID . "_" . $order_id);
$transaction->setDescription($APPLICATION->ConvertCharset(Loc::getMessage("DEVTM_BEGATEWAY_ORDER_TITLE") . " #" .$order_id, SITE_CHARSET, 'utf-8'));
$transaction->setLanguage(LANGUAGE_ID);

if( \Bitrix\Main\Config\Option::get( $module_id, "transaction_type" ) == "authorization" )
{
  $transaction->setAuthorizationTransactionType();
}
else
{
  $transaction->setPaymentTransactionType();
}

$notification_url = \Bitrix\Main\Config\Option::get( $module_id, "notification_url" );

$server_name = $_SERVER["SERVER_NAME"];
if (preg_match('/bitrix(.*)\.local/', $server_name))
  $notification_url = str_replace($server_name, $server_name.'.webhook.begateway.com:8443', $notification_url);

$transaction->setNotificationUrl( $notification_url );
$transaction->setSuccessUrl( \Bitrix\Main\Config\Option::get( $module_id, "success_url" ) );
$transaction->setFailUrl( \Bitrix\Main\Config\Option::get( $module_id, "fail_url" ) );
$transaction->setDeclineUrl( \Bitrix\Main\Config\Option::get( $module_id, "fail_url" ) );
$transaction->setCancelUrl( \Bitrix\Main\Config\Option::get( $module_id, "cancel_url" ) );

//Получаем информацию о клиенте из профиля
$firstName = $_SESSION["SESS_AUTH"]["FIRST_NAME"];
$lastName = $_SESSION["SESS_AUTH"]["LAST_NAME"];
$email = $_SESSION["SESS_AUTH"]["EMAIL"];

if( \Bitrix\Main\Loader::includeModule( "sale" ) )
{
	$db_prop_order_vals = CSaleOrderPropsValue::GetList(
								array("SORT" => "ASC"),
								array(
									"ORDER_ID" => $order_id,
									"CODE" => array(
												"CITY",
												"ZIP",
												"PHONE",
												"ADDRESS"
											  )
									),
								false,
								false,
								array("CODE", "ID", "VALUE")
						  );
	while( $val = $db_prop_order_vals->Fetch() )
	{
		if( !empty( $val["VALUE"]  ) )
			${strtolower( $val["CODE"] )} = $val["VALUE"];
	}
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
  ShowMessage(Loc::getMessage("DEVTM_BEGATEWAY_GET_TOKEN_ERROR") . $response->getMessage());
  die;
}

$_SESSION["token"] = md5($response->getToken());

$form_type = \Bitrix\Main\Config\Option::get( $module_id, "form_type" );

if( $form_type == "inline" || $form_type == "overlay" ):

  $domain_gateway = \Bitrix\Main\Config\Option::get($module_id, 'domain_gateway');
  list($subdomain,$jsdomain) = explode('.', $domain_gateway, 2);

  $jsurl = 'https://js.' . $jsdomain . '/begateway-1-latest.min.js';

	$GLOBALS["APPLICATION"]->AddHeadScript( $jsurl );
	$css = \Bitrix\Main\Config\Option::get( $module_id, "css_form" );
	$id = "begateway-order-" . $order_id;
	if( $form_type == "overlay" )
		echo "<button id=\"$id\" >" . Loc::getMessage("DEVTM_BEGATEWAY_BUY_BUTTON") . "</button>";
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
  <input type="submit" value="<?=Loc::getMessage("DEVTM_BEGATEWAY_BUY_BUTTON")?>">
</form>
<?endif?>
