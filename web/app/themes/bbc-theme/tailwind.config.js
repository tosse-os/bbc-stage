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
          primaryFontDark: '#0c4d63',
          primaryHover: '#387e93',
        },
      },
      fontFamily: {
        sans: ['"Open Sans"', 'system-ui', 'sans-serif'],
      },
      maxWidth: {
        content: '1200px',
      },
    },
  },
  plugins: [],
}
