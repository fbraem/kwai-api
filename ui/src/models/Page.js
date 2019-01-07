import Model from './Model';
import { Attribute, DateAttribute } from './Attribute';

import Category from './Category';
import Content from './Content';

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
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true)
    };
  }

  static computed() {
    return {
      summary(page) {
        if (page.contents) {
          var content = page.contents.find((o) => {
            return o.locale === 'nl';
          });
          if (content) {
            return content.html_summary;
          }
        }
        return '';
      },
      content(page) {
        if (page.contents) {
          var content = page.contents.find((o) => {
            return o.locale === 'nl';
          });
          if (content) {
            return content.html_content;
          }
        }
        return '';
      },
      title(page) {
        if (page.contents) {
          var content = page.contents.find((o) => {
            return o.locale === 'nl';
          });
          if (content) {
            return content.title;
          }
        }
        return '';
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
      category: Category,
      contents: Content,
    };
  }
}
