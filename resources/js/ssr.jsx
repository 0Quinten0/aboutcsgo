// resources/js/server.jsx
import React from "react";
import ReactDOMServer from "react-dom/server";
import { createInertiaApp } from "@inertiajs/react";
import createServer from "@inertiajs/react/server";
import { ThemeProvider, CssBaseline } from "@mui/material"; // Import the ThemeProvider
import darkTheme from "./theme/Theme"; // Import your theme
import Layout from "./Layouts/Layout"; // Import your layout

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
            <ThemeProvider theme={darkTheme}>
                <CssBaseline />
                <App {...props} />
            </ThemeProvider>
        ),
    }),
);
