<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
$obj = new stdClass();
$obj->error = true;

global $global, $config;
if(!isset($global['systemRootPath'])){
    require_once '../videos/configuration.php';
}
require_once $global['systemRootPath'] . 'objects/user.php';
require_once $global['systemRootPath'] . 'objects/video.php';

if (empty($_POST)) {
    $obj->msg = __("Your POST data is empty may be your video file too big for the host");
    _error_log("ReceiveImage: ".$obj->msg);
    die(json_encode($obj));
}

// pass admin user and pass
// $user = new User("", @$_POST['user'], @$_POST['password']);
// tempcode receive video from encoder, create user with email and password. 
// origin is created withuser and password
$user = new User("", null, @$_POST['password'], @$_POST['user']);
$user->login_encoder(false, true);
$user->login(false, true);
if (!User::canUpload()) {
    $obj->msg = __("Permission denied to receive a file: " . print_r($_POST, true));
    _error_log("ReceiveImage: ".$obj->msg);
    die(json_encode($obj));
}

if(!Video::canEdit($_POST['videos_id'])){
    $obj->msg = __("Permission denied to edit a video: " . print_r($_POST, true));
    _error_log("ReceiveImage: ".$obj->msg);
    die(json_encode($obj));
}
_error_log("ReceiveImage: "."Start receiving image");
// check if there is en video id if yes update if is not create a new one
$video = new Video("", "", $_POST['videos_id']);
$obj->video_id = $_POST['videos_id'];

_error_log("ReceiveImage: "."Encoder receiving post ". print_r($_FILES, true));
//_error_log("ReceiveImage: ".print_r($_POST, true));

$videoFileName = $video->getFilename();

$destination_local = "{$global['systemRootPath']}videos/{$videoFileName}";

if (!empty($_FILES['image']['tmp_name']) && !file_exists("{$destination_local}.jpg")) {
    if (!move_uploaded_file($_FILES['image']['tmp_name'], "{$destination_local}.jpg")) {
        $obj->msg = print_r(sprintf(__("Could not move image file [%s.jpg]"), $destination_local), true);
        _error_log("ReceiveImage: ".$obj->msg);
        die(json_encode($obj));
    } 
}
if (!empty($_FILES['gifimage']['tmp_name']) && !file_exists("{$destination_local}.gif")) {
    if (!move_uploaded_file($_FILES['gifimage']['tmp_name'], "{$destination_local}.gif")) {
        $obj->msg = print_r(sprintf(__("Could not move gif image file [%s.gif]"), $destination_local), true);
        _error_log("ReceiveImage: ".$obj->msg);
        die(json_encode($obj));
    } 
}
if (!empty($_FILES['webpimage']['tmp_name']) && !file_exists("{$destination_local}.webp")) {
    if (!move_uploaded_file($_FILES['webpimage']['tmp_name'], "{$destination_local}.webp")) {
        $obj->msg = print_r(sprintf(__("Could not move webp image file [%s.webp]"), $destination_local), true);
        _error_log("ReceiveImage: ".$obj->msg);
        die(json_encode($obj));
    } 
}
$video_id = $video->save();
Video::updateFilesize($video_id);
$obj->error = false;
$obj->video_id = $video_id;
_error_log("ReceiveImage: "."Files Received for video {$video_id}: " . $video->getTitle());
die(json_encode($obj));

/*
_error_log(print_r($_POST, true));
_error_log(print_r($_FILES, true));
var_dump($_POST, $_FILES);
*/
