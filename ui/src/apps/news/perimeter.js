import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
    purpose: 'story',
    can: {
        read: () => true,
        update(story) {
            if (this.child) {
                return true;
            }
            return false;
        },
        create(story) {
            if (this.child) {
                return true;
            }
            return false;
        }
    }
});
