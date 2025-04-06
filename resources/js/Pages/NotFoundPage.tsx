import useHelmet from "../hooks/useHelmet";
import { Typography, Button, Box } from "@mui/material";
import React, { useEffect } from "react";
import { Link, Head } from "@inertiajs/react";

const NotFound = () => {
    return (
        <>
            <Head>
                <title>Not Found | AboutCSGO</title>
                <meta
                    head-key="description"
                    name="description"
                    content="Learn more about CS:GO skins and weapons."
                />
            </Head>

            <style>
                {`
          body {
            margin: 0;
            padding: 0;
          }
        `}
            </style>

            <Box
                sx={{
                    display: "flex",
                    flexDirection: "column",
                    alignItems: "center",
                    justifyContent: "center",
                    height: "100vh",
                    textAlign: "center",
                    padding: 2, // MUI v5 uses the theme's spacing unit, equivalent to 8px by default
                }}
            >
                <Typography variant="h1">404</Typography>
                <Typography variant="h5">Page Not Found</Typography>
                <Typography variant="body1">
                    Sorry, the page you are looking for does not exist.
                </Typography>
                <Button
                    variant="contained"
                    color="primary"
                    component={Link}
                    href="/"
                    sx={{ mt: 2 }} // Add margin-top using the sx prop
                >
                    Go to Home
                </Button>
            </Box>
        </>
    );
};

export default NotFound;
