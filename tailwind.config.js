/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        gomad: {
          red: '#C1121F',       // Merah Utama
          darkRed: '#8A0F18',   // Merah Gelap
          accent: '#E63946',    // Merah Terang
          black: '#111111',     // Hitam
          white: '#FFFFFF',     // Putih
          gray: '#F5F5F5',      // Abu Muda
          line: '#E5E5E5',      // Garis Border
        },
      },
      fontFamily: {
        sans: ['Geist Sans', 'system-ui', 'sans-serif'],
        mono: ['Geist Mono', 'monospace'],
      },
      borderRadius: {
        gomad: '12px', 
        gomadlg: '20px',
      },
      keyframes: {
        'line-draw': {
          '0%': { 'stroke-dashoffset': '100%' },
          '100%': { 'stroke-dashoffset': '0%' },
        },
        'float-line': {
          '0%, 100%': { transform: 'translateX(0)' },
          '50%': { transform: 'translateX(10px)' },
        }
      },
      animation: {
        'line-draw': 'line-draw 1.5s ease-in-out forwards',
        'float-line': 'float-line 3s ease-in-out infinite',
      }
    },
  },
  plugins: [],
}