<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base Judo Member Migration
 */
class JudoMemberMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('sport_judo_members', ['signed' => false])
            ->addColumn('license', 'string')
            ->addColumn('license_end_date', 'date')
            ->addColumn('person_id', 'integer')
            ->addColumn('remark', 'text', ['null' => true])
            ->addColumn('competition', 'boolean', ['default' => false])
            ->addTimestamps()
            ->create()
        ;

        $this->table('sport_judo_grades', ['signed' => false])
            ->addColumn('grade', 'string')
            ->addColumn('name', 'string')
            ->addColumn('color', 'string')
            ->addColumn('position', 'integer')
            ->addColumn('min_age', 'integer')
            ->addColumn('prepare_time', 'integer')
            ->create()
        ;

        $pos = 0;
        $grades = [
            [ 'grade' => '6e Kyu', 'name' => 'rok-kyu', 'color' => 'wit', 'position' => $pos++, 'min_age' => 0, 'prepare_time' => 0 ],
            [ 'grade' => '5e Kyu', 'name' => 'go-kyu', 'color' => 'geel', 'position' => $pos++, 'min_age' => 6, 'prepare_time' => 3 ],
            [ 'grade' => '4e Kyu', 'name' => 'yon-kyu', 'color' => 'oranje', 'position' => $pos++, 'min_age' => 8, 'prepare_time' => 4 ],
            [ 'grade' => '3e Kyu', 'name' => 'san-kyu', 'color' => 'groen', 'position' => $pos++, 'min_age' => 10, 'prepare_time' => 5 ],
            [ 'grade' => '2e Kyu', 'name' => 'ni-kyu', 'color' => 'blauw', 'position' => $pos++, 'min_age' => 12, 'prepare_time' => 6 ],
            [ 'grade' => '1e Kyu', 'name' => 'ik-kyu', 'color' => 'bruin', 'position' => $pos++, 'min_age' => 14, 'prepare_time' => 8 ],
            [ 'grade' => '1e dan', 'name' => 'sho-dan', 'color' => 'zwart', 'position' => $pos++, 'min_age' => 16, 'prepare_time' => 0 ],
            [ 'grade' => '2e dan', 'name' => 'ni-dan', 'color' => 'zwart', 'position' => $pos++, 'min_age' => 17, 'prepare_time' => 12 ],
            [ 'grade' => '3e dan', 'name' => 'san-dan', 'color' => 'zwart', 'position' => $pos++, 'min_age' => 18, 'prepare_time' => 24 ],
            [ 'grade' => '4e dan', 'name' => 'yon-dan', 'color' => 'zwart', 'position' => $pos++, 'min_age' => 21, 'prepare_time' => 36 ],
            [ 'grade' => '5e dan', 'name' => 'go-dan', 'color' => 'zwart', 'position' => $pos++, 'min_age' => 26, 'prepare_time' => 36 ],
            [ 'grade' => '6e dan', 'name' => 'roku-dan', 'color' => 'rood-wit', 'position' => $pos++, 'min_age' => 0, 'prepare_time' => 0 ],
            [ 'grade' => '7e dan', 'name' => 'nana-dan', 'color' => 'rood-wit', 'position' => $pos++, 'min_age' => 0, 'prepare_time' => 0 ],
            [ 'grade' => '8e dan', 'name' => 'hachi-dan', 'color' => 'rood-wit', 'position' => $pos++, 'min_age' => 0, 'prepare_time' => 0 ],
            [ 'grade' => '9e dan', 'name' => 'ku-dan', 'color' => 'rood', 'position' => $pos++, 'min_age' => 0, 'prepare_time' => 0 ],
            [ 'grade' => '10e dan', 'name' => 'ju-dan', 'color' => 'rood', 'position' => $pos++, 'min_age' => 0, 'prepare_time' => 0 ],
            [ 'grade' => '11e dan', 'name' => 'juichi-dan', 'color' => 'wit', 'position' => $pos++, 'min_age' => 0, 'prepare_time' => 0 ],
        ];
        foreach ($grades as $grade) {
            $this->table('sport_judo_grades')->insert($grade)->save();
        }

        $this->table('sport_judo_member_grades', ['signed' => false])
            ->addColumn('member_id', 'integer')
            ->addColumn('grade_id', 'integer')
            ->addColumn('exam_date', 'date', ['null' => true])
            ->addColumn('success', 'boolean', ['default' => true])
            ->create()
        ;
    }

    public function down()
    {
        $this->table('sport_judo_members')->drop();
        $this->table('sport_judo_grades')->drop();
        $this->table('sport_judo_members_grades')->drop();
    }
}
