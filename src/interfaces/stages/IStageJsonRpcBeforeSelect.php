<?php
namespace extas\interfaces\stages;

use extas\interfaces\http\IHasJsonRpcRequest;
use extas\interfaces\http\IHasJsonRpcResponse;
use extas\interfaces\IItem;

/**
 * Interface IStageJsonRpcBeforeSelect
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageJsonRpcBeforeSelect extends IHasJsonRpcRequest, IHasJsonRpcResponse
{
    public const NAME = 'extas.jsonrpc.before.select';

    /**
     * @param array $select
     * @param IItem[] $items
     * @return array
     */
    public function __invoke(array $select, array $items): array;
}
