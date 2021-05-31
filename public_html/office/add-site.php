<?php
define("SITE_CREATING", true);
require_once 'init.php';
$success = false;
$errorText = "";
$Page["id"] = "site-add";
$Page["title"] = "Добавление сайта | ReLead";

$limitcount = false;
if($client_tarif->site_count != 0) {
    $current_count = $mdb->get_var("SELECT COUNT(*) FROM `{$mdb->sites}` WHERE `client_id` = '{$client->id}'");
    $limitcount = $current_count >= $client_tarif->site_count;
}

if($_POST["submit"] == 1 && !$limitcount) {
    $title = sqlstring($_POST["title"]);
    $domain = sqlstring($_POST["domain"]);
    $utf8_domain = idn_to_utf8($utf8_domain);
    $https_check = $_POST["https_check"] == 1 ? 1 : 0;
    if($title == "" || $domain == "")
    {
        $errorText = "Заполните все необходимые поля!";
    } else {
        $query = "SELECT COUNT(`id`) FROM `{$mdb->sites}` WHERE `client_id` = '{$client->id}' AND `title` = '{$title}'";
        $checkTitle = $mdb->get_var($query);
        if($checkTitle > 0) {
            $errorText = "Сайт с таким названием уже есть в Вашем аккаунте.";
        } else {
            $query = "SELECT COUNT(`id`) FROM `{$mdb->sites}` WHERE `domain` = '{$domain}'";
            if($utf8_domain != "")
                $query .= " OR `domain` = '{$utf8_domain}";
            $checkDomain = $mdb->get_var($query);
            if($checkDomain > 0)
            {
                $errorText = "Этот сайт уже есть в системе.";   
            } else {
                $date = time();
                $query = "INSERT INTO `{$mdb->sites}` SET `client_id` = '{$client->id}', `title` = '{$title}', `domain` = '{$domain}', `https_check` = '{$https_check}', `create_date` = '{$date}'";
                $insert = $mdb->query($query);

                if(!$insert) {
                    $errorText = "Произошла ошибка.<br>Повторите попытку позже.";
                } else {
                    $success = true;
                    $site_id = $mdb->insert_id;
                }
            }
        }
    }
}

pil_office_header();
?>
<div class="wrap">
<h1>Добавление сайта</h1>
<? if($success) : ?>
Сайт "<?=$title?>" успешно добавлен в Ваш личный кабинет ReLEAD.<br>
Ниже для Вас сгенерирован код, который необходимо добавить на все страницы вашего сайта <b>после открывающего тега &lt;head&gt;</b>:
<code><?
    $script = '<script type="text/javascript" src="'.LOAD_URI.'"></script>';
    $script = htmlspecialchars($script); 
    echo $script;
    ?></code>
* Для добавления кода обратитесь к вашему разработчику/администратору или сделайте это самостоятельно.<br>
<a style="margin-top: 2em;" class="btn btn-primary" href="<?=OFFICE_URI?>?site_id=<?=$site_id?>">Приступить к работе</a>
<? else : ?>
<? if($limitcount) : ?>
Невозможно добавить сайт.<br>Максимальное количество сайтов для тарифа «<?=$client_tarif->title?>»: <b><?=$client_tarif->site_count?></b>
<? else : ?>
<form method="POST" action="" class="office-form">
<? if($errorText != "") : ?>
<div class="error"><?=$errorText?></div>
<? endif; ?>
  <input type="hidden" name="submit" value="1">
  <div class="form-group">
      <label for="site_href">Укажите адрес интернет-ресурса (сайта, магазина и т.п.), товары или услуги которого Вы хотите продвигать</label>
      <input placeholder="https://aliexpress.com/" type="text" value="<?=$_POST["url"]?>" name="url" class="form-control" id="site_href">
  </div>
  <div id="afterlink">
  <div class="form-group">
    <label for="site_title">Название сайта</label>
    <input type="text" value="<?=$_POST["title"]?>" class="form-control" name="title" id="site_title" placeholder="Например, AliExpress" required>
  </div>
  <div class="form-group">
    <label for="domain_name">Доменное имя сайта (без http(s):// и слэшей)</label>
    <input type="text" value="<?=$_POST["domain"]?>" name="domain" class="form-control" id="domain_name" aria-describedby="emailHelp" placeholder="Например, aliexpress.com" required>
  </div>
  <div class="form-check" style="display: none;">
     <input type="checkbox" <?=$_POST["https_check"] ? "checked" :""?> class="form-check-input" name="https_check" id="checkHttps">
     <label class="form-check-label" for="checkHttps">HTTPS</label>
  </div>
  </div>
  <button disabled type="submit" id="submitBtn" class="submitb btn btn-primary">Добавить сайт</button>
</form>
<? endif; ?>
<? endif;?>
</div>
<style>
    #afterlink { display: none; }
    .nojs #afterlink { display: inherit; }
</style>
<script>
function decodeHtml(str)
{
    var map =
    {
        '&amp;': '&',
        '&lt;': '<',
        '&gt;': '>',
        '&quot;': '"',
        '&#039;': "'"
    };
    return str.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function(m) {return map[m];});
}

    $('#site_href').keyup(function() {
        var url = $(this).val().trim();
        var showing = url != "";
        $('#submitBtn').prop('disabled', !showing);
        if(showing)
        {
            $('#afterlink').slideDown();
            
              var proxyurl = "<?=OFFICE_URI?>ajax/geturlinfo.php?url=" + url;
              $.ajax({
                url: proxyurl,
                async: true,
                success: function(response) {
                    var data = JSON.parse(response);
                    var title = decodeHtml(data.title);
                    //var url_i = new URL(url);
                    //var domain = url_i.host;
                    var domain = data.host;
                    $('#site_title').val(title);
                    $('#domain_name').val(domain);
                    $('#checkHttps').prop('checked', data.https);
                }
              });
            
        } else {
            $('#afterlink').slideUp();
        }
        });
</script>
<? pil_office_footer(); ?>