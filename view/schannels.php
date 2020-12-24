<?php
global $global, $config;
if (!isset($global['systemRootPath'])) {
    require_once '../videos/configuration.php';
}
require_once $global['systemRootPath'] . 'objects/user.php';
require_once $global['systemRootPath'] . 'objects/Channel.php';
require_once $global['systemRootPath'] . 'objects/subscribe.php';
require_once $global['systemRootPath'] . 'objects/video.php';

if (isset($_SESSION['channelName'])) {
    _session_start();
    unset($_SESSION['channelName']);
}

$totalChannels = Channel::getTotalChannels();

if (!empty($_GET['page'])) {
    $_POST['current'] = intval($_GET['page']);
} else {
    $_POST['current'] = 1;
}

$users_id_array = VideoStatistic::getUsersIDFromChannelsWithMoreViews();

$current = $_POST['current'];
$_POST['rowCount'] = 10;
$channels = Channel::getChannels(true, "u.id, '". implode(",", $users_id_array)."'");

$totalPages = ceil($totalChannels / $_POST['rowCount']);
$metaDescription = __("Channels");
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
    <head>
        <title><?php echo $config->getWebSiteTitle(); ?> :: <?php echo __("Channels").getSEOComplement(); ?></title>
        <?php
        include $global['systemRootPath'] . 'view/include/head.php';
        ?>
        <style>
            #custom-search-input{
                padding: 3px;
                border: solid 1px #E4E4E4;
                border-radius: 6px;
                background-color: #fff;
            }

            #custom-search-input input{
                border: 0;
                box-shadow: none;
            }

            #custom-search-input button{
                margin: 2px 0 0 0;
                background: none;
                box-shadow: none;
                border: 0;
                color: #666666;
                padding: 0 8px 0 10px;
                border-left: solid 1px #ccc;
            }

            #custom-search-input button:hover{
                border: 0;
                box-shadow: none;
                border-left: solid 1px #ccc;
            }

            #custom-search-input .glyphicon-search{
                font-size: 23px;
            }
            .btn-channels { 
                text-shadow: 0 1px 0 #fff;
                  background-image: -webkit-linear-gradient(top, #fff 0%, #e0e0e0 100%);
                  background-image:      -o-linear-gradient(top, #fff 0%, #e0e0e0 100%);
                  background-image: -webkit-gradient(linear, left top, left bottom, from(#fff), to(#e0e0e0));
                  background-image:         linear-gradient(to bottom, #fff 0%, #e0e0e0 100%);
                  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff', endColorstr='#ffe0e0e0', GradientType=0);
                  filter: progid:DXImageTransform.Microsoft.gradient(enabled = false);
                  background-repeat: repeat-x;
                  border-color: #dbdbdb;
                  border-color: #ccc; 
                  background: #474949;
                  color: #fff;
            } 
            .btn-channels:hover{ 
                color:#fff;
                background-color: #337ab7; 
            }
        </style>
    </head>

    <body class="<?php echo $global['bodyClass']; ?> youtube">
        <?php
        include $global['systemRootPath'] . 'view/include/navbar.php';
        ?>

        <div class="container-fluid">
            <div class="panel" >
                <div class="panel-heading">
                    <form id="search-form" name="search-form" action="<?php echo $global['webSiteRootURL']; ?>schannels" method="get">
                        <div id="custom-search-input">
                            <div class="input-group col-md-12">
                                <input type="search" name="searchPhrase" class="form-control input-lg" placeholder="<?php echo$t['Search Channels']; ?>" value="<?php echo @$_GET['searchPhrase']; unsetSearch(); ?>" />
                                <span class="input-group-btn">
                                    <button class="btn btn-info btn-lg" type="submit">
                                        <i class="glyphicon glyphicon-search"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="panel-body" >
                    <ul class="pages">
                    </ul>

                    <?php
                    foreach ($channels as $value) {
                        $get = array('channelName' => $value['channelName']);
                        ?>
                        <div style="margin: 10px 30px; display: inline-grid; justify-items: center">
                            <div>
                                <a href="<?php echo User::getChannelLink($value['id']); ?>" >
                                    <img src="<?php echo User::getPhoto($value['id']); // tempcode 17.channels browser ?>"
                                     class="img img-thumbnail img-responsive pull-left" style="border-radius: 100%; max-height: 120px; margin: 0 10px;" alt="User Photo" />
                                 </a>
                            </div>
                            <div>
                                <span style="font-weight: 700; font-size: 18px;margin-right: 10px;"><?php echo $value['channelName']; ?></span> 
                            </div>
                            <div>
                                <span class=" ">
                                    <?php echo Subscribe::getButton($value['id']); ?>
                                </span> 
                            </div> 
                        </div>
                        <?php
                    } 
                    
                    // echo getPagination($totalPages, $current, "{$global['webSiteRootURL']}channels?page={page}");
                               
                    ?>
                </div>
            </div>
        </div>

        <?php
        include $global['systemRootPath'] . 'view/include/footer.php';
        ?>
    </body>
</html>
