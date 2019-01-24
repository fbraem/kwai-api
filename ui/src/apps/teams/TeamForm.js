import VueForm, { notEmpty } from '@/js/VueForm';

import Season from '@/models/Season';
import TeamType from '@/models/TeamType';

export default {
  mixins: [ VueForm ],
  form() {
    return {
      name: {
        value: '',
        required: true,
        validators: [
          {
            v: notEmpty,
            error: this.$t('form.team.name.required'),
          },
        ]
      },
      season: {
        value: 0
      },
      team_type: {
        value: 0
      },
      remark: {
        value: ''
      }
    };
  },
  methods: {
    writeForm(team) {
      this.form.name.value = team.name;
      this.form.remark.value = team.remark;
      if (team.season) {
        this.form.season.value = this.team.season.id;
      }
      if (team.team_type) {
        this.form.team_type.value = this.team.team_type.id;
      }
    },
    readForm(team) {
      team.name = this.form.name.value;
      team.remark = this.form.remark.value;
      team.season = new Season();
      if (this.form.team.season === 0) {
        team.season.id = null;
      } else {
        team.season.id = this.form.season.value;
      }
      this.team.team_type = new TeamType();
      if (this.form.team_type.value === 0)
        team.team_type.id = this.form.team_type.value;
    }
  }
};
