/*
 * Gijgo JavaScript Library v1.5.0
 * http://gijgo.com/
 *
 * Copyright 2014, 2017 gijgo.com
 * Released under the MIT license
 */
if (typeof (gj) === 'undefined') {
    gj = {};
}

gj.widget = function () {
    var self = this;

    self.xhr = null;

    self.generateGUID = function () {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
        }
        return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
    };

    self.mouseX = function (e) {
        if (e) {
            if (e.pageX) {
                return e.pageX;
            } else if (e.clientX) {
                return e.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
            } else if (e.touches && e.touches.length) {
                return e.touches[0].pageX;
            } else if (e.changedTouches && e.changedTouches.length) {
                return e.changedTouches[0].pageX;
            } else if (e.originalEvent && e.originalEvent.touches && e.originalEvent.touches.length) {
                return e.originalEvent.touches[0].pageX;
            } else if (e.originalEvent && e.originalEvent.changedTouches && e.originalEvent.changedTouches.length) {
                return e.originalEvent.touches[0].pageX;
            }
        }
        return null;
    };

    self.mouseY = function (e) {
        if (e) {
            if (e.pageY) {
                return e.pageY;
            } else if (e.clientY) {
                return e.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
            } else if (e.touches && e.touches.length) {
                return e.touches[0].pageY;
            } else if (e.changedTouches && e.changedTouches.length) {
                return e.changedTouches[0].pageY;
            } else if (e.originalEvent && e.originalEvent.touches && e.originalEvent.touches.length) {
                return e.originalEvent.touches[0].pageY;
            } else if (e.originalEvent && e.originalEvent.changedTouches && e.originalEvent.changedTouches.length) {
                return e.originalEvent.touches[0].pageY;
            }
        }
        return null;
    };
};

gj.widget.prototype.init = function (jsConfig, type) {
    var option, clientConfig, fullConfig;

    this.attr('data-type', type);
    clientConfig = $.extend(true, {}, this.getHTMLConfig() || {});
    $.extend(true, clientConfig, jsConfig || {});
    fullConfig = this.getConfig(clientConfig, type);
    this.attr('data-guid', fullConfig.guid);
    this.data(fullConfig);

    // Initialize events configured as options
    for (option in fullConfig) {
        if (gj[type].events.hasOwnProperty(option)) {
            this.on(option, fullConfig[option]);
            delete fullConfig[option];
        }
    }

    // Initialize all plugins
    for (plugin in gj[type].plugins) {
        if (gj[type].plugins.hasOwnProperty(plugin)) {
            gj[type].plugins[plugin].configure(this, fullConfig, clientConfig);
        }
    }

    return this;
};

gj.widget.prototype.getConfig = function (clientConfig, type) {
    var config, uiLibrary, iconsLibrary, plugin;

    config = $.extend(true, {}, gj[type].config.base);

    uiLibrary = clientConfig.hasOwnProperty('uiLibrary') ? clientConfig.uiLibrary : config.uiLibrary;
    if (gj[type].config[uiLibrary]) {
        $.extend(true, config, gj[type].config[uiLibrary]);
    }

    iconsLibrary = clientConfig.hasOwnProperty('iconsLibrary') ? clientConfig.iconsLibrary : config.iconsLibrary;
    if (gj[type].config[iconsLibrary]) {
        $.extend(true, config, gj[type].config[iconsLibrary]);
    }

    for (plugin in gj[type].plugins) {
        if (gj[type].plugins.hasOwnProperty(plugin)) {
            $.extend(true, config, gj[type].plugins[plugin].config.base);
            if (gj[type].plugins[plugin].config[uiLibrary]) {
                $.extend(true, config, gj[type].plugins[plugin].config[uiLibrary]);
            }
            if (gj[type].plugins[plugin].config[iconsLibrary]) {
                $.extend(true, config, gj[type].plugins[plugin].config[iconsLibrary]);
            }
        }
    }

    $.extend(true, config, clientConfig);

    if (!config.guid) {
        config.guid = this.generateGUID();
    }

    return config;
}

gj.widget.prototype.getHTMLConfig = function () {
    var result = this.data(),
        attrs = this[0].attributes;
    if (attrs['width']) {
        result.width = attrs['width'].nodeValue;
    }
    if (attrs['height']) {
        result.height = attrs['height'].nodeValue;
    }
    if (attrs['align']) {
        result.align = attrs['align'].nodeValue;
    }
    if (result && result.source) {
        result.dataSource = result.source;
        delete result.source;
    }
    return result;
};

gj.widget.prototype.createDoneHandler = function () {
    var $widget = this;
    return function (response) {
        if (typeof (response) === 'string' && JSON) {
            response = JSON.parse(response);
        }
        gj[$widget.data('type')].methods.render($widget, response);
    };
};

gj.widget.prototype.createErrorHandler = function () {
    var $widget = this;
    return function (response) {
        if (response && response.statusText && response.statusText !== 'abort') {
            alert(response.statusText);
        }
    };
};

gj.widget.prototype.reload = function (params) {
    var ajaxOptions, result, data = this.data(), type = this.data('type');
    if (data.dataSource === undefined) {
        gj[type].methods.useHtmlDataSource(this, data);
    }
    $.extend(data.params, params);
    console.log(JSON.stringify(data.params));
    if ($.isArray(data.dataSource)) {
        result = gj[type].methods.filter(this);
        gj[type].methods.render(this, result);
    } else if (typeof(data.dataSource) === 'string') {
        ajaxOptions = { url: data.dataSource, data: data.params };
        if (this.xhr) {
            this.xhr.abort();
        }
        this.xhr = $.ajax(ajaxOptions).done(this.createDoneHandler()).fail(this.createErrorHandler());
    } else if (typeof (data.dataSource) === 'object') {
        if (!data.dataSource.data) {
            data.dataSource.data = {};
        }
        $.extend(data.dataSource.data, data.params);
        ajaxOptions = $.extend(true, {}, data.dataSource); //clone dataSource object
        if (ajaxOptions.dataType === 'json' && typeof(ajaxOptions.data) === 'object') {
            ajaxOptions.data = JSON.stringify(ajaxOptions.data);
        }
        if (!ajaxOptions.success) {
            ajaxOptions.success = this.createDoneHandler();
        }
        if (!ajaxOptions.error) {
            ajaxOptions.error = this.createErrorHandler();
        }
        if (this.xhr) {
            this.xhr.abort();
        }
        this.xhr = $.ajax(ajaxOptions);
    }
    return this;
}

gj.documentManager = {
    events: {},

    subscribeForEvent: function (eventName, widgetId, callback) {
        if (!gj.documentManager.events[eventName] || gj.documentManager.events[eventName].length === 0) {
            gj.documentManager.events[eventName] = [{ widgetId: widgetId, callback: callback }];
            $(document).on(eventName, gj.documentManager.executeCallbacks);
        } else if (!gj.documentManager.events[eventName][widgetId]) {
            gj.documentManager.events[eventName].push({ widgetId: widgetId, callback: callback });
        } else {
            throw 'Event ' + eventName + ' for widget with guid="' + widgetId + '" is already attached.';
        }
    },

    executeCallbacks: function (e) {
        var callbacks = gj.documentManager.events[e.type];
        if (callbacks) {
            for (var i = 0; i < callbacks.length; i++) {
                callbacks[i].callback(e);
            }
        }
    },

    unsubscribeForEvent: function (eventName, widgetId) {
        var success = false,
            events = gj.documentManager.events[eventName];
        if (events) {
            for (var i = 0; i < events.length; i++) {
                if (events[i].widgetId === widgetId) {
                    events.splice(i, 1);
                    success = true;
                    if (events.length === 0) {
                        $(document).off(eventName);
                        delete gj.documentManager.events[eventName];
                    }
                }
            }
        }
        if (!success) {
            throw 'The "' + eventName + '" for widget with guid="' + widgetId + '" can\'t be removed.';
        }
    }
};

/**
  * @widget Core
  * @plugin Base
  */
gj.core = {
    /** 
     * @method
     * @example String.1
     * <div id="date"></div>
     * <script>
     *     $('#date').text(gj.core.parseDate('02/03/17', 'mm/dd/yy'));
     * </script>
     * @example String.2
     * <div id="date"></div>
     * <script>
     *     $('#date').text(gj.core.parseDate('2017 2.3', 'yyyy m.d'));
     * </script>
     * @example ASP.NET.JSON.Date
     * <div id="date"></div>
     * <script>
     *     $('#date').text(gj.core.parseDate("\/Date(349653600000)\/"));
     * </script>
     * @example UNIX.Timestamp
     * <div id="date"></div>
     * <script>
     *     $('#date').text(gj.core.parseDate(349653600000));
     * </script>

     */
    parseDate: function (value, format) {
        var i, date, month, year, dateParts, formatParts, result;

        if (value && typeof value === 'string') {
            if (/^\d+$/.test(value)) {
                result = new Date(value);
            } else if (value.indexOf('/Date(') > -1) {
                result = new Date(parseInt(value.substr(6), 10));
            } else if (value) {
                dateParts = value.split(/[\s,-\.//\:]+/);
                formatParts = format.split(/[\s,-\.//\:]+/);
                for (i = 0; i < formatParts.length; i++) {
                    if (['d', 'dd'].indexOf(formatParts[i]) > -1) {
                        date = parseInt(dateParts[i], 10);
                    } else if (['m', 'mm'].indexOf(formatParts[i]) > -1) {
                        month = parseInt(dateParts[i], 10);
                    } else if (['yy', 'yyyy'].indexOf(formatParts[i]) > -1) {
                        year = parseInt(dateParts[i], 10);
                        if (formatParts[i] === 'yy') {
                            year += 2000;
                        }
                    }
                }
                result = new Date(year, month - 1, date);
            }
        } else if (typeof value === 'number') {
            result = new Date(value);
        } else if (value instanceof Date) {
            result = value;
        }

        return result;
    },

    /** 
     * @method
     * @example Sample.1
     * <div id="date"></div>
     * <script>
     *     $('#date').text(gj.core.formatDate(new Date(2017, 1, 3), 'mm/dd/yy'));
     * </script>
     * @example Sample.2
     * <div id="date"></div>
     * <script>
     *     $('#date').text(gj.core.formatDate(new Date(2017, 1, 3), 'yyyy m.d'));
     * </script>
     * @example Sample.3
     * <div id="date"></div>
     * <script>
     *     $('#date').text(gj.core.formatDate(new Date(2017, 1, 3, 20, 43, 53), 'hh:MM:ss tt mm/dd/yyyy'));
     * </script>
     * @example Sample.4
     * <div id="date"></div>
     * <script>
     *     $('#date').text(gj.core.formatDate(new Date(2017, 1, 3, 20, 43, 53), 'hh:MM TT'));
     * </script>
     */
    formatDate: function (date, format) {
        var result = '', separator, tmp,
            formatParts = format.split(/[\s,-\.//\:]+/),
            separators = format.replace(/[shtdmyHTDMY]/g, ''),
            pad = function (val, len) {
                val = String(val);
                len = len || 2;
                while (val.length < len) {
                    val = '0' + val;
                }
                return val;
            };

        for (i = 0; i < formatParts.length; i++) {
            separator = (separators[i] || '');
            switch (formatParts[i]) {
                case 's':
                    result += date.getSeconds() + separator;
                    break;
                case 'ss':
                    result += pad(date.getSeconds()) + separator;
                    break;
                case 'M':
                    result += date.getMinutes() + separator;
                    break;
                case 'MM':
                    result += pad(date.getMinutes()) + separator;
                    break;
                case 'H':
                    result += date.getHours() + separator;
                    break;
                case 'HH':
                    result += pad(date.getHours()) + separator;
                    break;
                case 'h':
                    tmp = date.getHours() > 12 ? date.getHours() % 12 : date.getHours();
                    result += tmp + separator;
                    break;
                case 'hh':
                    tmp = date.getHours() > 12 ? date.getHours() % 12 : date.getHours();
                    result += pad(tmp) + separator;
                    break;
                case 'tt':
                    result += (date.getHours() >= 12 ? 'pm' : 'am') + separator;
                    break;
                case 'TT':
                    result += (date.getHours() >= 12 ? 'PM' : 'AM') + separator;
                    break;
                case 'd':
                    result += date.getDate() + separator;
                    break;
                case 'dd' :
                    result += pad(date.getDate()) + separator;
                    break;
                case 'm' :
                    result += (date.getMonth() + 1) + separator;
                    break;
                case 'mm' :
                    result += pad(date.getMonth() + 1) + separator;
                    break;
                case 'yy' :
                    result += date.getFullYear().toString().substr(2) + separator;
                    break;
                case 'yyyy':
                    result += date.getFullYear() + separator;
                    break;
            }
        }

        return result;
    }
};
if (typeof (gj.dialog) === 'undefined') {
    gj.dialog = {
        plugins: {},
        messages: []
    };
}

gj.dialog.messages['en-us'] = {
    Close: 'Close',
    DefaultTitle: 'Dialog'
};
/* global window alert jQuery */
/** 
 * @widget Dialog 
 * @plugin Base
 */
gj.dialog.config = {
    base: {
        /** If set to true, the dialog will automatically open upon initialization.
         * If false, the dialog will stay hidden until the open() method is called.
         * @type boolean
         * @default true
         * @example True <!-- dialog.base, draggable.base -->
         * <div id="dialog">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         * <script>
         *     $("#dialog").dialog({
         *         autoOpen: true
         *     });
         * </script>
         * @example False <!-- dialog.base, bootstrap -->
         * <div id="dialog" style="display: none">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         * <button onclick="dialog.open()">Open Dialog</button>
         * <script>
         *     var dialog = $("#dialog").dialog({
         *         uiLibrary: 'bootstrap',
         *         autoOpen: false
         *     });
         * </script>
         */
        autoOpen: true,

        /** Specifies whether the dialog should close when it has focus and the user presses the escape (ESC) key.
         * @type boolean
         * @default true
         * @example True <!-- dialog.base -->
         * <div id="dialog">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         * <script>
         *     $("#dialog").dialog({
         *         closeOnEscape: true
         *     });
         * </script>
         * @example False <!-- dialog.base, draggable.base -->
         * <div id="dialog" style="display: none">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         * <button onclick="dialog.open()">Open Dialog</button>
         * <script>
         *     var dialog = $("#dialog").dialog({
         *         closeOnEscape: false
         *     });
         * </script>
         */
        closeOnEscape: true,

        /** Specifies whether the dialog should have a close button in right part of dialog header.
         * @type boolean
         * @default true
         * @example True <!-- dialog.base, draggable.base -->
         * <div id="dialog">
         *     <div data-role="header"><h4 data-role="title">Dialog</h4></div>
         *     <div data-role="body">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         *     <div data-role="footer">
         *         <button onclick="dialog.close()" class="gj-button-md">Ok</button>
         *         <button onclick="dialog.close()" class="gj-button-md">Cancel</button>
         *     </div>
         * </div>
         * <script>
         *     var dialog = $("#dialog").dialog({
         *         closeButtonInHeader: true,
         *         height: 200
         *     });
         * </script>
         * @example False <!-- dialog.base, draggable.base -->
         * <div id="dialog">
         *     <div data-role="header"><h4 data-role="title">Dialog</h4></div>
         *     <div data-role="body">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         *     <div data-role="footer">
         *         <button onclick="dialog.close()" class="gj-button-md">Ok</button>
         *         <button onclick="dialog.close()" class="gj-button-md">Cancel</button>
         *     </div>
         * </div>
         * <script>
         *     var dialog = $("#dialog").dialog({
         *         closeButtonInHeader: false
         *     });
         * </script>
         */
        closeButtonInHeader: true,

        /** If set to true, the dialog will be draggable by the title bar.
         * @type boolean
         * @default true
         * @example True <!-- draggable.base, dialog.base -->
         * <div id="dialog">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         * <script>
         *     $("#dialog").dialog({
         *         draggable: true
         *     });
         * </script>
         * @example False <!-- draggable.base, dialog.base -->
         * <div id="dialog">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         * <script>
         *     $("#dialog").dialog({
         *         draggable: false
         *     });
         * </script>
         */
        draggable: true,

        /** The height of the dialog.
         * @additionalinfo Support string and number values. The number value sets the height in pixels.
         * The only supported string value is "auto" which will allow the dialog height to adjust based on its content.
         * @type (number|string)
         * @default "auto"
         * @example Short.Text <!-- draggable.base, dialog.base -->
         * <div id="dialog">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         * <script>
         *     $("#dialog").dialog({
         *         height: 200
         *     });
         * </script>
         * @example Long.Text.Material.Design <!-- draggable.base, dialog.base -->
         * <div id="dialog">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus porttitor quam in magna vulputate, vitae laoreet odio ultrices. Phasellus at efficitur magna. Mauris purus dolor, egestas quis leo et, vulputate dictum mauris. Vivamus maximus lectus sollicitudin lorem blandit tempor. Maecenas eget posuere mi. Suspendisse id hendrerit nibh. Morbi eu odio euismod, venenatis ipsum in, egestas nunc. Mauris dignissim metus ac risus porta eleifend. Aliquam tempus libero orci, id placerat odio vehicula eu. Donec tincidunt justo dolor, sit amet tempus turpis varius sit amet. Suspendisse ut ex blandit, hendrerit enim tristique, iaculis ipsum. Vivamus venenatis dolor justo, eget scelerisque lacus dignissim quis. Duis imperdiet ex at aliquet cursus. Proin non ultricies leo. Fusce quam diam, laoreet quis fringilla vitae, viverra id magna. Nam laoreet sem in volutpat rhoncus.</div>
         * <script>
         *     $("#dialog").dialog({
         *         height: 350
         *     });
         * </script>
         * @example Long.Text.Bootstrap3 <!-- bootstrap, draggable.base, dialog.base -->
         * <div id="dialog">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus porttitor quam in magna vulputate, vitae laoreet odio ultrices. Phasellus at efficitur magna. Mauris purus dolor, egestas quis leo et, vulputate dictum mauris. Vivamus maximus lectus sollicitudin lorem blandit tempor. Maecenas eget posuere mi. Suspendisse id hendrerit nibh. Morbi eu odio euismod, venenatis ipsum in, egestas nunc. Mauris dignissim metus ac risus porta eleifend. Aliquam tempus libero orci, id placerat odio vehicula eu. Donec tincidunt justo dolor, sit amet tempus turpis varius sit amet. Suspendisse ut ex blandit, hendrerit enim tristique, iaculis ipsum. Vivamus venenatis dolor justo, eget scelerisque lacus dignissim quis. Duis imperdiet ex at aliquet cursus. Proin non ultricies leo. Fusce quam diam, laoreet quis fringilla vitae, viverra id magna. Nam laoreet sem in volutpat rhoncus.</div>
         * <script>
         *     $("#dialog").dialog({
         *         height: 350,
         *         uiLibrary: 'bootstrap'
         *     });
         * </script>
         * @example Long.Text.Bootstrap4 <!-- bootstrap4, draggable.base, dialog.base -->
         * <div id="dialog">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus porttitor quam in magna vulputate, vitae laoreet odio ultrices. Phasellus at efficitur magna. Mauris purus dolor, egestas quis leo et, vulputate dictum mauris. Vivamus maximus lectus sollicitudin lorem blandit tempor. Maecenas eget posuere mi. Suspendisse id hendrerit nibh. Morbi eu odio euismod, venenatis ipsum in, egestas nunc. Mauris dignissim metus ac risus porta eleifend. Aliquam tempus libero orci, id placerat odio vehicula eu. Donec tincidunt justo dolor, sit amet tempus turpis varius sit amet. Suspendisse ut ex blandit, hendrerit enim tristique, iaculis ipsum. Vivamus venenatis dolor justo, eget scelerisque lacus dignissim quis. Duis imperdiet ex at aliquet cursus. Proin non ultricies leo. Fusce quam diam, laoreet quis fringilla vitae, viverra id magna. Nam laoreet sem in volutpat rhoncus.</div>
         * <script>
         *     $("#dialog").dialog({
         *         height: 350,
         *         uiLibrary: 'bootstrap4'
         *     });
         * </script>
         */
        height: 'auto',

        /** The language that needs to be in use.
         * @type string
         * @default 'en-us'
         * @example French.Default <!-- draggable.base, dialog.base-->
         * <script src="../../dist/modular/dialog/js/messages/messages.fr-fr.js"></script>
         * <div id="dialog">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         * <script>
         *     $("#dialog").dialog({
         *         resizable: true,
         *         locale: 'fr-fr'
         *     });
         * </script>
         * @example French.Custom <!-- draggable.base, dialog.base -->
         * <script src="../../dist/modular/dialog/js/messages/messages.fr-fr.js"></script>
         * <div id="dialog">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         * <script>
         *     gj.dialog.messages['fr-fr'].DefaultTitle = 'Titre de la boîte de dialogue';
         *     $("#dialog").dialog({
         *         resizable: true,
         *         locale: 'fr-fr',
         *         width: 700
         *     });
         * </script>
         */
        locale: 'en-us',

        /** The minimum height in pixels to which the dialog can be resized.
         * @type number
         * @default undefined
         * @example sample <!-- draggable.base, dialog.base -->
         * <div id="dialog">The minimum height of this dialog is set to 200 px. Try to resize it for testing.</div>
         * <script>
         *     $("#dialog").dialog({
         *         resizable: true,
         *         height: 300,
         *         minHeight: 200
         *     });
         * </script>
         */
        minHeight: undefined,

        /** The maximum height in pixels to which the dialog can be resized.
         * @type number
         * @default undefined
         * @example sample <!-- draggable.base, dialog.base -->
         * <div id="dialog">The maximum height of this dialog is set to 300 px. Try to resize it for testing.</div>
         * <script>
         *     $("#dialog").dialog({
         *         resizable: true,
         *         height: 200,
         *         maxHeight: 300
         *     });
         * </script>
         */
        maxHeight: undefined,

        /** The width of the dialog.
         * @type number
         * @default 300
         * @example Fixed.Width <!-- draggable.base, dialog.base -->
         * <div id="dialog">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         * <script>
         *     $("#dialog").dialog({
         *         width: 400
         *     });
         * </script>
         * @example Auto.Width <!-- draggable.base, dialog.base -->
         * <div id="dialog" title="Wikipedia">
         *   <img src="https://upload.wikimedia.org/wikipedia/en/thumb/8/80/Wikipedia-logo-v2.svg/1122px-Wikipedia-logo-v2.svg.png" width="420"/>
         * </div>
         * <script>
         *     $("#dialog").dialog({
         *         width: 'auto'
         *     });
         * </script>
         */
        width: 300,

        /** The minimum width in pixels to which the dialog can be resized.
         * @type number
         * @default undefined
         * @example sample <!-- draggable.base, dialog.base -->
         * <div id="dialog">The minimum width of this dialog is set to 200 px. Try to resize it for testing.</div>
         * <script>
         *     $("#dialog").dialog({
         *         resizable: true,
         *         minWidth: 200
         *     });
         * </script>
         */
        minWidth: undefined,

        /** The maximum width in pixels to which the dialog can be resized.
         * @type number
         * @default undefined
         * @example sample <!-- draggable.base, dialog.base -->
         * <div id="dialog">The maximum width of this dialog is set to 400 px. Try to resize it for testing.</div>
         * <script>
         *     $("#dialog").dialog({
         *         resizable: true,
         *         maxWidth: 400
         *     });
         * </script>
         */
        maxWidth: undefined,

        /** If set to true, the dialog will have modal behavior.
         * Modal dialogs create an overlay below the dialog, but above other page elements and you can't interact with them.
         * @type boolean
         * @default false
         * @example True <!-- draggable.base, dialog.base -->
         * <div id="dialog">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         * <script>
         *     $("#dialog").dialog({
         *         modal: true
         *     });
         * </script>
         * @example False <!-- draggable.base, dialog.base, bootstrap -->
         * <div id="dialog">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         * <script>
         *     $("#dialog").dialog({
         *         modal: false
         *     });
         * </script>
         */
        modal: false,

        /** If set to true, the dialog will be resizable.
         * @type boolean
         * @default false
         * @example True <!-- draggable.base, dialog.base -->
         * <div id="dialog">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         * <script>
         *     $("#dialog").dialog({
         *         resizable: true
         *     });
         * </script>
         * @example False <!-- draggable.base, dialog.base -->
         * <div id="dialog">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         * <script>
         *     $("#dialog").dialog({
         *         resizable: false
         *     });
         * </script>
         */
        resizable: false,

        /** If set to true, add vertical scroller to the dialog body.
         * @type Boolean
         * @default false
         * @example Bootstrap.3 <!-- bootstrap, draggable.base, dialog.base -->
         * <div id="dialog">
         *     <div data-role="body">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus porttitor quam in magna vulputate, vitae laoreet odio ultrices. Phasellus at efficitur magna. Mauris purus dolor, egestas quis leo et, vulputate dictum mauris. Vivamus maximus lectus sollicitudin lorem blandit tempor. Maecenas eget posuere mi. Suspendisse id hendrerit nibh. Morbi eu odio euismod, venenatis ipsum in, egestas nunc. Mauris dignissim metus ac risus porta eleifend. Aliquam tempus libero orci, id placerat odio vehicula eu. Donec tincidunt justo dolor, sit amet tempus turpis varius sit amet. Suspendisse ut ex blandit, hendrerit enim tristique, iaculis ipsum. Vivamus venenatis dolor justo, eget scelerisque lacus dignissim quis. Duis imperdiet ex at aliquet cursus. Proin non ultricies leo. Fusce quam diam, laoreet quis fringilla vitae, viverra id magna. Nam laoreet sem in volutpat rhoncus.</div>
         *     <div data-role="footer">
         *         <button class="btn btn-default" data-role="close">Cancel</button>
         *         <button class="btn btn-default" onclick="dialog.close()">OK</button>
         *     </div>
         * </div>
         * <script>
         *     var dialog = $("#dialog").dialog({
         *         scrollable: true,
         *         height: 300,
         *         uiLibrary: 'bootstrap'
         *     });
         * </script>
         * @example Bootstrap.4 <!-- bootstrap4, draggable.base, dialog.base -->
         * <div id="dialog">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus porttitor quam in magna vulputate, vitae laoreet odio ultrices. Phasellus at efficitur magna. Mauris purus dolor, egestas quis leo et, vulputate dictum mauris. Vivamus maximus lectus sollicitudin lorem blandit tempor. Maecenas eget posuere mi. Suspendisse id hendrerit nibh. Morbi eu odio euismod, venenatis ipsum in, egestas nunc. Mauris dignissim metus ac risus porta eleifend. Aliquam tempus libero orci, id placerat odio vehicula eu. Donec tincidunt justo dolor, sit amet tempus turpis varius sit amet. Suspendisse ut ex blandit, hendrerit enim tristique, iaculis ipsum. Vivamus venenatis dolor justo, eget scelerisque lacus dignissim quis. Duis imperdiet ex at aliquet cursus. Proin non ultricies leo. Fusce quam diam, laoreet quis fringilla vitae, viverra id magna. Nam laoreet sem in volutpat rhoncus.</div>
         * <script>
         *     $("#dialog").dialog({
         *         scrollable: true,
         *         height: 300,
         *         uiLibrary: 'bootstrap'
         *     });
         * </script>
         * @example Material.Design <!-- draggable.base, dialog.base -->
         * <div id="dialog">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus porttitor quam in magna vulputate, vitae laoreet odio ultrices. Phasellus at efficitur magna. Mauris purus dolor, egestas quis leo et, vulputate dictum mauris. Vivamus maximus lectus sollicitudin lorem blandit tempor. Maecenas eget posuere mi. Suspendisse id hendrerit nibh. Morbi eu odio euismod, venenatis ipsum in, egestas nunc. Mauris dignissim metus ac risus porta eleifend. Aliquam tempus libero orci, id placerat odio vehicula eu. Donec tincidunt justo dolor, sit amet tempus turpis varius sit amet. Suspendisse ut ex blandit, hendrerit enim tristique, iaculis ipsum. Vivamus venenatis dolor justo, eget scelerisque lacus dignissim quis. Duis imperdiet ex at aliquet cursus. Proin non ultricies leo. Fusce quam diam, laoreet quis fringilla vitae, viverra id magna. Nam laoreet sem in volutpat rhoncus.</div>
         * <script>
         *     $("#dialog").dialog({
         *         scrollable: true,
         *         height: 300,
         *         uiLibrary: 'materialdesign'
         *     });
         * </script>
         */
        scrollable: false,

        /** The title of the dialog. Can be also set through the title attribute of the html element.
         * @type String
         * @default "Dialog"
         * @example Js.Config <!-- draggable.base, dialog.base -->
         * <div id="dialog">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         * <script>
         *     $("#dialog").dialog({
         *         title: 'My Custom Title',
         *         width: 400
         *     });
         * </script>
         * @example Html.Config <!-- draggable.base, dialog.base -->
         * <div id="dialog" title="My Custom Title" width="400">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         * <script>
         *     $("#dialog").dialog();
         * </script>
         */
        title: undefined,

        /** The name of the UI library that is going to be in use. Currently we support Material Design and Bootstrap.
         * @additionalinfo The css file for bootstrap should be manually included if you use bootstrap.
         * @type string (bootstrap|materialdesign)
         * @default undefined
         * @example Bootstrap.3 <!-- draggable.base, dialog.base, bootstrap -->
         * <div id="dialog">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         * <script>
         *     $("#dialog").dialog({
         *         uiLibrary: 'bootstrap'
         *     });
         * </script>
         * @example Bootstrap.4 <!-- draggable.base, dialog.base, bootstrap4 -->
         * <div id="dialog">
         *     <div data-role="body">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
         *     <div data-role="footer">
         *         <button class="btn btn-default" data-role="close">Cancel</button>
         *         <button class="btn btn-default" onclick="dialog.close()">OK</button>
         *     </div>
         * </div>
         * <script>
         *     var dialog = $("#dialog").dialog({
         *         uiLibrary: 'bootstrap4'
         *     });
         * </script>
         * @example Material.Design <!-- draggable.base, dialog.base  -->
         * <div id="dialog">
         *   <div data-role="body">
         *     Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
         *     Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
         *   </div>
         *   <div data-role="footer">
         *     <button class="gj-button-md" onclick="dialog.close()">OK</button>
         *     <button class="gj-button-md" data-role="close">Cancel</button>
         *   </div>
         * </div>
         * <script>
         *     var dialog = $("#dialog").dialog({
         *         uiLibrary: 'materialdesign',
         *         resizable: true
         *     });
         * </script>
         */
        uiLibrary: undefined,

        style: {
            modal: 'gj-modal',
            content: 'gj-dialog-md',
            header: 'gj-dialog-md-header gj-unselectable',
            headerTitle: 'gj-dialog-md-title',
            headerCloseButton: 'gj-dialog-md-close',
            body: 'gj-dialog-md-body',
            footer: 'gj-dialog-footer gj-dialog-md-footer'
        }
    },

    bootstrap: {
        style: {
            modal: 'modal',
            content: 'modal-content gj-dialog-bootstrap',
            header: 'modal-header',
            headerTitle: 'modal-title',
            headerCloseButton: 'close',
            body: 'modal-body',
            footer: 'gj-dialog-footer modal-footer'
        }
    },

    bootstrap4: {
        style: {
            modal: 'modal',
            content: 'modal-content gj-dialog-bootstrap4',
            header: 'modal-header',
            headerTitle: 'modal-title',
            headerCloseButton: 'close',
            body: 'modal-body',
            footer: 'gj-dialog-footer modal-footer'
        }
    }
};
/** 
  * @widget Dialog 
  * @plugin Base
  */
gj.dialog.events = {
    /**
     * Triggered when the dialog is initialized.
     *
     * @event initialized
     * @param {object} e - event data
     * @example sample <!-- draggable.base, dialog.base, bootstrap -->
     * <div id="dialog" style="display: none">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
     * <button onclick="dialog.open()">Open Dialog</button>
     * <script>
     *     var dialog = $("#dialog").dialog({
     *         autoOpen: false,
     *         initialized: function (e) {
     *             alert('The initialized event is fired.');
     *         }
     *     });
     * </script>
     */
    initialized: function ($dialog) {
        $dialog.trigger("initialized");
    },

    /**
     * Triggered before the dialog is opened.
     * @event opening
     * @param {object} e - event data
     * @example sample <!-- draggable.base, dialog.base, bootstrap -->
     * <div id="dialog" style="display: none">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
     * <button onclick="dialog.open()">Open Dialog</button>
     * <script>
     *     var dialog = $("#dialog").dialog({
     *         autoOpen: false,
     *         opening: function (e) {
     *             alert('The opening event is fired.');
     *         },
     *         opened: function (e) {
     *             alert('The opened event is fired.');
     *         }
     *     });
     * </script>
     */
    opening: function ($dialog) {
        $dialog.trigger("opening");
    },

    /**
     * Triggered when the dialog is opened.
     * @event opened
     * @param {object} e - event data
     * @example sample <!-- draggable.base, dialog.base, bootstrap -->
     * <div id="dialog" style="display: none">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
     * <button onclick="dialog.open()">Open Dialog</button>
     * <script>
     *     var dialog = $("#dialog").dialog({
     *         autoOpen: false,
     *         opening: function (e) {
     *             alert('The opening event is fired.');
     *         },
     *         opened: function (e) {
     *             alert('The opened event is fired.');
     *         }
     *     });
     * </script>
     */
    opened: function ($dialog) {
        $dialog.trigger("opened");
    },

    /**
     * Triggered before the dialog is closed.
     * @event closing
     * @param {object} e - event data
     * @example sample <!-- draggable.base, dialog.base, bootstrap -->
     * <div id="dialog" style="display: none">Close the dialog in order to fire closing event.</div>
     * <button onclick="dialog.open()">Open Dialog</button>
     * <script>
     *     var dialog = $("#dialog").dialog({
     *         autoOpen: false,
     *         closing: function (e) {
     *             alert('The closing event is fired.');
     *         },
     *         closed: function (e) {
     *             alert('The closed event is fired.');
     *         }
     *     });
     * </script>
     */
    closing: function ($dialog) {
        $dialog.trigger("closing");
    },

    /**
     * Triggered when the dialog is closed.
     * @event closed
     * @param {object} e - event data
     * @example sample <!-- draggable.base, dialog.base, bootstrap -->
     * <div id="dialog" style="display: none">Close the dialog in order to fire closed event.</div>
     * <button onclick="dialog.open()">Open Dialog</button>
     * <script>
     *     var dialog = $("#dialog").dialog({
     *         autoOpen: false,
     *         closing: function (e) {
     *             alert('The closing event is fired.');
     *         },
     *         closed: function (e) {
     *             alert('The closed event is fired.');
     *         }
     *     });
     * </script>
     */
    closed: function ($dialog) {
        $dialog.trigger("closed");
    },

    /**
     * Triggered while the dialog is being dragged.
     * @event drag
     * @param {object} e - event data
     * @example sample <!-- draggable.base, dialog.base, bootstrap -->
     * <div id="dialog" style="display: none">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
     * <div id="logPanel" class="col-xs-12 well pre-scrollable" style="height: 200px"></div>
     * <script>
     *     var log = $('#logPanel');
     *     $("#dialog").dialog({
     *         drag: function (e) {
     *             log.append('<div class="row">The drag event is fired.</div>');
     *         },
     *         dragStart: function (e) {
     *             log.append('<div class="row">The dragStart event is fired.</div>');
     *         },
     *         dragStop: function (e) {
     *             log.append('<div class="row">The dragStop event is fired.</div>');
     *         }
     *     });
     * </script>
     */
    drag: function ($dialog) {
        $dialog.trigger("drag");
    },

    /**
     * Triggered when the user starts dragging the dialog.
     * @event dragStart
     * @param {object} e - event data
     * @example sample <!-- draggable.base, dialog.base, bootstrap -->
     * <div id="dialog" style="display: none">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
     * <div id="logPanel" class="col-xs-12 well pre-scrollable" style="height: 200px"></div>
     * <script>
     *     var log = $('#logPanel');
     *     $("#dialog").dialog({
     *         drag: function (e) {
     *             log.append('<div class="row">The drag event is fired.</div>');
     *         },
     *         dragStart: function (e) {
     *             log.append('<div class="row">The dragStart event is fired.</div>');
     *         },
     *         dragStop: function (e) {
     *             log.append('<div class="row">The dragStop event is fired.</div>');
     *         }
     *     });
     * </script>
     */
    dragStart: function ($dialog) {
        $dialog.trigger("dragStart");
    },

    /**
     * Triggered after the dialog has been dragged.
     * @event dragStop
     * @param {object} e - event data
     * @example sample <!-- draggable.base, dialog.base, bootstrap -->
     * <div id="dialog" style="display: none">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
     * <div id="logPanel" class="col-xs-12 well pre-scrollable" style="height: 200px"></div>
     * <script>
     *     var log = $('#logPanel');
     *     $("#dialog").dialog({
     *         drag: function (e) {
     *             log.append('<div class="row">The drag event is fired.</div>');
     *         },
     *         dragStart: function (e) {
     *             log.append('<div class="row">The dragStart event is fired.</div>');
     *         },
     *         dragStop: function (e) {
     *             log.append('<div class="row">The dragStop event is fired.</div>');
     *         }
     *     });
     * </script>
     */
    dragStop: function ($dialog) {
        $dialog.trigger("dragStop");
    },

    /**
     * Triggered while the dialog is being resized.
     * @event resize
     * @param {object} e - event data
     * @example sample <!-- draggable.base, dialog.base, bootstrap -->
     * <div id="dialog" style="display: none">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
     * <div id="logPanel" class="col-xs-12 well pre-scrollable" style="height: 200px"></div>
     * <script>
     *     var log = $('#logPanel');
     *     $("#dialog").dialog({
     *         resizable: true,
     *         resize: function (e) {
     *             log.append('<div class="row">The resize event is fired.</div>');
     *         },
     *         resizeStart: function (e) {
     *             log.append('<div class="row">The resizeStart event is fired.</div>');
     *         },
     *         resizeStop: function (e) {
     *             log.append('<div class="row">The resizeStop event is fired.</div>');
     *         }
     *     });
     * </script>
     */
    resize: function ($dialog) {
        $dialog.trigger("resize");
    },

    /**
     * Triggered when the user starts resizing the dialog.
     * @event resizeStart
     * @param {object} e - event data
     * @example sample <!-- draggable.base, dialog.base, bootstrap -->
     * <div id="dialog" style="display: none">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
     * <div id="logPanel" class="col-xs-12 well pre-scrollable" style="height: 200px"></div>
     * <script>
     *     var log = $('#logPanel');
     *     $("#dialog").dialog({
     *         resizable: true,
     *         resize: function (e) {
     *             log.append('<div class="row">The resize event is fired.</div>');
     *         },
     *         resizeStart: function (e) {
     *             log.append('<div class="row">The resizeStart event is fired.</div>');
     *         },
     *         resizeStop: function (e) {
     *             log.append('<div class="row">The resizeStop event is fired.</div>');
     *         }
     *     });
     * </script>
     */
    resizeStart: function ($dialog) {
        $dialog.trigger("resizeStart");
    },

    /**
     * Triggered after the dialog has been resized.
     * @event resizeStop
     * @param {object} e - event data
     * @example sample <!-- draggable.base, dialog.base, bootstrap -->
     * <div id="dialog" style="display: none">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
     * <div id="logPanel" class="col-xs-12 well pre-scrollable" style="height: 200px"></div>
     * <script>
     *     var log = $('#logPanel');
     *     $("#dialog").dialog({
     *         resizable: true,
     *         resize: function (e) {
     *             log.append('<div class="row">The resize event is fired.</div>');
     *         },
     *         resizeStart: function (e) {
     *             log.append('<div class="row">The resizeStart event is fired.</div>');
     *         },
     *         resizeStop: function (e) {
     *             log.append('<div class="row">The resizeStop event is fired.</div>');
     *         }
     *     });
     * </script>
     */
    resizeStop: function ($dialog) {
        $dialog.trigger("resizeStop");
    }
};

gj.dialog.methods = {

    init: function (jsConfig) {
        gj.widget.prototype.init.call(this, jsConfig, 'dialog');

        gj.dialog.methods.localization(this);
        gj.dialog.methods.initialize(this);
        gj.dialog.events.initialized(this);
        return this;
    },

    localization: function($dialog) {
        var data = $dialog.data();
        if (typeof (data.title) === 'undefined') {
            data.title = gj.dialog.messages[data.locale].DefaultTitle;
        }
    },

    getHTMLConfig: function () {
        var result = gj.widget.prototype.getHTMLConfig.call(this),
            attrs = this[0].attributes;
        if (attrs['title']) {
            result.title = attrs['title'].nodeValue;
        }
        return result;
    },

    initialize: function ($dialog) {
        var data = $dialog.data(),
            $header, $body, $footer;

        $dialog.addClass(data.style.content);

        gj.dialog.methods.setSize($dialog);

        if (data.closeOnEscape) {
            $(document).keyup(function (e) {
                if (e.keyCode === 27) {
                    $dialog.close();
                }
            });
        }

        $body = $dialog.children('div[data-role="body"]');
        if ($body.length === 0) {
            $body = $('<div data-role="body"/>').addClass(data.style.body);
            $dialog.wrapInner($body);
        } else {
            $body.addClass(data.style.body);
        }

        $header = gj.dialog.methods.renderHeader($dialog);

        $footer = $dialog.children('div[data-role="footer"]').addClass(data.style.footer);

        $dialog.find('[data-role="close"]').on('click', function () {
            $dialog.close();
        });

        if (data.draggable && $.fn.draggable) {
            gj.dialog.methods.draggable($dialog, $header);
        }

        if (data.resizable && $.fn.draggable) {
            gj.dialog.methods.resizable($dialog);
        }

        if (data.scrollable && data.height) {
            $dialog.addClass('gj-dialog-scrollable');
            $dialog.on('opened', function () {
                var $body = $dialog.children('div[data-role="body"]');
                $body.css('height', data.height - $header.outerHeight() - ($footer.length ? $footer.outerHeight() : 0));
            });            
        }

        gj.dialog.methods.setPosition($dialog);

        if (data.modal) {
            $dialog.wrapAll('<div data-role="modal" class="' + data.style.modal + '"/>');
        }

        if (data.autoOpen) {
            $dialog.open();
        }
    },

    setSize: function ($dialog) {
        var data = $dialog.data();
        if (data.width) {
            $dialog.css("width", data.width);
        }
        if (data.height) {
            $dialog.css("height", data.height);
        }
    },

    renderHeader: function ($dialog) {
        var $header, $title, $closeButton, data = $dialog.data();
        $header = $dialog.children('div[data-role="header"]');
        if ($header.length === 0) {
            $header = $('<div data-role="header" />');
            $dialog.prepend($header);
        }
        $header.addClass(data.style.header);

        $title = $header.find('[data-role="title"]');
        if ($title.length === 0) {
            $title = $('<h4 data-role="title">' + data.title + '</h4>');
            $header.append($title);
        }
        $title.addClass(data.style.headerTitle);

        $closeButton = $header.find('[data-role="close"]');
        if ($closeButton.length === 0 && data.closeButtonInHeader) {
            $closeButton = $('<button type="button" data-role="close" title="' + gj.dialog.messages[data.locale].Close + '"><span>×</span></button>');
            $closeButton.addClass(data.style.headerCloseButton);
            $header.append($closeButton);
        } else if ($closeButton.length > 0 && data.closeButtonInHeader === false) {
            $closeButton.hide();
        } else {
            $closeButton.addClass(data.style.headerCloseButton);
        }

        return $header;
    },

    setPosition: function ($dialog) {
        var left = ($(window).width() / 2) - ($dialog.width() / 2),
            top = ($(window).height() / 2) - ($dialog.height() / 2);
        $dialog.css('position', 'absolute');
        $dialog.css('left', left > 0 ? left : 0);
        $dialog.css('top', top > 0 ? top : 0);
    },

    draggable: function ($dialog, $header) {
        $dialog.appendTo('body');
        $header.addClass('gj-draggable');
        $dialog.draggable({
            handle: $header,
            start: function () {
                $dialog.addClass('gj-unselectable');
                gj.dialog.events.dragStart($dialog);
            },
            stop: function () {
                $dialog.removeClass('gj-unselectable');
                gj.dialog.events.dragStop($dialog);
            }
        });
    },

    resizable: function ($dialog) {
        var config = {
            'drag': gj.dialog.methods.resize,
            'start': function () {
                $dialog.addClass('gj-unselectable');
                gj.dialog.events.resizeStart($dialog);
            },
            'stop': function () {
                this.removeAttribute('style');
                $dialog.removeClass('gj-unselectable');
                gj.dialog.events.resizeStop($dialog);
            }
        };
        $dialog.append($('<div class="gj-resizable-handle gj-resizable-n"></div>').draggable($.extend(true, { horizontal: false }, config)));
        $dialog.append($('<div class="gj-resizable-handle gj-resizable-e"></div>').draggable($.extend(true, { vertical: false }, config)));
        $dialog.append($('<div class="gj-resizable-handle gj-resizable-s"></div>').draggable($.extend(true, { horizontal: false }, config)));
        $dialog.append($('<div class="gj-resizable-handle gj-resizable-w"></div>').draggable($.extend(true, { vertical: false }, config)));
        $dialog.append($('<div class="gj-resizable-handle gj-resizable-ne"></div>').draggable($.extend(true, {}, config)));
        $dialog.append($('<div class="gj-resizable-handle gj-resizable-nw"></div>').draggable($.extend(true, {}, config)));
        $dialog.append($('<div class="gj-resizable-handle gj-resizable-sw"></div>').draggable($.extend(true, {}, config)));
        $dialog.append($('<div class="gj-resizable-handle gj-resizable-se"></div>').draggable($.extend(true, {}, config)));
    },

    resize: function (e, offset) {
        var $el, $dialog, data, height, width, top, left, result = false;

        $el = $(this);
        $dialog = $el.parent();
        data = $dialog.data();

        //TODO: Include margins in the calculations
        if ($el.hasClass('gj-resizable-n')) {
            height = $dialog.height() - offset.top;
            top = $dialog.offset().top + offset.top;
        } else if ($el.hasClass('gj-resizable-e')) {
            width = $dialog.width() + offset.left;
        } else if ($el.hasClass('gj-resizable-s')) {
            height = $dialog.height() + offset.top;
        } else if ($el.hasClass('gj-resizable-w')) {
            width = $dialog.width() - offset.left;
            left = $dialog.offset().left + offset.left;
        } else if ($el.hasClass('gj-resizable-ne')) {
            height = $dialog.height() - offset.top;
            top = $dialog.offset().top + offset.top;
            width = $dialog.width() + offset.left;
        } else if ($el.hasClass('gj-resizable-nw')) {
            height = $dialog.height() - offset.top;
            top = $dialog.offset().top + offset.top;
            width = $dialog.width() - offset.left;
            left = $dialog.offset().left + offset.left;
        } else if ($el.hasClass('gj-resizable-se')) {
            height = $dialog.height() + offset.top;
            width = $dialog.width() + offset.left;
        } else if ($el.hasClass('gj-resizable-sw')) {
            height = $dialog.height() + offset.top;
            width = $dialog.width() - offset.left;
            left = $dialog.offset().left + offset.left;
        }

        if (height && (!data.minHeight || height >= data.minHeight) && (!data.maxHeight || height <= data.maxHeight)) {
            $dialog.height(height);
            if (top) {
                $dialog.css('top', top);
            }
            result = true;
        }

        if (width && (!data.minWidth || width >= data.minWidth) && (!data.maxWidth || width <= data.maxWidth)) {
            $dialog.width(width);
            if (left) {
                $dialog.css('left', left);
            }
            result = true;
        }

        if (result) {
            gj.dialog.events.resize($dialog);
        }
        
        return result;
    },

    open: function ($dialog, title) {
        var $footer;
        gj.dialog.events.opening($dialog);
        $dialog.css('display', 'block');
        $dialog.closest('div[data-role="modal"]').css('display', 'block');
        $footer = $dialog.children('div[data-role="footer"]');
        if ($footer.length && $footer.outerHeight()) {
            $dialog.children('div[data-role="body"]').css('margin-bottom', $footer.outerHeight());
        }
        if (title !== undefined) {
            $dialog.find('[data-role="title"]').html(title);
        }
        gj.dialog.events.opened($dialog);
        return $dialog;
    },

    close: function ($dialog) {
        if ($dialog.is(':visible')) {
            gj.dialog.events.closing($dialog);
            $dialog.css('display', 'none');
            $dialog.closest('div[data-role="modal"]').css('display', 'none');
            gj.dialog.events.closed($dialog);
        }
        return $dialog;
    },

    isOpen: function ($dialog) {
        return $dialog.is(':visible');
    },

    destroy: function ($dialog, keepHtml) {
        var data = $dialog.data();
        if (data) {
            if (keepHtml === false) {
                $dialog.remove();
            } else {
                $dialog.close();
                $dialog.off();
                $dialog.removeData();
                $dialog.removeAttr('data-type');
                $dialog.removeClass(data.style.content);
                $dialog.find('[data-role="header"]').removeClass(data.style.header);
                $dialog.find('[data-role="title"]').removeClass(data.style.headerTitle);
                $dialog.find('[data-role="close"]').remove();
                $dialog.find('[data-role="body"]').removeClass(data.style.body);
                $dialog.find('[data-role="footer"]').removeClass(data.style.footer);
            }
            
        }
        return $dialog;
    }
};
/** 
  * @widget Dialog 
  * @plugin Base
  */
gj.dialog.widget = function ($element, jsConfig) {
    var self = this,
        methods = gj.dialog.methods;

    /**
     * Opens the dialog.
     * @method
     * @param {String} title - The dialog title.
     * @fires opening, opened
     * @return dialog
     * @example Sample <!-- draggable.base, dialog.base, bootstrap -->
     * <div id="dialog" style="display: none">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
     * <button onclick="dialog.open()">Open Dialog</button>
     * <script>
     *     var dialog = $('#dialog').dialog({
     *         autoOpen: false
     *     });
     * </script>
     * @example Title <!-- draggable.base, dialog.base, bootstrap -->
     * <div id="dialog" style="display: none">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
     * <button onclick="dialog.open('Custom Text')">Open Dialog</button>
     * <script>
     *     var dialog = $('#dialog').dialog({
     *         autoOpen: false
     *     });
     * </script>
     */
    self.open = function (title) {
        return methods.open(this, title);
    }

    /**
     * Close the dialog.
     * @method
     * @fires closing, closed
     * @return dialog
     * @example sample <!-- draggable.base, dialog.base, bootstrap -->
     * <div id="dialog" style="display: none">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
     * <button onclick="dialog.open()">Open Dialog</button>
     * <button onclick="dialog.close()">Close Dialog</button>
     * <script>
     *     var dialog = $('#dialog').dialog();
     * </script>
     */
    self.close = function () {
        return methods.close(this);
    }

    /**
     * Check if the dialog is currently open.
     * @method
     * @return boolean
     * @example sample <!-- draggable.base, dialog.base, bootstrap -->
     * <div id="dialog" style="display: none">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
     * <button onclick="dialog.open()">Open Dialog</button>
     * <button onclick="dialog.close()">Close Dialog</button>
     * <button onclick="alert($('#dialog').dialog('isOpen'))">isOpen</button>
     * <script>
     *     var dialog = $('#dialog').dialog();
     * </script>
     */
    self.isOpen = function () {
        return methods.isOpen(this);
    }

    /**
     * Destroy the dialog.
     * @method
     * @param {boolean} keepHtml - If this flag is set to false, the dialog html markup will be removed from the HTML dom tree.
     * @return void
     * @example Keep.HTML.Markup <!-- draggable.base, dialog.base -->
     * <div id="dialog" style="display: none">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
     * <button onclick="create()">Create</button>
     * <button onclick="dialog.destroy()">Destroy</button>
     * <script>
     *     var dialog;
     *     function create() { 
     *         dialog = $('#dialog').dialog();
     *     }
     * </script>
     * @example Remove.HTML.Markup <!-- draggable.base, dialog.base -->
     * <div id="dialog" style="display: none">Lorem ipsum dolor sit amet, consectetur adipiscing elit...</div>
     * <button onclick="create()">Create</button>
     * <button onclick="dialog.destroy(false)">Destroy</button>
     * <script>
     *     var dialog;
     *     function create() {
     *         if ($('#dialog').length === 0) {
     *             alert('The dialog can not be created.');
     *         } else {
     *             dialog = $('#dialog').dialog();
     *         }
     *     }
     * </script>
     */
    self.destroy = function (keepHtml) {
        return methods.destroy(this, keepHtml);
    }

    $.extend($element, self);
    if ('dialog' !== $element.attr('data-type')) {
        methods.init.call($element, jsConfig);
    }

    return $element;
};

gj.dialog.widget.prototype = new gj.widget();
gj.dialog.widget.constructor = gj.dialog.widget;

gj.dialog.widget.prototype.getHTMLConfig = gj.dialog.methods.getHTMLConfig;

(function ($) {
    $.fn.dialog = function (method) {
        var $widget;        
        if (this && this.length) {
            if (typeof method === 'object' || !method) {
                return new gj.dialog.widget(this, method);
            } else {
                $widget = new gj.dialog.widget(this, null);
                if ($widget[method]) {
                    return $widget[method].apply(this, Array.prototype.slice.call(arguments, 1));
                } else {
                    throw 'Method ' + method + ' does not exist.';
                }
            }
        }
    };
})(jQuery);
/* global window alert jQuery */
/** 
 * @widget Draggable 
 * @plugin Base
 */
if (typeof (gj.draggable) === 'undefined') {
    gj.draggable = {};
}

gj.draggable.config = {
    base: {
        /** If specified, restricts dragging from starting unless the mousedown occurs on the specified element.
         * Only elements that descend from the draggable element are permitted.
         * @type jquery element
         * @default undefined
         * @example sample <!-- draggable.base -->
         * <style>
         * .element { border: 1px solid #999; width: 300px; height: 200px; }
         * .handle { background-color: #DDD; cursor: move; width: 200px; margin: 5px auto 0px auto; text-align: center; padding: 5px; }
         * </style>
         * <div id="element" class="element">
         *   <div id="handle" class="handle">Handle for dragging</div>
         * </div>
         * <script>
         *     $('#element').draggable({
         *         handle: $('#handle')
         *     });
         * </script>
         */
        handle: undefined,

        /** If set to false, restricts dragging on vertical direction.
         * @type Boolean
         * @default true
         * @example sample <!-- draggable.base -->
         * <style>
         * .element { border: 1px solid #999; width: 300px; height: 200px; cursor: move; text-align: center; background-color: #DDD; }
         * </style>
         * <div id="element" class="element">
         *     drag me<br/>
         *     <i>(dragging on vertical direction is disabled)</i>
         * </div>
         * <script>
         *     $('#element').draggable({
         *         vertical: false
         *     });
         * </script>
         */
        vertical: true,

        /** If set to false, restricts dragging on horizontal direction.
         * @type Boolean
         * @default true
         * @example sample <!-- draggable.base -->
         * <style>
         * .element { border: 1px solid #999; width: 300px; height: 200px; cursor: move; text-align: center; background-color: #DDD; }
         * </style>
         * <div id="element" class="element">
         *     drag me<br/>
         *     <i>(dragging on horizontal direction is disabled)</i>
         * </div>
         * <script>
         *     $('#element').draggable({
         *         horizontal: false
         *     });
         * </script>
         */
        horizontal: true
    }
};

gj.draggable.methods = {
    init: function (jsConfig) {
        var $handleEl, $dragEl = this;

        gj.widget.prototype.init.call(this, jsConfig, 'draggable');
        $dragEl.attr('data-draggable', 'true');

        $handleEl = gj.draggable.methods.getHandleElement($dragEl);

        $handleEl.on('touchstart mousedown', function (e) {
            $dragEl.attr('data-draggable-dragging', true);
            $dragEl.removeAttr('data-draggable-x').removeAttr('data-draggable-y');
            $dragEl.css('position', 'absolute');
            gj.documentManager.subscribeForEvent('touchmove', $dragEl.data('guid'), gj.draggable.methods.createMoveHandler($dragEl));
            gj.documentManager.subscribeForEvent('mousemove', $dragEl.data('guid'), gj.draggable.methods.createMoveHandler($dragEl));
        });

        gj.documentManager.subscribeForEvent('mouseup', $dragEl.data('guid'), gj.draggable.methods.createUpHandler($dragEl));
        gj.documentManager.subscribeForEvent('touchend', $dragEl.data('guid'), gj.draggable.methods.createUpHandler($dragEl));
        gj.documentManager.subscribeForEvent('touchcancel', $dragEl.data('guid'), gj.draggable.methods.createUpHandler($dragEl));

        return $dragEl;
    },

    getHandleElement: function ($dragEl) {
        var $handle = $dragEl.data('handle');
        return ($handle && $handle.length) ? $handle : $dragEl;
    },

    createUpHandler: function ($dragEl) {
        return function (e) {
            if ($dragEl.attr('data-draggable-dragging') === 'true') {
                $dragEl.attr('data-draggable-dragging', false);
                gj.documentManager.unsubscribeForEvent('mousemove', $dragEl.data('guid'));
                gj.documentManager.unsubscribeForEvent('touchmove', $dragEl.data('guid'));
                gj.draggable.events.stop($dragEl, { left: $dragEl.mouseX(e), top: $dragEl.mouseY(e) });
            }
        };
    },

    createMoveHandler: function ($dragEl) {
        return function (e) {
            var x, y, offsetX, offsetY, prevX, prevY;
            if ($dragEl.attr('data-draggable-dragging') === 'true') {
                x = $dragEl.mouseX(e);
                y = $dragEl.mouseY(e);
                prevX = $dragEl.attr('data-draggable-x');
                prevY = $dragEl.attr('data-draggable-y');
                if (prevX && prevY) {                
                    offsetX = $dragEl.data('horizontal') ? x - parseInt(prevX, 10) : 0;
                    offsetY = $dragEl.data('vertical') ? y - parseInt(prevY, 10) : 0;
                    if (false !== gj.draggable.events.drag($dragEl, offsetX, offsetY, x, y)) {
                        gj.draggable.methods.move($dragEl, offsetX, offsetY);
                    }
                } else {
                    gj.draggable.events.start($dragEl, x, y);
                }
                $dragEl.attr('data-draggable-x', x);
                $dragEl.attr('data-draggable-y', y);
            }
        }
    },

    move: function ($dragEl, offsetX, offsetY) {
        var target = $dragEl.get(0),
            top = target.style.top ? parseInt(target.style.top) : $dragEl.position().top,
            left = target.style.left ? parseInt(target.style.left) : $dragEl.position().left;
        target.style.top = (top + offsetY) + 'px';
        target.style.left = (left + offsetX) + 'px';
    },

    destroy: function ($dragEl) {
        if ($dragEl.attr('data-draggable') === 'true') {
            gj.documentManager.unsubscribeForEvent('mouseup', $dragEl.data('guid'));
            $dragEl.removeData();
            $dragEl.removeAttr('data-guid');
            $dragEl.removeAttr('data-draggable');
            $dragEl.off('drag').off('start').off('stop');
            gj.draggable.methods.getHandleElement($dragEl).off('mousedown');
        }
        return $dragEl;
    }
};

gj.draggable.events = {
    /**
     * Triggered while the mouse is moved during the dragging, immediately before the current move happens.
     *
     * @event drag
     * @param {object} e - event data
     * @param {object} offset - Current offset position as { top, left } object.
     * @param {object} mousePosition - Current mouse position as { top, left } object.
     * @example sample <!-- draggable.base -->
     * <style>
     * .element { border: 1px solid #999; width: 300px; height: 200px; cursor: move; text-align: center; background-color: #DDD; }
     * </style>
     * <div id="element" class="element gj-unselectable">drag me</div>
     * <script>
     *     $('#element').draggable({
     *         drag: function (e, offset, mousePosition) {
     *             $('body').append('<div>The drag event is fired. offset { top:' + offset.top + ', left: ' + offset.left + '}.</div>');
     *         }
     *     });
     * </script>
     */
    drag: function ($dragEl, offsetX, offsetY, mouseX, mouseY) {
        return $dragEl.triggerHandler('drag', [{ top: offsetY, left: offsetX }, { top: mouseY, left: mouseX }]);
    },

    /**
     * Triggered when dragging starts.
     *
     * @event start
     * @param {object} e - event data
     * @example sample <!-- draggable.base -->
     * <style>
     * .element { border: 1px solid #999; width: 300px; height: 200px; cursor: move; text-align: center; background-color: #DDD; }
     * </style>
     * <div id="element" class="element gj-unselectable">
     *   drag me
     * </div>
     * <script>
     *     $('#element').draggable({
     *         start: function (e, mousePosition) {
     *             $('body').append('<div>The start event is fired. mousePosition { top:' + mousePosition.top + ', left: ' + mousePosition.left + '}.</div>');
     *         }
     *     });
     * </script>
     */
    start: function ($dragEl, mouseX, mouseY) {
        $dragEl.triggerHandler('start', [{ top: mouseY, left: mouseX }]);
    },

    /**
     * Triggered when dragging stops.
     *
     * @event stop
     * @param {object} e - event data
     * @param {object} mousePosition - Current mouse position as { top, left } object.
     * @example sample <!-- draggable.base -->
     * <style>
     * .element { border: 1px solid #999; width: 300px; height: 200px; cursor: move; text-align: center; background-color: #DDD; }
     * </style>
     * <div id="element" class="element gj-unselectable">
     *   drag me
     * </div>
     * <script>
     *     $('#element').draggable({
     *         stop: function (e, offset) {
     *             $('body').append('<div>The stop event is fired.</div>');
     *         }
     *     });
     * </script>
     */
    stop: function ($dragEl, mousePosition) {
        $dragEl.triggerHandler('stop', [mousePosition]);
    }
};

gj.draggable.widget = function ($element, jsConfig) {
    var self = this,
        methods = gj.draggable.methods;

    if (!$element.destroy) {
        /** Remove draggable functionality from the element.
         * @method
         * @return jquery element
         * @example sample <!-- draggable.base -->
         * <style>
         * .element { border: 1px solid #999; width: 300px; height: 200px; cursor: move; text-align: center; background-color: #DDD; }
         * </style>
         * <button onclick="dragEl.destroy()">Destroy</button>
         * <div id="element" class="element">Drag Me</div>
         * <script>
         *     var dragEl = $('#element').draggable();
         * </script>
         */
        self.destroy = function () {
            return methods.destroy(this);
        };
    }

    $.extend($element, self);
    if ('true' !== $element.attr('data-draggable')) {
        methods.init.call($element, jsConfig);
    }

    return $element;
};

gj.draggable.widget.prototype = new gj.widget();
gj.draggable.widget.constructor = gj.draggable.widget;

(function ($) {
    $.fn.draggable = function (method) {
        var $widget;        
        if (this && this.length) {
            if (typeof method === 'object' || !method) {
                return new gj.draggable.widget(this, method);
            } else {
                $widget = new gj.draggable.widget(this, null);
                if ($widget[method]) {
                    return $widget[method].apply(this, Array.prototype.slice.call(arguments, 1));
                } else {
                    throw 'Method ' + method + ' does not exist.';
                }
            }
        }
    };
})(jQuery);
/* global window alert jQuery */
/** 
 * @widget Droppable 
 * @plugin Base
 */
if (typeof (gj.droppable) === 'undefined') {
    gj.droppable = {};
}

gj.droppable.config = {
    /** If specified, the class will be added to the droppable while draggable is being hovered over the droppable.
     * @type string
     * @default undefined
     * @example sample <!-- droppable.base, draggable.base -->
     * <style>
     * .draggable { border: 1px solid #999; width: 300px; height: 200px; text-align: center; }
     * .droppable { border: 1px solid #999; width: 300px; height: 200px; text-align: center; }
     * .hover { background-color: #FF0000; }
     * </style>
     * <div id="droppable" class="droppable">Drop Here</div>
     * <div id="draggable" class="draggable">Drag Me</div>
     * <script>
     *     $('#draggable').draggable();
     *     $('#droppable').droppable({ hoverClass: 'hover' });
     * </script>
     */
    hoverClass: undefined
};

gj.droppable.methods = {
    init: function (jsConfig) {
        var $dropEl = this;

        gj.widget.prototype.init.call(this, jsConfig, 'droppable');
        $dropEl.attr('data-droppable', 'true');
        
        gj.documentManager.subscribeForEvent('mousedown', $dropEl.data('guid'), gj.droppable.methods.createMouseDownHandler($dropEl));
        gj.documentManager.subscribeForEvent('mousemove', $dropEl.data('guid'), gj.droppable.methods.createMouseMoveHandler($dropEl));
        gj.documentManager.subscribeForEvent('mouseup', $dropEl.data('guid'), gj.droppable.methods.createMouseUpHandler($dropEl));
        
        return $dropEl;
    },

    createMouseDownHandler: function ($dropEl) {
        return function (e) {
            $dropEl.isDragging = true;
        }
    },

    createMouseMoveHandler: function ($dropEl) {
        return function (e) {
            if ($dropEl.isDragging) {
                var hoverClass = $dropEl.data('hoverClass'),
                    mousePosition = {
                        left: $dropEl.mouseX(e),
                        top: $dropEl.mouseY(e)
                    },
                    newIsOver = gj.droppable.methods.isOver($dropEl, mousePosition);
                if (newIsOver != $dropEl.isOver) {
                    if (newIsOver) {
                        if (hoverClass) {
                            $dropEl.addClass(hoverClass);
                        }
                        gj.droppable.events.over($dropEl, mousePosition);
                    } else {
                        if (hoverClass) {
                            $dropEl.removeClass(hoverClass);
                        }
                        gj.droppable.events.out($dropEl);
                    }
                }
                $dropEl.isOver = newIsOver;
            }
        }
    },

    createMouseUpHandler: function ($dropEl) {
        return function (e) {
            var mousePosition = {
                left: $dropEl.mouseX(e),
                top: $dropEl.mouseY(e)
            };
            $dropEl.isDragging = false;
            if (gj.droppable.methods.isOver($dropEl, mousePosition)) {
                gj.droppable.events.drop($dropEl);
            }
        }
    },

    isOver: function ($dropEl, mousePosition) {
        var offsetTop = $dropEl.offset().top;// + parseInt($dropEl.css("border-top-width")) + parseInt($dropEl.css("margin-top")) + parseInt($dropEl.css("padding-top")),
        offsetLeft = $dropEl.offset().left;// + parseInt($dropEl.css("border-left-width")) + parseInt($dropEl.css("margin-left")) + parseInt($dropEl.css("padding-left"));
        return mousePosition.left > offsetLeft && mousePosition.left < (offsetLeft + $dropEl.outerWidth(true))
            && mousePosition.top > offsetTop && mousePosition.top < (offsetTop + $dropEl.outerHeight(true));
    },

    destroy: function ($dropEl) {
        if ($dropEl.attr('data-droppable') === 'true') {
            gj.documentManager.unsubscribeForEvent('mousedown', $dropEl.data('guid'));
            gj.documentManager.unsubscribeForEvent('mousemove', $dropEl.data('guid'));
            gj.documentManager.unsubscribeForEvent('mouseup', $dropEl.data('guid'));
            $dropEl.removeData();
            $dropEl.removeAttr('data-guid');
            $dropEl.removeAttr('data-droppable');
            $dropEl.off('drop').off('over').off('out');
        }
        return $dropEl;
    }
};

gj.droppable.events = {
    /** Triggered when a draggable element is dropped.
     * @event drop
     * @param {object} e - event data
     * @example sample <!-- droppable.base, draggable.base -->
     * <style>
     * .draggable { border: 1px solid #999; width: 300px; height: 200px; text-align: center; }
     * .droppable { border: 1px solid #999; width: 300px; height: 200px; text-align: center; }
     * .drop { background-color: #FF0000; }
     * </style>
     * <div id="droppable" class="droppable">Drop Here</div>
     * <div id="draggable" class="draggable">Drag Me</div>
     * <script>
     *     $('#draggable').draggable();
     *     $('#droppable').droppable({ drop: function() { $(this).addClass('drop') } });
     * </script>
     */
    drop: function ($dropEl, offsetX, offsetY) {
        $dropEl.trigger('drop', [{ top: offsetY, left: offsetX }]);
    },

    /** Triggered when a draggable element is dragged over the droppable.
     * @event over
     * @param {object} e - event data
     * @param {object} mousePosition - Current mouse position as { top, left } object.
     * @example sample <!-- droppable.base, draggable.base -->
     * <style>
     * .draggable { border: 1px solid #999; width: 300px; height: 200px; text-align: center; }
     * .droppable { border: 1px solid #999; width: 300px; height: 200px; text-align: center; }
     * .hover { background-color: #FF0000; }
     * </style>
     * <div id="droppable" class="droppable">Drop Here</div>
     * <div id="draggable" class="draggable">Drag Me</div>
     * <script>
     *     $('#draggable').draggable();
     *     $('#droppable').droppable({
     *         over: function() { 
     *             $(this).addClass('hover')
     *         },
     *         out: function() {
     *             $(this).removeClass('hover')
     *         }
     *     });
     * </script>
     */
    over: function ($dropEl, mousePosition) {
        $dropEl.trigger('over', [mousePosition]);
    },

    /** Triggered when a draggable element is dragged out of the droppable.
     * @event out
     * @param {object} e - event data
     * @example sample <!-- droppable.base, draggable.base -->
     * <style>
     * .draggable { border: 1px solid #999; width: 300px; height: 200px; text-align: center; }
     * .droppable { border: 1px solid #999; width: 300px; height: 200px; text-align: center; }
     * .hover { background-color: #FF0000; }
     * </style>
     * <div id="droppable" class="droppable">Drop Here</div>
     * <div id="draggable" class="draggable">Drag Me</div>
     * <script>
     *     $('#draggable').draggable();
     *     $('#droppable').droppable({
     *         over: function() { $(this).addClass('hover') },
     *         out: function() { $(this).removeClass('hover') }
     *     });
     * </script>
     */
    out: function ($dropEl) {
        $dropEl.trigger('out');
    }
};

gj.droppable.widget = function ($element, jsConfig) {
    var self = this,
        methods = gj.droppable.methods;

    self.isOver = false;
    self.isDragging = false;

    /** Removes the droppable functionality.
     * @method
     * @return jquery element
     * @example sample <!-- draggable.base, droppable.base -->
     * <button onclick="create()">Create</button>
     * <button onclick="dropEl.destroy()">Destroy</button>
     * <br/><br/>
     * <style>
     * .draggable { border: 1px solid #999; width: 300px; height: 200px; text-align: center; }
     * .droppable { border: 1px solid #999; width: 300px; height: 200px; text-align: center; }
     * .hover { background-color: #FF0000; }
     * </style>
     * <div id="droppable" class="droppable">Drop Here</div>
     * <div id="draggable" class="draggable">Drag Me</div>
     * <script>
     *     var dropEl;
     *     $('#draggable').draggable();
     *     function create() {
     *         dropEl = $('#droppable').droppable({
     *             hoverClass: 'hover'
     *         });
     *     }
     *     create();
     * </script>
     */
    self.destroy = function () {
        return methods.destroy(this);
    }

    self.isOver = function (mousePosition) {
        return methods.isOver(this, mousePosition);
    }

    $.extend($element, self);
    if ('true' !== $element.attr('data-droppable')) {
        methods.init.call($element, jsConfig);
    }

    return $element;
};

gj.droppable.widget.prototype = new gj.widget();
gj.droppable.widget.constructor = gj.droppable.widget;

(function ($) {
    $.fn.droppable = function (method) {
        var $widget;
        if (this && this.length) {
            if (typeof method === 'object' || !method) {
                return new gj.droppable.widget(this, method);
            } else {
                $widget = new gj.droppable.widget(this, null);
                if ($widget[method]) {
                    return $widget[method].apply(this, Array.prototype.slice.call(arguments, 1));
                } else {
                    throw 'Method ' + method + ' does not exist.';
                }
            }
        }
    };
})(jQuery);
if (typeof (gj.grid) === 'undefined') {
    gj.grid = {
        plugins: {},
        messages: []
    };
}

gj.grid.messages['en-us'] = {
    First: 'First',
    Previous: 'Previous',
    Next: 'Next',
    Last: 'Last',
    Page: 'Page',
    FirstPageTooltip: 'First Page',
    PreviousPageTooltip: 'Previous Page',
    NextPageTooltip: 'Next Page',
    LastPageTooltip: 'Last Page',
    Refresh: 'Refresh',
    Of: 'of',
    DisplayingRecords: 'Displaying records',
    RowsPerPage: 'Rows per page:',
    Edit: 'Edit',
    Delete: 'Delete',
    Update: 'Update',
    Cancel: 'Cancel',
    NoRecordsFound: 'No records found.',
    Loading: 'Loading...'
};
/* global window alert jQuery gj */
/**
  * @widget Grid
  * @plugin Base
  */
gj.grid.config = {
    base: {
        /** The data source for the grid.
         * @additionalinfo If set to string, then the grid is going to use this string as a url for ajax requests to the server.<br />
         * If set to object, then the grid is going to use this object as settings for the <a href="http://api.jquery.com/jquery.ajax/" target="_new">jquery ajax</a> function.<br />
         * If set to array, then the grid is going to use the array as data for rows.
         * @type (string|object|array)
         * @default undefined
         * @example Remote.JS.Configuration <!-- grid -->
         * <table id="grid"></table>
         * <script>
         *     $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         columns: [ { field: 'Name' }, { field: 'PlaceOfBirth' } ]
         *     });
         * </script>
         * @example Remote.Html.Configuration <!-- grid -->
         * <table id="grid" data-source="/Players/Get">
         *     <thead>
         *         <tr>
         *             <th width="56" data-field="ID">#</th>
         *             <th>Name</th>
         *             <th>PlaceOfBirth</th>
         *         </tr>
         *     </thead>
         * </table>
         * <script>
         *     $('#grid').grid();
         * </script>
         * @example Local.DataSource <!-- grid -->
         * <table id="grid"></table>
         * <script>
         *     var data = [
         *         { 'ID': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
         *         { 'ID': 2, 'Name': 'Ronaldo Luis Nazario de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
         *         { 'ID': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' }
         *     ];
         *     $('#grid').grid({
         *         dataSource: data,
         *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
         *     });
         * </script>
         * @example Html.DataSource <!-- materialicons, grid -->
         * <table id="grid">
         *     <thead>
         *         <tr>
         *             <th width="56" data-field="ID">#</th>
         *             <th data-sortable="true">Name</th>
         *             <th data-field="PlaceOfBirth" data-sortable="true">Place Of Birth</th>
         *         </tr>
         *     </thead>
         *     <tbody>
         *         <tr>
         *             <td>1</td>
         *             <td>Hristo Stoichkov</td>
         *             <td>Plovdiv, Bulgaria</td>
         *         </tr>
         *         <tr>
         *             <td>2</td>
         *             <td>Ronaldo Luis Nazario de Lima</td>
         *             <td>Rio de Janeiro, Brazil</td>
         *         </tr>
         *         <tr>
         *             <td>3</td>
         *             <td>David Platt</td>
         *             <td>Chadderton, Lancashire, England</td>
         *         </tr>
         *     </tbody>
         * </table>
         * <script>
         *     $('#grid').grid({ pager: { limit: 2 }});
         * </script>
         * @example Remote.Custom.Render <!-- grid -->
         * <table id="grid"></table>
         * <script>
         *     var grid, onSuccessFunc = function (response) {
         *         alert('The result contains ' + response.records.length + ' records.');
         *         grid.render(response);
         *     };
         *     grid = $('#grid').grid({
         *         dataSource: { url: '/Players/Get', data: {}, success: onSuccessFunc },
         *         columns: [ { field: 'Name' }, { field: 'PlaceOfBirth' } ]
         *     });
         * </script>
         * @example Remote.Custom.Error <!-- grid -->
         * <table id="grid"></table>
         * <script>
         *     var grid, onErrorFunc = function (response) {
         *         alert('Server error.');
         *     };
         *     grid = $('#grid').grid({
         *         dataSource: { url: '/DataSources/InvalidUrl', error: onErrorFunc },
         *         columns: [ { field: 'Name' }, { field: 'PlaceOfBirth' } ]
         *     });
         * </script>
         */
        dataSource: undefined,

        /** An array that holds the configurations of each column from the grid.
         * @type array
         * @example JS.Configuration <!-- grid -->
         * <table id="grid"></table>
         * <script>
         *     $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth', name: 'Birth Place' } ]
         *     });
         * </script>
         */
        columns: [],

        /** Auto generate column for each field in the datasource when set to true.
         * @type array
         * @example sample <!-- grid -->
         * <table id="grid"></table>
         * <script>
         *     $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         autoGenerateColumns: true
         *     });
         * </script>
         */
        autoGenerateColumns: false,

        /** An object that holds the default configuration settings of each column from the grid.
         * @type object
         * @example sample <!-- materialicons, grid -->
         * <table id="grid"></table>
         * <script>
         *     $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         defaultColumnSettings: { align: 'right' },
         *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth', name: 'Birth Place' } ]
         *     });
         * </script>
         */
        defaultColumnSettings: {

            /** If set to true the column will not be displayed in the grid. By default all columns are displayed.
             * @alias column.hidden
             * @type boolean
             * @default false
             * @example sample <!-- materialicons, grid -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         columns: [
             *            { field: 'ID', width: 56 },
             *            { field: 'Name' },
             *            { field: 'PlaceOfBirth', hidden: true }
             *        ]
             *     });
             * </script>
             */
            hidden: false,

            /** The width of the column. Numeric values are treated as pixels.
             * If the width is undefined the width of the column is not set and depends on the with of the table(grid).
             * @alias column.width
             * @type number|string
             * @default undefined
             * @example sample <!-- grid -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         columns: [
             *             { field: 'ID', width: 56 },
             *             { field: 'Name', width: 120 },
             *             { field: 'PlaceOfBirth' }
             *         ]
             *     });
             * </script>
             */
            width: undefined,

            /** Indicates if the column is sortable.
             * If set to true the user can click the column header and sort the grid by the column source field.
             * @alias column.sortable
             * @type boolean|object
             * @default false
             * @example Remote <!-- materialicons, grid -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         columns: [
             *             { field: 'ID', width: 56 },
             *             { field: 'Name', sortable: true },
             *             { field: 'PlaceOfBirth', sortable: false }
             *         ]
             *     });
             * </script>
             * @example Local.Custom <!-- materialicons, grid -->
             * <table id="grid"></table>
             * <script>
             *     var data = [
             *         { 'ID': 1, 'Value1': 'Foo', 'Value2': 'Foo' },
             *         { 'ID': 2, 'Value1': 'bar', 'Value2': 'bar' },
             *         { 'ID': 3, 'Value1': 'moo', 'Value2': 'moo' },
             *         { 'ID': 4, 'Value1': null, 'Value2': undefined }
             *     ];
             *     var caseSensitiveSort = function (direction, column) { 
             *         return function (recordA, recordB) {
             *             var a = recordA[column.field] || '',
             *                 b = recordB[column.field] || '';
             *             return (direction === 'asc') ? a < b : b < a;
             *         };
             *     };
             *     $('#grid').grid({
             *         dataSource: data,
             *         columns: [
             *             { field: 'ID' },
             *             { field: 'Value1', sortable: true },
             *             { field: 'Value2', sortable: { sorter: caseSensitiveSort } }
             *         ]
             *     });
             * </script>
             * @example Remote.Bootstrap.3 <!-- bootstrap, grid -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         uiLibrary: 'bootstrap',
             *         dataSource: '/Players/Get',
             *         columns: [
             *             { field: 'ID', width: 34 },
             *             { field: 'Name', sortable: true },
             *             { field: 'PlaceOfBirth', sortable: false }
             *         ]
             *     });
             * @example Remote.Bootstrap.4.Material.Icons <!-- materialicons, bootstrap4, grid -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         uiLibrary: 'bootstrap4',
             *         dataSource: '/Players/Get',
             *         columns: [
             *             { field: 'ID', width: 56 },
             *             { field: 'Name', sortable: true },
             *             { field: 'PlaceOfBirth', sortable: false }
             *         ]
             *     });
             * </script>
             * @example Remote.Bootstrap.4.FontAwesome <!-- bootstrap4, fontawesome, grid -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         uiLibrary: 'bootstrap4',
             *         iconsLibrary: 'fontawesome',
             *         dataSource: '/Players/Get',
             *         columns: [
             *             { field: 'ID', width: 42 },
             *             { field: 'Name', sortable: true },
             *             { field: 'PlaceOfBirth', sortable: false }
             *         ]
             *     });
             * </script>
             */
            sortable: false,

            /** Indicates the type of the column.
             * @alias column.type
             * @type text|checkbox|icon
             * @default 'text'
             * @example Icon <!-- grid, bootstrap -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         uiLibrary: 'bootstrap',
             *         columns: [
             *             { field: 'ID', width: 34 },
             *             { field: 'Name', title: 'Player' },
             *             { field: 'PlaceOfBirth', title: 'Place of Birth' },
             *             {
             *               title: '', field: 'Info', width: 32, type: 'icon', icon: 'glyphicon-info-sign',
             *               events: {
             *                 'click': function (e) {
             *                     alert('record with id=' + e.data.id + ' is clicked.');
             *                 }
             *               }
             *             }
             *         ]
             *     });
             * </script>
             * @example Checkbox <!-- grid, checkbox, bootstrap -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         uiLibrary: 'bootstrap',
             *         columns: [
             *             { field: 'ID', width: 34 },
             *             { field: 'Name', title: 'Player' },
             *             { field: 'PlaceOfBirth', title: 'Place of Birth' },
             *             { title: 'Active?', field: 'IsActive', width: 80, type: 'checkbox', align: 'center' }
             *         ]
             *     });
             * </script>
             */
            type: 'text',

            /** The caption that is going to be displayed in the header of the grid.
             * @alias column.title
             * @type string
             * @default undefined
             * @example sample <!-- grid -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         columns: [
             *             { field: 'ID', width: 56 },
             *             { field: 'Name', title: 'Player' },
             *             { field: 'PlaceOfBirth', title: 'Place of Birth' }
             *         ]
             *     });
             * </script>
             */
            title: undefined,

            /** The field name to which the column is bound.
             * If the column.title is not defined this value is used as column.title.
             * @alias column.field
             * @type string
             * @default undefined
             * @example sample <!-- grid -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         columns: [
             *             { field: 'ID', width: 56 },
             *             { field: 'Name' },
             *             { field: 'PlaceOfBirth', title: 'Place of Birth' }
             *         ]
             *     });
             * </script>
             */
            field: undefined,

            /** This setting control the alignment of the text in the cell.
             * @alias column.align
             * @type left|right|center|justify|initial|inherit
             * @default "left"
             * @example sample <!-- grid -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         columns: [
             *             { field: 'ID', width: 56, align: 'center' },
             *             { field: 'Name', align: 'right' },
             *             { field: 'PlaceOfBirth', align: 'left' }
             *         ]
             *     });
             * </script>
             */
            align: 'left',

            /** The name(s) of css class(es) that are going to be applied to all cells inside that column, except the header cell.
             * @alias column.cssClass
             * @type string
             * @default undefined
             * @example sample <!-- grid -->
             * <table id="grid"></table>
             * <style>
             * .nowrap { white-space: nowrap }
             * .bold { font-weight: bold }
             * </style>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         columns: [
             *             { field: 'ID', width: 56 },
             *             { field: 'Name', width: 100, cssClass: 'nowrap bold' },
             *             { field: 'PlaceOfBirth' }
             *         ]
             *     });
             * </script>
             */
            cssClass: undefined,

            /** The name(s) of css class(es) that are going to be applied to the header cell of that column.
             * @alias column.headerCssClass
             * @type string
             * @default undefined
             * @example sample <!-- grid -->
             * <table id="grid"></table>
             * <style>
             * .italic { font-style: italic }
             * </style>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         columns: [
             *             { field: 'ID', width: 56 },
             *             { field: 'Name', width: 100, headerCssClass: 'italic' },
             *             { field: 'PlaceOfBirth' }
             *         ]
             *     });
             * </script>
             */
            headerCssClass: undefined,

            /** The text for the cell tooltip.
             * @alias column.tooltip
             * @type string
             * @default undefined
             * @example sample <!-- grid -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         columns: [
             *             { field: 'ID', width: 56, tooltip: 'This is my tooltip 1.' },
             *             { field: 'Name', tooltip: 'This is my tooltip 2.' },
             *             { field: 'PlaceOfBirth', tooltip: 'This is my tooltip 3.' }
             *         ]
             *     });
             * </script>
             */
            tooltip: undefined,

            /** Css class for icon that is going to be in use for the cell.
             * This setting can be in use only with combination of type icon.
             * @alias column.icon
             * @type string
             * @default undefined
             * @example sample <!-- bootstrap, grid -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         uiLibrary: 'bootstrap',
             *         columns: [
             *             { field: 'ID', width: 34 },
             *             { field: 'Name' },
             *             { field: 'PlaceOfBirth' },
             *             { title: '', field: 'Edit', width: 32, type: 'icon', icon: 'glyphicon-pencil', events: { 'click': function (e) { alert('name=' + e.data.record.Name); } } }
             *         ]
             *     });
             * </script>
             */
            icon: undefined,

            /** Configuration object with event names as keys and functions as values that are going to be bind to each cell from the column.
             * Each function is going to receive event information as a parameter with info in the "data" field for id, field name and record data.
             * @alias column.events
             * @type object
             * @default undefined
             * @example javascript.configuration <!-- bootstrap, grid -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         uiLibrary: 'bootstrap',
             *         columns: [
             *             { field: 'ID', width: 34 },
             *             {
             *               field: 'Name',
             *               events: {
             *                 'mouseenter': function (e) {
             *                     e.stopPropagation();
             *                     $(e.currentTarget).css('background-color', 'red');
             *                 },
             *                 'mouseleave': function (e) {
             *                     e.stopPropagation();
             *                     $(e.currentTarget).css('background-color', '');
             *                 }
             *               }
             *             },
             *             { field: 'PlaceOfBirth' },
             *             {
             *               title: '', field: 'Info', width: 34, type: 'icon', icon: 'glyphicon-info-sign',
             *               events: {
             *                 'click': function (e) {
             *                     alert('record with id=' + e.data.id + ' is clicked.'); }
             *                 }
             *             }
             *         ]
             *     });
             * </script>
             * @example html.configuration <!-- bootstrap, grid -->
             * <table id="grid" data-source="/Players/Get" data-ui-library="bootstrap">
             *     <thead>
             *         <tr>
             *             <th data-field="ID" width="34">ID</th>
             *             <th data-events="mouseenter: onMouseEnter, mouseleave: onMouseLeave">Name</th>
             *             <th data-field="PlaceOfBirth">Place Of Birth</th>
             *             <th data-events="click: onClick" data-type="icon" data-icon="glyphicon-info-sign" width="32"></th>
             *         </tr>
             *     </thead>
             * </table>
             * <script>
             *     function onMouseEnter (e) {
             *         $(e.currentTarget).css('background-color', 'red');
             *     }
             *     function onMouseLeave (e) {
             *         $(e.currentTarget).css('background-color', '');
             *     }
             *     function onClick(e) {
             *         alert('record with id=' + e.data.id + ' is clicked.');
             *     }
             *     $('#grid').grid();
             * </script>
             */
            events: undefined,

            /** Format the date when the type of the column is date.
             * @additionalinfo <b>d</b> - Day of the month as digits; no leading zero for single-digit days.<br/>
             * <b>dd</b> - Day of the month as digits; leading zero for single-digit days.<br/>
             * <b>m</b> - Month as digits; no leading zero for single-digit months.<br/>
             * <b>mm</b> - Month as digits; leading zero for single-digit months.<br/>
             * <b>yy</b> - Year as last two digits; leading zero for years less than 10.<br/>
             * <b>yyyy</b> - Year represented by four digits.<br/>
             * <b>s</b> - Seconds; no leading zero for single-digit seconds.<br/>
             * <b>ss</b> - Seconds; leading zero for single-digit seconds.<br/>
             * <b>M</b> - Minutes; no leading zero for single-digit minutes. Uppercase MM to avoid conflict with months.<br/>
             * <b>MM</b> - Minutes; leading zero for single-digit minutes. Uppercase MM to avoid conflict with months.<br/>
             * <b>H</b> - Hours; no leading zero for single-digit hours (24-hour clock).<br/>
             * <b>HH</b> - Hours; leading zero for single-digit hours (24-hour clock).<br/>
             * <b>h</b> - Hours; no leading zero for single-digit hours (12-hour clock).<br/>
             * <b>hh</b> - Hours; leading zero for single-digit hours (12-hour clock).<br/>
             * <b>tt</b> - Lowercase, two-character time marker string: am or pm.<br/>
             * <b>TT</b> - Uppercase, two-character time marker string: AM or PM.<br/>
             * @alias column.format
             * @type string
             * @default 'mm/dd/yyyy'
             * @example sample <!-- grid -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         columns: [
             *             { field: 'ID', width: 56 },
             *             { field: 'Name' },
             *             { field: 'DateOfBirth', title: 'Date 1', type: 'date', format: 'HH:MM:ss mm/dd/yyyy' },
             *             { field: 'DateOfBirth', title: 'Date 2', type: 'date' }
             *         ]
             *     });
             * </script>
             */
            format: 'mm/dd/yyyy',

            /** Number of decimal digits after the decimal point.
             * @alias column.decimalDigits
             * @type number
             * @default undefined
             */
            decimalDigits: undefined,

            /** Template for the content in the column.
             * Use curly brackets "{}" to wrap the names of data source columns from server response.
             * @alias column.tmpl
             * @type string
             * @default undefined
             * @example sample <!-- grid -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         columns: [
             *             { field: 'ID', width: 56 },
             *             { field: 'Name' },
             *             { title: 'Info', tmpl: '{Name} is born in {PlaceOfBirth}.' }
             *         ]
             *     });
             * </script>
             */
            tmpl: undefined,

            /** If set to true stop event propagation when event occur.
             * @alias column.stopPropagation
             * @type boolean
             * @default false
             * @example sample <!-- bootstrap, grid -->
             * <table id="grid" data-source="/Players/Get"></table>
             * <script>
             *     $('#grid').grid({
             *         uiLibrary: 'bootstrap',
             *         columns: [
             *             { field: 'ID', width: 34 },
             *             { field: 'Name', events: { 'click': function (e) { alert('name=' + e.data.record.Name); } }  },
             *             { field: 'PlaceOfBirth', stopPropagation: true, events: { 'click': function (e) { alert('name=' + e.data.record.Name); } }   },
             *             { title: '', field: 'Edit', width: 32, type: 'icon', icon: 'glyphicon-pencil', events: { 'click': function (e) { alert('name=' + e.data.record.Name); } } }
             *         ]
             *     });
             * </script>
             */
            stopPropagation: false,

            /** A renderer is an 'interceptor' function which can be used to transform data (value, appearance, etc.) before it is rendered.
             * @additionalinfo If the renderer function return a value, then this value is going to be automatically set as value of the cell.<br/>
             * If the renderer function doesn't return a value, then you have to set the content of the cell manually.
             * @alias column.renderer
             * @type function
             * @default undefined
             * @param {string} value - the record field value
             * @param {object} record - the data of the row record
             * @param {object} $cell - the current table cell presented as jquery object
             * @param {object} $displayEl - inner div element for display of the cell value presented as jquery object
             * @param {string} id - the id of the record
             * @example sample <!-- grid -->
             * <table id="grid" data-source="/Players/Get"></table>
             * <script>
             *     var nameRenderer = function (value, record, $cell, $displayEl) { 
             *         $cell.css('font-style', 'italic'); 
             *         $displayEl.css('background-color', '#EEE');
             *         $displayEl.text(value);
             *     };
             *     $('#grid').grid({
             *         columns: [
             *             { field: 'ID', width: 56 },
             *             { field: 'Name', renderer: nameRenderer },
             *             { field: 'PlaceOfBirth', renderer: function (value, record) { return record.ID % 2 ? '<b>' + value + '</b>' : '<i>' + value + '</i>'; }  }
             *         ]
             *     });
             * </script>
             */
            renderer: undefined,

            /** Function which can be used to customize filtering with local data (javascript sourced data).
             * @additionalinfo The default filtering is not case sensitive. The filtering with remote data sources needs to be handled on the server.
             * @alias column.filter
             * @type function
             * @default undefined
             * @param {string} value - the record field value
             * @param {string} searchStr - the search string
             * @example example <!-- grid -->
             * <input type="text" id="txtValue1" placeholder="Value 1" /> &nbsp;
             * <input type="text" id="txtValue2" placeholder="Value 2" /> &nbsp;
             * <button id="btnSearch">Search</button> <br/><br/>
             * <table id="grid"></table>
             * <script>
             *     var grid, data = [
             *             { 'ID': 1, 'Value1': 'Foo', 'Value2': 'Foo' },
             *             { 'ID': 2, 'Value1': 'bar', 'Value2': 'bar' },
             *             { 'ID': 3, 'Value1': 'moo', 'Value2': 'moo' },
             *             { 'ID': 4, 'Value1': null, 'Value2': undefined }
             *         ],
             *         caseSensitiveFilter = function (value, searchStr) { 
             *             return value.indexOf(searchStr) > -1
             *         };
             *     grid = $('#grid').grid({
             *         dataSource: data,
             *         columns: [
             *             { field: 'ID', width: 56 },
             *             { field: 'Value1' },
             *             { field: 'Value2', filter: caseSensitiveFilter }
             *         ]
             *     });
             *     $('#btnSearch').on('click', function () {
             *         grid.reload({ Value1: $('#txtValue1').val(), Value2: $('#txtValue2').val() });
             *     });
             * </script>
             */
            filter: undefined
        },

        mapping: {
            /** The name of the object in the server response, that contains array with records, that needs to be display in the grid.
             * @alias mapping.dataField
             * @type string
             * @default "records"
             */
            dataField: 'records',

            /** The name of the object in the server response, that contains the number of all records on the server.
             * @alias mapping.totalRecordsField
             * @type string
             * @default "total"
             */
            totalRecordsField: 'total'
        },

        params: {},

        paramNames: {

            /** The name of the parameter that is going to send the name of the column for sorting.
             * The "sortable" setting for at least one column should be enabled in order this parameter to be in use.
             * @alias paramNames.sortBy
             * @type string
             * @default "sortBy"
             */
            sortBy: 'sortBy',

            /** The name of the parameter that is going to send the direction for sorting.
             * The "sortable" setting for at least one column should be enabled in order this parameter to be in use.
             * @alias paramNames.direction
             * @type string
             * @default "direction"
             */
            direction: 'direction'
        },

        /** The name of the UI library that is going to be in use. Currently we support Bootstrap 3, Bootstrap 4 and Material Design.
         * @additionalinfo The css files for Bootstrap or Material Design should be manually included to the page where the grid is in use.
         * @type (materialdesign|bootstrap|bootstrap4)
         * @default 'materialdesign'
         * @example Material.Design.With.Icons <!-- materialicons, dropdown, grid -->
         * <table id="grid"></table>
         * <script>
         *     $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         columns: [ { field: 'ID', width: 56 }, { field: 'Name', sortable: true }, { field: 'PlaceOfBirth' } ],
         *         pager: { limit: 2, sizes: [2, 5, 10, 20] }
         *     });
         * </script>
         * @example Material.Design.Without.Icons <!-- materialicons, grid -->
         * <table id="grid"></table>
         * <script>
         *     $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         uiLibrary: 'materialdesign',
         *         iconsLibrary: '',
         *         columns: [ { field: 'ID', width: 56 }, { field: 'Name', sortable: true }, { field: 'PlaceOfBirth' } ],
         *         pager: { limit: 2, sizes: [2, 5, 10, 20] }
         *     });
         * </script>
         * @example Bootstrap.3 <!-- grid, dropdown, bootstrap -->
         * <div class="container"><table id="grid"></table></div>
         * <script>
         *     $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         uiLibrary: 'bootstrap',
         *         columns: [
         *             { field: 'ID' },
         *             { field: 'Name', sortable: true },
         *             { field: 'PlaceOfBirth' }
         *         ],
         *         pager: { limit: 2, sizes: [2, 5, 10, 20] }
         *     });
         * </script>
         * @example Bootstrap.4.Font.Awesome <!-- bootstrap4, fontawesome, dropdown, grid -->
         * <table id="grid"></table>
         * <script>
         *     $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         uiLibrary: 'bootstrap4',
         *         iconsLibrary: 'fontawesome',
         *         columns: [ { field: 'ID', width: 38 }, { field: 'Name', sortable: true }, { field: 'PlaceOfBirth' } ],
         *         pager: { limit: 2, sizes: [2, 5, 10, 20] }
         *     });
         * </script>
         */
        uiLibrary: 'materialdesign',

        /** The name of the icons library that is going to be in use. Currently we support Material Icons, Font Awesome and Glyphicons.
         * @additionalinfo If you use Bootstrap 3 as uiLibrary, then the iconsLibrary is set to Glyphicons by default.<br/>
         * If you use Material Design as uiLibrary, then the iconsLibrary is set to Material Icons by default.<br/>
         * The css files for Material Icons, Font Awesome or Glyphicons should be manually included to the page where the grid is in use.
         * @type (materialicons|fontawesome|glyphicons)
         * @default 'materialicons'
         * @example Font.Awesome <!-- fontawesome, grid, grid.pagination -->
         * <table id="grid"></table>
         * <script>
         *     $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         iconsLibrary: 'fontawesome',
         *         columns: [ { field: 'ID', width: 56 }, { field: 'Name', sortable: true }, { field: 'PlaceOfBirth' } ],
         *         pager: { limit: 5 }
         *     });
         * </script>
         */
        iconsLibrary: 'materialicons',

        /** The type of the row selection.<br/>
         * If the type is set to multiple the user will be able to select more then one row from the grid.
         * @type (single|multiple)
         * @default 'single'
         * @example Multiple.Material.Design.Checkbox <!-- materialicons, checkbox, grid -->
         * <table id="grid"></table>
         * <script>
         *     $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         selectionType: 'multiple',
         *         selectionMethod: 'checkbox',
         *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
         *     });
         * </script>
         * @example Multiple.Bootstrap.3.Checkbox <!-- bootstrap, checkbox, grid -->
         * <table id="grid"></table>
         * <script>
         *     $('#grid').grid({
         *         primaryKey: 'ID',
         *         uiLibrary: 'bootstrap',
         *         dataSource: '/Players/Get',
         *         selectionType: 'multiple',
         *         selectionMethod: 'checkbox',
         *         columns: [ { field: 'ID', width: 32 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
         *     });
         * </script>
         * @example Multiple.Bootstrap.4.Checkbox <!-- bootstrap4, materialicons, checkbox, grid -->
         * <table id="grid"></table>
         * <script>
         *     $('#grid').grid({
         *         uiLibrary: 'bootstrap4',
         *         dataSource: '/Players/Get',
         *         selectionType: 'multiple',
         *         selectionMethod: 'checkbox',
         *         columns: [ { field: 'ID', width: 34 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
         *     });
         * </script>
         * @example Single.Checkbox <!-- materialicons, checkbox, grid -->
         * <table id="grid"></table>
         * <script>
         *     $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         selectionType: 'single',
         *         selectionMethod: 'checkbox',
         *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
         *     });
         * </script>
         */
        selectionType: 'single',

        /** The type of the row selection mechanism.
         * @additionalinfo If this setting is set to "basic" when the user select a row, then this row will be highlighted.<br/>
         * If this setting is set to "checkbox" a column with checkboxes will appear as first row of the grid and when the user select a row, then this row will be highlighted and the checkbox selected.
         * @type (basic|checkbox)
         * @default "basic"
         * @example sample <!-- materialicons, checkbox, grid -->
         * <table id="grid"></table>
         * <script>
         *     $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         selectionType: 'single',
         *         selectionMethod: 'checkbox',
         *         columns: [ { field: 'ID' }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
         *     });
         * </script>
         */
        selectionMethod: 'basic',

        /** When this setting is enabled the content of the grid will be loaded automatically after the creation of the grid.
         * @type boolean
         * @default true
         * @example disabled <!-- grid -->
         * <table id="grid"></table>
         * <script>
         *     var grid = $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         autoLoad: false,
         *         columns: [ { field: 'ID' }, { field: 'Name' } ]
         *     });
         *     grid.reload(); //call .reload() explicitly in order to load the data in the grid
         * </script>
         * @example enabled <!-- grid -->
         * <table id="grid"></table>
         * <script>
         *     $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         autoLoad: true,
         *         columns: [ { field: 'ID' }, { field: 'Name' } ]
         *     });
         * </script>
         */
        autoLoad: true,

        /** The text that is going to be displayed if the grid is empty.
         * @type string
         * @default "No records found."
         * @example sample <!-- grid -->
         * <table id="grid"></table>
         * <script>
         *     $('#grid').grid({
         *         dataSource: { url: '/Players/Get', data: { name: 'not existing name' } },
         *         notFoundText: 'No records found custom message',
         *         columns: [ { field: 'ID' }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
         *     });
         * </script>
         * @example localization <!-- grid -->
         * <table id="grid"></table>
         * <script src="../../dist/modular/grid/js/messages/messages.de-de.js"></script>
         * <script>
         *     $('#grid').grid({
         *         dataSource: { url: '/Players/Get', data: { name: 'not existing name' } },
         *         locale: 'de-de',
         *         columns: [ { field: 'ID' }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
         *     });
         * </script>
         */
        notFoundText: undefined,

        /** Width of the grid.
         * @type number
         * @default undefined
         * @example sample <!-- grid -->
         * <table id="grid"></table>
         * <script>
         *     $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         width: 400,
         *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
         *     });
         * </script>
         */
        width: undefined,

        /** Minimum width of the grid.
         * @type number
         * @default undefined
         */
        minWidth: undefined,

        /** The size of the font in the grid.
         * @type string
         * @default undefined
         * @example sample <!-- grid -->
         * <table id="grid"></table>
         * <script>
         *     $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         fontSize: '16px',
         *         columns: [ { field: 'ID' }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
         *     });
         * </script>
         */
        fontSize: undefined,

        /** Name of column that contains the record id. 
         * @additionalinfo If you set primary key, we assume that this number is unique for each records presented in the grid.<br/>
         * For example this should contains the column with primary key from your relation db table.<br/>
         * If the primaryKey is undefined, we autogenerate id for each record in the table by starting from 1.
         * @type string
         * @default undefined
         * @example defined <!-- grid -->
         * <table id="grid"></table>
         * <script>
         *     var data = [
         *         { 'ID': 101, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
         *         { 'ID': 102, 'Name': 'Ronaldo Luis Nazario de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
         *         { 'ID': 103, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' }
         *     ];
         *     $('#grid').grid({
         *         dataSource: data,
         *         primaryKey: 'ID',
         *         columns: [ 
         *             { field: 'ID', width: 70 },
         *             { field: 'Name' },
         *             { field: 'PlaceOfBirth' } ,
         *             { tmpl: '<a href="#">click me</a>', events: { click: function(e) { alert('Your id is ' + e.data.id); } }, width: 100, stopPropagation: true } 
         *         ]
         *     });
         * </script>
         * @example undefined <!-- grid -->
         * <table id="grid"></table>
         * <script>
         *     var data = [
         *         { 'ID': 101, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
         *         { 'ID': 102, 'Name': 'Ronaldo Luis Nazario de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
         *         { 'ID': 103, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' }
         *     ];
         *     $('#grid').grid({
         *         dataSource: data,
         *         columns: [ 
         *             { field: 'ID', width: 70 },
         *             { field: 'Name' },
         *             { field: 'PlaceOfBirth' } ,
         *             { tmpl: '<a href="#">click me</a>', events: { click: function(e) { alert('Your id is ' + e.data.id); } }, width: 100, stopPropagation: true } 
         *         ]
         *     });
         * </script>
         */
        primaryKey: undefined,

        /** The language that needs to be in use.
         * @type string
         * @default 'en-us'
         * @example German.Bootstrap.Default <!-- bootstrap, grid-->
         * <script src="../../dist/modular/grid/js/messages/messages.de-de.js"></script>
         * <table id="grid"></table>
         * <script>
         *     $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         uiLibrary: 'bootstrap',
         *         locale: 'de-de',
         *         columns: [ 
         *             { field: 'ID', width: 34 },
         *             { field: 'Name', title: 'Prénom' },
         *             { field: 'PlaceOfBirth', title: 'Lieu de naissance' }
         *         ],
         *         pager: { limit: 2 }
         *     });
         * </script>
         * @example French.MaterialDesign.Custom <!-- materialicons, grid-->
         * <script src="../../dist/modular/grid/js/messages/messages.fr-fr.js"></script>
         * <table id="grid"></table>
         * <script>
         *     gj.grid.messages['fr-fr'].DisplayingRecords = 'Mes résultats';
         *     $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         uiLibrary: 'materialdesign',
         *         locale: 'fr-fr',
         *         columns: [ 
         *             { field: 'ID', width: 56 },
         *             { field: 'Name', title: 'Prénom' },
         *             { field: 'PlaceOfBirth', title: 'Lieu de naissance' }
         *         ],
         *         pager: { limit: 2 }
         *     });
         * </script>
         */
        locale: 'en-us',

        defaultIconColumnWidth: 70,
        defaultCheckBoxColumnWidth: 70,

        style: {
            wrapper: 'gj-grid-wrapper',
            table: 'gj-grid gj-grid-md',
            loadingCover: 'gj-grid-loading-cover',
            loadingText: 'gj-grid-loading-text',
            header: {
                cell: undefined,
                sortable: 'gj-cursor-pointer'
            },
            content: {
                rowHover: undefined,
                rowSelected: 'gj-grid-md-select'
            }
        },

        icons: {
            asc: '▲',
            desc: '▼'
        }
    },

    bootstrap: {
        style: {
            wrapper: 'gj-grid-wrapper',
            table: 'gj-grid gj-grid-bootstrap gj-grid-bootstrap-3 table table-bordered table-hover',
            content: {
                rowHover: undefined,
                rowSelected: 'active'
            }
        },

        iconsLibrary: 'glyphicons',

        defaultIconColumnWidth: 34,
        defaultCheckBoxColumnWidth: 36
    },

    bootstrap4: {
        style: {
            wrapper: 'gj-grid-wrapper',
            table: 'gj-grid gj-grid-bootstrap gj-grid-bootstrap-4 table table-bordered table-hover',
            content: {
                rowHover: undefined,
                rowSelected: 'active'
            }
        },

        defaultIconColumnWidth: 42,
        defaultCheckBoxColumnWidth: 44
    },

    materialicons: {
        icons: {
            asc: '<i class="material-icons">arrow_upward</i>',
            desc: '<i class="material-icons">arrow_downward</i>'
        }
    },

    fontawesome: {
        icons: {
            asc: '<i class="fa fa-sort-amount-asc" aria-hidden="true"></i>',
            desc: '<i class="fa fa-sort-amount-desc" aria-hidden="true"></i>'
        }
    },

    glyphicons: {
        icons: {
            asc: '<span class="glyphicon glyphicon-sort-by-alphabet" />',
            desc: '<span class="glyphicon glyphicon-sort-by-alphabet-alt" />'
        }
    }
};

/**
  * @widget Grid
  * @plugin Base
  */
gj.grid.events = {
    /**
     * Event fires before addition of an empty row to the grid.
     * @event beforeEmptyRowInsert
     * @param {object} e - event data
     * @param {object} $row - The empty row as jquery object
     * @example sample <!-- grid -->
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: {
     *             url: '/Players/Get',
     *             data: { name: 'not existing data' } //search for not existing data in order to fire the event
     *         },
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
     *     });
     *     grid.on('beforeEmptyRowInsert', function (e, $row) {
     *         alert('beforeEmptyRowInsert is fired.');
     *     });
     * </script>
     */
    beforeEmptyRowInsert: function ($grid, $row) {
        $grid.triggerHandler('beforeEmptyRowInsert', [$row]);
    },

    /**
     * Event fired before data binding takes place.
     *
     * @event dataBinding
     * @param {object} e - event data
     * @param {array} records - the list of records
     * @example sample <!-- grid -->
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
     *     });
     *     grid.on('dataBinding', function (e, records) {
     *         alert('dataBinding is fired. ' + records.length + ' records will be loaded in the grid.');
     *     });
     * </script>
     */
    dataBinding: function ($grid, records) {
        $grid.triggerHandler('dataBinding', [records]);
    },

    /**
     * Event fires after the loading of the data in the grid.
     *
     * @event dataBound
     * @param {object} e - event data
     * @param {array} records - the list of records
     * @param {number} totalRecords - the number of the all records that can be presented in the grid
     * @example sample <!-- grid -->
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
     *     });
     *     grid.on('dataBound', function (e, records, totalRecords) {
     *         alert('dataBound is fired. ' + records.length + ' records are bound to the grid.');
     *     });
     * </script>
     */
    dataBound: function ($grid, records, totalRecords) {
        $grid.triggerHandler('dataBound', [records, totalRecords]);
    },

    /**
     * Event fires after insert of a row in the grid during the loading of the data.
     * @event rowDataBound
     * @param {object} e - event data
     * @param {object} $row - the row presented as jquery object
     * @param {string} id - the id of the record
     * @param {object} record - the data of the row record
     * @example sample <!-- grid -->
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
     *     });
     *     grid.on('rowDataBound', function (e, $row, id, record) {
     *         alert('rowDataBound is fired for row with id=' + id + '.');
     *     });
     * </script>
     */
    rowDataBound: function ($grid, $row, id, record) {
        $grid.triggerHandler('rowDataBound', [$row, id, record]);
    },

    /**
     * Event fires after insert of a cell in the grid during the loading of the data
     *
     * @event cellDataBound
     * @param {object} e - event data
     * @param {object} $displayEl - inner div element for display of the cell value presented as jquery object
     * @param {string} id - the id of the record
     * @param {object} column - the column configuration data
     * @param {object} record - the data of the row record
     * @example sample <!-- grid -->
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' }, { field: 'Bulgarian', title: 'Is Bulgarian?' } ]
     *     });
     *     grid.on('cellDataBound', function (e, $displayEl, id, column, record) {
     *         if ('Bulgarian' === column.field) {
     *             $displayEl.text(record.PlaceOfBirth.indexOf('Bulgaria') > -1 ? 'Yes' : 'No');
     *         }
     *     });
     * </script>
     */
    cellDataBound: function ($grid, $displayEl, id, column, record) {
        $grid.triggerHandler('cellDataBound', [$displayEl, id, column, record]);
    },

    /**
     * Event fires on selection of row
     *
     * @event rowSelect
     * @param {object} e - event data
     * @param {object} $row - the row presented as jquery object
     * @param {string} id - the id of the record
     * @param {object} record - the data of the row record
     * @example sample <!-- materialicons, checkbox, grid -->
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
     *         selectionMethod: 'checkbox'
     *     });
     *     grid.on('rowSelect', function (e, $row, id, record) {
     *         alert('Row with id=' + id + ' is selected.');
     *     });
     * </script>
     */
    rowSelect: function ($grid, $row, id, record) {
        $grid.triggerHandler('rowSelect', [$row, id, record]);
    },

    /**
     * Event fires on un selection of row
     *
     * @event rowUnselect
     * @param {object} e - event data
     * @param {object} $row - the row presented as jquery object
     * @param {string} id - the id of the record
     * @param {object} record - the data of the row record
     * @example sample <!-- materialicons, checkbox, grid -->
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
     *         selectionMethod: 'checkbox'
     *     });
     *     grid.on('rowUnselect', function (e, $row, id, record) {
     *         alert('Row with id=' + id + ' is unselected.');
     *     });
     * </script>
     */
    rowUnselect: function ($grid, $row, id, record) {
        $grid.triggerHandler('rowUnselect', [$row, id, record]);
    },

    /**
     * Event fires before deletion of row in the grid.
     * @event rowRemoving
     * @param {object} e - event data
     * @param {object} $row - the row presented as jquery object
     * @param {string} id - the id of the record
     * @param {object} record - the data of the row record
     * @example sample <!-- grid -->
     * <button onclick="grid.removeRow('1')">Remove Row</button><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         primaryKey: 'ID',
     *         dataSource: [
     *             { 'ID': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
     *             { 'ID': 2, 'Name': 'Ronaldo Luis Nazario de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
     *             { 'ID': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' }
     *         ],
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
     *     });
     *     grid.on('rowRemoving', function (e, $row, id, record) {
     *         alert('rowRemoving is fired for row with id=' + id + '.');
     *     });
     * </script>
     */
    rowRemoving: function ($grid, $row, id, record) {
        $grid.triggerHandler('rowRemoving', [$row, id, record]);
    },

    /**
     * Event fires when the grid.destroy method is called.
     *
     * @event destroying
     * @param {object} e - event data
     * @example sample <!-- grid -->
     * <button id="btnDestroy">Destroy</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
     *     });
     *     grid.on('destroying', function (e) {
     *         alert('destroying is fired.');
     *     });
     *     $('#btnDestroy').on('click', function() {
     *         grid.destroy();
     *     });
     * </script>
     */
    destroying: function ($grid) {
        $grid.triggerHandler('destroying');
    },

    /**
     * Event fires when column is hidding
     *
     * @event columnHide
     * @param {object} e - event data
     * @param {object} column - The data about the column that is hidding
     * @example sample <!-- grid -->
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
     *     });
     *     grid.on('columnHide', function (e, column) {
     *         alert('The ' + column.field + ' column is hidden.');
     *     });
     *     grid.hideColumn('PlaceOfBirth');
     * </script>
     */
    columnHide: function ($grid, column) {
        $grid.triggerHandler('columnHide', [column]);
    },

    /**
     * Event fires when column is showing
     *
     * @event columnShow
     * @param {object} e - event data
     * @param {object} column - The data about the column that is showing
     * @example sample <!-- grid -->
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth', hidden: true } ]
     *     });
     *     grid.on('columnShow', function (e, column) {
     *         alert('The ' + column.field + ' column is shown.');
     *     });
     *     grid.showColumn('PlaceOfBirth');
     * </script>
     */
    columnShow: function ($grid, column) {
        $grid.triggerHandler('columnShow', [column]);
    },

    /**
     * Event fires when grid is initialized.
     *
     * @event initialized
     * @param {object} e - event data
     * @example sample <!-- grid -->
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth', hidden: true } ],
     *         initialized: function (e) {
     *             alert('The grid is initialized.');
     *         }
     *     });
     * </script>
     */
    initialized: function ($grid) {
        $grid.triggerHandler('initialized');
    },

    /**
     * Event fires when the grid data is filtered.
     *
     * @additionalinfo This event is firing only when you use local dataSource, because the filtering with remote dataSource needs to be done on the server side.
     * @event dataFiltered
     * @param {object} e - event data
     * @param {object} records - The records after the filtering.
     * @example sample <!-- grid -->
     * <table id="grid"></table>
     * <script>
     *     var grid, data = [
     *         { 'ID': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria', Nationality: 'Bulgaria' },
     *         { 'ID': 2, 'Name': 'Ronaldo Luís Nazário de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil', Nationality: 'Brazil' },
     *         { 'ID': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England', Nationality: 'England' },
     *         { 'ID': 4, 'Name': 'Manuel Neuer', 'PlaceOfBirth': 'Gelsenkirchen, West Germany', Nationality: 'Germany' },
     *         { 'ID': 5, 'Name': 'James Rodríguez', 'PlaceOfBirth': 'Cúcuta, Colombia', Nationality: 'Colombia' },
     *         { 'ID': 6, 'Name': 'Dimitar Berbatov', 'PlaceOfBirth': 'Blagoevgrad, Bulgaria', Nationality: 'Bulgaria' }
     *     ];
     *     grid = $('#grid').grid({
     *         dataSource: data,
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
     *         dataFiltered: function (e, records) {
     *             records.reverse(); // reverse the data
     *             records.splice(3, 2); // remove 2 elements after the 3rd record
     *         }
     *     });
     * </script>
     */
    dataFiltered: function ($grid, records) {
        $grid.triggerHandler('dataFiltered', [records]);
    }
};

/*global gj $*/
gj.grid.methods = {

    init: function (jsConfig) {
        gj.widget.prototype.init.call(this, jsConfig, 'grid');

        gj.grid.methods.initialize(this);

        if (this.data('autoLoad')) {
            this.reload();
        }
        return this;
    },

    getConfig: function (jsConfig, type) {
        var config = gj.widget.prototype.getConfig.call(this, jsConfig, type);
        gj.grid.methods.setDefaultColumnConfig(config.columns, config.defaultColumnSettings);
        return config;
    },

    setDefaultColumnConfig: function (columns, defaultColumnSettings) {
        var column, i;
        if (columns && columns.length) {
            for (i = 0; i < columns.length; i++) {
                column = $.extend(true, {}, defaultColumnSettings);
                $.extend(true, column, columns[i]);
                columns[i] = column;
            }
        }
    },

    getHTMLConfig: function () {
        var result = gj.widget.prototype.getHTMLConfig.call(this);
        result.columns = [];
        this.find('thead > tr > th').each(function () {
            var $el = $(this),
                title = $el.text(),
                config = gj.widget.prototype.getHTMLConfig.call($el);
            config.title = title;
            if (!config.field) {
                config.field = title;
            }
            if (config.events) {
                config.events = gj.grid.methods.eventsParser(config.events);
            }
            result.columns.push(config);
        });
        return result;
    },

    eventsParser: function (events) {
        var result = {}, list, i, key, func, position;
        list = events.split(',');
        for (i = 0; i < list.length; i++) {
            position = list[i].indexOf(':');
            if (position > 0) {
                key = $.trim(list[i].substr(0, position));
                func = $.trim(list[i].substr(position + 1, list[i].length));
                result[key] = eval('window.' + func); //window[func]; //TODO: eveluate functions from string
            }
        }
        return result;
    },
    
    initialize: function ($grid) {
        var data = $grid.data(),
            $wrapper = $grid.parent('div[data-role="wrapper"]');

        gj.grid.methods.localization(data);

        if ($wrapper.length === 0) {
            $wrapper = $('<div data-role="wrapper" />').addClass(data.style.wrapper); //The css class needs to be added before the wrapping, otherwise doesn't work.
            $grid.wrap($wrapper);
        } else {
            $wrapper.addClass(data.style.wrapper);
        }

        if (data.width) {
            $grid.parent().css('width', data.width);
        }
        if (data.minWidth) {
            $grid.css('min-width', data.minWidth);
        }
        if (data.fontSize) {
            $grid.css('font-size', data.fontSize);
        }
        $grid.addClass(data.style.table);
        if ('checkbox' === data.selectionMethod) {
            data.columns.splice(gj.grid.methods.getColumnPositionNotInRole($grid), 0, {
                title: '',
                width: data.defaultCheckBoxColumnWidth,
                align: 'center',
                type: 'checkbox',
                role: 'selectRow',
                events: {
                    click: function (e) {
                        gj.grid.methods.setSelected($grid, e.data.id, $(this).closest('tr'));
                    }
                },
                headerCssClass: 'gj-grid-select-all',
                stopPropagation: true
            });
        }
        
        if ($grid.children('tbody').length === 0) {
            $grid.append($('<tbody/>'));
        }

        gj.grid.methods.renderHeader($grid);
        gj.grid.methods.appendEmptyRow($grid, '&nbsp;');
        gj.grid.events.initialized($grid);
    },

    localization: function (data) {
        if (!data.notFoundText) {
            data.notFoundText = gj.grid.messages[data.locale].NoRecordsFound;
        }
    },

    renderHeader: function ($grid) {
        var data, columns, style, $thead, $row, $cell, i, $checkAllBoxes;

        data = $grid.data();
        columns = data.columns;
        style = data.style.header;

        $thead = $grid.children('thead');
        if ($thead.length === 0) {
            $thead = $('<thead />');
            $grid.prepend($thead);
        }

        $row = $('<tr data-role="caption" />');
        for (i = 0; i < columns.length; i += 1) {
            $cell = $('<th data-field="' + (columns[i].field || '') + '" />');
            if (columns[i].width) {
                $cell.attr('width', columns[i].width);
            } else if (columns[i].type === 'checkbox') {
                $cell.attr('width', data.defaultIconColumnWidth);
            }
            $cell.addClass(style.cell);
            if (columns[i].headerCssClass) {
                $cell.addClass(columns[i].headerCssClass);
            }
            $cell.css('text-align', columns[i].align || 'left');
            if (columns[i].sortable) {
                $cell.addClass(style.sortable);
                $cell.on('click', gj.grid.methods.createSortHandler($grid, $cell, columns[i]));
            }
            if ('checkbox' === data.selectionMethod && 'multiple' === data.selectionType &&
                'checkbox' === columns[i].type && 'selectRow' === columns[i].role) {
                $checkAllBoxes = $cell.find('input[data-role="selectAll"]');
                if ($checkAllBoxes.length === 0) {
                    $checkAllBoxes = $('<input type="checkbox" data-role="selectAll" />');
                    $cell.append($checkAllBoxes);
                    $checkAllBoxes.checkbox({ uiLibrary: data.uiLibrary });
                }
                $checkAllBoxes.off('click').on('click', function () {
                    if (this.checked) {
                        $grid.selectAll();
                    } else {
                        $grid.unSelectAll();
                    }
                });
            } else {
                $cell.append($('<div data-role="title"/>').html(typeof (columns[i].title) === 'undefined' ? columns[i].field : columns[i].title));
            }
            if (columns[i].hidden) {
                $cell.hide();
            }
            $row.append($cell);
        }

        $thead.empty().append($row);
    },

    createSortHandler: function ($grid, $cell, column) {
        return function () {
            var data, params = {};
            if ($grid.count() > 0) {
                data = $grid.data();
                params[data.paramNames.sortBy] = column.field;
                column.direction = (column.direction === 'asc' ? 'desc' : 'asc');
                params[data.paramNames.direction] = column.direction;
                $grid.reload(params);
            }
        };
    },

    updateHeader: function ($grid) {
        var $sortIcon,
            data = $grid.data(),
            style = data.style.header,
            sortBy = data.params[data.paramNames.sortBy],
            direction = data.params[data.paramNames.direction];

        $grid.find('thead tr th [data-role="sorticon"]').remove();

        if (sortBy) {
            position = gj.grid.methods.getColumnPosition($grid.data('columns'), sortBy);
            if (position > -1) {
                $cell = $grid.find('thead tr th:eq(' + position + ')');
                $sortIcon = $('<div data-role="sorticon" class="gj-unselectable" />').append(('desc' === direction) ? data.icons.desc : data.icons.asc);
                $cell.append($sortIcon);
            }
        }
    },

    useHtmlDataSource: function ($grid, data) {
        var dataSource = [], i, j, $cells, record,
            $rows = $grid.find('tbody tr[data-role != "empty"]');
        for (i = 0; i < $rows.length; i++) {
            $cells = $($rows[i]).find('td');
            record = {};
            for (j = 0; j < $cells.length; j++) {
                record[data.columns[j].field] = $($cells[j]).html();
            }
            dataSource.push(record);
        }
        data.dataSource = dataSource;
    },

    startLoading: function ($grid) {
        var $tbody, $cover, $loading, width, height, top, data;
        gj.grid.methods.stopLoading($grid);
        data = $grid.data();
        if (0 === $grid.outerHeight()) {
            return;
        }
        $tbody = $grid.children('tbody');
        width = $tbody.outerWidth(false);
        height = $tbody.outerHeight(false);
        top = Math.abs($grid.parent().offset().top - $tbody.offset().top);
        $cover = $('<div data-role="loading-cover" />').addClass(data.style.loadingCover).css({
            width: width,
            height: height,
            top: top
        });
        $loading = $('<div data-role="loading-text">' + gj.grid.messages[data.locale].Loading + '</div>').addClass(data.style.loadingText);
        $loading.insertAfter($grid);
        $cover.insertAfter($grid);
        $loading.css({
            top: top + (height / 2) - ($loading.outerHeight(false) / 2),
            left: (width / 2) - ($loading.outerWidth(false) / 2)
        });
    },

    stopLoading: function ($grid) {
        $grid.parent().find('div[data-role="loading-cover"]').remove();
        $grid.parent().find('div[data-role="loading-text"]').remove();
    },

    createAddRowHoverHandler: function ($row, cssClass) {
        return function () {
            $row.addClass(cssClass);
        };
    },

    createRemoveRowHoverHandler: function ($row, cssClass) {
        return function () {
            $row.removeClass(cssClass);
        };
    },

    appendEmptyRow: function ($grid, caption) {
        var data, $row, $cell, $wrapper;
        data = $grid.data();
        $row = $('<tr data-role="empty"/>');
        $cell = $('<td/>').css({ width: '100%', 'text-align': 'center' });
        $cell.attr('colspan', gj.grid.methods.countVisibleColumns($grid));
        $wrapper = $('<div />').html(caption || data.notFoundText);
        $cell.append($wrapper);
        $row.append($cell);

        gj.grid.events.beforeEmptyRowInsert($grid, $row);

        $grid.append($row);
    },

    autoGenerateColumns: function ($grid, records) {
        var names, value, type, i, data = $grid.data();
        data.columns = [];
        if (records.length > 0) {
            names = Object.getOwnPropertyNames(records[0]);
            for (i = 0; i < names.length; i++) {
                value = records[0][names[i]];
                type = 'text';
                if (value) {
                    if (typeof value === 'number') {
                        type = 'number';
                    } else if (value.indexOf('/Date(') > -1) {
                        type = 'date';
                    }
                }
                data.columns.push({ field: names[i], type: type });
            }
            gj.grid.methods.setDefaultColumnConfig(data.columns, data.defaultColumnSettings);
        }
        gj.grid.methods.renderHeader($grid);
    },

    loadData: function ($grid) {
        var data, records, i, recLen, rowCount, $tbody, $rows, $row;

        data = $grid.data();
        records = $grid.getAll();
        gj.grid.events.dataBinding($grid, records);
        recLen = records.length;
        gj.grid.methods.stopLoading($grid);

        if (data.autoGenerateColumns) {
            gj.grid.methods.autoGenerateColumns($grid, records);
        }

        $tbody = $grid.children('tbody');
        if ('checkbox' === data.selectionMethod && 'multiple' === data.selectionType) {
            $grid.find('thead input[data-role="selectAll"]').prop('checked', false);
        }
        $tbody.children('tr').not('[data-role="row"]').remove();
        if (0 === recLen) {
            $tbody.empty();
            gj.grid.methods.appendEmptyRow($grid);
        }

        $rows = $tbody.children('tr');

        rowCount = $rows.length;

        for (i = 0; i < rowCount; i++) {
            if (i < recLen) {
                $row = $rows.eq(i);
                gj.grid.methods.renderRow($grid, $row, records[i], i);
            } else {
                $tbody.find('tr[data-role="row"]:gt(' + (i - 1) + ')').remove();
                break;
            }
        }

        for (i = rowCount; i < recLen; i++) {
            gj.grid.methods.renderRow($grid, null, records[i], i);
        }
        gj.grid.events.dataBound($grid, records, data.totalRecords);
    },

    getId: function (record, primaryKey, position) {
        return (primaryKey && record[primaryKey]) ? record[primaryKey] : position;
    },

    renderRow: function ($grid, $row, record, position) {
        var id, $cell, i, data, mode;
        data = $grid.data();
        if (!$row || $row.length === 0) {
            mode = 'create';
            $row = $('<tr data-role="row"/>');
            $grid.children('tbody').append($row);
            $row.on('mouseenter', gj.grid.methods.createAddRowHoverHandler($row, data.style.content.rowHover));
            $row.on('mouseleave', gj.grid.methods.createRemoveRowHoverHandler($row, data.style.content.rowHover));
        } else {
            mode = 'update';
            $row.removeClass(data.style.content.rowSelected).removeAttr('data-selected').off('click');
        }
        id = gj.grid.methods.getId(record, data.primaryKey, (position + 1));
        $row.attr('data-position', position + 1);
        if (data.selectionMethod !== 'checkbox') {
            $row.on('click', gj.grid.methods.createRowClickHandler($grid, id));
        }
        for (i = 0; i < data.columns.length; i++) {
            if (mode === 'update') {
                $cell = $row.find('td:eq(' + i + ')');
                gj.grid.methods.renderCell($grid, $cell, data.columns[i], record, id);
            } else {
                $cell = gj.grid.methods.renderCell($grid, null, data.columns[i], record, id);
                $row.append($cell);
            }
        }
        gj.grid.events.rowDataBound($grid, $row, id, record);
    },

    renderCell: function ($grid, $cell, column, record, id, mode) {
        var $displayEl, key;

        if (!$cell || $cell.length === 0) {
            $cell = $('<td/>').css('text-align', column.align || 'left');
            $displayEl = $('<div data-role="display" />');
            if (column.cssClass) {
                $cell.addClass(column.cssClass);
            }
            $cell.append($displayEl);
            mode = 'create';
        } else {
            $displayEl = $cell.find('div[data-role="display"]');
            mode = 'update';
        }

        gj.grid.methods.renderDisplayElement($grid, $displayEl, column, record, id, mode);

        //remove all event handlers
        if ('update' === mode) {
            $cell.off();
            $displayEl.off();
        }
        if (column.events) {
            for (key in column.events) {
                if (column.events.hasOwnProperty(key)) {
                    $cell.on(key, { id: id, field: column.field, record: record }, gj.grid.methods.createCellEventHandler(column, column.events[key]));
                }
            }
        }
        if (column.hidden) {
            $cell.hide();
        }

        gj.grid.events.cellDataBound($grid, $displayEl, id, column, record);

        return $cell;
    },

    createCellEventHandler: function (column, func) {
        return function (e) {
            if (column.stopPropagation) {
                e.stopPropagation();
            }
            func.call(this, e);
        };
    },

    renderDisplayElement: function ($grid, $displayEl, column, record, id, mode) {
        var text, $checkbox;

        if ('checkbox' === column.type && gj.checkbox) {
            if ('create' === mode) {
                $checkbox = $('<input type="checkbox" />').val(id).prop('checked', (record[column.field] ? true : false));
                column.role && $checkbox.attr('data-role', column.role);
                $displayEl.append($checkbox);
                $checkbox.checkbox({ uiLibrary: $grid.data('uiLibrary') });
                if (column.role === 'selectRow') {
                    $checkbox.on('click', function () { return false; });
                } else {
                    $checkbox.prop('disabled', true);
                }
            } else {
                $displayEl.find('input[type="checkbox"]').val(id).prop('checked', (record[column.field] ? true : false));
            }
        } else if ('icon' === column.type) {
            if ('create' === mode) {
                $displayEl.append($('<span/>')
                    .addClass($grid.data().uiLibrary === 'bootstrap' ? 'glyphicon' : 'ui-icon')
                    .addClass(column.icon).css({ cursor: 'pointer' }));
                column.stopPropagation = true;
            }
        } else if (column.tmpl) {
            text = column.tmpl;
            column.tmpl.replace(/\{(.+?)\}/g, function ($0, $1) {
                text = text.replace($0, gj.grid.methods.formatText(record[$1], column));
            });
            $displayEl.html(text);
        } else if (column.renderer && typeof (column.renderer) === 'function') {
            text = column.renderer(record[column.field], record, $displayEl.parent(), $displayEl, id, $grid);
            if (text) {
                $displayEl.html(text);
            }
        } else {
            record[column.field] = gj.grid.methods.formatText(record[column.field], column);
            if (!column.tooltip && record[column.field]) {
                $displayEl.attr('title', record[column.field]);
            }
            $displayEl.html(record[column.field]);
        }
        if (column.tooltip && 'create' === mode) {
            $displayEl.attr('title', column.tooltip);
        }
    },

    formatText: function (text, column) {
        if (text && column.type === 'date') {
            text = gj.core.formatDate(gj.core.parseDate(text, column.format), column.format);
        } else {
            text = (typeof (text) === 'undefined' || text === null) ? '' : text.toString();
        }
        if (column.decimalDigits && text) {
            text = parseFloat(text).toFixed(column.decimalDigits);
        }
        return text;
    },

    setRecordsData: function ($grid, response) {
        var records = [],
            totalRecords = 0,
            data = $grid.data();
        if ($.isArray(response)) {
            records = response;
            totalRecords = response.length;
        } else if (data && data.mapping && $.isArray(response[data.mapping.dataField])) {
            records = response[data.mapping.dataField];
            totalRecords = response[data.mapping.totalRecordsField];
            if (!totalRecords || isNaN(totalRecords)) {
                totalRecords = 0;
            }
        }
        $grid.data('records', records);
        $grid.data('totalRecords', totalRecords);
        return records;
    },

    createRowClickHandler: function ($grid, id) {
        return function () {
            gj.grid.methods.setSelected($grid, id, $(this));
        };
    },

    selectRow: function ($grid, data, $row, id) {
        var $checkbox;
        $row.addClass(data.style.content.rowSelected);
        $row.attr('data-selected', 'true');
        if ('checkbox' === data.selectionMethod) {
            $checkbox = $row.find('input[type="checkbox"][data-role="selectRow"]');
            $checkbox.length && !$checkbox.prop('checked') && $checkbox.prop('checked', true);
            if ('multiple' === data.selectionType && $grid.getSelections().length === $grid.count(false)) {
                $grid.find('thead input[data-role="selectAll"]').prop('checked', true);
            }
        }
        gj.grid.events.rowSelect($grid, $row, id, $grid.getById(id));
    },

    unselectRow: function ($grid, data, $row, id) {
        var $checkbox;
        if ($row.attr('data-selected') === 'true') {
            $row.removeClass(data.style.content.rowSelected);
            if ('checkbox' === data.selectionMethod) {
                $checkbox = $row.find('td input[type="checkbox"][data-role="selectRow"]');
                $checkbox.length && $checkbox.prop('checked') && $checkbox.prop('checked', false);
                if ('multiple' === data.selectionType) {
                    $grid.find('thead input[data-role="selectAll"]').prop('checked', false);
                }
            }
            $row.removeAttr('data-selected');
            gj.grid.events.rowUnselect($grid, $row, id, $grid.getById(id));
        }
    },

    setSelected: function ($grid, id, $row) {
        var data = $grid.data();
        if (!$row || !$row.length) {
            $row = gj.grid.methods.getRowById($grid, id);
        }
        if ($row) {
            if ($row.attr('data-selected') === 'true') {
                gj.grid.methods.unselectRow($grid, data, $row, id);
            } else {
                if ('single' === data.selectionType) {
                    $row.siblings('[data-selected="true"]').each(function () {
                        var $row = $(this),
                            id = gj.grid.methods.getId($row, data.primaryKey, $row.data('position'));
                        gj.grid.methods.unselectRow($grid, data, $row, id);
                    });
                }
                gj.grid.methods.selectRow($grid, data, $row, id);
            }
        }
        return $grid;
    },

    selectAll: function ($grid) {
        var data = $grid.data();
        $grid.find('tbody tr[data-role="row"]').each(function () {
            var $row = $(this);
            gj.grid.methods.selectRow($grid, data, $row, $grid.get($row.data('position')));
        });
        $grid.find('thead input[data-role="selectAll"]').prop('checked', true);
        return $grid;
    },

    unSelectAll: function ($grid) {
        var data = $grid.data();
        $grid.find('tbody tr').each(function () {
            var $row = $(this);
            gj.grid.methods.unselectRow($grid, data, $row, $grid.get($row.data('position')));
            $row.find('input[type="checkbox"][data-role="selectRow"]').prop('checked', false);
        });
        $grid.find('thead input[data-role="selectAll"]').prop('checked', false);
        return $grid;
    },

    getSelected: function ($grid) {
        var result = null, selections, record, position;
        selections = $grid.find('tbody>tr[data-selected="true"]');
        if (selections.length > 0) {
            position = $(selections[0]).data('position');
            record = $grid.get(position);
            result = gj.grid.methods.getId(record, $grid.data().primaryKey, position);
        }
        return result;
    },

    getSelectedRows: function ($grid) {
        var data = $grid.data();
        return $grid.find('tbody>tr[data-selected="true"]');
    },

    getSelections: function ($grid) {
        var result = [], position, record,
            data = $grid.data(),
            $selections = gj.grid.methods.getSelectedRows($grid);
        if (0 < $selections.length) {
            $selections.each(function () {
                position = $(this).data('position');
                record = $grid.get(position);
                result.push(gj.grid.methods.getId(record, data.primaryKey, position));
            });
        }
        return result;
    },

    getById: function ($grid, id) {
        var result = null, i, primaryKey = $grid.data('primaryKey'), records = $grid.data('records');
        if (primaryKey) {
            for (i = 0; i < records.length; i++) {
                if (records[i][primaryKey] == id) {
                    result = records[i];
                    break;
                }
            }
        } else {
            result = $grid.get(id);
        }
        return result;
    },

    getRecVPosById: function ($grid, id) {
        var result = id, i, data = $grid.data();
        if (data.primaryKey) {
            for (i = 0; i < data.dataSource.length; i++) {
                if (data.dataSource[i][data.primaryKey] == id) {
                    result = i;
                    break;
                }
            }
        }
        return result;
    },

    getRowById: function ($grid, id) {
        var records = $grid.getAll(false),
            primaryKey = $grid.data('primaryKey'),
            $result = undefined,
            position,
            i;
        if (primaryKey) {
            for (i = 0; i < records.length; i++) {
                if (records[i][primaryKey] == id) {
                    position = i + 1;
                    break;
                }
            }
        } else {
            position = id;
        }
        if (position) {
            $result = $grid.find('tbody > tr[data-position="' + position + '"]');
        }
        return $result;
    },

    getByPosition: function ($grid, position) {
        return $grid.getAll(false)[position - 1];
    },

    getColumnPosition: function (columns, field) {
        var position = -1, i;
        for (i = 0; i < columns.length; i++) {
            if (columns[i].field === field) {
                position = i;
                break;
            }
        }
        return position;
    },

    getColumnInfo: function ($grid, field) {
        var i, result = {}, data = $grid.data();
        for (i = 0; i < data.columns.length; i += 1) {
            if (data.columns[i].field === field) {
                result = data.columns[i];
                break;
            }
        }
        return result;
    },

    getCell: function ($grid, id, field) {
        var position, $row, $result = null;
        position = gj.grid.methods.getColumnPosition($grid.data('columns'), field);
        if (position > -1) {
            $row = gj.grid.methods.getRowById($grid, id);
            $result = $row.find('td:eq(' + position + ') div[data-role="display"]');
        }
        return $result;
    },

    setCellContent: function ($grid, id, field, value) {
        var column, $displayEl = gj.grid.methods.getCell($grid, id, field);
        if ($displayEl) {
            $displayEl.empty();
            if (typeof (value) === 'object') {
                $displayEl.append(value);
            } else {
                column = gj.grid.methods.getColumnInfo($grid, field);
                gj.grid.methods.renderDisplayElement($grid, $displayEl, column, $grid.getById(id), id, 'update');
            }
        }
    },

    clone: function (source) {
        var target = [];
        $.each(source, function () {
            target.push(this.clone());
        });
        return target;
    },

    getAll: function ($grid) {
        return $grid.data('records');
    },

    countVisibleColumns: function ($grid) {
        var columns, count, i;
        columns = $grid.data().columns;
        count = 0;
        for (i = 0; i < columns.length; i++) {
            if (columns[i].hidden !== true) {
                count++;
            }
        }
        return count;
    },

    clear: function ($grid, showNotFoundText) {
        var data = $grid.data();
        $grid.xhr && $grid.xhr.abort();
        $grid.children('tbody').empty();
        data.records = [];
        gj.grid.methods.stopLoading($grid);
        gj.grid.methods.appendEmptyRow($grid, showNotFoundText ? data.notFoundText : '&nbsp;');
        gj.grid.events.dataBound($grid, [], 0);
        return $grid;
    },

    render: function ($grid, response) {
        if (response) {
            gj.grid.methods.setRecordsData($grid, response);
            gj.grid.methods.updateHeader($grid);
            gj.grid.methods.loadData($grid);
        }
        return $grid;
    },

    filter: function ($grid) {
        var field, column,
            data = $grid.data(),
            records = data.dataSource.slice();

        if (data.params[data.paramNames.sortBy]) {
            column = gj.grid.methods.getColumnInfo($grid, data.params[data.paramNames.sortBy]);
            records.sort(column.sortable.sorter ? column.sortable.sorter(column.direction, column) : gj.grid.methods.createDefaultSorter(column.direction, column.field));
        }

        for (field in data.params) {
            if (data.params[field] && !data.paramNames[field]) {
                column = gj.grid.methods.getColumnInfo($grid, field);
                records = $.grep(records, function (record) {
                    var value = record[field] || '',
                        searchStr = data.params[field] || '';
                    return column && typeof (column.filter) === 'function' ? column.filter(value, searchStr) : (value.toUpperCase().indexOf(searchStr.toUpperCase()) > -1);
                });
            }
        }

        gj.grid.events.dataFiltered($grid, records);

        return records;
    },

    createDefaultSorter: function (direction, field) {
        return function (recordA, recordB) {
            var a = (recordA[field] || '').toString(),
                b = (recordB[field] || '').toString();
            return (direction === 'asc') ? a.localeCompare(b) : b.localeCompare(a);
        };
    },

    destroy: function ($grid, keepTableTag, keepWrapperTag) {
        var data = $grid.data();
        if (data) {
            gj.grid.events.destroying($grid);
            gj.grid.methods.stopLoading($grid);
            $grid.xhr && $grid.xhr.abort();
            $grid.off();
            if (keepWrapperTag === false && $grid.parent('div[data-role="wrapper"]').length > 0) {
                $grid.unwrap();
            }
            $grid.removeData();
            if (keepTableTag === false) {
                $grid.remove();
            } else {
                $grid.removeClass().empty();
            }
            $grid.removeAttr('data-type');
        }
        return $grid;
    },

    showColumn: function ($grid, field) {
        var data = $grid.data(),
            position = gj.grid.methods.getColumnPosition(data.columns, field),
            $cells;

        if (position > -1) {
            $grid.find('thead>tr').each(function() {
                $(this).children('th').eq(position).show();
            });
            $.each($grid.find('tbody>tr'), function () {
                $(this).children('td').eq(position).show();
            });
            data.columns[position].hidden = false;

            $cells = $grid.find('tbody > tr[data-role="empty"] > td');
            if ($cells && $cells.length) {
                $cells.attr('colspan', gj.grid.methods.countVisibleColumns($grid));
            }

            gj.grid.events.columnShow($grid, data.columns[position]);
        }

        return $grid;
    },

    hideColumn: function ($grid, field) {
        var data = $grid.data(),
            position = gj.grid.methods.getColumnPosition(data.columns, field),
            $cells;

        if (position > -1) {
            $grid.find('thead>tr').each(function () {
                $(this).children('th').eq(position).hide();
            });
            $.each($grid.find('tbody>tr'), function () {
                $(this).children('td').eq(position).hide();
            });
            data.columns[position].hidden = true;

            $cells = $grid.find('tbody > tr[data-role="empty"] > td');
            if ($cells && $cells.length) {
                $cells.attr('colspan', gj.grid.methods.countVisibleColumns($grid));
            }

            gj.grid.events.columnHide($grid, data.columns[position]);
        }

        return $grid;
    },

    isLastRecordVisible: function () {
        return true;
    },

    addRow: function ($grid, record) {
        var data = $grid.data();
        data.totalRecords = $grid.data('totalRecords') + 1;
        gj.grid.events.dataBinding($grid, [record]);
        data.records.push(record);
        if ($.isArray(data.dataSource)) {
            data.dataSource.push(record);
        }
        if (data.totalRecords === 1) {
            $grid.children('tbody').empty();
        }
        if (gj.grid.methods.isLastRecordVisible($grid)) {
            gj.grid.methods.renderRow($grid, null, record, $grid.count() - 1);
        }
        gj.grid.events.dataBound($grid, [record], data.totalRecords);
        return $grid;
    },

    updateRow: function ($grid, id, record) {
        var $row = gj.grid.methods.getRowById($grid, id),
            data = $grid.data(), position;
        data.records[$row.data('position') - 1] = record;
        if ($.isArray(data.dataSource)) {
            position = gj.grid.methods.getRecVPosById($grid, id);
            data.dataSource[position] = record;
        }
        gj.grid.methods.renderRow($grid, $row, record, $row.index());
        return $grid;
    },

    removeRow: function ($grid, id) {
        var position,
            data = $grid.data(),
            $row = gj.grid.methods.getRowById($grid, id);

        gj.grid.events.rowRemoving($grid, $row, id, $grid.getById(id));
        if ($.isArray(data.dataSource)) {
            position = gj.grid.methods.getRecVPosById($grid, id);
            data.dataSource.splice(position, 1);
        }
        $grid.reload();
        return $grid;
    },

    count: function ($grid, includeAllRecords) {
        return includeAllRecords ? $grid.data().totalRecords : $grid.getAll().length;
    },

    getColumnPositionByRole: function ($grid, role) {
        var i, result, columns = $grid.data('columns');
        for (i = 0; i < columns.length; i++) {
            if (columns[i].role === role) {
                result = i;
                break;
            }
        }
        return result;
    },

    getColumnPositionNotInRole: function ($grid) {
        var i, result = 0, columns = $grid.data('columns');
        for (i = 0; i < columns.length; i++) {
            if (!columns[i].role) {
                result = i;
                break;
            }
        }
        return result;
    }
};

/**
  * @widget Grid
  * @plugin Base
  */
gj.grid.widget = function ($grid, jsConfig) {
    var self = this,
        methods = gj.grid.methods;

    /**
     * Reload the data in the grid from a data source.
     * @method
     * @param {object} params - An object that contains a list with parameters that are going to be send to the server.
     * @fires beforeEmptyRowInsert, dataBinding, dataBound, cellDataBound
     * @return grid
     * @example sample <!-- grid -->
     * <input type="text" id="txtSearch">
     * <button id="btnSearch">Search</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
     *     });
     *     $('#btnSearch').on('click', function () {
     *         grid.reload({ name: $('#txtSearch').val() });
     *     });
     * </script>
     */
    self.reload = function (params) {
        methods.startLoading(this);
        return gj.widget.prototype.reload.call(this, params);
    };

    /**
     * Clear the content in the grid.
     * @method
     * @param {boolean} showNotFoundText - Indicates if the "Not Found" text is going to show after the clearing of the grid.
     * @return grid
     * @example sample <!-- materialicons, grid -->
     * <button id="btnClear">Clear</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
     *         pager: { limit: 5 }
     *     });
     *     $('#btnClear').on('click', function () {
     *         grid.clear();
     *     });
     * </script>
     */
    self.clear = function (showNotFoundText) {
        return methods.clear(this, showNotFoundText);
    };

    /**
     * Return the number of records in the grid. By default return only the records that are visible in the grid.
     * @method
     * @param {boolean} includeAllRecords - include records that are not visible when you are using local dataSource.
     * @return number
     * @example Local.DataSource <!-- bootstrap, grid, grid.pagination -->
     * <button class="btn btn-default" onclick="alert(grid.count())">Count Visible Records</button>
     * <button class="btn btn-default" onclick="alert(grid.count(true))">Count All Records</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var data, grid;
     *     data = [
     *         { 'ID': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
     *         { 'ID': 2, 'Name': 'Ronaldo Luis Nazario de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
     *         { 'ID': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' }
     *     ];
     *     grid = $('#grid').grid({
     *         dataSource: data,
     *         columns: [ { field: 'ID', width: 34 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
     *         uiLibrary: 'bootstrap',
     *         pager: { limit: 2, sizes: [2, 5, 10] }
     *     });
     * </script>
     * @example Remote.DataSource <!-- bootstrap, grid, grid.pagination -->
     * <button onclick="alert(grid.count())">Count Visible Records</button>
     * <button onclick="alert(grid.count(true))">Count All Records</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 34 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
     *         uiLibrary: 'bootstrap',
     *         pager: { limit: 2, sizes: [2, 5, 10] }
     *     });
     * </script>
     */
    self.count = function (includeAllRecords) {
        return methods.count(this, includeAllRecords);
    };

    /**
     * Render data in the grid
     * @method
     * @param {object} response - An object that contains the data that needs to be loaded in the grid.
     * @fires beforeEmptyRowInsert, dataBinding, dataBound, cellDataBound
     * @return grid
     * @example sample <!-- grid -->
     * <table id="grid"></table>
     * <script>
     *     var grid, onSuccessFunc;
     *     onSuccessFunc = function (response) {
     *         //you can modify the response here if needed
     *         grid.render(response);
     *     };
     *     grid = $('#grid').grid({
     *         dataSource: { url: '/Players/Get', success: onSuccessFunc },
     *         columns: [ { field: 'Name' }, { field: 'PlaceOfBirth' } ]
     *     });
     * </script>
     */
    self.render = function (response) {
        return methods.render($grid, response);
    };

    /**
     * Destroy the grid. This method remove all data from the grid and all events attached to the grid.
     * @additionalinfo The grid table tag and wrapper tag are kept by default after the execution of destroy method,
     * but you can remove them if you pass false to the keepTableTag and keepWrapperTag parameters.
     * @method
     * @param {boolean} keepTableTag - If this flag is set to false, the table tag will be removed from the HTML dom tree.
     * @param {boolean} keepWrapperTag - If this flag is set to false, the table wrapper tag will be removed from the HTML dom tree.
     * @fires destroying
     * @return void
     * @example keep.wrapper.and.table <!-- grid -->
     * <button class="gj-button-md" id="btnDestroy">Destroy</button>
     * <button class="gj-button-md" id="btnCreate">Create</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var createFunc = function() {
     *         $('#grid').grid({
     *             dataSource: '/Players/Get',
     *             columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
     *         });
     *     };
     *     createFunc();
     *     $('#btnDestroy').on('click', function () {
     *         $('#grid').grid('destroy', true, true);
     *     });
     *     $('#btnCreate').on('click', function () {
     *         createFunc();
     *     });
     * </script>
     * @example remove.wrapper.and.table <!-- grid -->
     * <button class="gj-button-md" id="btnRemove">Remove</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
     *     });
     *     $('#btnRemove').on('click', function () {
     *         grid.destroy();
     *     });
     * </script>
     */
    self.destroy = function (keepTableTag, keepWrapperTag) {
        return methods.destroy(this, keepTableTag, keepWrapperTag);
    };

    /**
     * Select a row from the grid based on id parameter.
     * @method
     * @param {string} id - The id of the row that needs to be selected
     * @return grid
     * @example sample <!-- materialicons, checkbox, grid -->
     * <input type="text" id="txtNumber" value="1" />
     * <button id="btnSelect" class="gj-button-md">Select</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
     *         selectionMethod: 'checkbox'
     *     });
     *     $('#btnSelect').on('click', function () {
     *         grid.setSelected(parseInt($('#txtNumber').val(), 10));
     *     });
     * </script>
     */
    self.setSelected = function (id) {
        return methods.setSelected(this, id);
    };

    /**
     * Return the id of the selected record.
     * If the multiple selection method is one this method is going to return only the id of the first selected record.
     * @method
     * @return string
     * @example sample <!-- materialicons, checkbox, grid -->
     * <button id="btnShowSelection" class="gj-button-md">Show Selection</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
     *         selectionMethod: 'checkbox'
     *     });
     *     $('#btnShowSelection').on('click', function () {
     *         alert(grid.getSelected());
     *     });
     * </script>
     */
    self.getSelected = function () {
        return methods.getSelected(this);
    };

    /**
     * Return an array with the ids of the selected record.
     * @additionalinfo Specify primaryKey if you want to use field from the dataSource as identificator for selection.
     * @method
     * @return array
     * @example With.Primary.Ket <!-- materialicons, checkbox, grid, dropdown -->
     * <button id="btnShowSelection" class="gj-button-md">Show Selections</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid, data = [
     *         { 'ID': 101, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
     *         { 'ID': 102, 'Name': 'Ronaldo Luis Nazario de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
     *         { 'ID': 103, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' },
     *         { 'ID': 104, 'Name': 'Manuel Neuer', 'PlaceOfBirth': 'Gelsenkirchen, West Germany' }
     *     ];
     *     grid = $('#grid').grid({
     *         dataSource: data,
     *         primaryKey: 'ID',
     *         columns: [ { field: 'ID', width: 70 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
     *         selectionMethod: 'checkbox',
     *         selectionType: 'multiple',
     *         pager: { limit: 2, sizes: [2, 5, 10] }
     *     });
     *     $('#btnShowSelection').on('click', function () {
     *         var selections = grid.getSelections();
     *         alert(selections.join());
     *     });
     * </script>
     * @example Without.Primary.Ket <!-- materialicons, checkbox, grid, dropdown -->
     * <button id="btnShowSelection" class="gj-button-md">Show Selections</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid, data = [
     *         { 'ID': 101, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
     *         { 'ID': 102, 'Name': 'Ronaldo Luis Nazario de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
     *         { 'ID': 103, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' },
     *         { 'ID': 104, 'Name': 'Manuel Neuer', 'PlaceOfBirth': 'Gelsenkirchen, West Germany' }
     *     ];
     *     grid = $('#grid').grid({
     *         dataSource: data,
     *         columns: [ { field: 'ID', width: 70 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
     *         selectionMethod: 'checkbox',
     *         selectionType: 'multiple',
     *         pager: { limit: 2, sizes: [2, 5, 10] }
     *     });
     *     $('#btnShowSelection').on('click', function () {
     *         var selections = grid.getSelections();
     *         alert(selections.join());
     *     });
     * </script>
     */
    self.getSelections = function () {
        return methods.getSelections(this);
    };

    /**
     * Select all records from the grid.
     * @method
     * @return grid
     * @example sample <!-- materialicons, checkbox, grid -->
     * <button id="btnSelectAll">Select All</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
     *         selectionMethod: 'checkbox',
     *         selectionType: 'multiple'
     *     });
     *     $('#btnSelectAll').on('click', function () {
     *         grid.selectAll();
     *     });
     * </script>
     */
    self.selectAll = function () {
        return methods.selectAll(this);
    };

    /**
     * Unselect all records from the grid.
     * @method
     * @return void
     * @example sample <!-- materialicons, checkbox, grid -->
     * <button id="btnSelectAll">Select All</button>
     * <button id="btnUnSelectAll">UnSelect All</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
     *         selectionMethod: 'checkbox',
     *         selectionType: 'multiple'
     *     });
     *     $('#btnSelectAll').on('click', function () {
     *         grid.selectAll();
     *     });
     *     $('#btnUnSelectAll').on('click', function () {
     *         grid.unSelectAll();
     *     });
     * </script>
     */
    self.unSelectAll = function () {
        return methods.unSelectAll(this);
    };

    /**
     * Return record by id of the record.
     * @method
     * @param {string} id - The id of the row that needs to be returned.
     * @return object
     * @example sample <!-- grid -->
     * <button id="btnGetData" class="gj-button-md">Get Data</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
     *         primaryKey: 'ID' //define the name of the column that you want to use as ID here.
     *     });
     *     $('#btnGetData').on('click', function () {
     *         var data = grid.getById('2');
     *         alert(data.Name + ' born in ' + data.PlaceOfBirth);
     *     });
     * </script>
     */
    self.getById = function (id) {
        return methods.getById(this, id);
    };

    /**
     * Return record from the grid based on position.
     * @method
     * @param {number} position - The position of the row that needs to be return.
     * @return object
     * @example sample <!-- grid -->
     * <button id="btnGetData" class="gj-button-md">Get Data</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
     *     });
     *     $('#btnGetData').on('click', function () {
     *         var data = grid.get(3);
     *         alert(data.Name + ' born in ' + data.PlaceOfBirth);
     *     });
     * </script>
     */
    self.get = function (position) {
        return methods.getByPosition(this, position);
    };

    /**
     * Return an array with all records presented in the grid.
     * @method
     * @param {boolean} includeAllRecords - include records that are not visible when you are using local dataSource.
     * @return number
     * @example Local.DataSource <!-- bootstrap, grid, grid.pagination -->
     * <button onclick="alert(JSON.stringify(grid.getAll()))" class="btn btn-default">Get All Visible Records</button>
     * <button onclick="alert(JSON.stringify(grid.getAll(true)))" class="btn btn-default">Get All Records</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var data, grid;
     *     data = [
     *         { 'ID': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
     *         { 'ID': 2, 'Name': 'Ronaldo Luis Nazario de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
     *         { 'ID': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' }
     *     ];
     *     grid = $('#grid').grid({
     *         dataSource: data,
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
     *         uiLibrary: 'bootstrap',
     *         pager: { limit: 2, sizes: [2, 5, 10] }
     *     });
     * </script>
     * @example Remote.DataSource <!-- bootstrap, grid, grid.pagination -->
     * <button onclick="alert(JSON.stringify(grid.getAll()))" class="btn btn-default">Get All Visible Records</button>
     * <button onclick="alert(JSON.stringify(grid.getAll(true)))" class="btn btn-default">Get All Records</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
     *         uiLibrary: 'bootstrap',
     *         pager: { limit: 2, sizes: [2, 5, 10] }
     *     });
     * </script>
     */
    self.getAll = function (includeAllRecords) {
        return methods.getAll(this, includeAllRecords);
    };

    /**
     * Show hidden column.
     * @method
     * @param {string} field - The name of the field bound to the column.
     * @return grid
     * @example sample <!-- grid -->
     * <button id="btnShowColumn" class="gj-button-md">Show Column</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth', hidden: true } ]
     *     });
     *     $('#btnShowColumn').on('click', function () {
     *         grid.showColumn('PlaceOfBirth');
     *     });
     * </script>
     */
    self.showColumn = function (field) {
        return methods.showColumn(this, field);
    };

    /**
     * Hide column from the grid.
     * @method
     * @param {string} field - The name of the field bound to the column.
     * @return grid
     * @example sample <!-- grid -->
     * <button id="btnHideColumn" class="gj-button-md">Hide Column</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
     *     });
     *     $('#btnHideColumn').on('click', function () {
     *         grid.hideColumn('PlaceOfBirth');
     *     });
     * </script>
     */
    self.hideColumn = function (field) {
        return methods.hideColumn(this, field);
    };

    /**
     * Add new row to the grid.
     * @method
     * @param {object} record - Object with data for the new record.
     * @return grid
     * @example without.pagination <!-- materialicons, grid -->
     * <button id="btnAdd" class="gj-button-md">Add Row</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: [
     *             { 'ID': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
     *             { 'ID': 2, 'Name': 'Ronaldo Luis Nazario de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
     *             { 'ID': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' }
     *         ],
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
     *     });
     *     $('#btnAdd').on('click', function () {
     *         grid.addRow({ 'ID': grid.count(true) + 1, 'Name': 'Test Player', 'PlaceOfBirth': 'Test City, Test Country' });
     *     });
     * </script>
     * @example with.pagination <!-- materialicons, grid -->
     * <button id="btnAdd" class="gj-button-md">Add Row</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         primaryKey: 'ID',
     *         dataSource: [
     *             { 'ID': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
     *             { 'ID': 2, 'Name': 'Ronaldo Luis Nazario de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
     *             { 'ID': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' }
     *         ],
     *         columns: [ 
     *             { field: 'ID', width: 56 },
     *             { field: 'Name' },
     *             { field: 'PlaceOfBirth' },
     *             { width: 70, align: 'center', tmpl: '<i class="material-icons">delete</i>', events: { 'click': function(e) { grid.removeRow(e.data.id); } } }
     *         ],
     *         pager: { limit: 2 }
     *     });
     *     $('#btnAdd').on('click', function () {
     *         grid.addRow({ 'ID': grid.count(true) + 1, 'Name': 'Test Player', 'PlaceOfBirth': 'Test City, Test Country' });
     *     });
     * </script>
     */
    self.addRow = function (record) {
        return methods.addRow(this, record);
    };

    /**
     * Update row data.
     * @method
     * @param {string} id - The id of the row that needs to be updated
     * @param {object} record - Object with data for the new record.
     * @return grid
     * @example sample <!-- materialicons, grid -->
     * <table id="grid"></table>
     * <script>
     *     var grid;
     *     grid = $('#grid').grid({
     *         primaryKey: 'ID',
     *         dataSource: [
     *             { 'ID': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
     *             { 'ID': 2, 'Name': 'Ronaldo Luis Nazario de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
     *             { 'ID': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' }
     *         ],
     *         columns: [
     *             { field: 'ID', width: 56 },
     *             { field: 'Name' },
     *             { field: 'PlaceOfBirth' },
     *             { title: '', width: 50, align: 'center', tmpl: '<u>Edit</u>', events: { 'click': Edit } }
     *         ],
     *         pager: { limit: 2 }
     *     });
     *     function Edit(e) {
     *         grid.updateRow(e.data.id, { 'ID': e.data.id, 'Name': 'Ronaldo', 'PlaceOfBirth': 'Rio, Brazil' });
     *     }
     * </script>
     */
    self.updateRow = function (id, record) {
        return methods.updateRow(this, id, record);
    };

    //TODO: needs to be removed
    self.setCellContent = function (id, index, value) {
        methods.setCellContent(this, id, index, value);
    };

    /**
     * Remove row from the grid
     * @additionalinfo This method is design to work only with local datasources. If you use remote datasource, you need to send a request to the server to remove the row and then reload the data in the grid.
     * @method
     * @param {string} id - Id of the record that needs to be removed.
     * @return grid
     * @example Without.Pagination <!-- materialicons, grid -->
     * <table id="grid"></table>
     * <script>
     *     var grid;
     *     function Delete(e) {
     *         if (confirm('Are you sure?')) {
     *             grid.removeRow(e.data.id);
     *         }
     *     }
     *     grid = $('#grid').grid({
     *         primaryKey: 'ID',
     *         dataSource: [
     *             { 'ID': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
     *             { 'ID': 2, 'Name': 'Ronaldo Luís Nazário de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
     *             { 'ID': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' }
     *         ],
     *         columns: [
     *             { field: 'ID', width: 56 },
     *             { field: 'Name' },
     *             { field: 'PlaceOfBirth' },
     *             { width: 100, align: 'center', tmpl: '<u class="gj-cursor-pointer">Delete</u>', events: { 'click': Delete } }
     *         ]
     *     });
     * </script>
     * @example With.Pagination <!-- materialicons, grid, grid.pagination -->
     * <table id="grid"></table>
     * <script>
     *     var grid;
     *     function Delete(e) {
     *         if (confirm('Are you sure?')) {
     *             grid.removeRow(e.data.id);
     *         }
     *     }
     *     grid = $('#grid').grid({
     *         primaryKey: 'ID',
     *         dataSource: [
     *             { 'ID': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
     *             { 'ID': 2, 'Name': 'Ronaldo Luís Nazário de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
     *             { 'ID': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' }
     *         ],
     *         columns: [
     *             { field: 'ID', width: 56 },
     *             { field: 'Name' },
     *             { field: 'PlaceOfBirth' },
     *             { width: 100, align: 'center', tmpl: '<u class="gj-cursor-pointer">Delete</u>', events: { 'click': Delete } }
     *         ],
     *         pager: { limit: 2 }
     *     });
     * </script>
     */
    self.removeRow = function (id) {
        return methods.removeRow(this, id);
    };

    $.extend($grid, self);
    if ('grid' !== $grid.attr('data-type')) {
        methods.init.call($grid, jsConfig);
    }

    return $grid;
}

gj.grid.widget.prototype = new gj.widget();
gj.grid.widget.constructor = gj.grid.widget;

gj.grid.widget.prototype.getConfig = gj.grid.methods.getConfig;
gj.grid.widget.prototype.getHTMLConfig = gj.grid.methods.getHTMLConfig;

(function ($) {
    $.fn.grid = function (method) {
        var $widget;
        if (this && this.length) {
            if (typeof method === 'object' || !method) {
                return new gj.grid.widget(this, method);
            } else {
                $widget = new gj.grid.widget(this, null);
                if ($widget[method]) {
                    return $widget[method].apply(this, Array.prototype.slice.call(arguments, 1));
                } else {
                    throw 'Method ' + method + ' does not exist.';
                }
            }
        }
    };
})(jQuery);

/** 
 * @widget Grid 
 * @plugin Expand Collapse Rows
 */
gj.grid.plugins.expandCollapseRows = {
    config: {
        base: {
            /** Template for the content in the detail section of the row.
             * Automatically add expand collapse column as a first column in the grid during initialization.
             * @type string
             * @default undefined
             * @example Material.Design <!-- materialicons, grid, grid.expandCollapseRows -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         uiLibrary: 'materialdesign',
             *         detailTemplate: '<div style="text-align: left"><b>Place Of Birth:</b> {PlaceOfBirth}</div>',
             *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'DateOfBirth', type: 'date' } ]
             *     });
             * </script>
             * @example Bootstrap.3 <!-- bootstrap, grid, grid.expandCollapseRows -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         uiLibrary: 'bootstrap',
             *         detailTemplate: '<div><b>Place Of Birth:</b> {PlaceOfBirth}</div>',
             *         columns: [ { field: 'ID', width: 34 }, { field: 'Name' }, { field: 'DateOfBirth', type: 'date' } ]
             *     });
             * </script>
             * @example Bootstrap.4.Font.Awesome <!-- bootstrap4, fontawesome, grid, grid.expandCollapseRows -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         uiLibrary: 'bootstrap4',
             *         iconsLibrary: 'fontawesome',
             *         detailTemplate: '<div><b>Place Of Birth:</b> {PlaceOfBirth}</div>',
             *         columns: [ { field: 'ID', width: 34 }, { field: 'Name' }, { field: 'DateOfBirth', type: 'date' } ]
             *     });
             * </script>
             */
            detailTemplate: undefined,

            /** If set try to persist the state of expanded rows.
             * You need to specify primaryKey on the initialization of the grid in order to enable this feature.
             * @default true
             * @example True <!-- bootstrap, grid  -->
             * <div class="container">
             *     <div class="row">
             *         <div class="col-xs-12">
             *             <p>Expand row, then change the page and return back to the page with expanded row in order to see that the expansion is kept.</p>
             *             <table id="grid"></table>
             *         </div>
             *     </div>
             * </div>
             * <script>
             *     var grid = $('#grid').grid({
             *         uiLibrary: 'bootstrap',
             *         primaryKey: 'ID',
             *         dataSource: '/Players/Get',
             *         columns: [ { field: 'ID', width: 34 }, { field: 'Name' } ],
             *         detailTemplate: '<div><b>Place Of Birth:</b> {PlaceOfBirth}</div>',
             *         keepExpandedRows: true,
             *         pager: { limit: 2, sizes: [2, 5, 10, 20] }
             *     });
             * </script>
             */
            keepExpandedRows: true,

            icons: {
                /** Expand row icon definition.
                 * @alias icons.expandRow
                 * @type String
                 * @default '<i class="material-icons">keyboard_arrow_right</i>'
                 * @example Plus.Minus.Icons <!-- materialicons, grid -->
                 * <table id="grid"></table>
                 * <script>
                 *     $('#grid').grid({
                 *         primaryKey: 'ID',
                 *         dataSource: '/Players/Get',
                 *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' } ],
                 *         detailTemplate: '<div><b>Place Of Birth:</b> {PlaceOfBirth}</div>',
                 *         icons: {
                 *             expandRow: '<i class="material-icons">add</i>',
                 *             collapseRow: '<i class="material-icons">remove</i>'
                 *         }
                 *     });
                 * </script>
                 */
                expandRow: '<i class="material-icons">keyboard_arrow_right</i>',

                /** Collapse row icon definition.
                 * @alias icons.collapseRow
                 * @type String
                 * @default '<i class="material-icons">keyboard_arrow_down</i>'
                 * @example Plus.Minus.Icons <!-- materialicons, grid -->
                 * <table id="grid"></table>
                 * <script>
                 *     $('#grid').grid({
                 *         primaryKey: 'ID',
                 *         dataSource: '/Players/Get',
                 *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' } ],
                 *         detailTemplate: '<div><b>Place Of Birth:</b> {PlaceOfBirth}</div>',
                 *         icons: {
                 *             expandRow: '<i class="material-icons">add</i>',
                 *             collapseRow: '<i class="material-icons">remove</i>'
                 *         }
                 *     });
                 * </script>
                 */
                collapseRow: '<i class="material-icons">keyboard_arrow_down</i>'
            }
        },

        fontawesome: {
            icons: {
                expandRow: '<i class="fa fa-angle-right" aria-hidden="true"></i>',
                collapseRow: '<i class="fa fa-angle-down" aria-hidden="true"></i>'
            }
        },

        glyphicons: {
            icons: {
                expandRow: '<span class="glyphicon glyphicon-chevron-right" />',
                collapseRow: '<span class="glyphicon glyphicon-chevron-down" />'
            }
        }
    },

    'private': {
        detailExpand: function ($grid, $cell) {
            var $contentRow = $cell.closest('tr'),
                $detailsRow = $('<tr data-role="details" />'),
                $detailsCell = $('<td colspan="' + gj.grid.methods.countVisibleColumns($grid) + '" />'),
                $detailsWrapper = $('<div data-role="display" />'),
                data = $grid.data(),
                position = $contentRow.data('position'),
                record = $grid.get(position),
                id = gj.grid.methods.getId(record, data.primaryKey, record);

            $detailsRow.append($detailsCell.append($detailsWrapper.append($contentRow.data('details'))));
            $detailsRow.insertAfter($contentRow);
            $cell.children('div[data-role="display"]').empty().append(data.icons.collapseRow);
            $grid.updateDetails($contentRow);
            gj.grid.plugins.expandCollapseRows.events.detailExpand($grid, $detailsRow.find('td>div'), id);
        },

        detailCollapse: function ($grid, $cell) {
            var $contentRow = $cell.closest('tr'),
                $detailsRow = $contentRow.next('tr[data-role="details"]'),
                data = $grid.data(),
                id = gj.grid.methods.getId($contentRow, data.primaryKey, $contentRow.data('position'));
            $detailsRow.remove();
            $cell.children('div[data-role="display"]').empty().append(data.icons.expandRow);
            gj.grid.plugins.expandCollapseRows.events.detailCollapse($grid, $detailsRow.find('td>div'), id);
        },

        keepSelection: function($grid, id) {
            var data = $grid.data();
            if (data.keepExpandedRows) {
                if ($.isArray(data.expandedRows)) {
                    if (data.expandedRows.indexOf(id) == -1) {
                        data.expandedRows.push(id)
                    }
                } else {
                    data.expandedRows = [id];
                }
            }
        },

        removeSelection: function ($grid, id) {
            var data = $grid.data();
            if (data.keepExpandedRows && $.isArray(data.expandedRows) && data.expandedRows.indexOf(id) > -1) {
                data.expandedRows.splice(data.expandedRows.indexOf(id), 1);
            }
        },

        updateDetailsColSpan: function ($grid) {
            var $cells = $grid.find('tbody > tr[data-role="details"] > td');
            if ($cells && $cells.length) {
                $cells.attr('colspan', gj.grid.methods.countVisibleColumns($grid));
            }
        }        
    },

    'public': {
        //TODO: add documentation
        collapseAll: function () {
            var $grid = this,
                position = gj.grid.methods.getColumnPositionByRole($grid, 'expander');
            $grid.find('tbody tr[data-role="row"]').each(function () {
                gj.grid.plugins.expandCollapseRows.private.detailCollapse($grid, $(this).find('td:eq(' + position + ')'));
            });
        },

        //TODO: add documentation
        expandAll: function () {
            var $grid = this,
                position = gj.grid.methods.getColumnPositionByRole($grid, 'expander');
            $grid.find('tbody tr[data-role="row"]').each(function () {
                gj.grid.plugins.expandCollapseRows.private.detailExpand($grid, $(this).find('td:eq(' + position + ')'));
            });
        },

        //TODO: add documentation
        updateDetails: function ($contentRow) {
            var $grid = this,
                $detailWrapper = $contentRow.data('details'),
                content = $detailWrapper.html(),
                record = $grid.get($contentRow.data('position'));

            if (record && content) {
                $detailWrapper.html().replace(/\{(.+?)\}/g, function ($0, $1) {
                    var column = gj.grid.methods.getColumnInfo($grid, $1);
                    content = content.replace($0, gj.grid.methods.formatText(record[$1], column));
                });
                $detailWrapper.html(content);
            }
        }
    },

    'events': {
        /**
         * Event fires when detail row is showing
         *
         * @event detailExpand
         * @param {object} e - event data
         * @param {object} detailWrapper - the detail wrapper as jQuery object 
         * @param {string} id - the id of the record
         * @example sample <!-- materialicons, grid -->
         * <table id="grid"></table>
         * <script>
         *     var grid = $('#grid').grid({
         *         primaryKey: 'ID',
         *         dataSource: '/Players/Get',
         *         detailTemplate: '<div></div>',
         *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'DateOfBirth', type: 'date' } ]
         *     });
         *     grid.on('detailExpand', function (e, $detailWrapper, id) {
         *         var record = grid.getById(id);
         *         $detailWrapper.empty().append('<b>Place Of Birth:</b> ' + record.PlaceOfBirth);
         *     });
         * </script>
         */
        detailExpand: function ($grid, $detailWrapper, id) {
            $grid.triggerHandler('detailExpand', [$detailWrapper, id]);
        },

        /**
         * Event fires when detail row is hiding
         *
         * @event detailCollapse
         * @param {object} e - event data
         * @param {object} detailWrapper - the detail wrapper as jQuery object 
         * @param {string} id - the id of the record
         * @example sample <!-- materialicons, grid -->
         * <table id="grid"></table>
         * <script>
         *     var grid = $('#grid').grid({
         *         primaryKey: 'ID',
         *         dataSource: '/Players/Get',
         *         detailTemplate: '<div></div>',
         *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'DateOfBirth', type: 'date' } ]
         *     });
         *     grid.on('detailExpand', function (e, $detailWrapper, id) {
         *         var record = grid.getById(id);
         *         $detailWrapper.append('<b>Place Of Birth:</b>' + record.PlaceOfBirth);
         *     });
         *     grid.on('detailCollapse', function (e, $detailWrapper, id) {
         *         $detailWrapper.empty();
         *         alert('detailCollapse is fired.');
         *     });
         * </script>
         */
        detailCollapse: function ($grid, $detailWrapper, id) {
            $grid.triggerHandler('detailCollapse', [$detailWrapper, id]);
        }
    },

    'configure': function ($grid) {
        var column, data = $grid.data();

        $.extend(true, $grid, gj.grid.plugins.expandCollapseRows.public);

        if (typeof (data.detailTemplate) !== 'undefined') {
            column = {
                title: '',
                field: data.primaryKey,
                width: data.defaultIconColumnWidth,
                align: 'center',
                stopPropagation: true,
                cssClass: 'gj-cursor-pointer gj-unselectable',
                tmpl: data.icons.expandRow,
                role: 'expander',
                events: {
                    'click': function (e) {
                        var $cell = $(this), methods = gj.grid.plugins.expandCollapseRows.private;
                        if ($cell.closest('tr').next().attr('data-role') === 'details') {
                            methods.detailCollapse($grid, $cell);
                            methods.removeSelection($grid, e.data.id);
                        } else {
                            methods.detailExpand($grid, $(this));
                            methods.keepSelection($grid, e.data.id);
                        }
                    }
                }
            };
            data.columns = [column].concat(data.columns);

            $grid.on('rowDataBound', function (e, $row, id, record) {
                $row.data('details', $(data.detailTemplate));
            });
            $grid.on('columnShow', function (e, column) {
                gj.grid.plugins.expandCollapseRows.private.updateDetailsColSpan($grid);
            });
            $grid.on('columnHide', function (e, column) {
                gj.grid.plugins.expandCollapseRows.private.updateDetailsColSpan($grid);
            });
            $grid.on('rowRemoving', function (e, $row, id, record) {
                gj.grid.plugins.expandCollapseRows.private.detailCollapse($grid, $row.children('td').first());
            });
            $grid.on('dataBinding', function () {
                $grid.collapseAll();
            });
            $grid.on('pageChanging', function () {
                $grid.collapseAll();
            });
            $grid.on('dataBound', function () {
                var i, $cell, $row, position, data = $grid.data();
                if (data.keepExpandedRows && $.isArray(data.expandedRows)) {
                    for (i = 0; i < data.expandedRows.length; i++) {
                        $row = gj.grid.methods.getRowById($grid, data.expandedRows[i]);
                        if ($row && $row.length) {
                            position = gj.grid.methods.getColumnPositionByRole($grid, 'expander');
                            $cell = $row.children('td:eq(' + position + ')');
                            if ($cell && $cell.length) {
                                gj.grid.plugins.expandCollapseRows.private.detailExpand($grid, $cell);
                            }
                        }
                    }
                }
            });
        }
    }
};
/** 
 * @widget Grid 
 * @plugin Inline Editing
 */
gj.grid.plugins.inlineEditing = {
    renderers: {
        editManager: function (value, record, $cell, $displayEl, id, $grid) {
            var data = $grid.data(),
                $edit = $(data.inlineEditing.editButton).attr('data-key', id),
                $delete = $(data.inlineEditing.deleteButton).attr('data-key', id),
                $update = $(data.inlineEditing.updateButton).attr('data-key', id).hide(),
                $cancel = $(data.inlineEditing.cancelButton).attr('data-key', id).hide();
            $edit.on('click', function (e) {
                $grid.edit($(this).data('key'));
                $edit.hide();
                $delete.hide();
                $update.show();
                $cancel.show();
            });
            $delete.on('click', function (e) {
                $grid.removeRow($(this).data('key'));
            });
            $update.on('click', function (e) {
                $grid.update($(this).data('key'));
                $edit.show();
                $delete.show();
                $update.hide();
                $cancel.hide();
            });
            $cancel.on('click', function (e) {
                $grid.cancel($(this).data('key'));
                $edit.show();
                $delete.show();
                $update.hide();
                $cancel.hide();
            });
            $displayEl.empty().append($edit).append($delete).append($update).append($cancel);
        }
    }
};

gj.grid.plugins.inlineEditing.config = {
    base: {
        defaultColumnSettings: {
            /** Provides a way to specify a custom editing UI for the column.
             * @alias column.editor
             * @type function|boolean
             * @default undefined
             * @example sample <!-- grid, checkbox, bootstrap -->
             * <table id="grid"></table>
             * <script>
             *     function editRenderer($container, currentValue, record) {
             *         $container.append('<input type="text" value="' + currentValue + '" class="gj-width-full"/>');
             *     }
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         uiLibrary: 'bootstrap',
             *         columns: [
             *             { field: 'ID', width: 32 },
             *             { field: 'Name', editor: editRenderer },
             *             { field: 'PlaceOfBirth', editor: true },
             *             { field: 'IsActive', title: 'Active?', type:'checkbox', editor: true, mode: 'editOnly', width: 80, align: 'center' }
             *         ]
             *     });
             * </script>
             * @example Date.And.Dropdown.Material <!-- materialicons, grid, datepicker, dropdown, checkbox -->
             * <table id="grid"></table>
             * <script>
             *     var countries = [ "Bulgaria", "Brazil", "England", "Germany", "Colombia", "Poland" ];
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         columns: [
             *             { field: 'Name', editor: true },
             *             { field: 'Nationality', type: 'dropdown', editor: { dataSource: countries } },
             *             { field: 'DateOfBirth', type: 'date', editor: true },
             *             { field: 'IsActive', title: 'Active?', type:'checkbox', editor: true, mode: 'editOnly', width: 80, align: 'center' }
             *         ]
             *     });
             * </script>
             * @example Date.And.Dropdown.Bootstrap <!-- bootstrap, grid, datepicker, dropdown, checkbox -->
             * <table id="grid"></table>
             * <script>
             *     var countries = [ "Bulgaria", "Brazil", "England", "Germany", "Colombia", "Poland" ];
             *     $('#grid').grid({
             *         uiLibrary: 'bootstrap',
             *         dataSource: '/Players/Get',
             *         columns: [
             *             { field: 'Name', editor: true },
             *             { field: 'Nationality', type: 'dropdown', editor: { dataSource: countries } },
             *             { field: 'DateOfBirth', type: 'date', editor: true },
             *             { field: 'IsActive', title: 'Active?', type:'checkbox', editor: true, mode: 'editOnly', width: 80, align: 'center' }
             *         ]
             *     });
             * </script>
             * @example Date.And.Dropdown.Bootstrap.4 <!-- bootstrap4, materialicons, grid, datepicker, dropdown, checkbox -->
             * <table id="grid"></table>
             * <script>
             *     var countries = [ "Bulgaria", "Brazil", "England", "Germany", "Colombia", "Poland" ];
             *     $('#grid').grid({
             *         uiLibrary: 'bootstrap4',
             *         dataSource: '/Players/Get',
             *         columns: [
             *             { field: 'Name', editor: true },
             *             { field: 'Nationality', type: 'dropdown', editor: { dataSource: countries } },
             *             { field: 'DateOfBirth', type: 'date', editor: true },
             *             { field: 'IsActive', title: 'Active?', type:'checkbox', editor: true, mode: 'editOnly', width: 80, align: 'center' }
             *         ]
             *     });
             * </script>
             */
            editor: undefined,

            /** Provides a way to specify a display mode for the column.
             * @alias column.mode
             * @type readEdit|editOnly|readOnly
             * @default readEdit
             * @example sample <!-- grid -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         columns: [
             *             { field: 'ID', width: 56 },
             *             { field: 'Name', editor: true, mode: 'editOnly' },
             *             { field: 'PlaceOfBirth', editor: true, mode: 'readOnly' }
             *         ]
             *     });
             * </script>
             */
            mode: 'readEdit'
        },
        inlineEditing: {

            /** Inline editing mode.
             * @alias inlineEditing.mode
             * @type click|dblclick|command
             * @default 'click'
             * @example Double.Click <!-- grid -->
             * <table id="grid"></table>
             * <script>
             *     var grid = $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         primaryKey: 'ID',
             *         inlineEditing: { mode: 'dblclick' },
             *         columns: [
             *             { field: 'ID', width: 56 },
             *             { field: 'Name', editor: true },
             *             { field: 'PlaceOfBirth', editor: true }
             *         ]
             *     });
             * </script>
             * @example Command <!-- materialicons, dropdown, grid -->
             * <table id="grid"></table>
             * <script>
             *     var grid, data = [
             *         { 'ID': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
             *         { 'ID': 2, 'Name': 'Ronaldo Luís Nazário de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
             *         { 'ID': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' },
             *         { 'ID': 4, 'Name': 'Manuel Neuer', 'PlaceOfBirth': 'Gelsenkirchen, West Germany' },
             *         { 'ID': 5, 'Name': 'James Rodríguez', 'PlaceOfBirth': 'Cúcuta, Colombia' },
             *         { 'ID': 6, 'Name': 'Dimitar Berbatov', 'PlaceOfBirth': 'Blagoevgrad, Bulgaria' }
             *     ];
             *     grid = $('#grid').grid({
             *         dataSource: data,
             *         primaryKey: 'ID',
             *         inlineEditing: { mode: 'command' },
             *         columns: [
             *             { field: 'ID', width: 56 },
             *             { field: 'Name', editor: true },
             *             { field: 'PlaceOfBirth', editor: true }
             *         ],
             *         pager: { limit: 3 }
             *     });
             * </script>
             */
            mode: 'click',
                
            /** If set to true, add column with buttons for edit, delete, update and cancel at the end of the grid.
             * @alias inlineEditing.managementColumn
             * @type Boolean
             * @default true
             * @example True <!-- materialicons, grid, checkbox -->
             * <table id="grid"></table>
             * <script>
             *     var grid, data = [
             *         { 'ID': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria', IsActive: false },
             *         { 'ID': 2, 'Name': 'Ronaldo Luís Nazário de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil', IsActive: false },
             *         { 'ID': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England', IsActive: false },
             *         { 'ID': 4, 'Name': 'Manuel Neuer', 'PlaceOfBirth': 'Gelsenkirchen, West Germany', IsActive: true },
             *         { 'ID': 5, 'Name': 'James Rodríguez', 'PlaceOfBirth': 'Cúcuta, Colombia', IsActive: true },
             *         { 'ID': 6, 'Name': 'Dimitar Berbatov', 'PlaceOfBirth': 'Blagoevgrad, Bulgaria', IsActive: false }
             *     ];
             *     grid = $('#grid').grid({
             *         dataSource: data,
             *         primaryKey: 'ID',
             *         inlineEditing: { mode: 'command', managementColumn: true },
             *         columns: [
             *             { field: 'ID', width: 56 },
             *             { field: 'Name', editor: true },
             *             { field: 'PlaceOfBirth', editor: true },
             *             { field: 'IsActive', title: 'Active?', type: 'checkbox', editor: true, width: 100, align: 'center' }
             *         ]
             *     });
             * </script>
             * @example False <!-- materialicons, grid -->
             * <table id="grid"></table>
             * <script>
             *     var grid, data = [
             *         { 'ID': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
             *         { 'ID': 2, 'Name': 'Ronaldo Luís Nazário de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
             *         { 'ID': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' },
             *         { 'ID': 4, 'Name': 'Manuel Neuer', 'PlaceOfBirth': 'Gelsenkirchen, West Germany' },
             *         { 'ID': 5, 'Name': 'James Rodríguez', 'PlaceOfBirth': 'Cúcuta, Colombia' },
             *         { 'ID': 6, 'Name': 'Dimitar Berbatov', 'PlaceOfBirth': 'Blagoevgrad, Bulgaria' }
             *     ];
             *     grid = $('#grid').grid({
             *         dataSource: data,
             *         primaryKey: 'ID',
             *         inlineEditing: { mode: 'command', managementColumn: false },
             *         columns: [
             *             { field: 'ID', width: 56 },
             *             { field: 'Name', editor: true },
             *             { field: 'PlaceOfBirth', editor: true },
             *             { width: 300, align: 'center', renderer: gj.grid.plugins.inlineEditing.renderers.editManager }
             *         ]
             *     });
             * </script>
             * @example Bootstrap <!-- bootstrap, grid -->
             * <table id="grid"></table>
             * <script>
             *     var grid, data = [
             *         { 'ID': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
             *         { 'ID': 2, 'Name': 'Ronaldo Luís Nazário de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
             *         { 'ID': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' },
             *         { 'ID': 4, 'Name': 'Manuel Neuer', 'PlaceOfBirth': 'Gelsenkirchen, West Germany' },
             *         { 'ID': 5, 'Name': 'James Rodríguez', 'PlaceOfBirth': 'Cúcuta, Colombia' },
             *         { 'ID': 6, 'Name': 'Dimitar Berbatov', 'PlaceOfBirth': 'Blagoevgrad, Bulgaria' }
             *     ];
             *     grid = $('#grid').grid({
             *         dataSource: data,
             *         primaryKey: 'ID',
             *         inlineEditing: { mode: 'command' },
             *         uiLibrary: 'bootstrap',
             *         columns: [
             *             { field: 'ID', width: 34 },
             *             { field: 'Name', editor: true },
             *             { field: 'PlaceOfBirth', editor: true }
             *         ],
             *         pager: { limit: 3 }
             *     });
             * </script>
             * @example Bootstrap.4 <!-- materialicons, bootstrap4, grid -->
             * <table id="grid"></table>
             * <script>
             *     var grid, data = [
             *         { 'ID': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
             *         { 'ID': 2, 'Name': 'Ronaldo Luís Nazário de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
             *         { 'ID': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' },
             *         { 'ID': 4, 'Name': 'Manuel Neuer', 'PlaceOfBirth': 'Gelsenkirchen, West Germany' },
             *         { 'ID': 5, 'Name': 'James Rodríguez', 'PlaceOfBirth': 'Cúcuta, Colombia' },
             *         { 'ID': 6, 'Name': 'Dimitar Berbatov', 'PlaceOfBirth': 'Blagoevgrad, Bulgaria' }
             *     ];
             *     grid = $('#grid').grid({
             *         dataSource: data,
             *         primaryKey: 'ID',
             *         inlineEditing: { mode: 'command' },
             *         uiLibrary: 'bootstrap4',
             *         columns: [
             *             { field: 'ID', width: 34 },
             *             { field: 'Name', editor: true },
             *             { field: 'PlaceOfBirth', editor: true }
             *         ],
             *         pager: { limit: 3 }
             *     });
             * </script>
            */
            managementColumn: true,

            managementColumnConfig: { width: 300, align: 'center', renderer: gj.grid.plugins.inlineEditing.renderers.editManager, cssClass: 'gj-grid-management-column' }
        }
    },

    bootstrap: {
        inlineEditing: {
            managementColumnConfig: { width: 200, align: 'center', renderer: gj.grid.plugins.inlineEditing.renderers.editManager, cssClass: 'gj-grid-management-column' }
        }
    },

    bootstrap4: {
        inlineEditing: {
            managementColumnConfig: { width: 200, align: 'center', renderer: gj.grid.plugins.inlineEditing.renderers.editManager, cssClass: 'gj-grid-management-column' }
        }
    }
};

gj.grid.plugins.inlineEditing.private = {
    localization: function (data) {
        if (data.uiLibrary === 'bootstrap' || data.uiLibrary === 'bootstrap4') {
            data.inlineEditing.editButton = '<button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> ' + gj.grid.messages[data.locale].Edit + '</button>';
            data.inlineEditing.deleteButton = '<button type="button" class="btn btn-default btn-sm gj-margin-left-10"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> ' + gj.grid.messages[data.locale].Delete + '</button>';
            data.inlineEditing.updateButton = '<button type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span> ' + gj.grid.messages[data.locale].Update + '</button>';
            data.inlineEditing.cancelButton = '<button type="button" class="btn btn-default btn-sm gj-margin-left-10"><span class="glyphicon glyphicon-ban-circle" aria-hidden="true"></span> ' + gj.grid.messages[data.locale].Cancel + '</button>';
        } else {
            data.inlineEditing.editButton = '<button class="gj-button-md"><i class="material-icons">mode_edit</i> ' + gj.grid.messages[data.locale].Edit.toUpperCase() + '</button>';
            data.inlineEditing.deleteButton = '<button class="gj-button-md"><i class="material-icons">delete</i> ' + gj.grid.messages[data.locale].Delete.toUpperCase() + '</button>';
            data.inlineEditing.updateButton = '<button class="gj-button-md"><i class="material-icons">check_circle</i> ' + gj.grid.messages[data.locale].Update.toUpperCase() + '</button>';
            data.inlineEditing.cancelButton = '<button class="gj-button-md"><i class="material-icons">cancel</i> ' +gj.grid.messages[data.locale].Cancel.toUpperCase() + '</button>';
        }
    },

    editMode: function ($grid, $cell, column, record) {
        var $displayContainer, $editorContainer, $editorField, value, config, data = $grid.data();
        if ($cell.attr('data-mode') !== 'edit' && column.editor) {
            gj.grid.plugins.inlineEditing.private.updateOtherCells($grid, column.mode);
            $displayContainer = $cell.find('div[data-role="display"]').hide();
            $editorContainer = $cell.find('div[data-role="edit"]').show();
            if ($editorContainer.length === 0) {
                $editorContainer = $('<div data-role="edit" />');
                $cell.append($editorContainer);
            }
            value = column.type === 'checkbox' ? record[column.field] : $displayContainer.html();
            $editorField = $editorContainer.find('input, select, textarea').first();
            if ($editorField.length) {
                column.type === 'checkbox' ? $editorField.prop('checked', value) : $editorField.val(value);
            } else {
                if (typeof (column.editor) === 'function') {
                    column.editor($editorContainer, value, record);
                } else {
                    if ('checkbox' === column.type) {
                        $editorField = $('<input type="checkbox" />').prop('checked', value);
                        $editorContainer.append($editorField);
                        $editorField.checkbox({ uiLibrary: data.uiLibrary });
                    } else if ('date' === column.type) {
                        $editorField = $('<input type="text" width="100%"/>');
                        $editorContainer.append($editorField);
                        config = typeof column.editor === "object" ? column.editor : {};
                        config.uiLibrary = data.uiLibrary;
                        config.fontSize = $grid.css('font-size');
                        $editorField.datepicker(config).value($displayContainer.html());
                    } else if ('dropdown' === column.type) {
                        $editorField = $('<select type="text" width="100%"/>');
                        $editorContainer.append($editorField);
                        config = typeof column.editor === "object" ? column.editor : {};
                        config.uiLibrary = data.uiLibrary;
                        config.fontSize = $grid.css('font-size');
                        $editorField.dropdown(config).value($displayContainer.html());
                    } else {
                        $editorField = $('<input type="text" value="' + value + '" class="gj-width-full"/>');
                        if (data.uiLibrary === 'materialdesign') {
                            $editorField.addClass('gj-textbox-md').css('font-size', $grid.css('font-size'));
                        }
                        $editorContainer.append($editorField);
                    }
                }
                $editorField = $editorContainer.find('input, select, textarea').first();
                if (data.inlineEditing.mode !== 'command' && column.mode !== 'editOnly') {
                    $editorField.on('keyup', function (e) {
                        if (e.keyCode === 13 || e.keyCode === 27) {
                            gj.grid.plugins.inlineEditing.private.displayMode($grid, $cell, column);
                        }
                    });
                }
            }
            if ($editorField.prop('tagName').toUpperCase() === "INPUT" && $editorField.prop('type').toUpperCase() === 'TEXT') {
                gj.grid.plugins.inlineEditing.private.setCaretAtEnd($editorField[0]);
            } else {
                $editorField.focus();
            }
            $cell.attr('data-mode', 'edit');
        }
    },

    setCaretAtEnd: function (elem) {
        var elemLen;
        if (elem) {
            elemLen = elem.value.length;
            if (document.selection) { // For IE Only
                elem.focus();
                var oSel = document.selection.createRange();
                oSel.moveStart('character', -elemLen);
                oSel.moveStart('character', elemLen);
                oSel.moveEnd('character', 0);
                oSel.select();
            } else if (elem.selectionStart || elem.selectionStart == '0') { // Firefox/Chrome                
                elem.selectionStart = elemLen;
                elem.selectionEnd = elemLen;
                elem.focus();
            }
        }
    },

    displayMode: function ($grid, $cell, column, cancel) {
        var $editorContainer, $displayContainer, $ele, newValue, oldValue, record, position, style = '';
        if ($cell.attr('data-mode') === 'edit' && column.mode !== 'editOnly') {
            $editorContainer = $cell.find('div[data-role="edit"]');
            $displayContainer = $cell.find('div[data-role="display"]');
            $ele = $editorContainer.find('input, select, textarea').first();
            newValue = column.type === 'checkbox' ? $ele.prop('checked') : $ele.val();
            position = $cell.parent().data('position');
            record = $grid.get(position);
            oldValue = column.type === 'checkbox' ? record[column.field] : $displayContainer.html();
            if (cancel !== true && newValue !== oldValue) {
                record[column.field] = column.type === 'date' ? gj.core.parseDate(newValue, column.format) : newValue;
                if (column.mode !== 'editOnly') {
                    gj.grid.methods.renderDisplayElement($grid, $displayContainer, column, record, gj.grid.methods.getId(record, $grid.data('primaryKey'), position), 'update');
                    if ($cell.find('span.gj-dirty').length === 0) {
                        $cell.prepend($('<span class="gj-dirty" />'));
                    }
                }
                gj.grid.plugins.inlineEditing.events.cellDataChanged($grid, $cell, column, record, oldValue, newValue);
                gj.grid.plugins.inlineEditing.private.updateChanges($grid, column, record, newValue);
            }
            $editorContainer.hide();
            $displayContainer.show();
            $cell.attr('data-mode', 'display');
        }
    },

    updateOtherCells: function($grid, mode) {
        var data = $grid.data();
        if (data.inlineEditing.mode !== 'command' && mode !== 'editOnly') {
            $grid.find('div[data-role="edit"]:visible').parent('td').each(function () {
                var $cell = $(this),
                    column = data.columns[$cell.index()];
                gj.grid.plugins.inlineEditing.private.displayMode($grid, $cell, column);
            });
        }
    },

    updateChanges: function ($grid, column, sourceRecord, newValue) {
        var targetRecords, filterResult, newRecord, data = $grid.data();
        if (!data.guid) {
            data.guid = gj.grid.plugins.inlineEditing.private.generateGUID();
        }
        if (data.primaryKey) {
            targetRecords = JSON.parse(sessionStorage.getItem('gj.grid.' + data.guid));
            if (targetRecords) {
                filterResult = targetRecords.filter(function (record) {
                    return record[data.primaryKey] === sourceRecord[data.primaryKey];
                });
            } else {
                targetRecords = [];
            }
            if (filterResult && filterResult.length === 1) {
                filterResult[0][column.field] = newValue;
            } else {
                newRecord = {};
                newRecord[data.primaryKey] = sourceRecord[data.primaryKey];
                if (data.primaryKey !== column.field) {
                    newRecord[column.field] = newValue;
                }
                targetRecords.push(newRecord);
            }
            sessionStorage.setItem('gj.grid.' + data.guid, JSON.stringify(targetRecords));
        }
    },

    generateGUID: function () {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000)
              .toString(16)
              .substring(1);
        }
        return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
    }
};

gj.grid.plugins.inlineEditing.public = {
    /**
     * Return array with all changes
     * @method
     * @return array
     * @example sample <!-- grid, grid.inlineEditing -->
     * <button id="btnGetChanges" class="gj-button-md">Get Changes</button>
     * <br/><br/>
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         primaryKey: 'ID',
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID' }, { field: 'Name', editor: true }, { field: 'PlaceOfBirth', editor: true } ]
     *     });
     *     $('#btnGetChanges').on('click', function () {
     *         alert(JSON.stringify(grid.getChanges()));
     *     });
     * </script>
     */
    getChanges: function () {
        return JSON.parse(sessionStorage.getItem('gj.grid.' + this.data().guid));
    },

    /**
     * Enable edit mode for all editable cells within a row.
     * @method
     * @param {string} id - The id of the row that needs to be edited
     * @return grid
     * @example Edit.Row <!-- grid -->
     * <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
     * <table id="grid"></table>
     * <script>
     *     var grid, renderer;
     *     renderer = function (value, record, $cell, $displayEl, id) {
     *         var $editBtn = $('<i class="fa fa-pencil gj-cursor-pointer" data-key="' + id + '"></i>'),
     *             $updateBtn = $('<i class="fa fa-save gj-cursor-pointer" data-key="' + id + '"></i>').hide();
     *         $editBtn.on('click', function (e) {
     *             grid.edit($(this).data('key'));
     *             $editBtn.hide();
     *             $updateBtn.show();
     *         });
     *         $updateBtn.on('click', function (e) {
     *             grid.update($(this).data('key'));
     *             $editBtn.show();
     *             $updateBtn.hide();
     *         });
     *         $displayEl.append($editBtn).append($updateBtn);
     *     }
     *     grid = $('#grid').grid({
     *         primaryKey: 'ID',
     *         dataSource: '/Players/Get',
     *         inlineEditing: { mode: 'command', managementColumn: false },
     *         columns: [ 
     *             { field: 'ID', width: 56 },
     *             { field: 'Name', editor: true }, 
     *             { field: 'PlaceOfBirth', editor: true },
     *             { width: 56, align: 'center', renderer: renderer }
     *         ]
     *     });
     * </script>
     */
    edit: function (id) {
        var i, record = this.getById(id),
            $cells = gj.grid.methods.getRowById(this, id).find('td'),
            columns = this.data('columns');

        for (i = 0; i < $cells.length; i++) {
            gj.grid.plugins.inlineEditing.private.editMode(this, $($cells[i]), columns[i], record);
        }
            
        return this;
    },

    /**
     * Update all editable cells within a row, when the row is in edit mode.
     * @method
     * @param {string} id - The id of the row that needs to be updated
     * @return grid
     * @fires rowDataChanged
     * @example Update.Row <!-- grid -->
     * <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
     * <table id="grid"></table>
     * <script>
     *     var grid, renderer;
     *     renderer = function (value, record, $cell, $displayEl, id) {
     *         var $editBtn = $('<i class="fa fa-pencil gj-cursor-pointer" data-key="' + id + '"></i>'),
     *             $updateBtn = $('<i class="fa fa-save gj-cursor-pointer" data-key="' + id + '"></i>').hide();
     *         $editBtn.on('click', function (e) {
     *             grid.edit($(this).data('key'));
     *             $editBtn.hide();
     *             $updateBtn.show();
     *         });
     *         $updateBtn.on('click', function (e) {
     *             grid.update($(this).data('key'));
     *             $editBtn.show();
     *             $updateBtn.hide();
     *         });
     *         $displayEl.append($editBtn).append($updateBtn);
     *     }
     *     grid = $('#grid').grid({
     *         primaryKey: 'ID',
     *         dataSource: '/Players/Get',
     *         inlineEditing: { mode: 'command', managementColumn: false },
     *         columns: [ 
     *             { field: 'ID', width: 56 },
     *             { field: 'Name', editor: true }, 
     *             { field: 'PlaceOfBirth', editor: true },
     *             { width: 56, align: 'center', renderer: renderer }
     *         ]
     *     });
     * </script>
     */
    update: function (id) {
        var i, record = this.getById(id),
            $cells = gj.grid.methods.getRowById(this, id).find('td'),
            columns = this.data('columns');

        for (i = 0; i < $cells.length; i++) {
            gj.grid.plugins.inlineEditing.private.displayMode(this, $($cells[i]), columns[i], false);
        }

        gj.grid.plugins.inlineEditing.events.rowDataChanged(this, id, record);

        return this;
    },

    /**
     * Cancel the edition of all editable cells, when the row is in edit mode.
     * @method
     * @param {string} id - The id of the row where you need to undo all changes
     * @return grid
     * @example Cancel.Row <!-- grid -->
     * <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
     * <table id="grid"></table>
     * <script>
     *     var grid, renderer;
     *     renderer = function (value, record, $cell, $displayEl, id) {
     *         var $editBtn = $('<i class="fa fa-pencil gj-cursor-pointer" data-key="' + id + '"></i>'),
     *             $cancelBtn = $('<i class="fa fa-undo gj-cursor-pointer" data-key="' + id + '"></i>').hide();
     *         $editBtn.on('click', function (e) {
     *             grid.edit($(this).data('key'));
     *             $editBtn.hide();
     *             $cancelBtn.show();
     *         });
     *         $cancelBtn.on('click', function (e) {
     *             grid.cancel($(this).data('key'));
     *             $editBtn.show();
     *             $cancelBtn.hide();
     *         });
     *         $displayEl.append($editBtn).append($cancelBtn);
     *     }
     *     grid = $('#grid').grid({
     *         primaryKey: 'ID',
     *         dataSource: '/Players/Get',
     *         inlineEditing: { mode: 'command', managementColumn: false },
     *         columns: [ 
     *             { field: 'ID', width: 56 },
     *             { field: 'Name', editor: true }, 
     *             { field: 'PlaceOfBirth', editor: true },
     *             { width: 56, align: 'center', renderer: renderer }
     *         ]
     *     });
     * </script>
     */
    cancel: function (id) {
        var i, record = this.getById(id),
            $cells = gj.grid.methods.getRowById(this, id).find('td'),
            columns = this.data('columns');

        for (i = 0; i < $cells.length; i++) {
            gj.grid.plugins.inlineEditing.private.displayMode(this, $($cells[i]), columns[i], true);
        }

        return this;
    }
};

gj.grid.plugins.inlineEditing.events = {
    /**
     * Event fires after inline edit of a cell in the grid.
     *
     * @event cellDataChanged
     * @param {object} e - event data
     * @param {object} $cell - the cell presented as jquery object 
     * @param {object} column - the column configuration data
     * @param {object} record - the data of the row record
     * @param {object} oldValue - the old cell value
     * @param {object} newValue - the new cell value
     * @example sample <!-- grid -->
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         dataSource: '/Players/Get',
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name', editor: true }, { field: 'PlaceOfBirth', editor: true } ]
     *     });
     *     grid.on('cellDataChanged', function (e, $cell, column, record, oldValue, newValue) {
     *         alert('"' + oldValue + '" is changed to "' + newValue + '"');
     *     });
     * </script>
     */
    cellDataChanged: function ($grid, $cell, column, record, oldValue, newValue) {
        $grid.triggerHandler('cellDataChanged', [$cell, column, record, oldValue, newValue]);
    },

    /**
     * Event fires after inline edit of a row in the grid.
     *
     * @event rowDataChanged
     * @param {object} e - event data
     * @param {object} id - the id of the record
     * @param {object} record - the data of the row record
     * @example sample <!-- materialicons, grid -->
     * <table id="grid"></table>
     * <script>
     *     var grid = $('#grid').grid({
     *         primaryKey: 'ID',
     *         dataSource: '/Players/Get',
     *         inlineEditing: { mode: 'command' },
     *         columns: [ { field: 'ID', width: 56 }, { field: 'Name', editor: true }, { field: 'PlaceOfBirth', editor: true } ]
     *     });
     *     grid.on('rowDataChanged', function (e, id, record) {
     *         alert('Record with id="' + id + '" is changed to "' + JSON.stringify(record) + '"');
     *     });
     * </script>
     */
    rowDataChanged: function ($grid, id, record) {
        $grid.triggerHandler('rowDataChanged', [id, record]);
    }
};

gj.grid.plugins.inlineEditing.configure = function ($grid, fullConfig, clientConfig) {
    var data = $grid.data();
    $.extend(true, $grid, gj.grid.plugins.inlineEditing.public);
    if (clientConfig.inlineEditing) {
        $grid.on('dataBound', function () {
            $grid.find('span.gj-dirty').remove();
        });
    }
    if (data.inlineEditing.mode === 'command') {
        gj.grid.plugins.inlineEditing.private.localization(data);
        if (fullConfig.inlineEditing.managementColumn) {
            data.columns.push(fullConfig.inlineEditing.managementColumnConfig);
        }
    } else {
        $grid.on('cellDataBound', function (e, $displayEl, id, column, record) {
            if (column.editor) {
                if (column.mode === 'editOnly') {
                    gj.grid.plugins.inlineEditing.private.editMode($grid, $displayEl.parent(), column, record);
                } else {
                    $displayEl.parent('td').on(data.inlineEditing.mode === 'dblclick' ? 'dblclick' : 'click', function () {
                        gj.grid.plugins.inlineEditing.private.editMode($grid, $displayEl.parent(), column, record);
                    });
                }
            }
        });
    }
};

/** 
 * @widget Grid 
 * @plugin Optimistic Persistence
 */
gj.grid.plugins.optimisticPersistence = {

    config: {
        base: {
            optimisticPersistence: {
                /** Array that contains a list with param names that needs to be saved in the localStorage. You need to specify guid on the initialization of the grid in order to enable this feature.
                 * @additionalinfo This feature is using <a href="https://developer.mozilla.org/en/docs/Web/API/Window/localStorage" target="_blank">HTML5 localStorage</a> to store params and values.
                 * You can clear the data saved in localStorage when you clear your browser cache.
                 * @alias optimisticPersistence.localStorage
                 * @type array
                 * @default undefined
                 * @example sample <!-- bootstrap, grid  -->
                 * <p>Change the page and/or page size and then refresh the grid.</p>
                 * <table id="grid"></table>
                 * <script>
                 *     var grid = $('#grid').grid({
                 *         guid: '58d47231-ac7b-e6d2-ddba-5e0195b31f2e',
                 *         uiLibrary: 'bootstrap',
                 *         dataSource: '/Players/Get',
                 *         columns: [ { field: 'ID', width: 36 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
                 *         optimisticPersistence: { localStorage: ["page", "limit"] },
                 *         pager: { limit: 2, sizes: [2, 5, 10, 20] }
                 *     });
                 * </script>
                 */
                localStorage: undefined,

                /** Array that contains a list with param names that needs to be saved in the sessionStorage. You need to specify guid on the initialization of the grid in order to enable this feature.
                 * @additionalinfo This feature is using <a href="https://developer.mozilla.org/en/docs/Web/API/Window/sessionStorage" target="_blank">HTML5 sessionStorage</a> to store params and values.
                 * You can clear the data saved in sessionStorage when you open and close the browser.
                 * @alias optimisticPersistence.sessionStorage
                 * @type array
                 * @default undefined
                 * @example sample <!-- bootstrap, grid  -->
                 * <p>Change the page and/or page size and then refresh the grid. </p>
                 * <table id="grid"></table>
                 * <script>
                 *     var grid = $('#grid').grid({
                 *         guid: '58d47231-ac7b-e6d2-ddba-5e0195b31f2f',
                 *         uiLibrary: 'bootstrap',
                 *         dataSource: '/Players/Get',
                 *         columns: [ { field: 'ID', width: 36 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
                 *         optimisticPersistence: { sessionStorage: ["page", "limit"] },
                 *         pager: { limit: 2, sizes: [2, 5, 10, 20] }
                 *     });
                 * </script>
                 */
                sessionStorage: undefined
            }
        }
    },

    private: {
        applyParams: function ($grid) {
            var data = $grid.data(),
                params = {}, storage;
            storage = JSON.parse(sessionStorage.getItem('gj.grid.' + data.guid));
            if (storage && storage.optimisticPersistence) {
                $.extend(params, storage.optimisticPersistence);
            }
            storage = JSON.parse(localStorage.getItem('gj.grid.' + data.guid));
            if (storage && storage.optimisticPersistence) {
                $.extend(params, storage.optimisticPersistence);
            }
            $.extend(data.params, params);
        },

        saveParams: function ($grid) {
            var i, param,
                data = $grid.data(),
                storage = { optimisticPersistence: {} };

            if (data.optimisticPersistence.sessionStorage) {
                for (i = 0; i < data.optimisticPersistence.sessionStorage.length; i++) {
                    param = data.optimisticPersistence.sessionStorage[i];
                    storage.optimisticPersistence[param] = data.params[param];
                }
                storage = $.extend(true, JSON.parse(sessionStorage.getItem('gj.grid.' + data.guid)), storage);
                sessionStorage.setItem('gj.grid.' + data.guid, JSON.stringify(storage));
            }

            if (data.optimisticPersistence.localStorage) {
                storage = { optimisticPersistence: {} };
                for (i = 0; i < data.optimisticPersistence.localStorage.length; i++) {
                    param = data.optimisticPersistence.localStorage[i];
                    storage.optimisticPersistence[param] = data.params[param];
                }
                storage = $.extend(true, JSON.parse(localStorage.getItem('gj.grid.' + data.guid)), storage);
                localStorage.setItem('gj.grid.' + data.guid, JSON.stringify(storage));
            }
        }
    },

    configure: function ($grid, fullConfig, clientConfig) {
        if (fullConfig.guid) {
            if (fullConfig.optimisticPersistence.localStorage || fullConfig.optimisticPersistence.sessionStorage) {
                gj.grid.plugins.optimisticPersistence.private.applyParams($grid);
                $grid.on('dataBound', function (e) {
                    gj.grid.plugins.optimisticPersistence.private.saveParams($grid);
                });
            }
        }
    }
};
/**
 * @widget Grid
 * @plugin Pagination
 */
gj.grid.plugins.pagination = {
    config: {
        base: {
            style: {
                pager: {
                    cell: '',
                    stateDisabled: '',
                    activeButton: ''
                }
            },

            paramNames: {
                /** The name of the parameter that is going to send the number of the page.
                 * The pager should be enabled in order this parameter to be in use.
                 * @alias paramNames.page
                 * @type string
                 * @default "page"
                 */
                page: 'page',

                /** The name of the parameter that is going to send the maximum number of records per page.
                 * The pager should be enabled in order this parameter to be in use.
                 * @alias paramNames.limit
                 * @type string
                 * @default "limit"
                 */
                limit: 'limit'
            },

            pager: {
                /** The maximum number of records that can be show by page.
                 * @alias pager.limit
                 * @type number
                 * @default 10
                 * @example local.data <!-- materialicons, grid -->
                 * <table id="grid"></table>
                 * <script>
                 *     var data, grid;
                 *     data = [
                 *         { 'ID': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
                 *         { 'ID': 2, 'Name': 'Ronaldo Luis Nazario de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
                 *         { 'ID': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' }
                 *     ];
                 *     grid = $('#grid').grid({
                 *         dataSource: data,
                 *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
                 *         pager: { limit: 2 }
                 *     });
                 * </script>
                 * @example remote.data <!-- materialicons, grid -->
                 * <table id="grid"></table>
                 * <script>
                 *     var grid = $('#grid').grid({
                 *         dataSource: '/Players/Get',
                 *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
                 *         pager: { limit: 2 }
                 *     });
                 * </script>
                 */
                limit: 10,

                /** Array that contains the possible page sizes of the grid.
                 * When this setting is set, then a drop down with the options for each page size is visualized in the pager.
                 * @alias pager.sizes
                 * @type array
                 * @default [5, 10, 20, 100]
                 * @example Bootstrap.3 <!-- bootstrap, grid, grid.pagination  -->
                 * <table id="grid"></table>
                 * <script>
                 *     var grid = $('#grid').grid({
                 *         dataSource: '/Players/Get',
                 *         uiLibrary: 'bootstrap',
                 *         columns: [ { field: 'ID', width: 36 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
                 *         pager: { limit: 2, sizes: [2, 5, 10, 20] }
                 *     });
                 * </script>
                 * @example Material.Design <!-- materialicons, grid, grid.pagination  -->
                 * <table id="grid"></table>
                 * <script>
                 *     var grid = $('#grid').grid({
                 *         dataSource: '/Players/Get',
                 *         uiLibrary: 'materialdesign',
                 *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
                 *         pager: { limit: 2, sizes: [2, 5, 10, 20] }
                 *     });
                 * </script>
                 */
                sizes: [5, 10, 20, 100],

                /** Array that contains a list with jquery objects that are going to be used on the left side of the pager.
                 * @alias pager.leftControls
                 * @type array
                 * @default array
                 * @example Font.Awesome <!-- fontawesome, grid  -->
                 * <style>
                 * .icon-disabled { color: #ccc; }
                 * </style>
                 * <table id="grid"></table>
                 * <script>
                 *     var grid = $('#grid').grid({
                 *         dataSource: '/Players/Get',
                 *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
                 *         style: {
                 *             pager: {
                 *                 stateDisabled: 'icon-disabled'
                 *             }
                 *         },
                 *         pager: { 
                 *             limit: 2, 
                 *             sizes: [2, 5, 10, 20],
                 *             leftControls: [
                 *                 $('<div title="First" data-role="page-first" class="gj-grid-icon fa fa-fast-backward" aria-hidden="true"></div>'),
                 *                 $('<div title="Previous" data-role="page-previous" class="gj-grid-icon fa fa-backward" aria-hidden="true"></div>'),
                 *                 $('<div> Page </div>'),
                 *                 $('<div></div>').append($('<input type="text" data-role="page-number" style="margin: 0 5px; width: 34px;" value="0">')),
                 *                 $('<div>of&nbsp;</div>'),
                 *                 $('<div data-role="page-label-last" style="margin-right: 5px;">0</div>'),
                 *                 $('<div title="Next" data-role="page-next" class="gj-grid-icon fa fa-forward" aria-hidden="true"></div>'),
                 *                 $('<div title="Last" data-role="page-last" class="gj-grid-icon fa fa-fast-forward" aria-hidden="true"></div>'),
                 *                 $('<div title="Reload" data-role="page-refresh" class="gj-grid-icon fa fa-refresh" aria-hidden="true"></div>'),
                 *                 $('<div></div>').append($('<select data-role="page-size" style="margin: 0 5px; width: 50px;"></select>'))
                 *             ],
                 *             rightControls: [
                 *                 $('<div>Displaying records&nbsp;</div>'),
                 *                 $('<div data-role="record-first">0</div>'),
                 *                 $('<div>&nbsp;-&nbsp;</div>'),
                 *                 $('<div data-role="record-last">0</div>'),
                 *                 $('<div>&nbsp;of&nbsp;</div>'),
                 *                 $('<div data-role="record-total">0</div>').css({ "margin-right": "5px" })
                 *             ]
                 *         }
                 *     });
                 * </script>
                 */
                leftControls: undefined,

                /** Array that contains a list with jquery objects that are going to be used on the right side of the pager.
                 * @alias pager.rightControls
                 * @type array
                 * @default array
                 */
                rightControls: undefined
            }
        },

        bootstrap: {
            style: {
                pager: {
                    cell: 'gj-grid-bootstrap-tfoot-cell',
                    stateDisabled: ''
                }
            }
        },

        bootstrap4: {
            style: {
                pager: {
                    cell: 'gj-grid-bootstrap-tfoot-cell',
                    stateDisabled: ''
                }
            }
        },

        glyphicons: {
            icons: {
                first: '<span class="glyphicon glyphicon-step-backward"></span>',
                previous: '<span class="glyphicon glyphicon-backward"></span>',
                next: '<span class="glyphicon glyphicon-forward"></span>',
                last: '<span class="glyphicon glyphicon-step-forward"></span>',
                refresh: '<span class="glyphicon glyphicon-refresh"></span>'
            }
        },

        materialicons: {
            icons: {
                first: '<i class="material-icons">first_page</i>',
                previous: '<i class="material-icons">chevron_left</i>',
                next: '<i class="material-icons">chevron_right</i>',
                last: '<i class="material-icons">last_page</i>',
                refresh: '<i class="material-icons">refresh</i>'
            }
        },

        fontawesome: {
            icons: {
                first: '<i class="fa fa-fast-backward" aria-hidden="true"></i>',
                previous: '<i class="fa fa-backward" aria-hidden="true"></i>',
                next: '<i class="fa fa-forward" aria-hidden="true"></i>',
                last: '<i class="fa fa-fast-forward" aria-hidden="true"></i>',
                refresh: '<i class="fa fa-refresh" aria-hidden="true"></i>'
            }
        }
    },

    private: {
        init: function ($grid) {
            var $row, $cell, data, controls, $leftPanel, $rightPanel, $tfoot, leftControls, rightControls, i;

            data = $grid.data();

            if (data.pager) {
                if (!data.params[data.paramNames.page]) {
                    data.params[data.paramNames.page] = 1;
                }
                if (!data.params[data.paramNames.limit]) {
                    data.params[data.paramNames.limit] = data.pager.limit;
                }

                gj.grid.plugins.pagination.private.localization(data);

                $row = $('<tr data-role="pager"/>');
                $cell = $('<th/>').addClass(data.style.pager.cell);
                $row.append($cell);

                $leftPanel = $('<div data-role="display" />').css({ 'float': 'left' });
                $rightPanel = $('<div data-role="display" />').css({ 'float': 'right' });
                if (/msie/.test(navigator.userAgent.toLowerCase())) {
                    $rightPanel.css({ 'padding-top': '3px' });
                }

                $cell.append($leftPanel).append($rightPanel);

                $tfoot = $('<tfoot />').append($row);
                $grid.append($tfoot);
                gj.grid.plugins.pagination.private.updatePagerColSpan($grid);

                leftControls = gj.grid.methods.clone(data.pager.leftControls); //clone array
                $.each(leftControls, function () {
                    $leftPanel.append(this);
                });

                rightControls = gj.grid.methods.clone(data.pager.rightControls); //clone array
                $.each(rightControls, function () {
                    $rightPanel.append(this);
                });

                controls = $grid.find('tfoot [data-role]');
                for (i = 0; i < controls.length; i++) {
                    gj.grid.plugins.pagination.private.initPagerControl($(controls[i]), $grid);
                }
            }
        },

        localization: function (data) {
            if (data.uiLibrary === 'bootstrap') {
                gj.grid.plugins.pagination.private.localizationBootstrap(data);
            } else if (data.uiLibrary === 'bootstrap4') {
                gj.grid.plugins.pagination.private.localizationBootstrap4(data);
            } else {
                gj.grid.plugins.pagination.private.localizationMaterialDesign(data);
            }
        },

        localizationBootstrap: function (data) {
            var msg = gj.grid.messages[data.locale];
            if (typeof (data.pager.leftControls) === 'undefined') {
                data.pager.leftControls = [
                    $('<button type="button" class="btn btn-default btn-sm">' + (data.icons.first || msg.First) + '</button>').attr('title', msg.FirstPageTooltip).attr('data-role', 'page-first'),
                    $('<button type="button" class="btn btn-default btn-sm">' + (data.icons.previous || msg.Previous) + '</button>').attr('title', msg.PreviousPageTooltip).attr('data-role', 'page-previous'),
                    $('<div>' + msg.Page + '</div>'),
                    $('<input data-role="page-number" class="form-control input-sm" type="text" value="0">'),
                    $('<div>' + msg.Of + '</div>'),
                    $('<div data-role="page-label-last">0</div>'),
                    $('<button type="button" class="btn btn-default btn-sm">' + (data.icons.next || msg.Next) + '</button>').attr('title', msg.NextPageTooltip).attr('data-role', 'page-next'),
                    $('<button type="button" class="btn btn-default btn-sm">' + (data.icons.last || msg.Last) + '</button>').attr('title', msg.LastPageTooltip).attr('data-role', 'page-last'),
                    $('<button type="button" class="btn btn-default btn-sm">' + (data.icons.refresh || msg.Refresh) + '</button>').attr('title', msg.Refresh).attr('data-role', 'page-refresh'),
                    $('<select data-role="page-size" class="form-control input-sm" width="60"></select>')
                ];
            }
            if (typeof (data.pager.rightControls) === 'undefined') {
                data.pager.rightControls = [
                    $('<div>' + msg.DisplayingRecords + '</div>'),
                    $('<div data-role="record-first">0</div>'),
                    $('<div>-</div>'),
                    $('<div data-role="record-last">0</div>'),
                    $('<div>' + msg.Of + '</div>'),
                    $('<div data-role="record-total">0</div>')
                ];
            }
        },

        localizationBootstrap4: function (data) {
            var msg = gj.grid.messages[data.locale];
            if (typeof (data.pager.leftControls) === 'undefined') {
                data.pager.leftControls = [
                    $('<button class="btn btn-default btn-sm gj-cursor-pointer">' + (data.icons.first || msg.First) + '</button>').attr('title', msg.FirstPageTooltip).attr('data-role', 'page-first'),
                    $('<button class="btn btn-default btn-sm gj-cursor-pointer">' + (data.icons.previous || msg.Previous) + '</button>').attr('title', msg.PreviousPageTooltip).attr('data-role', 'page-previous'),
                    $('<div>' + msg.Page + '</div>'),
                    $('<input data-role="page-number" class="form-control form-control-sm" type="text" value="0">'),
                    $('<div>' + msg.Of + '</div>'),
                    $('<div data-role="page-label-last">0</div>'),
                    $('<button class="btn btn-default btn-sm gj-cursor-pointer">' + (data.icons.next || msg.Next) + '</button>').attr('title', msg.NextPageTooltip).attr('data-role', 'page-next'),
                    $('<button class="btn btn-default btn-sm gj-cursor-pointer">' + (data.icons.last || msg.Last) + '</button>').attr('title', msg.LastPageTooltip).attr('data-role', 'page-last'),
                    $('<button class="btn btn-default btn-sm gj-cursor-pointer">' + (data.icons.refresh || msg.Refresh) + '</button>').attr('title', msg.Refresh).attr('data-role', 'page-refresh'),
                    $('<select data-role="page-size" class="form-control input-sm" width="60"></select>')
                ];
            }
            if (typeof (data.pager.rightControls) === 'undefined') {
                data.pager.rightControls = [
                    $('<div>' + msg.DisplayingRecords + '&nbsp;</div>'),
                    $('<div data-role="record-first">0</div>'),
                    $('<div>-</div>'),
                    $('<div data-role="record-last">0</div>'),
                    $('<div>' + msg.Of + '</div>'),
                    $('<div data-role="record-total">0</div>')
                ];
            }
        },

        localizationMaterialDesign: function (data) {
            var msg = gj.grid.messages[data.locale];
            if (typeof (data.pager.leftControls) === 'undefined') {
                data.pager.leftControls = [];
            }
            if (typeof (data.pager.rightControls) === 'undefined') {
                data.pager.rightControls = [
                    $('<span class="">' + msg.RowsPerPage + '</span>'),
                    $('<select data-role="page-size" class="gj-grid-md-limit-select" width="52"></select></div>'),
                    $('<span class="gj-md-spacer-32">&nbsp;</span>'),
                    $('<span data-role="record-first" class="">0</span>'),
                    $('<span class="">-</span>'),
                    $('<span data-role="record-last" class="">0</span>'),
                    $('<span class="gj-grid-mdl-pager-label">' + msg.Of + '</span>'),
                    $('<span data-role="record-total" class="">0</span>'),
                    $('<span class="gj-md-spacer-32">&nbsp;</span>'),
                    $('<button class="gj-button-md">' + (data.icons.previous || msg.Previous) + '</button>').attr('title', msg.PreviousPageTooltip).attr('data-role', 'page-previous').addClass(data.icons.first ? 'gj-button-md-icon' : ''),
                    $('<span class="gj-md-spacer-24">&nbsp;</span>'),
                    $('<button class="gj-button-md">' + (data.icons.next || msg.Next) + '</button>').attr('title', msg.NextPageTooltip).attr('data-role', 'page-next').addClass(data.icons.first ? 'gj-button-md-icon' : '')
                ];
            }
        },

        initPagerControl: function ($control, $grid) {
            var data = $grid.data();
            switch ($control.data('role')) {
                case 'page-size':
                    if (data.pager.sizes && 0 < data.pager.sizes.length) {
                        $control.show();
                        $.each(data.pager.sizes, function () {
                            $control.append($('<option/>').attr('value', this.toString()).text(this.toString()));
                        });
                        $control.change(function () {
                            var newSize = parseInt(this.value, 10);
                            data.params[data.paramNames.limit] = newSize;
                            gj.grid.plugins.pagination.private.changePage($grid, 1);
                            gj.grid.plugins.pagination.events.pageSizeChange($grid, newSize);
                        });
                        $control.val(data.params[data.paramNames.limit]);
                        if ($.fn.dropdown) {
                            $control.dropdown({
                                uiLibrary: data.uiLibrary,
                                iconsLibrary: data.iconsLibrary,
                                style: {
                                    presenter: 'btn btn-default btn-sm'
                                }
                            });
                        }
                    } else {
                        $control.hide();
                    }
                    break;
                case 'page-refresh':
                    $control.on('click', function () { $grid.reload(); });
                    break;
            }

        },

        reloadPager: function ($grid, totalRecords) {
            var page, limit, lastPage, firstRecord, lastRecord, data, controls, i;

            data = $grid.data();

            if (data.pager) {
                page = (0 === totalRecords) ? 0 : parseInt(data.params[data.paramNames.page], 10);
                limit = parseInt(data.params[data.paramNames.limit], 10);
                lastPage = Math.ceil(totalRecords / limit);
                firstRecord = (0 === page) ? 0 : (limit * (page - 1)) + 1;
                lastRecord = (firstRecord + limit) > totalRecords ? totalRecords : (firstRecord + limit) - 1;

                controls = $grid.find('TFOOT [data-role]');
                for (i = 0; i < controls.length; i++) {
                    gj.grid.plugins.pagination.private.reloadPagerControl($(controls[i]), $grid, page, lastPage, firstRecord, lastRecord, totalRecords);
                }

                gj.grid.plugins.pagination.private.updatePagerColSpan($grid);
            }
        },

        reloadPagerControl: function ($control, $grid, page, lastPage, firstRecord, lastRecord, totalRecords) {
            var newPage;
            switch ($control.data('role')) {
                case 'page-first':
                    gj.grid.plugins.pagination.private.assignPageHandler($grid, $control, 1, page < 2);
                    break;
                case 'page-previous':
                    gj.grid.plugins.pagination.private.assignPageHandler($grid, $control, page - 1, page < 2);
                    break;
                case 'page-number':
                    $control.val(page).off('change').on('change', gj.grid.plugins.pagination.private.createChangePageHandler($grid, page, lastPage));
                    break;
                case 'page-label-last':
                    $control.text(lastPage);
                    break;
                case 'page-next':
                    gj.grid.plugins.pagination.private.assignPageHandler($grid, $control, page + 1, lastPage === page);
                    break;
                case 'page-last':
                    gj.grid.plugins.pagination.private.assignPageHandler($grid, $control, lastPage, lastPage === page);
                    break;
                case 'page-button-one':
                    newPage = (page === 1) ? 1 : ((page == lastPage) ? (page - 2) : (page - 1));
                    gj.grid.plugins.pagination.private.assignButtonHandler($grid, $control, page, newPage, lastPage);
                    break;
                case 'page-button-two':
                    newPage = (page === 1) ? 2 : ((page == lastPage) ? lastPage - 1 : page);
                    gj.grid.plugins.pagination.private.assignButtonHandler($grid, $control, page, newPage, lastPage);
                    break;
                case 'page-button-three':
                    newPage = (page === 1) ? page + 2 : ((page == lastPage) ? page : (page + 1));
                    gj.grid.plugins.pagination.private.assignButtonHandler($grid, $control, page, newPage, lastPage);
                    break;
                case 'record-first':
                    $control.text(firstRecord);
                    break;
                case 'record-last':
                    $control.text(lastRecord);
                    break;
                case 'record-total':
                    $control.text(totalRecords);
                    break;
            }
        },

        assignPageHandler: function ($grid, $control, newPage, disabled) {
            var style = $grid.data().style.pager;
            if (disabled) {
                $control.addClass(style.stateDisabled).prop('disabled', true).off('click');
            } else {
                $control.removeClass(style.stateDisabled).prop('disabled', false).off('click').on('click', function () {
                    gj.grid.plugins.pagination.private.changePage($grid, newPage);
                });
            }
        },

        assignButtonHandler: function ($grid, $control, page, newPage, lastPage) {
            var style = $grid.data().style.pager;
            if (newPage < 1 || newPage > lastPage) {
                $control.hide();
            } else {
                $control.show().off('click').text(newPage);
                if (newPage === page) {
                    $control.addClass(style.activeButton);
                } else {
                    $control.removeClass(style.activeButton).on('click', function () {
                        gj.grid.plugins.pagination.private.changePage($grid, newPage);
                    });
                }
            }
        },

        createChangePageHandler: function ($grid, currentPage, lastPage) {
            return function () {
                var data = $grid.data(),
                    newPage = parseInt(this.value, 10);
                if (newPage && !isNaN(newPage) && newPage <= lastPage) {
                    gj.grid.plugins.pagination.private.changePage($grid, newPage);
                } else {
                    this.value = currentPage;
                    alert('Please enter a valid number.');
                }
            };
        },

        changePage: function ($grid, newPage) {
            var data = $grid.data();
            $grid.find('TFOOT [data-role="page-number"]').val(newPage);
            data.params[data.paramNames.page] = newPage;
            gj.grid.plugins.pagination.events.pageChanging($grid, newPage);
            $grid.reload();
        },

        updatePagerColSpan: function ($grid) {
            var $cell = $grid.find('tfoot > tr[data-role="pager"] > th');
            if ($cell && $cell.length) {
                $cell.attr('colspan', gj.grid.methods.countVisibleColumns($grid));
            }
        },
        
        isLastRecordVisible: function ($grid) {
            var result = true,
                data = $grid.data(),
                limit = parseInt(data.params[data.paramNames.limit], 10),
                page = parseInt(data.params[data.paramNames.page], 10),
                count = $grid.count();
            if (limit && page) {
                result = ((page - 1) * limit) + count === data.totalRecords;
            }
            return result;
        }
    },

    public: {
        getAll: function (includeAllRecords) {
            var limit, page, start, data = this.data();
            if (!includeAllRecords && $.isArray(data.dataSource) && data.params[data.paramNames.limit] && data.params[data.paramNames.page]) {
                limit = parseInt(data.params[data.paramNames.limit], 10);
                page = parseInt(data.params[data.paramNames.page], 10);
                start = (page - 1) * limit;
                return data.records.slice(start, start + limit);
            } else {
                return data.records;
            }
        }
    },

    events: {
        /**
         * Triggered when the page size is changed.
         *
         * @event pageSizeChange
         * @param {object} e - event data
         * @param {number} newSize - The new page size
         * @example sample <!-- bootstrap, grid, grid.pagination -->
         * <table id="grid"></table>
         * <script>
         *     var grid = $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         uiLibrary: 'bootstrap',
         *         columns: [ { field: 'ID', width: 36 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
         *         pager: { limit: 2, sizes: [2, 5, 10, 20] }
         *     });
         *     grid.on('pageSizeChange', function (e, newSize) {
         *         alert('The new page size is ' + newSize + '.');
         *     });
         * </script>
         */
        pageSizeChange: function ($grid, newSize) {
            $grid.triggerHandler('pageSizeChange', [newSize]);
        },

        /**
         * Triggered before the change of the page.
         *
         * @event pageChanging
         * @param {object} e - event data
         * @param {number} newPage - The new page
         * @example sample <!-- materialicons, grid, grid.pagination -->
         * <table id="grid"></table>
         * <script>
         *     var grid = $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         columns: [ { field: 'ID', width: 56 }, { field: 'Name', sortable: true }, { field: 'PlaceOfBirth' } ],
         *         pager: { limit: 2, sizes: [2, 5, 10, 20] }
         *     });
         *     grid.on('pageChanging', function (e, newPage) {
         *         alert('The new page is ' + newPage + '.');
         *     });
         * </script>
         */
        pageChanging: function ($grid, newSize) {
            $grid.triggerHandler('pageChanging', [newSize]);
        }
    },

    configure: function ($grid, fullConfig, clientConfig) {
        $.extend(true, $grid, gj.grid.plugins.pagination.public);
        var data = $grid.data();
        if (clientConfig.pager) {
            gj.grid.methods.isLastRecordVisible = gj.grid.plugins.pagination.private.isLastRecordVisible;

            $grid.on('initialized', function () {
                gj.grid.plugins.pagination.private.init($grid);
            });
            $grid.on('dataBound', function (e, records, totalRecords) {
                gj.grid.plugins.pagination.private.reloadPager($grid, totalRecords);
            });
            $grid.on('columnShow', function () {
                gj.grid.plugins.pagination.private.updatePagerColSpan($grid);
            });
            $grid.on('columnHide', function () {
                gj.grid.plugins.pagination.private.updatePagerColSpan($grid);
            });
        }
    }
};

/** 
 * @widget Grid 
 * @plugin Responsive Design
 */
gj.grid.plugins.responsiveDesign = {
    config: {
        base: {
            /** The interval in milliseconds for checking if the grid is resizing.
             * This setting is in use only if the resizeMonitoring setting is set to true.
             * @type number
             * @default 500
             * @example sample <!-- materialicons, grid, grid.responsiveDesign -->
             * <p>Change browser window size in order to fire resize event.</p>
             * <table id="grid"></table>
             * <script>
             *     var grid = $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         responsive: true,
             *         resizeCheckInterval: 2000, //check if the grid is resized on each 2 second
             *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
             *     });
             *     grid.on('resize', function () {
             *         alert('resize is fired.');
             *     });
             * </script>
             */
            resizeCheckInterval: 500,

            /** This setting enables responsive behaviour of the grid where some column are invisible when there is not enough space on the screen for them.
             * The visibility of the columns in this mode is driven by the column minWidth and priority settings.
             * The columns without priority setting are always visible and can't hide in small screen resolutions.
             * @type boolean
             * @default false
             * @example sample <!-- grid, grid.responsiveDesign -->
             * <p>Resize browser window in order to see his responsive behaviour.</p>
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         responsive: true,
             *         columns: [
             *             { field: 'Name' },
             *             { field: 'PlaceOfBirth', minWidth: 340, priority: 1 },
             *             { field: 'DateOfBirth', minWidth: 360, priority: 2, type: 'date' }
             *         ]
             *     });
             * </script>
             */
            responsive: false,

            /** Automatically adds hidden columns to the details section of the row.
             * This setting works only if the responsive setting is set to true and the detailTemplate is set.
             * You need to set priority and minWidth on the colums, that needs to be hidden in smaller screens.
             * @type boolean
             * @default false
             * @example Remote.Data.Source <!-- bootstrap, grid, grid.expandCollapseRows, grid.responsiveDesign -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         detailTemplate: '<div class="row"></div>',
             *         responsive: true,
             *         showHiddenColumnsAsDetails: true,
             *         uiLibrary: 'bootstrap',
             *         columns: [
             *             { field: 'ID', width: 34 },
             *             { field: 'Name', minWidth: 320, priority: 1 },
             *             { field: 'PlaceOfBirth', minWidth: 320, priority: 2 }
             *         ]
             *     });
             * </script>
             * @example Local.Data.Source <!-- bootstrap, grid, grid.expandCollapseRows, grid.responsiveDesign -->
             * <table id="grid"></table>
             * <script>             
             *     var data = [
             *         { 'ID': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
             *         { 'ID': 2, 'Name': 'Ronaldo Luis Nazario de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
             *         { 'ID': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' }
             *     ];
             *     $('#grid').grid({
             *         dataSource: data,
             *         detailTemplate: '<div class="row"></div>',
             *         responsive: true,
             *         showHiddenColumnsAsDetails: true,
             *         uiLibrary: 'bootstrap',
             *         columns: [
             *             { field: 'ID', width: 34 },
             *             { field: 'Name', minWidth: 320, priority: 1 },
             *             { field: 'PlaceOfBirth', minWidth: 320, priority: 2 }
             *         ],
             *         pager: { limit: 2 }
             *     });
             * </script>
             */
            showHiddenColumnsAsDetails: false,

            defaultColumn: {
                /** The priority of the column compared to other columns in the grid.
                 * The columns are hiding based on the priorities.
                 * This setting is working only when the responsive setting is set to true.
                 * @alias column.priority
                 * @type number
                 * @default undefined
                 * @example sample <!-- grid, grid.responsiveDesign -->
                 * <table id="grid"></table>
                 * <script>
                 *     $('#grid').grid({
                 *         dataSource: '/Players/Get',
                 *         responsive: true,
                 *         columns: [
                 *             { field: 'Name' },
                 *             { field: 'PlaceOfBirth', priority: 1 },
                 *             { field: 'DateOfBirth', priority: 2, type: 'date' }
                 *         ]
                 *     });
                 * </script>
                 */
                priority: undefined,

                /** The minimum width of the column.
                 * The column is getting invisible when there is not enough space in the grid for this minimum width.
                 * This setting is working only when the responsive setting is set to true and the column priority setting is set.
                 * @alias column.minWidth
                 * @type number
                 * @default 250
                 * @example sample <!-- grid, grid.responsiveDesign -->
                 * <table id="grid"></table>
                 * <script>
                 *     $('#grid').grid({
                 *         dataSource: '/Players/Get',
                 *         responsive: true,
                 *         columns: [
                 *             { field: 'Name' },
                 *             { field: 'PlaceOfBirth', minWidth: 240, priority: 1 },
                 *             { field: 'DateOfBirth', minWidth: 260, priority: 2, type: 'date' }
                 *         ]
                 *     });
                 * </script>
                 */
                minWidth: 250
            },
            style: {
                rowDetailItem: ''
            }
        },

        bootstrap: {
            style: {
                rowDetailItem: 'col-lg-4'
            }
        }
    },

    'private': {

        orderColumns: function (config) {
            var result = [];
            if (config.columns && config.columns.length) {
                for (i = 0; i < config.columns.length; i++) {
                    result.push({
                        position: i,
                        field: config.columns[i].field,
                        minWidth: config.columns[i].width || config.columns[i].minWidth || config.defaultColumn.minWidth,
                        priority: config.columns[i].priority || 0
                    });
                }
                result.sort(function (a, b) {
                    var result = 0;
                    if (a.priority < b.priority) {
                        result = -1;
                    } else if (a.priority > b.priority) {
                        result = 1;
                    }
                    return result;
                });
            }
            return result;
        },
        
        updateDetails: function ($grid) {      
            var rows, data, i, j, $row, details, $placeholder, column, tmp;
            rows = $grid.find('tbody > tr[data-role="row"]');
            data = $grid.data();
            for (i = 0; i < rows.length; i++) {
                $row = $(rows[i]);
                details = $row.data('details');
                for (j = 0; j < data.columns.length; j++) {
                    column = data.columns[j];
                    $placeholder = details && details.find('div[data-id="' + column.field + '"]');
                    if (data.columns[j].hidden) {
                        tmp = '<b>' + (column.title || column.field) + '</b>: {' + column.field + '}';
                        if (!$placeholder || !$placeholder.length) {
                            $placeholder = $('<div data-id="' + column.field + '"/>').html(tmp);
                            $placeholder.addClass(data.style.rowDetailItem);
                            if (!details || !details.length) {
                                details = $('<div class="row"/>');
                            }
                            details.append($placeholder);
                        } else {
                            $placeholder.empty().html(tmp);
                        }
                    } else if ($placeholder && $placeholder.length) {
                        $placeholder.remove();
                    }
                }
                $grid.updateDetails($row);
            }
        }
    },

    'public': {

        oldWidth: undefined,

        resizeCheckIntervalId: undefined,

        /**
         * Make the grid responsive based on the available space.
         * Show column if the space for the grid is expanding and hide columns when the space for the grid is decreasing.
         * @method
         * @return void
         * @example sample <!-- grid, grid.responsiveDesign -->
         * <button onclick="grid.makeResponsive()" class="gj-button-md">Make Responsive</button>
         * <br/><br/>
         * <table id="grid"></table>
         * <script>
         *     var grid = $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         responsive: false,
         *         columns: [
         *             { field: 'ID', width: 56 },
         *             { field: 'Name', minWidth: 320, priority: 1 },
         *             { field: 'PlaceOfBirth', minWidth: 320, priority: 2 }
         *         ]
         *     });
         * </script>
         */
        makeResponsive: function () {
            var i, $column,
                extraWidth = 0,
                config = this.data(),
                columns = gj.grid.plugins.responsiveDesign.private.orderColumns(config);
            //calculate extra width
            for (i = 0; i < columns.length; i++) {
                $column = this.find('thead>tr>th:eq(' + columns[i].position + ')');
                if ($column.is(':visible') && columns[i].minWidth < $column.width()) {
                    extraWidth += $column.width() - columns[i].minWidth;
                }
            }
            //show columns
            if (extraWidth) {
                for (i = 0; i < columns.length; i++) {
                    $column = this.find('thead>tr>th:eq(' + columns[i].position + ')');
                    if (!$column.is(':visible') && columns[i].minWidth <= extraWidth) {
                        this.showColumn(columns[i].field);
                        extraWidth -= $column.width();
                    }
                }
            }
            //hide columns
            for (i = (columns.length - 1); i >= 0; i--) {
                $column = this.find('thead>tr>th:eq(' + columns[i].position + ')');
                if ($column.is(':visible') && columns[i].priority && columns[i].minWidth > $column.outerWidth()) {
                    this.hideColumn(columns[i].field);
                }
            }
        },
    },

    'events': {
        /**
         * Event fires when the grid width is changed. The "responsive" configuration setting should be set to true in order this event to fire.
         *
         * @event resize
         * @param {object} e - event data
         * @param {number} newWidth - The new width
         * @param {number} oldWidth - The old width
         * @example sample <!-- grid, grid.responsiveDesign -->
         * <table id="grid"></table>
         * <script>
         *     var grid = $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         responsive: true,
         *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
         *     });
         *     grid.on('resize', function (e, newWidth, oldWidth) {
         *         alert('resize is fired.');
         *     });
         * </script>
         */
        resize: function ($grid, newWidth, oldWidth) {
            $grid.triggerHandler('resize', [newWidth, oldWidth]);
        }
    },

    'configure': function ($grid, fullConfig, clientConfig) {
        $.extend(true, $grid, gj.grid.plugins.responsiveDesign.public);
        if (fullConfig.responsive) {
            $grid.on('initialized', function () {
                $grid.makeResponsive();
                $grid.oldWidth = $grid.width();
                $grid.resizeCheckIntervalId = setInterval(function () {
                    var newWidth = $grid.width();
                    if (newWidth !== $grid.oldWidth) {
                        gj.grid.plugins.responsiveDesign.events.resize($grid, newWidth, $grid.oldWidth);
                    }
                    $grid.oldWidth = newWidth;
                }, fullConfig.resizeCheckInterval);
            });
            $grid.on('destroy', function () {
                if ($grid.resizeCheckIntervalId) {
                    clearInterval($grid.resizeCheckIntervalId);
                }
            });
            $grid.on('resize', function () {
                $grid.makeResponsive();
            });
        }
        if (fullConfig.showHiddenColumnsAsDetails && gj.grid.plugins.expandCollapseRows) {
            $grid.on('dataBound', function () {
                gj.grid.plugins.responsiveDesign.private.updateDetails($grid);
            });
            $grid.on('columnHide', function () {
                gj.grid.plugins.responsiveDesign.private.updateDetails($grid);
            });
            $grid.on('columnShow', function () {
                gj.grid.plugins.responsiveDesign.private.updateDetails($grid);
            });
            $grid.on('rowDataBound', function () {
                gj.grid.plugins.responsiveDesign.private.updateDetails($grid);
            });
        }
    }
};

/** 
 * @widget Grid 
 * @plugin Toolbar
 */
gj.grid.plugins.toolbar = {
    config: {
        base: {
            /** Template for the content in the toolbar. Appears in a separate row on top of the grid.
              * @type string
              * @default undefined
              * @example sample <!-- bootstrap, grid, grid.toolbar, grid.pagination -->
              * <table id="grid"></table>
              * <script>
              *     var grid = $('#grid').grid({
              *         dataSource: '/Players/Get',
              *         uiLibrary: 'bootstrap',
              *         toolbarTemplate: '<div class="row"><div class="col-md-8" style="line-height:34px"><span data-role="title">Grid Title</span></div><div class="col-md-4 text-right"><button onclick="grid.reload()" class="btn btn-default">click here to refresh</button></div></div>',
              *         columns: [ { field: 'ID', width: 34 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
              *         pager: { limit: 5 }
              *     });
              * </script>
              */
            toolbarTemplate: undefined,

            /** The title of the grid. Appears in a separate row on top of the grid.
              * @type string
              * @default undefined
              * @example Material.Design <!-- materialicons, grid, grid.toolbar -->
              * <table id="grid"></table>
              * <script>
              *     $('#grid').grid({
              *         dataSource: '/Players/Get',
              *         title: 'Players',
              *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
              *     });
              * </script>
              * @example Bootstrap.3 <!-- bootstrap, grid, grid.toolbar -->
              * <table id="grid"></table>
              * <script>
              *     $('#grid').grid({
              *         dataSource: '/Players/Get',
              *         uiLibrary: 'bootstrap',
              *         title: 'Players',
              *         columns: [ { field: 'ID', width: 34 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
              *     });
              * </script>
              * @example Bootstrap.4 <!-- bootstrap4, grid, grid.toolbar -->
              * <table id="grid"></table>
              * <script>
              *     $('#grid').grid({
              *         dataSource: '/Players/Get',
              *         uiLibrary: 'bootstrap4',
              *         title: 'Players',
              *         columns: [ { field: 'ID', width: 34 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
              *     });
              * </script>
              */
            title: undefined,

            style: {
                toolbar: 'gj-grid-md-toolbar'
            }
        },

        bootstrap: {
            style: {
                toolbar: 'gj-grid-bootstrap-toolbar'
            }
        },

        bootstrap4: {
            style: {
                toolbar: 'gj-grid-bootstrap-4-toolbar'
            }
        }
    },

    private: {
        init: function ($grid) {
            var data, $toolbar, $title;
            data = $grid.data();
            $toolbar = $grid.prev('div[data-role="toolbar"]');
            if (typeof (data.toolbarTemplate) !== 'undefined' || typeof (data.title) !== 'undefined' || $toolbar.length > 0) {
                if ($toolbar.length === 0) {
                    $toolbar = $('<div data-role="toolbar"></div>');
                    $grid.before($toolbar);
                }
                $toolbar.addClass(data.style.toolbar);

                if ($toolbar.children().length === 0 && data.toolbarTemplate) {
                    $toolbar.append(data.toolbarTemplate);
                }

                $title = $toolbar.find('[data-role="title"]');
                if ($title.length === 0) {
                    $title = $('<div data-role="title"/>');
                    $toolbar.prepend($title);
                }
                if (data.title) {
                    $title.text(data.title);
                }

                if (data.minWidth) {
                    $toolbar.css('min-width', data.minWidth);
                }
            }
        }
    },

    public: {        
        /**
         * Get or set grid title.
         * @additionalinfo When you pass value in the text parameter this value with be in use for the new title of the grid and the method will return grid object.<br/>
         * When you don't pass value in the text parameter, then the method will return the text of the current grid title.<br/>
         * You can use this method in a combination with toolbarTemplate only if the title is wrapped in element with data-role attribute that equals to "title".<br/>
         * @method
         * @param {object} text - The text of the new grid title.
         * @return string or grid object
         * @example text <!-- materialicons, grid, grid.toolbar -->
         * <button onclick="grid.title('New Title')" class="gj-button-md">Set New Title</button>
         * <button onclick="alert(grid.title())" class="gj-button-md">Get Title</button>
         * <br/><br/>
         * <table id="grid"></table>
         * <script>
         *     var grid = $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         title: 'Initial Grid Title',
         *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
         *     });
         * </script>
         * @example html.template <!-- materialicons, grid, grid.toolbar -->
         * <button onclick="grid.title('New Title')" class="gj-button-md">Set New Title</button>
         * <button onclick="alert(grid.title())" class="gj-button-md">Get Title</button>
         * <br/><br/>
         * <table id="grid"></table>
         * <script>
         *     var grid = $('#grid').grid({
         *         dataSource: '/Players/Get',
         *         toolbarTemplate: '<div data-role="title">Initial Grid Title</div>',
         *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
         *     });
         * </script>
         */
        title: function (text) {
            var $titleEl = this.parent().find('div[data-role="toolbar"] [data-role="title"]');
            if (typeof (text) !== 'undefined') {
                $titleEl.text(text);
                return this;
            } else {
                return $titleEl.text();
            }
        }
    },

    configure: function ($grid) {
        $.extend(true, $grid, gj.grid.plugins.toolbar.public);
        $grid.on('initialized', function () {
            gj.grid.plugins.toolbar.private.init($grid);
        });
    }
};

/** 
 * @widget Grid 
 * @plugin Resizable Columns
 */
gj.grid.plugins.resizableColumns = {
    config: {
        base: {
            /** If set to true, users can resize columns by dragging the edges (resize handles) of their header cells.
             * @type boolean
             * @default false
             * @example Material.Design <!-- materialicons, grid, draggable.base -->
             * <table id="grid"></table>
             * <script>
             *     var grid = $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         resizableColumns: true,
             *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
             *     });
             * </script>
             * @example Bootstrap <!-- bootstrap, grid, draggable.base -->
             * <table id="grid"></table>
             * <script>
             *     var grid = $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         resizableColumns: true,
             *         uiLibrary: 'bootstrap',
             *         columns: [ { field: 'ID', width: 34 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
             *     });
             * </script>
             */
            resizableColumns: false
        }
    },

    private: {
        init: function ($grid, config) {
            var $columns, $column, i, $wrapper, $resizer, marginRight;
            $columns = $grid.find('thead tr[data-role="caption"] th');
            if ($columns.length) {
                for (i = 0; i < $columns.length - 1; i++) {
                    $column = $($columns[i]);
                    $wrapper = $('<div class="gj-grid-column-resizer-wrapper" />');
                    marginRight = parseInt($column.css('padding-right'), 10) + 3;
                    $resizer = $('<span class="gj-grid-column-resizer" />').css('margin-right', '-' + marginRight + 'px');
                    if ($.fn.draggable) {
                        $resizer.draggable({
                            start: function () {
                                $grid.addClass('gj-unselectable');
                                $grid.addClass('gj-grid-resize-cursor');
                            },
                            stop: function () {
                                $grid.removeClass('gj-unselectable');
                                $grid.removeClass('gj-grid-resize-cursor');
                                this.style.removeProperty('top');
                                this.style.removeProperty('left');
                                this.style.removeProperty('position');
                            },
                            drag: gj.grid.plugins.resizableColumns.private.createResizeHandle($grid, $column, config.columns[i])
                        });
                    }
                    $column.append($wrapper.append($resizer));
                }
            }
        },

        createResizeHandle: function ($grid, $column, column) {
            return function (e, offset) {
                var newWidth, currentWidth = parseInt($column.attr('width'), 10);
                if (!currentWidth) {
                    currentWidth = $column.outerWidth();
                }
                if (offset && offset.left) {
                    newWidth = currentWidth + offset.left;
                    column.width = newWidth;
                    $column.attr('width', newWidth);
                }
            };
        }
    },

    public: {
    },

    configure: function ($grid, fullConfig, clientConfig) {
        $.extend(true, $grid, gj.grid.plugins.resizableColumns.public);
        if (fullConfig.resizableColumns) {
            $grid.on('initialized', function () {
                gj.grid.plugins.resizableColumns.private.init($grid, fullConfig);
            });
        }
    }
};

/** 
 * @widget Grid 
 * @plugin Row Reorder
 */
gj.grid.plugins.rowReorder = {
    config: {
        base: {
            /** If set to true, enable row reordering with drag and drop.
             * @type boolean
             * @default false
             * @example Material.Design <!-- materialicons, grid, grid.rowReorder, draggable.base, droppable.base -->
             * <p>Drag and Drop rows in order to reorder them.</p>
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         rowReorder: true,
             *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
             *     });
             * </script>
             * @example Bootstrap.3 <!-- bootstrap, grid, grid.rowReorder, draggable.base, droppable.base -->
             * <p>Drag and Drop rows in order to reorder them.</p>
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         rowReorder: true,
             *         uiLibrary: 'bootstrap',
             *         columns: [ { field: 'ID', width: 36 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
             *     });
             * </script>
             * @example Bootstrap.4 <!-- bootstrap4, grid, grid.rowReorder, draggable.base, droppable.base -->
             * <p>Drag and Drop rows in order to reorder them.</p>
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         rowReorder: true,
             *         uiLibrary: 'bootstrap4',
             *         columns: [ { field: 'ID', width: 36 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
             *     });
             * </script>
             */
            rowReorder: false,

            /** If set, enable row reordering only when you try to drag cell from the configured column.
             * Accept only field names of columns.
             * @type string
             * @default undefined
             * @example sample <!-- materialicons, grid, grid.rowReorder, draggable.base, droppable.base -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         rowReorder: true,
             *         rowReorderColumn: 'ID',
             *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
             *     });
             * </script>
             */
            rowReorderColumn: undefined,

            /** If set, update the value in the field for all records. Accept only field names of columns.
             * @type string
             * @default undefined
             * @example Visible.OrderNumber <!-- grid, grid.rowReorder, draggable.base, droppable.base -->
             * <table id="grid"></table>
             * <script>
             *     var data = [
             *         { 'ID': 1, 'OrderNumber': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
             *         { 'ID': 2, 'OrderNumber': 2, 'Name': 'Ronaldo Luis Nazario de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
             *         { 'ID': 3, 'OrderNumber': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' }
             *     ];
             *     $('#grid').grid({
             *         dataSource: data,
             *         rowReorder: true,
             *         orderNumberField: 'OrderNumber',
             *         columns: [ { field: 'ID', width: 56 }, { field: 'OrderNumber', width:120 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
             *     });
             * </script>
             * @example Hidden.OrderNumber <!-- grid, grid.rowReorder, draggable.base, droppable.base -->
             * <button onclick="alert(JSON.stringify(grid.getAll()))">Show Data</button>
             * <table id="grid"></table>
             * <script>
             *     var data = [
             *         { 'ID': 1, 'OrderNumber': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
             *         { 'ID': 2, 'OrderNumber': 2, 'Name': 'Ronaldo Luis Nazario de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
             *         { 'ID': 3, 'OrderNumber': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' }
             *     ],
             *     grid = $('#grid').grid({
             *         dataSource: data,
             *         rowReorder: true,
             *         orderNumberField: 'OrderNumber',
             *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
             *     });
             * </script>
             */
            orderNumberField: undefined,

            style: {
                targetRowIndicatorTop: 'gj-grid-row-reorder-indicator-top',
                targetRowIndicatorBottom: 'gj-grid-row-reorder-indicator-bottom'
            }
        }
    },

    private: {
        init: function ($grid) {
            var i, columnPosition, $row,
                $rows = $grid.find('tbody tr[data-role="row"]');
            if ($grid.data('rowReorderColumn')) {
                columnPosition = gj.grid.methods.getColumnPosition($grid.data('columns'), $grid.data('rowReorderColumn'));
            }
            for (i = 0; i < $rows.length; i++) {
                $row = $($rows[i]);
                if (typeof (columnPosition) !== 'undefined') {
                    $row.find('td:eq(' + columnPosition + ')').on('mousedown', gj.grid.plugins.rowReorder.private.createRowMouseDownHandler($grid, $row));
                } else {
                    $row.on('mousedown', gj.grid.plugins.rowReorder.private.createRowMouseDownHandler($grid, $row));
                }
            }
        },

        createRowMouseDownHandler: function ($grid, $trSource) {
            return function (e) {
                var $dragEl = $grid.clone(), columns = $grid.data('columns'), i, $cells;
                $('body').append($dragEl);
                $dragEl.attr('data-role', 'draggable-clone').css('cursor', 'move');
                $dragEl.children('thead').remove().children('tfoot').remove();
                $dragEl.find('tbody tr:not([data-position="' + $trSource.data('position') + '"])').remove();
                $cells = $dragEl.find('tbody tr td');
                for (i = 0; i < $cells.length; i++) {
                    if (columns[i].width) {
                        $cells[i].setAttribute('width', columns[i].width);
                    }
                }
                $dragEl.draggable({
                    stop: gj.grid.plugins.rowReorder.private.createDragStopHandler($grid, $trSource)
                });
                $dragEl.css({ 
                    position: 'absolute', top: $trSource.offset().top, left: $trSource.offset().left, width: $trSource.width()
                });
                if ($trSource.attr('data-droppable') === 'true') {
                    $trSource.droppable('destroy');
                }
                $trSource.siblings('tr[data-role="row"]').each(function () {
                    var $dropEl = $(this);
                    if ($dropEl.attr('data-droppable') === 'true') {
                        $dropEl.droppable('destroy');
                    }
                    $dropEl.droppable({
                        over: gj.grid.plugins.rowReorder.private.createDroppableOverHandler($trSource),
                        out: gj.grid.plugins.rowReorder.private.droppableOut
                    });
                });
                $dragEl.trigger('mousedown');
            };
        },

        createDragStopHandler: function ($grid, $trSource) {
            return function (e, mousePosition) {
                $('table[data-role="draggable-clone"]').draggable('destroy').remove();
                $trSource.siblings('tr[data-role="row"]').each(function () {
                    var $trTarget = $(this),
                        targetPosition = $trTarget.data('position'),
                        sourcePosition = $trSource.data('position'),
                        data = $grid.data(),
                        $rows, $row, i, record, id;
                        
                    if ($trTarget.droppable('isOver', mousePosition)) {
                        if (targetPosition < sourcePosition) {
                            $trTarget.before($trSource);
                        } else {
                            $trTarget.after($trSource);
                        }
                        data.records.splice(targetPosition - 1, 0, data.records.splice(sourcePosition - 1, 1)[0]);
                        $rows = $trTarget.parent().find('tr[data-role="row"]');
                        for (i = 0; i < $rows.length; i++) {
                            $($rows[i]).attr('data-position', i + 1);
                        }
                        if (data.orderNumberField) {
                            for (i = 0; i < data.records.length; i++) {
                                data.records[i][data.orderNumberField] = i + 1;
                            }
                            for (i = 0; i < $rows.length; i++) {
                                $row = $($rows[i]);
                                id = gj.grid.methods.getId($row, data.primaryKey, $row.attr('data-position'));
                                record = gj.grid.methods.getByPosition($grid, $row.attr('data-position'));
                                $grid.setCellContent(id, data.orderNumberField, record[data.orderNumberField]);
                            }
                        }
                    }
                    $trTarget.removeClass('gj-grid-base-top-border');
                    $trTarget.removeClass('gj-grid-base-bottom-border');
                    $trTarget.droppable('destroy');
                });
            }
        },

        createDroppableOverHandler: function ($trSource) {
            return function (e) {
                var $trTarget = $(this),
                    targetPosition = $trTarget.data('position'),
                    sourcePosition = $trSource.data('position');
                if (targetPosition < sourcePosition) {
                    $trTarget.addClass('gj-grid-base-top-border');
                } else {
                    $trTarget.addClass('gj-grid-base-bottom-border');
                }
            };
        },

        droppableOut: function () {
            $(this).removeClass('gj-grid-base-top-border');
            $(this).removeClass('gj-grid-base-bottom-border');
        }
    },

    public: {
    },

    configure: function ($grid, fullConfig, clientConfig) {
        $.extend(true, $grid, gj.grid.plugins.rowReorder.public);
        if (fullConfig.rowReorder && $.fn.draggable && $.fn.droppable) {
            $grid.on('dataBound', function () {
                gj.grid.plugins.rowReorder.private.init($grid);
            });
        }
    }
};

/** 
 * @widget Grid 
 * @plugin Column Reorder
 */
gj.grid.plugins.columnReorder = {
    config: {
        base: {
            /** If set to true, enable column reordering with drag and drop.
             * @type boolean
             * @default false
             * @example Material.Design <!-- materialicons, grid, draggable.base, droppable.base -->
             * <p>Drag and Drop column headers in order to reorder the columns.</p>
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         columnReorder: true,
             *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
             *     });
             * </script>
             * @example Bootstrap <!-- bootstrap, grid, draggable.base, droppable.base -->
             * <p>Drag and Drop column headers in order to reorder the columns.</p>
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         uiLibrary: 'bootstrap',
             *         columnReorder: true,
             *         columns: [ { field: 'ID', width: 36 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
             *     });
             * </script>
             */
            columnReorder: false,

            style: {
                targetRowIndicatorTop: 'gj-grid-row-reorder-indicator-top',
                targetRowIndicatorBottom: 'gj-grid-row-reorder-indicator-bottom'
            }
        }
    },

    private: {
        init: function ($grid) {
            var i, $cell,
                $cells = $grid.find('thead tr th');
            for (i = 0; i < $cells.length; i++) {
                $cell = $($cells[i]);
                $cell.on('mousedown', gj.grid.plugins.columnReorder.private.createMouseDownHandler($grid, $cell));
            }
        },

        createMouseDownHandler: function ($grid, $thSource) {
            return function (e) {
                var $dragEl = $grid.clone(),
                    srcIndex = $thSource.index();
                $('body').append($dragEl);
                $dragEl.attr('data-role', 'draggable-clone').css('cursor', 'move');
                $dragEl.find('thead tr th:eq(' + srcIndex + ')').siblings().remove();
                $dragEl.find('tbody tr[data-role != "row"]').remove();
                $dragEl.find('tbody tr td:nth-child(' + (srcIndex + 1) + ')').siblings().remove();
                $dragEl.find('tfoot').remove();
                $dragEl.draggable({
                    stop: gj.grid.plugins.columnReorder.private.createDragStopHandler($grid, $thSource)
                });
                $dragEl.css({
                    position: 'absolute', top: $thSource.offset().top, left: $thSource.offset().left, width: $thSource.width()
                });
                if ($thSource.attr('data-droppable') === 'true') {
                    $thSource.droppable('destroy');
                }
                $thSource.siblings('th').each(function () {
                    var $dropEl = $(this);
                    if ($dropEl.attr('data-droppable') === 'true') {
                        $dropEl.droppable('destroy');
                    }
                    $dropEl.droppable({
                        over: gj.grid.plugins.columnReorder.private.createDroppableOverHandler($grid, $thSource),
                        out: gj.grid.plugins.columnReorder.private.droppableOut
                    });
                });
                $dragEl.trigger('mousedown');
            };
        },

        createDragStopHandler: function ($grid, $thSource) {
            return function (e, mousePosition) {
                $('table[data-role="draggable-clone"]').draggable('destroy').remove();
                $thSource.siblings('th').each(function () {
                    var $thTarget = $(this),
                        data = $grid.data(),
                        targetPosition = gj.grid.methods.getColumnPosition(data.columns, $thTarget.data('field')),
                        sourcePosition = gj.grid.methods.getColumnPosition(data.columns, $thSource.data('field'));

                    $thTarget.removeClass('gj-grid-left-border').removeClass('gj-grid-right-border');
                    $thTarget.closest('table').find('tbody tr[data-role="row"] td:nth-child(' + ($thTarget.index() + 1) + ')').removeClass('gj-grid-left-border').removeClass('gj-grid-right-border');
                    if ($thTarget.droppable('isOver', mousePosition)) {
                        if (targetPosition < sourcePosition) {
                            $thTarget.before($thSource);
                        } else {
                            $thTarget.after($thSource);
                        }
                        gj.grid.plugins.columnReorder.private.moveRowCells($grid, sourcePosition, targetPosition);
                        data.columns.splice(targetPosition, 0, data.columns.splice(sourcePosition, 1)[0]);
                    }
                    $thTarget.droppable('destroy');
                });
            }
        },

        moveRowCells: function ($grid, sourcePosition, targetPosition) {
            var i, $row, $rows = $grid.find('tbody tr[data-role="row"]');
            for (i = 0; i < $rows.length; i++) {
                $row = $($rows[i]);
                if (targetPosition < sourcePosition) {
                    $row.find('td:eq(' + targetPosition + ')').before($row.find('td:eq(' + sourcePosition + ')'));
                } else {
                    $row.find('td:eq(' + targetPosition + ')').after($row.find('td:eq(' + sourcePosition + ')'));
                }                
            }
        },

        createDroppableOverHandler: function ($grid, $thSource) {
            return function (e) {
                var $thTarget = $(this),
                    data = $grid.data(),
                    targetPosition = gj.grid.methods.getColumnPosition(data.columns, $thTarget.data('field')),
                    sourcePosition = gj.grid.methods.getColumnPosition(data.columns, $thSource.data('field'));
                if (targetPosition < sourcePosition) {
                    $thTarget.addClass('gj-grid-left-border');
                    $grid.find('tbody tr[data-role="row"] td:nth-child(' + ($thTarget.index() + 1) + ')').addClass('gj-grid-left-border');
                } else {
                    $thTarget.addClass('gj-grid-right-border');
                    $grid.find('tbody tr[data-role="row"] td:nth-child(' + ($thTarget.index() + 1) + ')').addClass('gj-grid-right-border');
                }
            };
        },

        droppableOut: function () {
            var $thTarget = $(this);
            $thTarget.removeClass('gj-grid-left-border').removeClass('gj-grid-right-border');
            $thTarget.closest('table').find('tbody tr[data-role="row"] td:nth-child(' + ($thTarget.index() + 1) + ')').removeClass('gj-grid-left-border').removeClass('gj-grid-right-border');
        }
    },

    public: {
    },

    configure: function ($grid, fullConfig, clientConfig) {
        $.extend(true, $grid, gj.grid.plugins.columnReorder.public);
        if (fullConfig.columnReorder) {
            $grid.on('initialized', function () {
                gj.grid.plugins.columnReorder.private.init($grid);
            });
        }
    }
};

/**
 * @widget Grid
 * @plugin Header Filter
 */
gj.grid.plugins.headerFilter = {
    config: {
        base: {
            defaultColumnSettings: {
                /** Indicates if the column is sortable. If set to false the header filter is hidden.
                 * @alias column.filterable
                 * @type boolean
                 * @default true
                 * @example Material.Design <!-- materialicons, grid -->
                 * <table id="grid"></table>
                 * <script>
                 *     $('#grid').grid({
                 *         dataSource: '/Players/Get',
                 *         headerFilter: true,
                 *         columns: [
                 *             { field: 'ID', width: 56, filterable: false },
                 *             { field: 'Name', filterable: true },
                 *             { field: 'PlaceOfBirth' }
                 *         ]
                 *     });
                 * </script>
                 * @example Bootstrap.3 <!-- bootstrap, grid -->
                 * <table id="grid"></table>
                 * <script>
                 *     $('#grid').grid({
                 *         dataSource: '/Players/Get',
                 *         headerFilter: true,
                 *         uiLibrary: 'bootstrap',
                 *         columns: [
                 *             { field: 'ID', width: 56, filterable: false },
                 *             { field: 'Name', filterable: true },
                 *             { field: 'PlaceOfBirth' }
                 *         ]
                 *     });
                 * </script>
                 */
                filterable: true
            },

            /** If set to true, add filters for each column
             * @type boolean
             * @default object
             * @example Remote.DataSource <!-- materialicons, grid -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         headerFilter: true,
             *         columns: [ { field: 'ID', width: 56, filterable: false }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
             *     });
             * </script>
             * @example Local.DataSource <!-- materialicons, grid -->
             * <table id="grid"></table>
             * <script>
             *     var data = [
             *         { 'ID': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria' },
             *         { 'ID': 2, 'Name': 'Ronaldo Luís Nazário de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil' },
             *         { 'ID': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England' },
             *         { 'ID': 4, 'Name': 'Manuel Neuer', 'PlaceOfBirth': 'Gelsenkirchen, West Germany' },
             *         { 'ID': 5, 'Name': 'James Rodríguez', 'PlaceOfBirth': 'Cúcuta, Colombia' },
             *         { 'ID': 6, 'Name': 'Dimitar Berbatov', 'PlaceOfBirth': 'Blagoevgrad, Bulgaria' }
             *     ];
             *     $('#grid').grid({
             *         dataSource: data,
             *         headerFilter: true,
             *         columns: [ 
             *             { field: 'ID', width: 56, filterable: false }, 
             *             { field: 'Name' }, 
             *             { field: 'PlaceOfBirth' } 
             *         ],
             *         pager: { limit: 5 }
             *     });
             * </script>
             */
            headerFilter: {
                /** Type of the header filter
                 * @alias headerFilter.type
                 * @type (onenterkeypress|onchange)
                 * @default 'onenterkeypress'
                 * @example OnEnterKeyPress <!-- materialicons, grid -->
                 * <table id="grid"></table>
                 * <script>
                 *     $('#grid').grid({
                 *         dataSource: '/Players/Get',
                 *         headerFilter: {
                 *             type: 'onenterkeypress'
                 *         },
                 *         columns: [ { field: 'ID', width: 56, filterable: false }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
                 *     });
                 * </script>
                 * @example OnChange <!-- materialicons, grid -->
                 * <table id="grid"></table>
                 * <script>
                 *     $('#grid').grid({
                 *         dataSource: '/Players/Get',
                 *         headerFilter: {
                 *             type: 'onchange'
                 *         },
                 *         columns: [ { field: 'ID', width: 56, filterable: false }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
                 *     });
                 * </script>
                 */
                type: 'onenterkeypress'
            }
        }
    },

    private: {
        init: function ($grid) {
            var i, $th, $ctrl, data = $grid.data(),
                $filterTr = $('<tr data-role="filter"/>');

            for (i = 0; i < data.columns.length; i++) {
                $th = $('<th/>');
                if (data.columns[i].filterable) {
                    $ctrl = $('<input data-field="' + data.columns[i].field + '" class="gj-width-full" />');
                    if ('onchange' === data.headerFilter.type) {
                        $ctrl.on('input propertychange', function (e) {
                            gj.grid.plugins.headerFilter.private.reload($grid, $(this));
                        });
                    } else {
                        $ctrl.on('keypress', function (e) {
                            if (e.which == 13) {
                                gj.grid.plugins.headerFilter.private.reload($grid, $(this));
                            }
                        });
                        $ctrl.on('blur', function (e) {
                            gj.grid.plugins.headerFilter.private.reload($grid, $(this));
                        });
                    }
                    $th.append($ctrl);
                }
                if (data.columns[i].hidden) {
                    $th.hide();
                }
                $filterTr.append($th);
            }

            $grid.children('thead').append($filterTr);
        },

        reload: function ($grid, $ctrl) {
            var params = {};
            params[$ctrl.data('field')] = $ctrl.val();
            $grid.reload(params);
        }
    },

    public: {
    },

    events: {
    },

    configure: function ($grid, fullConfig, clientConfig) {
        $.extend(true, $grid, gj.grid.plugins.headerFilter.public);
        var data = $grid.data();
        if (clientConfig.headerFilter) {
            $grid.on('initialized', function () {
                gj.grid.plugins.headerFilter.private.init($grid);
            });
        }
    }
};

/** 
 * @widget Grid 
 * @plugin Grouping
 */
gj.grid.plugins.grouping = {
    config: {
        base: {
            paramNames: {
                /** The name of the parameter that is going to send the name of the column for grouping.
                 * The grouping should be enabled in order this parameter to be in use.
                 * @alias paramNames.groupBy
                 * @type string
                 * @default "groupBy"
                 */
                groupBy: 'groupBy',

                /** The name of the parameter that is going to send the direction for grouping.
                 * The grouping should be enabled in order this parameter to be in use.
                 * @alias paramNames.groupByDirection
                 * @type string
                 * @default "groupByDirection"
                 */
                groupByDirection: 'groupByDirection'
            },

            grouping: {
                /** The name of the field that needs to be in use for grouping.
                  * @type string
                  * @alias grouping.groupBy
                  * @default undefined
                  * @example Local.Data <!-- materialicons, grid -->
                  * <table id="grid"></table>
                  * <script>
                  *     var grid, data = [
                  *         { 'ID': 1, 'Name': 'Hristo Stoichkov', 'PlaceOfBirth': 'Plovdiv, Bulgaria', Nationality: 'Bulgaria' },
                  *         { 'ID': 2, 'Name': 'Ronaldo Luís Nazário de Lima', 'PlaceOfBirth': 'Rio de Janeiro, Brazil', Nationality: 'Brazil' },
                  *         { 'ID': 3, 'Name': 'David Platt', 'PlaceOfBirth': 'Chadderton, Lancashire, England', Nationality: 'England' },
                  *         { 'ID': 4, 'Name': 'Manuel Neuer', 'PlaceOfBirth': 'Gelsenkirchen, West Germany', Nationality: 'Germany' },
                  *         { 'ID': 5, 'Name': 'James Rodríguez', 'PlaceOfBirth': 'Cúcuta, Colombia', Nationality: 'Colombia' },
                  *         { 'ID': 6, 'Name': 'Dimitar Berbatov', 'PlaceOfBirth': 'Blagoevgrad, Bulgaria', Nationality: 'Bulgaria' }
                  *     ];
                  *     $('#grid').grid({
                  *         dataSource: data,
                  *         grouping: { groupBy: 'Nationality' },
                  *         columns: [ { field: 'Name', sortable: true }, { field: 'PlaceOfBirth' } ],
                  *         pager: { limit: 5 }
                  *     });
                  * </script>
                  * @example Remote.Data <!-- materialicons, grid -->
                  * <table id="grid"></table>
                  * <script>
                  *     $('#grid').grid({
                  *         dataSource: '/Players/Get',
                  *         grouping: { groupBy: 'Nationality' },
                  *         columns: [ { field: 'Name', sortable: true }, { field: 'PlaceOfBirth' } ],
                  *         pager: { limit: 5 }
                  *     });
                  * </script>
                  * @example Bootstrap.3 <!-- bootstrap, grid -->
                  * <table id="grid"></table>
                  * <script>
                  *     $('#grid').grid({
                  *         dataSource: '/Players/Get',
                  *         uiLibrary: 'bootstrap',
                  *         grouping: { groupBy: 'Nationality' },
                  *         columns: [ { field: 'Name', sortable: true }, { field: 'DateOfBirth', type: 'date' } ],
                  *         pager: { limit: 5 },
                  *         detailTemplate: '<div><b>Place Of Birth:</b> {PlaceOfBirth}</div>'
                  *     });
                  * </script>
                  * @example Bootstrap.4 <!-- bootstrap4, fontawesome, grid -->
                  * <table id="grid"></table>
                  * <script>
                  *     $('#grid').grid({
                  *         dataSource: '/Players/Get',
                  *         uiLibrary: 'bootstrap4',
                  *         iconsLibrary: 'fontawesome',
                  *         grouping: { groupBy: 'Nationality' },
                  *         columns: [ { field: 'Name', sortable: true }, { field: 'PlaceOfBirth' } ],
                  *         pager: { limit: 5 }
                  *     });
                  * </script>
                  */
                groupBy: undefined,

                direction: 'asc'
            },

            icons: {
                /** Expand row icon definition.
                 * @alias icons.expandGroup
                 * @type String
                 * @default '<i class="material-icons">add</i>'
                 * @example Right.Down.Icons <!-- materialicons, grid -->
                 * <table id="grid"></table>
                 * <script>
                 *     $('#grid').grid({
                 *         primaryKey: 'ID',
                 *         dataSource: '/Players/Get',
                 *         columns: [ { field: 'Name', sortable: true }, { field: 'PlaceOfBirth' } ],
                 *         grouping: { groupBy: 'Nationality' },
                 *         icons: {
                 *             expandGroup: '<i class="material-icons">keyboard_arrow_right</i>',
                 *             collapseGroup: '<i class="material-icons">keyboard_arrow_down</i>'
                 *         }
                 *     });
                 * </script>
                 */
                expandGroup: '<i class="material-icons">add</i>',

                /** Collapse row icon definition.
                 * @alias icons.collapseGroup
                 * @type String
                 * @default '<i class="material-icons">remove</i>'
                 * @example Right.Down.Icons <!-- materialicons, grid -->
                 * <table id="grid"></table>
                 * <script>
                 *     $('#grid').grid({
                 *         primaryKey: 'ID',
                 *         dataSource: '/Players/Get',
                 *         columns: [ { field: 'Name', sortable: true }, { field: 'PlaceOfBirth' } ],
                 *         grouping: { groupBy: 'Nationality' },
                 *         icons: {
                 *             expandGroup: '<i class="material-icons">keyboard_arrow_right</i>',
                 *             collapseGroup: '<i class="material-icons">keyboard_arrow_down</i>'
                 *         }
                 *     });
                 * </script>
                 */
                collapseGroup: '<i class="material-icons">remove</i>'
            }
        },

        fontawesome: {
            icons: {
                expandGroup: '<i class="fa fa-plus" aria-hidden="true"></i>',
                collapseGroup: '<i class="fa fa-minus" aria-hidden="true"></i>'
            }
        },

        glyphicons: {
            icons: {
                expandGroup: '<span class="glyphicon glyphicon-plus" />',
                collapseGroup: '<span class="glyphicon glyphicon-minus" />'
            }
        }
    },

    private: {
        init: function ($grid) {
            var previousValue, data = $grid.data();

            previousValue = undefined;
            $grid.on('rowDataBound', function (e, $row, id, record) {
                if (previousValue !== record[data.grouping.groupBy]) {
                    var colspan = gj.grid.methods.countVisibleColumns($grid) - 1,
                        $groupRow = $('<tr data-role="group" />'),
                        $expandCollapseCell = $('<td class="gj-text-align-center gj-unselectable gj-cursor-pointer" />');

                    $expandCollapseCell.append('<div data-role="display">' + data.icons.collapseGroup + '</div>');
                    $expandCollapseCell.on('click', gj.grid.plugins.grouping.private.createExpandCollapseHandler(data));
                    $groupRow.append($expandCollapseCell);
                    $groupRow.append('<td colspan="' + colspan + '"><div data-role="display">' + data.grouping.groupBy + ': ' + record[data.grouping.groupBy] + '</div></td>');
                    $groupRow.insertBefore($row);
                    previousValue = record[data.grouping.groupBy];
                }
                $row.show();
            });

            data.params[data.paramNames.groupBy] = data.grouping.groupBy;
            data.params[data.paramNames.groupByDirection] = data.grouping.direction;
        },

        grouping: function ($grid, records) {
            var data = $grid.data();
            records.sort(gj.grid.methods.createDefaultSorter(data.grouping.direction, data.grouping.groupBy));
        },

        createExpandCollapseHandler: function (data) {
            return function (e) {
                var $cell = $(this),
                    $display = $cell.children('div[data-role="display"]'),
                    $groupRow = $cell.closest('tr');
                if ($groupRow.next(':visible').data('role') === 'row') {
                    $groupRow.nextUntil('[data-role="group"]').hide();
                    $display.empty().append(data.icons.expandGroup);
                } else {
                    $groupRow.nextUntil('[data-role="group"]').show();
                    $display.empty().append(data.icons.collapseGroup);
                }
            };
        }
    },

    public: { },

    configure: function ($grid) {
        var column, data = $grid.data();
        $.extend(true, $grid, gj.grid.plugins.grouping.public);
        if (data.grouping && data.grouping.groupBy) {
            column = {
                title: '',
                field: '',
                width: data.defaultIconColumnWidth,
                align: 'center',
                stopPropagation: true,
                cssClass: 'gj-cursor-pointer gj-unselectable'
            };
            data.columns = [column].concat(data.columns);

            $grid.on('initialized', function () {
                gj.grid.plugins.grouping.private.init($grid);
            });

            $grid.on('dataFiltered', function (e, records) {
                gj.grid.plugins.grouping.private.grouping($grid, records);
            });
        }
    }
};

/**
 * @widget Grid
 * @plugin Fixed Header
 */
gj.grid.plugins.fixedHeader = {
    config: {
        base: {

            /** If set to true, add scroll to the table body
             * @type boolean
             * @default object
             * @example Material.Design.Without.Pager <!-- materialicons, grid -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         fixedHeader: true,
             *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ]
             *     });
             * </script>
             * @example Material.Design.With.Pager <!-- materialicons, grid -->
             * <table id="grid"></table>
             * <script>
             *     $('#grid').grid({
             *         dataSource: '/Players/Get',
             *         fixedHeader: true,
             *         columns: [ { field: 'ID', width: 56 }, { field: 'Name' }, { field: 'PlaceOfBirth' } ],
             *         pager: { limit: 5 }
             *     });
             * </script>
             * @example Bootstrap.3.Without.Pager <!-- bootstrap, grid -->
             * <div class="container"><table id="grid"></table></div>
             * <script>
             *     $('#grid').grid({
             *         uiLibrary: 'bootstrap',
             *         dataSource: '/Players/Get',
             *         fixedHeader: true,
             *         height: 200,
             *         columns: [ 
             *             { field: 'ID', width: 34 },
             *             { field: 'Name' },
             *             { field: 'PlaceOfBirth' }
             *         ]
             *     });
             * </script>
             * @example Bootstrap.3.With.Pager <!-- bootstrap, grid -->
             * <div class="container"><table id="grid"></table></div>
             * <script>
             *     $('#grid').grid({
             *         uiLibrary: 'bootstrap',
             *         dataSource: '/Players/Get',
             *         fixedHeader: true,
             *         height: 200,
             *         columns: [ 
             *             { field: 'ID', width: 34 }, 
             *             { field: 'Name' }, 
             *             { field: 'PlaceOfBirth' } 
             *         ],
             *         pager: { limit: 5 }
             *     });
             * </script>
             * @example Bootstrap.4 <!-- materialicons, bootstrap4, grid -->
             * <div class="container"><table id="grid"></table></div>
             * <script>
             *     $('#grid').grid({
             *         uiLibrary: 'bootstrap4',
             *         dataSource: '/Players/Get',
             *         fixedHeader: true,
             *         columns: [ 
             *             { field: 'ID', width: 34 }, 
             *             { field: 'Name' }, 
             *             { field: 'PlaceOfBirth' } 
             *         ],
             *         pager: { limit: 5 }
             *     });
             * </script>
             */
            fixedHeader: false,

            height: 300
        }
    },

    private: {
        init: function ($grid) {
            var data = $grid.data(),
                $tbody = $grid.children('tbody'),
                $thead = $grid.children('thead'),
                bodyHeight = data.height - $thead.outerHeight() - ($grid.children('tfoot').outerHeight() || 0);
            $grid.addClass('gj-grid-scrollable');
            $tbody.css('width', $thead.outerWidth());
            $tbody.height(bodyHeight);
        },

        refresh: function ($grid) {
            var i, $theadCell,
                data = $grid.data(),
                $tbody = $grid.children('tbody'),
                $thead = $grid.children('thead'),
                $tbodyCells = $grid.find('tbody tr[data-role="row"] td'),
                $theadCells = $grid.find('thead tr[data-role="caption"] th');

            if ($grid.children('tbody').height() < gj.grid.plugins.fixedHeader.private.getRowsHeight($grid)) {
                $tbody.css('width', $thead.outerWidth() + gj.grid.plugins.fixedHeader.private.getScrollBarWidth() + (navigator.userAgent.toLowerCase().indexOf('firefox') > -1 ? 1 : 0));
            } else {
                $tbody.css('width', $thead.outerWidth());
            }

            for (i = 0; i < $theadCells.length; i++) {
                $theadCell = $($theadCells[i]);
                $($tbodyCells[i]).attr('width', $theadCell.outerWidth());
            }
        },

        getRowsHeight: function ($grid) {
            var total = 0;
            $grid.find('tbody tr').each(function () {
                total += $(this).height();
            });
            return total;
        },

        getScrollBarWidth: function () {
            var inner = document.createElement('p');
            inner.style.width = "100%";
            inner.style.height = "200px";

            var outer = document.createElement('div');
            outer.style.position = "absolute";
            outer.style.top = "0px";
            outer.style.left = "0px";
            outer.style.visibility = "hidden";
            outer.style.width = "200px";
            outer.style.height = "150px";
            outer.style.overflow = "hidden";
            outer.appendChild(inner);

            document.body.appendChild(outer);
            var w1 = inner.offsetWidth;
            outer.style.overflow = 'scroll';
            var w2 = inner.offsetWidth;
            if (w1 == w2) w2 = outer.clientWidth;

            document.body.removeChild(outer);

            return (w1 - w2);
        }
    },

    public: {
    },

    events: {
    },

    configure: function ($grid, fullConfig, clientConfig) {
        $.extend(true, $grid, gj.grid.plugins.fixedHeader.public);
        var data = $grid.data();
        if (clientConfig.fixedHeader) {
            $grid.on('initialized', function () {
                gj.grid.plugins.fixedHeader.private.init($grid);
            });
            $grid.on('dataBound', function () {
                gj.grid.plugins.fixedHeader.private.refresh($grid);
            });
            $grid.on('resize', function () {
                gj.grid.plugins.fixedHeader.private.refresh($grid);
            });
        }
    }
};

/* global window alert jQuery gj */
/**
  * @widget Tree
  * @plugin Base
  */
if (typeof(gj.tree) === 'undefined') {
    gj.tree = {
        plugins: {}
    };
}

gj.tree.config = {
    base: {

        /** When this setting is enabled the content of the tree will be loaded automatically after the creation of the tree.
         * @type boolean
         * @default true
         * @example disabled <!-- materialicons, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         dataSource: '/Locations/Get',
         *         autoLoad: false
         *     });
         *     tree.reload(); //call .reload() explicitly in order to load the data in the tree
         * </script>
         * @example enabled <!-- materialicons, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     $('#tree').tree({
         *         dataSource: '/Locations/Get',
         *         autoLoad: true
         *     });
         * </script>
         */
        autoLoad: true,

        /** The type of the node selection.<br/>
         * If the type is set to multiple the user will be able to select more then one node in the tree.
         * @type (single|multiple)
         * @default single
         * @example Single.Selection <!-- materialicons, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         dataSource: '/Locations/Get',
         *         selectionType: 'single'
         *     });
         * </script>
         * @example Multiple.Selection <!-- materialicons, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     $('#tree').tree({
         *         dataSource: '/Locations/Get',
         *         selectionType: 'multiple'
         *     });
         * </script>
         */
        selectionType: 'single',

        /** This setting enable cascade selection and unselection of children
         * @type boolean
         * @default false
         * @example Sample <!-- materialicons, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     $('#tree').tree({
         *         dataSource: '/Locations/Get',
         *         cascadeSelection: true
         *     });
         * </script>
         */
        cascadeSelection: false,

        /** The data source of tree.
         * @additionalinfo If set to string, then the tree is going to use this string as a url for ajax requests to the server.<br />
         * If set to object, then the tree is going to use this object as settings for the <a href="http://api.jquery.com/jquery.ajax/" target="_new">jquery ajax</a> function.<br />
         * If set to array, then the tree is going to use the array as data for tree nodes.
         * @type (string|object|array)
         * @default undefined
         * @example Local.DataSource <!-- materialicons, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     $('#tree').tree({
         *         dataSource: [ { text: 'foo', children: [ { text: 'bar' } ] } ]
         *     });
         * </script>
         * @example Remote.DataSource <!-- materialicons, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     $('#tree').tree({
         *         dataSource: '/Locations/Get'
         *     });
         * </script>
         */
        dataSource: undefined,

        /** Primary key field name.
         * @type string
         * @default undefined
         * @example sample <!-- materialicons, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         primaryKey: 'id',
         *         dataSource: [ { id: 101, text: 'foo', children: [ { id: 202, text: 'bar' } ] } ]
         *     });
         *     alert(tree.getDataById(101).text);
         * </script>
         */
        primaryKey: undefined,

        /** Text field name.
         * @type string
         * @default 'text'
         * @example sample <!-- materialicons, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         textField: 'newTextName',
         *         dataSource: [ { newTextName: 'foo', children: [ { newTextName: 'bar' } ] } ]
         *     });
         * </script>
         */
        textField: 'text',

        /** Children field name.
         * @type string
         * @default 'children'
         * @example Custom.FieldName <!-- materialicons, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         childrenField: 'myChildrenNode',
         *         dataSource: [ { text: 'foo', myChildrenNode: [ { text: 'bar' } ] } ]
         *     });
         * </script>
         */
        childrenField: 'children',

        /** Image css class field name.
         * @type string
         * @default 'imageCssClass'
         * @example Default.Name <!-- bootstrap, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         uiLibrary: 'bootstrap',
         *         dataSource: [ { text: 'folder', imageCssClass: 'glyphicon glyphicon-folder-close', children: [ { text: 'file', imageCssClass: 'glyphicon glyphicon-file' } ] } ]
         *     });
         * </script>
         * @example Custom.Name <!-- materialicons, tree.base  -->
         * <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
         * <div id="tree"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         imageCssClassField: 'faCssClass',
         *         dataSource: [ { text: 'folder', faCssClass: 'fa fa-folder', children: [ { text: 'file', faCssClass: 'fa fa-file' } ] } ]
         *     });
         * </script>
         */
        imageCssClassField: 'imageCssClass',

        /** Image url field name.
         * @type string
         * @default 'imageUrl'
         * @example Default.HTML.Field.Name <!-- materialicons, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         dataSource: [ { text: 'World', imageUrl: 'http://gijgo.com/content/icons/world-icon.png', children: [ { text: 'USA', imageUrl: 'http://gijgo.com/content/icons/usa-oval-icon.png' } ] } ]
         *     });
         * </script>
         * @example Custom.HTML.Field.Name <!-- materialicons, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         imageUrlField: 'icon',
         *         dataSource: [ { text: 'World', icon: 'http://gijgo.com/content/icons/world-icon.png', children: [ { text: 'USA', icon: 'http://gijgo.com/content/icons/usa-oval-icon.png' } ] } ]
         *     });
         * </script>
         */
        imageUrlField: 'imageUrl',

        /** Image html field name.
         * @type string
         * @default 'imageHtml'
         * @example Default.HTML.Field.Name <!-- materialicons, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         dataSource: [ { text: 'folder', imageHtml: '<i class="material-icons">folder</i>', children: [ { text: 'file', imageHtml: '<i class="material-icons">insert_drive_file</i>' } ] } ]
         *     });
         * </script>
         * @example Custom.HTML.Field.Name <!-- materialicons, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         imageHtmlField: 'icon',
         *         dataSource: [ { text: 'folder', icon: '<i class="material-icons">folder</i>', children: [ { text: 'file', icon: '<i class="material-icons">insert_drive_file</i>' } ] } ]
         *     });
         * </script>
         */
        imageHtmlField: 'imageHtml',

        /** Width of the tree.
         * @type number
         * @default undefined
         * @example JS.Config <!-- bootstrap, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         dataSource: '/Locations/Get',
         *         width: 500,
         *         uiLibrary: 'bootstrap'
         *     });
         * </script>
         * @example HTML.Config <!-- bootstrap, tree.base -->
         * <div id="tree" width="500" data-source="/Locations/Get" data-ui-library="bootstrap"></div>
         * <script>
         *     $('#tree').tree();
         * </script>
         */
        width: undefined,

        /** When this setting is enabled the content of the tree will be wrapped by borders.
         * @type boolean
         * @default false
         * @example Material.Design.True <!-- materialicons, checkbox, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     $('#tree').tree({
         *         dataSource: '/Locations/Get',
         *         width: 500,
         *         border: true,
         *         checkboxes: true
         *     });
         * </script>
         * @example Material.Design.False <!-- materialicons, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     $('#tree').tree({
         *         dataSource: '/Locations/Get',
         *         width: 500,
         *         border: false
         *     });
         * </script>
         * @example Bootstrap.3.True <!-- bootstrap, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     $('#tree').tree({
         *         dataSource: '/Locations/Get',
         *         width: 500,
         *         uiLibrary: 'bootstrap',
         *         border: true
         *     });
         * </script>
         * @example Bootstrap.3.False <!-- bootstrap, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     $('#tree').tree({
         *         dataSource: '/Locations/Get',
         *         width: 500,
         *         uiLibrary: 'bootstrap',
         *         border: false
         *     });
         * </script>
         * @example Bootstrap.4.True <!-- bootstrap4, fontawesome, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     $('#tree').tree({
         *         dataSource: '/Locations/Get',
         *         width: 500,
         *         uiLibrary: 'bootstrap4',
         *         iconsLibrary: 'fontawesome',
         *         border: true
         *     });
         * </script>
         */
        border: false,

        /** The name of the UI library that is going to be in use.
         * @additionalinfo The css file for bootstrap should be manually included if you use bootstrap.
         * @type (materialdesign|bootstrap|bootstrap4)
         * @default materialdesign
         * @example MaterialDesign <!-- materialicons, tree.base, checkbox -->
         * <div id="tree"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         dataSource: '/Locations/Get',
         *         width: 500,
         *         uiLibrary: 'materialdesign',
         *         checkboxes: true
         *     });
         * </script>
         * @example Bootstrap.3 <!-- bootstrap, tree.base, checkbox -->
         * <div id="tree"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         dataSource: '/Locations/Get',
         *         width: 500,
         *         uiLibrary: 'bootstrap',
         *         checkboxes: true
         *     });
         * </script>
         * @example Bootstrap.4 <!-- materialicons, bootstrap4, tree.base, checkbox -->
         * <div id="tree"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         dataSource: '/Locations/Get',
         *         width: 500,
         *         uiLibrary: 'bootstrap4',
         *         checkboxes: true
         *     });
         * </script>
         */
        uiLibrary: 'materialdesign',

        /** The name of the icons library that is going to be in use. Currently we support Material Icons, Font Awesome and Glyphicons.
         * @additionalinfo If you use Bootstrap 3 as uiLibrary, then the iconsLibrary is set to Glyphicons by default.<br/>
         * If you use Material Design as uiLibrary, then the iconsLibrary is set to Material Icons by default.<br/>
         * The css files for Material Icons, Font Awesome or Glyphicons should be manually included to the page where the grid is in use.
         * @type (materialicons|fontawesome|glyphicons)
         * @default 'materialicons'
         * @example Base.Theme.Material.Icons <!-- materialicons, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         dataSource: '/Locations/Get',
         *         width: 500,
         *         iconsLibrary: 'materialicons'
         *     });
         * </script>
         * @example Bootstrap.4.Font.Awesome <!-- bootstrap4, fontawesome, tree.base -->
         * <div id="tree"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         dataSource: '/Locations/Get',
         *         width: 500,
         *         uiLibrary: 'bootstrap4',
         *         iconsLibrary: 'fontawesome'
         *     });
         * </script>
         */
        iconsLibrary: 'materialicons',

        autoGenId: 1,

        indentation: 24,

        style: {
            wrapper: 'gj-unselectable',
            list: 'gj-list gj-list-md',
            item: undefined,
            active: 'gj-list-md-active',
            leafIcon: undefined,
            border: 'gj-tree-md-border'
        },

        icons: {
            /** Expand icon definition.
             * @alias icons.expand
             * @type String
             * @default '<i class="material-icons">keyboard_arrow_right</i>'
             * @example Plus.Minus.Icons <!-- materialicons, tree.base -->
             * <div id="tree"></div>
             * <script>
             *     var tree = $('#tree').tree({
             *         dataSource: '/Locations/Get',
             *         icons: { 
             *             expand: '<i class="material-icons">add</i>',
             *             collapse: '<i class="material-icons">remove</i>'
             *         }
             *     });
             * </script>
             */
            expand: '<i class="material-icons">keyboard_arrow_right</i>',

            /** Collapse icon definition.
             * @alias icons.collapse
             * @type String
             * @default '<i class="material-icons">keyboard_arrow_right</i>'
             * @example Plus.Minus.Icons <!-- materialicons, tree.base -->
             * <div id="tree"></div>
             * <script>
             *     var tree = $('#tree').tree({
             *         dataSource: '/Locations/Get',
             *         icons: { 
             *             expand: '<i class="material-icons">add</i>',
             *             collapse: '<i class="material-icons">remove</i>'
             *         }
             *     });
             * </script>
             */
            collapse: '<i class="material-icons">keyboard_arrow_down</i>'
        }
    },

    bootstrap: {
        indentation: 24,
        style: {
            wrapper: 'gj-unselectable gj-tree-bootstrap-3',
            list: 'gj-list gj-list-bootstrap list-group',
            item: 'list-group-item',
            active: 'active',
            border: 'gj-tree-bootstrap-border'
        },
        iconsLibrary: 'glyphicons'
    },

    bootstrap4: {
        indentation: 24,
        style: {
            wrapper: 'gj-unselectable gj-tree-bootstrap-4',
            list: 'gj-list gj-list-bootstrap list-group',
            item: 'list-group-item',
            active: 'active',
            border: 'gj-tree-bootstrap-border'
        }
    },

    materialicons: {
        indentation: 24,
        style: {
            expander: 'gj-tree-material-icons-expander'
        }
    },

    fontawesome: {
        style: {
            expander: 'gj-tree-font-awesome-expander'
        },
        icons: {
            expand: '<i class="fa fa-plus" aria-hidden="true"></i>',
            collapse: '<i class="fa fa-minus" aria-hidden="true"></i>'
        }
    },

    glyphicons: {
        style: {
            expander: 'gj-tree-glyphicons-expander'
        },
        icons: {
            expand: '<span class="glyphicon glyphicon-plus" />',
            collapse: '<span class="glyphicon glyphicon-minus" />'
        }
    }
};
/**
  * @widget Tree
  * @plugin Base
  */
gj.tree.events = {

    /**
     * Event fires when the tree is initialized
     * @event initialized
     * @param {object} e - event data
     * @example sample <!-- materialicons, tree.base -->
     * <button id="reload">Reload</button>
     * <div id="tree"></div>
     * <script>
     *     var tree = $('#tree').tree({
     *         dataSource: '/Locations/Get',
     *         initialized: function (e) {
     *             alert('initialized is fired.');
     *         }
     *     });
     *     $('#reload').on('click', function() { 
     *         tree.reload(); 
     *     });
     * </script>
     */
    initialized: function ($tree) {
        $tree.triggerHandler('initialized');
    },

    /**
     * Event fired before data binding takes place.
     * @event dataBinding
     * @param {object} e - event data
     * @example sample <!-- materialicons, tree.base -->
     * <div id="tree"></div>
     * <script>
     *     $('#tree').tree({
     *         dataSource: '/Locations/Get',
     *         dataBinding: function (e) {
     *             alert('dataBinding is fired.');
     *         }
     *     });
     * </script>
     */
    dataBinding: function ($tree) {
        $tree.triggerHandler('dataBinding');
    },

    /**
     * Event fires after the loading of the data in the tree.
     * @event dataBound
     * @param {object} e - event data
     * @example sample <!-- materialicons, tree.base -->
     * <div id="tree"></div>
     * <script>
     *     $('#tree').tree({
     *         dataSource: '/Locations/Get',
     *         dataBound: function (e) {
     *             alert('dataBound is fired.');
     *         }
     *     });
     * </script>
     */
    dataBound: function ($tree) {
        $tree.triggerHandler('dataBound');
    },

    /**
     * Event fires after selection of tree node.
     * @event select
     * @param {object} e - event data
     * @param {object} node - the node as jquery object
     * @param {string} id - the id of the record
     * @example sample <!-- materialicons, tree.base -->
     * <div id="tree" data-source="/Locations/Get"></div>
     * <script>
     *     var tree = $('#tree').tree();
     *     tree.on('select', function (e, node, id) {
     *         alert('select is fired.');
     *     });
     * </script>
     */
    select: function ($tree, $node, id) {
        return $tree.triggerHandler('select', [$node, id]);
    },

    /**
     * Event fires on un selection of tree node
     * @event unselect
     * @param {object} e - event data
     * @param {object} node - the node as jquery object
     * @param {string} id - the id of the record
     * @example sample <!-- materialicons, tree.base -->
     * <div id="tree" data-source="/Locations/Get"></div>
     * <script>
     *     var tree = $('#tree').tree();
     *     tree.on('unselect', function (e, node, id) {
     *         alert('unselect is fired.');
     *     });
     * </script>
     */
    unselect: function ($tree, $node, id) {
        return $tree.triggerHandler('unselect', [$node, id]);
    },

    /**
     * Event fires before node expand.
     * @event expand
     * @param {object} e - event data
     * @param {object} node - the node as jquery object
     * @param {string} id - the id of the record
     * @example Event.Sample <!-- materialicons, tree.base -->
     * <div id="tree" data-source="/Locations/Get"></div>
     * <script>
     *     var tree = $('#tree').tree();
     *     tree.on('expand', function (e, node, id) {
     *         alert('expand is fired.');
     *     });
     * </script>
     */
    expand: function ($tree, $node, id) {
        return $tree.triggerHandler('expand', [$node, id]);
    },

    /**
     * Event fires before node collapse.
     * @event collapse
     * @param {object} e - event data
     * @param {object} node - the node as jquery object
     * @param {string} id - the id of the record
     * @example Event.Sample <!-- materialicons, tree.base -->
     * <div id="tree" data-source="/Locations/Get"></div>
     * <script>
     *     var tree = $('#tree').tree();
     *     tree.on('collapse', function (e, node, id) {
     *         alert('collapse is fired.');
     *     });
     * </script>
     */
    collapse: function ($tree, $node, id) {
        return $tree.triggerHandler('collapse', [$node, id]);
    },

    /**
     * Event fires before tree destroy
     * @event destroying
     * @param {object} e - event data
     * @example Event.Sample <!-- materialicons, tree.base -->
     * <button onclick="tree.destroy()">Destroy</button>
     * <br/><br/>
     * <div id="tree" data-source="/Locations/Get"></div>
     * <script>
     *     var tree = $('#tree').tree();
     *     tree.on('destroying', function (e) {
     *         alert('destroying is fired.');
     *     });
     * </script>
     */
    destroying: function ($tree) {
        return $tree.triggerHandler('destroying');
    },

    /**
     * Event fires when the data is bound to node.
     * @event nodeDataBound
     * @param {object} e - event data
     * @param {object} node - the node as jquery object
     * @param {string} id - the id of the record
     * @param {object} record - the data of the node record
     * @example Event.Sample <!-- materialicons, tree.base -->
     * <div id="tree" data-source="/Locations/Get"></div>
     * <script>
     *     var tree = $('#tree').tree();
     *     tree.on('nodeDataBound', function (e, node, id, record) {
     *         if ((parseInt(id, 10) % 2) === 0) {
     *             node.css('background-color', 'red');
     *         }
     *     });
     * </script>
     */
    nodeDataBound: function ($tree, $node, id, record) {
        return $tree.triggerHandler('nodeDataBound', [$node, id, record]);
    }
}
/*global gj $*/
gj.tree.methods = {

    init: function (jsConfig) {
        gj.widget.prototype.init.call(this, jsConfig, 'tree');

        gj.tree.methods.initialize.call(this);

        if (this.data('autoLoad')) {
            this.reload();
        }
        return this;
    },

    initialize: function () {
        var data = this.data(),
            $root = $('<ul class="' + data.style.list + '"/>');
        this.empty().addClass(data.style.wrapper).append($root);
        if (data.width) {
            this.width(data.width);
        }
        if (data.border) {
            this.addClass(data.style.border);
        }
        gj.tree.events.initialized(this);
    },

    useHtmlDataSource: function ($tree, data) {
        data.dataSource = [];
    },

    render: function ($tree, response) {
        if (response) {
            if (typeof (response) === 'string' && JSON) {
                response = JSON.parse(response);
            }
            $tree.data('records', gj.tree.methods.getRecords($tree, response));
            gj.tree.methods.loadData($tree);
        }
        return $tree;
    },

    filter: function ($grid) {
        return $grid.data().dataSource;
    },

    getRecords: function ($tree, response) {
        var i, id, nodeData, result = [],
            data = $tree.data();
        for (i = 0; i < response.length; i++) {
            id = data.primaryKey ? response[i][data.primaryKey] : data.autoGenId++;
            nodeData = { id: id, data: response[i] };
            if (response[i][data.childrenField] && response[i][data.childrenField].length) {
                nodeData.children = gj.tree.methods.getRecords($tree, response[i][data.childrenField]);
                delete response[i][data.childrenField];
            }
            result.push(nodeData);
        }
        return result;
    },

    loadData: function ($tree) {
        var i,
            records = $tree.data('records'),
            $root = $tree.children('ul');

        gj.tree.events.dataBinding($tree);
        $root.off().empty();
        for (i = 0; i < records.length; i++) {
            gj.tree.methods.appendNode($tree, $root, records[i], 1);
        }
        gj.tree.events.dataBound($tree);
    },

    appendNode: function ($tree, $parent, nodeData, level, position) {
        var i, $node, $newParent, $span, $img,
            data = $tree.data(),
            $node = $('<li data-id="' + nodeData.id + '" data-role="node" />').addClass(data.style.item),
            $wrapper = $('<div data-role="wrapper" />'),
            $expander = $('<span data-role="expander" data-mode="close"></span>').addClass(data.style.expander),
            $display = $('<span data-role="display">' + nodeData.data[data.textField] + '</span>');

        if (data.indentation) {
            $wrapper.append('<span data-role="spacer" style="width: ' + (data.indentation * (level - 1)) + 'px;"></span>');
        }

        $expander.on('click', gj.tree.methods.expanderClickHandler($tree));
        $wrapper.append($expander);

        $display.on('click', gj.tree.methods.displayClickHandler($tree));
        $wrapper.append($display);
        $node.append($wrapper);

        if (position) {
            $parent.find('li:eq(' + (position - 1) + ')').before($node);
        } else {
            $parent.append($node);
        }

        if (nodeData.children && nodeData.children.length) {
            $expander.empty().append(data.icons.expand);
            $newParent = $('<ul />').addClass(data.style.list).addClass('gj-hidden');
            $node.append($newParent);

            for (i = 0; i < nodeData.children.length; i++) {
                gj.tree.methods.appendNode($tree, $newParent, nodeData.children[i], level + 1);
            }
        } else {
            data.style.leafIcon ? $expander.addClass(data.style.leafIcon) : $expander.html('&nbsp;');
        }

        if (data.imageCssClassField && nodeData.data[data.imageCssClassField]) {
            $('<span data-role="image"><span class="' + nodeData.data[data.imageCssClassField] + '"></span></span>').insertBefore($display);
        } else if (data.imageUrlField && nodeData.data[data.imageUrlField]) {
            $span = $('<span data-role="image"></span>');
            $span.insertBefore($display);
            $img = $('<img src="' + nodeData.data[data.imageUrlField] + '"></img>');
            $img.attr('width', $span.width()).attr('height', $span.height());
            $span.append($img);
        } else if (data.imageHtmlField && nodeData.data[data.imageHtmlField]) {
            $span = $('<span data-role="image">' + nodeData.data[data.imageHtmlField] + '</span>');
            $span.insertBefore($display);
        }

        gj.tree.events.nodeDataBound($tree, $node, nodeData.id, nodeData.data);
    },

    expanderClickHandler: function ($tree) {
        return function (e) {
            var $expander = $(this),
                $node = $expander.closest('li');
            if ($expander.attr('data-mode') === 'close') {
                $tree.expand($node);
            } else {
                $tree.collapse($node);
            }
        }
    },

    expand: function ($tree, $node, cascade) {
        var $children, i,
            $expander = $node.find('>[data-role="wrapper"]>[data-role="expander"]'),
            data = $tree.data(),
            id = $node.attr('data-id'),
            $list = $node.children('ul');
        if ($list && $list.length && gj.tree.events.expand($tree, $node, id) !== false) {
            $list.show();
            $expander.attr('data-mode', 'open');
            $expander.empty().append(data.icons.collapse);
            if (cascade) {
                $children = $node.find('ul>li');
                for (i = 0; i < $children.length; i++) {
                    gj.tree.methods.expand($tree, $($children[i]), cascade);
                }
            }
        }
        return $tree;
    },

    collapse: function ($tree, $node, cascade) {
        var $children, i,
            $expander = $node.find('>[data-role="wrapper"]>[data-role="expander"]'),
            data = $tree.data(),
            id = $node.attr('data-id'),
            $list = $node.children('ul');
        if ($list && $list.length && gj.tree.events.collapse($tree, $node, id) !== false) {
            $list.hide();
            $expander.attr('data-mode', 'close');
            $expander.empty().append(data.icons.expand);
            if (cascade) {
                $children = $node.find('ul>li');
                for (i = 0; i < $children.length; i++) {
                    gj.tree.methods.collapse($tree, $($children[i]), cascade);
                }
            }
        }
        return $tree;
    },

    expandAll: function ($tree) {
        var i, $nodes = $tree.find('ul>li');
        for (i = 0; i < $nodes.length; i++) {
            gj.tree.methods.expand($tree, $($nodes[i]), true);
        }
        return $tree;
    },

    collapseAll: function ($tree) {
        var i, $nodes = $tree.find('ul>li');
        for (i = 0; i < $nodes.length; i++) {
            gj.tree.methods.collapse($tree, $($nodes[i]), true);
        }
        return $tree;
    },

    displayClickHandler: function ($tree) {
        return function (e) {
            var $display = $(this),
                $node = $display.closest('li'),
                cascade = $tree.data().cascadeSelection;
            if ($node.attr('data-selected') === 'true') {
                gj.tree.methods.unselect($tree, $node, cascade);
            } else {
                if ($tree.data('selectionType') === 'single') {
                    gj.tree.methods.unselectAll($tree);
                }
                gj.tree.methods.select($tree, $node, cascade);
            }
        }
    },

    selectAll: function ($tree) {
        var i, $nodes = $tree.find('ul>li');
        for (i = 0; i < $nodes.length; i++) {
            gj.tree.methods.select($tree, $($nodes[i]), true);
        }
        return $tree;
    },

    select: function ($tree, $node, cascade) {
        var i, $children, data = $tree.data();
        if ($node.attr('data-selected') !== 'true' && gj.tree.events.select($tree, $node, $node.attr('data-id')) !== false) {
            $node.addClass(data.style.active).attr('data-selected', 'true');
            if (cascade) {
                $children = $node.find('ul>li');
                for (i = 0; i < $children.length; i++) {
                    gj.tree.methods.select($tree, $($children[i]), cascade);
                }
            }
        }
    },
    
    unselectAll: function ($tree) {
        var i, $nodes = $tree.find('ul>li');
        for (i = 0; i < $nodes.length; i++) {
            gj.tree.methods.unselect($tree, $($nodes[i]), true);
        }
        return $tree;
    },

    unselect: function ($tree, $node, cascade) {
        var i, $children, data = $tree.data();
        if ($node.attr('data-selected') === 'true' && gj.tree.events.unselect($tree, $node, $node.attr('data-id')) !== false) {
            $node.removeClass($tree.data().style.active).removeAttr('data-selected');
            if (cascade) {
                $children = $node.find('ul>li');
                for (i = 0; i < $children.length; i++) {
                    gj.tree.methods.unselect($tree, $($children[i]), cascade);
                }
            }
        }
    },

    getSelections: function ($list) {
        var i, $node, children,
            result = [],
            $nodes = $list.children('li');
        if ($nodes && $nodes.length) {
            for (i = 0; i < $nodes.length; i++) {
                $node = $($nodes[i]);
                if ($node.attr('data-selected') === 'true') {
                    result.push($node.attr('data-id'));
                } else if ($node.has('ul')) {
                    children = gj.tree.methods.getSelections($node.children('ul'));
                    if (children.length) {
                        result = result.concat(children);
                    }
                }
            }
        }

        return result;
    },

    getById: function ($tree, id, records) {
        var i, result = undefined;
        for (i = 0; i < records.length; i++) {
            if (id == records[i].id) {
                result = records[i];
                break;
            } else if (records[i].children && records[i].children.length) {
                result = gj.tree.methods.getById($tree, id, records[i].children);
                if (result) {
                    break;
                }
            }
        }
        return result;
    },

    getDataById: function ($tree, id, records) {
        var result = gj.tree.methods.getById($tree, id, records);
        return result ? result.data : result;
    },

    getDataByText: function ($tree, text, records) {
        var i, id,
            result = undefined,
            data = $tree.data();
        for (i = 0; i < records.length; i++) {
            if (text === records[i].data[data.textField]) {
                result = records[i].data;
                break;
            } else if (records[i].children && records[i].children.length) {
                result = gj.tree.methods.getDataByText($tree, text, records[i].children);
                if (result) {
                    break;
                }
            }
        }
        return result;
    },

    getNodeById: function ($list, id) {
        var i, $node,
            $result = undefined,
            $nodes = $list.children('li');
        if ($nodes && $nodes.length) {
            for (i = 0; i < $nodes.length; i++) {
                $node = $($nodes[i]);
                if (id == $node.attr('data-id')) {
                    $result = $node;
                    break;
                } else if ($node.has('ul')) {
                    $result = gj.tree.methods.getNodeById($node.children('ul'), id);
                    if ($result) {
                        break;
                    }
                }
            }
        }
        return $result;
    },

    getNodeByText: function ($list, text) {
        var i, $node,
            $result = undefined,
            $nodes = $list.children('li');
        if ($nodes && $nodes.length) {
            for (i = 0; i < $nodes.length; i++) {
                $node = $($nodes[i]);
                if (text === $node.find('>[data-role="wrapper"]>[data-role="display"]').text()) {
                    $result = $node;
                    break;
                } else if ($node.has('ul')) {
                    $result = gj.tree.methods.getNodeByText($node.children('ul'), text);
                    if ($result) {
                        break;
                    }
                }
            }
        }
        return $result;
    },

    addNode: function ($tree, data, $parent, position) {
        var level, nodeData = gj.tree.methods.getRecords($tree, [data]);

        if (!$parent || !$parent.length) {
            $parent = $tree.children('ul');
        }
        level = $parent.parentsUntil('[data-type="tree"]', 'ul').length + 1;

        gj.tree.methods.appendNode($tree, $parent, nodeData[0], level, position);

        return $tree;
    },

    remove: function ($tree, $node) {
        gj.tree.methods.removeDataById($tree, $node.attr('data-id'), $tree.data('records'));
        $node.remove();    
        return $tree;
    },

    removeDataById: function ($tree, id, records) {
        var i;
        for (i = 0; i < records.length; i++) {
            if (id == records[i].id) {
                records.splice(i, 1);
                break;
            } else if (records[i].children && records[i].children.length) {
                gj.tree.methods.removeDataById($tree, id, records[i].children);
            }
        }
    },

    destroy: function ($tree) {
        var data = $tree.data();
        if (data) {
            gj.tree.events.destroying($tree);
            $tree.xhr && $tree.xhr.abort();
            $tree.off();
            $tree.removeData();
            $tree.removeAttr('data-type');
            $tree.removeClass().empty();
        }
        return $tree;
    }
}
/**
  * @widget Tree
  * @plugin Base
  */
gj.tree.widget = function ($element, jsConfig) {
    var self = this,
        methods = gj.tree.methods;

    /**
     * Reload the tree.
     * @method
     * @param {object} params - Params that needs to be send to the server. Only in use for remote data sources.
     * @return jQuery object
     * @example Method.Sample <!-- materialicons, tree.base -->
     * <button onclick="tree.reload()">Reload</button>
     * <br/><br/>
     * <div id="tree"></div>
     * <script>
     *     var tree = $('#tree').tree({
     *         dataSource: '/Locations/Get',
     *         autoLoad: false
     *     });
     * </script>
     */
    self.reload = function (params) {
        return gj.widget.prototype.reload.call(this, params);
    };

    /**
     * Render data in the tree
     * @method
     * @param {object} response - An object that contains the data that needs to be loaded in the tree.
     * @fires dataBinding, dataBound
     * @return tree
     * @example sample <!-- materialicons, tree.base -->
     * <div id="tree"></div>
     * <script>
     *     var tree, onSuccessFunc;
     *     onSuccessFunc = function (response) {
     *         //you can modify the response here if needed
     *         tree.render(response);
     *     };
     *     tree = $('#tree').tree({
     *         dataSource: { url: '/Locations/Get', success: onSuccessFunc }
     *     });
     * </script>
     */
    self.render = function (response) {
        return methods.render(this, response);
    };

    /**
     * Add node to the tree.
     * @method
     * @param {object} data - The node data.
     * @param {object} parentNode - Parent node as jquery object.
     * @param {Number} position - Position where the new node need to be added. 
     * @return jQuery object
     * @example Append.ToRoot <!-- materialicons, tree.base -->
     * <button onclick="append()">Append Node</button>
     * <br/><br/>
     * <div id="tree" data-source="/Locations/Get"></div>
     * <script>
     *     var tree = $('#tree').tree();
     *     function append() {
     *         tree.addNode({ text: 'New Node' });
     *     }
     * </script>
     * @example Append.Parent <!-- materialicons, tree.base -->
     * <button onclick="append()">Append Node</button>
     * <br/><br/>
     * <div id="tree" data-source="/Locations/Get"></div>
     * <script>
     *     var parent, tree = $('#tree').tree();
     *     tree.on('dataBound', function () {
     *         parent = tree.getNodeByText('Asia').children('ul');
     *         tree.off('dataBound');
     *     });
     *     function append() {
     *         tree.addNode({ text: 'New Node' }, parent);
     *     }
     * </script>
     * @example Bootstrap <!-- bootstrap, tree.base -->
     * <button onclick="append()">Append Node</button>
     * <br/><br/>
     * <div id="tree" data-source="/Locations/Get" data-ui-library="bootstrap"></div>
     * <script>
     *     var parent, tree = $('#tree').tree();
     *     tree.on('dataBound', function () {
     *         parent = tree.getNodeByText('Asia').children('ul');
     *         tree.off('dataBound');
     *     });
     *     function append() {
     *         tree.addNode({ text: 'New Node' }, parent);
     *     }
     * </script>
     * @example Prepend <!-- materialicons, tree.base -->
     * <button onclick="append()">Append Node</button>
     * <br/><br/>
     * <div id="tree" data-source="/Locations/Get"></div>
     * <script>
     *     var parent, tree = $('#tree').tree();
     *     tree.on('dataBound', function () {
     *         parent = tree.getNodeByText('Asia').children('ul');
     *         tree.off('dataBound');
     *     });
     *     function append() {
     *         tree.addNode({ text: 'New Node' }, parent, 1);
     *     }
     * </script>
     * @example Position <!-- materialicons, tree.base -->
     * <button onclick="append()">Append Node</button>
     * <br/><br/>
     * <div id="tree" data-source="/Locations/Get"></div>
     * <script>
     *     var parent, tree = $('#tree').tree();
     *     tree.on('dataBound', function () {
     *         parent = tree.getNodeByText('Asia').children('ul');
     *         tree.off('dataBound');
     *     });
     *     function append() {
     *         tree.addNode({ text: 'New Node' }, parent, 2);
     *     }
     * </script>
     */
    self.addNode = function (data, $parentNode, position) {
        return methods.addNode(this, data, $parentNode, position);
    };

    /**
     * Remove node from the tree.
     * @method
     * @param {object} node - The node as jQuery object
     * @return jQuery object
     * @example Method.Sample <!-- materialicons, tree.base -->
     * <button onclick="remove()">Remove USA</button>
     * <br/><br/>
     * <div id="tree"></div>
     * <script>
     *     var tree = $('#tree').tree({
     *         dataSource: '/Locations/Get'
     *     });
     *     function remove() {
     *         var node = tree.getNodeByText('USA');
     *         tree.removeNode(node);
     *     }
     * </script>
     */
    self.removeNode = function ($node) {
        return methods.remove(this, $node);
    };

    /**
     * Destroy the tree.
     * @method
     * @return jQuery object
     * @example Method.Sample <!-- materialicons, tree.base -->
     * <button onclick="tree.destroy()">Destroy</button>
     * <br/><br/>
     * <div id="tree"></div>
     * <script>
     *     var tree = $('#tree').tree({
     *         dataSource: '/Locations/Get'
     *     });
     * </script>
     */
    self.destroy = function () {
        return methods.destroy(this);
    };

    /**
     * Expand node from the tree.
     * @method
     * @param {object} node - The node as jQuery object
     * @param {boolean} cascade - Expand all children
     * @return jQuery object
     * @example Method.Sample <!-- materialicons, tree.base -->
     * <button onclick="expand()">Expand Asia</button><button onclick="collapse()">Collapse Asia</button>
     * <br/><br/>
     * <div id="tree"></div>
     * <script>
     *     var tree = $('#tree').tree({
     *         dataSource: '/Locations/Get'
     *     });
     *     function expand() {
     *         var node = tree.getNodeByText('Asia');
     *         tree.expand(node);
     *     }
     *     function collapse() {
     *         var node = tree.getNodeByText('Asia');
     *         tree.collapse(node);
     *     }
     * </script>
     * @example Cascade <!-- materialicons, tree.base -->
     * <button onclick="expand()">Expand North America</button>
     * <br/><br/>
     * <div id="tree"></div>
     * <script>
     *     var tree = $('#tree').tree({
     *         dataSource: '/Locations/Get'
     *     });
     *     function expand() {
     *         var node = tree.getNodeByText('North America');
     *         tree.expand(node, true);
     *     }
     * </script>
     */
    self.expand = function ($node, cascade) {
        return methods.expand(this, $node, cascade);
    };

    /**
     * Collapse node from the tree.
     * @method
     * @param {object} node - The node as jQuery object
     * @param {boolean} cascade - Collapse all children
     * @return jQuery object
     * @example Method.Sample <!-- materialicons, tree.base -->
     * <button onclick="expand()">Expand Asia</button><button onclick="collapse()">Collapse Asia</button>
     * <br/><br/>
     * <div id="tree"></div>
     * <script>
     *     var tree = $('#tree').tree({
     *         dataSource: '/Locations/Get'
     *     });
     *     function expand() {
     *         var node = tree.getNodeByText('Asia');
     *         tree.expand(node);
     *     }
     *     function collapse() {
     *         var node = tree.getNodeByText('Asia');
     *         tree.collapse(node);
     *     }
     * </script>
     * @example Cascade <!-- materialicons, tree.base -->
     * <button onclick="collapse()">Collapse North America</button>
     * <br/><br>
     * <div id="tree"></div>
     * <script>
     *     var tree = $('#tree').tree({
     *         dataSource: '/Locations/Get'
     *     });
     *     function collapse() {
     *         var node = tree.getNodeByText('North America');
     *         tree.collapse(node, true);
     *     }
     * </script>
     */
    self.collapse = function ($node, cascade) {
        return methods.collapse(this, $node, cascade);
    };

    /**
     * Expand all tree nodes
     * @method
     * @return jQuery object
     * @example Sample <!-- materialicons, tree.base -->
     * <button onclick="tree.expandAll()">Expand All</button><button onclick="tree.collapseAll()">Collapse All</button>
     * <br/><br/>
     * <div id="tree" data-source="/Locations/Get"></div>
     * <script>
     *     var tree = $('#tree').tree();
     * </script>
     */
    self.expandAll = function () {
        return methods.expandAll(this);
    };

    /**
     * Collapse all tree nodes
     * @method
     * @return jQuery object
     * @example Sample <!-- materialicons, tree.base -->
     * <button onclick="tree.expandAll()">Expand All</button><button onclick="tree.collapseAll()">Collapse All</button>
     * <br/><br/>
     * <div id="tree" data-source="/Locations/Get"></div>
     * <script>
     *     var tree = $('#tree').tree();
     * </script>
     */
    self.collapseAll = function () {
        return methods.collapseAll(this);
    };

    /**
     * Return node data by id of the record.
     * @method
     * @param {string|number} id - The id of the record that needs to be returned
     * @return object
     * @example sample <!-- materialicons, tree.base -->
     * <button id="btnGetData">Get Data</button>
     * <br/><br/>
     * <div id="tree"></div>
     * <script>
     *     var tree = $('#tree').tree({
     *         dataSource: '/Locations/Get',
     *         primaryKey: 'id' //define the name of the column that you want to use as ID here.
     *     });
     *     $('#btnGetData').on('click', function () {
     *         var data = tree.getDataById(9);
     *         alert('The population of ' + data.text + ' is ' + data.population);
     *     });
     * </script>
     */
    self.getDataById = function (id) {
        return methods.getDataById(this, id, this.data('records'));
    };

    /**
     * Return node data by text.
     * @method
     * @param {string} text - The text of the record that needs to be returned
     * @return object
     * @example sample <!-- materialicons, tree.base -->
     * <button id="btnGetData">Get Data</button>
     * <br/><br/>
     * <div id="tree"></div>
     * <script>
     *     var tree = $('#tree').tree({
     *         dataSource: '/Locations/Get',
     *     });
     *     $('#btnGetData').on('click', function () {
     *         var data = tree.getDataByText('California');
     *         alert('The population of California is ' + data.population);
     *     });
     * </script>
     */
    self.getDataByText = function (text) {
        return methods.getDataByText(this, text, this.data('records'));
    };

    /**
     * Return node by id of the record.
     * @method
     * @param {string} id - The id of the node that needs to be returned
     * @return jQuery object
     * @example sample <!-- materialicons, tree.base -->
     * <div id="tree"></div>
     * <script>
     *     var tree = $('#tree').tree({
     *         dataSource: '/Locations/Get',
     *         primaryKey: 'id' //define the name of the column that you want to use as ID here.
     *     });
     *     tree.on('dataBound', function() {
     *         var node = tree.getNodeById('1');
     *         node.css('background-color', 'red');
     *     });
     * </script>
     */
    self.getNodeById = function (id) {
        return methods.getNodeById(this.children('ul'), id);
    };

    /**
     * Return node by text.
     * @method
     * @param {string} text - The text in the node that needs to be returned
     * @return jQuery object
     * @example sample <!-- materialicons, tree.base -->
     * <div id="tree"></div>
     * <script>
     *     var tree = $('#tree').tree({
     *         dataSource: '/Locations/Get'
     *     });
     *     tree.on('dataBound', function() {
     *         var node = tree.getNodeByText('Asia');
     *         node.css('background-color', 'red');
     *     });
     * </script>
     */
    self.getNodeByText = function (text) {
        return methods.getNodeByText(this.children('ul'), text);
    };

    /**
     * Select all tree nodes
     * @method
     * @return jQuery object
     * @example Sample <!-- materialicons, tree.base -->
     * <button onclick="tree.selectAll()">Select All</button><button onclick="tree.unselectAll()">Unselect All</button>
     * <br/><br/>
     * <div id="tree" data-source="/Locations/Get"></div>
     * <script>
     *     var tree = $('#tree').tree({
     *         selectionType: 'multiple'
     *     });
     *     tree.on('dataBound', function() {
     *         tree.expandAll();
     *     });
     * </script>
     */
    self.selectAll = function () {
        return methods.selectAll(this);
    };

    /**
     * Unselect all tree nodes
     * @method
     * @return jQuery object
     * @example Sample <!-- materialicons, tree.base -->
     * <button onclick="tree.selectAll()">Select All</button><button onclick="tree.unselectAll()">Unselect All</button>
     * <br/><br/>
     * <div id="tree" data-source="/Locations/Get"></div>
     * <script>
     *     var tree = $('#tree').tree({
     *         selectionType: 'multiple'
     *     });
     *     tree.on('dataBound', function() {
     *         tree.expandAll();
     *     });
     * </script>
     */
    self.unselectAll = function () {
        return methods.unselectAll(this);
    };

    /**
     * Return an array with the ids of the selected nodes.
     * @method
     * @return array
     * @example Sample <!-- materialicons, tree.base -->
     * <button id="btnShowSelection">Show Selections</button>
     * <br/><br/>
     * <div id="tree" data-source="/Locations/Get"></div>
     * <script>
     *     var tree = $('#tree').tree({
     *         selectionType: 'multiple'
     *     });
     *     $('#btnShowSelection').on('click', function () {
     *         var selections = tree.getSelections();
     *         selections && selections.length && alert(selections.join());
     *     });
     * </script>
     */
    self.getSelections = function () {
        return methods.getSelections(this.children('ul'));
    };

    $.extend($element, self);
    if ('tree' !== $element.attr('data-type')) {
        methods.init.call($element, jsConfig);
    }

    return $element;
};

gj.tree.widget.prototype = new gj.widget();
gj.tree.widget.constructor = gj.tree.widget;

(function ($) {
    $.fn.tree = function (method) {
        var $widget;        
        if (this && this.length) {
            if (typeof method === 'object' || !method) {
                return new gj.tree.widget(this, method);
            } else {
                $widget = new gj.tree.widget(this, null);
                if ($widget[method]) {
                    return $widget[method].apply(this, Array.prototype.slice.call(arguments, 1));
                } else {
                    throw 'Method ' + method + ' does not exist.';
                }
            }
        }
    };
})(jQuery);
/** 
 * @widget Tree 
 * @plugin Checkboxes
 */
gj.tree.plugins.checkboxes = {
    config: {
        base: {
            /** Add checkbox for each node, if set to true.
              * @type Boolean
              * @default undefined
              * @example Bootstrap <!-- bootstrap, checkbox, tree.base -->
              * <div class="container-fluid">
              *     <h3>Bootstrap Treeview With Checkboxes</h3>
              *     <div id="tree"></div>
              * </div>
              * <script>
              *     var tree = $('#tree').tree({
              *         dataSource: '/Locations/Get',
              *         checkboxes: true,
              *         uiLibrary: 'bootstrap'
              *     });
              * </script>
              * @example Material.Design <!-- materialicons, checkbox, tree.base -->
              * <div class="container-fluid">
              *     <h3>Material Design Treeview With Checkboxes</h3>
              *     <div id="tree"></div>
              * </div>
              * <script>
              *     var tree = $('#tree').tree({
              *         dataSource: '/Locations/Get',
              *         checkboxes: true,
              *         uiLibrary: 'materialdesign'
              *     });
              * </script>
              */
            checkboxes: undefined,

            /** Name of the source field, that indicates if the checkbox is checked.
             * @type string
             * @default 'checked'
             * @example Custom.Name <!-- materialicons, checkbox, tree.base -->
             * <div id="tree"></div>
             * <script>
             *     var tree = $('#tree').tree({
             *         checkboxes: true,
             *         checkedField: 'checkedFieldName',
             *         dataSource: [ { text: 'foo', checkedFieldName: false, children: [ { text: 'bar', checkedFieldName: true }, { text: 'bar2', checkedFieldName: false } ] }, { text: 'foo2', children: [ { text: 'bar2' } ] } ]
             *     });
             * </script>
             */
            checkedField: 'checked',

            /** This setting enable cascade check and uncheck of children
             * @type boolean
             * @default true
             * @example False <!-- materialicons, checkbox, tree.base -->
             * <div id="tree"></div>
             * <script>
             *     var tree = $('#tree').tree({
             *         checkboxes: true,
             *         dataSource: '/Locations/Get',
             *         cascadeCheck: false
             *     });
             *     tree.on('dataBound', function() {
             *         tree.expandAll();
             *     });
             * </script>
             * @example True <!-- materialicons, checkbox, tree.base -->
             * <div id="tree"></div>
             * <script>
             *     var tree = $('#tree').tree({
             *         checkboxes: true,
             *         dataSource: '/Locations/Get',
             *         cascadeCheck: true
             *     });
             *     tree.on('dataBound', function() {
             *         tree.expandAll();
             *     });
             * </script>
             */
            cascadeCheck: true,
        }
    },

    private: {
        nodeDataBound: function ($tree, $node, id, record) {
            var data = $tree.data(),
                $expander = $node.find('> [data-role="wrapper"] > [data-role="expander"]'),
                $checkbox = $('<input type="checkbox"/>'),
                $wrapper = $('<span data-role="checkbox"></span>').append($checkbox);
            $checkbox = $checkbox.checkbox({
                uiLibrary: data.uiLibrary,
                iconsLibrary: data.iconsLibrary,
                change: function (e, state) {
                    gj.tree.plugins.checkboxes.events.checkboxChange($tree, $node, record, $checkbox.state());
                }
            });
            if (record[data.checkedField]) {
                $checkbox.state('checked');
            }
            $checkbox.on('click', function (e) {
                var $node = $checkbox.closest('li'),
                    state = $checkbox.state();
                if (data.cascadeCheck) {
                    gj.tree.plugins.checkboxes.private.updateChildrenState($node, state);
                    gj.tree.plugins.checkboxes.private.updateParentState($node, state);
                }
            });
            $expander.after($wrapper);
        },

        updateParentState: function ($node, state) {
            var $parentNode, $parentCheckbox, $siblingCheckboxes, allChecked, allUnchecked, parentState;

            $parentNode = $node.parent('ul').parent('li');
            if ($parentNode.length === 1) {
                $parentCheckbox = $node.parent('ul').parent('li').find('> [data-role="wrapper"] > [data-role="checkbox"] input[type="checkbox"]');
                $siblingCheckboxes = $node.siblings().find('> [data-role="wrapper"] > span[data-role="checkbox"] input[type="checkbox"]');
                allChecked = (state === 'checked');
                allUnchecked = (state === 'unchecked');
                parentState = 'indeterminate';
                $.each($siblingCheckboxes, function () {
                    var state = $(this).checkbox('state');
                    if (allChecked && state !== 'checked') {
                        allChecked = false;
                    }
                    if (allUnchecked && state !== 'unchecked') {
                        allUnchecked = false;
                    }
                });
                if (allChecked && !allUnchecked) {
                    parentState = 'checked';
                }
                if (!allChecked && allUnchecked) {
                    parentState = 'unchecked';
                }
                $parentCheckbox.checkbox('state', parentState);
                gj.tree.plugins.checkboxes.private.updateParentState($parentNode, $parentCheckbox.checkbox('state'));
            }
        },

        updateChildrenState: function ($node, state) {
            var $childrenCheckboxes = $node.find('ul li [data-role="wrapper"] [data-role="checkbox"] input[type="checkbox"]');
            if ($childrenCheckboxes.length > 0) {
                $.each($childrenCheckboxes, function () {
                    $(this).checkbox('state', state);
                });
            }
        },

        update: function ($tree, $node, state) {
            var checkbox = $node.find('[data-role="checkbox"] input[type="checkbox"]').first();
            $(checkbox).checkbox('state', state);
            if ($tree.data().cascadeCheck) {
                gj.tree.plugins.checkboxes.private.updateChildrenState($node, state);
                gj.tree.plugins.checkboxes.private.updateParentState($node, state);
            }
        }
    },

    public: {

        /** Get ids of all checked nodes
         * @method
         * @return Array
         * @example Base.Theme <!-- materialicons, checkbox, tree.base -->
         * <button id="btnGet">Get Checked Nodes</button>
         * <div id="tree"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         dataSource: '/Locations/Get',
         *         checkboxes: true
         *     });
         *     $('#btnGet').on('click', function() {
         *         var result = tree.getCheckedNodes();
         *         alert(result.join());
         *     });
         * </script>
         */
        getCheckedNodes: function () {
            var result = [],
                checkboxes = this.find('li [data-role="checkbox"] input[type="checkbox"]');
            $.each(checkboxes, function () {
                var checkbox = $(this);
                if (checkbox.checkbox('state') === 'checked') {
                    result.push(checkbox.closest('li').data('id'));
                }
            });
            return result;
        },

        /**
         * Check all tree nodes
         * @method
         * @return tree as jQuery object
         * @example Sample <!-- materialicons, checkbox, tree.base -->
         * <button onclick="tree.checkAll()">Check All</button><button onclick="tree.uncheckAll()">Uncheck All</button>
         * <br/><br/>
         * <div id="tree" data-source="/Locations/Get"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         checkboxes: true
         *     });
         *     tree.on('dataBound', function() {
         *         tree.expandAll();
         *     });
         * </script>
         */
        checkAll: function () {
            var $checkboxes = this.find('li [data-role="checkbox"] input[type="checkbox"]');
            $.each($checkboxes, function () {
                $(this).checkbox('state', 'checked');
            });
            return this;
        },

        /**
         * Uncheck all tree nodes
         * @method
         * @return tree as jQuery object
         * @example Sample <!-- materialicons, checkbox, tree.base -->
         * <button onclick="tree.checkAll()">Check All</button><button onclick="tree.uncheckAll()">Uncheck All</button>
         * <br/><br/>
         * <div id="tree" data-source="/Locations/Get"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         checkboxes: true
         *     });
         *     tree.on('dataBound', function() {
         *         tree.expandAll();
         *     });
         * </script>
         */
        uncheckAll: function () {
            var $checkboxes = this.find('li [data-role="checkbox"] input[type="checkbox"]');
            $.each($checkboxes, function () {
                $(this).checkbox('state', 'unchecked');
            });
            return this;
        },

        /**
         * Check tree node.
         * @method
         * @param {object} node - The node as jQuery object
         * @return tree as jQuery object
         * @example Sample <!-- materialicons, checkbox, tree.base -->
         * <button onclick="tree.check(tree.getNodeByText('China'))">Check China</button>
         * <br/><br/>
         * <div id="tree" data-source="/Locations/Get"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         checkboxes: true
         *     });
         *     tree.on('dataBound', function() {
         *         tree.expandAll();
         *     });
         * </script>
         */
        check: function ($node) {
            gj.tree.plugins.checkboxes.private.update(this, $node, 'checked');
            return this;
        },

        /**
         * Uncheck tree node.
         * @method
         * @param {object} node - The node as jQuery object
         * @return tree as jQuery object
         * @example Sample <!-- materialicons, checkbox, tree.base -->
         * <button onclick="tree.uncheck(tree.getNodeByText('China'))">UnCheck China</button>
         * <br/><br/>
         * <div id="tree" data-source="/Locations/Get"></div>
         * <script>
         *     var tree = $('#tree').tree({
         *         checkboxes: true
         *     });
         *     tree.on('dataBound', function() {
         *         tree.expandAll();
         *         tree.check(tree.getNodeByText('China'));
         *     });
         * </script>
         */
        uncheck: function ($node) {
            gj.tree.plugins.checkboxes.private.update(this, $node, 'unchecked');
            return this;
        }
    },

    events: {
        /**
         * Event fires when the checkbox state is changed.
         * @event checkboxChange
         * @param {object} e - event data
         * @param {object} $node - the node object as jQuery element
         * @param {object} record - the record data
         * @param {string} state - the new state of the checkbox
         * @example Event.Sample <!-- materialicons, checkbox, tree.base -->
         * <div id="tree" data-source="/Locations/Get" data-checkboxes="true"></div>
         * <script>
         *     var tree = $('#tree').tree();
         *     tree.on('checkboxChange', function (e, $node, record, state) {
         *         alert('The new state of record ' + record.text + ' is ' + state);
         *     });
         * </script>
         */
        checkboxChange: function ($tree, $node, record, state) {
            return $tree.triggerHandler('checkboxChange', [$node, record, state]);
        }
    },

    configure: function ($tree) {
        if ($tree.data('checkboxes') && gj.checkbox) {
            $.extend(true, $tree, gj.tree.plugins.checkboxes.public);
            $tree.on('nodeDataBound', function (e, $node, id, record) {
                gj.tree.plugins.checkboxes.private.nodeDataBound($tree, $node, id, record);
            });
            $tree.on('dataBound', function () {
                $nodes = $tree.find('li[data-role="node"]');
                $.each($nodes, function () {
                    var $node = $(this),
                        state = $node.find('[data-role="checkbox"] input[type="checkbox"]').checkbox('state');
                    if (state === 'checked') {
                        gj.tree.plugins.checkboxes.private.updateChildrenState($node, state);
                        gj.tree.plugins.checkboxes.private.updateParentState($node, state);
                    }
                });
            });
        }
    }
};

/**
 * @widget Tree
 * @plugin DragAndDrop
 */
gj.tree.plugins.dragAndDrop = {
	config: {
		base: {
			/** Enables drag and drop functionality for each node.
              * @type Boolean
              * @default undefined
              * @example Bootstrap <!-- bootstrap, draggable.base, droppable.base, tree.base -->
              * <div class="container">
              *     <h3>Drag and Drop Tree Nodes</h3>
              *     <div id="tree"></div>
              * </div>
              * <script>
              *     $('#tree').tree({
              *         dataSource: '/Locations/Get',
              *         dragAndDrop: true,
              *         uiLibrary: 'bootstrap'
              *     });
              * </script>
              * @example Material.Design <!-- materialicons, draggable.base, droppable.base, tree.base -->
              * <h3>Drag and Drop Tree Nodes</h3>
              * <div id="tree"></div>
              * <script>
              *     $('#tree').tree({
              *         dataSource: '/Locations/Get',
              *         dragAndDrop: true,
              *         uiLibrary: 'materialdesign'
              *     });
              * </script>
              */
			dragAndDrop: undefined,

			style: {
			    dragEl: 'gj-tree-drag-el gj-tree-mdl-drag-el',
			    dropAsChildIcon: 'material-icons gj-cursor-pointer gj-mdl-icon-plus',
			    dropAbove: 'gj-tree-drop-above',
			    dropBelow: 'gj-tree-drop-below'
			}
		},

		bootstrap: {
		    style: {
		        dragEl: 'gj-tree-drag-el gj-tree-bootstrap-drag-el',
		        dropAsChildIcon: 'glyphicon glyphicon-plus',
		        dropAbove: 'gj-tree-drop-above',
		        dropBelow: 'gj-tree-drop-below'
		    }
		}
	},

	private: {
	    nodeDataBound: function ($tree, $node) {
	        var $wrapper = $node.children('[data-role="wrapper"]'),
    	        $display = $node.find('>[data-role="wrapper"]>[data-role="display"]');
	        if ($wrapper.length && $display.length) {
	            $display.on('mousedown', gj.tree.plugins.dragAndDrop.private.createNodeMouseDownHandler($tree, $node, $display));
		    }
		},

	    createNodeMouseDownHandler: function ($tree, $node, $display) {
		    return function (e) {
		        var data = $tree.data(), $dragEl, $wrapper, offsetTop, offsetLeft;
		        $dragEl = $display.clone().wrap('<div data-role="wrapper"/>').closest('div')
                            .wrap('<li class="' + data.style.item + '" />').closest('li')
                            .wrap('<ul class="' + data.style.list + '" />').closest('ul');
		        $('body').append($dragEl);
		        $dragEl.attr('data-role', 'draggable-clone').addClass('gj-unselectable').addClass(data.style.dragEl);
		        $dragEl.find('[data-role="wrapper"]').prepend('<span data-role="indicator" />');
		        $dragEl.draggable({
		            drag: gj.tree.plugins.dragAndDrop.private.createDragHandler($tree, $node, $display),
		            stop: gj.tree.plugins.dragAndDrop.private.createDragStopHandler($tree, $node, $display)
		        });
		        $wrapper = $display.parent();
		        offsetTop = $display.offset().top;
		        offsetTop -= parseInt($wrapper.css("border-top-width")) + parseInt($wrapper.css("margin-top")) + parseInt($wrapper.css("padding-top"));
		        offsetLeft = $display.offset().left;
		        offsetLeft -= parseInt($wrapper.css("border-left-width")) + parseInt($wrapper.css("margin-left")) + parseInt($wrapper.css("padding-left"));
		        offsetLeft -= $dragEl.find('[data-role="indicator"]').outerWidth(true);
		        $dragEl.css({
		            position: 'absolute', top: offsetTop, left: offsetLeft, width: $display.outerWidth(true)
		        });
		        if ($display.attr('data-droppable') === 'true') {
		            $display.droppable('destroy');
		        }
		        gj.tree.plugins.dragAndDrop.private.getTargetDisplays($tree, $node, $display).each(function () {
		            var $dropEl = $(this);
		            if ($dropEl.attr('data-droppable') === 'true') {
		                $dropEl.droppable('destroy');
		            }
		            $dropEl.droppable();
		        });
		        gj.tree.plugins.dragAndDrop.private.getTargetDisplays($tree, $node).each(function () {
		            var $dropEl = $(this);
		            if ($dropEl.attr('data-droppable') === 'true') {
		                $dropEl.droppable('destroy');
		            }
		            $dropEl.droppable();
		        });
		        $dragEl.trigger('mousedown');
		    };
	    },

	    getTargetDisplays: function ($tree, $node, $display) {
	        return $tree.find('[data-role="display"]').not($display).not($node.find('[data-role="display"]'));
	    },

	    getTargetWrappers: function ($tree, $node) {
	        return $tree.find('[data-role="wrapper"]').not($node.find('[data-role="wrapper"]'));
	    },

	    createDragHandler: function ($tree, $node, $display) {
	        var $displays = gj.tree.plugins.dragAndDrop.private.getTargetDisplays($tree, $node, $display),
                $wrappers = gj.tree.plugins.dragAndDrop.private.getTargetWrappers($tree, $node),
	            data = $tree.data();
	        return function (e, offset, mousePosition) {
	            var $dragEl = $(this), success = false;
	            $displays.each(function () {
	                var $targetDisplay = $(this),
	                    $indicator;
	                if ($targetDisplay.droppable('isOver', mousePosition)) {
	                    $indicator = $dragEl.find('[data-role="indicator"]');
	                    data.style.dropAsChildIcon ? $indicator.addClass(data.style.dropAsChildIcon) : $indicator.text('+');
	                    success = true;
	                    return false;
	                } else {
	                    $dragEl.find('[data-role="indicator"]').removeClass(data.style.dropAsChildIcon).empty();
                    }
	            });
	            $wrappers.each(function () {
	                var $wrapper = $(this),
                        $indicator, middle;
	                if (!success && $wrapper.droppable('isOver', mousePosition)) {
	                    middle = $wrapper.position().top + ($wrapper.outerHeight() / 2);
	                    if (mousePosition.top < middle) {
	                        $wrapper.addClass(data.style.dropAbove).removeClass(data.style.dropBelow);
	                    } else {
	                        $wrapper.addClass(data.style.dropBelow).removeClass(data.style.dropAbove);
	                    }
	                } else {
	                    $wrapper.removeClass(data.style.dropAbove).removeClass(data.style.dropBelow);
	                }
	            });
	        };
        },

	    createDragStopHandler: function ($tree, $sourceNode, $sourceDisplay) {
	        var $displays = gj.tree.plugins.dragAndDrop.private.getTargetDisplays($tree, $sourceNode, $sourceDisplay),
                $wrappers = gj.tree.plugins.dragAndDrop.private.getTargetWrappers($tree, $sourceNode),
	            data = $tree.data();
	        return function (e, mousePosition) {
	            var success = false;
	            $(this).draggable('destroy').remove();
	            $displays.each(function () {
	                var $targetDisplay = $(this), $targetNode, $sourceParentNode, $ul;
	                if ($targetDisplay.droppable('isOver', mousePosition)) {
	                    $targetNode = $targetDisplay.closest('li');
	                    $sourceParentNode = $sourceNode.parent('ul').parent('li');
	                    $ul = $targetNode.children('ul');
	                    if ($ul.length === 0) {
	                        $ul = $('<ul />').addClass(data.style.list);
	                        $targetNode.append($ul);
	                    }
	                    if (gj.tree.plugins.dragAndDrop.events.nodeDrop($tree, $sourceNode.data('id'), $targetNode.data('id'), $ul.children('li').length + 1) !== false) {
	                        $ul.append($sourceNode);
	                        gj.tree.plugins.dragAndDrop.private.refresh($tree, $sourceNode, $targetNode, $sourceParentNode);
	                    }
	                    success = true;
	                    return false;
	                }
	                $targetDisplay.droppable('destroy');
	            });
	            if (!success) {
	                $wrappers.each(function () {
	                    var $targetWrapper = $(this), $targetNode, $sourceParentNode, prepend, orderNumber;
	                    if ($targetWrapper.droppable('isOver', mousePosition)) {
	                        $targetNode = $targetWrapper.closest('li');
	                        $sourceParentNode = $sourceNode.parent('ul').parent('li');
	                        prepend = mousePosition.top < ($targetWrapper.position().top + ($targetWrapper.outerHeight() / 2));
	                        orderNumber = $targetNode.prev('li').length + (prepend ? 1 : 2);
	                        if (gj.tree.plugins.dragAndDrop.events.nodeDrop($tree, $sourceNode.data('id'), $targetNode.parent('ul').parent('li').data('id'), orderNumber) !== false) {
	                            if (prepend) {
	                                $sourceNode.insertBefore($targetNode);
	                            } else {
	                                $sourceNode.insertAfter($targetNode);
	                            }
	                            gj.tree.plugins.dragAndDrop.private.refresh($tree, $sourceNode, $targetNode, $sourceParentNode);
	                        }
	                        return false;
	                    }
	                    $targetWrapper.droppable('destroy');
	                });
                }
	        }
	    },

	    refresh: function ($tree, $sourceNode, $targetNode, $sourceParentNode) {
	        var data = $tree.data();
	        gj.tree.plugins.dragAndDrop.private.refreshNode($tree, $targetNode);
	        gj.tree.plugins.dragAndDrop.private.refreshNode($tree, $sourceParentNode);
	        gj.tree.plugins.dragAndDrop.private.refreshNode($tree, $sourceNode);
	        $sourceNode.find('li[data-role="node"]').each(function () {
	            gj.tree.plugins.dragAndDrop.private.refreshNode($tree, $(this));
	        });
	        $targetNode.children('[data-role="wrapper"]').removeClass(data.style.dropAbove).removeClass(data.style.dropBelow);
        },

	    refreshNode: function ($tree, $node) {
	        var $wrapper = $node.children('[data-role="wrapper"]'),
	            $expander = $wrapper.children('[data-role="expander"]'),
	            $spacer = $wrapper.children('[data-role="spacer"]'),
	            $list = $node.children('ul'),
                data = $tree.data(),
	            level = $node.parentsUntil('[data-type="tree"]', 'ul').length;

	        if ($list.length && $list.children().length) {
	            if ($list.is(':visible')) {
	                $expander.empty().append(data.icons.collapse);
	            } else {
	                $expander.empty().append(data.icons.expand);
	            }
	        } else {
	            $expander.empty();
	        }
	        $wrapper.removeClass(data.style.dropAbove).removeClass(data.style.dropBelow);

	        $spacer.css('width', (data.indentation * (level - 1)));
	    }
	},

	public: {
	},

	events: {
	    /**
         * Event fires when the node is dropped.
         * @event nodeDrop
         * @param {object} e - event data
         * @param {string} id - the id of the record
         * @param {object} parentId - the id of the new parend node
         * @param {object} orderNumber - the new order number
         * @example Event.Sample <!-- materialicons, draggable.base, droppable.base, tree.base -->
         * <div id="tree" data-source="/Locations/Get" data-drag-and-drop="true"></div>
         * <script>
         *     var tree = $('#tree').tree();
         *     tree.on('nodeDrop', function (e, id, parentId, orderNumber) {
         *         var node = tree.getDataById(id),
         *             parent = parentId ? tree.getDataById(parentId) : {};
         *         if (parent.text === 'North America') {
         *             alert('Can\'t add children to North America.');
         *             return false;
         *         } else {
         *             alert(node.text + ' is added to ' + parent.text + ' as ' + orderNumber);
         *             return true;
         *         }
         *     });
         * </script>
         */
	    nodeDrop: function ($tree, id, parentId, orderNumber) {
	        return $tree.triggerHandler('nodeDrop', [id, parentId, orderNumber]);
        }
    },

	configure: function ($tree) {
		$.extend(true, $tree, gj.tree.plugins.dragAndDrop.public);
		if ($tree.data('dragAndDrop') && $.fn.draggable && $.fn.droppable) {
			$tree.on('nodeDataBound', function (e, $node) {
				gj.tree.plugins.dragAndDrop.private.nodeDataBound($tree, $node);
			});
		}
	}
};

/* global window alert jQuery */
/** 
 * @widget Checkbox 
 * @plugin Base
 */
if (typeof (gj.checkbox) === 'undefined') {
    gj.checkbox = {};
}

gj.checkbox.config = {
    base: {
        /** The name of the UI library that is going to be in use. Currently we support only Material Design and Bootstrap. 
         * @additionalinfo The css files for Bootstrap should be manually included to the page if you use bootstrap as uiLibrary.
         * @type string (materialdesign|bootstrap|bootstrap4)
         * @default 'materialdesign'
         * @example Bootstrap.3 <!-- bootstrap, checkbox -->
         * <div class="container-fluid" style="margin-top:10px">
         *     <input type="checkbox" id="checkbox"/><br/><br/>
         *     <button onclick="$chkb.state('checked')" class="btn btn-default">Checked</button>
         *     <button onclick="$chkb.state('unchecked')" class="btn btn-default">Unchecked</button>
         *     <button onclick="$chkb.state('indeterminate')" class="btn btn-default">Indeterminate</button>
         * </div>
         * <script>
         *     var $chkb = $('#checkbox').checkbox({
         *         uiLibrary: 'bootstrap'
         *     });
         * </script>
         * @example Bootstrap.4 <!-- materialicons, bootstrap4, checkbox -->
         * <div class="container-fluid" style="margin-top:10px">
         *     <input type="checkbox" id="checkbox"/><br/><br/>
         *     <button onclick="$chkb.state('checked')" class="btn btn-default">Checked</button>
         *     <button onclick="$chkb.state('unchecked')" class="btn btn-default">Unchecked</button>
         *     <button onclick="$chkb.state('indeterminate')" class="btn btn-default">Indeterminate</button>
         * </div>
         * <script>
         *     var $chkb = $('#checkbox').checkbox({
         *         uiLibrary: 'bootstrap4'
         *     });
         * </script>
         * @example Material.Design <!-- materialicons, checkbox  -->
         * <input type="checkbox" id="checkbox"/><br/><br/>
         * <button onclick="$chkb.state('checked')" class="gj-button-md">Checked</button>
         * <button onclick="$chkb.state('unchecked')" class="gj-button-md">Unchecked</button>
         * <button onclick="$chkb.state('indeterminate')" class="gj-button-md">Indeterminate</button>
         * <button onclick="$chkb.prop('disabled', false)" class="gj-button-md">Enable</button>
         * <button onclick="$chkb.prop('disabled', true)" class="gj-button-md">Disable</button>
         * <script>
         *     var $chkb = $('#checkbox').checkbox({
         *         uiLibrary: 'materialdesign'
         *     });
         * </script>
         */
        uiLibrary: 'materialdesign',

        iconsLibrary: 'materialicons',

        style: {
            wrapperCssClass: 'gj-checkbox-md',
            spanCssClass: undefined
        }
        
    },

    bootstrap: {
        style: {
            wrapperCssClass: 'gj-checkbox-bootstrap'
        },
        iconsLibrary: 'glyphicons'
    },

    bootstrap4: {
        style: {
            wrapperCssClass: 'gj-checkbox-bootstrap'
        },
        iconsLibrary: 'materialicons'
    },

    materialicons: {
        style: {
            iconsCssClass: 'gj-checkbox-material-icons',
            spanCssClass: 'material-icons'
        }
    },

    glyphicons: {
        style: {
            iconsCssClass: 'gj-checkbox-glyphicons',
            spanCssClass: ''
        }
    }
};

gj.checkbox.methods = {
    init: function (jsConfig) {
        var $chkb = this;

        gj.widget.prototype.init.call(this, jsConfig, 'checkbox');
        $chkb.attr('data-checkbox', 'true');

        gj.checkbox.methods.initialize($chkb);

        return $chkb;
    },

    initialize: function ($chkb) {
        var data = $chkb.data(), $wrapper, $span;

        if (data.style.wrapperCssClass) {
            $wrapper = $('<label class="' + data.style.wrapperCssClass + ' ' + data.style.iconsCssClass + '"></label>');
            if ($chkb.attr('id')) {
                $wrapper.attr('for', $chkb.attr('id'));
            }
            $chkb.wrap($wrapper);
            $span = $('<span />');
            if (data.style.spanCssClass) {
                $span.addClass(data.style.spanCssClass);
            }
            $chkb.parent().append($span);
        }
    },

    state: function ($chkb, value) {
        if (value) {
            if ('checked' === value) {
                $chkb.prop('indeterminate', false);
                $chkb.prop('checked', true);
            } else if ('unchecked' === value) {
                $chkb.prop('indeterminate', false);
                $chkb.prop('checked', false);
            } else if ('indeterminate' === value) {
                $chkb.prop('checked', true);
                $chkb.prop('indeterminate', true);
            }
            gj.checkbox.events.change($chkb, value);
            return $chkb;
        } else {
            if ($chkb.prop('indeterminate')) {
                value = 'indeterminate';
            } else if ($chkb.prop('checked')) {
                value = 'checked';
            } else {
                value = 'unchecked';
            }
            return value;
        }
    },

    toggle: function ($chkb) {
        if ($chkb.state() == 'checked') {
            $chkb.state('unchecked');
        } else {
            $chkb.state('checked');
        }
        return $chkb;
    },

    destroy: function ($chkb) {
        if ($chkb.attr('data-checkbox') === 'true') {
            $chkb.removeData();
            $chkb.removeAttr('data-guid');
            $chkb.removeAttr('data-checkbox');
            $chkb.off();
            $chkb.next('span').remove();
            $chkb.unwrap();
        }
        return $chkb;
    }
};

gj.checkbox.events = {
    /**
     * Triggered when the state of the checkbox is changed
     *
     * @event change
     * @param {object} e - event data
     * @example sample <!-- materialicons, checkbox -->
     * <input type="checkbox" id="checkbox"/>
     * <script>
     *     var chkb = $('#checkbox').checkbox({
     *         change: function (e) {
     *             alert('State: ' + chkb.state());
     *         }
     *     });
     * </script>
     */
    change: function ($chkb, state) {
        return $chkb.triggerHandler('change', [state]);
    }
};


gj.checkbox.widget = function ($element, jsConfig) {
    var self = this,
        methods = gj.checkbox.methods;

    /** Toogle the state of the checkbox.
     * @method
     * @fires change
     * @return checked|unchecked|indeterminate|jquery
     * @example sample <!-- materialicons, checkbox -->
     * <button onclick="$chkb.toggle()">toggle</button>
     * <hr/>
     * <input type="checkbox" id="checkbox"/>
     * <script>
     *     var $chkb = $('#checkbox').checkbox();
     * </script>
     */
    self.toggle = function () {
        return methods.toggle(this);
    };

    /** Return state or set state if you pass parameter.
     * @method
     * @fires change
     * @param {string} value - State of the checkbox. Accept only checked, unchecked or indeterminate as values.
     * @return checked|unchecked|indeterminate|jquery
     * @example sample <!-- materialicons, checkbox -->
     * <button onclick="$chkb.state('checked')">Set to checked</button>
     * <button onclick="$chkb.state('unchecked')">Set to unchecked</button>
     * <button onclick="$chkb.state('indeterminate')">Set to indeterminate</button>
     * <button onclick="alert($chkb.state())">Get state</button>
     * <hr/>
     * <input type="checkbox" id="checkbox"/>
     * <script>
     *     var $chkb = $('#checkbox').checkbox();
     * </script>
     */
    self.state = function (value) {
        return methods.state(this, value);
    };

    /** Remove checkbox functionality from the element.
     * @method
     * @return jquery element
     * @example sample <!-- materialicons, checkbox -->
     * <button onclick="$chkb.destroy()">Destroy</button>
     * <input type="checkbox" id="checkbox"/>
     * <script>
     *     var $chkb = $('#checkbox').checkbox();
     * </script>
     */
    self.destroy = function () {
        return methods.destroy(this);
    };

    $.extend($element, self);
    if ('true' !== $element.attr('data-checkbox')) {
        methods.init.call($element, jsConfig);
    }

    return $element;
};

gj.checkbox.widget.prototype = new gj.widget();
gj.checkbox.widget.constructor = gj.checkbox.widget;

(function ($) {
    $.fn.checkbox = function (method) {
        var $widget;
        if (this && this.length) {
            if (typeof method === 'object' || !method) {
                return new gj.checkbox.widget(this, method);
            } else {
                $widget = new gj.checkbox.widget(this, null);
                if ($widget[method]) {
                    return $widget[method].apply(this, Array.prototype.slice.call(arguments, 1));
                } else {
                    throw 'Method ' + method + ' does not exist.';
                }
            }
        }
    };
})(jQuery);
if (typeof (gj.editor) === 'undefined') {
    gj.editor = {
        plugins: {},
        messages: []
    };
}

gj.editor.messages['en-us'] = {
    bold: 'Bold',
    italic: 'Italic',
    strikethrough: 'Strikethrough',
    underline: 'Underline',
    listBulleted: 'List Bulleted',
    listNumbered: 'List Numbered',
    indentDecrease: 'Indent Decrease',
    indentIncrease: 'Indent Increase',
    alignLeft: 'Align Left',
    alignCenter: 'Align Center',
    alignRight: 'Align Right',
    alignJustify: 'Align Justify',
    undo: 'Undo',
    redo: 'Redo'
};
/* global window alert jQuery */
/** 
 * @widget Editor 
 * @plugin Base
 */
gj.editor.config = {
    base: {

        /** The height of the editor. Numeric values are treated as pixels.
         * @type number|string
         * @default 300
         * @example sample <!-- editor, materialicons -->
         * <div id="editor"></div>
         * <script>
         *     $('#editor').editor({
         *         height: 500
         *     });
         * </script>
         */
        height: 300,

        /** The width of the editor. Numeric values are treated as pixels.
         * @type number|string
         * @default undefined
         * @example sample <!-- editor, materialicons -->
         * <div id="editor"></div>
         * <script>
         *     $('#editor').editor({
         *         width: 900
         *     });
         * </script>
         */
        width: undefined,

        /** The name of the UI library that is going to be in use. Currently we support only Material Design and Bootstrap. 
         * @additionalinfo The css files for Bootstrap should be manually included to the page if you use bootstrap as uiLibrary.
         * @type string (materialdesign|bootstrap|bootstrap4)
         * @default 'materialdesign'
         * @example Material.Design <!-- editor, materialicons  -->
         * <div id="editor"></div>
         * <script>
         *     $('#editor').editor({ uiLibrary: 'materialdesign' });
         * </script>
         * @example Bootstrap.3 <!-- fontawesome, bootstrap, editor -->
         * <div class="container"><div id="editor"></div></div>
         * <script>
         *     $('#editor').editor({
         *         uiLibrary: 'bootstrap'
         *     });
         * </script>
         * @example Bootstrap.4 <!-- fontawesome, bootstrap4, editor -->
         * <div class="container"><div id="editor"></div></div>
         * <script>
         *     $('#editor').editor({
         *         uiLibrary: 'bootstrap4'
         *     });
         * </script>
         */
        uiLibrary: 'materialdesign',

        /** The name of the icons library that is going to be in use. Currently we support Material Icons and Font Awesome.
         * @additionalinfo If you use Bootstrap as uiLibrary, then the iconsLibrary is set to font awesome by default.<br/>
         * If you use Material Design as uiLibrary, then the iconsLibrary is set to Material Icons by default.<br/>
         * The css files for Material Icons or Font Awesome should be manually included to the page where the grid is in use.
         * @type (materialicons|fontawesome)
         * @default 'materialicons'
         * @example Base.Theme.Material.Icons <!-- materialicons, bootstrap, editor -->
         * <div id="editor"></div>
         * <script>
         *     $('#editor').editor({
         *         uiLibrary: 'bootstrap',
         *         iconsLibrary: 'materialicons'
         *     });
         * </script>
         */
        iconsLibrary: 'materialicons',

        /** The language that needs to be in use.
         * @type string
         * @default 'en-us'
         * @example French <!-- materialicons, editor -->
         * <script src="../../dist/modular/editor/js/messages/messages.fr-fr.js"></script>
         * <div id="editor">Hover buttons in the toolbar in order to see localized tooltips</div>
         * <script>
         *     $("#editor").editor({
         *         locale: 'fr-fr'
         *     });
         * </script>
         * @example German <!-- materialicons, editor -->
         * <script src="../../dist/modular/editor/js/messages/messages.de-de.js"></script>
         * <div id="editor">Hover <b><u>buttons</u></b> in the toolbar in order to see localized tooltips</div>
         * <script>
         *     $("#editor").editor({
         *         locale: 'de-de'
         *     });
         * </script>
         */
        locale: 'en-us',

        buttons: undefined,

        style: {
            wrapper: 'gj-editor-md',
            buttonsGroup: 'gj-button-md-group',
            button: 'gj-button-md',
            buttonActive: 'active'
        }
    },

    bootstrap: {
        style: {
            wrapper: 'gj-editor-bootstrap',
            buttonsGroup: 'btn-group',
            button: 'btn btn-default gj-cursor-pointer',
            buttonActive: 'active'
        },
        iconsLibrary: 'fontawesome'
    },

    bootstrap4: {
        style: {
            wrapper: 'gj-editor-bootstrap',
            buttonsGroup: 'btn-group',
            button: 'btn btn-secondary gj-cursor-pointer',
            buttonActive: 'active'
        },
        iconsLibrary: 'fontawesome'
    },

    materialicons: {
        icons: {
            bold: '<i class="material-icons">format_bold</i>',
            italic: '<i class="material-icons">format_italic</i>',
            strikethrough: '<i class="material-icons">strikethrough_s</i>',
            underline: '<i class="material-icons">format_underlined</i>',

            listBulleted: '<i class="material-icons">format_list_bulleted</i>',
            listNumbered: '<i class="material-icons">format_list_numbered</i>',
            indentDecrease: '<i class="material-icons">format_indent_decrease</i>',
            indentIncrease: '<i class="material-icons">format_indent_increase</i>',

            alignLeft: '<i class="material-icons">format_align_left</i>',
            alignCenter: '<i class="material-icons">format_align_center</i>',
            alignRight: '<i class="material-icons">format_align_right</i>',
            alignJustify: '<i class="material-icons">format_align_justify</i>',

            undo: '<i class="material-icons">undo</i>',
            redo: '<i class="material-icons">redo</i>'
        }
    },

    fontawesome: {
        icons: {
            bold: '<i class="fa fa-bold" aria-hidden="true"></i>',
            italic: '<i class="fa fa-italic" aria-hidden="true"></i>',
            strikethrough: '<i class="fa fa-strikethrough" aria-hidden="true"></i>',
            underline: '<i class="fa fa-underline" aria-hidden="true"></i>',

            listBulleted: '<i class="fa fa-list-ul" aria-hidden="true"></i>',
            listNumbered: '<i class="fa fa-list-ol" aria-hidden="true"></i>',
            indentDecrease: '<i class="fa fa-indent" aria-hidden="true"></i>',
            indentIncrease: '<i class="fa fa-outdent" aria-hidden="true"></i>',

            alignLeft: '<i class="fa fa-align-left" aria-hidden="true"></i>',
            alignCenter: '<i class="fa fa-align-center" aria-hidden="true"></i>',
            alignRight: '<i class="fa fa-align-right" aria-hidden="true"></i>',
            alignJustify: '<i class="fa fa-align-justify" aria-hidden="true"></i>',

            undo: '<i class="fa fa-undo" aria-hidden="true"></i>',
            redo: '<i class="fa fa-repeat" aria-hidden="true"></i>'
        }
    }
};

gj.editor.methods = {
    init: function (jsConfig) {
        gj.widget.prototype.init.call(this, jsConfig, 'editor');
        this.attr('data-editor', 'true');
        gj.editor.methods.initialize(this);
        return this;
    },

    initialize: function ($editor) {
        var self = this, data = $editor.data(), $group, $btn,
            $body = $editor.children('div[data-role="body"]'),
            $toolbar = $editor.children('div[data-role="toolbar"]');

        gj.editor.methods.localization(data);

        $editor.addClass(data.style.wrapper);
        if (data.width) {
            $editor.width(data.width);
        }

        if ($body.length === 0) {
            $editor.wrapInner('<div data-role="body"></div>');
            $body = $editor.children('div[data-role="body"]');
        }

        $body.attr('contenteditable', true);

        $body.on('mouseup keyup mouseout', function () {
            self.updateToolbar($editor, $toolbar);
        });

        if ($toolbar.length === 0) {
            $toolbar = $('<div data-role="toolbar"></div>');
            $body.before($toolbar);

            for (var group in data.buttons) {
                $group = $('<div />').addClass(data.style.buttonsGroup);
                for (var btn in data.buttons[group]) {
                    $btn = $(data.buttons[group][btn]);
                    $btn.on('click', function () {
                        gj.editor.methods.executeCmd($editor, $body, $toolbar, $(this));
                    });
                    $group.append($btn);
                }
                $toolbar.append($group);
            }
        }

        $body.height(data.height - $toolbar.outerHeight());
    },

    localization: function (data) {
        var msg = gj.editor.messages[data.locale];
        if (typeof (data.buttons) === 'undefined') {
            data.buttons = [
                [
                    '<button type="button" class="' + data.style.button + '" title="' + msg.bold + '" data-role="bold">' + data.icons.bold + '</button>',
                    '<button type="button" class="' + data.style.button + '" title="' + msg.italic + '" data-role="italic">' + data.icons.italic + '</button>',
                    '<button type="button" class="' + data.style.button + '" title="' + msg.strikethrough + '" data-role="strikethrough">' + data.icons.strikethrough + '</button>',
                    '<button type="button" class="' + data.style.button + '" title="' + msg.underline + '" data-role="underline">' + data.icons.underline + '</button>'
                ],
                [
                    '<button type="button" class="' + data.style.button + '" title="' + msg.listBulleted + '" data-role="insertunorderedlist">' + data.icons.listBulleted + '</button>',
                    '<button type="button" class="' + data.style.button + '" title="' + msg.listNumbered + '" data-role="insertorderedlist">' + data.icons.listNumbered + '</button>',
                    '<button type="button" class="' + data.style.button + '" title="' + msg.indentDecrease + '" data-role="outdent">' + data.icons.indentDecrease + '</button>',
                    '<button type="button" class="' + data.style.button + '" title="' + msg.indentIncrease + '" data-role="indent">' + data.icons.indentIncrease + '</button>'
                ],
                [
                    '<button type="button" class="' + data.style.button + '" title="' + msg.alignLeft + '" data-role="justifyleft">' + data.icons.alignLeft + '</button>',
                    '<button type="button" class="' + data.style.button + '" title="' + msg.alignCenter + '" data-role="justifycenter">' + data.icons.alignCenter + '</button>',
                    '<button type="button" class="' + data.style.button + '" title="' + msg.alignRight + '" data-role="justifyright">' + data.icons.alignRight + '</button>',
                    '<button type="button" class="' + data.style.button + '" title="' + msg.alignJustify + '" data-role="justifyfull">' + data.icons.alignJustify + '</button>'
                ],
                [
                    '<button type="button" class="' + data.style.button + '" title="' + msg.undo + '" data-role="undo">' + data.icons.undo + '</button>',
                    '<button type="button" class="' + data.style.button + '" title="' + msg.redo + '" data-role="redo">' + data.icons.redo + '</button>'
                ]
            ];
        }
    },

    updateToolbar: function ($editor, $toolbar) {
        var data = $editor.data();
        $buttons = $toolbar.find('[data-role]').each(function() {
            var $btn = $(this),
                cmd = $btn.attr('data-role');

            if (cmd && document.queryCommandEnabled(cmd) && document.queryCommandValue(cmd) === "true") {
                $btn.addClass(data.style.buttonActive);
            } else {
                $btn.removeClass(data.style.buttonActive);
            }
        });
        gj.editor.events.change($editor);
    },

    executeCmd: function ($editor, $body, $toolbar, $btn) {
        $body.focus();
        document.execCommand($btn.attr('data-role'), false);
        gj.editor.methods.updateToolbar($editor, $toolbar);
    },

    content: function ($editor, html) {
        var $body = $editor.children('div[data-role="body"]');
        if (typeof (html) === "undefined") {
            return $body.html();
        } else {
            return $body.html(html);
        }
    },

    destroy: function ($editor) {
        if ($editor.attr('data-editor') === 'true') {
            $editor.removeClass($editor.data().style.wrapper);
            $editor.removeData();
            $editor.removeAttr('data-guid');
            $editor.removeAttr('data-editor');
            $editor.off();
            $editor.empty();
        }
        return $editor;
    }
};

gj.editor.events = {
    /**
     * Triggered when the editor text is changed.
     *
     * @event change
     * @param {object} e - event data
     * @example sample <!-- editor, materialicons -->
     * <div id="editor"></div>
     * <script>
     *     $('#editor').editor({
     *         change: function (e) {
     *             alert('Change is fired');
     *         }
     *     });
     * </script>
     */
    change: function ($editor) {
        return $editor.triggerHandler('change');
    }
};

gj.editor.widget = function ($element, jsConfig) {
    var self = this,
        methods = gj.editor.methods;

    /** Get or set html content in the body.
     * @method
     * @param {string} html - The html content that needs to be set.
     * @return string
     * @example Get <!-- editor, materialicons -->
     * <button class="gj-button-md" onclick="alert($editor.content())">Get Content</button>
     * <hr/>
     * <div id="editor">My <b>content</b>.</div>
     * <script>
     *     var $editor = $('#editor').editor();
     * </script>
     * @example Set <!-- editor, materialicons -->
     * <button class="gj-button-md" onclick="$editor.content('<h1>new value</h1>')">Set Content</button>
     * <hr/>
     * <div id="editor"></div>
     * <script>
     *     var $editor = $('#editor').editor();
     * </script>
     */
    self.content = function (html) {
        return methods.content(this, html);
    };

    /** Remove editor functionality from the element.
     * @method
     * @return jquery element
     * @example sample <!-- editor, materialicons -->
     * <button class="gj-button-md" onclick="editor.destroy()">Destroy</button>
     * <div id="editor"></div>
     * <script>
     *     var editor = $('#editor').editor();
     * </script>
     */
    self.destroy = function () {
        return methods.destroy(this);
    };

    $.extend($element, self);
    if ('true' !== $element.attr('data-editor')) {
        methods.init.call($element, jsConfig);
    }

    return $element;
};

gj.editor.widget.prototype = new gj.widget();
gj.editor.widget.constructor = gj.editor.widget;

(function ($) {
    $.fn.editor = function (method) {
        var $widget;
        if (this && this.length) {
            if (typeof method === 'object' || !method) {
                return new gj.editor.widget(this, method);
            } else {
                $widget = new gj.editor.widget(this, null);
                if ($widget[method]) {
                    return $widget[method].apply(this, Array.prototype.slice.call(arguments, 1));
                } else {
                    throw 'Method ' + method + ' does not exist.';
                }
            }
        }
    };
})(jQuery);
/* global window alert jQuery gj */
/**
  * @widget DropDown
  * @plugin Base
  */
if (typeof (gj.dropdown) === 'undefined') {
    gj.dropdown = {
        plugins: {}
    };
}

gj.dropdown.config = {
    base: {

        /** The data source of dropdown.
         * @additionalinfo If set to string, then the dropdown is going to use this string as a url for ajax requests to the server.<br />
         * If set to object, then the dropdown is going to use this object as settings for the <a href="http://api.jquery.com/jquery.ajax/" target="_new">jquery ajax</a> function.<br />
         * If set to array, then the dropdown is going to use the array as data for dropdown nodes.
         * @type (string|object|array)
         * @default undefined
         * @example Local.DataSource <!-- materialicons, dropdown -->
         * <select id="dropdown"></select>
         * <script>
         *     $('#dropdown').dropdown({
         *         dataSource: [ { value: 1, text: 'One' }, { value: 2, text: 'Two' }, { value: 3, text: 'Three' } ]
         *     });
         * </script>
         * @example Remote.DataSource <!-- materialicons, dropdown -->
         * <select id="dropdown"></select>
         * <script>
         *     $('#dropdown').dropdown({
         *         dataSource: '/Locations/Get',
         *         valueField: 'id'
         *     });
         * </script>
         */
        dataSource: undefined,

        /** Text field name.
         * @type string
         * @default 'text'
         * @example sample <!-- materialicons, dropdown -->
         * <select id="dropdown"></select>
         * <script>
         *     $('#dropdown').dropdown({
         *         textField: 'newTextField',
         *         dataSource: [ { value: 1, newTextField: 'One' }, { value: 2, newTextField: 'Two' }, { value: 3, newTextField: 'Three' } ]
         *     });
         * </script>
         */
        textField: 'text',

        /** Value field name.
         * @type string
         * @default 'text'
         * @example sample <!-- materialicons, dropdown -->
         * <select id="dropdown"></select>
         * <script>
         *     $('#dropdown').dropdown({
         *         valueField: 'newValueField',
         *         dataSource: [ { newValueField: 1, text: 'One' }, { newValueField: 2, text: 'Two' }, { newValueField: 3, text: 'Three' } ]
         *     });
         * </script>
         */
        valueField: 'value',

        /** Selected field name.
         * @type string
         * @default 'text'
         * @example sample <!-- materialicons, dropdown -->
         * <select id="dropdown"></select>
         * <script>
         *     $('#dropdown').dropdown({
         *         selectedField: 'newSelectedField',
         *         dataSource: [ { value: 1, text: 'One' }, { value: 2, text: 'Two', newSelectedField: true }, { value: 3, text: 'Three' } ]
         *     });
         * </script>
         */
        selectedField: 'selected',

        optionsDisplay: 'materialdesign',

        fontSize: undefined,

        /** The name of the UI library that is going to be in use.
         * @additionalinfo The css file for bootstrap should be manually included if you use bootstrap.
         * @type (materialdesign|bootstrap|bootstrap4)
         * @default materialdesign
         * @example MaterialDesign <!-- materialicons, dropdown -->
         * <select id="dropdown">
         *     <option value="1">One</option>
         *     <option value="2">Two</option>
         *     <option value="3">Three</option>
         * </select>
         * <script>
         *     var dropdown = $('#dropdown').dropdown({
         *         uiLibrary: 'materialdesign'
         *     });
         * </script>
         * @example Bootstrap.3 <!-- bootstrap, dropdown -->
         * <select id="dropdown">
         *     <option value="1">One</option>
         *     <option value="2">Two</option>
         *     <option value="3">Three</option>
         * </select>
         * <script>
         *     $('#dropdown').dropdown({ uiLibrary: 'bootstrap' });
         * </script>
         * @example Bootstrap.4 <!-- materialicons, bootstrap4, dropdown -->
         * <select id="dropdown">
         *     <option value="1">One</option>
         *     <option value="2">Two</option>
         *     <option value="3">Three</option>
         * </select>
         * <script>
         *     $('#dropdown').dropdown({ uiLibrary: 'bootstrap4', width: 300 });
         * </script>
         */
        uiLibrary: 'materialdesign',

        /** The name of the icons library that is going to be in use. Currently we support Material Icons, Font Awesome and Glyphicons.
         * @additionalinfo If you use Bootstrap 3 as uiLibrary, then the iconsLibrary is set to Glyphicons by default.<br/>
         * If you use Material Design as uiLibrary, then the iconsLibrary is set to Material Icons by default.<br/>
         * The css files for Material Icons, Font Awesome or Glyphicons should be manually included to the page where the grid is in use.
         * @type (materialicons|fontawesome|glyphicons)
         * @default 'materialicons'
         * @example Bootstrap.Material.Icons <!-- bootstrap, materialicons, dropdown -->
         * <select id="dropdown"></select>
         * <script>
         *     var dropdown = $('#dropdown').dropdown({
         *         dataSource: '/Locations/Get',
         *         uiLibrary: 'bootstrap',
         *         iconsLibrary: 'materialicons',
         *         valueField: 'id'
         *     });
         * </script>
         * @example Bootstrap.4.Font.Awesome <!-- bootstrap4, fontawesome, dropdown -->
         * <select id="dropdown"></select>
         * <script>
         *     var dropdown = $('#dropdown').dropdown({
         *         dataSource: '/Locations/Get',
         *         uiLibrary: 'bootstrap4',
         *         iconsLibrary: 'fontawesome',
         *         valueField: 'id'
         *     });
         * </script>
         */
        iconsLibrary: 'materialicons',

        icons: {
            /** DropDown icon definition.
             * @alias icons.dropdown
             * @type String
             * @default '<i class="material-icons">arrow_drop_down</i>'
             * @example Custom.Material.Icon <!-- materialicons, dropdown -->
             * <select id="dropdown"></select>
             * <script>
             *     var dropdown = $('#dropdown').dropdown({
             *         dataSource: '/Locations/Get',
             *         valueField: 'id',
             *         icons: { 
             *             dropdown: '<i class="material-icons">keyboard_arrow_down</i>'
             *         }
             *     });
             * </script>
             * @example Custom.Glyphicon.Icon <!-- bootstrap, dropdown -->
             * <select id="dropdown"></select>
             * <script>
             *     var dropdown = $('#dropdown').dropdown({
             *         dataSource: '/Locations/Get',
             *         valueField: 'id',
             *         uiLibrary: 'bootstrap',
             *         icons: { 
             *             dropdown: '<span class="glyphicon glyphicon-triangle-bottom" />'
             *         }
             *     });
             * </script>
             */
            dropdown: '<i class="material-icons">arrow_drop_down</i>'
        },

        style: {
            wrapper: 'gj-dropdown gj-dropdown-md gj-unselectable',
            list: 'gj-list gj-list-md gj-dropdown-list-md',
            active: 'gj-list-md-active'
        }
    },

    bootstrap: {
        style: {
            wrapper: 'gj-dropdown gj-dropdown-bootstrap gj-dropdown-bootstrap-3 gj-unselectable',
            presenter: 'btn btn-default',
            list: 'gj-list gj-list-bootstrap gj-dropdown-list-bootstrap list-group',
            item: 'list-group-item',
            active: 'active'
        },
        iconsLibrary: 'glyphicons',
        optionsDisplay: 'standard'
    },

    bootstrap4: {
        style: {
            wrapper: 'gj-dropdown gj-dropdown-bootstrap gj-dropdown-bootstrap-4 gj-unselectable',
            presenter: 'btn btn-secondary',
            list: 'gj-list gj-list-bootstrap gj-dropdown-list-bootstrap list-group',
            item: 'list-group-item',
            active: 'active'
        },
        optionsDisplay: 'standard'
    },

    materialicons: {
        style: {
            expander: 'gj-dropdown-expander-mi'
        }
    },

    fontawesome: {
        icons: {
            dropdown: '<i class="fa fa-caret-down" aria-hidden="true"></i>'
        },
        style: {
            expander: 'gj-dropdown-expander-fa'
        }
    },

    glyphicons: {
        icons: {
            dropdown: '<span class="caret"></span>'
        },
        style: {
            expander: 'gj-dropdown-expander-glyphicons'
        }
    }
};

gj.dropdown.methods = {
    init: function (jsConfig) {
        gj.widget.prototype.init.call(this, jsConfig, 'dropdown');
        this.attr('data-dropdown', 'true');
        gj.dropdown.methods.initialize(this);
        return this;
    },

    initialize: function ($dropdown) {
        var $item,
            data = $dropdown.data(),
            $wrapper = $dropdown.parent('div[role="wrapper"]'),
            $display = $('<span role="display"></span>'),
            $expander = $('<span role="expander">' + data.icons.dropdown + '</span>').addClass(data.style.expander),
            $presenter = $('<button role="presenter"></button>').addClass(data.style.presenter),
            $list = $('<ul role="list" class="' + data.style.list + '"></ul>').attr('guid', $dropdown.attr('data-guid'));

        if ($wrapper.length === 0) {
            $wrapper = $('<div role="wrapper" />').addClass(data.style.wrapper); // The css class needs to be added before the wrapping, otherwise doesn't work.
            $dropdown.wrap($wrapper);
        } else {
            $wrapper.addClass(data.style.wrapper);
        }

        if (data.fontSize) {
            $presenter.css('font-size', data.fontSize);
        }

        $presenter.on('click', function (e) {
            if ($list.is(':visible')) {
                $list.hide();
            } else {
                gj.dropdown.methods.setListPosition($presenter, $list, data);
                $list.show();
                gj.dropdown.methods.setListPosition($presenter, $list, data);
            }
        });
        $presenter.on('blur', function (e) {
            setTimeout(function () {
                $list.hide();
            }, 100);
        });
        $presenter.append($display).append($expander);

        $dropdown.hide();
        $dropdown.after($presenter);
        $('body').append($list);
        $list.hide();

        $dropdown.reload();
    },

    setListPosition: function ($presenter, $list, data) {
        var offset = $presenter.offset();
        $list.css('left', offset.left).css('width', $presenter.outerWidth(true));
        if (data.optionsDisplay === 'standard') {
            $list.css('top', offset.top + $presenter.outerHeight(true) + 2);
        } else {
            $list.css('top', offset.top);
        }
    },

    useHtmlDataSource: function ($dropdown, data) {
        var dataSource = [], i, $option, record,
            $options = $dropdown.find('option');
        for (i = 0; i < $options.length; i++) {
            $option = $($options[i]);
            record = {};
            record[data.valueField] = $option.val();
            record[data.textField] = $option.html();
            record[data.selectedField] = $option.prop('selected');
            dataSource.push(record);
        }
        data.dataSource = dataSource;
    },

    filter: function ($dropdown) {
        var i, record, data = $dropdown.data();
        if (!data.dataSource)
        {
            data.dataSource = [];
        } else if (typeof data.dataSource[0] === 'string') {
            for (i = 0; i < data.dataSource.length; i++) {
                record = {};
                record[data.valueField] = data.dataSource[i];
                record[data.textField] = data.dataSource[i];
                data.dataSource[i] = record;
            }
        }
        return data.dataSource;
    },

    render: function ($dropdown, response) {
        var width,
            selectedInd = false,
            data = $dropdown.data(),
            $parent = $dropdown.parent(),
            $list = $('body').children('[role="list"][guid="' + $dropdown.attr('data-guid') + '"]'),
            $presenter = $parent.children('[role="presenter"]'),
            $expander = $parent.find('[role="expander"]');

        $dropdown.data('records', response);
        $dropdown.empty();
        $list.empty();

        if (response && response.length) {
            $.each(response, function () {
                var value = this[data.valueField],
                    text = this[data.textField],
                    selected = this[data.selectedField] && this[data.selectedField].toString().toLowerCase() === 'true',
                    $item, $option;

                $item = $('<li value="' + value + '"><div data-role="wrapper"><span data-role="display">' + text + '</span></div></li>');
                $item.addClass(data.style.item);
                $item.on('click', function (e) {
                    gj.dropdown.methods.select($dropdown, value);
                });
                $list.append($item);

                $option = $('<option value="' + value + '">' + text + '</option>');
                $dropdown.append($option);

                if (selected) {
                    gj.dropdown.methods.select($dropdown, value);
                    selectedInd = true;
                }
            });
            if (selectedInd === false) {
                gj.dropdown.methods.select($dropdown, response[0][data.valueField]);
            }
        }

        width = data.width ? data.width : ($list.width() + $expander.outerWidth() + 10);
        $parent.css('width', width);
        $list.css('width', width);

        if (data.fontSize) {
            $list.children('li').css('font-size', data.fontSize);
        }

        gj.dropdown.events.dataBound($dropdown);

        return $dropdown;
    },

    select: function ($dropdown, value) {
        var data = $dropdown.data(),
            $list = $('body').children('[role="list"][guid="' + $dropdown.attr('data-guid') + '"]'),
            $item = $list.children('li[value="' + value + '"]'),
            record = gj.dropdown.methods.getRecordByValue($dropdown, value);
        $list.children('li').removeClass(data.style.active);
        $item.addClass(data.style.active);
        $dropdown.val(value);
        $dropdown.next('[role="presenter"]').find('[role="display"]').html(record[data.textField]);
        gj.dropdown.events.change($dropdown);
        $list.hide();
        return $dropdown;
    },

    getRecordByValue: function ($dropdown, value) {
        var data = $dropdown.data(),
            i, result = undefined;

        for (i = 0; i < data.records.length; i++) {
            if (data.records[i][data.valueField] === value) {
                result = data.records[i];
                break;
            }
        }

        return result;
    },

    value: function ($dropdown, value) {
        if (typeof (value) === "undefined") {
            return $dropdown.val();
        } else {
            return gj.dropdown.methods.select($dropdown, value);
        }
    },

    destroy: function ($dropdown) {
        var data = $dropdown.data(),
            $parent = $dropdown.parent('div[role="wrapper"]');
        if (data) {
            $dropdown.xhr && $dropdown.xhr.abort();
            $dropdown.off();
            $dropdown.removeData();
            $dropdown.removeAttr('data-type').removeAttr('data-guid').removeAttr('data-dropdown');
            $dropdown.removeClass();
            if ($parent.length > 0) {
                $parent.children('[role="presenter"]').remove();
                $parent.children('[role="list"]').remove();
                $dropdown.unwrap();
            }
            $dropdown.show();
        }
        return $tree;
    }
};

gj.dropdown.events = {
    /**
     * Triggered when the dropdown value is changed.
     *
     * @event change
     * @param {object} e - event data
     * @example sample <!-- dropdown, materialicons -->
     * <select id="dropdown">
     *     <option value="1">One</option>
     *     <option value="2" selected>Two</option>
     *     <option value="3">Three</option>
     * </select>
     * <script>
     *     $('#dropdown').dropdown({
     *         change: function (e) {
     *             alert('Change is fired');
     *         }
     *     });
     * </script>
     */
    change: function ($dropdown) {
        return $dropdown.triggerHandler('change');
    },

    /**
     * Event fires after the loading of the data in the dropdown.
     * @event dataBound
     * @param {object} e - event data
     * @example sample <!-- dropdown, materialicons -->
     * <select id="dropdown">
     *     <option value="1">One</option>
     *     <option value="2" selected>Two</option>
     *     <option value="3">Three</option>
     * </select>
     * <script>
     *     $('#dropdown').dropdown({
     *         dataBound: function (e) {
     *             alert('dataBound is fired.');
     *         }
     *     });
     * </script>
     */
    dataBound: function ($dropdown) {
        return $dropdown.triggerHandler('dataBound');
    }
};

gj.dropdown.widget = function ($element, jsConfig) {
    var self = this,
        methods = gj.dropdown.methods;

    /** Gets or sets the value of the DropDown.
     * @method
     * @param {string} value - The value that needs to be selected.
     * @return string
     * @example Get <!-- dropdown, materialicons -->
     * <button class="gj-button-md" onclick="alert($dropdown.value())">Get Content</button>
     * <hr/>
     * <select id="dropdown">
     *     <option value="1">One</option>
     *     <option value="2" selected>Two</option>
     *     <option value="3">Three</option>
     * </select>
     * <script>
     *     var $dropdown = $('#dropdown').dropdown();
     * </script>
     * @example Set <!-- dropdown, materialicons -->
     * <button class="gj-button-md" onclick="$dropdown.value('3')">Set Value</button>
     * <hr/>
     * <select id="dropdown">
     *     <option value="1">One</option>
     *     <option value="2" selected>Two</option>
     *     <option value="3">Three</option>
     * </select>
     * <script>
     *     var $dropdown = $('#dropdown').dropdown();
     * </script>
     */
    self.value = function (value) {
        return methods.value(this, value);
    };

    self.enable = function () {
        return methods.enable(this);
    };

    self.disable = function () {
        return methods.disable(this);
    };

    /** Remove dropdown functionality from the element.
     * @method
     * @return jquery element
     * @example sample <!-- dropdown, materialicons -->
     * <button class="gj-button-md" onclick="dropdown.destroy()">Destroy</button>
     * <select id="dropdown">
     *     <option value="1">One</option>
     *     <option value="2" selected>Two</option>
     *     <option value="3">Three</option>
     * </select>
     * <script>
     *     var dropdown = $('#dropdown').dropdown();
     * </script>
     */
    self.destroy = function () {
        return methods.destroy(this);
    };

    $.extend($element, self);
    if ('true' !== $element.attr('data-dropdown')) {
        methods.init.call($element, jsConfig);
    }

    return $element;
};

gj.dropdown.widget.prototype = new gj.widget();
gj.dropdown.widget.constructor = gj.dropdown.widget;

(function ($) {
    $.fn.dropdown = function (method) {
        var $widget;
        if (this && this.length) {
            if (typeof method === 'object' || !method) {
                return new gj.dropdown.widget(this, method);
            } else {
                $widget = new gj.dropdown.widget(this, null);
                if ($widget[method]) {
                    return $widget[method].apply(this, Array.prototype.slice.call(arguments, 1));
                } else {
                    throw 'Method ' + method + ' does not exist.';
                }
            }
        }
    };
})(jQuery);
/* global window alert jQuery gj */
/**
  * @widget DatePicker
  * @plugin Base
  */
if (typeof (gj.datepicker) === 'undefined') {
    gj.datepicker = {
        plugins: {}
    };
}

gj.datepicker.config = {
    base: {
        months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],

        weekDays: ['S', 'M', 'T', 'W', 'T', 'F', 'S'],

        /** Whether to display dates in other months at the start or end of the current month.
         * @additionalinfo Set to true by default for Bootstrap.
         * @type Boolean
         * @default false
         * @example True <!-- materialicons, datepicker -->
         * <input id="datepicker" width="312" />
         * <script>
         *    var datepicker = $('#datepicker').datepicker({ 
         *        showOtherMonths: true
         *    });
         * </script>
         * @example False <!-- materialicons, datepicker -->
         * <input id="datepicker" width="312" />
         * <script>
         *     $('#datepicker').datepicker({ 
         *         showOtherMonths: false
         *     });
         * </script>
         */
        showOtherMonths: false,

        /** Whether days in other months shown before or after the current month are selectable.
         * This only applies if the showOtherMonths option is set to true.
         * @type Boolean
         * @default true
         * @example True <!-- materialicons, datepicker -->
         * <input id="datepicker" width="312" />
         * <script>
         *    var datepicker = $('#datepicker').datepicker({
         *        showOtherMonths: true,
         *        selectOtherMonths: true
         *    });
         * </script>
         * @example False <!-- materialicons, datepicker -->
         * <input id="datepicker" width="312" />
         * <script>
         *     $('#datepicker').datepicker({ 
         *        showOtherMonths: true,
         *        selectOtherMonths: false
         *     });
         * </script>
         */
        selectOtherMonths: true,

        /** The minimum selectable date. When not set, there is no minimum
         * @type Date|String|Function
         * @default undefined
         * @example Today <!-- materialicons, datepicker -->
         * <input id="datepicker" width="312" />
         * <script>
         *    var today, datepicker;
         *    today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
         *    datepicker = $('#datepicker').datepicker({
         *        minDate: today
         *    });
         * </script>
         * @example Yesterday <!-- materialicons, datepicker -->
         * <input id="datepicker" width="312" />
         * <script>
         *     $('#datepicker').datepicker({ 
         *        minDate: function() {
         *            var date = new Date();
         *            date.setDate(date.getDate()-1);
         *            return new Date(date.getFullYear(), date.getMonth(), date.getDate());
         *        }
         *     });
         * </script>
         */
        minDate: undefined,

        /** The maximum selectable date. When not set, there is no maximum
         * @type Date|String|Function
         * @default undefined
         * @example Today <!-- materialicons, datepicker -->
         * <input id="datepicker" width="312" />
         * <script>
         *    var today, datepicker;
         *    today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
         *    datepicker = $('#datepicker').datepicker({
         *        maxDate: today
         *    });
         * </script>
         * @example Tomorrow <!-- materialicons, datepicker -->
         * <input id="datepicker" width="312" />
         * <script>
         *     $('#datepicker').datepicker({ 
         *        maxDate: function() {
         *            var date = new Date();
         *            date.setDate(date.getDate()+1);
         *            return new Date(date.getFullYear(), date.getMonth(), date.getDate());
         *        }
         *     });
         * </script>
         */
        maxDate: undefined,

        /** Specifies the format, which is used to format the value of the DatePicker displayed in the input.
         * @additionalinfo <b>d</b> - Day of the month as digits; no leading zero for single-digit days.<br/>
         * <b>dd</b> - Day of the month as digits; leading zero for single-digit days.<br/>
         * <b>m</b> - Month as digits; no leading zero for single-digit months.<br/>
         * <b>mm</b> - Month as digits; leading zero for single-digit months.<br/>
         * <b>yy</b> - Year as last two digits; leading zero for years less than 10.<br/>
         * <b>yyyy</b> - Year represented by four digits.<br/>
         * @type String
         * @default 'mm/dd/yyyy'
         * @example Sample <!-- materialicons, datepicker -->
         * <input id="datepicker" value="2017-25-07" />
         * <script>
         *     var datepicker = $('#datepicker').datepicker({
         *         format: 'yyyy-dd-mm'
         *     });
         * </script>
         */
        format: 'mm/dd/yyyy',

        /** The name of the UI library that is going to be in use.
         * @additionalinfo The css file for bootstrap should be manually included if you use bootstrap.
         * @type (materialdesign|bootstrap|bootstrap4)
         * @default materialdesign
         * @example MaterialDesign <!-- materialicons, datepicker -->
         * <input id="datepicker" width="312" />
         * <script>
         *    var datepicker = $('#datepicker').datepicker({ 
         *        uiLibrary: 'materialdesign'
         *    });
         * </script>
         * @example Bootstrap.3 <!-- bootstrap, datepicker -->
         * <input id="datepicker" width="270" />
         * <script>
         *     $('#datepicker').datepicker({ uiLibrary: 'bootstrap' });
         * </script>
         * @example Bootstrap.4.Material.Icons <!-- materialicons, bootstrap4, datepicker -->
         * <input id="datepicker" width="276" />
         * <script>
         *     $('#datepicker').datepicker({ uiLibrary: 'bootstrap4' });
         * </script>
         * @example Bootstrap.4.FontAwesome <!-- fontawesome, bootstrap4, datepicker -->
         * <input id="datepicker" width="276" />
         * <script>
         *     $('#datepicker').datepicker({ uiLibrary: 'bootstrap4', iconsLibrary: 'fontawesome' });
         * </script>
         */
        uiLibrary: 'materialdesign',

        /** The name of the icons library that is going to be in use. Currently we support Material Icons, Font Awesome and Glyphicons.
         * @additionalinfo If you use Bootstrap 3 as uiLibrary, then the iconsLibrary is set to Glyphicons by default.<br/>
         * If you use Material Design as uiLibrary, then the iconsLibrary is set to Material Icons by default.<br/>
         * The css files for Material Icons, Font Awesome or Glyphicons should be manually included to the page where the grid is in use.
         * @type (materialicons|fontawesome|glyphicons)
         * @default 'materialicons'
         * @example Bootstrap.Material.Icons <!-- bootstrap, materialicons, datepicker -->
         * <input id="datepicker" width="276" />
         * <script>
         *     $('#datepicker').datepicker({
         *         uiLibrary: 'bootstrap',
         *         iconsLibrary: 'materialicons'
         *     });
         * </script>
         * @example Bootstrap.4.Font.Awesome <!-- bootstrap4, fontawesome, datepicker -->
         * <input id="datepicker" width="276" />
         * <script>
         *     $('#datepicker').datepicker({
         *         uiLibrary: 'bootstrap4',
         *         iconsLibrary: 'fontawesome'
         *     });
         * </script>
         */
        iconsLibrary: 'materialicons',

        icons: {
            /** datepicker icon definition.
             * @alias icons.rightIcon
             * @type String
             * @default '<i class="material-icons">arrow_drop_down</i>'
             * @example Custom.Material.Icon <!-- materialicons, datepicker -->
             * <input id="datepicker" />
             * <script>
             *     $('#datepicker').datepicker({
             *         icons: { 
             *             rightIcon: '<i class="material-icons">date_range</i>'
             *         }
             *     });
             * </script>
             * @example Custom.Glyphicon.Icon <!-- bootstrap, datepicker -->
             * <input id="datepicker" />
             * <script>
             *     $('#datepicker').datepicker({
             *         uiLibrary: 'bootstrap',
             *         icons: { 
             *             rightIcon: '<span class="glyphicon glyphicon-chevron-down" />'
             *         }
             *     });
             * </script>
             */
            rightIcon: '<i class="material-icons">event</i>',

            previousMonth: '<i class="material-icons">keyboard_arrow_left</i>',
            nextMonth: '<i class="material-icons">keyboard_arrow_right</i>'
        },

        fontSize: undefined,

        style: {
            wrapper: 'gj-datepicker gj-datepicker-md gj-unselectable',
            input: 'gj-textbox-md',
            calendar: 'gj-calendar gj-calendar-md'
        }
    },

    bootstrap: {
        style: {
            wrapper: 'gj-datepicker gj-datepicker-bootstrap gj-unselectable input-group',
            input: 'form-control',
            calendar: 'gj-calendar gj-calendar-bootstrap'
        },
        iconsLibrary: 'glyphicons',
        showOtherMonths: true
    },

    bootstrap4: {
        style: {
            wrapper: 'gj-datepicker gj-datepicker-bootstrap gj-unselectable input-group',
            input: 'form-control',
            calendar: 'gj-calendar gj-calendar-bootstrap'
        },
        showOtherMonths: true
    },

    materialicons: {},

    fontawesome: {
        icons: {
            rightIcon: '<span class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></span>',
            previousMonth: '<i class="fa fa-chevron-left" aria-hidden="true"></i>',
            nextMonth: '<i class="fa fa-chevron-right" aria-hidden="true"></i>'
        }
    },

    glyphicons: {
        icons: {
            rightIcon: '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>',
            previousMonth: '<span class="glyphicon glyphicon-chevron-left"></span>',
            nextMonth: '<span class="glyphicon glyphicon-chevron-right"></span>'
        }
    }
};

gj.datepicker.methods = {
    init: function (jsConfig) {
        gj.widget.prototype.init.call(this, jsConfig, 'datepicker');
        this.attr('data-datepicker', 'true');
        gj.datepicker.methods.initialize(this);
        return this;
    },

    initialize: function ($datepicker) {
        var data = $datepicker.data(),
            $wrapper = $datepicker.parent('div[role="wrapper"]'),
            $rightIcon = data.uiLibrary !== 'materialdesign' && data.iconsLibrary === 'materialicons' ? $('<span class="input-group-addon">' + data.icons.rightIcon + '</span>') : $(data.icons.rightIcon);

        $rightIcon.attr('role', 'right-icon');
        if ($wrapper.length === 0) {
            $wrapper = $('<div role="wrapper" />').addClass(data.style.wrapper); // The css class needs to be added before the wrapping, otherwise doesn't work.
            $datepicker.wrap($wrapper);
        } else {
            $wrapper.addClass(data.style.wrapper);
        }
        $wrapper = $datepicker.parent('div[role="wrapper"]');

        data.width && $wrapper.css('width', data.width);

        $datepicker.addClass(data.style.input).attr('role', 'input');

        data.fontSize && $datepicker.css('font-size', data.fontSize);

        $rightIcon.on('click', function (e) {
            if ($('body').children('[role="calendar"][guid="' + $datepicker.attr('data-guid') + '"]').is(':visible')) {
                gj.datepicker.methods.hide($datepicker);
            } else {
                gj.datepicker.methods.renderCalendar($datepicker);
                gj.datepicker.methods.show($datepicker);
            }
        });

        $wrapper.append($rightIcon);

        gj.datepicker.methods.createCalendar($datepicker);

    },

    createCalendar: function ($datepicker) {
        var date, data = $datepicker.data(),
            value = $datepicker.val(),
            $calendar = $('<div role="calendar" />').addClass(data.style.calendar).attr('guid', $datepicker.attr('data-guid')),
            $table = $('<table/>'),
            $thead = $('<thead/>');
        
        data.fontSize && $calendar.css('font-size', data.fontSize);

        date = gj.core.parseDate(value, data.format);
        if (!date || isNaN(date.getTime())) {
            date = new Date();
        } else {
            $datepicker.attr('day', date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate());
        }

        $datepicker.attr('month', date.getMonth());
        $datepicker.attr('year', date.getFullYear());

        $row = $('<tr role="month-manager" />');
        $row.append($('<th><div>' + data.icons.previousMonth + '</div></th>').on('click', gj.datepicker.methods.prevMonth($datepicker)));
        $row.append('<th colspan="5"><div role="month"></div></th>');
        $row.append($('<th><div>' + data.icons.nextMonth + '</div></th>').on('click', gj.datepicker.methods.nextMonth($datepicker)));
        $thead.append($row);

        $row = $('<tr role="week-days" />');
        for (i = 0; i < data.weekDays.length; i++) {
            $row.append('<th><div>' + data.weekDays[i] + '</div></th>');
        }
        $thead.append($row);

        $table.append($thead);
        $table.append('<tbody/>');
        $calendar.append($table);
        $calendar.hide();

        $('body').append($calendar);
        return $calendar;
    },

    renderCalendar: function ($datepicker) {
        var weekDay, selectedDay, day, month, year, daysInMonth, total, firstDayPosition, i, now, prevMonth, nextMonth, $cell, $day,
            data = $datepicker.data(),
            $calendar = $('body').children('[role="calendar"][guid="' + $datepicker.attr('data-guid') + '"]'),
            $table = $calendar.children('table'),
            $tbody = $table.children('tbody'),
            minDate = gj.datepicker.methods.getMinDate(data),
            maxDate = gj.datepicker.methods.getMaxDate(data);
        
        selectedDay = new Date($datepicker.attr('day'));
        month = parseInt($datepicker.attr('month'), 10);
        year = parseInt($datepicker.attr('year'), 10);

        $table.find('thead [role="month"]').text(data.months[month] + ' ' + year);

        daysInMonth = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        if (year % 4 == 0 && year != 1900) {
            daysInMonth[1] = 29;
        }
        total = daysInMonth[month];

        firstDayPosition = new Date(year + '-' + (month + 1) + '-01').getDay();

        $tbody.empty();

        weekDay = 0;
        $row = $('<tr />');
        prevMonth = gj.datepicker.methods.getPrevMonth(month, year);
        for (i = 1; i <= firstDayPosition; i++) {
            day = (daysInMonth[prevMonth.month] - firstDayPosition + i);
            if (prevMonth.year === selectedDay.getFullYear() && prevMonth.month === selectedDay.getMonth() && day === selectedDay.getDate()) {
                $cell = $('<td type="selected" />');
            } else {
                $cell = $('<td type="other-month" />');
            }
            $day = $('<div>' + day + '</div>');
            if (data.showOtherMonths) {
                $cell.append($day);
                if (data.selectOtherMonths && gj.datepicker.methods.isSelectable(minDate, maxDate, prevMonth.year, prevMonth.month, day)) {
                    $cell.addClass('gj-cursor-pointer');
                    $day.on('click', gj.datepicker.methods.select($datepicker, $calendar, day, prevMonth.month, prevMonth.year));
                } else {
                    $cell.addClass('disabled');
                }
            }
            $row.append($cell);
            weekDay++;
        }
        $tbody.append($row);

        now = new Date();
        for (i = 1; i <= total; i++) {
            if (weekDay == 0) {
                $row = $('<tr>');
            }
            if (year === selectedDay.getFullYear() && month === selectedDay.getMonth() && i === selectedDay.getDate()) {
                $cell = $('<td type="selected" />');
            } else if (year === now.getFullYear() && month === now.getMonth() && i === now.getDate()) {
                $cell = $('<td type="today" />');
            } else {
                $cell = $('<td type="current-month" />');
            }
            $day = $('<div>' + i + '</div>');
            if (gj.datepicker.methods.isSelectable(minDate, maxDate, year, month, i)) {
                $cell.addClass('gj-cursor-pointer');
                $day.on('click', gj.datepicker.methods.select($datepicker, $calendar, i, month, year));
            } else {
                $cell.addClass('disabled');
            }
            $cell.append($day);
            $row.append($cell);
            weekDay++;
            if (weekDay == 7) {
                $tbody.append($row);
                weekDay = 0;
            }
        }

        nextMonth = gj.datepicker.methods.getNextMonth(month, year);
        for (i = 1; weekDay != 0; i++) {
            if (nextMonth.year === selectedDay.getFullYear() && nextMonth.month === selectedDay.getMonth() && i === selectedDay.getDate()) {
                $cell = $('<td type="selected" />');
            } else {
                $cell = $('<td type="other-month" />');
            }
            if (data.showOtherMonths) {
                $day = $('<div>' + i + '</div>');
                $cell.append($day);
                if (data.selectOtherMonths && gj.datepicker.methods.isSelectable(minDate, maxDate, nextMonth.year, nextMonth.month, i)) {
                    $cell.addClass('gj-cursor-pointer');
                    $day.on('click', gj.datepicker.methods.select($datepicker, $calendar, i, nextMonth.month, nextMonth.year));
                } else {
                    $cell.addClass('disabled');
                }
            }
            $row.append($cell);
            weekDay++;
            if (weekDay == 7) {
                $tbody.append($row);
                weekDay = 0;
            }
        }
    },

    getMinDate: function (data) {
        var minDate;
        if (data.minDate) {
            if (typeof (data.minDate) === 'string') {
                minDate = new Date(data.minDate);
            } else if (typeof (data.minDate) === 'function') {
                minDate = data.minDate();
            } else if (typeof data.minDate.getMonth === 'function') {
                minDate = data.minDate;
            }
        }
        return minDate;
    },

    getMaxDate: function (data) {
        var maxDate;
        if (data.maxDate) {
            if (typeof data.maxDate === 'string') {
                maxDate = new Date(data.maxDate);
            } else if (typeof data.maxDate === 'function') {
                maxDate = data.maxDate();
            } else if (typeof data.maxDate.getMonth === 'function') {
                maxDate = data.maxDate;
            }
        }
        return maxDate;
    },

    isSelectable: function (minDate, maxDate, year, month, day) {
        var result = false,
            date = new Date(year, month, day);
        if ((!minDate || minDate <= date) && (!maxDate || maxDate >= date)) {
            result = true;
        }
        return result;
    },

    getPrevMonth: function (month, year) {
        date = new Date(year + '-' + (month + 1) + '-01');
        date.setMonth(date.getMonth() - 1);
        return { month: date.getMonth(), year: date.getFullYear() };
    },

    getNextMonth: function (month, year) {
        date = new Date(year + '-' + (month + 1) + '-01');
        date.setMonth(date.getMonth() + 1);
        return { month: date.getMonth(), year: date.getFullYear() };
    },

    prevMonth: function ($datepicker) {
        return function () {
            var date,
                month = parseInt($datepicker.attr('month'), 10),
                year = parseInt($datepicker.attr('year'), 10);

            date = gj.datepicker.methods.getPrevMonth(month, year);

            $datepicker.attr('month', date.month);
            $datepicker.attr('year', date.year);

            gj.datepicker.methods.renderCalendar($datepicker);
        }
    },

    nextMonth: function ($datepicker) {
        return function () {
            var date,
                month = parseInt($datepicker.attr('month'), 10),
                year = parseInt($datepicker.attr('year'), 10);

            date = gj.datepicker.methods.getNextMonth(month, year);

            $datepicker.attr('month', date.month);
            $datepicker.attr('year', date.year);

            gj.datepicker.methods.renderCalendar($datepicker);
        }
    },

    select: function ($datepicker, $calendar, day, month, year) {
        return function (e) {
            var date, value,
                data = $datepicker.data();
            date = new Date(year + '-' + (month + 1) + '-' + day);
            value = gj.core.formatDate(date, data.format);
            $datepicker.val(value);
            gj.datepicker.events.change($datepicker);
            $datepicker.attr('day', year + '-' + (month + 1) + '-' + day);
            $datepicker.attr('month', month);
            $datepicker.attr('year', year);
            gj.datepicker.methods.hide($datepicker);
            return $datepicker;
        };
    },

    show: function ($datepicker) {
        var data = $datepicker.data(),
            offset = $datepicker.offset(),
            $calendar = $('body').children('[role="calendar"][guid="' + $datepicker.attr('data-guid') + '"]');

        $calendar.css('left', offset.left).css('top', offset.top + $datepicker.outerHeight(true) + 3);
        $calendar.show();
        $datepicker.focus();
        gj.datepicker.events.show($datepicker);
    },

    hide: function ($datepicker) {
        var $calendar = $('body').children('[role="calendar"][guid="' + $datepicker.attr('data-guid') + '"]');
        $calendar.hide();
        gj.datepicker.events.hide($datepicker);
    },

    value: function ($datepicker, value) {
        var $calendar, date;
        if (typeof (value) === "undefined") {
            return $datepicker.val();
        } else {
            date = gj.core.parseDate(value, $datepicker.data().format);
            if (date) {
                $calendar = $('body').children('[role="calendar"][guid="' + $datepicker.attr('data-guid') + '"]');
                gj.datepicker.methods.select($datepicker, $calendar, date.getDate(), date.getMonth(), date.getFullYear())();
            } else {
                $datepicker.val('');
            }            
            return $datepicker;
        }
    },

    destroy: function ($datepicker) {
        var data = $datepicker.data(),
            $parent = $datepicker.parent();
        if (data) {
            $datepicker.off();
            $('body').children('[role="calendar"][guid="' + $datepicker.attr('data-guid') + '"]').remove();
            $datepicker.removeData();
            $datepicker.removeAttr('data-type').removeAttr('data-guid').removeAttr('data-datepicker');
            $datepicker.removeClass();
            $parent.children('[role="right-icon"]').remove();
            $datepicker.unwrap();
        }
        return $datepicker;
    }
};

gj.datepicker.events = {
    /**
     * Triggered when the datepicker value is changed.
     *
     * @event change
     * @param {object} e - event data
     * @example sample <!-- datepicker, materialicons -->
     * <input id="datepicker" />
     * <script>
     *     $('#datepicker').datepicker({
     *         change: function (e) {
     *             alert('Change is fired');
     *         }
     *     });
     * </script>
     */
    change: function ($datepicker) {
        return $datepicker.triggerHandler('change');
    },

    /**
     * Event fires when the datepicker is opened.
     * @event show
     * @param {object} e - event data
     * @example sample <!-- datepicker, materialicons -->
     * <input id="datepicker" />
     * <script>
     *     $('#datepicker').datepicker({
     *         show: function (e) {
     *             alert('show is fired.');
     *         }
     *     });
     * </script>
     */
    show: function ($datepicker) {
        return $datepicker.triggerHandler('show');
    },

    /**
     * Event fires when the datepicker is closed.
     * @event hide
     * @param {object} e - event data
     * @example sample <!-- datepicker, materialicons -->
     * <input id="datepicker" />
     * <script>
     *     $('#datepicker').datepicker({
     *         hide: function (e) {
     *             alert('hide is fired.');
     *         }
     *     });
     * </script>
     */
    hide: function ($datepicker) {
        return $datepicker.triggerHandler('hide');
    }
};

gj.datepicker.widget = function ($element, jsConfig) {
    var self = this,
        methods = gj.datepicker.methods;

    /** Gets or sets the value of the datepicker.
     * @method
     * @param {string} value - The value that needs to be selected.
     * @return string
     * @example Get <!-- datepicker, materialicons -->
     * <button class="gj-button-md" onclick="alert($datepicker.value())">Get Content</button>
     * <hr/>
     * <input id="datepicker" />
     * <script>
     *     var $datepicker = $('#datepicker').datepicker();
     * </script>
     * @example Set <!-- datepicker, materialicons -->
     * <button class="gj-button-md" onclick="$datepicker.value('08/01/2017')">Set Value</button>
     * <hr/>
     * <input id="datepicker" />
     * <script>
     *     var $datepicker = $('#datepicker').datepicker();
     * </script>
     */
    self.value = function (value) {
        return methods.value(this, value);
    };

    /** Remove datepicker functionality from the element.
     * @method
     * @return jquery element
     * @example sample <!-- datepicker, materialicons -->
     * <button class="gj-button-md" onclick="datepicker.destroy()">Destroy</button>
     * <input id="datepicker" />
     * <script>
     *     var datepicker = $('#datepicker').datepicker();
     * </script>
     */
    self.destroy = function () {
        return methods.destroy(this);
    };

    $.extend($element, self);
    if ('true' !== $element.attr('data-datepicker')) {
        methods.init.call($element, jsConfig);
    }

    return $element;
};

gj.datepicker.widget.prototype = new gj.widget();
gj.datepicker.widget.constructor = gj.datepicker.widget;

(function ($) {
    $.fn.datepicker = function (method) {
        var $widget;
        if (this && this.length) {
            if (typeof method === 'object' || !method) {
                return new gj.datepicker.widget(this, method);
            } else {
                $widget = new gj.datepicker.widget(this, null);
                if ($widget[method]) {
                    return $widget[method].apply(this, Array.prototype.slice.call(arguments, 1));
                } else {
                    throw 'Method ' + method + ' does not exist.';
                }
            }
        }
    };
})(jQuery);