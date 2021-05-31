<?php
define("OFFICE", true);
require_once '../init.php';
global $logined, $client, $active, $client_tarif, $active_site, $current_site, $current_base, $event_ids;
$current_base = false;
$logined = $_SESSION["office_logined"] == 1;
if(!defined("LOGIN_PAGE")) {
    if(!$logined)
    {
        header("Location: /office/login.php?redirect=" . $_SERVER["REQUEST_URI"]);
        die(0);
    }
    $client = sql_where($mdb->clients, array("id" => $_SESSION["office_id"]));
    if(!$client)
    {
        session_destroy();
        die(0);
    }
    
    $active = $client->end_date == 0 || $client->end_date > time();
    if(!$active)
    {
        if(!defined("PAY_PAGE"))
        {
            header("Location: ". OFFICE_URI . "pay.php");
            die(0);
        }
    }
    
    $client_tarif = sql_where($mdb->tarifs, array("id" => $client->tarif));
    
    if($active) {
        $active_site = $client->active_site;
        if($active_site != 0)
        {
            $current_site = sql_where($mdb->sites, array("id" => $active_site));
            if(!$current_site) {
                $client->active_site = 0;
                $active_site = 0;
            }
        }
        if($active_site == 0)
        {
            $current_site = sql_where($mdb->sites, array("client_id" => $client->id));
            if($current_site) {
                $active_site = $current_site->id;
                $client->active_site = $active_site;
                $mdb->query("UPDATE `{$mdb->clients}` SET `active_site` = '{$active_site}' WHERE `id` = '{$client->id}'");
            } else if(!defined("SITE_CREATING") && !defined("PAY_PAGE")) {
                header("Location: " . OFFICE_URI ."add-site.php");
                die(0);
            }
        }
    }
    
    

    
}
?>