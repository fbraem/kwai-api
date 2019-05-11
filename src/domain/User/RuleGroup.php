<?php
namespace Domain\User;

/**
 * @inheritdoc
 */
class RuleGroup extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    protected $_hidden = ['rules'];
}
