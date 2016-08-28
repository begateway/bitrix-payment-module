<?php

use Bitrix\Main\Localization\Loc;

if( ! IsModuleInstalled("sale") ||
    ! function_exists("curl_init") ||
    ! function_exists("json_decode") ) return;

Loc::loadMessages(__FILE__);

class begateway_payment extends CModule {

  public $MODULE_ID = "begateway.payment";
  public $MODULE_VERSION;
  public $MODULE_VERSION_DATE;
  public $MODULE_NAME;
  public $MODULE_DESCRIPTION;
  public $MODULE_GROUP_RIGHTS = "N";

  function __construct() {
    $arModuleVersion = array();

    include(dirname(__FILE__) . "/version.php");

    if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
      $this->MODULE_VERSION = $arModuleVersion["VERSION"];
      $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
    }

    $this->MODULE_NAME = Loc::getMessage("MODULE_BEGATEWAY_NAME");
    $this->MODULE_DESCRIPTION = Loc::getMessage("MODULE_BEGATEWAY_DESCRIPTION");
    $this->PARTNER_NAME = "beGateway";
    $this->PARTNER_URI  = "https://begateway.com";
  }

  public function DoInstall() {
    $this->installFiles();
    \Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);
    return true;
  }

  public function DoUninstall() {
    $this->uninstallFiles();
    \Bitrix\Main\Config\Option::delete( $this->MODULE_ID );
    \Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);
    return true;
  }

  public function installFiles() {
    CopyDirFiles(
      $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/sale_payment/",
      $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_payment",
      true, true
    );
    CopyDirFiles(
      $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$this->MODULE_ID."/install/components/",
      $_SERVER["DOCUMENT_ROOT"]."/bitrix/components/",
      true, true
    );
    return true;
  }

  public function uninstallFiles() {
    DeleteDirFilesEx("/bitrix/components/begateway");
    DeleteDirFilesEx("/bitrix/php_interface/include/sale_payment/begateway");
    return true;
  }
}
