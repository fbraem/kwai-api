<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure;

use Kwai\Core\Infrastructure\Database\TableEnum;

/**
 * Class Tables
 *
 * This class defines all tables for the Trainings module.
 *
 * @method static Tables COACHES()
 * @method static Tables MEMBERS()
 * @method static Tables PERSONS()
 * @method static Tables TEAMS()
 * @method static Tables TRAININGS()
 * @method static Tables TRAINING_CONTENTS()
 * @method static Tables TRAINING_DEFINITIONS()
 * @method static Tables TRAINING_PRESENCES()
 * @method static Tables TRAINING_TEAMS()
 * @method static Tables USERS()
 */
class Tables extends TableEnum
{
    private const COACHES = 'coaches';
    private const MEMBERS = 'sport_judo_members';
    private const PERSONS = 'persons';
    private const TEAMS = 'teams';
    private const TRAININGS = 'trainings';
    private const TRAINING_CONTENTS = 'training_contents';
    private const TRAINING_DEFINITIONS = 'training_definitions';
    private const TRAINING_PRESENCES = 'training_presences';
    private const TRAINING_TEAMS = 'training_teams';
    private const USERS = 'users';
}
