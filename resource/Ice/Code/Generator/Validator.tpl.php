<?= '<?php' ?>

<?php if ($namespace) { ?>namespace <?= $namespace ?>;<?php
} ?>


use Ice\Core\Validator;

/**
* Class <?= $validatorName ?>

*
* @see Ice\Core\Validator
<?php if ($namespace) { ?> * @package <?= $namespace ?>;<?php
} ?>

* @author <?= get_current_user() ?>
<email>

    * @version 0
    * @since 0
    */
    class <?= $validatorName ?> extends Validator
    {
    /**
    * Validate data by scheme
    *
    * @example:
    * 'user_name' => [
    * [
    * 'validator' => 'Ice:Not_Empty',
    * 'message' => 'Введите имя пользователя.'
    * ],
    * ],
    * 'name' => 'Ice:Not_Null'
    *
    * @param $data
    * @param null $scheme
    * @return boolean
    */
    public function validate(array $data, $name, array $params)
    {
    throw new \Ice\Core\Exception(['Implement validator {$0}', '<?= $validatorName ?>']);
    }
    }