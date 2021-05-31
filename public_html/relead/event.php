<?php
header("Access-Control-Allow-Origin: *");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once '../init.php';


$site = render_site($_GET["location"]);
global $mdb;
$site_id = $site["data"]->id;
//var_dump($site);
//die();
$referral_id = intval($_GET["referral_id"]);
if(!$referral_id)
    die(NO_REF);

$referral = db_where($mdb->referrals, array("id" => $referral_id));
if(!$referral) 
    die(NO_REF);

$event_code = sqlstring($_GET["event_id"]);
if($event_code == "")
    die(EMPTY_FIELDS);
$query = "SELECT `id` FROM `{$mdb->event_ids}` WHERE `site_id` = '{$site_id}' AND `event_code` = '{$event_code}' AND `is_php` = 0";
$eventID = $mdb->get_var($query); 
if(!$eventID)
    die(WRONG_EVENT);
$ip = $_SERVER["REMOTE_ADDR"];
$date = time();
$path = sqlstring($site["url"]["path"]);
$query = "INSERT INTO `{$mdb->events}` SET `site_id` = '{$site_id}', `event_id` = '{$eventID}', `url` = '{$path}', `referrer_id` = '{$referral->ref_id}', `referral_id` = '{$referral->id}', `ip` = '{$ip}', `date` = '{$date}'";
$result = $mdb->query($query);

$query = "UPDATE `{$mdb->referrers}` SET `events` = 
(SELECT COUNT(*) FROM `{$mdb->events}` WHERE `referrer_id` = '{$referral->ref_id}')
, `uniq_events` = 
(SELECT COUNT(DISTINCT(`referral_id`)) FROM `{$mdb->events}` WHERE `referrer_id` = '{$referral->ref_id}') 
WHERE `id` = '{$referral->ref_id}'";

$update_refs = $mdb->query($query);

if(!$result)
    die(UNKNOWN_ERROR);
echo SUCCESS;
?>