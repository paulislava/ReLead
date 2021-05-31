<?php
session_start();
require_once 'config.php';
if(defined("DEBUG") && DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
define("WP_DEBUG", DEBUG);
require_once INCLUDE_PATH . 'mdb.php';
define("DB_CHARSET", $config["mysql"]["charset"]);
$mdb = new mdb($config["mysql"]["login"], $config["mysql"]["pass"], $config["mysql"]["db"], $config["mysql"]["host"]);

$mdb->prefix = $config["mysql"]["prefix"];
$mdb->clients = $mdb->prefix . 'clients';
$mdb->sites = $mdb->prefix . 'sites';
$mdb->bases = $mdb->prefix . 'bases';
$mdb->referrers = $mdb->prefix . 'referrers';
$mdb->scores = $mdb->prefix . 'scores';
$mdb->referrals = $mdb->prefix . 'referrals';
$mdb->event_ids = $mdb->prefix . 'event_ids';
$mdb->events = $mdb->prefix . 'events'; 
$mdb->tarifs = $mdb->prefix . 'tarifs';
$mdb->pays = $mdb->prefix . 'pays';
$mdb->entity_orders = $mdb->prefix . 'entity_orders';
$mdb->pass_restories = $mdb->prefix . 'pass_restories';
$mdb->temp_bases = $mdb->prefix . 'tempbases';


require_once 'includes/functions.php';
require_once 'includes/Mailing/Mailing.php';
require_once 'includes/Relead.php';

global $config, $Page, $mdb;
$Page["title"] = $config["site"]["title"];
date_default_timezone_set(TIMEZONE);
?>