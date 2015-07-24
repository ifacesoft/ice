/**
 * Created by dp on 4/6/15.
 */

var Ice_Widget_Menu = {
    click: function ($element, callback) {
        Ice.reRender($element, {}, callback, $element.attr('data-url'), 'GET');
    }
};