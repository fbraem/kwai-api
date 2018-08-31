import Model from '@/js/JSONAPI/BaseModel';

import moment from 'moment-timezone';

export default class User extends Model {
    resourceName() {
        return 'users';
    }

    fields() {
        return [
            'first_name',
            'last_name',
            'password',
            'email',
            'remark'
        ];
    }

    dates() {
        return {
            'last_login' : 'YYYY-MM-DD HH:mm:ss',
            'created_at' : 'YYYY-MM-DD HH:mm:ss',
            'updated_at' : 'YYYY-MM-DD HH:mm:ss'
        }
    }

    computed() {
        return {
            name(user) {
                return user.first_name + " " + user.last_name;
            },
            lastLoginFormatted(story) {
                if (story.last_login) {
                    return story.last_login.format('L HH:mm');
                }
                return "";
            },
            createdAtFormatted(story) {
                if (story.created_at) {
                    return story.created_at.format('L HH:mm');
                }
                return "";
            }
        };
    }
}
