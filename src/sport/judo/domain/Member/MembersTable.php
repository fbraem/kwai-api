<?php

namespace Judo\Domain\Member;

class MembersTable extends \Cake\ORM\Table
{
    public static $registryName = 'SportJudoMembers';
    public static $tableName = 'sport_judo_members';
    public static $entityClass = 'Judo\Domain\Member\Member';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('Person', [
                'className' => \Domain\Person\PersonsTable::class
            ])
            ->setForeignKey('person_id')
            ->setProperty('person')
        ;
        $this->belongsToMany('Trainings', [
                'className' =>  \Domain\Training\TrainingsTable::class,
                'targetForeignKey' => 'training_id',
                'joinTable' => 'training_presences',
                'through' => \Domain\Training\PresencesTable::getTableFromRegistry()
            ])
            ->setForeignKey('member_id')
            ->setProperty('trainings')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('license', [ 'type' => 'string' ])
            ->addColumn('license_end_date', [ 'type' => 'date' ])
            ->addColumn('person_id', [ 'type' => 'integer' ])
            ->addColumn('remark', [ 'type' => 'text'])
            ->addColumn('competition', [ 'type' => 'boolean'])
            ->addColumn('active', [ 'type' => 'boolean'])
            ->addColumn('import_id', [ 'type' => 'integer'])
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
