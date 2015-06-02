<?php include('includes/ipm_class.php'); ?>
<?php require_once('Net/IPv4.php'); ?>
<?php require_once('Net/IPv6.php'); ?>

<?php

//initialize the session
if (!isset($_SESSION)) {
  session_start();
}

?>
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

$authPage = $user->getAuthPage(2);

if (isset($_GET['container'])) {
	
	$openContainer = new container($_GET['container'], $db);
	$authContainer = $user->getAuthContainer($_GET['container']);
	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $title; ?></title>
<link rel="stylesheet" type="text/css" href="css/ipm6.css" />
<link rel="stylesheet" type="text/css" href="css/jqueryslidemenu.css" />

<!--[if lte IE 7]>
<style type="text/css">
html .jqueryslidemenu{height: 1%;} /*Holly Hack for IE7 and below*/
</style>
<![endif]-->

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
<script type="text/javascript" src="jqueryslidemenu.js"></script>

<!-- SmartMenus 6 config and script core files -->
<script type="text/javascript" src="c_config.js"></script>
<script type="text/javascript" src="c_smartmenus.js"></script>
<!-- SmartMenus 6 config and script core files -->

<!-- SmartMenus 6 Scrolling for Overlong Menus add-on -->
<script type="text/javascript" src="c_addon_scrolling.js"></script>

<script type="text/javascript">
<!--
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_validateForm() { //v4.0
  var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
  for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=MM_findObj(args[i]);
    if (val) { nm=val.name; if ((val=val.value)!="") {
      if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
        if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
      } else if (test!='R') { num = parseFloat(val);
        if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
        if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
          min=test.substring(8,p); max=test.substring(p+1);
          if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
    } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
  } if (errors) alert('The following error(s) occurred:\n'+errors);
  document.MM_returnValue = (errors == '');
}

function checkAll(theForm, status) {
for (i=0,n=theForm.elements.length;i<n;i++)
theForm.elements[i].checked = status;
}
function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
function MM_callJS(jsStr) { //v2.0
  return eval(jsStr)
}
//-->
</script>


<script type="text/javascript">
	
function dim(div,bool)
{
    if (typeof bool=='undefined') bool=true; // so you can shorten dim(true) to dim()
    document.getElementById('dimmer').style.display=(bool?'block':'none');
    document.getElementById(div).innerHTML = 'Please wait...';
    document.getElementById(div).style.display=(bool?'block':'none');
}

function showCustomers(str, end)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("customers" + str + end).innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","includes/nav/customers.php?container=<?php echo $_GET['container']; ?>&start="+str+"&end="+end,true);
xmlhttp.send();
}

function showLinks(str, end)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("links" + str + end).innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","includes/nav/links.php?container=<?php echo $_GET['container']; ?>&start="+str+"&end="+end,true);
xmlhttp.send();
}

function showTemplates(str)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("template" + str).innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","includes/nav/templates.php?container=<?php echo $_GET['container']; ?>&link="+str,true);
xmlhttp.send();
}

function searchQry(str,limit)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("searchQ").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","includes/nav/search.php?container=<?php echo $_GET['container']; ?>&search="+str+"&limit="+limit,true);
xmlhttp.send();
}

function searchQry_networks(str)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("searchQ_1").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","includes/nav/search_networks.php?container=<?php echo $_GET['container']; ?>&search="+str,true);
xmlhttp.send();
}

function searchQry_parents(str)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("searchQ_1").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","includes/nav/search_parents.php?container=<?php echo $_GET['container']; ?>&search="+str,true);
xmlhttp.send();
}

function ShowChildren(network, count)
{
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("child_network" + network).innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","includes/nav/child_networks.php?container=<?php echo $_GET['container']; ?>&network="+network+"&count="+count,true);
xmlhttp.send();
}

function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function ShowContent(contenttype,container,id,options,options1,options2,options3) {
		
	document.getElementById("preload").style.display = 'block';
	  
	if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
		document.getElementById("ipm_content").innerHTML=xmlhttp.responseText;
		document.getElementById("preload").style.display = 'none';
    }
  }
  if (contenttype == "networks") {
	xmlhttp.open("GET","includes/content/networks.php?container="+container+"&parent="+id+"&group="+options+"&sort="+options1+"&dir="+options2+"&expand="+options3,true);	  
  }
  if (contenttype == "customers") {
	xmlhttp.open("GET","includes/content/customers.php?container="+container+"&customer="+id+"&sort="+options1+"&dir="+options2,true);
  }
  if (contenttype == "networkgroups") {
	xmlhttp.open("GET","includes/content/networkgroups.php?container="+container+"&netgroup="+id+"&sort="+options1+"&dir="+options2,true);
  }
  xmlhttp.send();
  
}

function ShowDialogContent(div,contenttype,id,options) {
	
	if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById(div).innerHTML=xmlhttp.responseText;
    }
  }
  if (contenttype == "networkInfo") {
	xmlhttp.open("GET","includes/content/networkInfo.php?network="+id,true);	  
  }
  if (contenttype == "AddAddress") {
	xmlhttp.open("GET","includes/content/addAddress.php?network="+id,true);	  
  }
  if (contenttype == "EditAddress") {
	xmlhttp.open("GET","includes/content/editAddress.php?address="+id,true);	  
  }
  if (contenttype == "DeleteAddress") {
	xmlhttp.open("GET","includes/content/deleteAddress.php?address="+id,true);
  }
  if (contenttype == "AddNetwork") {
	xmlhttp.open("GET","includes/content/addNetwork.php?network="+id,true);
  }
  if (contenttype == "AddBaseNetwork") {
	xmlhttp.open("GET","includes/content/addBaseNetwork.php?container="+id,true);
  }
  if (contenttype == "EditNetwork") {
	xmlhttp.open("GET","includes/content/editNetwork.php?network="+id,true);
  }
  if (contenttype == "DeleteNetwork") {
	xmlhttp.open("GET","includes/content/deleteNetwork.php?network="+id,true);
  }
  xmlhttp.send();
}

function PostDialogContent(contenttype,id,str) {
	if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("ipm_form").innerHTML=xmlhttp.responseText;
    }
  }
  if (contenttype == "AddAddress") {
	xmlhttp.open("POST","includes/content/addAddress.php?network="+id,true);
  }
  if (contenttype == "EditAddress") {
	xmlhttp.open("POST","includes/content/editAddress.php?address="+id,true);
  }
  if (contenttype == "DeleteAddress") {
	xmlhttp.open("POST","includes/content/deleteAddress.php?address="+id,true);
  }
  if (contenttype == "AddNetwork") {
	xmlhttp.open("POST","includes/content/addNetwork.php?network="+id,true);
  }
  if (contenttype == "AddBaseNetwork") {
	xmlhttp.open("POST","includes/content/addBaseNetwork.php?container="+id,true);
  }
  if (contenttype == "EditNetwork") {
	xmlhttp.open("POST","includes/content/editNetwork.php?network="+id,true);
  }
  if (contenttype == "DeleteNetwork") {
	xmlhttp.open("POST","includes/content/deleteNetwork.php?network="+id,true);
  }
  xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  xmlhttp.send(str);
}

function ShowOptions(divId,menuId) {
	
	if (document.getElementById(divId).getAttribute('class') == "options") {
		document.getElementById(divId).setAttribute('class', 'optionsOver');
		document.getElementById(menuId).style.display = "block";
	}
	else {
		document.getElementById(divId).setAttribute('class', 'options');
		document.getElementById(menuId).style.display = "none";
	}
	
}
</script>

</head>

<body>
<div class="banner">
  &nbsp;<?php echo $title; ?><div style="float: right; padding-right: 50%; display: none" id="preload"><img alt="Loading..." src="images/preload.gif"></div><div class="clear"></div>
</div>
<div id="ipm_dialog">Please wait...</div>
<div id="ipm_form">Please wait...</div>
<div id="dimmer"></div>
<div class="clear"></div>
<div class="ipm_body">

<?php if (isset($_GET['status']) && $_GET['status'] == 'permissionfail') { ?>
<p class="ipm_error">Error: You are not authorised to view the selected content.</p>
<?php 
	exit();
} ?>
<?php if ($authPage < 1) { ?>
<div class="ipm_error">Error: You are not authorised to view the selected content.</div>
<?php 
	exit();
} ?>
<?php if (!isset($_GET['container'])) { ?>
<div class="ipm_error">Error: No container has been selected.</div>
<?php 
	exit();
} ?>
<?php if ($authContainer < 1) { ?>
<div class="ipm_error">Error: You are not authorised to view the selected content.</div>
<?php 
	exit();
} ?>

<div class="loginheader">&nbsp; <a class="text_white" title="<?php echo $openContainer->_container_description; ?>"><?php echo $openContainer->_container_name; ?></a></div>

<div class="ipm_nav">
	<div id="myslidemenu" class="jqueryslidemenu">
		<ul>
		<?php require_once('includes/nav/ipm_nav_standard.php'); ?>
        <?php require_once('includes/nav/ipm_nav_standard_middle.php'); ?>
        <?php require_once('includes/nav/ipm_nav_standard_footer.php'); ?>
		</ul>
    </div>
</div>
<div id="ipm_content">

</div>			
</div>
</body>
</html>