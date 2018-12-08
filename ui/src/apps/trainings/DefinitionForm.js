import Form from '@/js/Form';

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
        label: 'training.definitions.form.desription.label',
        validators: [
          {
            v: { required },
            error: 'training.definitions.form.description.required',
          },
        ]
      },
      season: {
        value: 0,
        label: 'training.definitions.form.desription.label',
        options: []
      },
      weekday: {
        value: 1,
        label: '',
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
};
