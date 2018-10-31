import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
    purpose: 'season',
    can: {
        read: () => true,
        update(season) {
            if (this.child.authenticated) {
                return true;
            }
            return false;
        },
        create(season) {
            if (this.child.authenticated) {
                return true;
            }
            return false;
        },
        remove(season) {
            if (this.child.authenticated) {
                return season.teams == null;
            }
            return false;
        }
    }
});
