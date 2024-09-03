jQuery(document).ready(function ($) {
    $('#create-folder-button').on('click', function (e) {
        e.preventDefault();
        $('#create-folder-overlay').show(); // Show the overlay
        $('#create-folder-modal').show(); // Show the modal
    });

    $('#cancel-new-folder').on('click', function(e) {
        e.preventDefault();
        $('#create-folder-overlay').hide(); // Hide the overlay
        $('#create-folder-modal').hide(); // Hide the modal
    });

    $('#save-new-folder').on('click', function(e) {
        e.preventDefault();
        var folderName = $('#new-folder-name').val();
        var parentFolderId = $('#parent-folder').val();

        if (folderName) {
            $.post(ajaxurl, {
                action: 'create_media_folder',
                folder_name: folderName,
                parent_folder: parentFolderId,
            }, function(response) {
                if (response.success) {
                    alert('Folder created: ' + response.data);
                    location.reload(); // Reload to show the new folder
                } else {
                    alert('Failed to create folder: ' + response.data);
                }
            });
        } else {
            alert('Please enter a folder name.');
        }
    });
    $('#media-folder-filter').on('change', function() {
        var folder = $(this).val(); // Get the selected folder slug
        var url = window.location.href.split('?')[0]; // Get the base URL without query parameters
        var query = new URLSearchParams(window.location.search);
    
        if (folder) {
            // If a folder is selected, set the taxonomy and term parameters
            query.set('taxonomy', 'media_folder');
            query.set('term', folder);
        } else {
            // If no folder is selected, remove the taxonomy and term parameters
            query.delete('taxonomy');
            query.delete('term');
        }
    
        // Reload the page with the updated query string
        window.location.href = url + '?' + query.toString();
    });
});