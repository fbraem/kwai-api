import { Model } from 'sarala';
import axios from 'axios';
import OAuth from '@/js/oauth';

export default class BaseModel extends Model {
    baseUrl() {
        return 'api';
    }

    request(config) {
        var oauth = new OAuth();
        config.headers = config.headers || {
            'Accept' : 'application/vnd.api+json',
            'Content-Type' : 'application/vnd.api+json'
        };
        if (oauth.isAuthenticated()) {
            config.withCredentials = true;
            var token = oauth.getAccessToken();
            if ( token ) {
                config.headers['Authorization'] = `Bearer ${token}`
            }
        }
        config.url = config.url.replace(/\/$/, "");
        return axios.request(config);
    }

    async call(method, retry) {
        if (!retry) retry = true;
        await method().catch((error) => {
            if (error.response) { // axios problem
                if (error.response.status == 401 && retry) { // Unauthorized
                    var oauth = new OAuth();
                    oauth.refreshToken().then((response) => {
                        this.call(method, false);
                    });
                }
            } else { // Another problem, rethrow it
                throw error;
            }
        });
    }
}
