import Model from '@/js/JSONAPI/BaseModel';

import moment from 'moment';

export default class Content extends Model {
    resourceName() {
        return 'contents';
    }

    fields() {
        return [
            'locale',
            'format',
            'title',
            'content',
            'html_content',
            'summary',
            'html_summary'
        ];
    }

    dates() {
        return {
            'created_at' : 'YYYY-MM-DD HH:mm:ss',
            'updated_at' : 'YYYY-MM-DD HH:mm:ss'
        }
    }

    relationships() {
        return {
            //TODO:
            //'user' : new User()
        };
    }
}
