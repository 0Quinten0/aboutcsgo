import React, { useState, useEffect } from "react";
import {
    Container,
    Grid,
    Typography,
    useTheme,
    Box,
    GlobalStyles,
} from "@mui/material";
import { useParams } from "react-router-dom";
import { usePage } from "@inertiajs/react"; // Import Inertia's usePage hook
import { SkinData, MarketplacePricesWithDetails } from "../types";
import PriceList from "../components/PriceList";
import PriceDetails from "../components/PriceDetails";

const SkinLayout = () => {
    const { weaponName, skinName } = useParams<{
        weaponName: string;
        skinName: string;
    }>();

    // Get data from Inertia's usePage()
    const { skinData } = usePage<{ skinData: SkinData }>().props; // Extract skinData from Inertia props

    const theme = useTheme();

    const isVanillaSkin = skinData.skin === "Vanilla";
    const exteriorOrder = isVanillaSkin
        ? ["No Exterior"]
        : [
              "Factory New",
              "Minimal Wear",
              "Field-Tested",
              "Well-Worn",
              "Battle-Scarred",
          ];

    const [selectedExterior, setSelectedExterior] = useState(exteriorOrder[0]);
    const [priceType, setPriceType] =
        useState<keyof MarketplacePricesWithDetails>("normal");

    const getLowestPriceAcrossMarketplaces = (
        condition: string,
        type: keyof MarketplacePricesWithDetails,
    ): string | null => {
        let lowestPrice: string | null = null;

        for (const marketplacePrices of Object.values(skinData.prices)) {
            const price = getLowestPriceForCondition(
                condition,
                type,
                marketplacePrices,
            );

            if (
                price &&
                (!lowestPrice || parseFloat(price) < parseFloat(lowestPrice))
            ) {
                lowestPrice = price;
            }
        }

        return lowestPrice;
    };

    const getLowestPriceForCondition = (
        condition: string,
        type: keyof MarketplacePricesWithDetails,
        prices: MarketplacePricesWithDetails,
    ): string | null => {
        switch (type) {
            case "normal":
                return prices.normal?.[condition] ?? null;
            case "stattrak":
                return prices.stattrak?.[condition] ?? null;
            case "souvenir":
                return prices.souvenir?.[condition] ?? null;
            default:
                return null;
        }
    };

    const handlePriceClick = (
        exterior: string,
        type: keyof MarketplacePricesWithDetails,
    ) => {
        setSelectedExterior(exterior);
        setPriceType(type);
    };

    return (
        <>
            <GlobalStyles
                styles={{
                    body: {
                        margin: 0,
                        padding: 0,
                    },
                }}
            />

            <Container
                style={{
                    backgroundColor: theme.palette.background.default,
                    borderRadius: "5px",
                    marginTop: 20,
                    minHeight: "calc(100vh - 64px)",
                }}
            >
                <Grid
                    container
                    spacing={2}
                    justifyContent="center"
                    style={{ maxWidth: 1152, margin: "auto" }}
                >
                    <Grid item xs={12} md={4}>
                        <Box
                            sx={{
                                backgroundColor: theme.palette.background.paper,
                                display: "flex",
                                flexDirection: "column",
                                alignItems: "center",
                                justifyContent: "center",
                                width: "100%",
                                height: "100%",
                                borderRadius: "5px",
                            }}
                        >
                            <Typography
                                variant="h6"
                                component="h1"
                                gutterBottom
                            >
                                {weaponName} {skinName}
                            </Typography>
                            {skinData && (
                                <>
                                    <Typography
                                        sx={{
                                            backgroundColor:
                                                skinData.quality_color,
                                            color: "#fff",
                                            padding: "4px 8px",
                                            borderRadius: "4px",
                                            width: "80%",
                                            textAlign: "center",
                                        }}
                                        gutterBottom
                                        align="center"
                                    >
                                        {skinData.quality}
                                    </Typography>
                                    {skinData.stattrak === 1 && (
                                        <Typography
                                            variant="body2"
                                            align="center"
                                            sx={{
                                                backgroundColor: "#F89406",
                                                color: "#fff",
                                                padding: "4px 8px",
                                                borderRadius: "4px",
                                                width: "80%",
                                                textAlign: "center",
                                            }}
                                        >
                                            StatTrak available
                                        </Typography>
                                    )}
                                    <img
                                        src={skinData.image_url}
                                        alt={skinData.skin}
                                        style={{
                                            width: "100%",
                                            marginTop: "16px",
                                            objectFit: "cover",
                                        }}
                                    />
                                </>
                            )}
                        </Box>
                    </Grid>
                    <Grid item xs={12} md={8}>
                        <Box
                            sx={{
                                width: "100%",
                                height: "auto",
                                borderRadius: "5px",
                                display: "flex",
                                flexDirection: "row",
                                justifyContent: "space-between",
                            }}
                        >
                            <Box sx={{ width: "48%" }}>
                                <Typography variant="subtitle1" gutterBottom>
                                    Normal Prices
                                </Typography>
                                <PriceList
                                    exteriorOrder={exteriorOrder}
                                    type="normal"
                                    label={null}
                                    color="#ffffff"
                                    onPriceClick={handlePriceClick}
                                    getLowestPriceAcrossMarketplaces={
                                        getLowestPriceAcrossMarketplaces
                                    }
                                    selectedExterior={selectedExterior}
                                    selectedType={priceType}
                                />
                            </Box>

                            {(skinData.stattrak === 1 ||
                                skinData.souvenir === 1) && (
                                <Box sx={{ width: "48%" }}>
                                    <Typography
                                        variant="subtitle1"
                                        gutterBottom
                                    >
                                        {skinData.stattrak === 1
                                            ? "StatTrak™ Prices"
                                            : "Souvenir Prices"}
                                    </Typography>
                                    <PriceList
                                        exteriorOrder={exteriorOrder}
                                        type={
                                            skinData.stattrak === 1
                                                ? "stattrak"
                                                : "souvenir"
                                        }
                                        label={
                                            skinData.stattrak === 1
                                                ? "StatTrak™"
                                                : "Souvenir"
                                        }
                                        color={
                                            skinData.stattrak === 1
                                                ? "#F89406"
                                                : "#ffd900"
                                        }
                                        onPriceClick={handlePriceClick}
                                        getLowestPriceAcrossMarketplaces={
                                            getLowestPriceAcrossMarketplaces
                                        }
                                        selectedExterior={selectedExterior}
                                        selectedType={priceType}
                                    />
                                </Box>
                            )}
                        </Box>
                    </Grid>
                    <Grid item xs={12} md={4}>
                        <Box
                            sx={{
                                backgroundColor: theme.palette.background.paper,
                                width: "100%",
                                height: "auto",
                                borderRadius: "5px",
                            }}
                        >
                            <Typography
                                variant="body2"
                                sx={{
                                    paddingTop: 2,
                                    marginLeft: 1,
                                    paddingBottom: 2,
                                }}
                                dangerouslySetInnerHTML={{
                                    __html:
                                        skinData?.description?.replace(
                                            /\\n\\n/g,
                                            "<br/> <br/>",
                                        ) ?? "",
                                }}
                            />
                        </Box>
                    </Grid>
                    <Grid item xs={12} md={8}>
                        <Box
                            sx={{
                                borderRadius: "5px",
                                width: "100%",
                                height: "auto",
                                padding: "0px",
                            }}
                        >
                            {selectedExterior && priceType && (
                                <PriceDetails
                                    selectedExterior={selectedExterior}
                                    priceType={priceType}
                                    prices={skinData.prices}
                                    weaponName={
                                        weaponName ?? "DefaultWeaponName"
                                    }
                                    skinName={skinName ?? "DefaultSkinName"}
                                    weaponCategory={skinData.category}
                                />
                            )}
                        </Box>
                    </Grid>
                </Grid>
            </Container>
        </>
    );
};

export default SkinLayout;
