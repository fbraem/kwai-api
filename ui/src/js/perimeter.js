import { createPerimeter } from 'vue-kindergarten';

export default createPerimeter({
    purpose : 'base',
    can : {
        login() {
            return !this.isLoggedIn();
        },
        logout() {
            return this.isLoggedIn();
        }
    },
    isLoggedIn() {
        return this.child != null;
    }
});
