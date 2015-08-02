/**
 * Created by dp on 28.05.15.
 */
var Ice_Core_Widget = {
    click: function ($element, callback, method) {
        var $baseElement = $element.prop('tagName') == 'FORM'
            ? $element
            : $element.closest('#' + $element.attr('data-for'));

        var data = Ice.jsonToObject($baseElement.attr('data-json'));

        if ($element.attr('data-params')) {
            data = Ice.objectMerge(data, Ice.jsonToObject($element.attr('data-params')));
        }

        if ($baseElement.prop('tagName') == 'FORM') {
            data = Ice.objectMerge(data, Ice.querystringToObject($baseElement.serialize()));
        }

        var url = $element.attr('href');

        if (typeof url == 'undefined' && $baseElement.prop('tagName') == 'FORM') {
            url = $baseElement.attr('action');
        }

        if (typeof url == 'undefined') {
            url = $baseElement.attr('data-url');
        } else {
            if (method == 'GET') {
                var a = document.createElement('a');
                a.href = url;
                url = a.pathname + '?' + $.param(data) + a.hash;
            }
        }

        var block = $baseElement.attr('data-block');

        var $targetBlock = $baseElement.closest('.' + block);

        if (!$targetBlock.length) {
            $targetBlock = $('.' + block);
        }

        var widgetCallback = function(result) {
            if (callback) {
                callback(result);
            }

            if (method == 'GET') {
                var title = result.title
                    ? result.title
                    : document.title;

                history.pushState({}, title, url);
            }
        };

        Ice.reRender($targetBlock, $baseElement.attr('data-action'), data, widgetCallback, url);
    }
};