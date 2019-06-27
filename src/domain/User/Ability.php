<?php
namespace Domain\User;

/**
 * @inheritdoc
 */
class Ability extends \Cake\ORM\Entity
{
    use \Domain\DatetimeMetaTrait;

    protected $_hidden = ['rules'];
}
