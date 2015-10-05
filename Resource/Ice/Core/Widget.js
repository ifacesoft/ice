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
        console.log(Ice.querystringToObject($form.serialize()));

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
                if (result.data.error) {
                    $widget.find('.ice-message').html(result.data.error)
                } else {
                    if (result.data.success) {
                        $widget.find('.ice-message').html(result.data.success)
                    }

                    console.log(result);

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

