/**
 * Created by dp on 9/26/14.
 */

var Ice_Ui_Form = {
    change: function ($element, callback) {
        var data = JSON.parse($element.attr('data-json'));
        data[$element.attr('name')] = $element.val();

        Ice.reRender($element, data, callback, $element.attr('data-url'));
    },

    submit: function ($button, submitActionName, $params, reRenderClosest, reRenderActionNames) {
        Ice.call(
            submitActionName,
            $button.closest('form').serialize(),
            function (result) {
                if (result.success && result.success.length) {
                    if (result.redirect && result.redirect.length) {
                        setTimeout(location.href = result.redirect, 3000);
                        return;
                    }

                    $button.closest('form')[0].reset();

                    if (reRenderClosest) {
                        Ice.reRenderClosest($button, reRenderClosest, $params);
                    }

                    reRenderActionNames.forEach(function (className) {
                        Ice.reRender(className);
                    });
                }
            }
        );
    },
    modal: function ($button, modelClassName, pk, submitActionName, formFilterFields, grouping, submitTitle, template, params, reRenderClosest, reRenderActionNames) {
        Ice.reRenderClosest(
            $button,
            'Ice:Form_Model',
            {
                modelClassName: modelClassName,
                pk: pk,
                submitActionName: submitActionName,
                formFilterFields: formFilterFields,
                grouping: grouping,
                submitTitle: submitTitle,
                template: template,
                params: params,
                reRenderClosest: reRenderClosest,
                reRenderActionNames: reRenderActionNames
            },
            function ($block) {
                $block.find('.modal').modal();
            },
            reRenderClosest
        );
    },
    remove: function ($button, modelClassName, pk, $params, reRenderClosest, reRenderActionNames) {
        Ice.call(
            'Ice:Model_Delete',
            {
                modelClassName: modelClassName,
                pk: pk
            },
            function (result) {
                if (result.success && result.success.length) {
                    if (result.redirect && result.redirect.length) {
                        setTimeout(location.href = result.redirect, 3000);
                        return;
                    }

                    if (reRenderClosest) {
                        Ice.reRenderClosest($button, reRenderClosest, $params);
                    }

                    reRenderActionNames.forEach(function (className) {
                        Ice.reRender(className);
                    });
                }
            }
        );
    }
};