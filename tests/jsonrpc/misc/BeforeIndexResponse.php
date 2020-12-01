<?php
namespace tests\jsonrpc\misc;

use extas\components\http\THasJsonRpcRequest;
use extas\components\http\THasJsonRpcResponse;
use extas\components\plugins\Plugin;
use extas\interfaces\IItem;
use extas\interfaces\stages\IStageJsonRpcBeforeIndexResponse;

/**
 * Class BeforeIndexResponse
 *
 * @package tests\jsonrpc\misc
 * @author jeyroik <jeyroik@gmail.com>
 */
class BeforeIndexResponse extends Plugin implements IStageJsonRpcBeforeIndexResponse
{
    use THasJsonRpcResponse;
    use THasJsonRpcRequest;

    /**
     * @param IItem[] $items
     * @return array
     */
    public function __invoke(array $items): array
    {
        foreach ($items as $index => $item) {
            $items[$index]['general'] = true;
        }

        return $items;
    }
}
