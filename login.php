<?php

include('includes/ipm_class.php');

$ipm = new ipm();
$title = $ipm->getApp()." ".$ipm->getVer();

$db = new Mysql();
$db->dbConnect();

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=$_POST['password'];
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "index.php";
  $MM_redirectLoginFailed = "login.php?status=authenticationfail";
  $MM_redirecttoReferrer = false;
  
  $LoginRS__query=sprintf("SELECT username, password FROM `user` WHERE username=%s AND password=%s AND inactive=0",
    GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $db->selectCustom($LoginRS__query);
  $loginFoundUser = $db->_db_totalrows;
  
  if ($loginFoundUser) {
     $loginStrGroup = "";
    
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;     

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $title; ?></title>
<link href="css/ipm6.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="banner">
  &nbsp;<?php echo $title; ?>
</div>
<div class="ipm_body">

<div class="loginheader">&nbsp;Welcome to <?php echo $title; ?> Enterprise</div>


					
<?php if (isset($_GET['status']) && $_GET['status'] == 'authenticationfail') { ?>
<p class="text_red">Error: The username/password entered is not correct.</p>
<?php } ?>
<?php if (isset($_GET['status']) && $_GET['status'] == 'permissionfail') { ?>
<p class="text_red">Please authenticate to view the requested content.</p>
<?php } ?>
<table width="50%" border="0" padding="10">
<tr>
<td width="100"><img src="images/padlock-xxl.png" alt="Login" width="75"></td>
<td>
<form name="frm_login" method="POST" action="<?php echo $loginFormAction; ?>">
  <p>
    <label><strong>Username</strong><br />
      <input name="username" type="text" class="input_standard" id="username" size="24" maxlength="255" />
    </label>
  </p>
  <p>
    <label><strong>Password</strong><br />
<input name="password" type="password" class="input_standard" id="password" size="24" maxlength="255" />
    </label>
  </p>
  <p>
<input name="log_in" type="submit" id="log_in" value="Submit" />
  </p>
</form>
</td>
</tr>
</table>
<strong>Note: For this website to function properly, you need an active Internet connection, and your browser must not have Javascript or cookies disabled.</strong>
<br /><br />
<div class="ipm_footer">&copy;UrbanEccentric, 2014 [Ver: <?php echo $ipm->getRelease(); ?>]
</div>
</body>
</html>