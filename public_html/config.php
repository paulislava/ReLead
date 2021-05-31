<?php
global $config;

define("DEBUG", isset($_GET["debug"]) && $_GET["debug"] == 1);
if(DEBUG) {
    ini_set("display_errors", 1);
    error_reporting(E_ALL);
}
// DEFs
define("HOME_URI", "https://relead.paulislava.space");
define("LOAD_URI", HOME_URI . "/relead/load.js");
define("OFFICE_URI", HOME_URI . "/office/");
define("RESTORE_URI", HOME_URI . "/office/createpass.php?token=%s");

define("ROOT", dirname(__FILE__) . '/');
define("INCLUDE_PATH", ROOT . "includes/");
define("TEMPLATE_PATH", ROOT . "templates/");
define("MAILING_TEMPLATE_PATH", TEMPLATE_PATH . "mailing/");
define("TMP_BASE_PATH", ROOT . "tmpbases/");
define("SALT", "pil_refsystem_19");

define("OFFICE_STYLE_VERS", "2.4");
define("SITE_STYLE_VERS", "2.6");
define("RESET_STYLE_VERS", "1.0");
define("ANIM_STYLE_VERS", "1.0");
define("BOOTSTRAP_STYLE_VERS", "1.0");

define("JQUERY_SCRIPT_VERS", "1.0");
define("BOOTSTRAP_SCRIPT_VERS", "1.0");
define("SITE_SCRIPT_VERS", "1.3");

// Answers
define("EMPTY_FIELDS", '0');
define("NO_REF", '1');
define("WRONG_EVENT", '2');
define("SUCCESS", '3');
define("UNKNOWN_ERROR", '4');
define("REPEAT_STRING", '5');
define("REPEAT_CODE", '6');

define("TARIF_TEST", 1);
define("TIMEZONE", 'Asia/Yekaterinburg');
define("DATE_FORMAT", "d-m-Y");

define("PARAMS_MAX", 5);

define("TMPBASE_KEEP_DUR", 60 * 60);
define("TMPBASE_CLIENT_MAX", 3);
// Global

// Robokassa
$config["robokassa"]["test"] = false;
$config["robokassa"]["receipts"] = true;
$config["robokassa"]["sno"] = "usn_income";
$config["robokassa"]["tax"] = "none";
$config["robokassa"]["shop_id"] = "relead-online";
$config["robokassa"]["pay_desc"] = "Тариф «%s»";
$config["robokassa"]["culture"] = "ru";
$config["robokassa"]["curr"] = "BANKOCEAN2R";
$config["robokassa"]["url"] = "https://auth.robokassa.ru/Merchant/Index.aspx";
if(!$config["robokassa"]["test"])
{
    $config["robokassa"]["pass1"] = 'X5Gxp4RD9cm7Uhbi5RVM';
    $config["robokassa"]["pass2"] = 'T0cPNg4me4jSfLTgv9i1';
} else {
    $config["robokassa"]["pass1"] = 'fC0vfK8coMVUd84Vz8jI';
    $config["robokassa"]["pass2"] = 'nci2hR0g6Dnw9SB2pqlx';
}

// MySQL data
$config["mysql"]["host"] = 'localhost';
$config["mysql"]["db"] = 'admin_relead';
$config["mysql"]["login"] = 'admin_relead';
$config["mysql"]["pass"] = 'I8bU8ovEJ3';
$config["mysql"]["charset"] = 'utf8';
$config["mysql"]["prefix"] = "";


// Site info
$config["site"]["title"] = "ReLead – система лидогенерации";
$config["site"]["url"] = "https://relead.online/";


// Ref system
$config["referral"]["expire"] = 60*60*24*30;
$config["referral"]["event_uri_format"] =  HOME_URI . "/relead/event.php?";

// Score system
$config["score"]["uri_format"] = HOME_URI . "/score?b=%d&c=%s";

// Notify system
$config["notify"]["from"] = "ReLead <notify@relead.paulislava.space>";
$config["notify"]["mails"] = ["eperoshenko@gmail.com", "p-kondratov@mail.ru"];
$config["notify"]["entity_orders"] = true;

$config["mailing"]["support"] = "Техподдержка ReLead <support@relead.paulislava.space>";

$config["consultant"] = false;
?>