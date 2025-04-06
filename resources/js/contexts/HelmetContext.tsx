import React, { useState } from "react";
import { Helmet } from "react-helmet-async";

const helmetContext = React.createContext({});

const HelmetProvider = (props: any) => {
    const googleAnalyticsId = "G-VQN1685HGW";

    const [title, setTitle] = useState("AboutCSGO"); // Default title
    const [meta, setMeta] = useState([
        {
            name: "description",
            content: "Find the best and cheapest CS2 skins with AboutCSGO",
        },
        {
            name: "viewport",
            content: "width=device-width, initial-scale=1",
        },
        {
            name: "robots",
            content: "index, follow",
        },
    ]); // Default meta tags

    const [link, setLink] = useState([
        {
            rel: "shortcut icon",
            href: "https://www.aboutcsgo.com/assets/logo-CEbGuhRT.png",
        },
        {
            rel: "canonical",
            href: "https://www.aboutcsgo.com/",
        },
    ]); // Default links

    const [script, setScript] = useState([
        {
            async: true,
            src: `https://www.googletagmanager.com/gtag/js?id=${googleAnalyticsId}`,
        },
        {
            innerHTML: `
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', "${googleAnalyticsId}");
        `,
            type: "text/javascript",
        },
    ]);

    const [style, setStyle] = useState<string | []>("");

    return (
        <helmetContext.Provider
            value={{
                title,
                setTitle,
                meta,
                setMeta,
                link,
                setLink,
                script,
                setScript,
                style,
                setStyle,
            }}
        >
            <Helmet>
                {title && <title>{title}</title>}
                {meta && meta.map((m, index) => <meta key={index} {...m} />)}
                {link && link.map((l, index) => <link key={index} {...l} />)}
                {script &&
                    script.map((s, index) => <script key={index} {...s} />)}
                {style && typeof style === "string" && <style>{style}</style>}
                {style &&
                    Array.isArray(style) &&
                    style.map((s, index) => <style key={index}>{s}</style>)}
            </Helmet>

            {props.children}
        </helmetContext.Provider>
    );
};

// eslint-disable-next-line react-refresh/only-export-components
export { helmetContext };
export default HelmetProvider;
