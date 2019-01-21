import VueForm from '@/js/VueForm';

export default {
  mixins: [ VueForm ],
  forms() {
    return {
      name : {
        value: '',
        required: true,
        validators: [
          {
            v: notEmpty,
            error: this.$t('form.teamtype.name.required'),
          },
        ]
      },
      start_age: {
        value : null,
        validators: [
          {
            v: isNumeric,
            error: this.$t('form.teamtype.start_age.numeric'),
          },
        ]
      },
      end_age: {
        value: null,
        validators: [
          {
            v: isNumeric,
            error: this.$t('form.teamtype.start_age.numeric'),
          },
        ]
      },
      gender: {
        value: 0,
      },
      active: {
        value: true
      },
      competition: {
        value: true
      },
      remark: {
        value: ''
      }
    };
  },
  methods: {
    writeForm(teamType) {
      this.form.team_type.name = this.teamType.name;
      this.form.team_type.start_age = this.teamType.start_age;
      this.form.team_type.end_age = this.teamType.end_age;
      this.form.team_type.gender = this.teamType.gender;
      this.form.team_type.active = this.teamType.active;
      this.form.team_type.competition = this.teamType.competition;
      this.form.team_type.remark = this.teamType.remark;
    },
    readForm(teamType) {
      this.teamType.name =this.form.team_type.name;
      this.teamType.start_age = this.form.team_type.start_age;
      this.teamType.end_age = this.form.team_type.end_age;
      this.teamType.gender = this.form.team_type.gender;
      this.teamType.active = this.form.team_type.active;
      this.teamType.competition = this.form.team_type.competition;
      this.teamType.remark =this.form.team_type.remark;
    }
  }
};

      validations : {
          form : {
              team_type : {
                  name : {
                      required
                  },
                  start_age : {
                      numeric
                  },
                  end_age : {
                      numeric
                  },
                  gender : {
                  },
                  remark : {
                  }
              }
          }
      },
      beforeCreate() {
          if (!this.$store.state.teamTypeModule) {
              this.$store.registerModule('teamTypeModule', teamTypeStore);
          }
      },
      beforeRouteEnter(to, from, next) {
          next(async (vm) => {
              if (to.params.id) await vm.fetchData(to.params.id);
              next();
          });
      },
      watch : {
          error(nv) {
              if (nv) {
                  if ( nv.response.status == 422 ) {
                      nv.response.data.errors.forEach((item, index) => {
                          if ( item.source && item.source.pointer ) {
                              var attr = item.source.pointer.split('/').pop();
                              this.errors[attr].push(item.title);
                          }
                      });
                  }
                  else if ( nv.response.status == 404 ){
                    //this.error = err.response.statusText;
                  }
                  else {
                    //TODO: check if we can get here ...
                    console.log(nv);
                  }
              }
          }
      },
      methods : {
          clear() {
              this.$v.$reset();
              this.form = initForm();
          },
          async fetchData(id) {
              this.teamType = await this.$store.dispatch('teamTypeModule/read', {
                  id : id
              });
              this.fillForm();
          },
          submit() {
              this.errors = initError();
              this.fillTeamType();
              this.$store.dispatch('teamTypeModule/save', this.teamType)
                  .then((newType) => {
                      this.$router.push({ name : 'team_types.read', params : { id : newType.id }});
                  })
                  .catch(err => {
                      console.log(err);
                  });
          }
      }
  };
