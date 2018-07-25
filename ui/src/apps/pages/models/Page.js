import Model from '@/js/JSONAPI/BaseModel';

import Category from '@/apps/categories/models/Category';
import Content from '@/apps/contents/models/Content';

export default class Page extends Model {
    resourceName() {
        return 'pages';
    }

    fields() {
        return [
            'name',
            'enabled',
            'remark',
            'priority',
            'header_detail_crop',
            'header_original',
            'header_overview_crop'
        ];
    }

    dates() {
        return {
            'created_at' : 'YYYY-MM-DD HH:mm:ss',
            'updated_at' : 'YYYY-MM-DD HH:mm:ss'
        }
    }

    computed() {
        return {
            summary(page) {
                if (page.contents) {
                    var content = page.contents.find((o) => {
                        return o.locale == 'nl';
                    });
                    if (content) {
                        return content.html_summary;
                    }
                }
                return "";
            },
            content(page) {
                if (page.contents) {
                    var content = page.contents.find((o) => {
                        return o.locale == 'nl';
                    });
                    if (content) {
                        return content.html_content;
                    }
                }
                return "";
            },
            title(page) {
                if (page.contents) {
                    var content = page.contents.find((o) => {
                        return o.locale == 'nl';
                    });
                    if (content) {
                        return content.title;
                    }
                }
                return "";
            },
            authorName(page) {
                if (page.contents) {
                    var content = page.contents.find((o) => {
                        return o.locale == 'nl';
                    });
                    if (content) {
                        var author = content.user;
                        if (author) {
                            return [author.first_name, author.last_name].filter(n => n != null).join(' ');
                        }
                    }
                }
                return "";
            }
        };
    }

    relationships() {
        return {
            'category' : new Category(),
            'contents' : new Content()
        };
    }
}
