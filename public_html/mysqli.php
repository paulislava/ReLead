<?php
global $mysql, $config;

$data = $config["mysql"];
$mysql = new mysqli($data["host"], $data["login"], $data["pass"], $data["db"]);
if ($mysql->connect_error) 
    die("Couldn't connect to MySQL: " . $mysql->connect_error);

$mysql->set_charset($charset);
$mysql->prefix = $data["prefix"];
$mysql->clients = $mysql->prefix . 'clients';
$mysql->sites = $mysql->prefix . 'sites';
$mysql->bases = $mysql->prefix . 'bases';
$mysql->referrers = $mysql->prefix . 'referrers';
$mysql->referrals = $mysql->prefix . 'referrals';
$mysql->event_ids = $mysql->prefix . 'event_ids';
$mysql->events = $mysql->prefix . 'events'; 
$mysql->tarifs = $mysql->prefix . 'tarifs';
$mysql->pays = $mysql->prefix . 'pays';

function sqlstring($string)
{
    global $mysql;
    return trim($mysql->real_escape_string($string));
}
?>