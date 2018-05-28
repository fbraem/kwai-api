import Model from '@/js/JSONAPI/BaseModel';

import Category from '@/apps/categories/models/Category';
import Content from '@/apps/contents/models/Content';
import moment from 'moment';

export default class Story extends Model {
    resourceName() {
        return 'news_stories';
    }

    aliasURL() {
        return '/news/stories';
    }

    fields() {
        return [
            'enabled',
            'featured',
            'featured_end_date_timezone',
            'publish_date_timezone',
            'end_date_timezone',
            'remark',
            'header_detail_crop',
            'header_original',
            'header_overview_crop'
        ];
    }

    dates() {
        return {
            'publish_date' : 'YYYY-MM-DD HH:mm:ss',
            'end_date' : 'YYYY-MM-DD HH:mm:ss',
            'featured_end_date' : 'YYYY-MM-DD HH:mm:ss',
            'created_at' : 'YYYY-MM-DD HH:mm:ss',
            'updated_at' : 'YYYY-MM-DD HH:mm:ss'
        }
    }

    computed() {
        return {
            summary(story) {
                if (story.contents) {
                    var content = story.contents.find((o) => {
                        return o.locale == 'nl';
                    });
                    if (content) {
                        return content.html_summary;
                    }
                }
                return "";
            },
            content(story) {
                if (story.contents) {
                    var content = story.contents.find((o) => {
                        return o.locale == 'nl';
                    });
                    if (content) {
                        return content.html_content;
                    }
                }
                return "";
            },
            title(story) {
                if (story.contents) {
                    var content = story.contents.find((o) => {
                        return o.locale == 'nl';
                    });
                    if (content) {
                        return content.title;
                    }
                }
                return "";
            },
            localPublishDate(story) {
                return moment.utc(story.publish_date).local().format('L');
            },
            publishDateFromNow(story) {
                return moment.utc(story.publish_date).local().fromNow();
            },
            isNew(story) {
                return moment().diff(moment.utc(story.publish_date).local(), 'weeks') < 1;
            },
            authorName(story) {
                if (story.contents) {
                    var content = story.contents.find((o) => {
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
