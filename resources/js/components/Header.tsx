import React from "react";
import { Link } from "@inertiajs/react";
import { AppBar, Box, Container, Toolbar, Typography } from "@mui/material";
import CategoryDropdown from "./CategoryDropdown"; // Import the CategoryDropdown component
import { categories } from "../data/categories"; // Import categories
import ItemSkinSearch from "./ItemSkinSearch";

// Header Component
const Header: React.FC = () => {
    return (
        <Box
            sx={{
                display: "flex",
                flexDirection: "column",
            }}
        >
            <AppBar
                position="sticky"
                sx={{ width: "100vw", overflowX: "hidden" }}
            >
                <Container maxWidth="xl" disableGutters sx={{ px: 0 }}>
                    <Toolbar disableGutters>
                        <Box
                            sx={{
                                display: "flex",
                                justifyContent: "space-between",
                                width: "100%",
                                alignItems: "center",
                            }}
                        >
                            {/* Left part with logo */}
                            <Box
                                sx={{
                                    display: "flex",
                                    alignItems: "center",
                                    mr: 4,
                                    mb: 1,
                                    mt: 1,
                                }}
                            >
                                <Link
                                    href="/"
                                    style={{
                                        display: "inline-flex",
                                        alignItems: "center",
                                    }}
                                >
                                    <img
                                        src="/logo.png"
                                        alt="AboutCSGO Logo"
                                        style={{
                                            maxWidth: "150px",
                                            maxHeight: "75px",
                                        }}
                                    />
                                </Link>
                            </Box>

                            {/* Categories centered */}
                            <Box
                                sx={{
                                    display: "flex",
                                    alignItems: "center",
                                    justifyContent: "center",
                                    flex: 1,
                                }}
                            >
                                {categories.map((category) => (
                                    <CategoryDropdown
                                        key={category.id}
                                        category={category}
                                    />
                                ))}
                            </Box>

                            {/* Right part with text */}
                            <Box sx={{ display: "flex", alignItems: "center" }}>
                                <ItemSkinSearch compact />
                            </Box>
                        </Box>
                    </Toolbar>
                </Container>
            </AppBar>
        </Box>
    );
};

export default Header;
