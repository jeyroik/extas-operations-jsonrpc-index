<?php
namespace extas\interfaces\stages;

use extas\interfaces\IItem;

/**
 * Interface IStageJsonRpcAfterIndex
 *
 * @package extas\interfaces\stages
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IStageJsonRpcBeforeIndexResponse
{
    public const NAME = 'extas.json.rpc.before.index.response';

    /**
     * @param IItem[] $items
     * @return array
     */
    public function __invoke(array $items): array;
}
