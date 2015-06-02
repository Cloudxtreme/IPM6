<a href="" onclick="dim('ipm_dialog',false); return false;" title="Close"><img src="images/cancel.gif" alt="Close" id="cancel"></a>
<?php include('../ipm_class.php'); ?>
<?php include('Net/IPv4.php'); ?>
<?php include('Net/IPv6.php'); ?>

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

$parentNetwork = new network($_GET['network'],$db);

if (isset($parentNetwork->_network_container)) {

	$authContainer = $user->getAuthContainer($parentNetwork->_network_container);
	
}

$net = find_net(long2ip($parentNetwork->_network),long2ip($parentNetwork->_network_mask));

?>

<?php if ($authContainer < 1) { ?>

	<div class="ipm_error">Error: You are not authorized to view the selected content.</div>
	<?php 
		exit();
} ?>

<?php
if (!isnull($_GET['network']) && !($user->getAuthNetwork($parentNetwork->_network_id, $_SESSION['MM_Username']) > 0 || ($user->getAuthNetworkgroup($parentNetwork->_network_group,$_SESSION['MM_Username']) > 0 && $user->getAuthNetwork($parentNetwork->_network_id,$_SESSION['MM_Username']) == "") || ($user->getAuthContainer($parentNetwork->_network_container,$_SESSION['MM_Username']) > 0 && $user->getAuthNetworkGroup($parentNetwork->_network_group,$_SESSION['MM_Username']) == "" && $user->getAuthNetwork($parentNetwork->_network_id,$_SESSION['MM_Username']) == ""))) { ?>

	<div class="ipm_error">Error: You are not authorized to view the selected content.</div>
	<?php 
		exit();
} ?>
		
<h2>Network Information</h2>

<div id="ipm_table_2col">
	
	<div id="row">
		
		<div id="col1"><strong>Network</strong></div>
		<div id="col2"><?php if (isnull($parentNetwork->_network_v6_mask)) { echo long2ip($parentNetwork->_network).get_slash(long2ip($parentNetwork->_network_mask)); } else { echo long2ipv6($parentNetwork->_network)."/".$parentNetwork->_network_v6_mask; } ?></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		
		<div id="col1"><strong>Description</strong></div>
		<div id="col2"><?php echo $parentNetwork->_network_description; ?></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		
		<div id="col1"><strong>Dotted Decimal Mask</strong></div>
		<div id="col2"><?php if (isnull($parentNetwork->_network_v6_mask)) { echo long2ip($parentNetwork->_network_mask); } else { echo "N/A"; } ?></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		
		<div id="col1"><strong>Broadcast</strong></div>
		<div id="col2"><?php if (isnull($parentNetwork->_network_v6_mask)) { echo long2ip($net['broadcast']); } else { echo "N/A"; } ?></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		
		<div id="col1"><strong>Normal Host Range</strong></div>
		<div id="col2"><?php if (isnull($parentNetwork->_network_v6_mask)) { echo long2ip($net['firstaddress'])." --> ".long2ip($net['lastaddress']); } else { echo "N/A"; } ?></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		
		<div id="col1"><strong>Total Number of Addresses</strong></div>
		<div id="col2"><?php if (isnull($parentNetwork->_network_v6_mask) && !($parentNetwork->_network_subnetted)) { echo $net['total']; } else { echo "N/A"; } ?></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		
		<div id="col1"><strong>Addresses in Use</strong></div>
		<div id="col2"><?php if ($parentNetwork->_network_subnetted) { echo "Network is subnetted"; } else { if (isnull($parentNetwork->_network_v6_mask)) { printf($parentNetwork->_network_addresses." (%d%% used)", $parentNetwork->_network_addresses / $net['total'] * 100); } else { echo $parentNetwork->_network_addresses; } }?></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		
		<div id="col1"><strong>Network Group</strong></div>
		<div id="col2"><?php echo $parentNetwork->_network_group_name; ?>&nbsp;</div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		
		<div id="col1"><strong>Comments</strong></div>
		<div id="col2"><?php echo $parentNetwork->_network_comments; ?>&nbsp;</div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		
		<div id="col1"><strong>Created by</strong></div>
		<div id="col2"><?php if (isset($parentNetwork->_network_user)) { $createUser = $user->getUserByUsername($parentNetwork->_network_user); echo $createUser['firstname']." ".$createUser['lastname']." on ".$parentNetwork->_network_user_date; } ?></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		
		<div id="col1"><strong>Updated by</strong></div>
		<div id="col2"><?php if (isset($parentNetwork->_network_update_user)) { $createUser = $user->getUserByUsername($parentNetwork->_network_update_user); echo $createUser['firstname']." ".$createUser['lastname']." on ".$parentNetwork->_network_update_user_date; } ?></div>
		<div class="clear"></div>
		
	</div>
	
</div>

