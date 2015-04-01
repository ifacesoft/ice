<?php
namespace Ice\Action;

use Ice\Core;
use Ice\Core\Action;
use Ice\Core\Ui_Data;
use Ice\Helper\Arrays;
use Ice\Helper\Emmet;
use Ice\View\Render\Php;

/**
 * Class Data_Table
 *
 * @see Ice\Core\Action
 * @see Ice\Core\Action_Context;
 *
 * @package Ice
 * @subpackage Action
 *
 * @version 0.2
 * @since 0.1
 */
class Data extends Action
{
    /**
     * Action config
     *
     * example:
     * ```php
     *  $config = [
     *      'actions' => [
     *          ['Ice:Title', ['title' => 'page title'], 'title'],
     *          ['Ice:Another_Action, ['param' => 'value']
     *      ],
     *      'view' => [
     *          'layout' => Emmet::PANEL_BODY,
     *          'template' => _Custom,
     *          'viewRenderClass' => Ice:Twig,
     *      ],
     *      'input' => [
     *          Request::DEFAULT_DATA_PROVIDER_KEY => [
     *              'paramFromGETorPOST => [
     *                  'default' => 'defaultValue',
     *                  'validators' => ['Ice:PATTERN => PATTERN::LETTERS_ONLY]
     *                  'type' => 'string'
     *              ]
     *          ]
     *      ],
     *      'output' => ['Ice:Resource/Ice\Action\Index'],
     *      'ttl' => 3600,
     *      'roles' => []
     *  ];
     * ```
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since 0
     */
    protected static function config()
    {
        return [
            'view' => ['viewRenderClass' => 'Ice:Php', 'layout' => Emmet::PANEL_BODY],
            'input' => [
                'data' => ['validators' => 'Ice:Is_Ui_Data']
            ]
        ];
    }

    /**
     * Run action
     *
     * @param array $input
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public function run(array $input)
    {
        /** @var Ui_Data $data */
        $data = $input['data'];

        /** @var Ui_Data $dataClass */
        $dataClass = get_class($data);

        $rows = [];

        $columns = $data->getColumns();

        $filterFields = $data->getFilterFields();

        $columnNames = Arrays::column($columns, 'name');

        if (!empty($filterFields)) {
            $columnNames = array_intersect($columnNames, $filterFields);
        }

        $rows[] = Php::getInstance()->fetch(
            Ui_Data::getClass($dataClass . '_' . $data->getRowHeaderTemplate()),
            ['columns' => array_intersect_key($columns, $columnNames)]
        );

        $offset = $data->getOffset();

        foreach ($data->getRows() as $row) {
            $rowResult = [];

            foreach ($columns as $column) {
                if (!empty($filterFields) && !in_array($column['name'], $filterFields)) {
                    continue;
                }

                if (isset($column['options']['href']) && isset($column['options']['href_ext'])) {
                    $column['options']['href'] .= implode('/', array_intersect_key($row, array_flip((array)$column['options']['href_ext'])));
                }

                $rowResult[] = Php::getInstance()->fetch(
                    Ui_Data::getClass($dataClass . '_' . $column['template']),
                    [
                        'value' => array_key_exists($column['name'], $row) ? $row[$column['name']] : $column['name'],
                        'options' => $column['options'],
                    ]
                );
            }

            $rows[] = Php::getInstance()->fetch(
                Ui_Data::getClass($dataClass . '_' . $data->getRowDataTemplate()),
                [
                    'columns' => $rowResult,
                    'id' => ++$offset
                ]
            );
        }

        if ($data->getFilterForm()) {
            $this->addAction(['Ice:Form' => 'filter', ['form' => $data->getFilterForm()]]);
        }

        if ($data->getPaginationMenu()) {
            $this->addAction(['Ice:Menu' => 'pagination', ['menu' => $data->getPaginationMenu()]]);
        }

        return [
            'title' => $data->getTitle(),
            'desc' => $data->getDesc(),
            'ui_data' => Php::getInstance()->fetch(
                Ui_Data::getClass($dataClass),
                [
                    'rows' => $rows,
                    'classes' => $data->getClasses(),
                    'style' => $data->getStyle()
                ]
            )
        ];
    }
}