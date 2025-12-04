module.exports = {
  content: [
    "./**/*.php",
    "./**/*.html",
    "./assets/js/**/*.js",
    "./css/**/*.css"
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: "#1e40af", // blue-800
          50: "#eff6ff", // blue-50
          100: "#dbeafe", // blue-100
          500: "#3b82f6", // blue-500
          600: "#2563eb", // blue-600
          700: "#1d4ed8", // blue-700
          800: "#1e40af", // blue-800
          900: "#1e3a8a", // blue-900
        },
        secondary: {
          DEFAULT: "#64748b", // slate-500
          50: "#f8fafc", // slate-50
          100: "#f1f5f9", // slate-100
          200: "#e2e8f0", // slate-200
          300: "#cbd5e1", // slate-300
          400: "#94a3b8", // slate-400
          500: "#64748b", // slate-500
          600: "#475569", // slate-600
          700: "#334155", // slate-700
          800: "#1e293b", // slate-800
        },
        accent: {
          DEFAULT: "#0891b2", // cyan-600
          50: "#ecfeff", // cyan-50
          100: "#cffafe", // cyan-100
          500: "#06b6d4", // cyan-500
          600: "#0891b2", // cyan-600
          700: "#0e7490", // cyan-700
        },
        background: "#f8fafc", // slate-50
        surface: "#ffffff", // white
        text: {
          primary: "#0f172a", // slate-900
          secondary: "#475569", // slate-600
        },
        success: {
          DEFAULT: "#059669", // emerald-600
          50: "#ecfdf5", // emerald-50
          100: "#d1fae5", // emerald-100
          500: "#10b981", // emerald-500
          600: "#059669", // emerald-600
        },
        warning: {
          DEFAULT: "#d97706", // amber-600
          50: "#fffbeb", // amber-50
          100: "#fef3c7", // amber-100
          500: "#f59e0b", // amber-500
          600: "#d97706", // amber-600
        },
        error: {
          DEFAULT: "#dc2626", // red-600
          50: "#fef2f2", // red-50
          100: "#fee2e2", // red-100
          500: "#ef4444", // red-500
          600: "#dc2626", // red-600
        },
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
        inter: ['Inter', 'sans-serif'],
        mono: ['JetBrains Mono', 'monospace'],
        data: ['JetBrains Mono', 'monospace'],
      },
      fontSize: {
        'xs': ['0.75rem', { lineHeight: '1rem' }],
        'sm': ['0.875rem', { lineHeight: '1.25rem' }],
        'base': ['1rem', { lineHeight: '1.5rem' }],
        'lg': ['1.125rem', { lineHeight: '1.75rem' }],
        'xl': ['1.25rem', { lineHeight: '1.75rem' }],
        '2xl': ['1.5rem', { lineHeight: '2rem' }],
        '3xl': ['1.875rem', { lineHeight: '2.25rem' }],
        '4xl': ['2.25rem', { lineHeight: '2.5rem' }],
      },
      boxShadow: {
        'card': '0 1px 3px rgba(0, 0, 0, 0.1)',
        'modal': '0 10px 25px rgba(0, 0, 0, 0.15)',
        'focus': '0 0 0 3px rgba(59, 130, 246, 0.1)',
      },
      borderRadius: {
        'sm': '0.25rem',
        'md': '0.375rem',
        'lg': '0.5rem',
        'xl': '0.75rem',
      },
      transitionDuration: {
        '200': '200ms',
        '300': '300ms',
      },
      transitionTimingFunction: {
        'out': 'ease-out',
      },
      spacing: {
        '18': '4.5rem',
        '88': '22rem',
        '128': '32rem',
      },
      maxWidth: {
        '8xl': '88rem',
        '9xl': '96rem',
      },
      zIndex: {
        '60': '60',
        '70': '70',
        '80': '80',
        '90': '90',
        '100': '100',
      },
    },
  },
  plugins: [
    function({ addUtilities }) {
      const newUtilities = {
        '.transition-micro': {
          transition: 'all 200ms ease-out',
        },
        '.transition-smooth': {
          transition: 'all 300ms ease-out',
        },
        '.focus-ring': {
          '&:focus': {
            outline: 'none',
            'box-shadow': '0 0 0 3px rgba(59, 130, 246, 0.1)',
          },
        },
      }
      addUtilities(newUtilities)
    }
  ],
}