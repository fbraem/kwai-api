import Model from './Model';
import {
  Attribute, DateAttribute, ObjectAttribute, ArrayAttribute
} from './Attribute';

import Category from './Category';
import moment from 'moment-timezone';

/**
 * Story model
 */
export default class Story extends Model {
  static type() {
    return 'stories';
  }

  static namespace() {
    return ['news'];
  }

  static fields() {
    return {
      enabled: new Attribute(),
      featured: new Attribute(),
      featured_end_date: new DateAttribute('YYYY-MM-DD HH:mm:ss'),
      timezone: new Attribute(),
      publish_date: new DateAttribute('YYYY-MM-DD HH:mm:ss'),
      end_date: new DateAttribute('YYYY-MM-DD HH:mm:ss'),
      remark: new Attribute(),
      images: new Attribute(true),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      contents: new ArrayAttribute(
        new ObjectAttribute({
          locale: new Attribute(),
          format: new Attribute(),
          title: new Attribute(),
          content: new Attribute(),
          html_content: new Attribute(true),
          summary: new Attribute(),
          html_summary: new Attribute(true),
          created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
          updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true)
        })
      )
    };
  }

  static computed() {
    return {
      content(story) {
        if (story.contents) {
          let content = story.contents.find((o) => {
            return o.locale === 'nl';
          });
          return content || story.contents[0];
        }
        return null;
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
        if (story.content) {
          var author = story.content.user;
          if (author) {
            return [
              author.first_name,
              author.last_name].filter(n => n != null).join(' ');
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

  static relationships() {
    return {
      category: Category
    };
  }
}
