<?php
require_once 'init.php';
$errorText = "";

$current_base = check_base(intval($_GET["id"]), $active_site);

if($current_base) {
    $Page["id"] = ["base-see", "base-" . $current_base->id];
    $Page["title"] = $current_base->title . " | ReLead";
} else {
    $Page["id"] = "base-notfound";
    $Page["title"] = "База не найдена | ReLead";
}
$event = intval($_GET["event"]);
$leadCount = $mdb->get_var("SELECT COUNT(*) FROM `{$mdb->referrers}` WHERE `base_id` = '{$current_base->id}'");
$per_page = 20;
$pageCount = ceil($leadCount / $per_page);
$page = intval($_GET["p"]);

if($page < 1)
    $page = 1;
pil_office_header();
function display_base_nav() {
    global $page, $pageCount;
    ?>
    <nav aria-label="Навигация по базе" class="page-nav">
  <ul class="pagination">
    <li class="page-item <?=$page <= 2 ? "disabled" : ""?>">
      <a class="page-link" href="<?=url_with_param("p", 1)?>" aria-label="Первая">
        <span aria-hidden="true">&laquo;</span>
        <span class="sr-only">Первая</span>
      </a>
    </li>
    <li class="page-item <?=$page <= 1 ? "disabled" : ""?>">
      <a class="page-link" href="<?=url_with_param("p", $page - 1)?>" aria-label="Предыдущая">
        <span aria-hidden="true">‹</span>
        <span class="sr-only">Предыдущая</span>
      </a>
    </li>
    </ul>
    <form method="GET" action="" onsubmit="location = '<?=url_with_param("p", false)?>&p=' + this.p.value; return false;">
        <input type="number" name="p" value="<?=$page?>" min="1" max="<?=$pageCount?>" class="form-control column"> из <?=$pageCount?>
    </form>
    <ul class="pagination">
    <li class="page-item <?=$page >= $pageCount ? "disabled" : ""?>">
      <a class="page-link" href="<?=url_with_param("p", $page + 1)?>" aria-label="Следующая">
        <span aria-hidden="true">›</span>
        <span class="sr-only">Следующая</span>
      </a>
    </li>
    <li class="page-item <?=$page >= $pageCount - 1 ? "disabled" : ""?>">
      <a class="page-link" href="<?=url_with_param("p", $pageCount)?>" aria-label="Последняя">
        <span aria-hidden="true">&raquo;</span>
        <span class="sr-only">Последняя</span>
      </a>
    </li>
  </ul>
</nav>
    <?
}
?>
<div class="wrap">
<? if($current_base) : ?>
<h1>База контактов: <?=$current_base->title?></h1>
<div class="mb-3">
    <div class="float-left filter-form form-inline">
        <div class="form-group mr-2">
        <select id="event_select" name="event" class="form-control">
             <?
             $options = [];
             $options[] = [0, "Все цели"];
             foreach($event_ids as $event_id) {
                 $options[] = [$event_id->id, $event_id->title];
             }
             foreach($options as $option) {
                 $selected = $option[0] == $event ? "selected" : "";
                 ?><option value="<?=$option[0]?>" <?=$selected?>><?=$option[1]?></option><?
             }
             ?>
        </select>
        </div>
        <button onclick="location = '?<?=del_get_param($_SERVER["QUERY_STRING"], "event")?>&event=' +  $('#event_select').val();" type="button" class="btn btn-primary">Отфильтровать</button>
    </div>
    <div class="float-right">
        <? display_base_nav(); ?>
    </div>
</div>
<table class="table table-dark table-striped clients-table">
  <thead>
    <tr>
      <th class="number-col" scope="col">№</th>
      <th class="ident-col" scope="col"><?=$current_base->ident?></th>
      <th scope="col">Рефералов</th>
      <th scope="col">Рефералов<br>(уникальные)</th>
      <th scope="col">Реферальных целей</th>
      <th scope="col">Реферальных целей<br>(уникальные)</th>
    </tr>
  </thead>
  <tbody>
      <?php
        if($event != 0)
        {
            $event_query = " AND `event_id` = '{$event}'";
        }
        $query = "SELECT * FROM `{$mdb->referrers}` WHERE `base_id` = '{$current_base->id}'";
		    $query.= " LIMIT {$per_page} OFFSET " . ($per_page * ($page - 1));
        $referrers = $mdb->get_results($query);
        $i = 1 + ($per_page * ($page - 1));
        foreach($referrers as $ref)
        {
            ?>
            <tr>
                <td scope="row"><?=$i++?></td>
                <td class="ident-col"><?=$ref->ident?></td>
                <td><?=$ref->refs?></td>
                <td><?=$ref->uniq_refs?></td>
                <td><?=$ref->events?></td>
                <td><?=$ref->uniq_events?></td>
            </tr>
            <?
        }
      ?>
  </tbody>
</table>
<div class="float-left">
  <a class="btn btn-primary" href="<?=OFFICE_URI?>get-rate-xls?id=<?=$current_base->id?>">Рейтинговая таблица</a>
    <a class="btn btn-primary ml-2" href="<?=OFFICE_URI?>export-base?id=<?=$current_base->id?>">Создать выгрузку</a>
    <a class="btn btn-primary ml-2" href="<?=OFFICE_URI?>insert-base?id=<?=$current_base->id?>">Добавить контакт</a></div>
<div class="float-right">
    <? display_base_nav(); ?>
</div>
<? else : ?>
<h1>База не найдена.</h1>
Выберите базу из меню слева.
<? endif; ?>
</div>
<? pil_office_footer(); ?>