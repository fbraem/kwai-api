<?php

namespace Kwai\Core\Infrastructure\Validators;

use Cake\Datasource\Exception\RecordNotFoundException;

class EntityExistValidator implements ValidatorInterface
{
    private $path;
    private $table;
    private $required;

    public function __construct($path, $table, $required = false)
    {
        $this->path = $path;
        $this->table = $table;
        $this->required = $required;
    }

    public function validate($data)
    {
        $paths = explode('.', $this->path);
        $errors = [];

        $relationshipData = \JmesPath\search($this->path . '.data', $data);
        if (isset($relationshipData)) {
            if ($this->isAssoc($relationshipData)) {
                try {
                    return $this->table->get($relationshipData['id']);
                } catch (RecordNotFoundException $rnfe) {
                    $errors['/' . str_replace('.', '/', $this->path)] = end($paths) . " doesn't exist";
                }
            } else {
                $result = [];
                foreach ($relationshipData as $relation) {
                    try {
                        $result[] = $this->table->get($relation['id']);
                    } catch (RecordNotFoundException $rnfe) {
                        $errors['/' . str_replace('.', '/', $this->path) . '/' . $relation['id']] = end($paths) . " doesn't exist";
                    }
                }
                return $result;
            }
        } elseif ($this->required) {
            $errors['/' . str_replace('.', '/', $this->path)] = end($paths) . " is missing";
        }

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }
    }

    private function isAssoc($array)
    {
        foreach ($array as $key => $value) {
            if ($key !== (int) $key) {
                return true;
            }
        }
        return false;
    }
}
