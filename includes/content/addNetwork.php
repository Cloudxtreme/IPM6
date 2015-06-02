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

?>

<a href="" onclick="dim('ipm_form',false); ShowContent('networks',<?php echo $parentNetwork->_network_container; ?>,<?php echo $parentNetwork->_network_id; ?><?php if (!isnull($parentNetwork->_network_group)) { echo ",".$parentNetwork->_network_group; } ?>); return false;" title="Close"><img src="images/cancel.gif" alt="Close" id="cancel"></a>

<?php if ($authContainer < 1) { ?>

	<div class="ipm_error">Error: You are not authorized to view the selected content.</div>
	<?php 
		exit();
} ?>

<?php
if (!isnull($_GET['network']) && !($user->getAuthNetwork($parentNetwork->_network_id, $_SESSION['MM_Username']) > 10 || ($user->getAuthNetworkgroup($parentNetwork->_network_group,$_SESSION['MM_Username']) > 10 && $user->getAuthNetwork($parentNetwork->_network_id,$_SESSION['MM_Username']) == "") || ($user->getAuthContainer($parentNetwork->_network_container,$_SESSION['MM_Username']) > 10 && $user->getAuthNetworkGroup($parentNetwork->_network_group,$_SESSION['MM_Username']) == "" && $user->getAuthNetwork($parentNetwork->_network_id,$_SESSION['MM_Username']) == ""))) { ?>

	<div class="ipm_error">Error: You are not authorized to view the selected content.</div>
	<?php 
		exit();
} ?>

<?php

#Process POST data


if (isset($_POST['token'])) {
	
	#Validate POST data
	
	$newNetwork = new network(0,$db);
		
	if (!isset($parentNetwork->_network_v6_mask)) {
		
		if ($_POST['mode'] == "manual") {
			$ip_calc = new Net_IPv4();
			$ip_calc->ip = $_POST['network'];
			$ip_calc->netmask = get_dotted_mask($_POST['mask']);
			$ip_calc->calculate();
			
			$_POST['network'] = $ip_calc->network;
		}
		elseif ($_POST['mode'] == 'auto') {
			$ip_calc = new Net_IPv4();
			$ip_calc->ip = long2ip($_POST['network']);
			$ip_calc->netmask = get_dotted_mask($_POST['mask']);
			$ip_calc->calculate();
			
			$_POST['network'] = $ip_calc->network;
		}
		
		if (!(Net_IPv4::validateIP($_POST['network']))) {?>
			<div class="ipm_error">Error: The network you entered is invalid.</div>
		<?php 
		}
		elseif ($parentNetwork->_network_id != 0 && (!(Net_IPv4::ipInNetwork($_POST['network'], long2ip($parentNetwork->_network).get_slash(long2ip($parentNetwork->_network_mask)))))) { ?>

			<div class="ipm_error">Error: The network is outside of the selected parent network.</div>
		<?php 
		}
		elseif ($newNetwork->checkOverlap($parentNetwork->_network_id,$_POST['network'],get_dotted_mask($_POST['mask']))) { ?>
			
			<div class="ipm_error">Error: The network overlaps with other networks in this container.</div>
		<?php
			
		}
		elseif (isnull($_POST['descr'])) { ?>
			
			<div class="ipm_error">Error: The description field is required.</div>
		<?php
			
		}
		elseif (isnull($_POST['network'])) { ?>
			
			<div class="ipm_error">Error: The network field is required.</div>
		<?php
			
		}
		else {
				
			$values = array(
				array("col" => "network",
					"val" => ip2long($_POST['network']),
					"type" => "char"),
				array("col" => "short",
					"val" => $_POST['network'],
					"type" => "char"),
				array("col" => "mask",
					"val" => get_dotted_mask($_POST['mask']),
					"type" => "char"),
				array("col" => "descr",
					"val" => $_POST['descr'],
					"type" => "char"),
				array("col" => "maskLong",
					"val" => ip2long(get_dotted_mask($_POST['mask'])),
					"type" => "char"),
				array("col" => "`user`",
					"val" => $_SESSION['MM_Username'],
					"type" => "char"),
				array("col" => "`date`",
					"val" => "now()",
					"type" => "int"),
				array("col" => "comments",
					"val" => $_POST['comments'],
					"type" => "char"),
				array("col" => "container",
					"val" => $_POST['container'],
					"type" => "char"),
				array("col" => "parent",
					"val" => $_POST['parent'],
					"type" => "char"),
				array("col" => "networkGroup",
					"val" => $_POST['netgroup'],
					"type" => "int")
				
					);
			
			$sql = $db->insertInto("networks",$values);
			
			?>
			<p>Action completed.</p>
			<a href="" onclick="dim('ipm_form',false); ShowContent('networks',<?php echo $parentNetwork->_network_container; ?>,<?php echo $parentNetwork->_network_id; ?><?php if (!isnull($parentNetwork->_network_group)) { echo ",".$parentNetwork->_network_group; } ?>); return false;" title="Close"><br /><div class="ipm_menu_button">Close</div>
		
			<?php
			exit();
				
		}
		
	}
	else {
				
		if ($_POST['mode'] == 'manual') {	
			$_POST['network'] = Net_IPv6::getNetmask(Net_IPv6::uncompress($_POST['network']), $_POST['mask']);
		}
		elseif ($_POST['mode'] == 'auto') {
			$_POST['network'] = Net_IPv6::getNetmask(Net_IPv6::uncompress(long2ipv6($_POST['network'])), $_POST['mask']);
		}
		if (!Net_IPv6::checkIPv6($_POST['network'])) { ?>
		
			<div class="ipm_error">Error: The network you entered is invalid.</div>
		<?php 
		}
		elseif ($parentNetwork->_network_id != 0 && (!(Net_IPv6::isInNetmask(Net_IPv6::compress($_POST['network']), Net_IPv6::compress(long2ipv6($parentNetwork->_network)), $parentNetwork->_network_v6_mask)))) {  ?>

		<div class="ipm_error">Error: The network is outside of the selected parent network.</div>
		<?php 
		}
		elseif ($newNetwork->checkOverlap($parentNetwork->_network_id,$_POST['network'],$_POST['mask'])) { ?>
		
		<div class="ipm_error">Error: The network overlaps with other networks in this container.</div>
		<?php
			
		}
		elseif (isnull($_POST['descr'])) { ?>
			
			<div class="ipm_error">Error: The description field is required.</div>
		<?php
			
		}
		elseif (isnull($_POST['network'])) { ?>
			
			<div class="ipm_error">Error: The network field is required.</div>
		<?php
			
		}
		else {
				
			$values = array(
				array("col" => "network",
					"val" => ipv62long($_POST['network']),
					"type" => "char"),
				array("col" => "short",
					"val" => $_POST['network'],
					"type" => "char"),
				array("col" => "v6mask",
					"val" => $_POST['mask'],
					"type" => "char"),
				array("col" => "descr",
					"val" => $_POST['descr'],
					"type" => "char"),
				array("col" => "`user`",
					"val" => $_SESSION['MM_Username'],
					"type" => "char"),
				array("col" => "`date`",
					"val" => "now()",
					"type" => "int"),
				array("col" => "comments",
					"val" => $_POST['comments'],
					"type" => "char"),
				array("col" => "container",
					"val" => $_POST['container'],
					"type" => "char"),
				array("col" => "parent",
					"val" => $_POST['parent'],
					"type" => "char"),
				array("col" => "networkGroup",
					"val" => $_POST['netgroup'],
					"type" => "int")
				
					);
							
			$sql = $db->insertInto("networks",$values);

			?>
			<p>Action completed.</p>
			<a href="" onclick="dim('ipm_form',false); ShowContent('networks',<?php echo $parentNetwork->_network_container; ?>,<?php echo $parentNetwork->_network_id; ?><?php if (!isnull($parentNetwork->_network_group)) { echo ",".$parentNetwork->_network_group; } ?>); return false;" title="Close"><br /><div class="ipm_menu_button">Close</div>
		
			<?php
			exit();
			
		}
	
	}
	
}

$allCustomers = new customer(0,$db);
$_allCustomers = $allCustomers->getAllCustomers($parentNetwork->_network_container);
$row_allCustomers = mysql_fetch_assoc($_allCustomers);

$allNetworkGroups = new networkgroup(0,$db);
$_allNetworkGroups = $allNetworkGroups->getAllNetworkgroups($parentNetwork->_network_container);
$row_allNetworkGroups = mysql_fetch_assoc($_allNetworkGroups);

?>

<h2>Add a <?php if (isset($_GET['network'])) { ?> Subnet to <?php if (isnull($parentNetwork->_network_v6_mask)) { echo long2ip($parentNetwork->_network).get_slash(long2ip($parentNetwork->_network_mask)); } else { echo Net_IPv6::Compress(long2ipv6($parentNetwork->_network))."/".$parentNetwork->_network_v6_mask; } ?><?php } else { ?>Network<?php } ?></h2>

<div id="ipm_table_2col">
	
	<div id="row">
		
		<div id="col1"><strong>Mode*</strong></div>
		<div id="col2">Auto <input type="radio" name="mode" id="mode" value="1" onchange="PostDialogContent('AddNetwork',<?php echo $parentNetwork->_network_id; ?>,'mode=auto')" <?php if ($_POST['mode'] == 'auto') { ?>checked=""<?php } ?>> &nbsp;&nbsp;&nbsp;Manual <input type="radio" name="mode" id="mode" value="0" onchange="PostDialogContent('AddNetwork',<?php echo $parentNetwork->_network_id; ?>,'mode=manual')" <?php if ($_POST['mode'] == 'manual') { ?>checked=""<?php } ?>></div>
		<div class="clear"></div>
		
	</div>

	<div id="row">
		
		<div id="col1"><strong>Mask*</strong></div>
		<div id="col2">
			<select id="addNetwork_mask" class="input_standard" <?php if ($_POST['mode'] == "auto") { ?>onChange="PostDialogContent('AddNetwork',<?php echo $parentNetwork->_network_id; ?>,'mask='+document.getElementById('addNetwork_mask').options[document.getElementById('addNetwork_mask').selectedIndex].value+'&parent=<?php echo $parentNetwork->_network_id; ?>&descr='+document.getElementById('addNetwork_descr').value+'&container=<?php echo $parentNetwork->_network_container; ?>&mode=auto&comments='+document.getElementById('addNetwork_comments').value); return false;"<?php } ?> style="float: left">
		      	<optgroup label="Common prefix lengths">
		      		<?php if (!isset($parentNetwork->_network_v6_mask)) { ?>
				        <?php if (get_slash(long2ip($parentNetwork->_network_mask),0) < 16) { ?>
				      	 <option value="16" <?php if (!(strcmp(16, $_POST['mask']))) {echo "SELECTED";} ?>>255.255.0.0 : /16</option>
				      	<?php } if (get_slash(long2ip($parentNetwork->_network_mask),0) < 24) { ?>
				      	 <option value="24" <?php if (!(strcmp(24, $_POST['mask']))) {echo "SELECTED";} ?>>255.255.255.0 : /24</option>
				      	<?php } if (get_slash(long2ip($parentNetwork->_network_mask),0) < 28) { ?>
				      	 <option value="28" <?php if (!(strcmp(28, $_POST['mask']))) {echo "SELECTED";} ?>>255.255.255.240 : /28</option>
				      	<?php } if (get_slash(long2ip($parentNetwork->_network_mask),0) < 29) { ?>
				      	 <option value="29" <?php if (!(strcmp(29, $_POST['mask']))) {echo "SELECTED";} ?>>255.255.255.248 : /29</option>
				      	<?php } if (get_slash(long2ip($parentNetwork->_network_mask),0) < 30) { ?>
				      	 <option value="30" <?php if (!(strcmp(30, $_POST['mask']))) {echo "SELECTED";} ?>>255.255.255.252 : /30</option>
				      	<?php } ?>
			    </optgroup>
			    <optgroup label="All prefix lengths">
			        <?php $sizeOfNet = strpos(decbin($parentNetwork->_network_mask),"0");
						$i = 32;
						while ($i > $sizeOfNet) { ?>
			        <option value="<?php echo $i; ?>" <?php if ($_POST['mask'] == $i) { echo "selected=\"selected\""; } ?>><?php echo get_dotted_mask($i); ?> : /<?php echo $i; ?></option>
			        <?php
						$i--;
						} ?>
				</optgroup>
				<?php } else { ?>
				      <?php if ($parentNetwork->_network_v6_mask < 32) { ?>
				      	 <option value="32" <?php if (!(strcmp(32, $_POST['mask']))) {echo "SELECTED";} ?>><strong>/32</strong></option>
				      	<?php } if ($parentNetwork->_network_v6_mask < 48) { ?>
				      	 <option value="48" <?php if (!(strcmp(48, $_POST['mask']))) {echo "SELECTED";} ?>><strong>/48</strong></option>
				      	<?php } if ($parentNetwork->_network_v6_mask < 56) { ?>
				      	 <option value="56" <?php if (!(strcmp(56, $_POST['mask']))) {echo "SELECTED";} ?>><strong>/56</strong></option>
				      	<?php } if ($parentNetwork->_network_v6_mask < 64) { ?>
				      	 <option value="64" <?php if (!(strcmp(64, $_POST['mask']))) {echo "SELECTED";} ?>><strong>/64</strong></option>
				      	<?php } ?>
				      </optgroup>
				      <optgroup label="All prefix lengths">
				      	<?php $i = 128;
							while ($i > $parentNetwork->_network_v6_mask) { ?>
				        <option value="<?php echo $i; ?>" <?php if (!(strcmp($i, $_POST['mask']))) {echo "SELECTED";} ?>>/<?php echo $i; ?></option>
				        <?php $i--;
				        	} ?>
				      </optgroup>
				<?php } ?>
		    </select>
		</div>
	</div>
	<div class="clear"></div>
	
	<span id="auto" <?php if ($_POST['mode'] != "auto") { ?>style="display: none;"<?php } ?>>
		<div id="row">
			
			<div id="col1">&nbsp;</div>
			<div id="col2"><a href="" onclick="PostDialogContent('AddNetwork',<?php echo $parentNetwork->_network_id; ?>,'mask='+document.getElementById('addNetwork_mask').options[document.getElementById('addNetwork_mask').selectedIndex].value+'&parent=<?php echo $parentNetwork->_network_id; ?>&descr='+document.getElementById('addNetwork_descr').value+'&container=<?php echo $parentNetwork->_network_container; ?>&mode=auto&comments='+document.getElementById('addNetwork_comments').value); return false;"><div class="ipm_menu_button">Search for Subnets</div></a></div>
			<div class="clear"></div>
			
		</div>
		<?php 
		if (isset($_POST['mask']) && $_POST['mode'] == 'auto') {
			$networks = new network(0,$db);
			$candidateNetworks = $networks->getNextNetwork($parentNetwork->_network_id,$_POST['mask']); ?>
			
			<div id="row">
				
				<div id="col1"><strong>Network*</strong></div>
				<div id="col2">
					
					<?php if (count($candidateNetworks) == 0) { ?>
						<div class="ipm_error">There are no available subnets of this size within the parent network.  Please choose a different mask, or select another network.</div>
					<?php } else { ?>
					Please choose from one of the free subnets below (showing the first 256 available subnets):<br />
					<select size="10" class="input_standard" id="addNetwork_network">
			          <?php for($i=0;$i<count($candidateNetworks);$i++) { ?>
			          <option value="<?php echo $candidateNetworks[$i]; ?>"><?php if (!isset($parentNetwork->_network_v6_mask)) { echo long2ip($candidateNetworks[$i]); } else { echo Net_IPv6::Compress(long2ipv6($candidateNetworks[$i])); } ?></option>
			          <?php } ?>
			        </select>
			        <?php } ?>
					
				</div>
				<div class="clear"></div>
				
			</div>
		
		<?php
		}
		?>
	</span>
	<span id="manual" <?php if ($_POST['mode'] != 'manual') { ?>style="display: none;"<?php } ?>>
	
		<?php if ($_POST['mode'] == "manual") { ?>
			<div id="row">
				
				<div id="col1"><strong>Network*</strong></div>
				<div id="col2"><input type="text" size="50" class="input_standard" id="addNetwork_network" value="<?php echo $_POST['network']; ?>"></div>
				<div class="clear"></div>
				
			</div>
			<div id="row">
		<?php } ?>
	</span>

	<div id="row">
		
		<div id="col1"><strong>Description*</strong></div>
		<div id="col2"><input type="text" size="50" class="input_standard" id="addNetwork_descr" value="<?php echo $_POST['descr']; ?>"></div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		
		<div id="col1"><strong>Network Group</strong></div>
		<div id="col2">
			<?php if ($allNetworkGroups->_networkgroups_total) { ?>
				
				<select id="addNetwork_group" class="input_standard">
					
					<option value="">None</option>
					<?php do { ?>
					
						<option value="<?php echo $row_allNetworkGroups['id']; ?>" <?php if ($_POST['netgroup'] == $row_allNetworkGroups['id']) { echo "SELECTED"; } elseif ($parentNetwork->_network_group == $row_allNetworkGroups['id']) { echo "SELECTED"; } ?>><?php echo $row_allNetworkGroups['name']; ?></option>
					
					<?php } while ($row_allNetworkGroups = mysql_fetch_assoc($_allNetworkGroups)); ?>
					
				</select>
				
			<?php } else { ?>
				
				There are no network groups defined for this container.
				
			<?php } ?>
		</div>
		<div class="clear"></div>
		
	</div>
	<div id="row">
		
		<div id="col1"><strong>Comments</strong></div>
		<div id="col2"><textarea class="input_standard" cols="50" rows="5" id="addNetwork_comments"><?php echo $_POST['comments']; ?></textarea></div>
		<div class="clear"></div>
		
	</div>
	
	<?php
	if (($_POST['mode'] == 'auto' && isset($_POST['mask'])) || $_POST['mode'] == 'manual') {
	?>
	
	<div id="row">
	
		<div id="col1"><a href="" onclick="PostDialogContent('AddNetwork',<?php echo $parentNetwork->_network_id; ?>,'network='+document.getElementById('addNetwork_network').value+'&mask='+document.getElementById('addNetwork_mask').options[document.getElementById('addNetwork_mask').selectedIndex].value+'&parent=<?php echo $parentNetwork->_network_id; ?>&descr='+document.getElementById('addNetwork_descr').value+<?php if ($allNetworkGroups->_networkgroups_total > 0) { ?>'&netgroup='+document.getElementById('addNetwork_group').options[document.getElementById('addNetwork_group').selectedIndex].value+<?php } ?>'&container=<?php echo $parentNetwork->_network_container; ?>&token=insert&mode=<?php echo $_POST['mode']; ?>&comments='+document.getElementById('addNetwork_comments').value); return false;"><div class="ipm_menu_button">Add</</a></div>
		<div class="clear"></div>
		
	</div>
	
	<?php } ?>
	
</div>

