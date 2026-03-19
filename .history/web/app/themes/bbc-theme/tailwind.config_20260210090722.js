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
      borderWidth: {
        ui: '2px',
      },
      opacity: {
        ui: '0.7',
      },
    },
  },
  plugins: [
    function ({ addBase, theme }) {
      const hexToRgb = hex =>
        hex
          .replace('#', '')
          .match(/.{1,2}/g)
          .map(v => parseInt(v, 16))
          .join(' ')

      addBase({
        ':root': {
          '--color-brand-primary': hexToRgb(theme('colors.brand.primary')),
          '--border-ui-width': theme('borderWidth.ui'),
          '--border-ui-opacity': theme('opacity.ui'),
          '--border-ui': `var(--border-ui-width) solid rgb(var(--color-brand-primary) / var(--border-ui-opacity))`,
        },
      })
    },
  ],
}
