/**
 * FileField client side functionality.
 */

(function (factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        // Register as an anonymous AMD module:
        define([
            'jquery',
            './jquery.fileupload-process'
        ], factory);
    } else {
        // Browser globals:
        factory(
            window.jQuery
        );
    }
}(function ($) {
    'use strict';

    // Prepend to the default processQueue:
    $.blueimp.fileupload.prototype.options.processQueue.push(
        {
            action: 'displayFile'
        }
    );

    $.widget('blueimp.fileupload', $.blueimp.fileupload, {
        options: {

        },
        processActions: {
            displayFile: function (data, options) {
                //filefield.displayFile(data.files[data.index]);
                return data;
            }
        }
    });

}));
