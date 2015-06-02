<?php

session_start();

function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  session_destroy();
  
  #session_unregister($_SESSION['MM_Username']);
  #session_unregister($_SESSION['MM_UserGroup']);
  #session_unregister($_SESSION['PrevUrl']);
	
  $logoutGoTo = "login.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}

function ipv62long($hex) {
	
	list($_7,$_6,$_5,$_4,$_3,$_2,$_1,$_0) = split(":",$hex);
	
	$dec7 = bcmul(hexdec($_7), bcpow(65536,7));
	$dec6 = bcmul(hexdec($_6), bcpow(65536,6));
	$dec5 = bcmul(hexdec($_5), bcpow(65536,5));
	$dec4 = bcmul(hexdec($_4), bcpow(65536,4));
	$dec3 = bcmul(hexdec($_3), bcpow(65536,3));
	$dec2 = bcmul(hexdec($_2), bcpow(65536,2));
	$dec1 = bcmul(hexdec($_1), bcpow(65536,1));
	$dec0 = bcmul(hexdec($_0), bcpow(65536,0));
	
	$dec = bcadd($dec7,$dec6);
	$dec = bcadd($dec,$dec5);
	$dec = bcadd($dec,$dec4);
	$dec = bcadd($dec,$dec3);
	$dec = bcadd($dec,$dec2);
	$dec = bcadd($dec,$dec1);
	$dec = bcadd($dec,$dec0);
	
	return($dec);
}
	
function long2ipv6($dec) {
	
	$bits7 = bcdiv($dec, bcpow(65536,7));

	$bits6_long = bcsub($dec, bcmul($bits7, bcpow(65536,7)));
	
	$bits6 = bcdiv($bits6_long, bcpow(65536,6));
	
	$bits5_long = bcsub($bits6_long, bcmul($bits6, bcpow(65536,6)));
	
	$bits5 = bcdiv($bits5_long, bcpow(65536,5));
	
	$bits4_long = bcsub($bits5_long, bcmul($bits5, bcpow(65536,5)));
	
	$bits4 = bcdiv($bits4_long, bcpow(65536,4));
	
	$bits3_long = bcsub($bits4_long, bcmul($bits4, bcpow(65536,4)));
	
	$bits3 = bcdiv($bits3_long, bcpow(65536,3));

	$bits2_long = bcsub($bits3_long, bcmul($bits3, bcpow(65536,3)));
	
	$bits2 = bcdiv($bits2_long, bcpow(65536,2));
	
	$bits1_long = bcsub($bits2_long, bcmul($bits2, bcpow(65536,2)));
	
	$bits1 = bcdiv($bits1_long, 65536);
	
	$bits0_long = bcsub($bits1_long, bcmul($bits1, 65536));
	
	$bits0 = $bits0_long;
	
	return(dechex($bits7).":".dechex($bits6).":".dechex($bits5).":".dechex($bits4).":".dechex($bits3).":".dechex($bits2).":".dechex($bits1).":".dechex($bits0));
	
}

function find_net($host,$mask) { 

   $bits=strpos(decbin(ip2long($mask)),"0");
   
   if ($bits == "") { $bits = 32; }
   
   $net["cidr"]=gethostbyname($host)."/".$bits; 

   $net["network"]=bindec(decbin(ip2long(gethostbyname($host))) & decbin(ip2long($mask))); 

   $binhost=str_pad(decbin(ip2long(gethostbyname($host))),32,"0",STR_PAD_LEFT); 
   $binmask=str_pad(decbin(ip2long($mask)),32,"0",STR_PAD_LEFT); 
   for ($i=0; $i<32; $i++) { 
      if (substr($binhost,$i,1)=="1" || substr($binmask,$i,1)=="0") { 
         $broadcast.="1"; 
      }  else { 
         $broadcast.="0"; 
      } 
   } 
   $net["broadcast"]=bindec($broadcast);
   $net["firstaddress"] = $net["network"]+1;
   $net["lastaddress"] = $net["broadcast"]-1;
   $net["total"] = ($net["broadcast"] - $net["network"]) -1;

   return $net; 
}

function get_slash ($mask,$slashflag = 1) {
	
	$bits=strpos(decbin(ip2long($mask)),"0");
	
	if ($mask == "255.255.255.255") {
		if ($slashflag == 1) {
			return "/32";
		}
		else {
			return "32";
		}
	}
	else {
		if ($slashflag == 1) {
			return "/".$bits;
		}
		else {
			return $bits;
		}
	}

}

function get_dotted_mask ($bits) {
	
	$slash = array(
		32 => "255.255.255.255",
		31 => "255.255.255.254",
		30 => "255.255.255.252",
		29 => "255.255.255.248",
		28 => "255.255.255.240",
		27 => "255.255.255.224",
		26 => "255.255.255.192",
		25 => "255.255.255.128",
		24 => "255.255.255.0",
		23 => "255.255.254.0",
		22 => "255.255.252.0",
		21 => "255.255.248.0",
		20 => "255.255.240.0",
		19 => "255.255.224.0",
		18 => "255.255.192.0",
		17 => "255.255.128.0",
		16 => "255.255.0.0",
		15 => "255.254.0.0",
		14 => "255.252.0.0",
		13 => "255.248.0.0",
		12 => "255.240.0.0",
		11 => "255.224.0.0",
		10 => "255.192.0.0",
		9 => "255.128.0.0",
		8 => "255.0.0.0");	
	
	return ($slash[$bits]);
	
}

function isnull ($var) {
	
	if ($var == "") {
		
		return true;
		
	}
	elseif ($var == " ") {
		
		return true;
		
	}
	elseif ($var == NULL) {
		
		return true;
		
	}
	elseif ($var == "null") {
		
		return true;
		
	}
	elseif ($var == "undefined") {
		
		return true;
		
	}

	elseif ($var == "0") {
		
		return true;
		
	}
	else {
		
		return false;
		
	}
	
}
?>