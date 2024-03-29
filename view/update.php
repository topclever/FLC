<?php
global $global, $config;
if (!isset($global['systemRootPath'])) {
    require_once '../videos/configuration.php';
}
session_write_close();
require_once $global['systemRootPath'] . 'objects/user.php';
//check if there is a update
if (!User::isAdmin()) {
    header("location: {$global['webSiteRootURL']}user");
    exit;
}
// remove cache dir before the script starts to let the script recreate the javascript and css files
if (!empty($_POST['updateFile'])) {
    $dir = "{$global['systemRootPath']}videos/cache";
    rrmdir($dir);
}
$version = json_decode(url_get_contents("https://tutorials.avideo.com/version"));
?>
<!DOCTYPE html>
<html lang="<?php echo $config->getLanguage(); ?>">
    <head>
        <title><?php echo $config->getWebSiteTitle(); ?></title>
        <?php
        include $global['systemRootPath'] . 'view/include/head.php';
        ?>
    </head>
    <body class="<?php echo $global['bodyClass']; ?> youtube">
        <?php
        include $global['systemRootPath'] . 'view/include/navbar.php';
        ?><div class="container-fluid">
            <div class="alert alert-success"><?php printf(__("You are running AVideo version %s!"), $config->getVersion()); ?></div>
            <?php
            if (empty($_POST['updateFile'])) {
                $updateFiles = getUpdatesFilesArray();
                if (!empty($updateFiles)) {
                    ?>
                    <div class="alert alert-warning">
                        <form method="post" class="form-compact well form-horizontal" >
                            <fieldset>
                                <legend><?php echo __("Update AVideo System"); ?></legend>
                                <label for="updateFile" class="sr-only"><?php echo __("Select the update"); ?></label>
                                <select class="selectpicker" data-width="fit" name="updateFile" id="updateFile" required autofocus>
                                    <?php
                                    foreach ($updateFiles as $value) {
                                        echo "<option value=\"{$value['filename']}\">Version {$value['version']}</option>";
                                    }
                                    ?>
                                </select>
                                <?php printf(__("We detected a total of %s pending updates, if you want to do it now click (Update Now) button"), "<strong class='badge'>" . count($updateFiles) . "</strong>"); ?>
                                <hr>
                                <button type="submit" class="btn btn-warning btn-lg center-block " href="?update=1" > <span class="glyphicon glyphicon-refresh"></span> <?php echo __("Update Now"); ?> </button>
                            </fieldset>
                        </form>
                    </div>

                    <script>
                        $(document).ready(function () {
                            $('#updateFile').selectpicker();
                        });
                    </script>
                    <?php
                } else
                if (!empty($version) && version_compare($config->getVersion(), $version->version) === -1) {
                    ?>
                    <div class="alert alert-warning">
                        Our repository is now running at version <?php echo $version->version; ?>. 
                        You can follow this <a target="_blank" href="https://github.com/WWBN/AVideo/wiki/How-to-Update-your-AVideo-Platform" class="btn btn-warning btn-xs" rel="noopener noreferrer">Update Tutorial</a> 
                        to update your files and get the latest version.
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="alert alert-success">
                        <h2><?php echo __("Your system is up to date"); ?></h2>
                    </div>
                    <?php
                }
            } else {
                $obj = new stdClass();
                $templine = '';
                $logfile = "{$global['systemRootPath']}videos/avideo.";
                if (file_exists($logfile . "log")) {
                    unlink($logfile . "log");
                    _error_log("avideo.log deleted by update");
                }
                if (file_exists($logfile . "js.log")) {
                    unlink($logfile . "js.log");
                    _error_log("avideo.js.log deleted by update");
                }
                $lines = file("{$global['systemRootPath']}updatedb/{$_POST['updateFile']}");
                $obj->error = "";
                foreach ($lines as $line) {
                    if (substr($line, 0, 2) == '--' || $line == '')
                        continue;
                    $templine .= $line;
                    if (substr(trim($line), -1, 1) == ';') {
                        if (!$global['mysqli']->query($templine)) {
                            $obj->error = ('Error performing query \'<strong>' . $templine . '\': ' . $global['mysqli']->error . '<br /><br />');
                            echo json_encode($obj);
                            //exit;
                        }
                        $templine = '';
                    }
                }

                // insert configuration if is version 1.0
                if ($config->currentVersionLowerThen('1.0')) {
                    $sql = "DELETE FROM configurations WHERE id = 1 ";
                    if ($global['mysqli']->query($sql) !== TRUE) {
                        $obj->error = "Error deleting configuration: " . $global['mysqli']->error;
                        echo json_encode($obj);
                        exit;
                    }

                    $sql = "INSERT INTO configurations (id, video_resolution, users_id, version,  created, modified) VALUES (1, '426:240', " . User::getId() . ",'1.0', now(), now())";
                    if ($global['mysqli']->query($sql) !== TRUE) {
                        $obj->error = "Error creating configuration: " . $global['mysqli']->error;
                        echo json_encode($obj);
                        exit;
                    }
                }

                if ($config->currentVersionEqual('1.0')) {
                    $sql = "UPDATE configurations SET  users_id = " . User::getId() . ", version = '1.1', webSiteTitle = '{$global['webSiteTitle']}', language = '{$global['language']}', contactEmail = '{$global['contactEmail']}', modified = now() WHERE id = 1";
                    if ($global['mysqli']->query($sql) !== TRUE) {
                        $obj->error = "Error creating configuration: " . $global['mysqli']->error;
                        echo json_encode($obj);
                        exit;
                    }
                }

                //$renamed = rename("{$global['systemRootPath']}updateDb.sql", "{$global['systemRootPath']}updateDb.sql.old");
                ?>
                <div class="alert alert-success">
                    <?php
                    printf(__("Your update from file %s is done, click continue"), $_POST['updateFile']);
                    ?><hr>
                    <a class="btn btn-success" href="?done=1" > <span class="glyphicon glyphicon-ok"></span> <?php echo __("Continue"); ?> </a>
                </div>
                <?php
            }
            ?></div><?php
        include $global['systemRootPath'] . 'view/include/footer.php';
        ?>
    </body>
</html>
<?php
exit;
?>
