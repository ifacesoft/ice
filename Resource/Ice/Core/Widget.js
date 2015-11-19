/**
 * Created by dp on 28.05.15.
 */
var Ice_Core_Widget = {
    click: function ($element, url, method, callback) {
        var $form = $element.prop('tagName') == 'FORM'
            ? $element
            : null;

        var $widget = $form
            ? $form
            : $element.closest('#' + $element.attr('data-for'));

        if (!$form && $widget.prop('tagName') == 'FORM') {
            $form = $widget;
        }

        if (!$widget.attr('data-params')) {
            console.warn('Data params for widget not found');
            return;
        }

        var data = Ice.objectMerge(
            Ice.jsonToObject($widget.attr('data-params')),
            Ice.jsonToObject($element.attr('data-params'))
        );

        if ($form) {
            data = Ice.objectMerge(data, Ice.querystringToObject($form.serialize()))

            $form.find("input:checkbox:not(:checked)").each(function() {
                data[$(this).attr('name')] = null;
            });
        }

        if (url == '') {
            url = location.href;
        }

        if (method == 'GET') {
            var a = document.createElement('a');
            a.href = url;
            url = a.pathname + '?' + $.param(data) + a.hash;
            data = {};
        }

        var dataAction = Ice.jsonToObject($element.attr('data-action'));

        if (!data.ajax) {
            location.href = url;
            return;
        }

        data.widget = Ice.jsonToObject($widget.attr('data-widget'));

        data = Ice.objectMerge(data, dataAction.data);

        var widgetCallback = function (result) {
            if (callback) {
                callback(result);
            }

            if (method == 'GET') {
                var title = result.title
                    ? result.title
                    : document.title;

                history.pushState(data, title, url);
                //window.onpopstate = function() {
                //    Ice.reRender($targetBlock, $baseElement.attr('data-action'), history.state, widgetCallback, url);
                //};
            }

            //var observers = Ice.jsonToObject($view.attr('data-observers'));
            //var $observer;

            //for (var forId in observers) {
            //    $observer = $('#' + forId);
            //
            //    Ice.reRender($observer, 'Ice:Render', {viewClass: observers[forId]}, null, url);
            //}
        };

        Ice_Core_Widget.reRender($widget, dataAction.class, data, widgetCallback, url, 'POST');
    },

    reRender: function ($widget, actionClass, actionParams, callback, url, method) {
        Ice.call(
            actionClass,
            actionParams,
            function (result) {
                var $iceMessage = $widget.find('.ice-message');

                if (result.error) {
                    if ($iceMessage.length) {
                        $iceMessage.html(result.error);
                    } else {
                        Ice.notify($('#iceMessages'), result.error, 5000);
                    }
                } else {
                    setTimeout(
                        function () {
                            if (result.redirect) {
                                location.href = result.redirect;
                            } else {
                                if (result.widgets) {
                                    for (widgetId in result.widgets) {
                                        $widget = $('#' + widgetId);
                                        if ($widget.length) {
                                            $widget.replaceWith(result.widgets[widgetId]);
                                        } else {
                                            Ice.notify($('#iceMessages'), '<div class="alert alert-danger">#' + widgetId + ' not found</div>', 5000);
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
                        if ($iceMessage.length) {
                            $iceMessage.html(result.success);
                        } else {
                            Ice.notify($('#iceMessages'), result.success, 5000);
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

