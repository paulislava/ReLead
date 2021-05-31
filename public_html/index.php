<?php
require_once 'index-simple.php';
exit;
include_once 'init.php';
pil_header();
$tplPath = TEMPLATE_PATH . 'home/';
$blocks = scandir($tplPath);;
foreach($blocks as $block) {
    $path = $tplPath . $block;
    if(is_file($path))
    {
        include_once $path;
    }
}
pil_footer(); 
?>