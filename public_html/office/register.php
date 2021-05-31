<? 
define("LOGIN_PAGE", true);
require_once 'init.php';
$errorText = "";
$success = false;
$tarif_id = intval($_GET["tarif"]);
if($tarif == 0)
    $tarif = TARIF_TEST;
    
if($_POST["submit"] == 1) {
    $email = sqlstring($_POST["email"]);
    $pass = $_POST["pass"];
    if($email == "" || $pass == "")
    {
        $errorText = "Заполните все необходимые поля!";
    } else {
        $r_pass = $_POST["r_pass"];
        $query = "SELECT COUNT(`id`) FROM `{$mdb->clients}` WHERE `email` = '{$email}'";
        $repeats = $mdb->get_var($query);
        if($repeats > 0) {
            $errorText = 'Этот E-mail уже зарегистрирован в системе.<br>Попробуйте <a href="'. OFFICE_URI.'login.php">войти</a>.';
        } else if($pass != $r_pass) {
            $errorText = "Пароль повторён неверно.<br>Введите одинаковые пароли в оба поля.";
        } else {
            $pass = soltstring($pass);
            $date = time();
            $tarif_id = intval($_POST["tarif"]);
            $tarif = sql_where($mdb->tarifs, array("id" => $tarif_id));
            if(!$tarif) {
                $tarif_id = TARIF_TEST;
                $tarif = sql_where($mdb->tarifs, array("id" => $tarif_id));
            }
            $end_date = $date;
            if($tarif_id == TARIF_TEST)
                $end_date += $tarif->duration;
            $query = "INSERT INTO `{$mdb->clients}` SET `email` = '{$email}', `pass` = '{$pass}', `reg_date` = '{$date}', `tarif` = '{$tarif_id}', `end_date` = '{$end_date}'";
            $insert = $mdb->query($query);
            if(!$insert)
            {
                $errorText = "Произошла ошибка при регистрации.<br>Повторите попытку позже.";
            } else {
                $success = true;
            }
        }
    }
}


$Page["title"] = "Регистрация | ReLead";
$Page["body_class"] = "authpage";
pil_header(); ?>
<div class="authform">
    <a class="logo" href="<?=HOME_URI?>"></a>
    <h1>Регистрация</h1>
    <? if($errorText != "") : ?>
    <div class="error"><?=$errorText?></div>
    <? endif; ?>
    <? if($success) : ?>
    <div class="success-text">
    Регистрация успешно завершена.
    </div>
    <a href="<?=OFFICE_URI?>login.php" id="submitBtn" class="btn btn-primary">Войти</a>
    <? else : ?>
    <form method="POST" action="">
        <input type="hidden" name="tarif" value="<?=$tarif_id?>">
        <input type="hidden" name="submit" value="1">
        <div class="form-group">
            <input type="email" class="form-control" name="email" value="<?=$_POST["email"]?>" placeholder="E-mail" required>
        </div>
        <div class="form-group">
            <input type="password" class="form-control" name="pass" placeholder="Пароль" required>
        </div>
        <div class="form-group">
            <input type="password" class="form-control" name="r_pass" placeholder="Повторите пароль" required>
        </div>

        <button type="submit" id="submitBtn" class="btn btn-primary">Создать аккаунт</button>
        <div id="toggleLink"><a href="/office/login.php">Уже есть аккаунт</a></div>
    </form>
    <? endif; ?>
</div>
<? pil_footer(); ?>