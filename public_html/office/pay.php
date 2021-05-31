<?php
define("PAY_PAGE", true);
require_once 'init.php';
$Page["id"] = "office-pay";
$Page["title"] = "Оплата тарифа | ReLead";
$active_site = 0;
$success = $_GET["success"] == 1;
if($_GET["pay_error"] == 1)
{
    $errorText = "Произошла ошибка при оплате.<br>Повторите попытку позже.";
}
if($_POST["submit"] == 1)
{
    $tarif = intval($_POST["tarif"]);
    $tarifInfo = $mdb->get_row("SELECT `price`, `title` FROM `{$mdb->tarifs}` WHERE `id` = '{$tarif}'");
    $price = $tarifInfo->price; 
    if(!$tarif || $tarif == TARIF_TEST || !$tarifInfo || $price == 0) {
        $errorText = "Выберите тариф из списка ниже!";
    }
    
    $date = time();
    
    $face = $_POST["face"];
    $phys = $face == "phys";
    if($phys) {
        $query = "INSERT INTO `{$mdb->pays}` SET `client` = '{$client->id}', `tarif` = '{$tarif}', `summ` = '{$price}', `create_date` = '{$date}'";
        $insert = $mdb->query($query);
        
        $pay_id = $mdb->insert_id;
        
        $pay_url = $config["robokassa"]["url"];
        $pay_login = $config["robokassa"]["shop_id"];
        $pay_pass1 = $config["robokassa"]["pass1"];
        $pay_price = $price;
        $pay_desc = sprintf($config["robokassa"]["pay_desc"], $tarifInfo->title);
         
        $pay_culture = $config["robokassa"]["culture"];
        $pay_curr = $config["robokassa"]["curr"];
        $ip = $_SERVER["REMOTE_ADDR"]; 
        $email = $client->email;
        $crc_str = "$pay_login:$pay_price:$pay_id:$ip:$pay_pass1";
        if($config["robokassa"]["receipts"])
        {
            $receipt = new stdClass();
            $receipt->sno = $config["robokassa"]["sno"];
            $r_item = new stdClass();
            $r_item->name = $pay_desc;
            $r_item->quantity = 1;
            $r_item->sum = $pay_price;
            $r_item->tax = $config["robokassa"]["tax"];
            $receipt->items = [$r_item];
            $receipt_json = urlencode(json_encode($receipt));
            
            $crc_str = "$pay_login:$pay_price:$pay_id:$ip:$receipt_json:$pay_pass1";
        }
        $pay_crc  = md5($crc_str);
        $pay_test = $config["robokassa"]["test"] ? 1 : 0;
        ?>
        <html>
            <body>
                <form method="POST" action="<?=$pay_url?>">
                    <input type="hidden" name="MrchLogin" value="<?=$pay_login?>">
                    <input type="hidden" name="OutSum" value="<?=$price?>">
                    <input type="hidden" name="InvId" value="<?=$pay_id?>">
                    <input type="hidden" name="Desc" value="<?=$pay_desc?>">
                    <input type="hidden" name="SignatureValue" value="<?=$pay_crc?>">
                    <input type="hidden" name="IncCurrLabel" value="<?=$pay_curr?>">
                    <input type="hidden" name="Culture" value="<?=$pay_culture?>">
                    <input type="hidden" name="IsTest" value="<?=$pay_test?>">
                    <input type="hidden" name="Email" value="<?=$email?>">
                    <input type="hidden" name="Receipt" value="<?=$receipt_json?>">
                    <input type="hidden" name="UserIp" value="<?=$ip?>">
                </form>
                <script>
                   document.forms[0].submit();
                </script>
            </body>
        </html>
        <?
        exit;
    } else {
        $entity_name = sqlstring($_POST["entity_name"]);
        $inn = sqlstring($_POST["entity_inn"]);
        if($entity_name == "" || $inn == "")
            $errorText = "Укажите полное название и ИНН юридического лица!";
        else {
            $query = "INSERT INTO `{$mdb->entity_orders}` SET `client_id` = '{$client->id}', `tarif` = '{$tarif}', `entity_name` = '{$entity_name}', `inn` = '{$inn}', `create_date` = '{$date}'";
            $insert = $mdb->query($query);
            if(!$insert) {
                $errorText = "Произошла ошибка.<br>Повторите попытку позже.";
            } else {
                $order_id = $mdb->insert_id;
                $success = true;
                $mails = implode(",", $config["notify"]["mails"]);
                $notify = new PIL_Mail($mails, $config["notify"]["from"]);
                $notify->subject = "Заявка на выставление счёта";
                $entity_name = strip_tags($_POST["entity_name"]);
                $inn = strip_tags($_POST["entity_inn"]);
                $notify->fill_template('entity_order', array("order_id" => $order_id, "entity_name" => $entity_name, "inn" => $inn, "email" => $client->email, "tarif" => $tarifInfo->title, "summ" => $tarifInfo->price));
                
                $notify->send();
            }
        }
    }
}

pil_office_header();

$tarifs = $mdb->get_results("SELECT * FROM `{$mdb->tarifs}` WHERE `id` <> '". TARIF_TEST ."'");
?>
<div class="wrap">
    <h1>Оплата тарифа</h1>
    <?if($client->first_pay == 1) : ?>
    <div class="text mb-4" >
        Тариф «<?=$client_tarif->title?>» <?if($active):?>действует до<?else:?>закончился<?endif;?>: <b><?=$client->end_date == 0 ? "бессрочно" : date(DATE_FORMAT, $client->end_date)?></b>
    </div>
    <?endif;?>
    <? if($success) : ?>
    <? if($phys) : ?>
    Оплата прошла успешно.<br>
    Продуктивного маркетинга с ReLead!
    <? else : ?>
    Заявка успешно отправлена.<br>
    В течение 24 часов Вам будет выставлен счёт на оплату.
    <? endif; ?>
    <? else : ?>
    <form method="POST" action="" class="office-form" id="payForm">
        <? if($errorText != "") : ?>
        <div class="error"><?=$errorText?></div>
        <? endif; ?>
          <input type="hidden" name="submit" value="1">
          <div class="form-group radiogroup">
              <label><input type="radio" id="phys-radio" name="face" value="phys" checked><span>Физическое лицо</span></label>
              <label><input type="radio" id="entity-radio" name="face" value="entity"><span>Юридическое лицо</span></label>
          </div>
          <div id="entity-block">
              <div class="form-group">
                  <label for="entity_name">Наименование юр.лица</label>
                  <input type="text" class="form-control" id="entity_name" name="entity_name" placeholder="ООО «ОДУВАНЧИК»">
              </div>
              <div class="form-group">
                  <label for="entity_inn">ИНН</label>
                  <input type="text" class="form-control" placeholder="5406 7759 85" id="entity_inn" name="entity_inn">
              </div>
          </div>
            <div class="form-group">
                <label for="event_title">Выберите тариф</label>
                <select id="tarifSelect" name="tarif" class="form-control">
                    <? foreach($tarifs as $tarif_opt) : ?>
                    <option data-price="<?=$tarif_opt->price?>" value="<?=$tarif_opt->id?>" <?=$tarif_opt->id == $client_tarif->id ? "selected" : ""?>><?=$tarif_opt->title?></option>
                    <? endforeach; ?>
                </select>
            </div>
            <div class="text mt-2">Сумма к оплате: <b id="tarifPriceBlock"><span id="tarifPrice">0</span> рублей</b></div>
            <button type="submit" class="submitb btn btn-primary">Перейти к оплате</button>
    </form>
    <? endif;?>
</div>
<script>
function checkMoney() {
    var summ = $('#tarifSelect option:selected').data('price');
    $('#tarifPrice').text(numberWithSpaces(summ));
}

checkMoney();
$('#tarifSelect').change(function() { 
    $("#tarifPriceBlock").fadeOut(100, function() {
        checkMoney(); 
        $(this).fadeIn(100);
    });});
$('#entity-radio').click(function() {
   $('#entity-block').fadeIn(200); 
});
$('#phys-radio').click(function() {
   $('#entity-block').fadeOut(200); 
});
</script>
<style>
    #entity-block { display: none; }
</style>
<?
pil_office_footer();
?>