/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                terracotta: {
                    DEFAULT: '#E87722', // Main terracotta color
                    50: '#FDF7F2',
                    100: '#FBECE2',
                    200: '#F7D6C1',
                    300: '#F2BF9F',
                    400: '#EEA97D',
                    500: '#E87722', // Equivalent to DEFAULT
                    600: '#D6651E',
                    700: '#B05318',
                    800: '#8A4012',
                    900: '#652D0C',
                    950: '#401A06',
                }
            },
        },
    },
    plugins: [],
};
