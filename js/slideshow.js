/*globals jQuery:false*/
/* jshint node: true */

"use strict";
var lastClickedImgBtn = "";
var fileIsImage = true;
var noOfFileIsNotImage = 0;
var noOfFileRemoved = 0;
window.newFileList = [];

jQuery(document).ready(function () {

    deleteConfirmation();
    sortImageOrder();

    jQuery(document).on('click', '.btnRemove', function () {

        var removeElement = jQuery(this);
        var id = removeElement.val();
        removeElement.closest('.appendedImg').remove();
        var input = document.getElementById('file_name');
        var files = input.files;
        if (files.length) {
            if (typeof files[id] !== 'undefined') {
                window.newFileList.push(files[id].name);
            }
        }
        noOfFileRemoved++;
        document.getElementById('removed_files').value = JSON.stringify(window.newFileList);
    });

    jQuery(document).on('change', '#file_name', function (event) {

        jQuery('#image_preview').empty();
        fileIsImage = true;
        document.getElementById('removed_files').value = "";
        noOfFileRemoved = noOfFileIsNotImage = 0;
        var totalFile = document.getElementById("file_name").files.length;
        window.newFileList = [];

        var columnSetCount = 1;
        var rowClssCounter = 1;
        for (var i = 0; i < totalFile; i++) {
            var imgType = this.files[i].type;
            if (imgType.startsWith('image/') === false) {
                fileIsImage = false;
                noOfFileIsNotImage++;
                continue;
            }
            if (columnSetCount === 1) {
                jQuery('#image_preview').append('<div class="row cstm_img_row gallery_row-' + rowClssCounter + '">');
            }

            jQuery('.gallery_row-' + rowClssCounter).append("<div class='col-md-6 col-sm-2 appendedImg'><img src='" + URL.createObjectURL(event.target.files[i]) + "'><div class='btn-container'><button class='btn btn-block btn-danger btnRemove' value='" + i + "'>Remove</button></div></div>");
            columnSetCount++;

            if (columnSetCount > 4 || totalFile === i) {
                columnSetCount = 1;
                rowClssCounter++;
                jQuery('#image_preview').append('</div>');
            }

        }

        if (!fileIsImage && (noOfFileIsNotImage === totalFile)) {
            toastr.warning('Please select image only.');
            return false;
        }

        if (!fileIsImage) {
            toastr.warning('Some of the file was not image');
        }


    });

    toastr.options = {
        'closeButton': true,
        'debug': false,
        'newestOnTop': false,
        'progressBar': false,
        'positionClass': 'toast-top-right',
        'preventDuplicates': false,
        'showDuration': '1000',
        'hideDuration': '1000',
        'timeOut': '5000',
        'extendedTimeOut': '1000',
        'showEasing': 'swing',
        'hideEasing': 'linear',
        'showMethod': 'fadeIn',
        'hideMethod': 'fadeOut'
    };


    jQuery("#gallery_image").sortable();

    jQuery("#frmslideshow").submit(function (e) {
        e.preventDefault();
        var totalFile = document.getElementById("file_name").files.length;
        console.log(document.getElementById("file_name").files);
        if (totalFile === 0 || noOfFileRemoved === totalFile) {
            toastr.info('No Image Selected');
            return false;
        }

        if (!fileIsImage && noOfFileIsNotImage === totalFile) {
            toastr.warning('Please select image only.');
            return false;
        }


        var nonce = slideshow_ajax.check_nonce;
        var formData = new FormData(this);
        formData.append('action', 'slideshow_ajax');
        formData.append('nonce', nonce);
        formData.append('process', 'add_images');
        jQuery.ajax({
            type: "post",
            processData: false,
            contentType: false,
            url: slideshow_ajax.ajax_url,
            data: formData,
            beforeSend: function () {
                jQuery('.cstm_loader').show();
                jQuery('input[name="btnsubmit"]').attr('disabled', 'disabled');
                jQuery('.btnRemove').attr('disabled', 'disabled');
                jQuery('#file_name').attr('disabled', 'disabled');

            },
            success: function (response) {

                if (noOfFileIsNotImage === totalFile) {
                    return false;
                }

                if (response.status === 'success') {
                    toastr.success(response.msg);
                } else {
                    toastr.error(response.msg);
                }

                jQuery('#image_preview').empty();
            },
            error: function (xhr) {
                toastr.error('Something went wrong.');
            },
            complete: function () {
                jQuery('.cstm_loader').hide();
                jQuery('input[name="btnsubmit"]').attr('disabled', false);
                jQuery("#file_name").val('');
                jQuery('#file_name').attr('disabled', false);

            }
        });

    })

    jQuery(document).on('click', '.cstm_del_img', function () {
        lastClickedImgBtn = jQuery(this).siblings('img').data("src");
    });


});


function sortImageOrder() {

    jQuery("#gallery_image").sortable({
        opacity: 0.5,
        animation: 150,
        stop: function (event, ui) {
            var imgSortArr = [];
            jQuery('.img-thumbnail').each(function (i) {
                imgSortArr.push(jQuery(this).attr('data-src')); // updates the data object
            });

            jQuery.ajax({
                type: "post",
                url: slideshow_ajax.ajax_url,
                data: {'action': 'slideshow_ajax', 'imgSortArr': imgSortArr, 'process': 'sort_images'},
                success: function (response) {
                    if (response.status === 'success') {
                        toastr.success(response.msg);
                    }
                },
                error: function (xhr) {
                    console.log(xhr.status);
                }
            });

        }
    });

}

function deleteConfirmation() {
    jQuery(document).find('.cstm_del_img').confirmation({
        onConfirm: function (event, element) {
            console.log(lastClickedImgBtn);
            jQuery(element).siblings('.cstm_spin_loader').css('visibility', 'unset');
            jQuery(element).siblings('img').css("opacity", "0.2");
            jQuery.ajax({
                type: "post",
                url: slideshow_ajax.ajax_url,
                data: {'img_name': lastClickedImgBtn, 'action': 'slideshow_ajax', 'process': 'delete_image'},
                success: function (response) {
                    jQuery(".cstm_img_row").load(window.location.href + " .cstm_img_row");

                    if (response.status === 'success') {
                        toastr.success(response.msg);
                    } else {
                        toastr.error(response.msg);
                    }

                },
                error: function (xhr) {
                    console.log(xhr.status);
                },
                complete: function () {
                    setTimeout(function () {
                        deleteConfirmation();
                        sortImageOrder();
                    }, 500);

                }
            });
        }

    });
}