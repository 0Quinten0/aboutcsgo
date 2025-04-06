import React from "react";
import { Container, Typography, Box, Link, useTheme } from "@mui/material";

const Footer: React.FC = () => {
    const theme = useTheme();

    return (
        <Box
            sx={{
                backgroundColor: theme.palette.background.paper,
                color: "#fff",
                padding: "20px 0",
                textAlign: "center",
                marginTop: "auto", // Make sure it stays at the bottom
            }}
        >
            <Container>
                <Typography variant="body2" align="center">
                    &copy; {new Date().getFullYear()} AboutCSGO. All rights
                    reserved.
                </Typography>
                <Typography variant="body2" align="center">
                    <Link href="/privacy-policy" color="inherit">
                        Privacy Policy
                    </Link>{" "}
                    |{" "}
                    <Link href="/terms-of-service" color="inherit">
                        Terms of Service
                    </Link>
                </Typography>
            </Container>
        </Box>
    );
};

export default Footer;
