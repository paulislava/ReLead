<?php
require_once 'init.php';
$errorText = "";
$success = false;
$current_base = check_base(intval($_GET["id"]), $active_site);
if($_POST["submit"] == 1 && $current_base)
{
    $ident = sqlstring($_POST["ident"]);
    $code = sqlstring($_POST["code"]);
    $param1 = sqlstring($_POST["param1"]);
    $param2 = sqlstring($_POST["param2"]);
    $param3 = sqlstring($_POST["param3"]);
    $param4 = sqlstring($_POST["param4"]);
    $param5 = sqlstring($_POST["param5"]);

    $insert = insert_base($active_site, $current_base->id, $ident, $code, $param1, $param2, $param3, $param4, $param5);
    $success = false;
    switch($insert) {
        case EMPTY_FIELDS: 
            $errorText = "Заполните все необходимые поля.";
        break;

        case REPEAT_CODE:
            $errorText = "Этот код уже используется для данного сайта.<br>Введите другое значение Кода реферала.";
        break;

        case UNKNOWN_ERROR:
            $errorText = "Произошла ошибка.<br>Повторите попытку позже.";
        break;

        case REPEAT_STRING: 
            $errorText = "Контакт с таким идентификатором уже есть в базе.";
        break;

        case SUCCESS:
            $success = true;
        break;
    }
}
if($current_base) {
    $Page["id"] = ["base-insert", "base-".$current_base->id];
    $Page["title"] = "Добавление контактов | ReLead";
} else {
    $Page["id"] = "base-notfound";
    $Page["title"] = "База не найдена | ReLead";
}

pil_office_header();
?>
<div class="wrap">
<? if($current_base) : ?>
<h1>Добавление в базу: <?=$current_base->title?></h1>
<? if($success) : ?>
Контакт успешно добавлен в базу.<br>
<a style="margin-top: 2em;" class="btn btn-primary" href="<?=OFFICE_URI?>insert-base.php?id=<?=$current_base->id?>">Добавить следующий</a><br>
<a style="margin-top: 2em;" class="btn btn-success" href="<?=OFFICE_URI?>base.php?id=<?=$current_base->id?>">Перейти к базе</a>
<? else : ?>
<form method="POST" action="" class="office-form">
    <? if($errorText != "") : ?>
    <div class="error"><?=$errorText?></div>
    <? endif; ?>
    <input type="hidden" name="submit" value="1">
    <div class="form-group">
        <label for="ident"><?=$current_base->ident?></label>
        <input class="form-control" type="text" id="ident" value="<?=$_POST["ident"]?>" name="ident" required>
    </div>
    <? if($current_base->param1 != "") :?>
    <div class="form-group">
        <label for="param1"><?=$current_base->param1?></label>
        <input class="form-control" type="text" id="param1" value="<?=$_POST["param1"]?>" name="param1">
    </div>
    <? endif; ?>
    <? if($current_base->param2 != "") :?>
    <div class="form-group">
        <label for="param2"><?=$current_base->param2?></label>
        <input class="form-control" type="text" id="param2" value="<?=$_POST["param2"]?>" name="param2">
    </div>
    <? endif; ?>
    <? if($current_base->param3 != "") :?>
    <div class="form-group">
        <label for="param3"><?=$current_base->param3?></label>
        <input class="form-control" type="text" id="param3" value="<?=$_POST["param3"]?>" name="param3">
    </div>
    <? endif; ?>
    <? if($current_base->param14 != "") :?>
    <div class="form-group">
        <label for="param4"><?=$current_base->param4?></label>
        <input class="form-control" type="text" id="param4" value="<?=$_POST["param4"]?>" name="param4">
    </div>
    <? endif; ?>
    <? if($current_base->param5 != "") :?>
    <div class="form-group">
        <label for="param5"><?=$current_base->param5?></label>
        <input class="form-control" type="text" id="param5" value="<?=$_POST["param5"]?>" name="param5">
    </div>
    <? endif; ?>
    <div class="form-group">
        <label for="code">Код реферала</label>
        <input class="form-control" type="text" id="code" value="<?=$_POST["code"]?>" name="code">
        <small id="codeHelp" class="form-text">Оставьте пустым, чтобы сгенерировать код автоматически.</small>
    </div>
    <button type="submit" class="submitb btn btn-primary">Добавить клиента</button>
</form>
<? endif; ?>
<? else : ?>
<h1>База не найдена.</h1>
Выберите базу из меню слева.
<? endif; ?>
</div>
<? pil_office_footer(); ?>