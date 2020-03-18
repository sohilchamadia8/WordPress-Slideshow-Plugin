<div class="container cstm_gallery_main">
    <h2>Image Gallery</h2>
    <div class="row cstm_short_cont">
        <span>Note : Use this code <code>[myslideshow]</code> for slider</span>
    </div>
    <div class="row cstm_img_row">
        <?php if (get_option('slide_show_images')) {
            $gallery_img = json_decode(get_option('slide_show_images'));
            if ($gallery_img) {
                ?>
                <div id="gallery_image">
                    <?php
                    foreach ($gallery_img as $img_key => $value) {
                        ?>
                        <div class="col-md-3 col-sm-2">
                            <div class="thumbnail1">
                                <button type="button" class="close cstm_del_img" data-toggle="popover"
                                        aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <img class="img-thumbnail" data-src="<?= $value ?>"
                                     src="<?= plugin_dir_url(__FILE__) . UPLOAD_DIRECTORY_FOLDER . '/' . $value ?>"
                                     alt="Gallery Images">
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            } else {
                ?>
                <div class="col-md-4">
                    <div class="thumbnail">
                        <div class="caption">
                            <p>No Images Available</p>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else { ?>
            <div class="col-md-4">
                <div class="thumbnail">
                    <div class="caption">
                        <p>No Images Available</p>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>