import React from "react";
import { hydrateRoot } from "react-dom/client";
import { createInertiaApp } from "@inertiajs/react";
import { ThemeProvider, CssBaseline } from "@mui/material";
import { HelmetProvider } from "react-helmet-async"; // ✅ Import HelmetProvider
import darkTheme from "./theme/Theme";
import createEmotionCache from "./utils/createEmotionCache";
import Layout from "./Layouts/Layout";
import "./css/app.css";
createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.tsx", { eager: true });
        let page = pages[`./Pages/${name}.tsx`];

        if (!page || !page.default) {
            throw new Error(`Page ${name} not found or has no default export`);
        }

        page.default.layout =
            page.default.layout || ((page) => <Layout>{page}</Layout>);

        return page.default;
    },
    setup({ el, App, props }) {
        hydrateRoot(
            el,
            // <HelmetProvider>
            <ThemeProvider theme={darkTheme}>
                <CssBaseline />
                <App {...props} />
            </ThemeProvider>,
            /* </HelmetProvider>, */
        );
    },
});
