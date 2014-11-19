/**
 * Created by dp on 9/26/14.
 */

var Form = {
    submit: function ($button, submitActionName, reRenderActionNames) {
        Ice.call(
            'Ice:Form_Submit',
            $button.closest('form').serialize(),
            function (result) {
                if (result.success) {
                    $button.closest('form')[0].reset();
                    reRenderActionNames.forEach(function(className) {
                        Ice.reRender(className);
                    });
                }
            }
        );
    }
};