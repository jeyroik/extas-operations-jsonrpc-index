<?php
namespace tests\jsonrpc;

use Dotenv\Dotenv;
use extas\components\conditions\Condition;
use extas\components\conditions\TSnuffConditions;
use extas\components\expands\Box;
use extas\components\extensions\Extension;
use extas\components\extensions\ExtensionJsonRpcIndex;
use extas\components\http\TSnuffHttp;
use extas\components\items\SnuffItem;
use extas\components\jsonrpc\Request;
use extas\components\operations\jsonrpc\Index;
use extas\components\operations\JsonRpcOperation;
use extas\components\plugins\expands\PluginExpand;
use extas\components\plugins\Plugin;
use extas\components\plugins\TSnuffPlugins;
use extas\components\repositories\TSnuffRepositoryDynamic;
use extas\components\THasMagicClass;
use extas\interfaces\extensions\IExtensionJsonRpcIndex;
use extas\interfaces\http\IHasHttpIO;
use extas\interfaces\jsonrpc\IRequest;
use extas\interfaces\samples\parameters\ISampleParameter;
use extas\interfaces\stages\IStageJsonRpcBeforeIndexResponse;
use extas\interfaces\stages\IStageJsonRpcBeforeSelect;
use PHPUnit\Framework\TestCase;
use tests\jsonrpc\misc\BeforeIndexResponse;
use tests\jsonrpc\misc\BeforeIndexResponseCustom;
use tests\jsonrpc\misc\ExpandDescription;
use tests\jsonrpc\misc\UnpackSelect;

/**
 * Class IndexTest
 *
 * @package tests\jsonrpc
 * @author jeyroik <jeyroik@gmail.com>
 */
class IndexTest extends TestCase
{
    use TSnuffHttp;
    use TSnuffRepositoryDynamic;
    use THasMagicClass;
    use TSnuffPlugins;
    use TSnuffConditions;

    protected function setUp(): void
    {
        parent::setUp();
        $env = Dotenv::create(getcwd() . '/tests/');
        $env->load();
        $this->createSnuffDynamicRepositories([
            ['snuffRepo', 'name', SnuffItem::class],
            ['expandBoxes', 'name', Box::class],
            ['conditions', 'name', Condition::class],
        ]);
        $this->getMagicClass('expandBoxes')->create(new Box([
            Box::FIELD__NAME => 'snuff.item.description',
            Box::FIELD__ALIASES => ['snuff.item.description', 'snuff.item']
        ]));
        $this->createWithSnuffRepo('pluginRepository', new Plugin([
            Plugin::FIELD__CLASS => PluginExpand::class,
            Plugin::FIELD__STAGE => 'extas.expand.@expand'
        ]));

        $this->createWithSnuffRepo('extensionRepository', new Extension([
            Extension::FIELD__CLASS => ExtensionJsonRpcIndex::class,
            Extension::FIELD__INTERFACE => IExtensionJsonRpcIndex::class,
            Extension::FIELD__SUBJECT => 'extas.jsonrpc.request',
            Extension::FIELD__METHODS => [
                'getLimit', 'getOffset', 'getSort', 'getSelect'
            ]
        ]));
        $this->createSnuffCondition('like');
    }

    protected function tearDown(): void
    {
        $this->deleteSnuffDynamicRepositories();
    }

    public function testIndexMethods()
    {
        /**
         * @var IExtensionJsonRpcIndex|IRequest $request
         */
        $request = new Request([
            Request::FIELD__PARAMS => [
                IExtensionJsonRpcIndex::FIELD__PARAMS_LIMIT => 10,
                IExtensionJsonRpcIndex::FIELD__PARAMS_SORT => ['id' => 1],
                IExtensionJsonRpcIndex::FIELD__PARAMS_SELECT => ['name'],
                IExtensionJsonRpcIndex::FIELD__PARAMS_OFFSET => 5
            ]
        ]);

        $this->assertEquals(10, $request->getLimit(0));
        $this->assertEquals(['id' => 1], $request->getSort([]));
        $this->assertEquals(['name'], $request->getSelect([]));
        $this->assertEquals(5, $request->getOffset(0));
    }

    public function testIndexOperation()
    {
        $this->createSnuffItems();
        $this->createSnuffPlugin(ExpandDescription::class, ['extas.expand.snuff.item.description']);
        $this->createSnuffPlugin(UnpackSelect::class, [IStageJsonRpcBeforeSelect::NAME]);

        $operation = new Index([
            Index::FIELD__OPERATION => new JsonRpcOperation([
                JsonRpcOperation::FIELD__PARAMETERS => [
                    JsonRpcOperation::PARAM__ITEM_REPOSITORY => [
                        ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_REPOSITORY,
                        ISampleParameter::FIELD__VALUE => 'snuffRepo'
                    ],
                    JsonRpcOperation::PARAM__ITEM_CLASS => [
                        ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_CLASS,
                        ISampleParameter::FIELD__VALUE => SnuffItem::class
                    ],
                    JsonRpcOperation::PARAM__ITEM_NAME => [
                        ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_NAME,
                        ISampleParameter::FIELD__VALUE => 'snuff.item'
                    ],
                    JsonRpcOperation::PARAM__METHOD => [
                        ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__METHOD,
                        ISampleParameter::FIELD__VALUE => 'index'
                    ]
                ]
            ])
        ]);

        $result = $operation([
            IHasHttpIO::FIELD__PSR_REQUEST => $this->getPsrRequest(),
            IHasHttpIO::FIELD__PSR_RESPONSE => $this->getPsrResponse(),
            IHasHttpIO::FIELD__ARGUMENTS => [
                'version' => 0,
                'expand' => ['snuff.item.description']
            ],
        ]);

        $this->assertEquals(
            [
                'items' => [
                    [
                        'name' => 'test_2',
                        'value' => 'is ok again',
                        'expand' => ['snuff.item.description'],
                        'description' => 'long long description'
                    ]
                ],
                'total' => 1
            ],
            $result,
            'Incorrect result: ' . print_r($result, true)
        );
    }

    public function testBeforeIndexResponsePlugins()
    {
        $this->createSnuffItems();
        $this->createSnuffPlugin(ExpandDescription::class, ['extas.expand.snuff.item.description']);
        $this->createSnuffPlugin(UnpackSelect::class, [IStageJsonRpcBeforeSelect::NAME]);
        $this->createSnuffPlugin(BeforeIndexResponse::class, [IStageJsonRpcBeforeIndexResponse::NAME]);
        $this->createSnuffPlugin(BeforeIndexResponseCustom::class, [IStageJsonRpcBeforeIndexResponse::NAME . '.test']);

        $operation = new Index([
            Index::FIELD__OPERATION => new JsonRpcOperation([
                JsonRpcOperation::FIELD__NAME => 'test',
                JsonRpcOperation::FIELD__PARAMETERS => [
                    JsonRpcOperation::PARAM__ITEM_REPOSITORY => [
                        ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_REPOSITORY,
                        ISampleParameter::FIELD__VALUE => 'snuffRepo'
                    ],
                    JsonRpcOperation::PARAM__ITEM_CLASS => [
                        ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_CLASS,
                        ISampleParameter::FIELD__VALUE => SnuffItem::class
                    ],
                    JsonRpcOperation::PARAM__ITEM_NAME => [
                        ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_NAME,
                        ISampleParameter::FIELD__VALUE => 'snuff.item'
                    ],
                    JsonRpcOperation::PARAM__METHOD => [
                        ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__METHOD,
                        ISampleParameter::FIELD__VALUE => 'index'
                    ]
                ]
            ])
        ]);

        $result = $operation([
            IHasHttpIO::FIELD__PSR_REQUEST => $this->getPsrRequest(),
            IHasHttpIO::FIELD__PSR_RESPONSE => $this->getPsrResponse(),
            IHasHttpIO::FIELD__ARGUMENTS => [
                'version' => 0,
                'expand' => ['snuff.item.description']
            ],
        ]);

        $this->assertEquals(
            [
                'items' => [
                    [
                        'name' => 'test_2',
                        'general' => true,
                        'custom' => true,
                        'value' => 'is ok again',
                        'expand' => ['snuff.item.description'],
                        'description' => 'long long description'
                    ]
                ],
                'total' => 1
            ],
            $result,
            'Incorrect result: ' . print_r($result, true)
        );
    }

    public function testEmptySelectAndFilter()
    {
        $this->createSnuffItems();
        $this->createSnuffPlugin(ExpandDescription::class, ['extas.expand.snuff.item.description']);

        $operation = new Index([
            Index::FIELD__OPERATION => new JsonRpcOperation([
                JsonRpcOperation::FIELD__PARAMETERS => [
                    JsonRpcOperation::PARAM__ITEM_REPOSITORY => [
                        ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_REPOSITORY,
                        ISampleParameter::FIELD__VALUE => 'snuffRepo'
                    ],
                    JsonRpcOperation::PARAM__ITEM_CLASS => [
                        ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_CLASS,
                        ISampleParameter::FIELD__VALUE => SnuffItem::class
                    ],
                    JsonRpcOperation::PARAM__ITEM_NAME => [
                        ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__ITEM_NAME,
                        ISampleParameter::FIELD__VALUE => 'snuff.item'
                    ],
                    JsonRpcOperation::PARAM__METHOD => [
                        ISampleParameter::FIELD__NAME => JsonRpcOperation::PARAM__METHOD,
                        ISampleParameter::FIELD__VALUE => 'index'
                    ]
                ]
            ])
        ]);

        $result = $operation([
            IHasHttpIO::FIELD__PSR_REQUEST => $this->getPsrRequest('.empty'),
            IHasHttpIO::FIELD__PSR_RESPONSE => $this->getPsrResponse(),
            IHasHttpIO::FIELD__ARGUMENTS => [
                'version' => 0,
                'expand' => ['snuff.item.description']
            ],
        ]);

        $this->assertEquals(
            [
                'items' => [
                    [
                        'name' => 'x_it?',
                        'value' => 'is ok again',
                        'expand' => ['snuff.item.description'],
                        'description' => 'long long description'
                    ],
                    [
                        'name' => 'test_3',
                        'expand' => ['snuff.item.description'],
                        'description' => 'long long description'
                    ],
                    [
                        'name' => 'test_2',
                        'value' => 'is ok again',
                        'expand' => ['snuff.item.description'],
                        'description' => 'long long description'
                    ]
                ],
                'total' => 3
            ],
            $result,
            'Incorrect result: ' . print_r($result, true)
        );
    }

    protected function createSnuffItems()
    {
        $this->getMagicClass('snuffRepo')->create(new SnuffItem([
            'name' => 'test',
            'value' => 'is ok'
        ]));
        $this->getMagicClass('snuffRepo')->create(new SnuffItem([
            'name' => 'test_2',
            'value' => 'is ok again'
        ]));
        $this->getMagicClass('snuffRepo')->create(new SnuffItem([
            'name' => 'test_3'
        ]));
        $this->getMagicClass('snuffRepo')->create(new SnuffItem([
            'name' => 'x_it?',
            'value' => 'is ok again'
        ]));
    }
}
