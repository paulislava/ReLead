<?php
require_once 'init.php';

$pay_id = intval($_REQUEST['InvId']);
$summ = $_REQUEST['OutSum'];
$crc_input = strtoupper($_REQUEST['SignatureValue']);
$pass = $config["robokassa"]["pass2"];
$crc = strtoupper(md5("$summ:$pay_id:$pass"));
if($crc != $crc_input)
{
    die("bad sign\n");
}

$confirm = confirm_pay($pay_id);
if(!$confirm)
    die("confirm error\n");
echo "OK{$pay_id}\n";
?>