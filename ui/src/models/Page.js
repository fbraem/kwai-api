import Model from './Model';
import {
  Attribute, DateAttribute, ArrayAttribute, ObjectAttribute
} from './Attribute';

import Category from './Category';

/**
 * Page model
 */
export default class Page extends Model {
  static type() {
    return 'pages';
  }

  static fields() {
    return {
      name: new Attribute(),
      enabled: new Attribute(),
      remark: new Attribute(),
      priority: new Attribute(),
      images: new Attribute(true),
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
      ),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true)
    };
  }

  static computed() {
    return {
      content(page) {
        if (page.contents) {
          let content = page.contents.find((o) => {
            return o.locale === 'nl';
          });
          return content || page.contents[0];
        }
        return null;
      },
      authorName(page) {
        if (page.contents) {
          var content = page.contents.find((o) => {
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
      localPublishDate(page) {
        var utc = page.updated_at.clone();
        utc.utcOffset('+00:00', true);
        return utc.local().format('L');
      },
      picture(page) {
        if (page.images) {
          return page.images.crop;
        }
        return null;
      },
    };
  }

  static relationships() {
    return {
      category: Category
    };
  }
}
