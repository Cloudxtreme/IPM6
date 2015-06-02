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
}

$start = $_GET['start'];
$end = $_GET['end'];

switch($start.$end) {

	case "09": 
		$query_links_1234 = "SELECT links.*, portsdevices.devicegroup FROM links INNER JOIN portsdevices ON portsdevices.id = links.provide_node_a WHERE links.provide_cct NOT REGEXP '^[A-Z]' AND links.container = ".$_GET['container']." ORDER BY links.provide_cct";
		$links_1234 = $db->selectCustom($query_links_1234);
		$row_links_1234 = mysql_fetch_assoc($links_1234);
		$totalRows_links_1234 = mysql_num_rows($links_1234); 
		
        if ($totalRows_links_1234 > 0) {
            do {
            	echo "<li><a href=\"?browse=devices&amp;container=".$_GET['container']."&amp;device=".$row_links_1234['provide_node_a']."&amp;group=".$row_links_1234['devicegroup']."&amp;port=".$row_links_1234['provide_port_node_a']."&amp;linkview=1\" title=\"Display this link\">".$row_links_1234['provide_cct']."</a></li>";
            } while ($row_customers_1234 = mysql_fetch_assoc($customers_1234));
        }
        else {
			echo "<li><a class=\"NOLINK\">No links to display</a></li>";
		}
    	break;
    
    default:
    	$query_links_abcd = "SELECT links.*, portsdevices.devicegroup FROM links INNER JOIN portsdevices ON portsdevices.id = links.provide_node_a WHERE links.provide_cct REGEXP '^[".$start."-".$end."]' AND links.container = ".$_GET['container']." ORDER BY links.provide_cct";
		$links_abcd = $db->selectCustom($query_links_abcd);
		$row_links_abcd = mysql_fetch_assoc($links_abcd);
		$totalRows_links_abcd = mysql_num_rows($links_abcd); 
		
        if ($totalRows_links_abcd > 0) {
            do {
            	echo "<li><a href=\"?browse=devices&amp;container=".$_GET['container']."&amp;device=".$row_links_abcd['provide_node_a']."&amp;group=".$row_links_abcd['devicegroup']."&amp;port=".$row_links_abcd['provide_port_node_a']."&amp;linkview=1\" title=\"Display this link\">".$row_links_abcd['provide_cct']."</a></li>";
            } while ($row_links_abcd = mysql_fetch_assoc($links_abcd));
        }
        else {
			echo "<li><a class=\"NOLINK\">No links to display</a></li>";
		}
    	break;
    
}
?>