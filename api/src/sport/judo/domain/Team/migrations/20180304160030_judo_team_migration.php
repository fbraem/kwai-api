<?php

use Phinx\Migration\AbstractMigration;

/**
 * Base Judo Member Migration
 */
class JudoTeamMigration extends AbstractMigration
{
    public function up()
    {
        $this->table('sport_judo_teams', ['signed' => false])
            ->addColumn('name', 'string')
            ->addColumn('start_age', 'integer', ['null' => true])
            ->addColumn('end_age', 'integer', ['null' => true])
            ->addColumn('min_grade_id', 'integer', ['null' => true])
            ->addColumn('competition', 'boolean', ['default' => false])
            ->addColumn('remark', 'text', ['null' => true])
            ->addTimestamps()
            ->create()
        ;

        $teams = [
            [ 'name' => 'U9', 'start_age' => 6, 'end_age' => 8, 'competition' => true ],
            [ 'name' => 'U11', 'start_age' => 9, 'end_age' => 10, 'competition' => true, 'min_grade_id' => 2],
            [ 'name' => 'U13', 'start_age' => 11, 'end_age' => 12, 'competition' => true, 'min_grade_id' => 3],
            [ 'name' => 'U14', 'start_age' => 12, 'end_age' => 13, 'competition' => true, 'min_grade_id' => 4 ],
            [ 'name' => 'U15', 'start_age' => 13, 'end_age' => 14, 'competition' => true, 'min_grade_id' => 4 ],
            [ 'name' => 'U17', 'start_age' => 14, 'end_age' => 16, 'competition' => true, 'min_grade_id' => 4 ],
            [ 'name' => 'U18', 'start_age' => 15, 'end_age' => 17, 'competition' => true, 'min_grade_id' => 4 ],
            [ 'name' => 'U18+', 'start_age' => 15, 'competition' => true, 'min_grade_id' => 4 ],
            [ 'name' => 'U21', 'start_age' => 18, 'end_age' => 20, 'competition' => true, 'min_grade_id' => 4 ],
            [ 'name' => 'U21+', 'start_age' => 18, 'competition' => true, 'min_grade_id' => 4 ],
        ];
        foreach ($teams as $team) {
            $this->table('sport_judo_teams')->insert($team)->save();
        }

        $this->table('sport_judo_team_members', ['id' => false, 'primary_key' => ['team_id', 'member_id']])
            ->addColumn('team_id', 'integer')
            ->addColumn('member_id', 'integer')
            ->addTimestamps()
            ->create()
        ;
    }

    public function down()
    {
        $this->table('sport_judo_team_members')->drop();
        $this->table('sport_judo_teams')->drop();
    }
}
