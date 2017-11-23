import Lockr from 'lockr';
import client from './client';

const USER_KEY = 'user';
const ACCESSTOKEN_KEY = 'access_token';
const REFRESHTOKEN_KEY = 'refresh_token';

const CLIENT_ID = 'clubman';
const CLIENT_SECRET = 'abc123';

class OAuth
{
    constructor() {
        this.access_token = Lockr.get(ACCESSTOKEN_KEY, null);
        this.refresh_token = Lockr.get(REFRESHTOKEN_KEY, null);
        this.user = Lockr.get(USER_KEY, null);
    }

    isGuest() {
        return this.access_token == null;
    }

    isAuthenticated() {
        return this.access_token != null;
    }

    refreshToken() {
        return new Promise((resolve, reject) => {
            var form = new FormData();
            form.append('grant_type', 'refresh_token');
            form.append('client_id', CLIENT_ID);
            form.append('client_secret', CLIENT_SECRET);
            form.append('refresh_token', this.refresh_token);
            client().withoutAuth().post('api/auth/access_token', {
                data : form
            }).then((response) => {
                this.access_token = response.data.access_token;
                Lockr.set(ACCESSTOKEN_KEY, response.data.access_token);
                this.refresh_token = response.data.access_token;
                Lockr.set(REFRESHTOKEN_KEY, response.data.refresh_token);
                resolve(response);
            }).catch((err) => {
                console.log(err.response);
                reject(err);
            });
        })
    }

    login(username, password) {
        return new Promise((resolve, reject) => {
            var form = new FormData();
            form.append('grant_type', 'password');
            form.append('client_id', CLIENT_ID);
            form.append('client_secret', CLIENT_SECRET);
            form.append('username', username);
            form.append('password', password);
            form.append('scope', 'basic');
            client().withoutAuth().post('api/auth/access_token', {
                data : form
            }).then((response) => {
                this.access_token = response.data.access_token;
                Lockr.set(ACCESSTOKEN_KEY, response.data.access_token);
                this.refresh_token = response.data.refresh_token;
                Lockr.set(REFRESHTOKEN_KEY, response.data.refresh_token);
                resolve(response);
            }).catch((err) => {
                console.log(err.response.status);
                reject(err);
            });
        });
    }

    logout() {
        this.access_token = null;
        Lockr.rm(ACCESSTOKEN_KEY);
        this.refresh_token = null;
        Lockr.rm(REFRESHTOKEN_KEY);
    }
}

export default OAuth;
