<? 
define("LOGIN_PAGE", true);
require_once 'init.php';
$errorText = "";
$success = false;
    
if($_POST["submit"] == 1) {
    $email = sqlstring($_POST["email"]);
    if($email == "")
    {
        $errorText = "Введите E-mail, указанный при регистрации!";
    } else {
        $r_pass = $_POST["r_pass"];
        $user = sql_where($mdb->clients, array("email" => $email));
        if(!$user) {
            $errorText = 'Мы не нашли в системе аккаунт с таким E-mail.<br>Вы можете <a href="'.OFFICE_URI.'register.php">зарегистрироваться</a> или <a href="#" onclick="jivo_api.open();">связаться с нами</a>.';
        } else {
            $email = $user->email;
            $id = $user->id;
            $date = time();
            $token = soltstring($email . $id . $date);
            $query = "INSERT INTO `{$mdb->pass_restories}` SET `user_id` = '{$id}', `token` = '{$token}', `create_date` = '{$date}'";
            $insert = $mdb->query($query);
            $restore_link = sprintf(RESTORE_URI, $token);
            
            $notify = new PIL_Mail($email, $config["mailing"]["support"]);
            $notify->subject = "Восстановления пароля";
            $notify->fill_template('restore_pass', array("link" => $restore_link));
            
            $send = $notify->send();
            
            $success = true;
        }
    }
}


$Page["title"] = "Восстановить пароль | ReLead";
$Page["body_class"] = "authpage";
pil_header(); ?>
<div class="authform">
    <a class="logo" href="<?=HOME_URI?>"></a>
    <h1>Восстановить пароль</h1>
    <? if($errorText != "") : ?>
    <div class="error"><?=$errorText?></div>
    <? endif; ?>
    <? if($success) : ?>
    <div class="success-text">
    Успешно!<br>
    Мы отправили ссылку для смены пароля на указанный E-mail.<br>
    Если письмо не приходит, убедитесь, что оно не попало в папку "Спам", а затем <a href="#" onclick="jivo_api.open();">свяжитесь с нами</a>.
    </div>
    <? else : ?>
    <form method="POST" action="">
        <input type="hidden" name="submit" value="1">
        <div class="form-group">
        Введите E-mail, указанный Вами при регистрации в ReLead.<br>
        Мы отправим на него ссылку для смены пароля.
        </div>
        <div class="form-group">
            <input type="email" class="form-control" name="email" value="<?=$_POST["email"]?>" placeholder="E-mail" required>
        </div>

        <button type="submit" id="submitBtn" class="btn btn-primary">Восстановить пароль</button>
        <div id="toggleLink"><a href="<?=OFFICE_URI?>login.php">Войти</a> или <a href="<?=OFFICE_URI?>register.php">зарегистрироваться</a></div>
    </form>
    <? endif; ?>
</div>
<? pil_footer(); ?>