<? 
define("LOGIN_PAGE", true);
require_once 'init.php';
$errorText = "";
if($_POST["submit"] == 1) {
    $email = sqlstring($_POST["email"]);
    if($email != "" && $_POST["pass"] != "") {
        $pass = soltstring($_POST["pass"]);
        $query = "SELECT `id` FROM `{$mdb->clients}` WHERE `email` = '{$email}' AND `pass` = '{$pass}'";
        $id = $mdb->get_var($query);
        if(!$id) {
            $errorText = "Ошибка авторизации.<br>Неправильный E-mail или пароль.";
        } else {
            $date = time();
            $query = "UPDATE `{$mdb->clients}` SET `auth_date` = '{$date}' WHERE `id` = '{$id}'";
            $update = $mdb->query($query);
            $redirect = $_GET["redirect"];
            if($redirect == "")
                $redirect = "/office/";
            $_SESSION["office_logined"] = 1;
            $_SESSION["office_id"] = $id;
            header("Location: {$redirect}");
        }
    }
}

$Page["title"] = "Вход | ReLead";
$Page["body_class"] = "authpage";
pil_header();
?>
<div class="authform">
    <a class="logo" href="<?=HOME_URI?>"></a>
    <h1>Войти</h1>
    <? if($errorText != "") : ?>
    <div class="error"><?=$errorText?></div>
    <? endif; ?>
    <form method="POST" action="">
        <input type="hidden" name="submit" value="1">
        <div class="form-group">
            <input type="email" class="form-control" value="<?=$_POST["email"]?>" name="email" placeholder="E-mail" required>
        </div>
        <div class="form-group">
            <input type="password" class="form-control" name="pass" placeholder="Пароль" required>
        </div>
        <div>
        <div class="float-left form-group form-check" style="display: none;">
            <input type="checkbox" class="form-check-input" id="rememberCheck" checked>
            <label class="form-check-label" for="rememberCheck">Оставаться в системе</label>
        </div>
        <div class="float-right mb-2">
            <a href="/office/restore.php">Забыли пароль?</a>
        </div>
        </div>
        <button type="submit" id="submitBtn" class="btn btn-primary">Войти</button>
        <div id="toggleLink"><a href="/office/register.php">Нет аккаунта</a></div>
    </form>
</div>
<? pil_footer(); ?>