<?php
global $Page, $mdb, $client, $client_tarif, $active_site, $event_ids, $bases;

if($Page["id"] == "site-add") {
    $active_site = 0;
}

    if($active_site != 0)
    {
        $query = "SELECT * FROM `{$mdb->event_ids}` WHERE `site_id` = '{$active_site}'";
        $event_ids = $mdb->get_results($query);
    }
?>
<aside id="sidebar">
    <div id="client_block">
    <h2 id="client_name"><?=$client->email?></h2>
    <a id="tarif_title" href="<?=OFFICE_URI?>pay"><?=$client_tarif->title?></a>
    <div id="client_links">
        <a href="<?=OFFICE_URI?>logout">Выйти из системы</a>
    </div>
    </div>
    <nav role="navigation" id="side-menu">
        <section>
            <h2>Сайты</h2>
            <? 
            $query = "SELECT * FROM `{$mdb->sites}` WHERE `client_id` = '{$client->id}'";
            $sites = $mdb->get_results($query);
            $sitesMenu = [];
            foreach($sites as $site) {
                $item = [];
                $item["id"] = "site-" . $site->id;
                $item["href"] = OFFICE_URI . "?site_id=". $site->id;
                $item["title"] = $site->title;
                $sitesMenu[] = $item;
            }
            $item = ["id" => "site-add", "href" => OFFICE_URI . "add-site", "title" => "+ Добавить сайт"]; 
            $addSiteMenu = [$item];
            display_menu($sitesMenu,  "site-".$active_site); ?>
            <? display_menu($addSiteMenu); ?>
        </section>
        <? if($active_site != 0) : ?>
        <section>
            <h2>Базы контактов</h2>
            <? 
            $query = "SELECT * FROM `{$mdb->bases}` WHERE `site_id` = '{$active_site}'";
            $bases = $mdb->get_results($query);
            $basesMenu = [];
            foreach($bases as $base) {
                $item = [];
                $itemID = "base-" . $base->id;
                $item["id"] = $itemID;
                $item["href"] = OFFICE_URI . "base?id=". $base->id;
                $item["title"] = $base->title;
                
                $seeItem = ["id" => "base-see", "href" => $item["href"], "title" => "Контакты"];
                $insertItem = ["id" => "base-insert", "href" => OFFICE_URI . "insert-base?id=".$base->id, "title" => "Добавить контакт"];
                $importItem = ["id" => "base-import", "href" => OFFICE_URI . "import-base?id=".$base->id, "title" => "Загрузить из .xls"];
                //$changeItem = ["id" => "base-change", "href" => OFFICE_URI . "change-base?id=".$base->id, "title" => "Изменить"];
                $exportItem = ["id" => "base-export", "href" => OFFICE_URI . "export-base?id=".$base->id, "title" => "Создать выгрузку"];
                $item["submenu"] = [$seeItem, $insertItem, $importItem, $exportItem]; 
                $basesMenu[] = $item;
            }
            $item = ["id" => "base-add", "href" => OFFICE_URI . "add-base-import", "title" => "+ Добавить базу"]; 
            $addBaseMenu = [$item];
            display_menu($basesMenu); ?>
            <? display_menu($addBaseMenu); ?>
        </section>
        <? endif; ?>
        <? if($active_site != 0) : ?>
        <section>
            <h2>Цели сайта</h2>
            <? 
            $eventsMenu = [];
            foreach($event_ids as $event_id) {
                $item = [];
                $item["id"] = "event-" . $event_id->id;
                $item["href"] = OFFICE_URI . "event?id=". $event_id->id;
                // $item["href"] = "#";
                $item["title"] = $event_id->title;
                $eventsMenu[] = $item;
            }
            $item = ["id" => "event-add", "href" => OFFICE_URI . "add-event", "title" => "+ Добавить цель"]; 
            $addEventMenu = [$item];
            display_menu($eventsMenu); ?>
            <? display_menu($addEventMenu); ?>
        </section>
        <? endif; ?>
    </nav>
</aside>