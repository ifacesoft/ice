/**
 *
 * @package Ice
 *
 */
var Ice = {
    _callbacks: {},
    _lastback: 0,

    call: function (action, data, callback, url, method) {
        $('#icePreloader').show();

        var back = this._lastback++;

        Ice._callbacks [back] = callback;

        data.call = action;
        data.back = back;

        $.ajax({
            type: method ? method : 'POST',
            url: url ? url : location.href,
            data: data,
            //crossDomain: true,
            beforeSend: function (jqXHR, settings) {
                jqXHR.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            },
            success: function (result) {
                if (result.error) {
                    Ice.notify($('#iceMessages'), result.error, 5000);
                }
                if (result.success) {
                    Ice.notify($('#iceMessages'), result.success, 5000);
                }

                if (result.back) {
                    back = result.back;
                }

                var callback = Ice._callbacks[back];
                callback(result);
                $('#icePreloader').hide();
            },
            error: function (result) {
                console.error(result);
                $('#icePreloader').hide();
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
                        var parentActionName = '';

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

                    if (callback != null) {
                        callback($block);
                    }
                }
            }
        );
    },

    reRender: function ($tagetBlock, action, actionParams, callback, url, method) {
        Ice.call(
            action,
            actionParams,
            function (result) {
                $tagetBlock.replaceWith(result.content);

                if (callback) {
                    callback();
                }

                var title = result.title
                    ? result.title
                    : document.title;

                history.pushState({}, title, url);
            },
            url,
            method
        );
    },

    notify: function ($element, body, time) {
        $element.append(body).delay(time).show(1, function () {
            $element.children().first().remove();
        });
    },

    querystringToObject: function (query) {
        if (query && query.length && query.charAt(0) === '?') {
            query = query.substr(1)
        }

        var match,
            pl = /\+/g,  // Regex for replacing addition symbol with a space
            search = /([^&=]+)=?([^&]*)/g,
            decode = function (s) {
                return decodeURIComponent(s.replace(pl, " "));
            },

            urlParams = {};

        while (match = search.exec(query)) {
            urlParams[decode(match[1])] = decode(match[2]);
        }

        return urlParams;
    },

    arrayToObject: function (arr) {
        return arr.reduce(function (o, v, i) {
            o[i] = v;
            return o;
        }, {});
    },

    jsonToObject: function (json) {
        return JSON.parse(json);
        //return Ice.arrayToObject(JSON.parse(json));
    },

    objectMerge: function (obj1, obj2) {
        return $.extend({}, obj1, obj2);
    }
};

Date.prototype.yyyymmdd = function () {
    var yyyy = this.getFullYear().toString();
    var mm = (this.getMonth() + 1).toString(); // getMonth() is zero-based
    var dd = this.getDate().toString();
    return yyyy + '-' + (mm[1] ? mm : "0" + mm[0]) + '-' + (dd[1] ? dd : "0" + dd[0]); // padding
};
