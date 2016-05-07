<?php
/**
 * Created by PhpStorm.
 * User: dp
 * Date: 19.04.16
 * Time: 16:08
 */

namespace Ice\WidgetComponent;

use Ice\Core\Debuger;
use Ice\Core\Render;
use Ice\Core\Resource;
use Ice\Exception\Error;
use Ice\Render\Replace;

class Form_ListBox extends FormElement_TextInput
{
    public function getItemKey()
    {
        $itemKey = $this->getOption('itemKey');

        if (!$itemKey) {
            throw new Error(['Option itemKey for component {$0} not found', $this->getComponentName()]);
        }

        return $itemKey;
    }

    public function getItemTitle($item = null)
    {
        $itemTitle = $this->getOption('itemTitle');

        if (!$itemTitle) {
            throw new Error(['Option itemTitle for component {$0} not found', $this->getComponentName()]);
        }

        if ($item === null) {
            return $itemTitle;
        }

        $resourceClass = $this->getOption('itemTitleResource', null);

        if ($resourceClass === null) {
            $resourceClass = $this->getOption('itemTitleHardResource', null);
        }

        $template = null;

        if ($resourceClass) {
            $template = $itemTitle;

            if ($this->getOption('itemTitleHardResource', null)) {
                $template .= '_' . $item[$itemTitle];
            }
        }

        /** @var Resource $resource */
        $resource = $resourceClass === true
            ? $this->getResource()
            : ($resourceClass === null ? $resourceClass : Resource::create($resourceClass));

        if ($template) {
            $title = $resource
                ? $resource->get($template, $item)
                : Replace::getInstance()->fetch($template, $item, null, Render::TEMPLATE_TYPE_STRING);

        } else {
            $title = $item[$itemTitle];
        }

        return htmlentities($title);
    }

    /**
     * @return null
     */
    public function getItems()
    {
        return $this->getOption('required', false) === false
            ? [[$this->getItemKey() => null, $this->getItemTitle() => '']] + $this->getOption('rows', [])
            : $this->getOption('rows', []);
    }
}