import React from "react";
import Header from "../Components/Header"; // Import the Header component
import { Container, useTheme } from "@mui/material";

interface LayoutProps {
    children: React.ReactNode;
}

const Layout: React.FC<LayoutProps> = ({ children }) => {
    const theme = useTheme();

    return (
        <div>
            <header>
                <Header />
            </header>
            {/* Render the header at the top */}

            {/* Render the page content passed as children */}
            <main>
                <Container
                    style={{
                        backgroundColor: theme.palette.background.paper,
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
        </div>
    );
};

export default Layout;
