import { Head, usePage } from "@inertiajs/react";
import { Link } from "@inertiajs/react";
import {
    Box,
    Card,
    CardContent,
    Typography,
    Grid,
    CardMedia,
    Container,
    useTheme,
} from "@mui/material";
import { PageProps } from "../types"; // Ensure this includes Item[]
import React from "react";

const ItemCategoryLayout = () => {
    const theme = useTheme();

    // Get data from Inertia's usePage()
    const { weaponName, skins } = usePage<PageProps>().props;

    const weaponNameTitle = weaponName ?? "Unknown Weapon";

    return (
        <>
            <Head>
                <title>{`All ${weaponNameTitle} Skins | AboutCSGO`}</title>
                <meta
                    head-key="description"
                    name="description"
                    content={`All the ${weaponNameTitle} skins and prices | AboutCSGO`}
                />
                <link
                    head-key="canonical"
                    rel="canonical"
                    href={`https://www.aboutcsgo.com/weapon/${weaponNameTitle}`}
                />
            </Head>

            <Container
                style={{ backgroundColor: theme.palette.background.default }}
            >
                <Box sx={{ padding: 2, justifyContent: "center" }}>
                    <Typography variant="h1" gutterBottom align="center">
                        {weaponName} Skins
                    </Typography>
                    <Grid container spacing={2}>
                        {skins.map((item) => (
                            <Grid size={{ xs: 12, md: 4, sm: 4 }} key={item.id}>
                                <Link
                                    href={`/skin/${weaponName}/${item.skin}`}
                                    style={{
                                        textDecoration: "none",
                                        color: "inherit",
                                    }}
                                >
                                    <Card
                                        sx={{
                                            height: "100%",
                                            display: "flex",
                                            flexDirection: "column",
                                        }}
                                    >
                                        <CardContent>
                                            <Typography
                                                variant="h6"
                                                component="h1"
                                                gutterBottom
                                                sx={{ textAlign: "center" }}
                                            >
                                                {item.skin}
                                            </Typography>
                                            <Typography
                                                sx={{
                                                    backgroundColor:
                                                        item.quality_color,
                                                    color: "#fff",
                                                    padding: "4px 8px",
                                                    borderRadius: "4px",
                                                    textAlign: "center",
                                                }}
                                                gutterBottom
                                                align="center"
                                            >
                                                {item.quality}
                                            </Typography>
                                            {item.stattrak === 1 && (
                                                <Typography
                                                    variant="body2"
                                                    align="center"
                                                    sx={{
                                                        backgroundColor:
                                                            "#F89406",
                                                        color: "#fff",
                                                        padding: "4px 8px",
                                                        borderRadius: "4px",
                                                        textAlign: "center",
                                                        textShadow:
                                                            "1px 1px 2px black, 0 0 1em black, 0 0 0.2em black",
                                                    }}
                                                >
                                                    Stattrak available
                                                </Typography>
                                            )}
                                            {item.souvenir === 1 && (
                                                <Typography
                                                    variant="body2"
                                                    align="center"
                                                    sx={{
                                                        backgroundColor:
                                                            "#ffd900",
                                                        color: "#fff",
                                                        padding: "4px 8px",
                                                        borderRadius: "4px",
                                                        textAlign: "center",
                                                        textShadow:
                                                            "1px 1px 2px black, 0 0 1em black, 0 0 0.2em black",
                                                    }}
                                                >
                                                    Souvenir available
                                                </Typography>
                                            )}
                                        </CardContent>
                                        <Box
                                            sx={{
                                                flexGrow: 1,
                                                display: "flex",
                                                alignItems: "center",
                                                justifyContent: "center",
                                            }}
                                        >
                                            <CardMedia
                                                component="img"
                                                width="100%"
                                                image={item.image_url}
                                                alt={item.skin}
                                                style={{
                                                    backgroundColor:
                                                        "transparent",
                                                    objectFit: "cover",
                                                }}
                                            />
                                        </Box>
                                        <CardContent sx={{ flexGrow: 0 }}>
                                            <Typography
                                                variant="body2"
                                                align="center"
                                                sx={{
                                                    color: "#fff",
                                                    padding: "4px 8px",
                                                }}
                                            >
                                                €{item.prices.normal.lowest} - €
                                                {item.prices.normal.highest}
                                            </Typography>
                                            {item.prices.stattrak ? (
                                                <Typography
                                                    variant="body2"
                                                    align="center"
                                                    sx={{
                                                        color: "#F89406",
                                                        padding: "4px 8px",
                                                        marginTop: "0px",
                                                    }}
                                                >
                                                    €
                                                    {
                                                        item.prices.stattrak
                                                            .lowest
                                                    }{" "}
                                                    - €
                                                    {
                                                        item.prices.stattrak
                                                            .highest
                                                    }
                                                </Typography>
                                            ) : (
                                                item.prices.souvenir && (
                                                    <Typography
                                                        variant="body2"
                                                        align="center"
                                                        sx={{
                                                            color: "#ffd900",
                                                            padding: "4px 8px",
                                                            marginTop: "0px",
                                                        }}
                                                    >
                                                        €
                                                        {
                                                            item.prices.souvenir
                                                                .lowest
                                                        }{" "}
                                                        - €
                                                        {
                                                            item.prices.souvenir
                                                                .highest
                                                        }
                                                    </Typography>
                                                )
                                            )}
                                        </CardContent>
                                    </Card>
                                </Link>
                            </Grid>
                        ))}
                    </Grid>
                </Box>
            </Container>
        </>
    );
};

export default ItemCategoryLayout;
