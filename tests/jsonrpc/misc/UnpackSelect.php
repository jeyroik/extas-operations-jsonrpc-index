<?php
namespace tests\jsonrpc\misc;

use extas\components\http\THasJsonRpcRequest;
use extas\components\http\THasJsonRpcResponse;
use extas\components\plugins\Plugin;
use extas\interfaces\IItem;
use extas\interfaces\stages\IStageJsonRpcBeforeSelect;

/**
 * Class UnpackSelect
 *
 * @package tests\jsonrpc\misc
 * @author jeyroik <jeyroik@gmail.com>
 */
class UnpackSelect extends Plugin implements IStageJsonRpcBeforeSelect
{
    use THasJsonRpcRequest;
    use THasJsonRpcResponse;

    /**
     * @param array $select
     * @param IItem[] $items
     * @return array
     */
    public function __invoke(array $select, array $items): array
    {
        if (in_array('*', $select)) {
            $select = [];
            foreach ($items as $item) {
                $select = array_merge($select, array_diff(array_keys($item->__toArray()), $select));
            }
        }

        return $select;
    }
}
