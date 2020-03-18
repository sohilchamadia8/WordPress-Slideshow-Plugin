<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}
/* delete data when plugin will be deleted */
delete_option("slide_show_images");
