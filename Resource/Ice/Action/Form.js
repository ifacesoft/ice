/**
 * Created by dp on 9/26/14.
 */

var Ice_Form = {
    submit: function ($button, submitActionName, reRenderActionNames) {
        Ice.call(
            submitActionName,
            $button.closest('form').serialize(),
            function (result) {
                if (result.success.length) {
                    if (result.redirect.length) {
                        setTimeout(location.href = result.redirect, 3000);
                        return;
                    }

                    $button.closest('form')[0].reset();
                    reRenderActionNames.forEach(function (className) {
                        Ice.reRender(className);
                    });
                }
            }
        );
    },
    modal: function (modelName, pk, submitActionName, reRenderActionNames, filterFields, groupping, submitTitle) {
        Ice.reRender(
            'Ice:Form_Model',
            {
                'modelName': modelName,
                'pk': pk,
                'submitActionName': submitActionName,
                'reRenderActionNames': reRenderActionNames,
                'filterFields': filterFields,
                'groupping': groupping,
                'submitTitle': submitTitle,
                'template': '_Modal'
            },
            function () {
                $('.Form_Model .modal').modal();
            }
        );
    }
};