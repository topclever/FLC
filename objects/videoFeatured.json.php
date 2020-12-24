<?php
// tempcode featured video set
error_reporting(0);
// header('Content-Type: application/json');
global $global, $config;
if(!isset($global['systemRootPath'])){
    require_once '../videos/configuration.php';
} 
require_once $global['systemRootPath'] . 'objects/user.php';
require_once 'video.php';

$obj = new Video("", "", $_POST['videos_id']);
$isStandardFeatured = $obj->getIsStandardFeatured();
$isPremiumFeatured = $obj->getIsPremiumFeatured();

echo '{"isStandardFeatured":' . $isStandardFeatured. ',"isPremiumFeatured":' . $isPremiumFeatured. ' }';