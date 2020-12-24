<?php
// header('Access-Control-Allow-Origin: *');
// header("Access-Control-Allow-Headers: Content-Type");
// header('Content-Type: application/json');
global $global, $config;
if(!empty($_GET) && empty($_POST)){
    $_POST = $_GET;
}
if(!isset($global['systemRootPath'])){
    require_once '../videos/configuration.php';
}
require_once $global['systemRootPath'] . 'objects/user.php';
require_once 'video.php';

$obj = new Video("", "", $_POST['videos_id']);
if($_POST['type'] == 'standard')
	$obj->setIsStandardFeatured($_POST['add']);
else
	$obj->setIsPremiumFeatured($_POST['add']);
$id = $obj->save();

echo '{"status":"'.$id.$_POST['videos_id']."--".$_POST['type']."--".$_POST['add'].'"}';