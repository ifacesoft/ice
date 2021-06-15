const Ice_Core_Widget = {
    waitClick: false,

    click: function ($element, url, method, callback, confirm_message, dataCallback) {
        if (Ice_Core_Widget.waitClick === true) {
            console.log('Another widget click runned. Wait! Please, try leter.');

            return false;
        }

        Ice_Core_Widget.waitClick = true;

        if (confirm_message) {
            if (!confirm(confirm_message)) {
                if (callback) {
                    callback({confirm: false});
                }

                Ice_Core_Widget.waitClick = false;

                return false;
            }
        }

        var $form = $element.prop('tagName') == 'FORM'
            ? $element
            : null;

        if ($form) {
            jQuery($form).find('button').prop('disabled', true);
        }

        // var $widget = $form
        //     ? $form
        //     : $element.closest('#' + $element.attr('data-for'));

        var $widget;

        if ($form) {
            $widget = $form;
        } else {
            $widget = $element.closest('#' + $element.attr('data-for'));

            if ($widget.length == 0) {
                $widget = $('#' + $element.attr('data-for'));
            }
        }

        if (!$form && $widget.prop('tagName') === 'FORM') {
            $form = $widget;
        }

        if (!$widget.attr('data-params')) {
            console.warn('Data params for widget not found');

            Ice_Core_Widget.waitClick = false;

            return false;
        }

        var data = {};

        try {
            data = Ice.jsonToObject(Base64.decode($widget.attr('data-params')));
        } catch (e) {
            // todo: обязательно кодировать в base64               // Это
            data = Ice.jsonToObject($widget.attr('data-params'));  // Нужно
        }                                                          // Выпилить

        if (!$form || $form != $element) {
            try {
                data = Ice.objectMerge(data, Ice.jsonToObject(Base64.decode($element.attr('data-params'))))
            } catch (e) {
                // todo: обязательно кодировать в base64                                      // Это
                data = Ice.objectMerge(data, Ice.jsonToObject($element.attr('data-params')))  // Нужно
            }                                                                                 // Выпилить
        }

        if ($form) {
            data = Ice.objectMerge(data, Ice.querystringToObject($form.serialize()))
        }

        if (!url) {
            url = location.href;
        }

        if (method == 'GET') {
            var a = document.createElement('a');
            a.href = url;
            var pathname = (a.pathname.charAt(0) == "/") ? a.pathname : "/" + a.pathname;
            url = pathname + '?' + $.param(data) + a.hash;
            data = {};
        }

        var dataAction = Ice.jsonToObject($element.attr('data-action'));

        if (dataWidget = $widget.attr('data-widget')) {
            data.widget = Ice.jsonToObject(dataWidget);
        }

        data = Ice.objectMerge(data, dataAction.params);

        if (dataCallback) {
            data = dataCallback($element, data);
        }

        var widgetCallback = function (result) {
            if (callback) {
                callback(result);
            }

            if (method === 'GET') {
                var title = result.title
                    ? result.title
                    : document.title;

                history.pushState(data, title, url);
                //window.onpopstate = function() {
                //    Ice.reRender($targetBlock, $baseElement.attr('data-action'), history.state, widgetCallback, url);
                //};
            } else {
                history.replaceState({}, document.title);
            }

            //var observers = Ice.jsonToObject($view.attr('data-observers'));
            //var $observer;

            //for (var forId in observers) {
            //    $observer = $('#' + forId);
            //
            //    Ice.reRender($observer, 'Ice:Render', {viewClass: observers[forId]}, null, url);
            //}
        };

        if (dataAction.ajax) {
            Ice_Core_Widget.reRender($widget, dataAction.class, data, widgetCallback, url, 'POST');
        } else {
            location.href = url;
        }

        if ($form) {
            var buttons = jQuery($form).find('button');
            setTimeout(function () {
                jQuery(buttons).prop('disabled', false);
            }, 1000);
        }

        Ice_Core_Widget.waitClick = false;

        return true;
    },

    reRender: function ($widget, actionClass, actionParams, callback, url, method) {
        Ice.call(
            actionClass,
            actionParams,
            function (result) {
                var $iceMessage = null;

                if (result.error) {
                    $iceMessage = $widget.find('.ice-message');

                    if ($widget && $iceMessage.length) {
                        $iceMessage.html(result.error);
                    } else {
                        Ice.notify($('#iceMessages'), '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + result.error + '</div>', 60000);
                    }
                } else {
                    setTimeout(
                        function () {
                            if (result.redirect) {
                                if (callback) {
                                    callback(result);
                                }
                                location.href = result.redirect;
                            } else {
                                if (result.widgets) {
                                    for (widgetId in result.widgets) {
                                        $widget = $('#' + widgetId);

                                        if ($widget.length) {
                                            $widget.replaceWith(result.widgets[widgetId].content);

                                            if (result.widgets[widgetId].callback) {
                                                eval('var widgetCallback = ' + result.widgets[widgetId].callback);
                                                widgetCallback(result.widgets[widgetId].params);
                                            }
                                        } else {
                                            Ice.notify($('#iceMessages'), '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>#' + widgetId + ' not found</div>', 60000);
                                        }
                                    }
                                }

                                if (callback) {
                                    callback(result);
                                }
                            }
                        },
                        result.timeout ? result.timeout : 0
                    );

                    if (result.success) {
                        $iceMessage = $widget.find('.ice-message');

                        if ($widget && $iceMessage.length) {
                            $iceMessage.html(result.success);
                        } else {
                            Ice.notify($('#iceMessages'), '<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + result.success + '</div>', 5000);
                        }
                    }
                }
            },
            url,
            method
        );
    },

    getAttr: function (name, $element) {
        var param = $element.attr(name);

        if (param) {
            return param;
        }

        $element = $('#' + $element.attr('data-for'));

        return $element ? Ice_Core_Widget.getAttr(name, $element) : param
    }
};

if (typeof module === "object") {
    module.exports = Ice_Core_Widget;
}
