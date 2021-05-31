<?php
set_time_limit(0);
require_once 'init.php';

$success = false;
$errorText = "";
$current_base = check_base(intval($_GET["id"]), $active_site);
if($_POST["submit"] == 1 && $current_base)
{
    $file = $_FILES["base_file"];
    $start_row = intval($_POST["start_row"]);
    $ident = intval($_POST["ident"]) ;
    $param1 = intval($_POST["param1"]);
    $param2 = intval($_POST["param2"]);
    $param3 = intval($_POST["param3"]);
    $param4 = intval($_POST["param4"]);
    $param5 = intval($_POST["param5"]);
    
    if(!$file || $file['error'] !== UPLOAD_ERR_OK) {
        $errorText = "Ошибка при загрузке файла.<br>Повторите попытку позже.";
    } else if($ident == 0 || 
        $start_row < 1 ||
        ($current_base->param1 != "" && $param1 < 1) ||
        ($current_base->param2 != "" && $param2 < 1) ||
        ($current_base->param3 != "" && $param3 < 1) ||
        ($current_base->param4 != "" && $param4 < 1) ||
        ($current_base->param5 != "" && $param5 < 1)) {
        $errorText = "Заполните все необходимые поля!";
    } else {
        $import = import_base($active_site, $current_base->id, $file['tmp_name'], $start_row, $ident - 1, $param1 - 1, $param2 - 1, $param3 - 1, $param4 - 1, $param5 - 1);
        switch($import[0]) {
            case UNKNOWN_ERROR:
                $errorText = "Произошла ошибка.<br>Повторите попытку позже.";
                break;
            case SUCCESS:
                $success = true;
                $count = $import[1];
                break;
        }
    }
}

if($current_base) {
    $Page["id"] = ["base-import", "base-".$current_base->id];
    $Page["title"] = "Импорт записей | ReLead";
} else {
    $Page["id"] = "base-notfound";
    $Page["title"] = "База не найдена | ReLead";
}

pil_office_header();
?>
<div class="wrap">
<? if($current_base) : ?>
<h1>Импорт записей: <?=$current_base->title?></h1>
<? if($success) : ?>
Контакты <b>(<?=$count?>)</b> успешно импортированы.<br>
<a style="margin-top: 2em;" class="btn btn-primary" href="<?=OFFICE_URI?>base.php?id=<?=$current_base->id?>">Открыть базу</a>
<? else : ?>
<form enctype="multipart/form-data" method="POST" action="" class="office-form">
    <? if($errorText != "") : ?>
    <div class="error"><?=$errorText?></div>
    <? endif; ?>
    <input type="hidden" name="submit" value="1">
    <div class="form-group">
    <label for="chooseFile">База данных</label>
     <div class="custom-file">
        <input accept=".xls, .xlsx" type="file" class="custom-file-input" onchange="$('#fileLabel').text(this.files[0].name)" name="base_file" id="chooseFile" required>
        <label class="custom-file-label" id="fileLabel" for="chooseFile">Выберете файл...</label>
    </div>
    <small id="fileHelp" class="form-text text-muted">Выберете файл с базой клиентов в форматах: .xls, .xlsx</small>
    </div>
    <div class="form-group">
        <label for="startRow">Первая строка данных</label>
        <input class="form-control" type="number" id="startRow" name="start_row" min="1" required value="2">
        <small id="startRowHelp" class="form-text text-muted">Введите номер первой строки данных, чтобы исключить заголовки.</small>
    </div>
    <div class="form-separator">
        <h2>Номера столбцов</h2>
        <small class="form-text">Введите номера столбцов в выбранном файле для каждого из параметров.</small>
    </div>
    <div id="col-numbers">
    <div class="form-group">
        <label for="ident"><?=$current_base->ident?></label>
        <input class="form-control" type="number" id="ident" name="ident" min="1" required>
    </div>
    <? if($current_base->param1 != "") :?>
    <div class="form-group">
        <label for="param1"><?=$current_base->param1?></label>
        <input class="form-control" type="number" id="param1" name="param1" min="1" required>
    </div>
    <? endif; ?>
    <? if($current_base->param2 != "") :?>
    <div class="form-group">
        <label for="param2"><?=$current_base->param2?></label>
        <input class="form-control" type="number" id="param2" name="param2" min="1" required>
    </div>
    <? endif; ?>
    <? if($current_base->param3 != "") :?>
    <div class="form-group">
        <label for="param3"><?=$current_base->param3?></label>
        <input class="form-control" type="number" id="param3" name="param3" min="1" required>
    </div>
    <? endif; ?>
    <? if($current_base->param4 != "") :?>
    <div class="form-group">
        <label for="param4"><?=$current_base->param4?></label>
        <input class="form-control" type="number" id="param4" name="param4" min="1" required>
    </div>
    <? endif; ?>
    <? if($current_base->param5 != "") :?>
    <div class="form-group">
        <label for="param5"><?=$current_base->param5?></label>
        <input class="form-control" type="number" id="param5" name="param5" min="1" required>
    </div>
    <? endif; ?>
    </div>
    <button type="submit" class="submitb btn btn-primary">Импортировать</button>
</form>
<? endif; ?>
<? else : ?>
<h1>База не найдена.</h1>
Выберите базу из меню слева.
<? endif; ?>
</div>
<? pil_office_footer(); ?>