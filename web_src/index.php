<?php
// ini_set('display_errors', 1);
// error_reporting(E_ALL & ~E_NOTICE);
session_start();

require_once "includes/config.php";
require_once "classes/DatabaseAPIConnection.php";
require_once "classes/SaveProcessRouter.php";
require_once "classes/LoginProcess.php";
require_once "classes/PageRouter.php";
require_once "classes/GeneralContent.php";
require_once "classes/EditItemForm.php";
require_once "classes/CheckLang.php";
require_once "lang/loadLang.php";



$title = "Blue Jay Pantry";
$useFoodTabs = false;
$useChartTabs = false;
$useCategoryTabs = false;
$content = '';

SaveProcessRouter::processData();
if($page=="cart" || $page=="scannercart") {
    header("Location: kiosk.php"); 
}


        
$content .= PageRouter::getContent($page,$url);

if($page=="labels"){
 echo $content;
 exit;
}



//page content now out to screen
require_once "includes/header.php";
//hide header for handheld scanner screen

require_once "includes/sidebar.php";


    if($useFoodTabs){
        require_once "includes/foodtabs.php";
    }
    if($useChartTabs){
        require_once "includes/charttabs.php";
    }

echo $content;
//hide footer for handheld scanner screen
require_once "includes/footerbar.php";

require_once "includes/footer.php";
?>