<?php
namespace extas\interfaces\stages;

use extas\interfaces\IItem;
use extas\interfaces\http\IHasJsonRpcRequest;
use extas\interfaces\http\IHasJsonRpcResponse;

/**
 * Interface IStageJsonRpcAfterIndex
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageJsonRpcBeforeIndexResponse extends IHasJsonRpcRequest, IHasJsonRpcResponse
{
    public const NAME = 'extas.json.rpc.before.index.response';

    /**
     * @param IItem[] $items
     * @return array
     */
    public function __invoke(array $items): array;
}
