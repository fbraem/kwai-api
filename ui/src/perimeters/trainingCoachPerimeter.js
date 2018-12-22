import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
  purpose: 'training_coach',
  can: {
    read: () => true,
    update(coach) {
      if (this.child.authenticated) {
        return true;
      }
      return false;
    },
    create(coach) {
      if (this.child.authenticated) {
        return true;
      }
      return false;
    },
    remove(coach) {
      if (this.child.authenticated) {
        return true;
      }
      return false;
    }
  },
});
