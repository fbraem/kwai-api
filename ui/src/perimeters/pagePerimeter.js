import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
  purpose: 'page',
  can: {
    read: () => true,
    update(page) {
      if (this.child.authenticated) {
        return true;
      }
      return false;
    },
    create(page) {
      if (this.child.authenticated) {
        return true;
      }
      return false;
    },
    remove(page) {
      if (this.child.authenticated) {
        return true;
      }
      return false;
    },
  },
});
