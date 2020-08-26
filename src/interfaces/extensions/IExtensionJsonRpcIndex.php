<?php
namespace extas\interfaces\extensions;

/**
 * Interface IExtensionJsonRpcIndex
 *
 * @package extas\interfaces\extensions
 * @author jeyroik <jeyroik@gmail.com>
 */
interface IExtensionJsonRpcIndex
{
    public const FIELD__PARAMS_LIMIT = 'limit';
    public const FIELD__PARAMS_SELECT = 'select';
    public const FIELD__PARAMS_OFFSET = 'offset';
    public const FIELD__PARAMS_SORT = 'sort';

    /**
     * @param int $default
     * @return int
     */
    public function getLimit(int $default): int;

    /**
     * @param int $default
     * @return int
     */
    public function getOffset(int $default): int;

    /**
     * @param array $default
     * @return array
     */
    public function getSelect(array $default): array;

    /**
     * @param array $default
     * @return array
     */
    public function getSort(array $default): array;
}
