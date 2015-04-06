/**
 * Created by dp on 4/6/15.
 */

var Ice_Action_Menu = {
    click: function ($element, value) {
        var data = JSON.parse($element.attr('data-json'));
        data.page = value;

        Ice.reRender($element, data, null, $element.attr('href'));
    }
};