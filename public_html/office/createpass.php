<? 
define("LOGIN_PAGE", true);
require_once 'init.php';
$errorText = "";
$success = false;
$wrongtoken = false;

$token = sqlstring($_GET["token"]);
if($token == "")
    $wrongtoken = true;
else {
    $restore = sql_where($mdb->pass_restories, array("token" => $token, "used" => 0));
    if(!$restore)
        $wrongtoken = true;
}

if(!$wrongtoken && $_POST["submit"] == 1)
{
    $pass = sqlstring($_POST["pass"]);
    $pass1 = sqlstring($_POST["pass1"]);
    if($pass == "" || $pass1 == "")
    {
        $errorText = "Введите новый пароль и повторите его для смены пароля.";
    } else if($pass != $pass1) {
        $errorText = "Введённые пароли не совпадают!";
    } else {
        $pass = soltstring($pass);
        $userID = $restore->user_id;
        $query = "UPDATE `{$mdb->clients}` SET `pass` = '{$pass}' WHERE `id` = '{$userID}'";
        $change = $mdb->query($query);
        $query = "UPDATE `{$mdb->pass_restories}` SET `used` = '1' WHERE `id` = '{$restore->id}'";
        $close = $mdb->query($query);
            $success = true;
    }
}

$Page["title"] = "Смена пароля | ReLead";
$Page["body_class"] = "authpage";
pil_header(); ?>
<div class="authform">
    <a class="logo" href="<?=HOME_URI?>"></a>
    <h1>Сменить пароль</h1>
    <? if($errorText != "") : ?>
    <div class="error"><?=$errorText?></div>
    <? endif; ?>
    <? if($success) { ?>
    <div class="success-text">
    Пароль успешно изменён, Вы можете использовать его для входа.
    </div>
    <a href="<?=OFFICE_URI?>login.php" id="submitBtn" class="btn btn-primary">Войти</a>
    <? } else if($wrongtoken) { ?>
    Ссылка устарела или недействительна.<br>
    Попробуйте <a href="<?=OFFICE_URI?>restore.php">восстановить пароль</a> снова.
    <? } else { ?>
    <form method="POST" action="">
        <input type="hidden" name="submit" value="1">
        <div class="form-group">
            <input type="password" class="form-control" name="pass" placeholder="Новый пароль" required>
        </div>
        <div class="form-group">
            <input type="password" class="form-control" name="pass1" placeholder="Повторите пароль" required>
        </div>

        <button type="submit" id="submitBtn" class="btn btn-primary">Сменить пароль</button>
    </form>
    <? } ?>
</div>
<? pil_footer(); ?>