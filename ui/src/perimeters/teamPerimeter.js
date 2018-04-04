import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
    purpose: 'team',
    can: {
        read: () => true,
        update(team) {
            if (this.child.authenticated) {
                return true;
            }
            return false;
        },
        create(team) {
            if (this.child.authenticated) {
                return true;
            }
            return false;
        },
        remove(team) {
            if (this.child.authenticated) {
                return true;
            }
            return false;
        },
        addMember(team) {
            if (this.child.authenticated) {
                return true;
            }
            return false;
        }
    }
});
