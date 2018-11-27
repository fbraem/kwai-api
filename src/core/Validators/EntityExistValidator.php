<?php

namespace Core\Validators;

use Cake\Datasource\Exception\RecordNotFoundException;

class EntityExistValidator implements ValidatorInterface
{
    private $path;
    private $data;
    private $table;
    private $required;

    public function __construct($path, $table, $required = false)
    {
        $this->path = $path;
        $this->data = $data;
        $this->table = $table;
        $this->required = $required;
    }

    public function validate($data)
    {
        $paths = explode('.', $this->path);
        $errors = [];

        $relationshipData = \JmesPath\search($this->path . '.data', $data);
        if (isset($relationshipData)) {
            try {
                return $this->table->get($relationshipData['id']);
            } catch (RecordNotFoundException $rnfe) {
                $errors['/' . str_replace('.', '/', $this->path)] = end($paths) . " doesn't exist";
            }
        } elseif ($this->required) {
            $errors['/' . str_replace('.', '/', $this->path)] = end($paths) . " is missing";
        }

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }
    }
}
