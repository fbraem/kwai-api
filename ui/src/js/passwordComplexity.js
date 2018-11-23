export default password => {
  // Minimum of 1 Uppercase Letter
  if (/[A-Z]/.test(password) === false)
    return false;

  // Minimum of 1 Number
  if (/\d/.test(password) === false)
    return false;

  return true;
};
