<?php

namespace Ice\WidgetComponent;

use Ice\Core\Render;
use Ice\Core\Resource;
use Ice\Helper\Type_String;
use Ice\Render\Replace;

class Form_ListBox extends FormElement_TextInput
{
    protected static function config()
    {
        $config = parent::config();

        $config['render']['template'] = __CLASS__;

        return $config;
    }

    public function getTitle($item = null)
    {
        $itemTitle = $this->getItemTitle();

        if ($item === null) {
            return $itemTitle;
        }

        if (is_array($itemTitle)) {
            $itemTitle = reset($itemTitle);
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
            if (array_filter($item)) {
                $title = $resource
                    ? $resource->get($template, $item)
                    : Replace::getInstance()->fetch($template, $item, null, Render::TEMPLATE_TYPE_STRING);
            } else {
                $title = $this->getItemEmpty();
            }
        } else {
            $title = $item[$itemTitle];
        }

        if ($truncate = $this->getOption('itemTitleTruncate')) {
            $title = Type_String::truncate($title, $truncate);
        }

        return htmlentities($title);
    }

    public function getItemTitle()
    {
        return $this->getOption('itemTitle', 'itemTitle');
    }

    public function getItemFilter()
    {
        return $this->getOption('itemFilter', []);
    }

    /**
     * @param array $fieldNames
     * @param array $filter
     * @return array
     */
    public function getItems($fieldNames = [], $filter = [])
    {
        $items = $this->getOption('items', []);

        if ($this->getOption('required', false) === false) {
            $itemKey = (array) $this->getItemKey();
            $itemTitle = (array) $this->getItemTitle();

            return [[reset($itemKey) => '', reset($itemTitle) => '']] + $items;
        }

        return $items;
    }

    public function getItemKey()
    {
        return $this->getOption('itemKey', 'itemKey');
    }

    public function getItemEmpty()
    {
        return $this->getOption('itemEmpty', ' — ');
    }
}