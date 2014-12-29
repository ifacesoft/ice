/**
 *
 * @package Ice
 *
 */
var Ice = {
    _callbacks: {},
    _lastback: 0,

    call: function (action, params, callback, url) {
        var back = this._lastback++;
        Ice._callbacks [back] = callback;
        $.ajax({
            type: 'POST',
            url: url ? url : location.href,
            data: {
                call: action,
                params: params,
                back: back
            },
            //crossDomain: true,
            beforeSend: function (jqXHR, settings) {
                jqXHR.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            },
            success: function (data) {
                if (data.result.error) {
                    Ice.notify($('#iceMessages'), data.result.error, 5000);
                }
                if (data.result.success) {
                    Ice.notify($('#iceMessages'), data.result.success, 5000);
                }

                var callback = Ice._callbacks[data.back];
                callback(data.result);
            },
            error: function (data) {
                console.error(data);
            },
            dataType: 'json'
        });
    },

    reRenderClosest: function ($element, actionClassName, actionParams, callback, container) {
        Ice.call(
            actionClassName,
            actionParams,
            function (result) {
                if (result.actionName) {
                    var $block = $(result.html);

                    if (container) {
                        var parentActionName = ''

                        if (parentActionName = Ice_Helper_String.strstr(container, ':')) {
                            parentActionName = parentActionName.substr(1);
                        } else if (container = Ice_Helper_String.strstr(container, '\\')) {
                            parentActionName = parentActionName.substr(1);
                        } else {
                            parentActionName = container;
                        }

                        $element.closest('.' + parentActionName).find('.' + result.actionName).replaceWith($block);
                    } else {
                        $element.closest('.' + result.actionName).replaceWith($block);
                    }

                    if (callback) {
                        callback($block);
                    }
                }
            }
        );
    },

    reRender: function (actionClassName, actionParams, callback) {
        Ice.call(
            actionClassName,
            actionParams,
            function (result) {
                if (result.actionName) {
                    $('.' + result.actionName).replaceWith(result.html);
                }
                if (callback) {
                    callback();
                }
            }
        );
    },

    notify: function ($element, body, time) {
        $element.append(body).delay(time).show(1, function () {
            $element.children().first().remove();
        });
    }

};
