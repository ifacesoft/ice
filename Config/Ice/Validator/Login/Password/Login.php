<?php
/**
 * Created by JetBrains PhpStorm.
 * User: denis
 * Date: 17.04.13
 * Time: 20:33
 * To change this template use File | Settings | File Templates.
 */

return array(
    'login' => array(),
    'password' => array(
        array(
            'validator' => 'Data_Validator_Not_Empty',
            'message' => 'Введите пароль.'
        ),
        array(
            'validator' => 'Data_Validator_Length_Min',
            'params' => array(
                'minLength' => 3,
            ),
            'message' => 'Минимальная длина пароля {$minLength} символа.'
        ),
        array(
            'validator' => 'Data_Validator_Length_Max',
            'params' => array(
                'maxLength' => 30,
            ),
            'message' => 'Максимальная длина пароля {$maxLength} символов.'
        )
    )
);