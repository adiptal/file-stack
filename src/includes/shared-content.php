<div class="container-box">
    <div class="container-fluid">

        <div class="nav-breadcrumbs">
            <ul class="list-inline col-sm-7 col-md-8 col-lg-9 float-left" id="breadcrumb_links">
            </ul>
            <div class="float-right mb-2 mb-sm-0">
                <div class="col-12">
                    <input type="checkbox" id="check_all" hidden>
                    <button class="ml-3" id="check_all_action">Select All</button>
                    <button class="ml-3" onclick="download_selected()"><i class="fas fa-download"></i></button>
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

<script>
    $(function(){
        // user_id SELECT - SHARE FILE
        var user_id_list;

        openDirectory();
        $('li').removeClass('active');
        $('li#shared-with-me').addClass('active');
        $('#addData').hide();
    });

    // ----------------------------------------------------------------------------------------------------//
                                            // UI FUNCTIONALITY
    // ----------------------------------------------------------------------------------------------------//

    async function openDirectory(directory_name = '')
    {
        // ------------------------------------------------------------------------------------------ //
                                    // FETCH FOLDER AND FILE NAMES IN DIRECTORY
        // ------------------------------------------------------------------------------------------ //

        $.ajax({
            method: "POST",
            url: baseUrl + "/shared-functions.php",
            data: 'openDirectory=' + directory_name ,
            dataType: "json"
        }).done(function(response) {
            $('#folder').html('');
            $('#file').html('');
            $('#breadcrumb_links').html(response['breadcrumps']);
        
            if (!jQuery.isEmptyObject(response['directory']))
            {
                for(var i=0 ; i<response['directory'].length ; i++)
                {
                    var loc = response['directory'][i]['user_file_location'];
                    $('#folder').append(
                        '<div class="col-lg-2 col-md-4 col-6 mb-2 folder-items">'+
                        '<div class="card context-menu-three"  id="'+ loc +'" ondblclick="openDirectory( '+ "'" + loc + "'" +' )">'+
                        '<div class="card-body">'+
                        '<div class="row">'+
                        '<input type="checkbox" name="selected_files[]" value="'+ loc +'" hidden>'+
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
                    var loc = response['file'][i]['user_file_location'];
                    $('#file').append(
                        '<div class="col-lg-2 col-md-4 col-6 mb-2 folder-items">'+
                        '<div class="card context-menu-three"  id="'+ loc + response['file'][i]['key'] +'">'+
                        '<div class="card-body">'+
                        '<div class="row">'+
                        '<input type="checkbox" name="selected_files[]" value="'+ loc + response['file'][i]['key'] +'" hidden>'+
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
            
            // '<td class="w-100 pt-3" onclick="getFileInfo('+ "'" + response['file'][i]['key'] + "'" +' , '+ "'" + response['file'][i]['user_data_name'] + "'" +' , '+ "'" + response['file'][i]['user_data_location'] + "'" +')">'+
        });
        
        // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    }

    function getFileInfo(file_name , user_file_name , user_file_location)
    {
        // ------------------------------------------------------------------------------------------ //
                                        // FILE INFORMATION THROUGH AJAX
        // ------------------------------------------------------------------------------------------ //

        $('#fileInformation').modal('toggle');

        $.ajax({
            method: "POST",
            url: baseUrl + "/shared-functions.php",
            data: 'getFileInformationJSON&user_file_name=' + user_file_name + '&user_file_location=' + user_file_location ,
            // dataType: "json"
        }).done(function(response) {
            $('#user_file_name').html(file_name);
            $('#shared_at').html(response[0]);
            $('#from_user_email').html(response[1]);
            $('#file_action').html(
                '<button type="button" class="btn btn-link" onclick="downloadFile('+ "'" + user_file_location + '/' + user_file_name + "'" +')"><i class="fas fa-download"></i></button>'
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

        window.location.href = baseUrl + "/shared-functions.php?downloadFile=" + user_file_name;
        $('#fileInformation').modal('hide');
        $('#uploading_status i').removeClass('fas fa-circle-notch fa-spin').addClass('fas fa-check-circle text-success');
        $('#upload_notification').delay(2000).fadeOut();

        // FOR DOWNLOAD SELECTED
        return true;

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
</script>