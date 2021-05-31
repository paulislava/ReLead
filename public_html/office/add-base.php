<?php
require_once 'init.php';
$success = false;
$errorText = "";
$Page["id"] = ["base-add", "create"];
$Page["title"] = "Создание базы | ReLead";

if($_POST["submit"] == 1) {

    $title = sqlstring($_POST["title"]);
    $ident = sqlstring($_POST["ident"]);
    $param1 = sqlstring($_POST["param1"]);
    $param2 = sqlstring($_POST["param2"]);
    $param3 = sqlstring($_POST["param3"]);
    $param4 = sqlstring($_POST["param4"]);
    $param5 = sqlstring($_POST["param5"]);
    
    $create = create_base($active_site, $title, $ident, $param1, $param2, $param3, $param4, $param5);
    switch($create["result"]) {
        case EMPTY_FIELDS:
            $errorText = "Заполните все необходимые поля!";
            break;
        case REPEAT_STRING:
            $errorText = "База с таким названием уже создана для этого сайта.";
            break;
        case UNKNOWN_ERROR:
            $errorText = "Произошла ошибка.<br>Повторите попытку позже.";
            break;
        case SUCCESS:
            $success = true;
            $baseID = $create["id"];
            break;
    }
}

pil_office_header();
?>
<div class="wrap">
<h1>Создание базы клиентов</h1>
<? if($success) : ?>
База "<?=$title?>" успешно создана.<br>
<a style="margin-top: 2em;" class="btn btn-primary" href="<?=OFFICE_URI?>insert-base.php?id=<?=$baseID?>">Начать заполнять</a>
<? else : ?>
<form method="POST" action="" class="office-form">
    <? if($errorText != "") : ?>
    <div class="error"><?=$errorText?></div>
    <? endif; ?>
    <input type="hidden" name="submit" value="1">
    <div class="choice-links">
      <a href="<?=OFFICE_URI . "add-base-import.php"?>">Загрузить из .xls</a>
      <a class="active" href="<?=OFFICE_URI . "add-base.php"?>">Создать вручную</a>
  </div>
    <div class="form-group">
    <label for="base_title">Название базы</label>
    <input type="text" value="<?=$_POST["title"]?>" class="form-control" name="title" id="base_title" placeholder="База №1" required>
    </div>
    <div class="form-separator">
    <h2>Названия столбцов в выгрузках</h2>
    </div>
    <div class="form-group">
    <label for="base_ident">Название идентификатора</label>
    <input type="text" value="<?=$_POST["ident"]?>" aria-describedby="identHelp" class="form-control" name="ident" id="base_ident">
     <small id="identHelp" class="form-text">По этому столбцу Вы сможете отслеживать эффективность канала продвижения.<br> Например:  «Клиент», «Компания», «Рекламный канал» и т.д.</small>
    </div>
    <div class="form-group">Ниже Вам необходимо ввести параметры, которые необходимы Вам для распространения реферальных ссылок.<br>Например: E-mail, Телефон, VK и т.д.
    </div>
    <div class="form-group">
    <label for="base_param1">Параметр 1 (необязательно)</label>
    <input type="text" value="<?=$_POST["param1"]?>" class="form-control" name="param1" id="base_param1">
    <small class="form-text">
        Например, «E-mail»
    </small>
    </div>
    <div class="form-group">
    <label for="base_param2">Параметр 2 (необязательно)</label>
    <input type="text" value="<?=$_POST["param2"]?>" class="form-control" name="param2" id="base_param2">
    <small class="form-text">
        Например, «Номер телефона»
    </small>
    </div>
    <div class="form-group">
    <label for="base_param3">Параметр 3 (необязательно)</label>
    <input type="text" value="<?=$_POST["param3"]?>" class="form-control" name="param3" id="base_param3">
    
    </div>
    <div class="form-group">
    <label for="base_param4">Параметр 4 (необязательно)</label>
    <input type="text" value="<?=$_POST["param4"]?>" class="form-control" name="param4" id="base_param4">
    </div>
    <div class="form-group">
    <label for="base_param5">Параметр 5 (необязательно)</label>
    <input type="text" value="<?=$_POST["param5"]?>" class="form-control" name="param5" id="base_param5">
    </div>
    <button type="submit" class="submitb btn btn-primary">Создать базу</button>
</form>
<? endif;?>
</div>
<? pil_office_footer(); ?>