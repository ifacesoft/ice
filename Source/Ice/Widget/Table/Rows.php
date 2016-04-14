<?php
namespace Ice\Widget;

use Ice\Action\Render;
use Ice\Core\Debuger;
use Ice\Core\QueryBuilder;
use Ice\Core\QueryResult;
use Ice\Core\Widget;
use Ice\WidgetComponent\HtmlTag;
use Ice\WidgetComponent\TableCell_A;
use Ice\WidgetComponent\TableCell_OneToMany;
use Ice\WidgetComponent\TableCell;
use Ice\WidgetComponent\TableCell_Span;

class Table_Rows extends Widget
{
    private $isShowCount = true;
    private $columnCount = 0;

    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => 'Widget: Access denied!'],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [],
            'output' => [],
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     */
    protected function build(array $input)
    {
        return [];
    }

    /**
     * @return boolean
     */
    public function isShowCount()
    {
        return $this->isShowCount;
    }

    /**
     * @param boolean $isShowCount
     * @return Table_Rows
     */
    public function setShowCount($isShowCount)
    {
        $this->isShowCount = $isShowCount;

        return $this;
    }

    protected function getCompiledResult()
    {
        return array_merge(
            parent::getCompiledResult(),
            [
                'isShowCount' => $this->isShowCount(),
                'columnCount' => $this->getColumnCount()
            ]
        );
    }

    /**
     * @return int
     */
    public function getColumnCount()
    {
        return $this->columnCount ? $this->columnCount : count($this->getParts());
    }

    /**
     * @param int $columnCount
     * @return Table_Rows
     */
    public function setColumnCount($columnCount)
    {
        $this->columnCount = $columnCount;
        return $this;
    }

    public function setQueryResult(QueryResult $queryResult)
    {
        parent::setQueryResult($queryResult);

        $queryBuilder = $queryResult->getQuery()->getQueryBuilder();

        $limitQueryPart = $queryBuilder->getSqlParts()[QueryBuilder::PART_LIMIT];

        $limit = isset($this->getDataParams()['limit'])
            ? $this->getDataParams()['limit']
            : $limitQueryPart['limit'];

        $offset = isset($this->getDataParams()['page'])
            ? $limit * ($this->getDataParams()['page'] - 1)
            : $limitQueryPart['offset'];

        if ($limit && $limit < $queryResult->getNumRows()) {
            $this->setRows(array_slice($queryResult->getRows(), $offset, $limit));
        } else {
            $this->setRows($queryResult->getRows());
        }

        $this->setOffset($offset);
    }

    /**
     * Build a tag part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return $this
     */
    public function a($columnName, array $options = [], $template = null)
    {
        return $this->addPart(new TableCell_A($columnName, $options, $template, $this));
    }

    /**
     * Build a tag part
     *
     * @param $columnName
     * @param  array $options
     * @param string $template
     * @return $this
     */
    public function text($columnName, array $options = [], $template = null)
    {
        return $this->span($columnName, $options);
    }

    /**
     * Build span tag part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return $this
     */
    public function span($columnName, array $options = [], $template = null)
    {
        if (isset($options['route']) || isset($options['href'])) {
            return $this->a($columnName, $options);
        }

        return $this->addPart(new TableCell_Span($columnName, $options, $template, $this));
    }

    /**
     * Build checkbox tag part
     *
     * @param  $columnName
     * @param  array $options
     * @return $this
     */
    public function checkbox($columnName, array $options = [])
    {
        return $this->span($columnName, $options);
    }

    public function number($columnName, array $options = [])
    {
        return $this->span($columnName, $options);
    }

    public function date($columnName, array $options = [])
    {
        return $this->span($columnName, $options);
    }

    public function oneToMany($columnName, array $options = [])
    {
        return $this->span($columnName, $options);
    }
}