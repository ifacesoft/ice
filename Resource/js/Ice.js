/**
 *
 * @package Ice
 *
 */
var Ice = {
    _callbacks: {},
    _lastback: 0,

    call: function (controller, params, callback) {

        var back = this._lastback++;
        Ice._callbacks [back] = callback;
        $.ajax({
            type: "POST",
            url: location.href,
            data: {
                call: controller,
                params: params,
                back: back
            },
            success: function (data) {
                var callback = Ice._callbacks[data.back];
                callback(data.result);
            },
            error: function (data) {
                console.error(data);
            },
            dataType: 'json'
        });
    },
    /**
     * @desc Все поля формы как поля объекта
     * @param jQuery $form Форма.
     * @param string filter jQuery selector.
     * @param boolean check проверять ли required
     * @returns object Объект, содержащий данные с формы.
     */
    getFormData: function ($form, filter, check) {
        $fields =
            $form.find('input,select,textarea')
                .filter(':not(.nosubmit)');

        if (filter) {
            $fields.filter(filter);
        }
        if (!check) {
            check = false;
        }
        var data = {}, placeholder, value,
            errorRequired = false;

        $fields.each(function () {

            /**
             * для корректной обработки select:multiple и
             * параметров с именем "param[]"
             *
             * @author red
             */
            function _setValue(name, value, lastObject, attrName) {
                var _name = name;
                var _isArray = false;
                var _isObject = false;
                var i;

                if (name.slice(-2) == '[]') {
                    _name = name.slice(0, -2);
                    _isArray = true;
                } else if (typeof value == 'object') {
                    _isArray = true;
                } else if (/\[([^\]]+)\]/.test(name)) {
                    _isObject = true;
                } else if (lastObject) {
                    _isObject = true;
                }

                if (_isArray) {
                    if (!(_name in data)) {
                        data[_name] = [];
                    }
                    if (typeof value == 'object') {
                        for (i in value) {
                            data[_name].push(value[i]);
                        }
                    } else {
                        data[_name].push(value);
                    }
                } else if (_isObject) {
                    var pos;
                    if (!lastObject) {
                        pos = name.indexOf('[');
                        _name = name.substr(0, pos);
                        name = name.substr(pos);
                        if (!(_name in data)) {
                            data[_name] = {};
                        }
                        _setValue(name, value, data[_name]);
                    } else {
                        if (name) {
                            pos = name.indexOf('[');
                            var endPos = name.indexOf(']');
                            _name = name.substr(pos + 1, endPos - pos - 1);
                            attrName = null;
                            if (endPos + 1 < name.length) {
                                name = name.substr(endPos + 1);
                            } else {
                                name = '';
                                attrName = _name;
                            }
                            if (!(_name in lastObject)) {
                                lastObject[_name] = {};
                            }
                            if (!attrName) {
                                _setValue(name, value, lastObject[_name]);
                            } else {
                                _setValue(name, value, lastObject, _name);
                            }
                        } else {
                            lastObject[attrName] = value;
                        }
                    }
                } else {
                    data[_name] = value;
                }
            }


            if (this.tagName.toLowerCase() == 'input') {
                if (this.type == "file") {
                    _setValue(this.name, this);
                }
                else if (this.type == 'checkbox') {
                    if (this.checked) {
                        _setValue(this.name, $(this).val() ? $(this).val() : 'on');
                    }
                }
                else if (this.type == 'radio') {
                    if (this.checked) {
                        _setValue(this.name, $(this).val());
                    }
                }
                // обычные input[name=text]
                else {
                    if (check && $(this).attr('required') && $(this).is(':visible')) {
                        value = $(this).val();
                        placeholder = $(this).attr('placeholder');
                        if (!value.length || value == placeholder) {
                            $(this).addClass('errorRequired');
                            $(this).attr('required');
                            errorRequired = true;
                        }
                        else {
                            $(this).removeClass('errorRequired');
                        }
                    }
                    if ($(this).attr('placeholder') == $(this).val()) {
                        _setValue(this.name, '');
                    }
                    else {
                        _setValue(this.name, $(this).val());
                    }
                }
            } else {
                //можно на наследование переделать всё это, вверху input ещё
                if (check && this.tagName.toLowerCase() == 'textarea') {
                    if ($(this).attr('required')) {
                        if ($(this).is(':visible')) {
                            if ($(this).val() == '') {
                                $(this).addClass('errorRequired');
                                errorRequired = true;
                            } else {
                                $(this).removeClass('errorRequired');
                            }
                        } else {
                            if ($(this).attr('id') &&
                                $('#' + $(this).attr('id') + '_tbl').length &&
                                $('#' + $(this).attr('id') + '_tbl').is(':visible')) {
                                if (Controller_TinyMce.getVal({'id': $(this).attr('id')}) == '') {
                                    $('#' + $(this).attr('id') + '_tbl')
                                        .addClass('errorRequired');
                                    errorRequired = true;
                                } else {
                                    $('#' + $(this).attr('id') + '_tbl')
                                        .removeClass('errorRequired');
                                }
                            }
                        }

                    }
                }
                if (this.tagName.toLowerCase() == 'select') {
                    if ($(this).attr('required')) {
                        value = $(this).val();
                        if (!value || value == 0) {
                            $(this).addClass('errorRequired');
                            errorRequired = true;
                        } else {
                            $(this).removeClass('errorRequired');
                        }
                    }
                }
                if ($(this).attr('placeholder') == $(this).val()) {
                    _setValue(this.name, '');
                } else {
                    _setValue(this.name, $(this).val());
                }
            }
        });
        data['errorRequired'] = errorRequired;
        return data;
    }
};
