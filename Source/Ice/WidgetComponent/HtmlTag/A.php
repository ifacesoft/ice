<?php

namespace Ice\WidgetComponent;


class HtmlTag_A extends HtmlTag
{
    public function getHtmlTagAttributes()
    {
        $htmlTagAttributes = parent::getHtmlTagAttributes();

        if ($target = $this->getOption('target', null)) {
            if ($htmlTagAttributes) {
                $htmlTagAttributes .= ' ';
            }

            $htmlTagAttributes .= 'target"=' . $target . '"';
        }
        
        return $htmlTagAttributes;
    }
}