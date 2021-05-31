<?php
require_once 'init.php';
$Page["id"] = "office-home";
$Page["title"] = "Личный кабинет | ReLead";
$newSiteID = intval($_GET["site_id"]);
if($newSiteID > 0 && $newSiteID != $active_site)
{
    $get_site = sql_where($mdb->sites, array("client_id" => $client->id, "id" => $newSiteID));
    if($get_site) {
        $current_site = $get_site;
        $client->active_site = $newSiteID;
        $active_site = $newSiteID;
        $mdb->query("UPDATE `{$mdb->clients}` SET `active_site` = '{$active_site}' WHERE `id` = '{$client->id}'");
    }
}
pil_office_header();
?>
<div class="wrap">
    <h1><?=$current_site->title?></h1>
    <h2>Базы клиентов</h2>
    <div class="bases">
        <? 
        if(count($bases) > 0) : 
            foreach($bases as $base) {
                $title = $base->title;
                $href = OFFICE_URI . "base.php?id=". $base->id;
                ?>
                <div class="base-link"><a title="Перейти к базе" href="<?=$href?>"><?=$title?></a></div>
                <?
            }
        else : ?>
        <div class="text">
            У Вас пока нет добавленных баз контактов.<br>
            <a class="link" href="<?=OFFICE_URI?>add-base-import.php">Загрузите вашу базу контактов</a> или <a class="link" href="<?=OFFICE_URI?>add-base.php">добавьте вручную</a>
        </div>
        <? endif; ?>
    </div>
    
    <h2 class="mt-5">Цели сайта</h2>
    <div id="siteEvents">
        <ul>
        <? 
        if(count($event_ids) > 0) : 
            foreach($event_ids as $event) {
                $title = $event->title;
                //$href = OFFICE_URI . "base.php?id=". $base->id;
                $href = "#";
                ?>
                <li><?=$title?></li>
                <?
            }
        ?>
        </ul>
        <?
        else : ?>
        <div class="text">
            Вы ещё не создали цели для этого сайта.<br>
            <a class="btn btn-primary mt-4" href="<?=OFFICE_URI?>add-event.php">Добавить цель</a>
        </div>
        <? endif; ?>
    </div>

    <h2 class="mt-5">Код для встраивания</h2>
    <code><?
    $script = '<script type="text/javascript" src="'.LOAD_URI.'"></script>';
    $script = htmlspecialchars($script); 
    echo $script;
    ?></code>
* Для добавления кода обратитесь к вашему разработчику/администратору или сделайте это самостоятельно.<br>
</div>
<?
pil_office_footer();
?>