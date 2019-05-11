import Lockr from 'lockr';

const ACCESSTOKEN_KEY = 'access_token';
const REFRESHTOKEN_KEY = 'refresh_token';

export default {
  access_token: Lockr.get(ACCESSTOKEN_KEY, null),
  refresh_token: Lockr.get(REFRESHTOKEN_KEY, null),
  clear() {
    this.access_token = null;
    Lockr.rm(ACCESSTOKEN_KEY);
    this.refresh_token = null;
    Lockr.rm(REFRESHTOKEN_KEY);
  },
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
  },
};
