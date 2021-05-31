<?php
function file_get_contents_curl($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $data = curl_exec($ch);
    $redirectedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    return [$redirectedUrl, $data];
}

$url = trim($_REQUEST["url"]);
if(mb_strpos($url, "https://") === FALSE && mb_strpos($url, "http://") === FALSE)
    $url = "http://" . $url;
$link = parse_url($url);
$geturl = $link["scheme"] . "://" . idn_to_ascii($link["host"]) . $link["path"] . "?" . $link["query"] . $link["fragment"];

$get = file_get_contents_curl($geturl);
$html = $get[1];

$reurl = $get[0];
$link = parse_url($reurl);

preg_match('/<title>(.+)<\/title>/',$html,$matches);
$title = $matches[1];
$host = $link["host"];
$response = array("host" => $host, "title" => htmlspecialchars_decode($title), "https" => $link["scheme"] == "https");
echo json_encode($response);
?>