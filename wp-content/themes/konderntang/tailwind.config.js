/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./**/*.php",
    "./assets/js/**/*.js"
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Sarabun', 'sans-serif'],
        heading: ['Kanit', 'sans-serif'],
      },
      colors: {
        primary: '#0ea5e9',   
        secondary: '#f97316', 
        dark: '#1e293b',
        light: '#f8fafc',
      }
    },
  },
  plugins: [],
}
