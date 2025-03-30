// resources/theme/Theme.tsx

import { createTheme, responsiveFontSizes } from "@mui/material";

export let darkTheme = createTheme({
    palette: {
        primary: {
            light: "#4169E1",
            main: "#2d3844",
            dark: "#87a3bf",
        },
        background: {
            default: "#1b1f23",
            paper: "#2d3844",
        },
        text: {
            primary: "#FFFFFF",
            secondary: "#ecf041",
        },
    },
    typography: {
        fontFamily: ["Poppins", "Oswald", "sans-serif"].join(","),
    },
});

darkTheme = responsiveFontSizes(darkTheme);

export default darkTheme; // Make sure this is the default export
