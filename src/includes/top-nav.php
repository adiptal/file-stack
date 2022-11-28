<div class="navbar">
    <h1 class="m-auto m-md-0">FileStack.io</h1>
    <button onclick="toggleSidebar()" class="btn btn-link btn-bar"><i class="fas fa-bars"></i></button>
<?php
if( isset( $_SESSION['user_id'] ) )
{
?>
    <div class="nav-drop mr-md-5">
        <button id="addData" onclick="toggleDropDown($(this).position().left , '#file-option')" class="btn btn-link"><i class="fas fa-plus-circle"></i></button>
        <div class="drop-down" id="file-option">
            <ul class="list-unstyled">
                <li class="list-item">
                    <a id="create_file_internal_call" data-toggle="modal" data-target="#createFolder">Create Folder</a>
                </li>
                <li class="list-item">
                    <input type="file" name="user_file[]" id="user_file" multiple="multiple" hidden>
                    <a id="upload_file_internal_call">Add Files</a>
                </li>
                <li class="list-item">
                    <input type="file" name="user_folder[]" id="user_folder" multiple="multiple" directory="" webkitdirectory="" hidden>
                    <a id="upload_folder_internal_call">Add Folder</a>
                </li>
            </ul>
        </div>
        <button onclick="toggleDropDown($(this).position().left , '#user-option')" class="btn btn-link"><i class="far fa-user"></i></button>
        <div class="drop-down" id="user-option">
            <ul class="list-unstyled">
                <li class="list-item">Profile</li>
                <li class="list-item" id="signout">Signout</li>
            </ul>
        </div>
    </div>
<?php
}
else
{
?>
    <ul class="social-links list-inline d-none d-md-block">
        <li class="list-inline-item no-shadow"><input type="text" name="" id="" placeholder="Search"></li>
        <li class="list-inline-item"><i class="fab fa-facebook-square"></i></li>
        <li class="list-inline-item"><i class="fab fa-instagram"></i></li>
        <li class="list-inline-item"><i class="fab fa-github-square"></i></li>
        <li class="list-inline-item"><i class="fab fa-linkedin"></i></li>
    </ul>
<?php
}
?>
</div>

<!-- CREATE FOLDER MODAL -->
<div class="modal fade" data-keyboard="false" data-backdrop="static" id="createFolder" tabindex="-1" role="dialog" aria-labelledby="createFolderLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content rounded-0 bg-custom text-white border-0">
            <div class="modal-header">
                <h5 class="modal-title mt-1" id="createFolderLabel">Create Folder</h5>
                <button type="button" class="btn btn-link text-white" onclick="$('#createFolder').modal('hide')" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="text-center bg-white text-dark">
                <div class="row m-5">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="create_folder">Folder Name</label>
                            <input class="form-control" type="text" name="create_folder" id="create_folder">
                            <p><small id="notification"></small></p>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn btn-dark" id="create_folder_submit">Create</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END CREATE FOLDER MODAL -->