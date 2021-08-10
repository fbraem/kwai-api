<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Presentation\REST;

use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;
use Nette\Schema\Processor;

/**
 * Class StorySchema
 */
class StorySchema
{
    private const DATETIME_PATTERN = '\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}';

    private Structure $schema;

    public function __construct(bool $create = false)
    {
        $this->schema = Expect::structure([
            'data' => Expect::structure([
                'type' => Expect::anyOf('stories'),
                'id' => Expect::string()->required(!$create),
                'attributes' => Expect::Structure([
                    'enabled' => Expect::bool(false),
                    'publish_date' => Expect::string()->required()->pattern(self::DATETIME_PATTERN),
                    'timezone' => Expect::string()->required(),
                    'end_date' => Expect::string()->nullable()->pattern(self::DATETIME_PATTERN),
                    'promotion' => Expect::int(0),
                    'promotion_end_date' => Expect::string()->nullable()->pattern(self::DATETIME_PATTERN),
                    'remark' => Expect::string()->nullable(),
                    'contents' => Expect::arrayOf(Expect::structure([
                        'title' => Expect::string()->required(),
                        'locale' => Expect::string('nl'),
                        'format' => Expect::string('md'),
                        'summary' => Expect::string()->required(),
                        'content' => Expect::string()
                    ]))->required()
                ]),
                'relationships' => Expect::structure([
                    'application' => Expect::structure([
                        'data' => Expect::structure([
                            'type' => Expect::string()->required(),
                            'id' => Expect::string()->required()
                        ])
                    ])
                ])
            ])
        ]);
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    public function normalize(mixed $data)
    {
        $processor = new Processor();
        return $processor->process($this->schema, $data);
    }
}
