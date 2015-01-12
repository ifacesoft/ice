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
 *
<?php if ($namespace) { ?>
 * @package <?= $namespace ?>
<?php
}?>
 * @author <?= get_current_user() ?> <email>
 *
 * @version 0
 * @since 0
 */
class <?= $modelName ?> extends Model {

}