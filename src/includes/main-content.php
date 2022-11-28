<div class="container-box">
    <div class="container-fluid">

        <div class="nav-breadcrumbs">
            <ul class="list-inline col-sm-7 col-md-8 col-lg-9 float-left" id="breadcrumb_links">
            </ul>
            <div class="float-right mb-2 mb-sm-0">
                <div class="col-12">
                    <input type="checkbox" id="check_all" hidden>
                    <button class="ml-3" id="check_all_action">Select All</button>
                    <button class="ml-3" onclick="share_selected()"><i class="fas fa-share-square"></i></button>
                    <button class="ml-3" onclick="download_selected()"><i class="fas fa-download"></i></button>
                    <button class="ml-3" onclick="delete_selected()"><i class="fas fa-trash-alt"></i></button>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        
        <div class="col-12 m-2">Folders</div>
        <div class="row" id="folder">
        </div>
        
        <div class="col-12 m-2">Files</div>
        <div class="row" id="file">
        </div>
    </div>
</div>

<!-- DRIVE STATUS -->
<div class="col-12 col-sm-5 d-none" id="upload_notification">
    <div class="card">
        <div class="card-header">
            <div class="float-right">
            </div>
            <h4 id="status_title">Uploading</h4>
        </div>
        <div class="card-body">
            <div class="row" id="uploading_status"></div>
        </div>
    </div>
</div>
<!-- END DRIVE STATUS -->

<!-- FILE INFORMATION MODAL -->
<div class="modal fade" data-keyboard="false" data-backdrop="static" id="fileInformation" tabindex="-1" role="dialog" aria-labelledby="fileInfoLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content rounded-0 bg-custom text-white border-0">
            <div class="modal-header">
                <h5 class="modal-title mt-1" id="fileInfoLabel">File</h5>
                <button type="button" class="btn btn-link text-white" onclick="$('#fileInformation').modal('hide')" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="bg-white text-primary">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-md-10 offset-md-1">
                            <table class="table borderless border-bottom">
                                <tbody class="text-justify">
                                <tr>
                                    <td><strong>File Name:</strong></td>
                                    <td id="user_file_name">Loading ...</td>
                                </tr>
                                <tr>
                                    <td><strong>File Size:</strong></td>
                                    <td id="user_file_size">Loading ...</td>
                                </tr>
                                <tr>
                                    <td><strong>Created at:</strong></td>
                                    <td id="created_at">Loading ...</td>
                                </tr>
                                <tr>
                                    <td><strong>Updated at:</strong></td>
                                    <td id="updated_at">Loading ...</td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="col-12" id="file_action">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END FILE INFORMATION MODAL -->

<!-- SHARE FILE MODAL -->
<div class="modal fade" data-keyboard="false" data-backdrop="static" id="shareFile" tabindex="-1" role="dialog" aria-labelledby="shareFileLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content rounded-0 bg-custom text-white border-0">
            <div class="modal-header">
                <h5 class="modal-title mt-1" id="shareFileLabel">Share File</h5>
                <button type="button" class="btn btn-link text-white" onclick="$('#shareFile').modal('hide')" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="text-center bg-white text-dark">
                <div class="row m-2 mt-5 mb-5">
                    <div class="col-12">
                            <h5 id="share_user_file_name"></h5>
                            <hr>
                        <div class="form-group">
                            <label for="user_id_list">Select User Email</label>
                            <select class="js-example-basic-single form-control" name="user_id_list" id="user_id_list">
                            </select>
                            <p><small id="share_notification"></small></p>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn btn-dark" id="share_file_submit">Share</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END SHARE FILE MODAL -->

<script>
    $(function(){
        // user_id SELECT - SHARE FILE
        var user_id_list;

        getFolderListJSON();
        $('li').removeClass('active');
        $('li#my-drive').addClass('active');
        $('#addData').show();
    });

    // ----------------------------------------------------------------------------------------------------//
                                            // UI FUNCTIONALITY
    // ----------------------------------------------------------------------------------------------------//
    
    async function getFolderListJSON(current_directory = '' , reset = 0)
    {
        // ------------------------------------------------------------------------------------------ //
                                // FOLDERS LIST IN SPECIFIC DIRECTORY THROUGH AJAX
        // ------------------------------------------------------------------------------------------ //

        $.ajax({
            method: "POST",
            url: baseUrl + "/functions.php",
            data: 'getFolderListJSON=' + current_directory + '&reset=' + reset,
            dataType: "json"
        }).done(function(response) {
            $('#folder').html('');
            $('#file').html('');
            $('#breadcrumb_links').html(response['breadcrumps']);

            if (!jQuery.isEmptyObject(response['directory']))
            {
                for(var i=0 ; i<response['directory'].length ; i++)
                {
                    $('#folder').append(
                        '<div class="col-lg-2 col-md-4 col-6 mb-2 folder-items">'+
                        '<div class="card context-menu-one"  id="'+ response['directory'][i]['key'] +'" ondblclick="getFolderListJSON( '+ "'" + response['directory'][i]['key'] + "'" +' )">'+
                        '<div class="card-body">'+
                        '<div class="row">'+
                        '<input type="checkbox" name="selected_files[]" value="'+ response['directory'][i]['key'] +'" hidden>'+
                        '<div class="col-12 d-none d-md-block"><i class="far fa-folder fa-2x"></i></div>'+
                        '<div class="col-12">'+ response['directory'][i]['key'] +'</div>'+
                        '</div>'+
                        '</div>'+
                        '</div>'+
                        '</div>'
                    );
                }
            }

            if (!jQuery.isEmptyObject(response['file']))
            {
                for(var i=0 ; i<response['file'].length ; i++)
                {
                    $('#file').append(
                        '<div class="col-lg-2 col-md-4 col-6 mb-2 folder-items">'+
                        '<div class="card context-menu-two"  id="'+ response['file'][i]['key'] +'">'+
                        '<div class="card-body">'+
                        '<div class="row">'+
                        '<input type="checkbox" name="selected_files[]" value="'+ response['file'][i]['key'] +'" hidden>'+
                        '<div class="col-12 d-none d-md-block"><i class="far fa-file fa-2x"></i></div>'+
                        '<div class="col-12">'+ response['file'][i]['key'] +'</div>'+
                        '</div>'+
                        '</div>'+
                        '</div>'+
                        '</div>'
                    );
                }
            }

            $("#folder .card , #file .card").click(function(e){
                e.stopPropagation();
                if (!window.event.ctrlKey)
                {
                    $('input:checkbox').not(this).prop('checked', false);
                    $('.container-box .active').removeClass('active');
                }
                if( $(this).hasClass('active') )
                {
                    $(this).removeClass('active');
                    $('input[value="'+ $(this).attr('id') +'"]').prop('checked', false);
                }
                else
                {
                    $(this).addClass('active');
                    $('input[value="'+ $(this).attr('id') +'"]').prop('checked', true);
                }
            });
        });
        
        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

    async function openDirectory(directory_name = '')
    {
        // ------------------------------------------------------------------------------------------ //
                            // OPEN DIRECTORY FOR BREADCRUMB FUNCTIONALITY
        // ------------------------------------------------------------------------------------------ //

        getFolderListJSON(directory_name , 1);
        
        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

    function getFileInfo(user_file_name)
    {
        // ------------------------------------------------------------------------------------------ //
                                        // FILE INFORMATION THROUGH AJAX
        // ------------------------------------------------------------------------------------------ //
        
        $('#fileInformation').modal('toggle');

        $.ajax({
            method: "POST",
            url: baseUrl + "/functions.php",
            data: 'getFileInformationJSON=' + user_file_name ,
            dataType: "json"
        }).done(function(response) {
            $('#user_file_name').html(user_file_name);
            $('#user_file_size').html(response[0]);
            $('#created_at').html(response[1]);
            $('#updated_at').html(response[2]);
            $('#file_action').html(
                '<button type="button" class="btn btn-link" onclick="shareFile('+ "'" + user_file_name + "'" +')"><i class="far fa-share-square"></i></button>'+
                '<button type="button" class="btn btn-link" onclick="downloadFile('+ "'" + user_file_name + "'" +')"><i class="fas fa-download"></i></button>'+
                '<button type="button" class="btn btn-link" onclick="deleteFile('+ "'" + user_file_name + "'" +')"><i class="far fa-trash-alt"></i></button>'
            );
        });
        
        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

    // ----------------------------------------------------------------------------------------------------//
                                        // FILE RELATED FUNCTIONALITY
    // ----------------------------------------------------------------------------------------------------//

    function downloadFile(user_file_name)
    {
        // ------------------------------------------------------------------------------------------ //
                                            // DOWNLOAD FILE
        // ------------------------------------------------------------------------------------------ //

        $('#status_title').html('Downloading');
        $('#upload_notification').removeClass("d-none").fadeIn();
        $('#uploading_status').html('');
        
        if( $.isArray(user_file_name) )
        {
            for( var i=0 ; i<user_file_name.length ; i++ )
            {
                $('#uploading_status').append('<div class="col-12 p-2"><i class="fas fa-circle-notch fa-spin"></i> '+ user_file_name[i] +'</div>');
            }
        }
        else
        {
            $('#uploading_status').append('<div class="col-12 p-2"><i class="fas fa-circle-notch fa-spin"></i> '+ user_file_name +'</div>');
        }

        window.location.href = baseUrl + "/functions.php?downloadFile=" + user_file_name;
        $('#fileInformation').modal('hide');
        $('#uploading_status i').removeClass('fas fa-circle-notch fa-spin').addClass('fas fa-check-circle text-success');
        $('#upload_notification').delay(2000).fadeOut();

        // FOR DOWNLOAD SELECTED
        return true;

        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

    function shareFile(user_file_name)
    {
        // ------------------------------------------------------------------------------------------ //
                                            // SHARE FILE
        // ------------------------------------------------------------------------------------------ //
        $('.js-example-basic-single').select2({
            multiple: true,
            width: '100%',
            allowClear: true
        });
        
        getUserListJSON();
        user_id_list = [];
        $('#user_id_list').html($('#user_id_list').html().replace('selected',''));
        $('#share_notification').html('');

        $('#fileInformation').modal('hide');
        $('#shareFile').modal('show');
        $('#share_user_file_name').html(user_file_name);

        // ON USER SELECT
        $('#user_id_list').on('select2:select', function (e) {
            user_id_list.push(e.params.data.id);
        });

        // ON USER DESELECT
        $('#user_id_list').on('select2:unselect', function (e) {
            user_id_list = jQuery.grep(user_id_list, function(value) {
                return value != e.params.data.id;
            });
        });

        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

    function deleteFile(user_file_name)
    {
        // ------------------------------------------------------------------------------------------ //
                                            // DELETE FILE
        // ------------------------------------------------------------------------------------------ //
        
        $('#status_title').html('Deleting');
        $('#upload_notification').removeClass("d-none").fadeIn();
        $('#uploading_status').html('');
        
        if( $.isArray(user_file_name) )
        {
            for( var i=0 ; i<user_file_name.length ; i++ )
            {
                $('#uploading_status').append('<div class="col-12 p-2"><i class="fas fa-circle-notch fa-spin"></i> '+ user_file_name[i] +'</div>');
            }
        }
        else
        {
            $('#uploading_status').append('<div class="col-12 p-2"><i class="fas fa-circle-notch fa-spin"></i> '+ user_file_name +'</div>');
        }

        $.ajax({
            method: "POST",
            url: baseUrl + "/functions.php",
            data: 'deleteFile=' + user_file_name
        }).done(function(response) {
            $('#fileInformation').modal('hide');
            getFolderListJSON();
            $('#uploading_status i').removeClass('fas fa-circle-notch fa-spin').addClass('fas fa-check-circle text-success');
            $('#upload_notification').delay(2000).fadeOut();

            // FOR DELETE SELECTED
            return true;
        });

        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

    function download_selected()
    {
        // ------------------------------------------------------------------------------------------ //
                                        // DOWNLOAD ALL SELECTED FILE
        // ------------------------------------------------------------------------------------------ //

        $('#folder .card, #file .card').removeClass('active');
        var selected_file = [];
        $("input[name='selected_files[]']:checked").each(function ()
        {
            selected_file.push($(this).val());
        });

        if( selected_file.length != 0 )
        {
            if( downloadFile(selected_file) )
            {
                $('input:checkbox').not(this).prop('checked', false);
            }
        }

        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

    function share_selected()
    {
        // ------------------------------------------------------------------------------------------ //
                                        // SHARE ALL SELECTED FILE
        // ------------------------------------------------------------------------------------------ //

        $('#folder .card, #file .card').removeClass('active');
        var selected_file = [];
        $("input[name='selected_files[]']:checked").each(function ()
        {
            selected_file.push($(this).val());
        });

        if( selected_file.length != 0 )
        {
            shareFile(selected_file.join('<br>'));
        }
        $('input:checkbox').not(this).prop('checked', false);

        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

    function delete_selected()
    {
        // ------------------------------------------------------------------------------------------ //
                                        // DELETE ALL SELECTED FILE
        // ------------------------------------------------------------------------------------------ //

        $('#folder .card, #file .card').removeClass('active');
        var selected_file = [];
        $("input[name='selected_files[]']:checked").each(function ()
        {
            selected_file.push($(this).val());
        });

        if( selected_file.length != 0 )
        {
            if( deleteFile(selected_file) )
            {
                $('input:checkbox').not(this).prop('checked', false);
            }
        }

        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }
</script>