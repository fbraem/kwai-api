module.exports = {
  theme: {
    extend: {
      colors: {
        tatami: '#969A86'
      }
    },
  },
  variants: [
    'responsive',
    'first',
    'last',
    'odd',
    'even',
    'hover',
    'focus',
    'active',
    'disabled',
  ],
  plugins: [
    require('tailwindcss-tables')(),
  ]
};
