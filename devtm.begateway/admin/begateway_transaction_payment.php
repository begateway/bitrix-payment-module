<?

$module_id = "devtm.begateway";

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/$module_id/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/$module_id/prolog.php");


use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$gr = explode("|", \Bitrix\Main\Config\Option::get( $module_id, "group_ids" ));
$in = array_intersect($gr,CUser::GetUserGroupArray());
if(empty($in))
	$APPLICATION->AuthForm(Loc::getMessage("DIB_AUTH_FORM_MESSAGE"));

\Bitrix\Main\Loader::includeModule("sale");
\Bitrix\Main\Config\Option::set("main", "~sale_converted_15", "N"); //Костыль из - за совместимости битрикс с ядром D7
try
{
	$ID = (int)$ID;
	if( $ID <= 0)
		throw new Exception( Loc::getMessage("DEVTM_BEGATEWAY_NOT_CORRECT_ID") );
	
	if(
		$_SERVER["REQUEST_METHOD"] == "POST" &&
		strlen($refund.$capture.$void) > 0 &&
		strlen($parent_uid) > 0 &&
		md5($parent_uid.$ID) === $hash &&
		check_bitrix_sessid()
	)
	{
		\Bitrix\Main\Loader::includeModule($module_id);
		\beGateway\Settings::$shopId = (int)\Bitrix\Main\Config\Option::get( $module_id, "shop_id" );;
		\beGateway\Settings::$shopKey = \Bitrix\Main\Config\Option::get( $module_id, "shop_key" );
		\beGateway\Settings::$gatewayBase = "https://". \Bitrix\Main\Config\Option::get( $module_id, "domain_gateway" );
		\beGateway\Settings::$checkoutBase = "https://". \Bitrix\Main\Config\Option::get( $module_id, "domain_payment_page" );
		
		if($amount > 0)
		{
			if(strlen($refund) > 0)
			{
				$query = new \beGateway\Refund();
				$query->setReason("Возврат денег");
				echo '1';
			}
			else
				if(strlen($capture) > 0)
				{
					$query = new \beGateway\Capture();
				}
				else
					if(strlen($void) > 0)
					{
						$query = new \beGateway\Void();
					}
				
			$query->setParentUid($parent_uid);
			$query->money->setCents($amount);
			
			$response = $query->submit()->getResponse();
			
			if(isset($response->errors))
				throw new Exception( $response->message );
			
			$order = CSaleOrder::GetList(
					array(),
					array("ID" => $ID),
					false,
					false,
					array("ID", "PS_STATUS_DESCRIPTION")
				  )->Fetch();
			
			$ps_desc = json_decode($order["PS_STATUS_DESCRIPTION"], true);
			if(isset($ps_desc["uids"]))
				$ps_desc["uids"][$response->transaction->uid] = $response->transaction->type;
			$fields = array("PS_STATUS_DESCRIPTION" => json_encode($ps_desc));
			CSaleOrder::Update($ID, $fields);
		}
		
		
	}
	
	$ps_id = (int)\Bitrix\Main\Config\Option::get( $module_id, "payment_system_id" );
	
	if( $ps_id <= 0 )
		throw new Exception( Loc::getMessage( "DEVTM_BEGATEWAY_PS_NOT_FOUND" ) );

	$order = CSaleOrder::GetList(
								array("ID" => "ASC"),
								array("ID" => $ID, "PAY_SYSTEM_ID" => $ps_id),
								false,
								false,
								array( "ID", "USER_ID", "PS_STATUS_DESCRIPTION", "PRICE", "CURRENCY" )
							)->Fetch();
	
	if( $order["ID"] <= 0 )
		throw new Exception( Loc::getMessage("DEVTM_BEGATEWAY_ORDER_NOT_FOUND") );
	

	if( strlen($order["PS_STATUS_DESCRIPTION"]) <= 0 )
		throw new Exception( Loc::getMessage("DEVTM_BEGATEWAY_TRANSINFO_NOT_FOUND") );
	
	$ps_status_desc = json_decode($order["PS_STATUS_DESCRIPTION"], true);
		
	$result = array();
	$result["order_id"] = $order["ID"];
	$result["amount"] = array("price" => $order["PRICE"], "currecy" => $order["CURRENCY"]);
	$user = CUser::GetList(
						($by="id"),
						($o="desc"),
						array("ID" => $order["USER_ID"]),
						array("FIELDS" => array("ID", "NAME", "LAST_NAME"))
				   )->Fetch();

	if($user["ID"] > 0)
	{
		$result["user"] = $user["NAME"] ." ". $user["LAST_NAME"];
	}	
	
	$result["uids"] = $ps_status_desc["uids"];
	
	$tabs = array(
				  array("DIV" => "edit1", "TAB" => Loc::getMessage("DEVTM_BEGATEWAY_TAB_TITLE"), "ICON"=>"main_user_edit", "TITLE" => Loc::getMessage("DEVTM_BEGATEWAY_TITLE_DESC")),
			  );
	
	$o_tab = new CAdminTabControl("tab_control", $tabs);
	
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	
	$o_tab->Begin();
	$o_tab->BeginNextTab();
	
	foreach($result as $key => $val)
	{
		if( $key == "order_id" )
		{
?>
			<tr>
				<td width="40%"><?echo Loc::getMessage("DEVTM_BEGATEWAY_ORDER_ID_TITLE")?>:</td>
				<td><?= $val?></td>
			</tr>
<?
		}
		else
			if($key == "amount")
			{
?>
				<tr>
					<td width="40%"><?echo Loc::getMessage("DEVTM_BEGATEWAY_AMOUNT_TITLE")?>:</td>
					<td><?= $val["price"]." ".$val["currecy"]?></td>
				</tr>
<?
			}
			else
				if($key == "user")
				{
?>
					<tr>
						<td width="40%"><?echo Loc::getMessage("DEVTM_BEGATEWAY_USER_TITLE")?>:</td>
						<td><?= $val?></td>
					</tr>
<?
				}
				else
					if($key == "uids")
					{
						foreach($val as $uid => $transaction)
						{
?>
							<tr>
								<td width="40%"><?echo Loc::getMessage("DEVTM_BEGATEWAY_UID_TITLE")?>:</td>
								<td><?= $uid ." - ". $transaction?></td>
							</tr>
<?
						}
					}
	}
?>
	<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>" name="post_form">
		<?echo bitrix_sessid_post()?>
		<input type="hidden" name="ID" value="<?= $ID?>">
		<input type="hidden" name="lang" value="<?= LANG?>">
<?
	if( ($parent_uid = array_search("payment", $result["uids"])) !== false ):
?>
		<tr>
			<td width="40%"><input type="submit" name="refund" value="Refund"></td>
			<td><input type="text" name="amount" value=""></td>
		</tr>
<?	
	elseif(($parent_uid = array_search("authorization", $result["uids"])) !== false):
?>
		<tr>
			<td width="40%"><input type="submit" name="capture" value="Capture"></td>
			<td><input type="text" id="order-amount" name="amount" value="<?= $result["amount"]["price"]*100?>"></td>
		</tr>
		<tr>
			<td width="40%"><input type="submit" onClick="document.getElementById('order-amount').value = '<?= $result["amount"]["price"]*100?>'" name="void" value="Void"></td>
			<td></td>
		</tr>
<?
	endif;
?>
		<tr>
			<td width="50%">Указывайте цену без точки. Например для 543.43 указывается 54343</td>
		</tr>
		<input type="hidden" name="parent_uid" value="<?= $parent_uid?>">	
		<input type="hidden" name="hash" value="<?= md5($parent_uid.$ID)?>">	
<?
$o_tab->End();

}catch(Exception $e){
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
	CAdminMessage::ShowMessage(array("MESSAGE" => $e->getMessage(), "TYPE"=>"ERROR"));
}
\Bitrix\Main\Config\Option::set("main", "~sale_converted_15", "Y");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");