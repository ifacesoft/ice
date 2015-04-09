/**
 * Created by dp on 4/6/15.
 */

var Ice_Ui_Data = {
    click: function ($element, ordering, callback) {
        var data = JSON.parse($element.attr('data-json'));
        data[$element.attr('data-name')] = ordering;

        Ice.reRender($element, data, callback, $element.attr('data-url'));
    }
};