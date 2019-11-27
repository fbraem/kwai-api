import Lockr from 'lockr';

const USER_RULES_KEY = 'rules';
const USER_KEY = 'user';

export default {
  user: Lockr.get(USER_KEY, null),
  rules: Lockr.get(USER_RULES_KEY, []),

  clear() {
    this.user = null;
    this.rules = null;
  },

  set(user, rules) {
    Lockr.set(USER_RULES_KEY, rules);
    this.rules = rules;
    Lockr.set(USER_KEY, user);
    this.user = user;
  }
};
