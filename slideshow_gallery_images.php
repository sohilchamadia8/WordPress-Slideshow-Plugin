<link rel="stylesheet" href="<?= plugin_dir_url(__FILE__) . 'css/hVCarousel.css' ?>">
<script src="<?= plugin_dir_url(__FILE__) . 'js/hVCarousel.min.js' ?>"></script>
<div class="container">
    <div class="row mb-5">
        <div class="col-12">
            <?php if (get_option('slide_show_images')) {
                $gallery_img = json_decode(get_option('slide_show_images'));
                if ($gallery_img) {
                    ?>
                    <div id="myCrousel" class="hVCarousel">
                        <div class="hVCarousel-inner">
                            <div class="hVCarousel-slides">
                                <?php
                                foreach ($gallery_img as $img_key => $value) {
                                    ?>
                                    <figure class="hVCarousel-item">
                                        <img src="<?= plugin_dir_url(__FILE__).UPLOAD_DIRECTORY_FOLDER.'/'. $value ?>"
                                             alt=""/>
                                    </figure>

                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } else {
                    ?>
                    <div class="alert alert-info" role="alert">
                        No Images Avaialable!
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="alert alert-info" role="alert">
                    No Images Avaialable!
                </div>
                <?php
            } ?>
        </div>
    </div>
</div>
<script>
    jQuery(function () {
        jQuery('#myCrousel').hVCarousel({
            arrow: false,
            dots: true,
            autoplay: true,
            fade:true,
        });
    });
</script>