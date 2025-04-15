import { createTheme, responsiveFontSizes } from "@mui/material/styles";

let darkTheme = createTheme({
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
        h1: {
            fontSize: "2.5rem", // Adjust this value to your desired base font size
            // Other h1 styles can be added here
        },
    },
});

darkTheme = responsiveFontSizes(darkTheme);

export default darkTheme;
