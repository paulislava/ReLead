<?php
require_once 'init.php';
$success = false;
$errorText = "";
$Page["id"] = "event-add";
$Page["title"] = "Добавление цели | ReLead";
if($_POST["submit"] == 1) {
    $title = sqlstring($_POST["title"]);
    $event_code = sqlstring($_POST["event_code"]);
    $site_id = $active_site;
    if($title == "" || $event_code == "" || $site_id == 0)
    {
        $errorText = "Заполните все необходимые поля!";
    } else {
        $query = "SELECT COUNT(`id`) FROM `{$mdb->event_ids}` WHERE `site_id` = '{$site_id}' AND `title` = '{$title}'";
        $checkTitle = $mdb->get_var($query);
        if($checkTitle > 0) {
            $errorText = "Цель с таким названием уже существует для этого сайта.";
        } else {
            $query = "SELECT COUNT(`id`) FROM `{$mdb->event_ids}` WHERE `site_id` = '{$site_id}' AND `event_code` = '{$event_code}'";
            $checkCode = $mdb->get_var($query);
            if($checkCode > 0) {
                $errorText = "Цель с таким идентификатором (ID) уже существует для этого сайта.";
            } else {
                $date = time();
                $query = "INSERT INTO `{$mdb->event_ids}` SET `site_id` = '{$site_id}', `title` = '{$title}', `event_code` = '{$event_code}', `create_date` = '{$date}'";
                $insert = $mdb->query($query);
    
                if(!$insert) {
                    $errorText = "Произошла ошибка.<br>Повторите попытку позже.";
                } else {
                    $success = true;
                    $eventD = $mdb->insert_id;
                }
            }
        }
    }
}

pil_office_header();
?>
<div class="wrap">
<h1>Добавление цели</h1>
<? if($success) : ?>
Цель "<?=$title?>" успешно создана.<br><br>
JavaScript-код для фиксации цели:
<code>rl_event('<?=$event_code?>')</code>
Пример работы (HTML):
<code><?
$code = '<button type="button" onclick="rl_event(\''.$event_code.'\');">'.$title.'</button>';
echo htmlspecialchars($code); 
?></code>
<? else : ?>
<form method="POST" action="" class="office-form">
<? if($errorText != "") : ?>
<div class="error"><?=$errorText?></div>
<? endif; ?>
  <input type="hidden" name="submit" value="1">
  <div class="form-group">
    <label for="event_title">Название цели</label>
    <input type="text" value="<?=$_POST["title"]?>" class="form-control" name="title" id="event_title" placeholder="Например, «Регистрация»" required>
  </div>
  <div class="form-group">
    <label for="event_code">Идентификатор цели (ID)</label>
    <input type="text" aria-describedby="identHelp" value="<?=$_POST["event_code"]?>" class="form-control" name="event_code" id="event_code" placeholder="Например, «reg»" required>
    <small id="identHelp" class="form-text">Используется для фиксации события в JavaScript.<br> Разрешённые символы: английские буквы, цифры, нижнее подчёркивание (_) и дефис (-).</small>
  </div>
  <button type="submit" class="submitb btn btn-primary">Добавить цель</button>
</form>
<? endif;?>
</div>
<? pil_office_footer(); ?>