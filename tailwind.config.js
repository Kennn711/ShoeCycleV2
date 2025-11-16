/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {},
    },
    plugins: [require("daisyui")],
    daisyui: {
        themes: ["light"], // Hanya tema light
        darkTheme: "light", // Force light mode
        base: true,
        styled: true,
        utils: true,
    },
};
