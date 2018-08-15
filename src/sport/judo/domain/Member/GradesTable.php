<?php

namespace Judo\Domain\Member;

class GradesTable extends \Cake\ORM\Table
{
    public static $registryName = 'SportJudoGrades';
    public static $tableName = 'sport_judo_grades';
    public static $entityClass = 'Judo\Domain\Member\Grade';

    use \Domain\DomainTableTrait;

    public function initialize(array $config)
    {
        $this->initializeTable();
    }

    protected function initializeSchema(\Cake\Database\Schema\TableSchema $schema)
    {
        $schema
            ->addColumn('id', [ 'type' => 'integer' ])
            ->addColumn('grade', [ 'type' => 'string' ])
            ->addColumn('name', [ 'type' => 'string' ])
            ->addColumn('color', [ 'type' => 'string' ])
            ->addColumn('position', [ 'type' => 'integer' ])
            ->addColumn('min_age', [ 'type' => 'integer' ])
            ->addColumn('prepare_time', [ 'type' => 'integer' ])
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
