export default password => {
    // Minimum of 1 Uppercase Letter
    if(false === /[A-Z]/.test(password))
        return false;

    // Minimum of 1 Number
    if(false === /\d/.test(password))
        return false;

    return true;
};
