import Lockr from 'lockr';

const USER_KEY = 'user';
const ACCESSTOKEN_KEY = 'access_token';
const REFRESHTOKEN_KEY = 'refresh_token';
const CLIENT_ID = 'judokwaikemzeke';

class TokenStore {
  constructor() {
    this.access_token = Lockr.get(ACCESSTOKEN_KEY, null);
    this.refresh_token = Lockr.get(REFRESHTOKEN_KEY, null);
    this.user = Lockr.get(USER_KEY, null);
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

  isGuest() {
    return this.access_token == null;
  }

  isAuthenticated() {
    return this.access_token != null;
  }

  getAccessToken() {
    return this.access_token;
  }

  getRefreshToken() {
    return this.refresh_token;
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
}

export default TokenStore;
