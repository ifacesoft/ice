/**
 *
 * @package Ice
 *
 */
var Ice = {
    _callbacks: {},
    _lastback: 0,

    call: function (action, params, callback) {
        var back = this._lastback++;
        Ice._callbacks [back] = callback;
        $.ajax({
            type: "POST",
            url: location.href,
            data: {
                call: action,
                params: params,
                back: back
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

    reRender: function (actionName, actionParams, callback) {
        Ice.call(
            actionName,
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
