<?php

namespace Domain\Team;

class TeamsTable extends \Cake\ORM\Table
{
    public static $registryName = 'Teams';
    public static $tableName = 'teams';
    public static $entityClass = 'Domain\Team\Team';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('TeamType', [
                'className' => TeamTypesTable::class
            ])
            ->setForeignKey('team_type_id')
            ->setProperty('team_type')
        ;
        $this->belongsTo('Season', [
                'className' => \Domain\Game\SeasonsTable::class
            ])
            ->setForeignKey('season_id')
            ->setProperty('season')
        ;
        //TODO: Remove sport dependency?
        $this->belongsToMany('Members', [
                'className' => \Judo\Domain\Member\MembersTable::class,
                'joinTable' => 'team_members',
                'through' => TeamMembersTable::getTableFromRegistry(),
                'dependent' => true
            ])
            ->setForeignKey('team_id')
            ->setProperty('members')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('name', [ 'type' => 'string' ])
            ->addColumn('season_id', [ 'type' => 'integer' ])
            ->addColumn('team_type_id', [ 'type' => 'integer' ])
            ->addColumn('active', ['type' => 'boolean'])
            ->addColumn('remark', [ 'type' => 'text'])
            ->addColumn('created_at', [ 'type' => 'timestamp'])
            ->addColumn('updated_at', [ 'type' => 'timestamp'])
            ->addConstraint(
                'primary',
                [
                    'type' => 'primary',
                    'columns' => [
                        'id'
                    ]
                ]
        );
    }
}
