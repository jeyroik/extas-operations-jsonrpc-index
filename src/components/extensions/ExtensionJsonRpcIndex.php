<?php
namespace extas\components\extensions;

use extas\interfaces\extensions\IExtensionJsonRpcIndex;
use extas\interfaces\jsonrpc\IRequest;

/**
 * Class ExtensionJsonRpcIndex
 *
 * @package extas\components\extensions
 * @author jeyroik <jeyroik@gmail.com>
 */
class ExtensionJsonRpcIndex extends Extension implements IExtensionJsonRpcIndex
{
    /**
     * @param int $default
     * @param IRequest|null $request
     * @return int
     */
    public function getLimit(int $default, IRequest $request = null): int
    {
        return $this->getParam(static::FIELD__PARAMS_LIMIT, $request, $default);
    }

    /**
     * @param int $default
     * @param IRequest|null $request
     * @return int
     */
    public function getOffset(int $default, IRequest $request = null): int
    {
        return $this->getParam(static::FIELD__PARAMS_OFFSET, $request, $default);
    }

    /**
     * @param array $default
     * @param IRequest|null $request
     * @return array|null
     */
    public function getSelect(array $default, IRequest $request = null): array
    {
        return $this->getParam(static::FIELD__PARAMS_SELECT, $request, $default);
    }

    /**
     * @param array $default
     * @param IRequest|null $request
     * @return array|null
     */
    public function getSort(array $default, IRequest $request = null): array
    {
        return $this->getParam(static::FIELD__PARAMS_SORT, $request, $default);
    }

    /**
     * @param string $name
     * @param IRequest $request
     * @param null $default
     * @return mixed|null
     */
    protected function getParam(string $name, IRequest $request, $default = null)
    {
        $params = $request->getParams();

        return $params[$name] ?? $default;
    }
}
