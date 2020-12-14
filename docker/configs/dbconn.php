<?
define("SHORT_INSTALL", true);
define("SHORT_INSTALL_CHECK", true);

define("MYSQL_TABLE_TYPE", "INNODB");
define("BX_UTF", true);

define("DBPersistent", false);
$DBType = "mysql";
$DBHost = "mysql";
$DBName = "sitemanager";
$DBLogin = "root";
$DBPassword = "root";
$DBDebug = false;
$DBDebugToFile = false;

define("BX_FILE_PERMISSIONS", 0664);
define("BX_DIR_PERMISSIONS", 0775);
@umask(~BX_DIR_PERMISSIONS);

define("BX_USE_MYSQLI", true);
define("DELAY_DB_CONNECT", true);
define("CACHED_menu", 120);
define("CACHED_b_file", 120);
define("CACHED_b_file_bucket_size", 10);
define("CACHED_b_lang", 120);
define("CACHED_b_option", 120);
define("CACHED_b_lang_domain", 120);
define("CACHED_b_site_template", 120);
define("CACHED_b_event", 120);
define("CACHED_b_agent", 120);

?>
