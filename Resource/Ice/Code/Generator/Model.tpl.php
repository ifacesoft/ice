<?= '<?php' ?>

<?php if ($namespace) { ?>namespace <?= $namespace ?>;<?php
}?>


use Ice\Core\Model;

/**
 * Class <?= $modelName ?>

 *
<?php foreach ($fields as $field) { ?>
 * @property mixed <?= $field ?>

<?php
}?>
 *
 * @see Ice\Core\Model
<?php if ($namespace) { ?>
 * @package <?= $namespace ?>;
<?php
}?>
 * @author <?= get_current_user() ?>

 * @version stable_0
 */
class <?= $modelName ?> extends Model {

}