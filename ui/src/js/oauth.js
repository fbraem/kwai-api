import Lockr from 'lockr';
import axios from 'axios';

const USER_KEY = 'user';
const ACCESSTOKEN_KEY = 'access_token';
const REFRESHTOKEN_KEY = 'refresh_token';

const CLIENT_ID = 'judokwaikemzeke';

class OAuth
{
    constructor() {
        this.access_token = Lockr.get(ACCESSTOKEN_KEY, null);
        this.refresh_token = Lockr.get(REFRESHTOKEN_KEY, null);
        this.user = Lockr.get(USER_KEY, null);
    }

    get(url, options) {
        var opt = options || Object.create(null);
        opt.method = 'get';
        return this.call(url, opt);
    }

    post(url, options) {
        var opt = options || Object.create(null);
        opt.method = 'post';
        return this.call(url, opt);
    }

    patch(url, options) {
        var opt = options || Object.create(null);
        opt.method = 'patch';
        return this.call(url, opt);
    }

    delete(url, options) {
        var opt = options || Object.create(null);
        opt.method = 'delete';
        return this.call(url, opt);
    }

    call(url, options) {
        var opts = options || Object.create(null);
        opts.headers = opts.headers || {
            'Accept' : 'application/vnd.api+json',
            'Content-Type' : 'application/vnd.api+json'
        };
        opts.withCredentials = true;
        if ( this.access_token) {
            opts.headers['Authorization'] = `Bearer ${this.access_token}`
        }
        opts.url = url;
        return new Promise((resolve, reject) => {
            axios.request(opts)
                .then((response) => {
                    resolve(response);
                }).catch((error) => {
                    if (error.response.status == 401 && !options.dontRetry) {
                        if (this.refresh_token) {
                            this.refreshToken().then((response) => {
                                if (this.access_token) {
                                    opts.headers['Authorization'] = `Bearer ${this.access_token}`
                                    return axios.request(opts)
                                    .then((response) => {
                                        resolve(response);
                                    }).catch((error) => {
                                        reject(error);
                                    });
                                }
                                console.log('No access_token received?');
                            }).catch((error) => {
                                reject(error);
                            })
                        }
                    } else {
                        reject(error);
                    }
                });
            });
    };

    isGuest() {
        return this.access_token == null;
    }

    isAuthenticated() {
        return this.access_token != null;
    }

    getAccessToken() {
        return this.access_token;
    }

    refreshToken() {
        return new Promise((resolve, reject) => {
            var form = new FormData();
            form.append('grant_type', 'refresh_token');
            form.append('client_id', CLIENT_ID);
            form.append('refresh_token', this.refresh_token);
            this.post('api/auth/access_token', {
                data : form,
                dontRetry : true
            }).then((response) => {
                this.setTokens(response.data.access_token, response.data.refresh_token);
                resolve(response);
            }).catch((err) => {
                console.log(err);
                reject(err);
            });
        })
    }

    login(username, password) {
        return new Promise((resolve, reject) => {
            var form = new FormData();
            form.append('grant_type', 'password');
            form.append('client_id', CLIENT_ID);
            form.append('username', username);
            form.append('password', password);
            form.append('scope', 'basic');
            this.post('api/auth/access_token', {
                data : form,
                dontRetry : true
            }).then((response) => {
                this.setTokens(response.data.access_token, response.data.refresh_token);
                resolve(response);
            }).catch((err) => {
                console.log(err.response.status);
                reject(err);
            });
        });
    }

    setTokens(access, refresh) {
        this.access_token = access;
        if (access) {
            Lockr.set(ACCESSTOKEN_KEY, access);
        } else {
            Lockr.rm(ACCESSTOKEN_KEY);
        }
        this.refresh_token = refresh;
        if (refresh) {
            Lockr.set(REFRESHTOKEN_KEY, refresh);
        } else {
            Lockr.rm(REFRESHTOKEN_KEY);
        }
    }

    logout() {
        return new Promise((resolve, reject) => {
            var form = new FormData();
            form.append('refresh_token', this.refresh_token);
            this.post('api/auth/logout', {
                data : form,
                dontRetry : true
            }).then((response) => {
                this.access_token = null;
                Lockr.rm(ACCESSTOKEN_KEY);
                this.refresh_token = null;
                Lockr.rm(REFRESHTOKEN_KEY);
                resolve(response)
            }).catch((err) => {
                if ( err.response.status != 401 ) {
                    reject(err);
                }
            });
        });
    }
}

export default OAuth;
