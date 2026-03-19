export default {
  content: [
    './resources/**/*.{blade.php,js,jsx,ts,tsx,vue}',
    './app/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          dark: '#0c4d63',
          light: '#acdbe5',
          primary: '#40889e',
          primaryHover: '#acdbe5',
        },
      },
    },
  },
  plugins: [],
}
