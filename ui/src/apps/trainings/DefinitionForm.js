import Form from '@/js/Form';

import Season from '@/models/Season';

import moment from 'moment';

import { required, numeric } from 'vuelidate/lib/validators';
import isTime from '@/js/isTime';

/**
 * Class representing the form fields for creating/updating a training
 * definition
 */
export default class DefinitionForm extends Form {
  static fields() {
    return {
      name: {
        value: '',
        label: 'training.definitions.form.name.label',
        validators: [
          {
            v: { required },
            error: 'training.definitions.form.name.required',
          },
        ]
      },
      description: {
        value: '',
        label: 'training.definitions.form.description.label',
        validators: [
          {
            v: { required },
            error: 'training.definitions.form.description.required',
          },
        ]
      },
      season: {
        value: 0,
        label: 'training.definitions.form.season.label'
      },
      weekday: {
        value: 1,
        label: 'training.definitions.form.weekday.label',
        validators: [
          {
            v: { required },
            error: 'training.definitions.form.weekday.required'
          },
          {
            v: { numeric },
            error: 'training.definitions.form.weekday.numeric'
          },
        ]
      },
      start_time: {
        value: '',
        label: 'training.definitions.form.start_time.label',
        validators: [
          {
            v: { required },
            error: 'training.definitions.form.start_time.required'
          },
          {
            v: { isTime },
            error: 'training.definitions.form.start_time.invalid'
          },
        ]
      },
      end_time: {
        value: '',
        label: 'training.definitions.form.end_time.label',
        validators: [
          {
            v: { required },
            error: 'training.definitions.form.end_time.required'
          },
          {
            v: { isTime },
            error: 'training.definitions.form.end_time.invalid'
          },
        ]
      },
      active: {
        value: true,
        label: 'training.definitions.form.active.label'
      },
      location: {
        value: null,
        label: 'training.definitions.form.location.label'
      },
      remark: {
        value: '',
        label: 'training.definitions.form.remark.label'
      }
    };
  }

  set(definition) {
    this.name.value = definition.name;
    this.description.value = definition.description;
    this.active.value = definition.active;
    this.location.value = definition.location;
    this.start_time.value = definition.localStartTime;
    this.end_time.value = definition.localEndtime;
    if (definition.season) {
      this.season.value = definition.season.id;
    }
    this.remark.value = definition.remark;
  }

  get(definition) {
    definition.name = this.name.value;
    definition.description = this.description.value;
    definition.active = this.active.value;
    definition.weekday = this.weekday.value;
    definition.location = this.location.value;
    var tz = moment.tz.guess();
    if (this.start_time.value) {
      definition.start_time
        = moment(this.start_time.value, 'HH:mm', true).utc();
    }
    if (this.end_time.value) {
      definition.end_time = moment(this.end_time.value, 'HH:mm', true).utc();
    }
    definition.time_zone = tz;
    definition.remark = this.remark.value;
    if (this.season.value) {
      if (this.season.value === 0) {
        definition.season = null;
      } else {
        definition.season = new Season();
        definition.season.id = this.season.value;
      }
    }
  }
};
