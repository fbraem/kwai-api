<?php

namespace Judo\Domain\Member;

class Grade implements GradeInterface
{
    private $db;

    private $id;
    private $grade;
    private $name;
    private $color;
    private $position;
    private $min_age;
    private $prepare_time;

    public function __construct($db, ?iterable $data = null)
    {
        $this->db = $db;

        if ($data) {
            $this->hydrate($data);
        }
    }

    public function hydrate(iterable $data)
    {
        $this->id = $data['id'] ?? null;
        $this->grade = $data['grade'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->color = $data['color'] ?? [];
        $this->position = $data['position'] ?? null;
        $this->min_age = $data['min_age'] ?? null;
        $this->prepare_time = $data['prepare_time'] ?? null;
    }

    public function extract() : iterable
    {
        return [
            'id' => $this->id,
            'grade' => $this->grade,
            'name' => $this->name,
            'color' => $this->color,
            'position' => $this->position,
            'min_age' => $this->min_age,
            'prepare_time' => $this->prepare_time
        ];
    }

    public function id() : ?int
    {
        return $this->id;
    }

    /*
        public function grade() : ?string
        {
            return $this->grade;
        }

        public function name() : ?string
        {
            return $this->name;
        }

        public function color() : ?string
        {
            return $this->color;
        }

        public function position() : ?int
        {
            return $this->position;
        }

        public function min_age() : ?string
        {
            return $this->min_age;
        }

        public function prepare_time() : ?string
        {
            return $this->prepare_time;
        }
    */
}
