<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?

$module_id = "devtm.begateway";
if( ! \Bitrix\Main\Loader::includeModule($module_id) || ! $GLOBALS["USER"]->IsAuthorized() ) return;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

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
$transaction->setDescription(Loc::getMessage("DEVTM_BEGATEWAY_ORDER_TITLE") . " #" .$order_id);
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
$notification_url = str_replace('bitrix.local', 'bitrix.webhook.begateway.com:8443', $notification_url);

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


//Тестовые данные
//$firstName = "John";
//$city = "RigaGGG";
//$zip = "LV-1082";
//$email = "john@example.com";
//$country = "LV";
//$phone = ;
//$state = ;


if ($firtName) $transaction->customer->setFirstName($firstName);
if ($lastName) $transaction->customer->setLastName($lastName);
if ($address)  $transaction->customer->setAddress($address);
if ($city)     $transaction->customer->setCity($city);
if ($zip)      $transaction->customer->setZip($zip);
if ($email)    $transaction->customer->setEmail($email);
if ($phone)    $transaction->customer->setPhone($phone);
if ($state)    $transaction->customer->setState($state);
if ($country)  $transaction->customer->setCountry($country);

$transaction->setAddressHidden();

$response = $transaction->submit();

if(!$response->isSuccess())
{
  ShowMessage(Loc::getMessage("DEVTM_BEGATEWAY_GET_TOKEN_ERROR") . $response->getMessage());
  die;
}

$_SESSION["token"] = md5( $USER->GetID() .":". $response->getToken() );

$form_type = \Bitrix\Main\Config\Option::get( $module_id, "form_type" );

if( $form_type == "inline" || $form_type == "overlay" ):
	$GLOBALS["APPLICATION"]->AddHeadScript( "/bitrix/php_interface/include/sale_payment/devtm.begateway/begateway.min.js" );
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
<form method="GET" action="<?=\beGateway\Settings::$checkoutBase . "/checkout";?>">
  <input type="hidden" value="<?=$response->getToken();?>" name="token">
  <input type="submit" value="<?=Loc::getMessage("DEVTM_BEGATEWAY_BUY_BUTTON")?>">
</form>
<?endif?>
