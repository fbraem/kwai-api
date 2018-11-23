import Model from './BaseModel';

import Category from './Category';
import Content from './Content';
import moment from 'moment-timezone';

export default class Story extends Model {
  resourceName() {
    return 'stories';
  }

  namespace() {
    return 'news';
  }

  fields() {
    return [
      'enabled',
      'featured',
      'featured_end_date_timezone',
      'publish_date_timezone',
      'end_date_timezone',
      'remark',
      'images',
    ];
  }

  dates() {
    return {
      publish_date: 'YYYY-MM-DD HH:mm:ss',
      end_date: 'YYYY-MM-DD HH:mm:ss',
      featured_end_date: 'YYYY-MM-DD HH:mm:ss',
      created_at: 'YYYY-MM-DD HH:mm:ss',
      updated_at: 'YYYY-MM-DD HH:mm:ss',
    };
  }

  computed() {
    return {
      summary(story) {
        if (story.contents) {
          let content = story.contents.find((o) => {
            return o.locale === 'nl';
          });
          if (content) {
            return content.html_summary;
          }
        }
        return '';
      },
      content(story) {
        if (story.contents) {
          let content = story.contents.find((o) => {
            return o.locale === 'nl';
          });
          if (content) {
            return content.html_content;
          }
        }
        return '';
      },
      title(story) {
        if (story.contents) {
          let content = story.contents.find((o) => {
            return o.locale === 'nl';
          });
          if (content) {
            return content.title;
          }
        }
        return '';
      },
      localPublishDate(story) {
        var utc = story.publish_date.clone();
        utc.utcOffset('+00:00', true);
        return utc.local().format('L');
      },
      localPublishTime(story) {
        var utc = story.publish_date.clone();
        utc.utcOffset('+00:00', true);
        return utc.local().format('HH:mm');
      },
      publishDateFromNow(story) {
        var utc = story.publish_date.clone();
        utc.utcOffset('+00:00', true);
        return utc.local().fromNow();
      },
      isNew(story) {
        var utc = story.publish_date.clone();
        utc.utcOffset('+00:00', true);
        return moment().diff(utc.local(), 'weeks') < 1;
      },
      localEndDate(story) {
        if (!story.end_date) return null;
        var utc = story.end_date.clone();
        utc.utcOffset('+00:00', true);
        return utc.local().format('L');
      },
      localEndTime(story) {
        if (!story.end_date) return null;
        var utc = story.end_date.clone();
        utc.utcOffset('+00:00', true);
        return utc.local().format('HH:mm');
      },
      localFeaturedEndDate(story) {
        if (!story.featured_end_date) return null;
        var utc = story.featured_end_date.clone();
        utc.utcOffset('+00:00', true);
        return utc.local().format('L');
      },
      localFeaturedEndTime(story) {
        if (!story.featured_end_date) return null;
        var utc = story.featured_end_date.clone();
        utc.utcOffset('+00:00', true);
        return utc.local().format('HH:mm');
      },
      authorName(story) {
        if (story.contents) {
          var content = story.contents.find((o) => {
            return o.locale === 'nl';
          });
          if (content) {
            var author = content.user;
            if (author) {
              return [
                author.first_name,
                author.last_name].filter(n => n != null).join(' ');
            }
          }
        }
        return '';
      },
      detail_picture(story) {
        if (story.images) {
          return story.images.header_detail_crop;
        }
      },
      overview_picture(story) {
        if (story.images) {
          return story.images.header_overview_crop;
        }
      },
    };
  }

  relationships() {
    return {
      category: new Category(),
      contents: new Content(),
    };
  }
}
