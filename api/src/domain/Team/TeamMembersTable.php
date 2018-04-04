<?php

namespace Domain\Team;

class TeamMembersTable extends \Cake\ORM\Table
{
    public static $registryName = 'TeamMembers';
    public static $tableName = 'team_members';
    public static $entityClass = 'Domain\Team\TeamMember';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('Team', [
            'className' => TeamsTable::class
            ])
            ->setForeignKey('team_id')
        ;
        //TODO: Remove sport dependency?
        $this->belongsTo('Member', [
            'className' => \Judo\Domain\Member\MembersTable::class
            ])
            ->setForeignKey('member_id')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('team_id', ['type' => 'integer'])
            ->addColumn('member_id', ['type' => 'integer'])
            ->addColumn('created_at', [ 'type' => 'timestamp'])
            ->addColumn('updated_at', [ 'type' => 'timestamp'])
            ->addConstraint(
                'primary',
                [
                    'type' => 'primary',
                    'columns' => [
                        'team_id',
                        'member_id'
                    ]
                ]
        );
    }
}
