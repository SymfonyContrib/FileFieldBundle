/**
 * FileField client side functionality.
 */

// Primary filefield API object.
var filefield = {};

(function ($) {
    'use strict';

    filefield = {
        setFileData: function (file, $template) {
            $template.find('.filefield-filename').text(file.name.substr(0, 40));
            $template.find('.filefield-filename').attr('href', file.url);
            $template.find('.filefield-filesize').text(file.size);
            $template.find('.filefield-fileicon').attr('src', file.iconUri);
            $template.find('input.filefield-value').val(file.name);
        },

        setProgressBar: function ($template) {
            var bar = '<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>';
            $template.find('.filefield-filesize').append(bar);
        },

        addRemoveListener: function () {
            $('.filefield-files').on('click', '.filefield-remove', function () {
                $(this).parents('.filefield-file').remove();
                $('.fileinput-button').show();
            });
        }
    };

    // Get filefield elements.
    var $fields = $('.filefield-upload');
    var id = $('.filefield-value').attr('id');

    // Initialize the jQuery File Upload widget:
    $fields.fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: '/filefield-upload',
        // Drag & drop zone is the widget wrapper.
        dropZone: $(this).parent(),
        // Disable paste support for now. Only supported in Chrome.
        pasteZone: null,
        singleFileUploads: true,
        limitMultiFileUploads: 0,
        sequentialUploads: false,
        limitConcurrentUploads: 4
    });

    // Attach events for remove buttons.
    filefield.addRemoveListener();

    // Disable default browser drop action.
    /*
    $(document).bind('drop dragover', function (e) {
        e.preventDefault();
    });
    */
})(jQuery);

