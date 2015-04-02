<?php

namespace Ice\Core;

use Ice\Core;
use Ice\Ui\Menu\Pagination;

abstract class Ui_Data extends Container
{
    use Ui, Stored;

    /**
     * Not ignored fields
     *
     * @var array
     */
    protected $_filterFields = [];

    private $_title = '';
    private $_desc = '';
    private $columns = [];
    private $rows = [];
    private $_rowHeaderTemplate = 'Row_Header';
    private $_rowDataTemplate = 'Row_Data';

    /**
     * Filter form
     *
     * @var Ui_Form
     */
    private $filterForm = null;

    /**
     * Pagination menu
     *
     * @var Pagination
     */
    private $paginationMenu = null;

    private $_key = null;

    private function __construct()
    {
    }

    /**
     * Return instance of Data
     *
     * @param string $key
     * @param null $ttl
     * @return Ui_Data
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.2
     */
    public static function getInstance($key = null, $ttl = null)
    {
        return parent::getInstance($key, $ttl);
    }

    /**
     * Create new instance of data
     *
     * @param $key
     * @return Ui_Data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.6
     * @since 0.2
     */
    protected static function create($key)
    {
        /** @var Ui_Data $class */
        $class = self::getClass();
//
//        if ($key) {
//            $class .= '_' . $key;
//        }

        /** @var Ui_Data $class */
        $data = new $class();

        $data->_key = $key;

        return $data;
    }

    protected static function getDefaultClassKey()
    {
        return 'Ice:Table';
    }

    protected static function getDefaultKey()
    {
        return 'default';
    }

    /**
     * @param Query_Result|array $rows
     * @return $this
     */
    public function bind($rows)
    {
        if ($rows instanceof Query_Result) {
            if ($this->paginationMenu) {
                $this->paginationMenu->setFoundRows($rows->getFoundRows());
            }

            $rows = $rows->getRows();
        }

        $this->rows = $rows;

        return $this;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @return Ui_Data
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * Build column part
     *
     * @param $columnName
     * @param $columnTitle
     * @param null $options
     * @param string $template
     * @return Ui_Data
     */
    public function text($columnName, $columnTitle, $options = null, $template = 'Column_Column')
    {
        return $this->addColumn($columnName, $columnTitle, $options, $template);
    }

    protected function addColumn($columnName, $columnTitle, $options, $template)
    {
        $this->columns[] = [
            'name' => $columnName,
            'title' => $columnTitle,
            'options' => $options,
            'template' => $template
        ];

        return $this;
    }

    /**
     * Add accepted fields
     *
     * @param array $filterFields
     * @return Ui_Data
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public function addFilterFields(array $filterFields)
    {
        if (empty($filterFields)) {
            return $this;
        }

        $this->_filterFields = array_merge($this->_filterFields, $filterFields);
        return $this;
    }

    /**
     * Build link part
     *
     * @param $columnName
     * @param $columnTitle
     * @param null $options
     * @param string $template
     * @return Ui_Data
     */
    public function link($columnName, $columnTitle, $options = null, $template = 'Column_Link')
    {
        return $this->addColumn($columnName, $columnTitle, $options, $template);
    }

    /**
     * Build button part
     *
     * @param $columnName
     * @param $columnTitle
     * @param null $options
     * @param string $template
     * @return Ui_Data
     */
    public function button($columnName, $columnTitle, $options = null, $template = 'Column_Button')
    {
        return $this->addColumn($columnName, $columnTitle, $options, $template);
    }

    /**
     * @return array
     */
    public function getFilterFields()
    {
        return $this->_filterFields;
    }

    public static function schemeColumnPlugin($columnName, $table)
    {
        return 'text';
    }

    public function title($title)
    {
        $this->_title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @return string
     */
    public function getRowHeaderTemplate()
    {
        return $this->_rowHeaderTemplate;
    }

    /**
     * @param string $rowHeaderTemplate
     * @return Ui_Data
     */
    public function setRowHeaderTemplate($rowHeaderTemplate)
    {
        $this->_rowHeaderTemplate = $rowHeaderTemplate;
        return $this;
    }

    /**
     * @return string
     */
    public function getRowDataTemplate()
    {
        return $this->_rowDataTemplate;
    }

    /**
     * @param string $rowDataTemplate
     * @return Ui_Data
     */
    public function setRowDataTemplate($rowDataTemplate)
    {
        $this->_rowDataTemplate = $rowDataTemplate;
        return $this;
    }

    /**
     * @return string
     */
    public function getDesc()
    {
        return $this->_desc;
    }

    /**
     * @param string $desc
     * @return Ui_Data
     */
    public function desc($desc)
    {
        $this->_desc = $desc;
        return $this;
    }

    /**
     * @return Ui_Form
     */
    public function getFilterForm()
    {
        return $this->filterForm;
    }

    /**
     * @param Ui_Form $filter
     * @return Ui_Data
     */
    public function setFilterForm(Ui_Form $filter)
    {
        $this->filterForm = $filter;
        return $this;
    }

    /**
     * @return Pagination
     */
    public function getPaginationMenu()
    {
        return $this->paginationMenu;
    }

    /**
     * @param Pagination $paginationMenu
     * @return Ui_Data
     */
    public function setPaginationMenu(Pagination $paginationMenu)
    {
        $this->paginationMenu = $paginationMenu;
        return $this;
    }

    public function getParams()
    {
        $params = $this->getKey();

        if ($this->filterForm) {
            $params += $this->filterForm->getKey();
        }

        if ($this->paginationMenu) {
            $params += $this->paginationMenu->getKey();
        }

        return $params;
    }
}