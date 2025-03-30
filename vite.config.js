import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    mode: "development", // Explicitly set to development mode
    build: {
        chunkSizeWarningLimit: 100,
        rollupOptions: {
            onwarn(warning, warn) {
                if (warning.code === "MODULE_LEVEL_DIRECTIVE") {
                    return;
                }
                warn(warning);
            },
        },
    },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.jsx"],
            ssr: "resources/js/ssr.jsx",
            refresh: true,
        }),
    ],
});
