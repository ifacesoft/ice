/**
 * Created by dp on 28.05.15.
 */
var Ice_Core_Widget = {
    click: function ($element, callback, method) {
        var $form = $element.prop('tagName') == 'FORM'
            ? $element
            : null;

        var $widget = $form
            ? $form
            : $element.closest('#' + $element.attr('data-for'));

        if (!$form && $widget.prop('tagName') == 'FORM') {
            $form = $widget;
        }

        var data = Ice.jsonToObject($widget.attr('data-params'));

        data = $form
            ? Ice.objectMerge(data, Ice.querystringToObject($form.serialize()))
            : Ice.objectMerge(data, Ice.jsonToObject($element.attr('data-params')));

        var url = $element.attr('href');

        if (typeof url == 'undefined' && $form) {
            url = $form.attr('action');
        }

        if (typeof url == 'undefined') {
            url = $widget.attr('data-url');
        } else {
            if (method == 'GET') {
                var a = document.createElement('a');
                a.href = url;
                url = a.pathname + '?' + $.param(data) + a.hash;
            }
        }

        data.widget = Ice.jsonToObject($widget.attr('data-widget'));

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
            //    Ice.reRender($observer, 'Ice:View_Render', {viewClass: observers[forId]}, null, url);
            //}
        };

        Ice_Core_Widget.reRender($widget, Ice_Core_Widget.getAttr('data-action', $element), data, widgetCallback, url);
    },

    reRender: function ($widget, actionClass, actionParams, callback, url, method) {
        Ice.call(
            actionClass,
            actionParams,
            function (result) {
                if (result.data.error) {
                    $widget.find('.ice-message').html(result.data.error)
                } else {
                    if (result.data.success) {
                        $widget.find('.ice-message').html(result.data.success)
                    }

                    setTimeout(
                        function () {
                            if (result.data.redirect) {
                                location.href = result.data.redirect;
                            } else {
                                if (result.data.content) {
                                    $widget.replaceWith(result.data.content);
                                }

                                if (result.data.widgets) {
                                    for (widgetId in result.data.widgets) {
                                        $('#' + widgetId).replaceWith(result.data.widgets[widgetId]);
                                    }
                                }

                                if (callback) {
                                    callback(result);
                                }
                            }
                        },
                        result.data.timeout ? result.data.timeout : 0
                    );
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

