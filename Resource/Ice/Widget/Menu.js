/**
 * Created by dp on 4/6/15.
 */

var Ice_Widget_Menu = {
    click: function ($element, page, callback) {
        var data = JSON.parse($element.attr('data-json'));
        data.page = page;

        Ice.reRender($element, data, callback, $element.attr('data-url'));
    }
};