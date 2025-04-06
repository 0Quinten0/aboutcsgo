import React from "react";
import ReactDOMServer from "react-dom/server";
import { createInertiaApp } from "@inertiajs/react";
import createServer from "@inertiajs/react/server";
import { ThemeProvider, CssBaseline } from "@mui/material"; // MUI Theme
import { HelmetProvider } from "react-helmet-async"; // ✅ Import HelmetProvider
import darkTheme from "./theme/Theme";
import Layout from "./Layouts/Layout";
import "./css/app.css";
createServer((page) =>
    createInertiaApp({
        page,
        render: ReactDOMServer.renderToString,
        resolve: (name) => {
            const pages = import.meta.glob("./Pages/**/*.tsx", { eager: true });
            let page = pages[`./Pages/${name}.tsx`];

            page.default.layout =
                page.default.layout || ((page) => <Layout>{page}</Layout>);

            return page;
        },

        setup: ({ App, props }) => (
            // <HelmetProvider>
            <ThemeProvider theme={darkTheme}>
                <CssBaseline />
                <App {...props} />
            </ThemeProvider>
            /* </HelmetProvider> */
        ),
    }),
);
