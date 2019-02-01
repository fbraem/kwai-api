import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
  purpose: 'event',
  can: {
    read: () => true,
    update(event) {
      if (this.child.authenticated) {
        return true;
      }
      return false;
    },
    create(event) {
      if (this.child.authenticated) {
        return true;
      }
      return false;
    },
    remove(event) {
      if (this.child.authenticated) {
        return true;
      }
      return false;
    }
  },
});
