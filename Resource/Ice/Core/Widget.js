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

        var data = Ice.jsonToObject($widget.closest('#' + $widget.attr('data-for')).attr('data-params'));

        if ($element.attr('data-params')) {
            data = Ice.objectMerge(data, Ice.jsonToObject($element.attr('data-params')));
        }

        if ($form) {
            data = Ice.objectMerge(data, Ice.querystringToObject($form.serialize()));
        }

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

        data.widgetClass = $widget.attr('data-widget');
        data.token = $widget.attr('data-token');

        data.viewClass = $element.attr('data-view');
        if (!data.viewClass) {
            data.viewClass = $widget.attr('data-view');
        }

        var action = $element.attr('data-action');
        if (!action) {
            action = $widget.attr('data-action');
        }

        var block = data.viewClass.split('\\').pop();

        if (!block) {
            alert('viewClass not found!');
            return;
        }

        var $view = $('#' + $widget.attr('data-for'));

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

            var observers = Ice.jsonToObject($view.attr('data-observers'));
            var $observer;

            for (var forId in observers) {
                $observer = $('#' + forId);

                Ice.reRender($observer, 'Ice:View_Render', {viewClass: observers[forId]}, null, url);
            }
        };

        Ice.reRender($view, action, data, widgetCallback, url);
    }
};

