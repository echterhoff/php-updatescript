<?php

require("classes/Systemupdate.php");

use updatesystem\Systemupdate;
use updatesystem\Report;

if( $_REQUEST["get"] == "filelist" )
{
    Report::mute(true);
}
$systemupdate = new Systemupdate(__DIR__);
$systemupdate->loadConfigData();

if( $_REQUEST["get"] == "filelist" )
{
    echo json_encode($systemupdate->getFullFileList());
    exit;
}
else
{
    $systemupdate->scanFiles();
}



?>
