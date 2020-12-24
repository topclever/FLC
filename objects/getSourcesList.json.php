<?php
error_reporting(0);
global $global, $config;
if(!isset($global['systemRootPath'])){
    require_once '../videos/configuration.php';
}
// session_write_close();
require_once $global['systemRootPath'] . 'objects/video.php';

$video = new Video("", "", @$_POST['video_id']);
$result = $video->getSourcesList($_POST['user_id'], $_POST['video_id']);
$array = $video->getSourcesArray($_POST['user_id'], $_POST['video_id']);

// echo $result;

$obj = new stdClass();
$obj->status = true;
$obj->rows = $result;
$obj->sarray = json_encode($array);

echo json_encode($obj);