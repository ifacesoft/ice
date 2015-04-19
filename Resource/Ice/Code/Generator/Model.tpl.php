<?= '<?php' ?> <?php if ($namespace) { ?>namespace <?= $namespace ?>;<?php
} ?>


use Ice\Core\Model;

/**
* Class <?= $modelName ?>

*
<?php foreach ($fields as $field) { ?>
    * @property mixed <?= $field ?>

    <?php
} ?>
*
* @see Ice\Core\Model
*
<?php if ($namespace) { ?>
    * @package <?= $namespace ?>
    <?php
} ?>

*/
class <?= $modelName ?> extends Model
{
protected static function config()
{
return <?= $config ?>

}
}