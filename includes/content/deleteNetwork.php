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

if (isset($_GET['network'])) {
	
	$network = new network($_GET['network'],$db);
	
}

if (isnull($network->_network_parent)) {
	$parentNetwork = $network;
}
else {
	$parentNetwork = new network($network->_network_parent,$db);
}

if (isset($network->_network_container)) {

	$authContainer = $user->getAuthContainer($network->_network_container);
	
}

?>

<?php if (isset($_POST['token'])) { ?>
<a href="" onclick="dim('ipm_form',false); ShowContent('networks',<?php echo $parentNetwork->_network_container; ?><?php if (!isnull($parentNetwork->_network_id)) { echo ",".$parentNetwork->_network_id; } ?><?php if (!isnull($parentNetwork->_network_group)) { echo ",".$parentNetwork->_network_group; } ?>); return false;" title="Close"><img src="images/cancel.gif" alt="Close" id="cancel"></a>
<?php } else { ?>
<a href="" onclick="dim('ipm_form',false); ShowContent('networks',<?php echo $network->_network_container; ?>,<?php echo $network->_network_id; ?><?php if (!isnull($network->_network_group)) { echo ",".$network->_network_group; } ?>); return false;" title="Close"><img src="images/cancel.gif" alt="Close" id="cancel"></a>
<?php } ?>

<?php if ($authContainer < 1) { ?>

	<div class="ipm_error">Error: You are not authorized to view the selected content.</div>
	<?php 
		exit();
} ?>

<?php
if (!isnull($_GET['network']) && !($user->getAuthNetwork($network->_network_id, $_SESSION['MM_Username']) > 20 || ($user->getAuthNetworkgroup($network->_network_group,$_SESSION['MM_Username']) > 20 && $user->getAuthNetwork($network->_network_id,$_SESSION['MM_Username']) == "") || ($user->getAuthContainer($network->_network_container,$_SESSION['MM_Username']) > 20 && $user->getAuthNetworkGroup($network->_network_group,$_SESSION['MM_Username']) == "" && $user->getAuthNetwork($network->_network_id,$_SESSION['MM_Username']) == ""))) { ?>

	<div class="ipm_error">Error: You are not authorized to view the selected content.</div>
	<?php 
		exit();
} ?>

<?php

#Process POST data


if (isset($_POST['token'])) {

	#Validate network
	
	if ($network->_network_subnetted) { ?>
	
		<div class="ipm_error">Error: The network is subnetted and cannot be deleted.</div>
	<?php 
		exit();
		
	}
	elseif ($network->getLinks()) { ?>
	
		<div class="ipm_error">Error: The network is associated with a link.  Please delete the link first.</div>
	<?php 
		exit();
		
	}
	elseif ($network->getLinkNetworks()) { ?>
	
		<div class="ipm_error">Error: The network is associated with a link.  Please delete the link first.</div>
	<?php 
		exit();
		
	}
	
	$sql = $db->deleteRecord("networks",$network->_network_id,'');
	$sql = $db->deleteCustom("addresses","network",$network->_network_id);
		
		?>
		<p>Action completed.</p>
		
		<a href="" onclick="dim('ipm_form',false); ShowContent('networks',<?php echo $network->_network_container; ?>,<?php echo $network->_network_parent; ?><?php if (!isnull($network->_network_group)) { echo ",".$network->_network_group; } ?>); return false;" title="Close"><br /><div class="ipm_menu_button">Close</div>
	
	<?php
		exit();
		
}

?>

<h2>Delete Network <?php if (isnull($network->_network_v6_mask)) { echo long2ip($network->_network).get_slash(long2ip($network->_network_mask)); } else { echo Net_IPv6::Compress(long2ipv6($network->_network))."/".$network->_network_v6_mask; } ?></h2>

<div id="ipm_table_2col">
	
	<div id="row">
		
		<div id="col1" style="width: 150px"><img src="images/warning.png" alt="Warning"></div>
		<div id="col2"><strong>Are you sure you want to delete this network?  This action cannot be undone.</strong><br /><br />Note: all addresses belonging to this network will be deleted.  You cannot delete a network with associated links, or with subnetworks.<br /><br /><a href="" onclick="PostDialogContent('DeleteNetwork',<?php echo $network->_network_id; ?>,'id=<?php echo $network->_network_id; ?>&token=delete'); return false;"><div class="ipm_menu_button">Delete</div></a></div>
		<div class="clear"></div>
		
	</div>
	
</div>

