<?php

/**
 * Plugin Name: Wordpress Slideshow
 * Description: This plugin is about gallery slideshow
 * Author: Sohil B. Chamadia
 * Author URI: https://sohilchamadia8.wordpress.com/
 */

define('UPLOAD_DIRECTORY_FOLDER', 'uploads');

/* Enqueue all required files at admin side */
function slideshow_enqueue_script($hook_suffix)
{
    $pages_slug = array('gallery', 'add-gallery');

    if (in_array($_GET['page'], $pages_slug)) {
        wp_enqueue_script('slideshow_script', plugin_dir_url(__FILE__) . 'js/slideshow.js');
        wp_localize_script('slideshow_script', 'slideshow_ajax', array('ajax_url' => admin_url('admin-ajax.php'), 'check_nonce' => wp_create_nonce('slideshow-nonce')));
        wp_enqueue_style('font-awesome-min', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
        wp_enqueue_style('bootstrap-min', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css');
        wp_enqueue_style('slideshow-style', plugin_dir_url(__FILE__) . 'css/style.css');
        wp_enqueue_script('jquery-min', plugin_dir_url(__FILE__) . 'js/jquery.min.js');
        wp_enqueue_script('bootstrap-min-js', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js');
        wp_enqueue_script('bootstrap-js', plugin_dir_url(__FILE__) . 'js/bootstrap.js');
        wp_enqueue_script('bootstrap-confirmation-min', plugin_dir_url(__FILE__) . 'js/bootstrap-confirmation.min.js');
        wp_enqueue_script('sortable-gallery-images', plugin_dir_url(__FILE__) . 'js/jquery-ui.js');
        wp_enqueue_style('toastr-css', plugin_dir_url(__FILE__) . 'css/toastr.min.css');
        wp_enqueue_script('toastr-css', plugin_dir_url(__FILE__) . 'js/toastr.min.js');
    }
}

add_action('admin_enqueue_scripts', 'slideshow_enqueue_script');

/* Enqueue all required files for slider */

function slider_enqueue_script()
{
    wp_enqueue_style('slider-style', plugin_dir_url(__FILE__) . 'css/slider_style.css');
    wp_enqueue_script('jquery-min-ajax', plugin_dir_url(__FILE__) . 'js/jquery.min.js');
}

add_action('wp_enqueue_scripts', 'slider_enqueue_script');

add_action("wp_ajax_slideshow_ajax", "slideshow_ajax");

/* all ajax data processs in below methods*/
function slideshow_ajax()
{
    include_once 'ajax/slideshow_ajax_process.php';
}


add_action('admin_menu', 'custom_menu');

/* menu & sub-menu create at admin side */
function custom_menu()
{

    add_menu_page(
        'Gallery',
        'Gallery',
        'edit_posts',
        'gallery',
        'callback_slideshow',
        'dashicons-format-gallery'
    );

    add_submenu_page('gallery', 'Add Gallery', 'Add', 'edit_posts', 'add-gallery', 'callback_add_image');
}

function callback_slideshow()
{
    include 'image_list.php';
}


function callback_add_image()
{
    include 'add_images.php';
}

/* modify name of file which is going to upload */
function modify_filename($filename)
{
    $info = pathinfo($filename);
    $ext = empty($info['extension']) ? '' : '.' . $info['extension'];
    $name = basename($filename, $ext);

    return md5($name) . $ext;
}

add_filter('sanitize_file_name', 'modify_filename', 10);

/* change path of file to be uploaded */
function change_upload_dir($pathdata)
{
    $pathdata['path'] = plugin_dir_path(__FILE__) . UPLOAD_DIRECTORY_FOLDER;
    $pathdata['url'] = plugin_dir_url(__FILE__) . UPLOAD_DIRECTORY_FOLDER;
    $pathdata['subdir'] = '/' . UPLOAD_DIRECTORY_FOLDER;
    return $pathdata;
}


add_shortcode('myslideshow', 'callback_get_slideshow_images');
/* method to show slider */
function callback_get_slideshow_images()
{
    ob_start();
    require 'slideshow_gallery_images.php';
    return ob_get_clean();
}
