<?php
use Ice\Core\View;

?><?= '<?php' ?>

<?php if ($namespace) { ?>namespace <?= $namespace ?>;<?php
} ?>


use Ice\Core\Action;
use Ice\Core\Action_Context;

/**
* Class <?= $actionName ?>

*
* @see Ice\Core\Action
* @see Ice\Core\Action_Context;
<?php if ($namespace) : ?>
    * @package <?= $namespace ?>;
<?php endif; ?>

* @author <?= get_current_user() ?>
<email>

    * @version 0
    * @since 0
    */
    class <?= $actionName ?> extends Action
    {
    /** public static $config = [
    * 'afterActions' => [], // actions
    * 'layout' => null, // Emmet style layout
    * 'template' => null, // Template of view
    * 'output' => null, // Output type: standard|file
    * 'defaultViewRenderClassName' => null, // Render class for view (example: Ice:Php)
    * 'inputDefaults' => [], // Default input data
    * 'inputValidators' => [], // Input data validators
    * 'inputDataProviderKeys' => [], // InputDataProviders keys
    * 'outputDataProviderKeys' => [], // OutputDataProviders keys
    * 'cacheDataProviderKey' => '' // Cache data provider key
    * ];
    */
    public static $config = [
    'defaultViewRenderClassName' => '<?= View::getConfig()->get('defaultViewRenderClassName') ?>',
    <?php if (php_sapi_name() == 'cli') { ?>'template' => ''<?php
    } ?>

    ];

    /**
    * Run action
    *
    * @param array $input
    * @param Action_Context $actionContext
    * @return array
    */
    public function run(array $input)
    {
    return ['errors' => 'Need implement run() method of action class <?= $actionName ?>.'];
    }
    }