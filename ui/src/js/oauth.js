import Lockr from 'lockr';
import axios from 'axios';
import config from 'config';

const USER_KEY = 'user';
const ACCESSTOKEN_KEY = 'access_token';
const REFRESHTOKEN_KEY = 'refresh_token';

const CLIENT_ID = 'judokwaikemzeke';

class OAuth {
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

  clear() {
    this.access_token = null;
    Lockr.rm(ACCESSTOKEN_KEY);
    this.refresh_token = null;
    Lockr.rm(REFRESHTOKEN_KEY);
  }

  getAuthorizationHeader() {
    return `Bearer ${this.access_token}`;
  }

  call(url, options) {
    var opts = options || Object.create(null);
    opts.headers = opts.headers || {
      Accept: 'application/vnd.api+json',
      'Content-Type': 'application/vnd.api+json',
    };
    opts.withCredentials = true;
    if (this.access_token) {
      opts.headers['Authorization'] = this.getAuthorizationHeader();
    }
    opts.url = url;
    return new Promise((resolve, reject) => {
      axios.request(opts)
        .then((response) => {
          resolve(response);
        }).catch((error) => {
          if (error.response.status === 401 && !options.dontRetry) {
            if (this.refresh_token) {
              this.refreshToken().then((response) => {
                if (this.access_token) {
                  opts.headers['Authorization'] = this.getAuthorizationHeader();
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
              });
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

  async refreshToken() {
    var form = new FormData();
    form.append('grant_type', 'refresh_token');
    form.append('client_id', CLIENT_ID);
    form.append('refresh_token', this.refresh_token);
    var response = await this.post(config.api + '/auth/access_token', {
      data: form,
      dontRetry: true,
    });
    this.setTokens(response.data.access_token, response.data.refresh_token);
  }

  async login(username, password) {
    var form = new FormData();
    form.append('grant_type', 'password');
    form.append('client_id', CLIENT_ID);
    form.append('username', username);
    form.append('password', password);
    form.append('scope', 'basic');
    var response = await this.post(config.api + '/auth/access_token', {
      data: form,
      dontRetry: true,
    });
    this.setTokens(response.data.access_token, response.data.refresh_token);
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

  async logout() {
    var form = new FormData();
    form.append('refresh_token', this.refresh_token);
    try {
      await this.post(config.api + '/auth/logout', {
        data: form,
        dontRetry: true,
      });
    } catch (error) {
      console.log(error);
    }
    this.clear();
  }
}

export default OAuth;
