/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        bg: '#E6E6E8',       // Background color
        text: '#222222',     // Text color
        white: '#FFFFFF',    // White color
        main: '#5D87EE',     // Main accent color
        bg2: '#F5F5F5',      // Secondary background color
      },
      fontFamily: {
        inter: ['Inter', 'sans-serif'],          // Custom Inter Font
        montserrat: ['Montserrat', 'sans-serif'], // Custom Montserrat Font
      },
    },
  },
  plugins: [],
}
