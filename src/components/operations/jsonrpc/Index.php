<?php
namespace extas\components\operations\jsonrpc;

use extas\components\conditions\ConditionParameter;
use extas\components\expands\Expand;
use extas\components\api\jsonrpc\operations\OperationRunner;
use extas\interfaces\extensions\IExtensionJsonRpcIndex;
use extas\interfaces\IItem;
use extas\interfaces\jsonrpc\IRequest;
use extas\interfaces\operations\jsonrpc\IIndex;
use extas\interfaces\stages\IStageJsonRpcBeforeSelect;

/**
 * Class Index
 *
 * @package extas\components\api\jsonrpc
 * @author jeyroik@gmail.com
 */
class Index extends OperationRunner implements IIndex
{
    /**
     * @return array
     */
    public function run(): array
    {
        /**
         * @var IExtensionJsonRpcIndex|IRequest $request
         */
        $request = $this->getJsonRpcRequest();
        $records = $this->getOperation()->getItemRepository()->all(
            [],
            $request->getLimit(0),
            $request->getOffset(0),
            $this->convertIntoTableSort($request->getSort([]))
        );

        $items = $this->filter($request->getFilter(), $records);
        $items = $this->selectFields($items);
        $items = $this->expandItems($items);

        $asArray = [];

        foreach ($items as $item) {
            $asArray[] = $item->__toArray();
        }

        return [
            'items' => $asArray,
            'total' => count($asArray)
        ];
    }

    /**
     * @param array $sort
     * @return array
     */
    protected function convertIntoTableSort(array $sort): array
    {
        $keys = array_keys($sort);
        $firstKey = array_shift($keys);

        return [$firstKey, $sort[$firstKey]];
    }

    /**
     * @param $items
     * @return array
     */
    protected function expandItems($items): array
    {
        $expand = new Expand($this->getHttpIO());

        foreach ($items as $index => $item) {
            $items[$index] = $expand->expand($item);
        }

        return $items;
    }

    /**
     * @param IItem[] $items
     * @return IItem[]
     */
    protected function selectFields(array $items): array
    {
        /**
         * @var IExtensionJsonRpcIndex $request
         */
        $request = $this->getJsonRpcRequest();
        $select = $request->getSelect([]);

        if (empty($select)) {
            return $items;
        }

        foreach ($this->getPluginsByStage(IStageJsonRpcBeforeSelect::NAME, $this->getHttpIO()) as $plugin) {
            /**
             * @var IStageJsonRpcBeforeSelect $plugin
             */
            $select = $plugin($select, $items);
        }

        $valid = [];
        foreach ($items as $index => $item) {
            $this->selectItemFields($item, $select, $valid);
        }

        return $valid;
    }

    /**
     * @param IItem $item
     * @param array $select
     * @param array $items
     */
    protected function selectItemFields(IItem $item, array $select, array &$items): void
    {
        if ($item->has(...$select)) {
            $items[] = $item->__select($select);
        }
    }

    /**
     * @param array $filter
     * @param array $items
     *
     * @return array
     */
    protected function filter($filter, $items)
    {
        if (empty($filter)) {
            return $items;
        }

        $result = [];
        $conditions = [];

        foreach ($filter as $fieldName => $filterOptions) {
            $this->appendCondition($fieldName, $filterOptions, $conditions);
        }

        foreach ($items as $item) {
            $this->filterByConditions($item, $conditions, $result);
        }

        return $result;
    }

    /**
     * @param string $fieldName
     * @param array $filterOptions
     * @param array $conditions
     */
    protected function appendCondition(string $fieldName, array $filterOptions, array &$conditions): void
    {
        foreach ($filterOptions as $filterCompare => $filterValue) {
            $conditions[] = new ConditionParameter([
                ConditionParameter::FIELD__NAME => $fieldName,
                ConditionParameter::FIELD__CONDITION => str_replace('$', '', $filterCompare),
                ConditionParameter::FIELD__VALUE => $filterValue
            ]);
        }
    }

    /**
     * @param IItem $item
     * @param array $conditions
     * @param array $result
     */
    protected function filterByConditions(IItem $item, array $conditions, array &$result): void
    {
        $valid = true;
        foreach ($conditions as $condition) {
            if (!$condition->isConditionTrue($item[$condition->getName()] ?? null)) {
                $valid = false;
                break;
            }
        }

        $valid && ($result[] = $item);
    }

    /**
     * @return string
     */
    protected function getSubjectForExtension(): string
    {
        return 'extas.operation.jsonrpc.index';
    }
}
