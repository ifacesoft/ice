<?= '<?php' ?>


<?php if ($namespace) { ?>namespace <?= $namespace ?>;<?php
} ?>


use Ice\Core\Action;

/**
 * Class <?= $actionName ?>

 *
 * @see Ice\Core\Action
<?php if ($namespace) : ?>
 * @package <?= $namespace ?>;
<?php endif; ?>
 *
 * @author <?= get_current_user() ?> <email>
 * @version 0
 * @since 0
 */
class <?= $actionName ?> extends Action
{
    /**
     * Action config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'view' => ['viewRenderClass' => '<?= $defaultViewRenderClass ?>'],
            'actions' => [],
            'input' => [],
            'output' => [],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'access' => [
                'roles' => [],
                'request' => null,
                'env' => null
            ]
        ];
    }

    /** Run action
     *
     * @param array $input
     * @return array
     */
    public function run(array $input)
    {

    }
}