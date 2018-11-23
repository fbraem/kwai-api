import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
  purpose: 'user',
  can: {
    read: () => true,
    update(user) {
      if (this.child.authenticated) {
        return true;
      }
      return false;
    },
    create(user) {
      if (this.child.authenticated) {
        return true;
      }
      return false;
    },
    remove(user) {
      if (this.child.authenticated) {
        return true;
      }
      return false;
    },
  },
});
