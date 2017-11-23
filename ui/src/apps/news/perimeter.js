import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
    purpose: 'story',
    can: {
        read: () => true,
        update(story) {
            if (this.child.isAuthenticated()) {
                return true;
            }
            return false;
        },
        create(story) {
            if (this.child.isAuthenticated()) {
                return true;
            }
            return false;
        },
        remove(story) {
            if (this.child.isAuthenticated()) {
                return true;
            }
            return false;
        }
    }
});
