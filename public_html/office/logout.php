<? 
define("LOGIN_PAGE", true);
require_once 'init.php';
session_destroy();
header("Location: /office/login.php");
?>