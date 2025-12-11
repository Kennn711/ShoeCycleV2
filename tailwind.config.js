/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            transitionProperty: {
                all: "all",
                colors: "color, background-color, border-color, outline-color, fill, stroke",
                opacity: "opacity",
                transform: "transform",
            },
        },
    },
    plugins: [
        require("@tailwindcss/typography"),
        require("@tailwindcss/forms"),
        require("daisyui"),
    ],
    daisyui: {
        themes: ["light"], // Hanya tema light
        darkTheme: "light", // Force light mode
        base: true,
        styled: true,
        utils: false,
    },
};
