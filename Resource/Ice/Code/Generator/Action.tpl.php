<?php
use Ice\Core\View;
?><?= '<?php' ?>

<?php if ($namespace) {?>namespace <?=$namespace?>;<?php
}?>


use Ice\Core\Action;
use Ice\Core\Action_Context;
use Ice\Core\Exception;
use Ice\View\Render\Php;

/**
 * Class <?=$actionName?>

 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context;
<?php if ($namespace) {?> * @package <?=$namespace?>;<?php
}?>

 * @author <?=get_current_user()?>

 * @version stable_0
 */
class <?=$actionName?> extends Action
{
    /**  public static $config = [
     *      'staticActions' => [],          // actions
     *      'layout' => null,               // Emmet style layout
     *      'template' => null,             // Template of view
     *      'output' => null,               // Output type: standart|file
     *      'viewRenderClassName' => null,  // Render class for view (example: Ice:Php)
     *      'inputDefaults' => [],          // Default input data
     *      'inputValidators' => [],        // Input data validators
     *      'inputDataProviderKeys' => [],  // InputDataProviders keys
     *      'outputDataProviderKeys' => [], // OutputDataProviders keys
     *      'cacheDataProviderKey' => ''    // Cache data provider key
     *  ];
     */
    public static $config = [
        'viewRenderClassName' => '<?= View::getConfig()->get('defaultViewRenderClassName') ?>',
        <?php if (php_sapi_name() == 'cli') {?>'template' => ''<?php
}?>

    ];

    /**
     * Run action
     *
     * @param array $input
     * @param Action_Context $actionContext
     * @return array
     */
    protected function run(array $input, Action_Context $actionContext)
    {
        return ['errors' => 'Implement run() method of action class <?=$actionName?>.'];
    }
}