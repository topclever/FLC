<?php

require_once '../../videos/configuration.php';
set_time_limit(0);
session_write_close();
require_once $global['systemRootPath'] . 'plugin/CloneSite/Objects/Clones.php';
require_once $global['systemRootPath'] . 'plugin/CloneSite/functions.php';
header('Content-Type: application/json');

$videosDir = "{$global['systemRootPath']}videos/";
$clonesDir = "{$videosDir}cache/clones/";
$photosDir = "{$videosDir}userPhoto/";

$resp = new stdClass();
$resp->error = true;
$resp->msg = "";
$resp->url = $_GET['url'];
$resp->key = $_GET['key'];
$resp->useRsync = intval($_GET['useRsync']);
$resp->videosDir = "{$global['systemRootPath']}videos/";
$resp->sqlFile = "";
$resp->videoFiles = array();
$resp->photoFiles = array();

$objClone = AVideoPlugin::getObjectDataIfEnabled("CloneSite");
if(empty($objClone)){
    $resp->msg = "CloneSite is not enabled on the Master site";
    die(json_encode($resp));
}
// check if the url is allowed to clone it
$canClone = Clones::thisURLCanCloneMe($resp->url, $resp->key);
if(empty($canClone->canClone)){
    $resp->msg = $canClone->msg;
    die(json_encode($resp));
}

if(!empty($_GET['deleteDump'])){
    $resp->error = !unlink("{$clonesDir}{$_GET['deleteDump']}");
    $resp->msg = "Delete Dump {$_GET['deleteDump']}";
    die(json_encode($resp));
}

if (!file_exists($clonesDir)) {
    mkdir($clonesDir, 0777, true);
    file_put_contents($clonesDir."index.html", '');
}

$resp->sqlFile = uniqid('Clone_mysqlDump_').".sql";
// update this clone last request
$resp->error = !$canClone->clone->updateLastCloneRequest();


// tempcode remove backupfiles---------------------
$backupDir = "/var/backups/";

$dbfile1 = "database-".date('Y-m-d',strtotime("-3 days"));
$dbfile2 = "database-".date('Y-m-d',strtotime("-2 days"));
$videofile1 = "videos-".date('Y-m-d',strtotime("-3 days"));
$videofile2 = "videos-".date('Y-m-d',strtotime("-2 days"));
$afiles = scandir($backupDir);
$removefile = "";
for ($i=0; $i < count($afiles); $i++) { 
	if (strrpos($afiles[$i], $dbfile1) !== false ||
		strrpos($afiles[$i], $dbfile2) !== false ||
		strrpos($afiles[$i], $videofile1) !== false ||
		strrpos($afiles[$i], $videofile2) !== false)
	{
	    $removefile .= $backupDir.$afiles[$i]." ";
	}
}

$cmd = "rm {$removefile}";
_error_log("Check remove backup files: {$removefile}");
exec($cmd . " 2>&1", $output, $return_val);
if ($return_val !== 0) {
    _error_log("remove backup files Error: " . print_r($output, true));
}
_error_log("Check remove backup files: Nice!");
//--------------------------------------------------

// get mysql dump
$cmd = "mysqldump -u {$mysqlUser} -p{$mysqlPass} --host {$mysqlHost} {$mysqlDatabase} > {$clonesDir}{$resp->sqlFile}";
_error_log("Clone: Dump to {$clonesDir}{$resp->sqlFile}");
exec($cmd." 2>&1", $output, $return_val);
if ($return_val !== 0) {
    _error_log("Clone Error: ". print_r($output, true));
}

if(empty($resp->useRsync)){
    $resp->videoFiles = getCloneFilesInfo($videosDir);
    $resp->photoFiles = getCloneFilesInfo($photosDir, "userPhoto/");
}

echo json_encode($resp);