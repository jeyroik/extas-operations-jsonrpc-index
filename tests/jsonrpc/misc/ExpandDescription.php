<?php
namespace tests\jsonrpc\misc;

use extas\components\plugins\Plugin;
use extas\interfaces\expands\IExpand;
use extas\interfaces\IItem;
use extas\interfaces\stages\IStageExpand;

/**
 * Class ExpandDescription
 *
 * @package tests\jsonrpc\misc
 * @author jeyroik <jeyroik@gmail.com>
 */
class ExpandDescription extends Plugin implements IStageExpand
{
    /**
     * @param IItem $subject
     * @param IExpand $expand
     * @return IItem
     */
    public function __invoke(IItem $subject, IExpand $expand): IItem
    {
        $subject['description'] = 'long long description';

        return $subject;
    }
}
