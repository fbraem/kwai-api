<?php
/**
 * @package Modules
 * @subpackage News
 */

/* @noinspection PhpUnusedPrivateFieldInspection */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure;

use Kwai\Core\Infrastructure\Database\TableEnum;

/**
 * Class Tables
 *
 * This class defines all table names for the News module.
 * @method static Tables AUTHORS()
 * @method static Tables APPLICATIONS()
 * @method static Tables STORIES()
 * @method static Tables CONTENTS()
 */
class Tables extends TableEnum
{
    private const AUTHORS = 'users';
    private const APPLICATIONS = 'applications';
    private const STORIES = 'news_stories';
    private const CONTENTS = 'news_contents_2';
}
