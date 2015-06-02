<?php include('../ipm_class.php'); ?>

<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "login.php?status=permissionfail";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}

$ipm = new ipm();
$db = new Mysql();

$user = new user($_SESSION['MM_Username'], $db);

$title = $ipm->getApp()." ".$ipm->getVer();

if (isset($_GET['container'])) {

	$authContainer = $user->getAuthContainer($_GET['container']);
	
}

?>

<?php if ($authContainer < 1) { ?>
<div class="ipm_error">Error: You are not authorised to view the selected content.</div>
<?php 
	exit();
} ?>

<?php


$start = $_GET['start'];
$end = $_GET['end'];

switch($start.$end) {

	case "09": 
		$query_customers_1234 = "SELECT * FROM customer WHERE container = '".$_GET['container']."' AND customer.name NOT REGEXP '^[A-Z]' ORDER BY customer.name";
		$customers_1234 = $db->selectCustom($query_customers_1234);
		$row_customers_1234 = mysql_fetch_assoc($customers_1234);
		$totalRows_customers_1234 = mysql_num_rows($customers_1234);
		
        if ($totalRows_customers_1234 > 0) {
            do {
            	echo "<li><a href=\"\" onclick=\"ShowContent('customers',".$_GET['container'].",".$row_customers_1234['id']."); return false;\" title=\"".$row_customers_1234['name']."\">";
                if (strlen($row_customers_1234['name']) < 25) { echo $row_customers_1234['name']; } else { echo substr_replace($row_customers_1234['name'],'...',25); };
                echo "</a></li>";
            } while ($row_customers_1234 = mysql_fetch_assoc($customers_1234));
        }
        else {
			echo "<li><a class=\"NOLINK\">No customers to display</a></li>";
		}
    	break;
    
    default:
    	mysql_select_db($database_subman, $subman);
		$query_customers_abcd = "SELECT * FROM customer WHERE container = '".$_GET['container']."' AND customer.name REGEXP '^[".$start."-".$end."]' ORDER BY customer.name";
		$customers_abcd = $db->selectCustom($query_customers_abcd);
		$row_customers_abcd = mysql_fetch_assoc($customers_abcd);
		$totalRows_customers_abcd = mysql_num_rows($customers_abcd);
		
		if ($totalRows_customers_abcd > 0) {
            do {
            	echo "<li><a href=\"\" onclick=\"ShowContent('customers',".$_GET['container'].",".$row_customers_abcd['id']."); return false;\" title=\"".$row_customers_abcd['name']."\">";
                if (strlen($row_customers_abcd['name']) < 25) { echo $row_customers_abcd['name']; } else { echo substr_replace($row_customers_abcd['name'],'...',25); };
                echo "</a></li>";
            } while ($row_customers_abcd = mysql_fetch_assoc($customers_abcd));
        }
        else {
			echo "<li><a class=\"NOLINK\">No customers to display</a></li>";
		}
        break;
    
}
?>