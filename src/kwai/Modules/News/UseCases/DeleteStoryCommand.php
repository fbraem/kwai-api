<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\UseCases;

/**
 * Class DeleteStoryCommand
 *
 * Command for the Delete Story use case.
 */
class DeleteStoryCommand
{
    public int $id;
}
