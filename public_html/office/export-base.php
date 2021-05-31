<?php
require_once 'init.php';
$success = false;
$errorText = "";
$current_base = check_base(intval($_GET["id"]), $active_site);

if($current_base) {
    $Page["id"] = ["base-export", "base-".$current_base->id];
    $Page["title"] = "Создание выгрузки | ReLead";
} else {
    $Page["id"] = "base-notfound";
    $Page["title"] = "База не найдена | ReLead";
}

$standart_uri = "https://" . $current_site->domain . "/";
$export_uri = $standart_uri;

if($_POST["submit"] == 1) {
    $export_uri = trim($_POST["export_uri"]);
    $success = true;
 //   header("Location: ".  OFFICE_URI . "get-base-xls.php?id=".$current_base->id."&url=".urlencode($export_uri));
}

pil_office_header();
?>
<div class="wrap">
<? if($current_base) : ?>
<h1>Создание выгрузки: <?=$current_base->title?></h1>
<? if($success) : 
$link = OFFICE_URI . "get-base-xls.php?id=".$current_base->id."&url=".urlencode($export_uri);
?>
    Выгрузка успешно создана.<br>
    Разошлите реферальные ссылки в выгрузке клиентам, чтобы они делились ими с друзьями.<br><br>
    Если скачивание не началось, перейдите по <a href="<?=$link?>">ссылке</a>.
    <script>window.open('<?=$link?>');</script>
    <div class="mt-5">
    <a class="btn btn-primary" href="<?=$link?>">Скачать выгрузку</a>
    <a class="btn btn-success ml-2" href="<?=OFFICE_URI?>base.php?id=<?=$current_base->id?>">Перейти к базе</a>
    </div>
<? else : ?>
<form class="office-form" method="POST" action="">
    <? if($errorText != "") : ?>
    <div class="error"><?=$errorText?></div>
    <? endif; ?>
    <input type="hidden" name="submit" value="1">
    <div class="form-group">
        Вы можете использовать предоставленную выгрузку для рассылки в системах E-mail-информирования (Mailigen, SendPulse и т.д.) или для самостоятельного распространения другими способами.
    </div>
    <div class="form-group">
        Введите адрес целевой страницы для данной рассылки.<br>Например, адрес страницы регистрации или покупки товара.<br><br>
    </div>
    <div class="form-group">
        <label for="exportUri">Адрес целевой страницы</label>
        <input type="text" class="form-control" placeholder="<?=$standart_uri?>" required id="exportUri" name="export_uri" value="<?=$export_uri?>">
    </div>
    <button type="submit" class="submitb btn btn-primary">Создать выгрузку</button>
</form>
<? endif; ?>
<? else : ?>
<h1>База не найдена.</h1>
Выберите базу из меню слева.
<? endif; ?>
</div>
<? pil_office_footer(); ?>