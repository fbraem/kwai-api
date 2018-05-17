import Model from '@/js/JSONAPI/BaseModel';
import moment from 'moment';

export default class Season extends Model {
    resourceName() {
        return 'seasons';
    }

    fields() {
        return [
            'name',
            'remark'
        ];
    }

    dates() {
        return {
            'start_date' : 'YYYY-MM-DD',
            'end_date' : 'YYYY-MM-DD',
            'created_at' : 'YYYY-MM-DD HH:mm:ss',
            'updated_at' : 'YYYY-MM-DD HH:mm:ss'
        }
    }

    computed() {
        return {
            formatted_start_date(season) {
                if (season.start_date) {
                    return season.start_date.format('L');
                }
                return "";
            },
            formatted_end_date(season) {
                if (season.end_date) {
                    return season.end_date.format('L');
                }
                return "";
            },
            formatted_created_at(season) {
                if (season.created_at) {
                    return season.created_at.format('L');
                }
                return "";
            },
            formatted_updated_at(season) {
                if (season.updated_at) {
                    return season.updated_at.format('L');
                }
                return "";
            },
            active(season) {
                var today = moment();
                return today.isBetween(season.start_date, season.end_date)
                    || today.isSame(season.start_date)
                    || today.isSame(season.end_date);
            }
        }
    }
}
