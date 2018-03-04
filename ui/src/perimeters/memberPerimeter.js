import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
    purpose: 'member',
    can: {
        read: () => true,
        update(member) {
            if (this.child.authenticated) {
                return true;
            }
            return false;
        },
        create(member) {
            if (this.child.authenticated) {
                return true;
            }
            return false;
        },
        remove(member) {
            if (this.child.authenticated) {
                return true;
            }
            return false;
        }
    }
});
