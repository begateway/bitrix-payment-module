<?

use Bitrix\Main\Localization\Loc;

if( ! $USER->isAdmin() || ! \Bitrix\Main\Loader::includeModule("sale") ) return ;

Loc::loadMessages(__FILE__);

global $APPLICATION;

$module_id = "devtm.begateway";

$db_payments =  CSalePaySystem::GetList(
										array("ID" => "ASC"),
										array()
								   );

$payments = array();
while($payment = $db_payments->Fetch())	
	$payments[] = array("value" => $payment["ID"], "title" => $payment["NAME"]);

$groups = array();
$db_gr = CGroup::GetList(($by="c_sort"), ($o="desc"), array());
while($group = $db_gr->Fetch())	
	$groups[] = array("value" => $group["ID"], "title" => $group["NAME"]);

$all_options = array(
					array( "domain_gateway", Loc::getMessage("DEVTM_BEGATEWAY_DOMAIN_GATEWAY_DESC"), "text" ),
					array( "domain_payment_page", Loc::getMessage("DEVTM_BEGATEWAY_DOMAIN_GATEWAY_PAGE_DESC"), "text" ),
					array( "transaction_type", Loc::getMessage("DEVTM_BEGATEWAY_TRANSACTION_TYPE_DESC"), "select", array(
																														array(
																															"title" => Loc::getMessage("DEVTM_BEGATEWAY_TRANSACTION_TYPE_PAYMENT_NAME"),
																															"value" => "payment",
																														),
																														array(
																															"title" => Loc::getMessage("DEVTM_BEGATEWAY_TRANSACTION_TYPE_AUTHORIZATION_NAME"),
																															"value" => "authorization",
																														),
																												   ) 
					),
					array( "shop_id", Loc::getMessage("DEVTM_BEGATEWAY_SHOP_ID_DESC"), "text" ),
					array( "shop_key", Loc::getMessage("DEVTM_BEGATEWAY_SHOP_KEY_DESC"), "text" ),
					array( "notification_url", Loc::getMessage("DEVTM_BEGATEWAY_NOTIFICATION_URL_DESC"), "text" ),
					array( "success_url", Loc::getMessage("DEVTM_BEGATEWAY_SUCCESS_URL_DESC"), "text"),
					array( "fail_url", Loc::getMessage("DEVTM_BEGATEWAY_FAIL_URL_DESC"), "text" ),
					array( "cancel_url", Loc::getMessage("DEVTM_BEGATEWAY_CANCEL_URL_DESC"), "text" ),
					array( "form_type", Loc::getMessage("DEVTM_BEGATEWAY_FORM_TYPE_DESC"), "select", array(
																				array(
																					"title" => Loc::getMessage("DEVTM_BEGATEWAY_FORM_TYPE_REDIRECT_NAME"),
																					"value" => "redirect",
																				),
																				array(
																					"title" => Loc::getMessage("DEVTM_BEGATEWAY_FORM_TYPE_INLINE_NAME"),
																					"value" => "inline",
																				),
																				array(
																					"title" => Loc::getMessage("DEVTM_BEGATEWAY_FORM_TYPE_OVERLAY_NAME"),
																					"value" => "overlay",
																				),
																		   ) 
					),
					array( "css_form", Loc::getMessage("DEVTM_BEGATEWAY_CSS_FORM_DESC"), "textarea" ),
					array("payment_system_id", Loc::getMessage("DEVTM_BEGATEWAY_PAYMENT_SYSTEM_DESC"), "select", $payments),
					array("group_ids", Loc::getMessage("DEVTM_BEGATEWAY_GROUPS_DESC"), "select", $groups)
				);

$tabs = array(
			array(
				"DIV" => "edit1",
				"TAB" => Loc::getMessage("DEVTM_BEGATEWAY_TAB_NAME"),
				"ICON" => "begateway-icon",
				"TITLE" => Loc::getMessage("DEVTM_BEGATEWAY_TAB_DESC")
			),
		);
		
$o_tab = new CAdminTabControl("beGatewayTabControl", $tabs);

if( $REQUEST_METHOD == "POST" && strlen( $save . $reset ) > 0 && check_bitrix_sessid() )
{
	if( strlen($reset) > 0 )
		\Bitrix\Main\Config\Option::delete( $module_id );
	else
	{
		foreach( $all_options as &$option )
		{
			if( isset( $_REQUEST[$option[0]] ) )
			{
				if( ( $option[2] == "text" || $option[2] == "textarea" ) )
					\Bitrix\Main\Config\Option::set( $module_id, $option[0], $_REQUEST[$option[0]] );
				else
					if( $option[2] == "select" )
					{
						foreach( $option[3] as $k => &$v )
						{
							if(is_array($_REQUEST[$option[0]]) && in_array($v["value"],$_REQUEST[$option[0]]))
							{
								\Bitrix\Main\Config\Option::set( $module_id, $option[0], implode("|",$_REQUEST[$option[0]]) );
								break;
							}
							elseif( $_REQUEST[$option[0]] == $v["value"] )
							{
								\Bitrix\Main\Config\Option::set( $module_id, $option[0], $_REQUEST[$option[0]] );
								break;
							}
						}
					}
			}
		}
		
	}
	
	LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&".$o_tab->ActiveTabParam());
}

$o_tab->Begin();
?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($module_id)?>&amp;lang=<?echo LANGUAGE_ID?>">
<?
$o_tab->BeginNextTab();
foreach( $all_options as &$option ):
	$type = $option[2];
	if($option[0] == "group_ids")
		$cur_opt_val = explode("|",Bitrix\Main\Config\Option::get( $module_id, $option[0] ));
	else
		$cur_opt_val = Bitrix\Main\Config\Option::get( $module_id, $option[0] );
?>
	<tr>
		<td width="40%" <?if($type == "textarea") echo "class=\"adm-detail-valign-top\""?>>
			<label for="<?echo htmlspecialcharsbx($option[0])?>"><?echo $option[1]?>:</label>
		</td>
		<td width="60%">
			<?if($type == "text"):?>
				<input type="text" value="<?echo htmlspecialcharsbx($cur_opt_val)?>" name="<?echo htmlspecialcharsbx($option[0])?>">
			<?elseif($type == "textarea"):?>
				<textarea rows="20" cols="40" name="<?echo htmlspecialcharsbx($option[0])?>"><?echo htmlspecialcharsbx($cur_opt_val)?></textarea>
			<?elseif($type == "select"):?>
				<select <?if($option[0] == "group_ids") echo "multiple"?> name="<?= $option[0]?><?if($option[0] == "group_ids") echo "[]"?>">
				<?foreach($option[3] as $v):?>
					<option value="<?= $v["value"]?>" <?if(in_array($v["value"],$cur_opt_val) || $cur_opt_val == $v["value"]) echo "selected"?>><?= $v["title"]?></option>
				<?endforeach?>
				</select>
			<?endif?>
		</td>
	</tr>
<?endforeach?>
<?$o_tab->Buttons();?>
	<input type="submit" name="save" value="<?= Loc::getMessage("DEVTM_BEGATEWAY_SAVE_BTN_NAME")?>" title="<?= Loc::getMessage("DEVTM_BEGATEWAY_SAVE_BTN_NAME")?>" class="adm-btn-save">
	<input type="submit" name="reset" title="<?= Loc::getMessage("DEVTM_BEGATEWAY_RESET_BTN_NAME")?>" OnClick="return confirm('<?echo AddSlashes(Loc::getMessage("DEVTM_BEGATEWAY_RESTORE_WARNING"))?>')" value="<?= Loc::getMessage("DEVTM_BEGATEWAY_RESET_BTN_NAME")?>">
	<?=bitrix_sessid_post();?>
<?$o_tab->End();?>
</form>
