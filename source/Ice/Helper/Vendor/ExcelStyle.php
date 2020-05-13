<?php

namespace Ice\Helper\Vendor;


class ExcelStyle
{
    const FONT = 'font';
    const FONT_BOLD = 'bold';
    const FONT_SIZE = 'size';
    const ALIGNMENT = 'alignment';
    const ALIGNMENT_HORIZONTAL = 'horizontal';
    const ALIGNMENT_VERTICAL = 'vertical';
    const ALIGNMENT_DIRECTION_CENTER = 'center';
    const ALIGNMENT_DIRECTION_LEFT = 'left';
    const ALIGNMENT_DIRECTION_RIGHT = 'right';
    const ALIGNMENT_DIRECTION_BOTTOM = 'bottom';
    const ALIGNMENT_DIRECTION_TOP = 'top';
    const ALIGNMENT_DIRECTION_JUSTIFY = 'justify';
    const ALIGNMENT_WRAPTEXT = 'wrapText';
    const ALIGNMENT_READORDER = 'readOrder';
    const BORDERS = 'borders';
    const BORDERS_ALLBORDERS = 'allBorders';

    private $styleArray = [];


    public static function build()
    {
        return new self();
    }

    public function fontBold($flag = true)
    {
        return $this->initField(self::FONT, self::FONT_BOLD, $flag);

    }

    public function fontSize($size = 12)
    {
        return $this->initField(self::FONT, self::FONT_SIZE, $size);
    }

    public function alignmentHorizontal($direction)
    {
        return $this->initField(self::ALIGNMENT, self::ALIGNMENT_HORIZONTAL, $this->getAlignmentDirection($direction));
    }

    public function alignmentVertical($direction)
    {
        return $this->initField(self::ALIGNMENT, self::ALIGNMENT_VERTICAL, $this->getAlignmentDirection($direction));
    }

    public function alignmentWrapText($value)
    {
        return $this->initField(self::ALIGNMENT, self::ALIGNMENT_WRAPTEXT, $value);
    }

    public function alignmentReadOrder($value)
    {
        return $this->initField(self::ALIGNMENT, self::ALIGNMENT_READORDER, $value);
    }

    public function bordersAllBorders($value)
    {
        return $this->initField(self::BORDERS, self::BORDERS_ALLBORDERS, ['borderStyle' => $value]);
    }

    private function initField($field1, $field2, $value)
    {
        if (!isset($this->styleArray[$field1])) {
            $this->styleArray[$field1] = [];
        }

        $this->styleArray[$field1] = array_merge(
            $this->styleArray[$field1],
            array_merge([
                $field2 => $value
            ])
        );
        return $this;
    }

    private function getAlignmentDirection($direction)
    {
        switch ($direction) {
            case 'right':
                $directionTo = self::ALIGNMENT_DIRECTION_RIGHT;
                break;
            case 'top':
                $directionTo = self::ALIGNMENT_DIRECTION_TOP;
                break;
            case 'center':
                $directionTo = self::ALIGNMENT_DIRECTION_CENTER;
                break;
            case 'bottom':
                $directionTo = self::ALIGNMENT_DIRECTION_BOTTOM;
                break;
            case 'left':
                $directionTo = self::ALIGNMENT_DIRECTION_LEFT;
                break;
            case 'justify':
                $directionTo = self::ALIGNMENT_DIRECTION_JUSTIFY;
                break;
            default:
                throw new \Ice\Exception\Error('No direction to alignment');
                break;
        }

        return $directionTo;
    }

    public function getStyle()
    {
        return $this->styleArray;
    }

}