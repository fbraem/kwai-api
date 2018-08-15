<?php

namespace Domain;

trait DomainTableTrait
{
    protected function initializeTable($timestamps = true)
    {
        $this->setTable(self::$tableName);
        $this->setEntityClass(self::$entityClass);

        if ($timestamps) {
            $this->addBehavior('Timestamp', [
                'events' => [
                    'Model.beforeSave' => [
                        'created_at' => 'new',
                        'updated_at' => 'existing'
                    ]
                ]
            ]);
        }

        $schema = new \Cake\Database\Schema\TableSchema(self::$tableName);
        $this->initializeSchema($schema);
        $this->setSchema($schema);
    }

    public static function getTableFromRegistry()
    {
        if (\Cake\ORM\TableRegistry::exists(self::$registryName)) {
            return \Cake\ORM\TableRegistry::get(self::$registryName);
        }
        return \Cake\ORM\TableRegistry::get(self::$registryName, [
            'className' => self::class
        ]);
    }

    abstract public function initializeSchema(\Cake\Database\Schema\TableSchema $schema);
}
