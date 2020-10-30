<?php
namespace Domain\User;

/**
 * @inheritdoc
 */
class Rule extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    protected $_hidden = ['action', 'action_id', 'subject', 'subject_id'];
}
