import React, { useEffect } from "react";
import Header from "../components/Header";
import { Container, useTheme } from "@mui/material";
import { Head } from "@inertiajs/react";
import Footer from "../components/Footer";

interface LayoutProps {
    children: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ children }) => {
    const theme = useTheme();
    const googleAnalyticsId = "G-VQN1685HGW";

    useEffect(() => {
        const script = document.createElement("script");
        script.src = `https://www.googletagmanager.com/gtag/js?id=${googleAnalyticsId}`;
        script.async = true;
        document.head.appendChild(script);

        const script2 = document.createElement("script");
        script2.innerHTML = `
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '${googleAnalyticsId}');
        `;
        document.head.appendChild(script2);
    }, [googleAnalyticsId]);

    return (
        <>
            <Head>
                <title>AboutCSGO</title>
                <meta
                    head-key="description"
                    name="description"
                    content="AboutCSGO home page where you can find all the prices and info about CS2 skins"
                />
                <link rel="icon" type="image/svg+xml" href="/logo.png" />
            </Head>

            <div>
                <header>
                    <Header />
                </header>

                <main>
                    <Container
                        style={{
                            backgroundColor: theme.palette.background.default,
                            borderRadius: "5px",
                            textAlign: "center",
                            marginTop: "30px",
                            paddingTop: "20px",
                            minHeight: "calc(100vh - 64px)",
                        }}
                    >
                        {children}
                    </Container>
                </main>

                <footer>
                    <Footer />
                </footer>
            </div>
        </>
    );
};

export default Layout;
