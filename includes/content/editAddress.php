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

if (isset($_GET['address'])) {
	
	$address = new address($_GET['address'],$db);
	
}

$parentNetwork = new network($address->_address_network,$db);

if (isset($parentNetwork->_network_container)) {

	$authContainer = $user->getAuthContainer($parentNetwork->_network_container);
	
}

?>

<a href="" onclick="dim('ipm_form',false); ShowContent('networks',<?php echo $parentNetwork->_network_container; ?>,<?php echo $parentNetwork->_network_id; ?><?php if (!isnull($parentNetwork->_network_group)) { echo ",".$parentNetwork->_network_group; } ?>); return false;" title="Close"><img src="images/cancel.gif" alt="Close" id="cancel"></a>

<?php if ($authContainer < 1) { ?>

	<div class="ipm_error">Error: You are not authorized to view the selected content.</div>
	<?php 
		exit();
} ?>

<?php
if (!isnull($_GET['address']) && !($user->getAuthNetwork($parentNetwork->_network_id, $_SESSION['MM_Username']) > 10 || ($user->getAuthNetworkgroup($parentNetwork->_network_group,$_SESSION['MM_Username']) > 10 && $user->getAuthNetwork($parentNetwork->_network_id,$_SESSION['MM_Username']) == "") || ($user->getAuthContainer($parentNetwork->_network_container,$_SESSION['MM_Username']) > 10 && $user->getAuthNetworkGroup($parentNetwork->_network_group,$_SESSION['MM_Username']) == "" && $user->getAuthNetwork($parentNetwork->_network_id,$_SESSION['MM_Username']) == ""))) { ?>

	<div class="ipm_error">Error: You are not authorized to view the selected content.</div>
	<?php 
		exit();
} ?>

<?php

#Process POST data


if (isset($_POST['token'])) {

	#Validate POST data
	
	if ($_POST['descr'] == "") { ?>
	
		<div class="ipm_error">Error: The description field is required.</div>
	<?php 
		
	}
	elseif ($_POST['customer'] == "") { ?>
	
		<div class="ipm_error">Error: The customer field is required.</div>

	<?php		
	}
	else {

		$values = array(
				array("col" => "descr",
					"val" => $_POST['descr'],
					"type" => "char"),
				array("col" => "customer",
					"val" => $_POST['customer'],
					"type" => "int"),
				array("col" => "comments",
					"val" => $_POST['comments'],
					"type" => "char"),
				array("col" => "`updateUser`",
					"val" => $_SESSION['MM_Username'],
					"type" => "char"),
				array("col" => "updateDate",
					"val" => "now()",
					"type" => "int")
				
			);
		
		$sql = $db->update("addresses",$address->_address_id,$values);
		
		?>
		<p>Action completed.</p>
		<a href="" onclick="dim('ipm_form',false); ShowContent('networks',<?php echo $parentNetwork->_network_container; ?>,<?php echo $parentNetwork->_network_id; ?><?php if (!isnull($parentNetwork->_network_group)) { echo ",".$parentNetwork->_network_group; } ?>); return false;" title="Close"><br /><div class="ipm_menu_button">Close</div>
	
	<?php
		exit();
		
	}
	
}

$allCustomers = new customer(0,$db);
$_allCustomers = $allCustomers->getAllCustomers($parentNetwork->_network_container);
$row_allCustomers = mysql_fetch_assoc($_allCustomers);

?>

<h2>Edit Address <?php if (isnull($parentNetwork->_network_v6_mask)) { echo long2ip($address->_address); } else { echo Net_IPv6::Compress(long2ipv6($address->_address)); } ?></h2>

<div id="ipm_table_2col" style="width: 50%">
	
	<div id="row">
		
		<div id="col1"><strong>Description*</strong></div>
		<div id="col2"><input type="text" class="input_standard" size="50" maxlength="255" id="editAddress_descr" value="<?php echo $address->_address_description; ?>"></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		
		<div id="col1"><strong>Customer*</strong></div>
		<div id="col2"><select class="input_standard" id="editAddress_customer">
			<?php do { ?>
				<option value="<?php echo $row_allCustomers['id']; ?>" <?php if ($address->_address_customer_id == $row_allCustomers['id']) { echo "selected"; } ?>><?php echo $row_allCustomers['name']; ?>
			<?php } while ($row_allCustomers = mysql_fetch_assoc($_allCustomers)); ?>
			</select></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		
		<div id="col1"><strong>Comments</strong></div>
		<div id="col2"><textarea class="input_standard" cols="50" rows="5" id="editAddress_comments"><?php echo $address->_address_comments; ?></textarea></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		<div id="col1">&nbsp;</div>
		<div class="clear"></div>
	</div>
	<div id="row">
		
		<div id="col1"><a href="" onclick="PostDialogContent('EditAddress',<?php echo $address->_address_id; ?>,'descr='+document.getElementById('editAddress_descr').value+'&customer='+document.getElementById('editAddress_customer').value+'&comments='+document.getElementById('editAddress_comments').value+'&token=update'); return false;"><div class="ipm_menu_button">Update</div></a></div>
		<div class="clear"></div>
		
	</div>
	
</div>

