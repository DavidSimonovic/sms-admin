<?php
/*
request format: clid=camp&reqip=ip&other_url_vars
then request with correct headers
especially things like 
HTTP_CLIENT_IP and 
HTTP_X_FORWARDED_FOR

Also pass
HTTP_ACCEPT
HTTP_ACCEPT_ENCODING
HTTP_ACCEPT_LANGUAGE
HTTP_ACCEPT_CHARSET
HTTP_REFERER
*/

//defines the location of noip client - you can get this line from the simple php deploy method
//** MAKE SURE YOU CHANGE THIS SO IT IS VALID FOR YOUR INSTALL
define('APPLOC','/var/www/public/nip/api/');

$apiResponse=true;
$apiError="";
$apiResult=array();
$goto="";

//make sure request is valid
if (!isset($_GET['clid']) || !isset($_GET['reqip']) ) {
    $apiResponse=false;
    $apiError="Invalid api request url. Must contain clid and reqip variables";
}
if (!file_exists(APPLOC.'config.php')) {
    $apiResponse=false;
    $apiError="Client install not completed. Please check the install guide you were sent when you joined. Also make sure you updated APPLOC in your api.php file.";
}

if ( $apiResponse === TRUE ) {
    $_SERVER['REMOTE_ADDR']=$_GET['reqip'];
    include_once(APPLOC.'go.php');
    $apiResponse=$isItSafe;
    $apiError=$isItSafe ? "" : "Blocked";
}

#return json object of result & dynamic variables
$result = array(
    'safe'=>$apiResponse,
    'goto'=>$goto,
    'data'=>(!empty($apiResult) ? $apiResult['geodata'] : []),
    'error'=>$apiError
);

if ( isset($_GET['debug']) ) {
    $result['debug']=['SERVER'=>$_SERVER,'GET'=>$_GET];
}

header('Content-Type: application/json');
echo json_encode($result);

?>