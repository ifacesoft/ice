/**
 * Created by dp on 9/26/14.
 */

var Ice_Form = {
    submit: function ($button, submitActionName, reRenderClosest, $params) {
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
    modal: function ($button, modelClassName, pk, submitActionName, groupping, submitTitle, params) {
        params.modelClassName = modelClassName;
        params.pk = pk;
        params.submitActionName = submitActionName;
        params.groupping = groupping;
        params.submitTitle = submitTitle;
        params.template = '_Modal';
        Ice.reRenderClosest(
            $button,
            'Ice:Form_Model',
            params,
            function ($block) {
                $block.find('.modal').modal();
            }
        );
    }
};