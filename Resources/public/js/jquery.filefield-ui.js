
(function (factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        // Register as an anonymous AMD module:
        define([
            'jquery',
            './jquery.fileupload-validate'
        ], factory);
    } else {
        // Browser globals:
        factory(
            window.jQuery
        );
    }
}(function ($) {
    'use strict';

    $.blueimp.fileupload.prototype._specialOptions.push(
        'filesContainer',
        'uploadTemplateId',
        'downloadTemplateId'
    );

    // The UI version extends the file upload widget
    // and adds complete user interface interaction:
    $.widget('blueimp.fileupload', $.blueimp.fileupload, {

        options: {
            // By default, files added to the widget are uploaded as soon
            // as the user clicks on the start buttons. To enable automatic
            // uploads, set the following option to true:
            autoUpload: true,
            // The container for the list of files. If undefined, it is set to
            // an element with class "files" inside of the widget element:
            filesContainer: $('.filefield-files'),
            // By default, files are appended to the files container.
            // Set the following option to true, to prepend files instead:
            prependFiles: false,
            // The expected data type of the upload response, sets the dataType
            // option of the $.ajax upload requests:
            dataType: 'json',

            // Function returning the current number of files,
            // used by the maxNumberOfFiles validation:
            getNumberOfFiles: function () {
                return this.filesContainer.children().length;
            },

            // Callback to retrieve the list of files from the server response:
            getFilesFromResponse: function (data) {
                if (data.result && $.isArray(data.result.files)) {
                    return data.result.files;
                }
                return [];
            },

            // The add callback is invoked as soon as files are added to the fileupload
            // widget (via file input selection, drag & drop or add API call).
            // See the basic file upload widget for more information:
            add: function (e, data) {
                var $this = $(this);
                var that = $this.data('blueimp-fileupload');
                var options = that.options;
                var files = data.files;

                data.process(function () {
                    return $this.fileupload('process', data);
                }).always(function () {
                    data.context = that.renderFiles(files).data('data', data);
                    //that._renderPreviews(data);
                    var where = options.prependFiles ? 'prepend' : 'append';
                    options.filesContainer[where](data.context);
                    that._forceReflow(data.context);
                    that._transition(data.context).done(function () {
                        var added = that._trigger('added', e, data);
                        if ((added !== false) && options.autoUpload && !data.files.error) {
                            filefield.setProgressBar(data.context);
                            data.submit();
                        }
                    });
                    if (that.options.getNumberOfFiles() >= that.options.maxNumberOfFiles) {
                        $('.fileinput-button').hide();
                    }
                });
            },

            // Callback for the start of each file upload request:
            send: function (e, data) {
                var that = $(this).data('blueimp-fileupload');
                if (data.context && data.dataType && data.dataType.substr(0, 6) === 'iframe') {
                    // Iframe Transport does not support progress events.
                    // In lack of an indeterminate progress bar, we set
                    // the progress to 100%, showing the full animated bar:
                    data.context.find('.progress')
                        .addClass(!$.support.transition && 'progress-animated')
                        .attr('aria-valuenow', 100)
                        .children().first().css('width', '100%');
                }
                return that._trigger('sent', e, data);
            },

            // Callback for successful uploads:
            done: function (e, data) {
                var that = $(this).data('blueimp-fileupload');
                var getFilesFromResponse = data.getFilesFromResponse || that.options.getFilesFromResponse;
                var files = getFilesFromResponse(data);
                var template;
                var deferred;
                if (data.context) {
                    data.context.each(function (index) {
                        var file = files[index] || {error: 'Empty file upload result'};
                        deferred = that._addFinishedDeferreds();
                        that._transition($(this)).done(function () {
                            var node = $(this);
                            template = that.renderSuccess([file], node).replaceAll(node);
                            that._forceReflow(template);
                            that._transition(template).done(function () {
                                data.context = $(this);
                                that._trigger('completed', e, data);
                                that._trigger('finished', e, data);
                                deferred.resolve();
                            });
                        });
                    });
                } else {
                    var where = that.options.prependFiles ? 'prependTo' : 'appendTo';
                    template = that._renderDownload(files)[where](that.options.filesContainer);
                    that._forceReflow(template);
                    deferred = that._addFinishedDeferreds();
                    that._transition(template).done(function () {
                        data.context = $(this);
                        that._trigger('completed', e, data);
                        that._trigger('finished', e, data);
                        deferred.resolve();
                    });
                }
            },

            // Callback for failed (abort or error) uploads:
            fail: function (e, data) {
                var that = $(this).data('blueimp-fileupload');
                var template;
                var deferred;
                if (data.context) {
                    data.context.each(function (index) {
                        if (data.errorThrown !== 'abort') {
                            var file   = data.files[index];
                            file.error = file.error || data.errorThrown || true;
                            deferred   = that._addFinishedDeferreds();

                            that._transition($(this)).done(function () {
                                var node = $(this);
                                template = that._renderDownload([file]).replaceAll(node);
                                that._forceReflow(template);
                                that._transition(template).done(function () {
                                    data.context = $(this);
                                    that._trigger('failed', e, data);
                                    that._trigger('finished', e, data);
                                    deferred.resolve();
                                });
                            });
                        } else {
                            deferred = that._addFinishedDeferreds();
                            that._transition($(this)).done(function () {
                                $(this).remove();
                                that._trigger('failed', e, data);
                                that._trigger('finished', e, data);
                                deferred.resolve();
                            });
                        }
                    });
                } else if (data.errorThrown !== 'abort') {
                    var where = that.options.prependFiles ? 'prependTo' : 'appendTo';
                    data.context = that.renderFiles(data.files)[where](that.options.filesContainer).data('data', data);
                    that._forceReflow(data.context);
                    deferred = that._addFinishedDeferreds();
                    that._transition(data.context).done(function () {
                        data.context = $(this);
                        that._trigger('failed', e, data);
                        that._trigger('finished', e, data);
                        deferred.resolve();
                    });
                } else {
                    that._trigger('failed', e, data);
                    that._trigger('finished', e, data);
                    that._addFinishedDeferreds().resolve();
                }
            },

            // Callback for upload progress events:
            progress: function (e, data) {
                var progress = Math.floor(data.loaded / data.total * 100);
                if (data.context) {
                    data.context.each(function () {
                        $(this).find('.progress')
                            .attr('aria-valuenow', progress)
                            .children().first().css(
                                'width',
                                progress + '%'
                            );
                    });
                }
            },

            // Callback for global upload progress events:
            progressall: function (e, data) {
                var $this = $(this),
                    progress = Math.floor(data.loaded / data.total * 100),
                    globalProgressNode = $this.find('.fileupload-progress'),
                    extendedProgressNode = globalProgressNode
                        .find('.progress-extended');
                if (extendedProgressNode.length) {
                    extendedProgressNode.html($this.data('blueimp-fileupload')._renderExtendedProgress(data));
                }
                globalProgressNode
                    .find('.progress')
                    .attr('aria-valuenow', progress)
                    .children().first().css(
                        'width',
                        progress + '%'
                    );
            },

            // Callback for uploads start, equivalent to the global ajaxStart event:
            start: function (e) {
                var that = $(this).data('blueimp-fileupload');
                that._resetFinishedDeferreds();
                that._transition($(this).find('.fileupload-progress')).done(
                    function () {
                        that._trigger('started', e);
                    }
                );
            },

            // Callback for uploads stop, equivalent to the global ajaxStop event:
            stop: function (e) {
                var that = $(this).data('blueimp-fileupload');
                var deferred = that._addFinishedDeferreds();
                $.when.apply($, that._getFinishedDeferreds())
                    .done(function () {
                        that._trigger('stopped', e);
                    });
                that._transition($(this).find('.fileupload-progress')).done(
                    function () {
                        $(this).find('.progress')
                            .attr('aria-valuenow', '0')
                            .children().first().css('width', '0%');
                        $(this).find('.progress-extended').html('&nbsp;');
                        deferred.resolve();
                    }
                );
            },

            processstart: function () {
                $(this).addClass('fileupload-processing');
            },

            processstop: function () {
                $(this).removeClass('fileupload-processing');
            },

            processfail: function (event, data) {
                //filefield.setFailure(data.files[0]);
            },

            // Callback for file deletion:
            destroy: function (e, data) {
                var that = $(this).data('blueimp-fileupload');
                var removeNode = function () {
                    that._transition(data.context).done(function () {
                        $(this).remove();
                        that._trigger('destroyed', e, data);
                    });
                };
                if (data.url) {
                    $.ajax(data).done(removeNode);
                } else {
                    removeNode();
                }
            }
        },






        _resetFinishedDeferreds: function () {
            this._finishedUploads = [];
        },

        _addFinishedDeferreds: function (deferred) {
            if (!deferred) {
                deferred = $.Deferred();
            }
            this._finishedUploads.push(deferred);
            return deferred;
        },

        _getFinishedDeferreds: function () {
            return this._finishedUploads;
        },

        // Link handler, that allows to download files
        // by drag & drop of the links to the desktop:
        _enableDragToDesktop: function () {
            var link = $(this);
            var url = link.prop('href');
            var name = link.prop('download');
            var type = 'application/octet-stream';

            link.bind('dragstart', function (e) {
                try {
                    e.originalEvent.dataTransfer.setData(
                        'DownloadURL',
                        [type, name, url].join(':')
                    );
                } catch (ignore) {}
            });
        },

        _formatFileSize: function (bytes) {
            if (typeof bytes !== 'number') {
                return '';
            }
            if (bytes >= 1000000000) {
                return (bytes / 1000000000).toFixed(2) + ' GB';
            }
            if (bytes >= 1000000) {
                return (bytes / 1000000).toFixed(2) + ' MB';
            }
            return (bytes / 1000).toFixed(2) + ' KB';
        },

        _formatBitrate: function (bits) {
            if (typeof bits !== 'number') {
                return '';
            }
            if (bits >= 1000000000) {
                return (bits / 1000000000).toFixed(2) + ' Gbit/s';
            }
            if (bits >= 1000000) {
                return (bits / 1000000).toFixed(2) + ' Mbit/s';
            }
            if (bits >= 1000) {
                return (bits / 1000).toFixed(2) + ' kbit/s';
            }
            return bits.toFixed(2) + ' bit/s';
        },

        _formatTime: function (seconds) {
            var date = new Date(seconds * 1000);
            var days = Math.floor(seconds / 86400);
            days = days ? days + 'd ' : '';
            return days +
                ('0' + date.getUTCHours()).slice(-2) + ':' +
                ('0' + date.getUTCMinutes()).slice(-2) + ':' +
                ('0' + date.getUTCSeconds()).slice(-2);
        },

        _formatPercentage: function (floatValue) {
            return (floatValue * 100).toFixed(2) + ' %';
        },

        _renderExtendedProgress: function (data) {
            return this._formatBitrate(data.bitrate) + ' | ' +
                this._formatTime(
                    (data.total - data.loaded) * 8 / data.bitrate
                ) + ' | ' +
                this._formatPercentage(
                    data.loaded / data.total
                ) + ' | ' +
                this._formatFileSize(data.loaded) + ' / ' +
                this._formatFileSize(data.total);
        },

        _renderPreviews: function (data) {
            data.context.find('.preview').each(function (index, elm) {
                $(elm).append(data.files[index].preview);
            });
        },



        _startHandler: function (e) {
            e.preventDefault();
            var button = $(e.currentTarget),
                template = button.closest('.template-upload'),
                data = template.data('data');
            if (data && data.submit && !data.jqXHR && data.submit()) {
                button.prop('disabled', true);
            }
        },

        _cancelHandler: function (e) {
            e.preventDefault();
            var template = $(e.currentTarget).closest('.template-upload,.template-download');
            var data = template.data('data') || {};
            if (!data.jqXHR) {
                data.context = data.context || template;
                data.errorThrown = 'abort';
                this._trigger('fail', e, data);
            } else {
                data.jqXHR.abort();
            }
        },

        _deleteHandler: function (e) {
            e.preventDefault();
            var button = $(e.currentTarget);
            this._trigger('destroy', e, $.extend({
                context: button.closest('.template-download'),
                type: 'DELETE'
            }, button.data()));
        },

        _forceReflow: function (node) {
            return $.support.transition && node.length &&
                node[0].offsetWidth;
        },

        _transition: function (node) {
            var dfd = $.Deferred();
            if ($.support.transition && node.hasClass('fade') && node.is(':visible')) {
                node.bind(
                    $.support.transition.end,
                    function (e) {
                        // Make sure we don't respond to other transitions events
                        // in the container element, e.g. from button elements:
                        if (e.target === node[0]) {
                            node.unbind($.support.transition.end);
                            dfd.resolveWith(node);
                        }
                    }
                ).toggleClass('in');
            } else {
                node.toggleClass('in');
                dfd.resolveWith(node);
            }
            return dfd;
        },

        _initEventHandlers: function () {
            this._super();
            this._on(this.options.filesContainer, {
                'click .start': this._startHandler,
                'click .cancel': this._cancelHandler,
                'click .delete': this._deleteHandler
            });
        },

        _destroyEventHandlers: function () {
            this._off(this.options.filesContainer, 'click');
            this._super();
        },

        _enableFileInputButton: function () {
            this.element.find('.fileinput-button input')
                .prop('disabled', false)
                .parent().removeClass('disabled');
        },

        _disableFileInputButton: function () {
            this.element.find('.fileinput-button input')
                .prop('disabled', true)
                .parent().addClass('disabled');
        },

        _initSpecialOptions: function () {
            this._super();
        },

        _create: function () {
            this._super();
            this._resetFinishedDeferreds();
            if (!$.support.fileInput) {
                this._disableFileInputButton();
            }
            if (this.options.getNumberOfFiles() >= this.options.maxNumberOfFiles) {
                $('.fileinput-button').hide();
            }
            this.formLeafName = this.options.getNumberOfFiles();
        },

        getFileTemplate: function (name) {
            // Get a file display template and set values.
            var template = $(this.element).parents('[data-prototype]').attr('data-prototype');
            template = template.replace(/__name__/g, name);

            return $(template);
        },

        renderFiles: function (files) {
            var $result = $([]);
            for (var i = 0, c = files.length; i < c; i++) {
                var name = this.options.maxNumberOfFiles === 1 ? 'filename' : this.formLeafName;
                var $template = this.getFileTemplate(name);
                filefield.setFileData(files[i], $template);
                $result = $result.add($template);
                this.formLeafName++;
            }
            return $result;
        },

        renderSuccess: function (files, node) {
            for (var i = 0, c = files.length; i < c; i++) {
                var file = files[i];
                filefield.setFileData(file, node);
            }
            return node;
        },

        enable: function () {
            var wasDisabled = false;
            if (this.options.disabled) {
                wasDisabled = true;
            }
            this._super();
            if (wasDisabled) {
                this.element.find('input, button').prop('disabled', false);
                this._enableFileInputButton();
            }
        },

        disable: function () {
            if (!this.options.disabled) {
                this.element.find('input, button').prop('disabled', true);
                this._disableFileInputButton();
            }
            this._super();
        },

        formLeafName: 0
    });

}));
