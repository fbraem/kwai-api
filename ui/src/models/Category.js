import Model from './Model';
import { Attribute, DateAttribute } from './Attribute';

/**
 * Category model
 */
export default class Category extends Model {
  static type() {
    return 'categories';
  }

  static fields() {
    return {
      name: new Attribute(),
      description: new Attribute(),
      remark: new Attribute(),
      images: new Attribute(true),
      short_description: new Attribute(),
      slug: new Attribute(),
      created_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
      updated_at: new DateAttribute('YYYY-MM-DD HH:mm:ss', true),
    };
  }

  static computed() {
    return {
      header_picture(category) {
        if (category.images) {
          return category.images.header;
        }
      },
      icon_picture(category) {
        if (category.images) {
          return category.images.icon;
        }
      },
    };
  }
}
