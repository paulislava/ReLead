<?php
require_once 'init.php';
$errorText = "";

$current_base = check_base(intval($_GET["id"]), $active_site);

if($current_base) {
    $Page["id"] = ["base-change", "base-".$current_base->id];
    $Page["title"] = $current_base->title . " | ReLead";
} else {
    $Page["id"] = "base-notfound";
    $Page["title"] = "База не найдена | ReLead";
}

pil_office_header();
?>
<div class="wrap">
<? if($current_base) : ?>
<h1>Редактирование базы: <?=$current_base->title?></h1>
<? else : ?>
<h1>База не найдена.</h1>
Выберите базу из меню слева.
<? endif; ?>
</div>
<? pil_office_footer(); ?>