<?php
/**
 * @package Pages
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Infrastructure;

use Kwai\Core\Infrastructure\Database\TableEnum;

/**
 * Class Tables
 *
 * This class defines all table names for the Pages module.
 *
 * @method static Tables AUTHORS()
 * @method static Tables APPLICATIONS()
 * @method static Tables CONTENTS()
 * @method static Tables PAGES()
 */
class Tables extends TableEnum
{
    private const AUTHORS = 'users';
    private const APPLICATIONS = 'applications';
    private const CONTENTS = 'page_contents_2';
    private const PAGES = 'pages';
}
