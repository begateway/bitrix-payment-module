<?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$aMenu = array(
	"parent_menu" => "global_menu_services", 							// поместим в раздел "Сервис"
	"sort"        => 100,                    							// вес пункта меню
//	"url"         => "",												// ссылка на пункте меню
	"text"        => Loc::getMessage("DEVTM_BEGATEWAY_MENU_MAIN"), 		// текст пункта меню
	"title"       => "", 												// текст всплывающей подсказки
	"icon"        => "", 												// малая иконка
	"page_icon"   => "", 												// большая иконка
	"items_id"    => "devtm.begateway",  								// идентификатор ветви
	"items"       => array( 											// остальные уровни меню сформируем ниже.
						array(
							"text" => Loc::getMessage("DEVTM_BEGATEWAY_MENU_LIST"),
							"url"  => "begateway_products_list.php?lang=".LANGUAGE_ID,
							"icon" => "",
							"page_icon" => "",
							"title" => ""
						)
					),
);

return $aMenu;
