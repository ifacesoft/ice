/**
 * Created by dp on 28.05.15.
 */
var Ice_Core_Widget = {
    click: function ($element, callback) {
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

        var data = $form
            ? Ice.querystringToObject($form.serialize())
            : Ice.objectMerge(Ice.jsonToObject($widget.attr('data-params')), Ice.jsonToObject($element.attr('data-params')));

        var url = $form
            ? $form.attr('action')
            : $element.attr('href');

        var action = Ice.jsonToObject(Ice_Core_Widget.getAttr('data-action', $element));

        if (!url) {
            url = action.url;
        }

        var method = action.method;

        if (method == 'GET') {
            var a = document.createElement('a');
            a.href = url;
            url = a.pathname + '?' + $.param(data) + a.hash;
            data = {};
        }

        data.widget = Ice.jsonToObject($widget.attr('data-widget'));

        data = Ice.objectMerge(data, action.params);

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

        Ice_Core_Widget.reRender($widget, action.class, data, widgetCallback, url, 'POST');
    },

    reRender: function ($widget, actionClass, actionParams, callback, url, method) {
        Ice.call(
            actionClass,
            actionParams,
            function (result) {
                var $iceMessage = $widget.find('.ice-message');

                if (result.error) {
                    if ($iceMessage) {
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
                                if (result.content) {
                                    $widget.replaceWith(result.content);
                                }

                                if (result.widgets) {
                                    for (widgetId in result.widgets) {
                                        $('#' + widgetId).replaceWith(result.widgets[widgetId]);
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
                        if ($iceMessage) {
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

