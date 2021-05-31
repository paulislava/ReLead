<?php
require_once '../init.php';
header("Access-Control-Allow-Origin: *");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
$headers = getallheaders();
$site = render_site($_GET["location"], $_GET["param"]);
$referral = referral_id($site["data"]->id, $site["referrer"], $site["url"]["path"]);

if (!$referral || !isset($referral["id"]))
  die(0);

echo $referral["id"];
?>