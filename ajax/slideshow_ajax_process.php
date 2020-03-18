<?php
if ($_POST['process'] == 'add_images') {
    $output = array();
    $isAllFileUploaded = true;
    if (!wp_verify_nonce($_POST['nonce'], "slideshow-nonce")) {
        $output = array("msg" => "Something went wrong!", 'status' => 'failed');
        wp_send_json($output);
        exit();
    }

    if (!is_dir(plugin_dir_path(dirname(__FILE__)) . UPLOAD_DIRECTORY_FOLDER)) {
        wp_mkdir_p(plugin_dir_path(dirname(__FILE__)) . UPLOAD_DIRECTORY_FOLDER);
    }

    $removedImages = $_POST['removed_files'] == "" ? array() : json_decode(stripslashes($_POST['removed_files']), true);

    add_filter('upload_dir', 'change_upload_dir');
    $file_uploaded_name_arr = array();
    foreach ($_FILES['file_name']['name'] as $key => $FILE) {
        if (in_array($FILE, $removedImages) || (substr($_FILES['file_name']['type'][$key], 0, strlen('video/')) === 'video/') == true) {
            continue;
        }

        if ($FILE != "") {
            $file = array(
                'name' => $_FILES['file_name']['name'][$key],
                'type' => $_FILES['file_name']['type'][$key],
                'tmp_name' => $_FILES['file_name']['tmp_name'][$key],
                'error' => $_FILES['file_name']['error'][$key],
                'size' => $_FILES['file_name']['size'][$key]
            );

            $isFileUploaded = wp_handle_upload($file, array('test_form' => false));

            if (array_key_exists('error', $isFileUploaded)) {
                $isAllFileUploaded = false;
            } else {
                $file_uploaded_name_arr[] = (basename($isFileUploaded['url']));
            }
        }
    }

    if (isset($_FILES['file_name']['name'])) {
        if (get_option('slide_show_images')) {
            $get_sld_show_arr = json_decode(get_option('slide_show_images'));
            $get_sld_show_arr = array_merge($get_sld_show_arr, $file_uploaded_name_arr);
            update_option('slide_show_images', json_encode($get_sld_show_arr));
        } else {
            add_option('slide_show_images', json_encode($file_uploaded_name_arr));
        }
    }
    $output = array("msg" => "Images Added Successfully.", 'status' => 'success');
    if ($isAllFileUploaded == false) {
        $output['msg'] = 'All Images Added except some because of image extension are not supported.';
    }
    wp_send_json($output);
}

if ($_POST['process'] == 'sort_images') {
    $isKeyUpdated = update_option('slide_show_images', json_encode($_POST['imgSortArr']));

    if ($isKeyUpdated) {
        $output = array('msg' => 'Images Updated Successfully', 'status' => 'success');
    } else {
        $output = array('msg' => 'Something Went Wrong', 'status' => 'failed');
    }
    wp_send_json($output);
}

if ($_POST['process'] == 'delete_image') {
    $isKeyUpdated = true;
    $get_sld_show_arr = json_decode(get_option('slide_show_images'));
    if ($_POST['img_name'] != "") {
        $key = array_search($_POST['img_name'], $get_sld_show_arr);
        if ($key !== false) {
            unset($get_sld_show_arr[$key]);
        }
        unlink(plugin_dir_path(dirname(__FILE__)) . UPLOAD_DIRECTORY_FOLDER . '/' . $_POST['img_name']);
        $get_sld_show_arr = array_values($get_sld_show_arr);
        $isKeyUpdated = update_option('slide_show_images', json_encode($get_sld_show_arr));
    }
    if ($isKeyUpdated) {
        $output = array('msg' => 'Image Deleted Successfully', 'status' => 'success');
    } else {
        $output = array('msg' => 'Something Went Wrong', 'status' => 'failed');
    }
    wp_send_json($output);
}
wp_die();
