<?php
global $advancedCustom;
$uid = uniqid();
$landscape = "rowPortrait";
$css = "";
if (!empty($obj->landscapePosters)) {
    $landscape = "landscapeTile";
    if (!empty($obj->titleLabel)) {
        $css = "height: 185px;";
    }
}
$get = $_GET;
$post = $_POST;
$timeLog3 = __FILE__ . " - modeFlix Row";
TimeLogStart($timeLog3);
?>
<div class="carousel <?php echo $landscape; ?>" data-flickity='<?php echo json_encode($dataFlickirty) ?>' style="<?php echo $css; ?>">
    <?php
    TimeLogEnd($timeLog3, __LINE__);
    foreach ($videos as $value) {
        TimeLogStart($timeLog3 . " Video {$value['clean_title']}");
        $images = Video::getImageFromFilename($value['filename'], $value['type']);
        TimeLogEnd($timeLog3 . " Video {$value['clean_title']}", __LINE__);
        $imgGif = $images->thumbsGif;
        $img = $images->thumbsJpg;
        $poster = $images->poster;
        $cssClass = "";
        if (empty($obj->landscapePosters) && !empty($images->posterPortraitThumbs)) {
            $imgGif = $images->gifPortrait;
            $img = $images->posterPortraitThumbs;
            $cssClass = "posterPortrait";
        }
        ?>
        <div class="carousel-cell  "  itemscope itemtype="http://schema.org/VideoObject">
            <div class="tile">
                <div class="slide thumbsImage" crc="<?php echo $value['id'] . $uid; ?>" videos_id="<?php echo $value['id']; ?>" poster="<?php echo $poster; ?>" href="<?php echo Video::getLink($value['id'], $value['clean_title']); ?>"  video="<?php echo $value['clean_title']; ?>" iframe="<?php echo $global['webSiteRootURL']; ?>videoEmbeded/<?php echo $value['clean_title']; ?>">
                    <div class="tile__media ">
                        <img alt="<?php echo $value['title']; ?>" src="<?php echo $global['webSiteRootURL']; ?>view/img/placeholder-image.png" class="tile__img <?php echo $cssClass; ?> thumbsJPG img img-responsive carousel-cell-image" data-flickity-lazyload="<?php echo $img; ?>" />
                        <?php if (!empty($imgGif)) { ?>
                            <img style="position: absolute; top: 0; display: none;" src="<?php echo $global['webSiteRootURL']; ?>view/img/placeholder-image.png"  alt="<?php echo $value['title']; ?>" id="tile__img thumbsGIF<?php echo $value['id']; ?>" class="thumbsGIF img-responsive img carousel-cell-image" data-flickity-lazyload="<?php echo $imgGif; ?>" />
                        <?php } ?>
                        <?php
                        if ($advancedCustom->paidOnlyShowLabels && $obj->paidOnlyLabelOverPoster) {
                            foreach ($value['tags'] as $value2) {
                                if (!empty($value2->label) && $value2->label === __("Paid Content")) {
                                    ?><span class="paidOnlyLabel label label-<?php echo $value2->type; ?>"><?php echo $value2->text; ?></span><?php
                                }
                            }
                        }
                        if (!empty($obj->titleLabel)) {
                            ?>  
                            <h4 style="<?php if (!empty($obj->titleLabelOverPoster)) { ?>margin-top: -27px;<?php } echo $obj->titleLabelCSS; ?> "><?php echo $value['title']; ?></h4>
                            <?php
                        }
                        ?>
                        <div class="progress" style="height: 3px; margin-bottom: 2px;">
                            <div class="progress-bar progress-bar-danger" role="progressbar" style="width: <?php echo $value['progress']['percent'] ?>%;" aria-valuenow="<?php echo $value['progress']['percent'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <?php
                        if ($advancedCustom->paidOnlyShowLabels && !$obj->paidOnlyLabelOverPoster) {
                            foreach ($value['tags'] as $value2) {
                                if (!empty($value2->label) && $value2->label === __("Paid Content")) {
                                    ?><div class="label label-<?php echo $value2->type; ?>" style="margin: 0; margin-top: -2px;  width: 100%; display: block; border-top-left-radius: 0; border-top-right-radius: 0; "><?php echo $value2->text; ?></div><?php
                                }
                            }
                        }
                        ?>  
                    </div>
                </div>
                <div class="arrow-down" style="display: none;"></div>
            </div>
            <?php
            TimeLogEnd($timeLog3 . " Video {$value['clean_title']}", __LINE__);
            getLdJson($value['id']);
            TimeLogEnd($timeLog3 . " Video {$value['clean_title']}", __LINE__);
            getItemprop($value['id']);
            TimeLogEnd($timeLog3 . " Video {$value['clean_title']}", __LINE__);
            ?>
        </div>        
        <?php
        TimeLogEnd($timeLog3 . " Video {$value['clean_title']}", __LINE__);
    }
    TimeLogEnd($timeLog3, __LINE__);
    ?>
</div>

<?php
TimeLogEnd($timeLog3, __LINE__);
foreach ($videos as $value) {
    $images = Video::getImageFromFilename($value['filename'], $value['type']);
    $imgGif = $images->thumbsGif;
    $img = $images->thumbsJpg;
    $poster = $images->poster;
    $canWatchPlayButton = "";
    if (User::canWatchVideoWithAds($value['id'])) {
        $canWatchPlayButton = "canWatchPlayButton";
    }
    ?>
    <!-- <div class="poster" id="poster<?php echo $value['id'] . $uid; ?>" poster="<?php echo $poster; ?>" style="display: none; background-image: url(<?php echo $global['webSiteRootURL']; ?>plugin/YouPHPFlix2/view/img/loading.gif);">
         
    </div>   -->   
    <?php
}

TimeLogEnd($timeLog3, __LINE__);
$_GET = $get;
$_POST = $post;
