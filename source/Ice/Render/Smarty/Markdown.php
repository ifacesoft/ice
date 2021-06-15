<?php

namespace Ice\Render;

use Michelf\MarkdownExtra;

class Smarty_Markdown extends Smarty
{
    const TEMPLATE_EXTENTION = '.tpl.md';

    public function fetch($template, array $data = [], $templateType = View_Render::TEMPLATE_TYPE_FILE)
    {
        return MarkdownExtra::defaultTransform(parent::fetch($template, $data, $templateType));
    }
}