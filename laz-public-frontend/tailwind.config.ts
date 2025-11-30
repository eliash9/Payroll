import type { Config } from "tailwindcss";

export default {
    content: [
        "./src/pages/**/*.{js,ts,jsx,tsx,mdx}",
        "./src/components/**/*.{js,ts,jsx,tsx,mdx}",
        "./src/app/**/*.{js,ts,jsx,tsx,mdx}",
    ],
    theme: {
        extend: {
            colors: {
                background: "var(--background)",
                foreground: "var(--foreground)",
                laz: {
                    green: {
                        DEFAULT: '#97C00E', // Lime green from logo
                        50: '#F4F9E2',
                        100: '#E9F4C6',
                        200: '#D3E98D',
                        300: '#BDDE55',
                        400: '#A7D31C',
                        500: '#97C00E',
                        600: '#799A0B',
                        700: '#5B7308',
                        800: '#3D4D05',
                        900: '#1E2603',
                    },
                    teal: {
                        DEFAULT: '#0F4C5C', // Dark teal from logo
                        50: '#E2F3F6',
                        100: '#C6E7EE',
                        200: '#8DCFD9',
                        300: '#55B7C5',
                        400: '#1C9FB1',
                        500: '#0F4C5C',
                        600: '#0C3D4A',
                        700: '#092D37',
                        800: '#061E25',
                        900: '#030F12',
                    }
                }
            },
        },
    },
    plugins: [],
} satisfies Config;
