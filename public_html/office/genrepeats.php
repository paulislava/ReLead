<?php
require_once 'init.php';
$base_id = intval($_GET["base_id"]);
//SELECT `code`, MAX(`id`) FROM `referrers` WHERE `base_id` = 11 GROUP BY `code` HAVING COUNT(*) > 1
?>