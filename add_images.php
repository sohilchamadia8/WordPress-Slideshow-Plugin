<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3>Add Images</h3>
        </div>
    </div>
</div>
<div class="container cstm_container">
    <div class="row">
        <form method="post" enctype="multipart/form-data" name="frmslideshow" id="frmslideshow">
            <div class="col-md-1 margin-bottom10">
                <label for="file_name">
                    <i class="fa fa-camera fa-4x file_icon" aria-hidden="true"></i>
                    <span class="cont_file_icon">Select File</span>
                    <input type="file" accept="image/*" name="file_name[]" id="file_name" class="inputfile" multiple/>
                </label>
                <input type="hidden" id="removed_files" name="removed_files" value=""/>
            </div>
            <div class="col-md-1">
                <input type="submit" name="btnsubmit" class="btn btn-primary" value="Upload"/>
            </div>
            <div class="col-md-1 offset-md-9">
                <div class="cstm_loader">
                    <i class="fa fa-refresh fa-spin fa-2x fa-fw"></i>
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </form>
    </div>
    <div class="col"><small><b><i>Note : Please select only images.</i></b></small></div>
</div>
<div class="container" id="image_preview">
</div>