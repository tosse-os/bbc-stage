export default {
  content: [
    './resources/**/*.{blade.php,js,jsx,ts,tsx,vue}',
    './app/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          dark: '#020b1a',
          light: '#e5eef3',
          primary: '#40889e',
          primaryHover: '#22b8ca',
        },
      },
    },
  },
  plugins: [],
}
