import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
  purpose: 'training_definition',
  can: {
    read: () => true,
    update(def) {
      if (this.child.authenticated) {
        return true;
      }
      return false;
    },
    create(def) {
      if (this.child.authenticated) {
        return true;
      }
      return false;
    },
    remove(def) {
      if (this.child.authenticated) {
        return true;
      }
      return false;
    }
  },
});
