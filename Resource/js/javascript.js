/**
 *
 * @package Ice
 *
 */
var Ice = {
    _callbacks: {},
    _lastback: 0,

    call: function (actionClass, data, callback, url, method) {
        $('#icePreloader').show();

        var back = this._lastback++;

        Ice._callbacks [back] = callback;

        data.actionClass = actionClass;
        data.back = back;

        var result;

        $.ajax({
            type: method ? method : 'POST',
            url: url ? url : location.href,
            data: data,
            //crossDomain: true,
            beforeSend: function (jqXHR, settings) {
                jqXHR.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            },
            success: function (result) {
                if (result.back) {
                    back = result.back;
                }

                var callback = Ice._callbacks[back];
                callback(result);
                $('#icePreloader').hide();
            },
            error: function (data) {
                if (data.responseJSON) {
                    result = data.responseJSON;
                    if (result.error) {
                        Ice.notify($('#iceMessages'), result.error, 5000);
                        console.warn(result.error);
                    }
                } else {
                    Ice.notify($('#iceMessages'), '<div class="alert alert-danger">' + data.statusText + '</div>', 5000);
                    console.warn(data.statusText);
                }

                $('#icePreloader').hide();
            },
            dataType: 'json'
        });
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

        var param;
        var value;

        while (match = search.exec(query)) {
            param = decode(match[1]);
            value = decode(match[2]);

            if (param.length > 2 && param.slice(-2) == '[]') {
                param = param.slice(0, -2);

                if (urlParams[param] == undefined) {
                    urlParams[param] = [value];
                } else {
                    urlParams[param].push(value);
                }
            } else {
                urlParams[param] = value;
            }
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
        return $.parseJSON(json.replace(/[\n\t\r]/g, ""));
    },

    objectMerge: function (obj1, obj2) {
        return $.extend({}, obj1, obj2);
    },

    //serialize: function($form) {
    //    /* Get input values from form */
    //    var values = $form.serializeArray();
    //
    //    /* Because serializeArray() ignores unset checkboxes and radio buttons: */
    //    values = values.concat(
    //        jQuery('#' + $form.attr('id') + ' input[type=checkbox]:not(:checked)').map(
    //            function() {
    //                return {"name": this.name, "value": this.value}
    //            }).get()
    //    );
    //
    //    return {"name": this.name, "value": 'off' }
    //}

    addSlashes: function (str) {
        return (str + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
    },

    replaceRender: function (template, data) {
        if (template.indexOf('{$') > -1) {
            for (var name in data) {
                template = template.replace('{$' + name + '}', data[name]);
            }

            return template;
        } else {
            return data[template];
        }
    }
};

Date.prototype.yyyymmdd = function () {
    var yyyy = this.getFullYear().toString();
    var mm = (this.getMonth() + 1).toString(); // getMonth() is zero-based
    var dd = this.getDate().toString();
    return yyyy + '-' + (mm[1] ? mm : "0" + mm[0]) + '-' + (dd[1] ? dd : "0" + dd[0]); // padding
};
