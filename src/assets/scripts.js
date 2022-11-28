$(function()
{
    // -------------------------------------------------------------------------------- //
                                        // CREATE FOLDER
    // -------------------------------------------------------------------------------- //
    
    $(document).on('click' , '#create_folder_submit' , {} , function(){
        var regex = new RegExp('^[a-zA-Z0-9]+$');
        if ( regex.test( $('#create_folder').val() ) )
        {
            $('#create_folder_submit').attr('disabled' , true);
            $('#notification').html('<span class="text-secondary">Creating Folder ...</span>');
            
            $.ajax({
                method: "POST",
                url: baseUrl + "/functions.php",
                data: 'create_folder=' + $('#create_folder').val()
            }).done(function(response) {
                $('#create_folder').val('');
                $('#notification').html('');
                $('#create_folder_submit').attr('disabled' , false);
                $('#createFolder').modal('hide');
                getFolderListJSON();
                $('body').append(response);
            });
        }
        else
        {
            $('#notification').html('<span class="text-danger">No special characters</span>');
        }
    });

    // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    

    // -------------------------------------------------------------------------------- //
                                        // FILE UPLOAD
    // -------------------------------------------------------------------------------- //

    $(document).on('click' , '#upload_file_internal_call' , {} , function(){
        $('#user_file').click();
    });
    
    $(document).on('change' , '#user_file' , {} , function(){
        $('#status_title').html('Uploading');
        $('#upload_notification').removeClass("d-none").fadeIn();
        $('#uploading_status').html('');
        
        // PROGRESS ANIMATION
        $('#status_title').parent().append(
            '<div class="progress" style="height: 2px;">'+
            '<div class="progress-bar bg-custom" role="progressbar"id="progress" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
            '</div>'
        );

        var formData = new FormData();
        var file_names = '';
        var zip = new JSZip();

        for(var i=0 ; i<$('#user_file')[0].files.length ; i++)
        {
            $('#uploading_status').append('<div class="col-12 p-2"><i class="fas fa-circle-notch fa-spin"></i> '+ $('#user_file')[0].files[i].name +'</div>');
            file_names += $('#user_file')[0].files[i].name+"###";
            zip.file( $('#user_file')[0].files[i].name , $('#user_file')[0].files[i] );
        }
        
        zip.generateAsync({type:"blob"}, function updateCallback(metadata) {
            // PROGRESS ANIMATION
            var status = ((metadata.percent/100)*10) + '%';
            $('#progress').css({
                'width': status
            });
          }).then(function (blob) {
            formData.append('zip_file', blob);
            formData.append('file_names', file_names);
            // ---------- SUBMIT BUTTON NAME TO CHECK ON FUNCTION.PHP ---------- //
            formData.append('upload_file_submit' , '');

            $.ajax({
                // PROGRESS ANIMATION
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            var status = 10+(percentComplete*90) + '%';
                            $('#progress').css({
                                'width': status
                            });
                        }
                   }, false);
            
                   xhr.addEventListener("progress", function(evt) {
                       if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        var status = 10+(percentComplete*90) + '%';
                        $('#progress').css({
                            'width': status
                        });
                       }
                   }, false);
            
                   return xhr;
                },
                // END PROGRESS ANIMATION
                method: "POST",
                url: baseUrl + "/functions.php",
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
            }).done(function(response) {
                $('.progress').remove();
                $('#uploading_status i').removeClass('fas fa-circle-notch fa-spin').addClass('fas fa-check-circle text-success');
                $('#upload_notification').delay(2000).fadeOut();
                getFolderListJSON();
                $('body').append(response);
                $('#user_file').val(''); 
            });
        });
    });

    // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //


    // -------------------------------------------------------------------------------- //
                                    // FOLDER UPLOAD
    // -------------------------------------------------------------------------------- //

    $(document).on('click' , '#upload_folder_internal_call' , {} , function(){
        $('#user_folder').click();
    });
    
    $(document).on('change' , '#user_folder' , {} , function(e){
        $('#status_title').html('Uploading');
        $('#upload_notification').removeClass("d-none").fadeIn();
        $('#uploading_status').html('');
        
        // PROGRESS ANIMATION
        $('#status_title').parent().append(
            '<div class="progress" style="height: 2px;">'+
            '<div class="progress-bar bg-custom" role="progressbar"id="progress" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>'+
            '</div>'
        );

        var formData = new FormData();
        var files = e.target.files;
        var file_paths = '';

        var i = 0;
        var j;
        
        // ---------- TAKE ALL FILES FROM FOLDER AND APPEND RELATIVE PATHS ---------- //
                            // ---------- AND AJAX CALL ---------- //

        var zip = new JSZip();

        for ( i , j = files[i] ; i<files.length ; i++ )
        {
            $('#uploading_status').append('<div class="col-12 p-2"><i class="fas fa-circle-notch fa-spin"></i> '+ files[i].name +'</div>');
            file_paths += files[i].webkitRelativePath+"###";
            formData.append(i , files[i].name);
            zip.file( files[i].webkitRelativePath , files[i] );
        };

        zip.generateAsync({type:"blob"}, function updateCallback(metadata) {
            // PROGRESS ANIMATION
            var status = ((metadata.percent/100)*10) + '%';
            $('#progress').css({
                'width': status
            });
          }).then(function (blob) {
            formData.append('zip_file', blob);
            formData.append('file_paths', file_paths);
            // ---------- SUBMIT BUTTON NAME TO CHECK ON FUNCTION.PHP ---------- //
            formData.append('upload_folder_submit' , '');
            
            $.ajax({
                // PROGRESS ANIMATION
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            var status = 10+(percentComplete*90) + '%';
                            $('#progress').css({
                                'width': status
                            });
                        }
                   }, false);
            
                   xhr.addEventListener("progress", function(evt) {
                       if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        var status = 10+(percentComplete*90) + '%';
                        $('#progress').css({
                            'width': status
                        });
                       }
                   }, false);
            
                   return xhr;
                },
                // END PROGRESS ANIMATION
                method: "POST",
                url: baseUrl + "/functions.php",
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
            }).done(function(response) {
                $('.progress').remove();
                $('#uploading_status i').removeClass('fas fa-circle-notch fa-spin').addClass('fas fa-check-circle text-success');
                $('#upload_notification').delay(2000).fadeOut();
                getFolderListJSON();
                $('body').append(response);
                $('#user_folder').val('');
            });
        });
    });

    // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //

    
    // -------------------------------------------------------------------------------- //
                                    // SHARE FILE
    // -------------------------------------------------------------------------------- //

    $(document).on('click' , '#share_file_submit' , {} , function(){
        
        if(user_id_list.length == 0)
        {
            $('#share_notification').html('<span class="text-danger">Select User Email</span>');
        }
        else
        {
            $('#status_title').html('Sharing');
            $('#upload_notification').removeClass("d-none").fadeIn();
            $('#uploading_status').html('');
            var share_user_file_name = $('#share_user_file_name').html().split('<br>');

            for(var i=0 ; i<share_user_file_name.length ; i++)
            {
                $('#uploading_status').append('<div class="col-12 p-2"><i class="fas fa-circle-notch fa-spin"></i> '+ share_user_file_name[i] +'</div>');
                $.ajax({
                    method: "POST",
                    url: baseUrl + "/functions.php",
                    data: 'shareFile=' + share_user_file_name[i] + '&user_id_list=' + user_id_list
                }).done(function(response) {
                    $('#shareFile').modal('hide');
                    $('#uploading_status i').removeClass('fas fa-circle-notch fa-spin').addClass('fas fa-check-circle text-success');
                    $('#upload_notification').delay(2000).fadeOut();
                });
            }
        }
    });

    // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
    

    // -------------------------------------------------------------------------------- //
                        // MULTIPLE SELECT CHECKBOX FUNCTIONALITY
    // -------------------------------------------------------------------------------- //

    $(document).on('click' , '#check_all_action' , {} , function(e){
        e.stopPropagation();
        $('#check_all').click();
    });

    $(document).on('click' , "#check_all" , {} , function(e){
        e.stopPropagation();
        if( this.checked )
        {
            $('#folder .card, #file .card').addClass('active');
        }
        else
        {
            $('#folder .card, #file .card').removeClass('active');
        }
        $('input:checkbox').not(this).prop('checked', this.checked);
    });
    
    jQuery(document).on('keydown' , function(e) {
        if(e.ctrlKey)
        {
            if(e.keyCode == 65 || e.keyCode == 97)
            {
                e.preventDefault();
                $('input:checkbox').not(this).prop('checked', true);
                $('#folder .card, #file .card').addClass('active');
            }
        }
    });

    // DESELECTING DATA ON BODY CLICK
    $(document).on('click' , function(){
        $('input:checkbox').not(this).prop('checked', false);
        $('#folder .card, #file .card').removeClass('active');
    });

    // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //


    // -------------------------------------------------------------------------------- //
                        // FOLDER RIGHT CLICK FUNCTIONALITY
    // -------------------------------------------------------------------------------- //
    
    // FOLDER CONTEXTMENU
    $.contextMenu({
        selector: '.context-menu-one', 
        callback: function(key, options) {
            switch(key)
            {
                case 'share' : shareFile( $(this).attr('id') );
                break;
                
                case 'download' : downloadFile( $(this).attr('id') );
                break;
                
                case 'delete' : deleteFile( $(this).attr('id') );
                break;
            }
        },
        items: {
            "share": {name: "Share"},
            "download": {name: "Download"},
            "delete": {name: "Delete"},
        }
    });
    
    // FILE CONTEXTMENU
    $.contextMenu({
        selector: '.context-menu-two', 
        callback: function(key, options) {
            switch(key)
            {
                case 'share' : shareFile( $(this).attr('id') );
                break;
                
                case 'download' : downloadFile( $(this).attr('id') );
                break;
                
                case 'delete' : deleteFile( $(this).attr('id') );
                break;
                
                case 'info' : getFileInfo( $(this).attr('id') );
                break;
            }
        },
        items: {
            "share": {name: "Share"},
            "download": {name: "Download"},
            "delete": {name: "Delete"},
            "info": {name: "Info"},
        }
    });
    
    // SHARED WITH ME CONTEXTMENU
    $.contextMenu({
        selector: '.context-menu-three', 
        callback: function(key, options) {
            switch(key)
            {
                case 'download' : downloadFile( $(this).attr('id') );
                break;
            }
        },
        items: {
            "download": {name: "Download"}
        }
    });

    // ---------- X ---------- X ---------- X ---------- X ---------- X ---------- //
});