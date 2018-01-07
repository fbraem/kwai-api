import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
    purpose: 'category',
    can: {
        read: () => true,
        update(category) {
            if (this.child.authenticated) {
                return true;
            }
            return false;
        },
        create(category) {
            if (this.child.authenticated) {
                return true;
            }
            return false;
        },
        remove(category) {
            if (this.child.authenticated) {
                return true;
            }
            return false;
        }
    }
});
