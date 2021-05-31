<?php
set_time_limit(0);
require_once 'init.php';
$success = false;
$errorText = "";
$Page["id"] = ["base-add", "import"];
$Page["title"] = "Загрузка базы | ReLead";
$selecting = false;

$file = $_GET["fname"];
if($file != "" && file_exists(TMP_BASE_PATH . $file)) {
	$selecting = true;
	$filePath = TMP_BASE_PATH . $file;
    require_once INCLUDE_PATH . 'PHPExcel.php';
    require_once INCLUDE_PATH . 'PHPExcel/IOFactory.php';
	require_once INCLUDE_PATH . 'PHPExcel/Cell.php';
    $objReader = PHPExcel_IOFactory::createReaderForFile($filePath);
    $objReader->setReadDataOnly(true);
    $xls = $objReader->load($filePath);
    $xls->setActiveSheetIndex(0);
    
    $sheet = $xls->getActiveSheet();
	$count = $sheet->getHighestDataRow() - 1; 
	$head_row = 1;
	
	$heads = [];
	$lastCol = PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn());
	$columns = 1; /// index;
	for($i = 0; $i <= $lastCol; $i++) {
	    $title = sqlstring($sheet->getCellByColumnAndRow($i, $head_row));
	    if($title != "")
		    $heads[] = [$i, $title];
	}
}

$allowedfileExtensions = array('xls', 'xlsx');

if($_POST["submit"] == 1) {
	$file = $_POST["fname"];
	$filePath = TMP_BASE_PATH . $file;
	if($file != "" && file_exists($filePath)) {

        $title = sqlstring($_POST["title"]);
        $identID = intval($_POST["ident"]);
        $headnames = $_POST["headnames"];
        $params = $_POST["params"];
        $start_row = intval($_POST["start_row"]);
        if(count($params) > PARAMS_MAX) {
            $errorText = "Максимальное количество параметров: <b>" . PARAMS_MAX ."</b>";
        } else {
            $ident = $headnames[$identID];
            if(isset($params[0])) {
                $param1 = $headnames[$params[0]];
            }
            if(isset($params[1])) {
                $param2 = $headnames[$params[1]];
            }
            if(isset($params[2])) {
                $param3 = $headnames[$params[2]];
            }
            if(isset($params[3])) {
                $param4 = $headnames[$params[3]];
            }
            if(isset($params[4])) {
                $param5 = $headnames[$params[4]];
            }
            
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
            
            if($success) {
                $paramIDS = [];
                for($i = 0; $i <= 4; $i++)
                {
                    $paramIDS[$i] = (isset($params[$i])) ? $params[$i] : -1;
                }
                $import = import_base($active_site, $baseID, $filePath, $start_row, $identID, $paramIDS[0], $paramIDS[1], $paramIDS[2], $paramIDS[3], $paramIDS[4], true, $file);
                switch($import) {
                    case UNKNOWN_ERROR:
                        $errorText = "Произошла ошибка.<br>Повторите попытку позже.";
                        break;
                    case SUCCESS:
                        $success = true;
                        break;
                }
            }
        }
	} else {
		if(!isset($_FILES["base_file"]) || $_FILES['base_file']['error'] !== UPLOAD_ERR_OK) {
			$errorText = "Не удалось загрузить базу контактов.<br>Повторите попытку позже.";
		}
		else {
			$file = $_FILES["base_file"];
			$tmp_name = $file["tmp_name"];
			$fileName = $file["name"];
			$fileSize = $file["size"];
			$fileType = $file["type"];
			$fileNameCmps = explode(".", $fileName);
			$fileExtension = strtolower(end($fileNameCmps));
			array_pop($fileNameCmps);
			$may_title = implode(".", $fileNameCmps);
			$date = time();
			if (!in_array($fileExtension, $allowedfileExtensions)) 
				$errorText = "Недопустимое расширение файла.<br>Разрешённые расширения: " . implode(", ", $allowedfileExtensions) . ".";
			else {
    			$upload_base = upload_base($client->id, $tmp_name);
				if(!$upload_base["result"])
					$errorText = "Не удалось загрузить базу контактов.<br>Повторите попытку позже.";
				else {
					header("Location: ". OFFICE_URI . "add-base-import.php?fname=" . $upload_base["fname"] . "&may_title=" . $may_title);
					die();
				}
			}
		}
	}
}

pil_office_header();
?>
<div class="wrap">
<h1>Создание базы контактов</h1>
<div class="row">
<form method="POST" action="" class="col office-form" enctype="multipart/form-data">
<? if($errorText != "") : ?>
<div class="error"><?=$errorText?></div>
<? endif; ?>
  <input type="hidden" name="submit" value="1">
  <input type="hidden" name="fname" value="<?=$file?>">
  <input type="hidden" name="start_row" value="2">
  <? if($success) : ?>
  База контактов успешно создана.<br>
    <a style="margin-top: 2em;" class="btn btn-primary" href="<?=OFFICE_URI?>base.php?id=<?=$baseID?>">Открыть базу</a>
  <? else : ?>
  <? if($selecting) : ?>
  <? foreach($heads as $head) : ?>
  <input type="hidden" name="headnames[<?=$head[0]?>]" value="<?=$head[1]?>">
  <? endforeach; ?>
  <div class="form-group">
    <label for="base_title">Название базы</label>
    <input type="text" value="<?=$_POST["title"]?>" class="form-control" name="title" id="base_title" placeholder="База №1" required>
    <small class="form-text">Назовите загружаемую базу. Название будет отображаться в личном кабинете ReLead.<br>
    Предлагаемое название: <a href="#" onclick="$('#base_title').val($(this).text())"><?=urldecode($_GET["may_title"])?></a></small>
  </div>
  <div class="form-group">
	<label for="ident">Идентификатор *</label>
	<select class="form-control" id="ident" name="ident">
		<? foreach($heads as $head) : ?>
		<option value="<?=$head[0]?>"><?=$head[1]?></option>
		<? endforeach; ?>
	</select>
	<small class="form-text">Выберите параметр, по которому Вы сможете отслеживать эффективность канала продвижения в личном кабинете ReLead.</small>
  </div>
  <div class="form-group">
      Выберите <b>до <?=PARAMS_MAX?> параметров</b>, необходимых для распространения уникальных ссылок или идентификации клиентов.<br><br>Например: E-mail, Телефон и т.д.<br>Эти данные будут отражены в выгрузках.
  </div>
  <div class="form-group">
  	<label for="params">Каналы распространения ссылок</label>
	<select class="form-control" id="params" name="params[]" multiple>
		<? foreach($heads as $head) : ?>
		<option value="<?=$head[0]?>"><?=$head[1]?></option>
		<? endforeach; ?>
	</select>
	<small class="form-text">Выбрать несколько можно с помощью комбинации CTRL + нажатие по пункту</small>
  </div>
  <button type="submit" class="submitb btn btn-primary">Создать базу</button>
  <? else :?>
    <div class="choice-links">
      <a class="active"  href="<?=OFFICE_URI . "add-base-import.php"?>">Загрузить из .xls</a>
      <a href="<?=OFFICE_URI . "add-base.php"?>">Создать вручную</a>
  </div>
  <div id="importBase">
         <div class="form-group">
         <label for="chooseFile">База данных</label>
         <div class="custom-file">
            <input accept=".xls, .xlsx" type="file" class="custom-file-input" onchange="$('#fileLabel').text(this.files[0].name)" name="base_file" id="chooseFile">
            <label class="custom-file-label" id="fileLabel" for="chooseFile">Выберете файл...</label>
        </div>
        <small id="fileHelp" class="form-text text-muted">Выберете файл с базой клиентов в форматах: .xls, .xlsx</small>
        </div>
        <button type="submit" class="submitb btn btn-primary">Загрузить базу</button>
  </div>
  <? endif; ?>
  <? endif; ?>
</form>
<? if(!$success && !$selecting) :?>
<div class="col">
    Внимание! Для автоматической загрузки база должна иметь такой вид:
    <br><br>
    <img src="<?=HOME_URI?>/imgs/baseexample.jpg">
    <br><br>
    Первая строка - заголовки, остальные - данные.
</div>
<? endif; ?>
</div>
</div>
<? pil_office_footer(); ?>