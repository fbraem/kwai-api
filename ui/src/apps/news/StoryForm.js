import VueForm, { notEmpty, isDate, isTime } from '@/js/VueForm';
import moment from 'moment';

import Category from '@/models/Category';

const createDatetime = (date, time) => {
  if (time == null || time.length === 0) {
    time = '00:00';
  }
  date += ' ' + time;
  return moment(date, 'L HH:mm', true);
};

/**
 * Mixin for the Story form.
 */
export default {
  mixins: [ VueForm ],
  form() {
    return {
      enabled: {
        value: false
      },
      category: {
        value: 0,
        required: true
      },
      publish_date: {
        value: moment().format('L'),
        required: true,
        validators: [
          {
            v: notEmpty,
            error: this.$t('form.story.publish_date.required')
          },
          {
            v: isDate,
            error: this.$t('form.story.publish_date.invalid', {
              format: moment.localeData().longDateFormat('L')
            })
          },
        ]
      },
      publish_time: {
        value: moment().format('HH:MM'),
        required: true,
        validators: [
          {
            v: notEmpty,
            error: this.$t('form.story.publish_time.required')
          },
          {
            v: isTime,
            error: this.$t('form.story.publish_time.invalid', {
              format: 'HH:MM'
            })
          },
        ]
      },
      end_date: {
        value: null,
        validators: [
          {
            v: isDate,
            error: this.$t('form.story.end_date.invalid', {
              format: moment.localeData().longDateFormat('L')
            })
          },
        ]
      },
      end_time: {
        value: null,
        validators: [
          {
            v: isTime,
            error: this.$t('form.story.end_time.invalid', {
              format: 'HH:MM'
            })
          },
        ]
      },
      featured: {
        value: 0
      },
      featured_end_date: {
        value: null,
        validators: [
          {
            v: isDate,
            error: this.$t('form.story.featured_end_date.invalid', {
              format: moment.localeData().longDateFormat('L')
            })
          },
        ]
      },
      featured_end_time: {
        value: null,
        validators: [
          {
            v: isTime,
            error: this.$t('form.story.featured_end_time.invalid', {
              format: 'HH:MM'
            })
          },
        ]
      },
      remark: {
        value: ''
      }
    };
  },
  methods: {
    publishDatetime() {
      return createDatetime(
        this.form.publish_date.value,
        this.form.publish_time.value
      );
    },
    endDatetime() {
      return createDatetime(
        this.form.end_date.value,
        this.form.end_time.value
      );
    },
    featuredEndDatetime() {
      return createDatetime(
        this.form.featured_end_date.value,
        this.form.featured_end_time.value
      );
    },
    writeForm(story) {
      this.form.category.value = story.category.id;
      this.form.enabled.value = story.enabled;
      if (story.publish_date) {
        this.form.publish_date.value = story.localPublishDate;
        this.form.publish_time.value = story.localPublishTime;
      }
      if (story.end_date) {
        this.story.end_date.value = story.localEndDate;
        this.story.end_time.value = story.localEndTime;
      }
      this.form.featured.value = story.featured;
      if (story.featured_end_date) {
        this.form.featured_end_date.value = story.localFeaturedEndDate;
        this.form.featured_end_time.value = story.localFeaturedEndTime;
      }
      this.form.remark.value = story.remark;
    },
    readForm(story) {
      story.timezone = moment.tz.guess();
      story.enabled = this.form.enabled.value;
      story.remark = this.form.remark.value;
      story.category = new Category();
      story.category.id = this.form.category.value;
      story.publish_date = this.publishDatetime().utc();
      if (this.form.end_date.value) {
        story.end_date = this.endDatetime().utc();
      } else {
        story.end_date = null;
      }
      story.featured = this.form.featured.value;
      if (this.form.featured_end_date.value) {
        story.featured_end_date = this.featuredEndDatetime().utc();
      } else {
        story.featured_end_date = null;
      }
      console.log(story);
    }
  }
};
