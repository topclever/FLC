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

$_POST['videos_id'] = intval($_POST['videos_id']);

if (empty($_POST)) {
    $obj->msg = __("Your POST data is empty may be your vide file is too big for the host");
    _error_log($obj->msg);
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
    $obj->msg = __("Permission denied to Notify Done: " . print_r($_POST, true));
    _error_log($obj->msg);
    die(json_encode($obj));
}

if(!Video::canEdit($_POST['videos_id'])){
    $obj->msg = __("Permission denied to edit a video: " . print_r($_POST, true));
    _error_log($obj->msg);
    die(json_encode($obj));
}

// check if there is en video id if yes update if is not create a new one
$video = new Video("", "", $_POST['videos_id']);
$obj->video_id = $_POST['videos_id'];

if(empty($_POST['fail'])){
    $status = $video->getStatus();
    // if status is not unlisted
    if($status!=='u' && $status !== 'a'){
        if(empty($advancedCustom->makeVideosInactiveAfterEncode)){
            // set active
            $video->setStatus('a');
        }else if(empty($advancedCustom->makeVideosUnlistedAfterEncode)){
            // set active
            $video->setStatus('u');
        }else{
            $video->setStatus('i');
        }
    }
}else{
    $video->setStatus('i');
}
$video_id = $video->save();
if(empty($_POST['fail'])){
    // tempcode 4.notification disable to send email here
    // AVideoPlugin::afterNewVideo($_POST['videos_id']);
}
$obj->error = false;
$obj->video_id = $video_id;
Video::updateFilesize($video_id);
// delete original files if any
$originalFilePath =  "{$global['systemRootPath']}videos/original_" . $video->getFilename();
if(file_exists($originalFilePath)){
    unlink($originalFilePath);
}
_error_log("Video is done notified {$video_id}: " . $video->getTitle());
die(json_encode($obj));

/*
_error_log(print_r($_POST, true));
_error_log(print_r($_FILES, true));
var_dump($_POST, $_FILES);
*/
