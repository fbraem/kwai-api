import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
    purpose: 'team_type',
    can: {
        read: () => true,
        update(type) {
            if (this.child.authenticated) {
                return true;
            }
            return false;
        },
        create(type) {
            if (this.child.authenticated) {
                return true;
            }
            return false;
        },
        remove(type) {
            if (this.child.authenticated) {
                return true;
            }
            return false;
        }
    }
});
