/**
 * FileField client side functionality.
 */

// Primary filefield API object.
var filefield = {};

(function ($) {
    'use strict';

    filefield = {
        setProgressBar: function ($template) {
            var bar = '<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>';
            $template.find('.filefield-filesize').append(bar);
        },

        addRemoveListener: function () {
            $('.filefield-files').on('click', '.filefield-remove', function () {
                var $this = $(this);
                $this.parents('.filefield-widget').find('.fileinput-button').show();
                $this.parents('.filefield-file').remove();
            });
        },

        /**
         * Since we can't depend on what the application templating introduces unwanted wrapping
         * elements, we remove them if found.
         */
        cleanFileContainers: function () {
            $('.filefield-files').each(function () {
                var $files = $(this);
                $files.children(':not(.filefield-file)').each(function () {
                    var $child = $(this);
                    $files.append($child.find('.filefield-file'));
                    $(this).remove();
                })
            })
        }
    };

    // Get filefield elements.
    var $fields = $('.filefield-upload');
    var id      = $('.filefield-value').attr('id');

    // Initialize the jQuery File Upload widget:
    $fields.each(function () {
        // Single file template
        var filesContainer = $(this).parents('.filefield-widget').find('.filefield-files');

        filefield.cleanFileContainers();

        $(this).fileupload({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: '/filefield-upload',
            fileInput: $('input:file', this),
            // Drag & drop zone is the widget wrapper.
            dropZone: $(this).parent(),
            // Disable paste support for now. Only supported in Chrome.
            pasteZone: null,
            singleFileUploads: true,
            limitMultiFileUploads: 0,
            sequentialUploads: false,
            limitConcurrentUploads: 4,
            filesContainer: filesContainer
        });
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

