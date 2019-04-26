<?php

namespace Domain\Training;

class PresencesTable extends \Cake\ORM\Table
{
    public static $registryName = 'Presences';
    public static $tableName = 'training_presences';
    public static $entityClass = 'Domain\Training\Presence';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();

        $this->belongsTo('Training', [
            'className' => TrainingsTable::class
            ])
            ->setForeignKey('training_id')
        ;
        $this->belongsTo('Member', [
            'className' => \Judo\Domain\Member\MembersTable::class
            ])
            ->setForeignKey('member_id')
        ;
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('training_id', ['type' => 'integer'])
            ->addColumn('member_id', ['type' => 'integer'])
            ->addColumn('remark', ['type' => 'text'])
            ->addColumn('created_at', [ 'type' => 'timestamp'])
            ->addColumn('updated_at', [ 'type' => 'timestamp'])
            ->addConstraint(
                'primary',
                [
                    'type' => 'primary',
                    'columns' => [
                        'training_id',
                        'member_id'
                    ]
                ]
        );
    }
}
