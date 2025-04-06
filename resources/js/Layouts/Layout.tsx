import React from "react";
import Header from "../Components/Header"; // Import the Header component
import { Container, useTheme } from "@mui/material";
import { Head } from "@inertiajs/react";
import Footer from "../components/Footer";

interface LayoutProps {
    children: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ children }) => {
    const theme = useTheme();

    return (
        <>
            <Head>
                <title>AboutCSGO</title>
                <meta
                    head-key="description"
                    name="description"
                    content="AboutCSGO home page where you can find all the prices and info about CS2 skins"
                />
                <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
            </Head>

            <div>
                <header>
                    <Header />
                </header>
                {/* Render the header at the top */}

                {/* Render the page content passed as children */}
                <main>
                    <Container
                        style={{
                            backgroundColor: theme.palette.background.default,
                            borderRadius: "5px",
                            textAlign: "center", // Align content (including the image) to center
                            marginTop: "30px",
                            paddingTop: "20px",
                            minHeight: "calc(100vh - 64px)", // Subtract header height
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
