<?php
require_once 'init.php';
$errorText = "";

$current_event = check_event(intval($_GET["id"]), $active_site);

if ($current_event) {
    $event_id = $current_event->id;
    $Page["id"] = ["event-see", "event-" . $current_event->id];
    $Page["title"] = $current_event->title . " | ReLead";
} else {
    $Page["id"] = "event-notfound";
    $Page["title"] = "Цель не найдена | ReLead";
}
$event = intval($_GET["event"]);
pil_office_header();
?>
<div class="wrap">
    <? if($current_event) : 
    $count = $mdb->get_var("SELECT COUNT(*) FROM `{$mdb->events}` WHERE `event_id` = '{$event_id}'");
    $uniq_count = $mdb->get_var("SELECT COUNT(DISTINCT(`referral_id`)) FROM `{$mdb->events}` WHERE `event_id` = '{$event_id}'");
    ?>
    <div>
        <h1>Цель: <?= $current_event->title ?></h1>
        <p id="event-goals">
            <span class="h1 count"><?= $count ?></span> <?= plural_form($count, ["достижение", "достижения", "достижений"], false) ?> цели</p>
        <p id="event-uniq-goals">
            <span class="h1 count"><?= $uniq_count ?></span> <?= plural_form($uniq_count, ['реферал', 'реферала', 'рефералов'], false) ?>, достигших цель</p>
    </div>
    <?php
    $bases = $mdb->get_results("SELECT `id`, `title`, 
        (SELECT COUNT(*) FROM `events` WHERE `event_id` = '{$event_id}' AND `referrer_id` IN (SELECT `id` FROM `referrers` WHERE `base_id` = `t`.`id`)) AS `events`,
        (SELECT COUNT(DISTINCT(`referral_id`)) FROM `events` WHERE `event_id` = '{$event_id}' AND `referrer_id` IN (SELECT `id` FROM `referrers` WHERE `base_id` = `t`.`id`)) AS `uniq_events`
        FROM `{$mdb->bases}` `t` WHERE `site_id` = '{$current_site->id}' ORDER BY `events` DESC");
    ?>
    <?php if (count($bases)) : ?>
        <div class="lk-block">
            <h2>Статистика по базам</h2>
            <table class="table table-dark table-striped event-bases-table">
                <thead>
                    <tr>
                        <th class="title-col">База</th>
                        <th class="events-col">Достижений цели</th>
                        <th class="uniq-events-col">Уникальных достижений</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bases as $base) : ?>
                        <tr>
                            <td class="title-col"><?= $base->title ?></td>
                            <td class="events-col"><?= $base->events ?></td>
                            <td class="uniq-events-col"><?= $base->uniq_events ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
    <div class="lk-block">
        <h2>Информация по установке</h3>
        <p>Идентификатор цели: <span class="h4"><?=$current_event->event_code?></span></p>
        JavaScript-код для фиксации цели:
<code>rl_event('<?=$current_event->event_code?>')</code>
    </div>
    <? else : ?>
    <h1>Цель не найдена.</h1>
    Выберите цель из меню слева.
    <? endif; ?>
</div>
<? pil_office_footer(); ?>