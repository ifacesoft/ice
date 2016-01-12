<?php
namespace Ice\Widget;

use Ice\Action\Render;
use Ice\Core\Debuger;
use Ice\Core\Query_Builder;
use Ice\Core\Query_Result;
use Ice\Core\Widget;

class Roll extends Widget
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
            'render' => ['template' => __CLASS__, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
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

    public function setQueryResult(Query_Result $queryResult)
    {
        parent::setQueryResult($queryResult);

        $queryBuilder = $queryResult->getQuery()->getQueryBuilder();

        $limitQueryPart = $queryBuilder->getSqlParts()[Query_Builder::PART_LIMIT];

//        Debuger::dump($this->getDataParams());

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
    public function a($columnName, array $options = [], $template = 'Ice\Widget\Table\Rows\A')
    {
        return $this->addPart($columnName, $options, $template, __FUNCTION__);
    }

    /**
     * Build span tag part
     *
     * @param  $columnName
     * @param  array $options
     * @param  string $template
     * @return $this
     */
    public function span($columnName, array $options = [], $template = 'Ice\Widget\Table\Rows\Span')
    {
        return $this->addPart($columnName, $options, $template, __FUNCTION__);
    }
}