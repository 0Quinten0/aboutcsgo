import React from "react";
import { Box, Typography, Button, useTheme } from "@mui/material";
import { MarketplacePricesWithDetails, PriceCategory } from "../types";

interface PriceDetailsProps {
    selectedExterior: string;
    priceType: keyof MarketplacePricesWithDetails;
    prices: Record<string, MarketplacePricesWithDetails>;
    skinName: string;
    weaponName: string;
    weaponCategory: string;
}

type ItemType = "souvenir" | "stattrak" | "default";

const PriceDetails: React.FC<PriceDetailsProps> = ({
    selectedExterior,
    priceType,
    prices,
    skinName,
    weaponName,
    weaponCategory,
}) => {
    const theme = useTheme();

    const exteriorOrder = [
        "Factory New", // 0
        "Minimal Wear", // 1
        "Field-Tested", // 2
        "Well-Worn", // 3
        "Battle-Scarred", // 4
    ];

    // Helper function to determine type, category, and prefix
    const determineTypeDetails = (type: string, category: string) => {
        let prefix = "";

        // Check if the category is Knives or Gloves and set the prefix
        const isSpecialCategory =
            category === "Knives" || category === "Gloves";
        if (isSpecialCategory) {
            prefix = "★ "; // Add star prefix for Knives and Gloves
        }

        // Determine the prefix based on type
        switch (type) {
            case "stattrak":
                prefix += "StatTrak™ "; // Append StatTrak™ for StatTrak™ items
                break;
            case "souvenir":
                prefix += "Souvenir "; // Append Souvenir for Souvenir items
                break;
            default:
                break; // No additional prefix for normal items
        }

        return { prefix };
    };

    const urlGenerators: Record<string, (params: any) => string> = {
        gamerpay: ({ item, skin, exterior, prefix }) => {
            const queryString = `${prefix}${item} | ${skin} (${exterior})`;
            return `https://gamerpay.gg/?query=${encodeURIComponent(
                queryString,
            )}&sortBy=price&ascending=true&ref=aboutcsgo`;
        },
        csfloat: ({
            item,
            skin,
            exterior,
            category,
            prefix,
            type,
        }: {
            item: string;
            skin: string;
            exterior: string;
            category: string;
            prefix: string;
            type: ItemType;
        }) => {
            // Define a mapping from type to category number
            const typeToCategoryNumber: Record<ItemType, number> = {
                souvenir: 3,
                stattrak: 2,
                default: 1,
            };

            // Determine the category number based on the type
            const categoryNumber =
                typeToCategoryNumber[type] || typeToCategoryNumber["default"];

            // Construct the query string
            const queryString = `${prefix}${item} | ${skin} (${exterior})`;

            // Return the URL with the numeric category
            return `https://csfloat.com/search?category=${categoryNumber}&sort_by=lowest_price&type=buy_now&market_hash_name=${encodeURIComponent(
                queryString,
            )}`;
        },

        skinbaron: ({ item, skin, exterior, type, category }) => {
            let queryParams = `?sort=CF`;

            // Add type-specific parameters
            if (type === "stattrak") {
                queryParams += `&statTrak=true`;
            } else if (type === "souvenir") {
                queryParams += `&souvenir=true`;
            }

            // Replace spaces with hyphens for SkinBaron
            const formattedItem = item.replace(/\s+/g, "-");
            const formattedSkin = skin.replace(/\s+/g, "-");
            const formattedExterior = exterior.replace(/\s+/g, "-");

            // Handle the special case for "Knives" category
            const formattedCategory =
                category === "Knives" ? "Knife" : category;

            // Construct the URL based on the provided parameters
            return `https://skinbaron.de/en/csgo/${encodeURIComponent(
                formattedCategory,
            )}/${encodeURIComponent(formattedItem)}/${encodeURIComponent(
                formattedSkin,
            )}/${encodeURIComponent(formattedExterior)}${queryParams}`;
        },

        shadowpay: ({ item, skin, exterior, type }) => {
            // Base URL
            const baseUrl = `https://shadowpay.com/csgo-items`;

            // Build the search query with proper encoding
            const prefix =
                type === "stattrak"
                    ? "StatTrak%E2%84%A2%20"
                    : type === "souvenir"
                      ? "Souvenir%20"
                      : "★%20";
            const queryString = `${prefix}${item.replace(
                /\s+/g,
                "%20",
            )}%20%7C%20${skin.replace(/\s+/g, "%20")}`;

            // Encode exterior and wrap it in a JSON-like array
            const encodedExterior = `[${encodeURIComponent(`"${exterior}"`)}]`;

            // Determine if it's StatTrak
            const isStattrak = type === "stattrak" ? 1 : 0;

            // Construct the full URL with parameters
            return `${baseUrl}?search=${queryString}&is_stattrak=${isStattrak}&sort_column=price&sort_dir=asc&utm_campaign=ptPfUYVmLE4KjaH&exteriors=${encodedExterior}`;
        },

        skinwallet: ({ item, skin, exterior, type, category }) => {
            // Base URL
            const baseUrl = `https://www.skinwallet.com/market/offers?appId=730`;

            // Build the search query with proper encoding
            const prefix =
                category === "Knives" || category === "Gloves" ? "★ " : "";
            const queryString = `${prefix}${item.replace(
                /\s+/g,
                "%20",
            )}%20%7C%20${skin.replace(/\s+/g, "%20")}`;

            // Determine the exterior code based on the exteriorOrder array
            const exteriorCode = exteriorOrder.indexOf(exterior);

            // Construct the full URL with parameters, including exterior and sorting
            return `${baseUrl}&search=${queryString}&sortBy=HotDeals${
                exteriorCode !== -1
                    ? `&exterior%5B%5D=WearCategory${exteriorCode}`
                    : ""
            }`;
        },

        waxpeer: ({ item, skin, exterior, type, category }) => {
            // Base URL
            const baseUrl = `https://waxpeer.com/?sort=ASC&order=price&all=0&skip=0&game=csgo`;

            // Build the search query with proper encoding
            const prefix =
                category === "Knives" || category === "Gloves" ? "★ " : "";
            const typePrefix =
                type === "stattrak"
                    ? "StatTrak™ "
                    : type === "souvenir"
                      ? "Souvenir "
                      : "";

            const queryString = `${prefix}${typePrefix}${item.replace(
                /\s+/g,
                "%20",
            )}%20%7C%20${skin.replace(/\s+/g, "%20")}%20(${exterior.replace(
                /\s+/g,
                "%20",
            )})`;

            // Construct the full URL with the search query and exact match
            return `${baseUrl}&search=${encodeURIComponent(
                queryString,
            )}&exact=1&ref=aboutcsgo`;
        },

        market_csgo: ({ item, skin, exterior, type, category }) => {
            // Base URL
            const baseUrl = `https://market.csgo.com/en/?order=asc&sort=price`;

            // Build the search query with proper encoding (no double encoding)
            const prefix =
                category === "Knives" || category === "Gloves" ? "★ " : "";
            const typePrefix =
                type === "stattrak"
                    ? "StatTrak™ "
                    : type === "souvenir"
                      ? "Souvenir "
                      : "";

            const queryString = `${prefix}${typePrefix}${item} | ${skin} (${exterior})`;

            // Construct the full URL with the search query, avoiding double encoding
            return `${baseUrl}&search=${encodeURIComponent(queryString)}`;
        },

        skinport: ({ item, skin, exterior, type, category }) => {
            // Base URL
            const baseUrl = `https://skinport.com/item`;

            // Determine if the item is StatTrak™ and include it in the URL path
            const typePrefix =
                type === "stattrak"
                    ? "stattrak-"
                    : type === "souvenir"
                      ? "souvenir-"
                      : "";
            // Replace spaces with hyphens for Skinport
            const formattedItem = item.replace(/\s+/g, "-").toLowerCase();
            const formattedSkin = skin.replace(/\s+/g, "-").toLowerCase();
            const formattedExterior = exterior
                .replace(/\s+/g, "-")
                .toLowerCase();

            // Construct the URL path
            const urlPath = `${typePrefix}${formattedItem}-${formattedSkin}-${formattedExterior}`;

            // Construct the full URL with the reference code
            return `${baseUrl}/${urlPath}?r=aboutcsgo`;
        },

        steam: ({ item, skin, exterior, type, category }) => {
            // Base URL for Steam Community Market
            const baseUrl = `https://steamcommunity.com/market/listings/730/`;

            // Determine type details and get the prefix
            const { prefix } = determineTypeDetails(type, category);

            // Special handling for Vanilla knives
            if (category === "Knives" && skin === "Vanilla") {
                // Format for Vanilla knives: No skin name or exterior
                const formattedItem = item
                    .replace(/\s+/g, "%20")
                    .replace("|", "%7C")
                    .replace(/\(/g, "%28")
                    .replace(/\)/g, "%29");
                return `${baseUrl}${prefix}${formattedItem}`;
            }

            // Default formatting
            const formattedItem = item
                .replace(/\s+/g, "%20")
                .replace("|", "%7C")
                .replace(/\(/g, "%28")
                .replace(/\)/g, "%29");
            const formattedSkin = skin
                .replace(/\s+/g, "%20")
                .replace("|", "%7C")
                .replace(/\(/g, "%28")
                .replace(/\)/g, "%29");
            const formattedExterior = exterior
                .replace(/\s+/g, "%20")
                .replace("|", "%7C")
                .replace(/\(/g, "%28")
                .replace(/\)/g, "%29");

            // Construct the URL path
            const urlPath = `${prefix}${formattedItem}%20%7C%20${formattedSkin}%20%28${formattedExterior}%29`;

            // Construct the full URL
            return `${baseUrl}${urlPath}`;
        },

        bitskins: ({ item, skin, exterior, type, category }) => {
            // Determine type details and get the prefix
            const { prefix } = determineTypeDetails(type, category);

            // Special handling for Vanilla knives (no skin name or exterior)
            const searchName =
                category === "Knives" && skin === "Vanilla"
                    ? `${prefix}${item}`
                    : `${prefix}${item} | ${skin} (${exterior})`;

            // Properly encode the search query
            const encodedSearchName = encodeURIComponent(searchName);

            // Construct the full URL
            return `https://bitskins.com/market/cs2?search={"order":[{"field":"price","order":"ASC"}],"where":{"skin_name":"${encodedSearchName}"}}&ref_alias=aboutcsgo`;
        },

        // Add more marketplaces here...
    };

    // Prepare an array of prices with marketplace names and sort them by price
    const sortedPrices = Object.entries(prices)
        .map(([marketplace, priceData]) => {
            const priceCategory = priceData[priceType] as
                | PriceCategory
                | undefined;
            const price = priceCategory?.[selectedExterior] ?? "N/A";

            return {
                marketplace,
                price:
                    price === "N/A"
                        ? Infinity
                        : parseFloat(price.replace("€", "").replace(",", ".")), // Convert to a number for sorting
            };
        })
        .sort((a, b) => a.price - b.price); // Sort by price

    const handleBuyClick = (marketplace: string) => {
        const generator = urlGenerators[marketplace];
        if (generator) {
            // Determine type details (category, prefix) based on priceType and weaponCategory
            const { prefix } = determineTypeDetails(priceType, weaponCategory);

            // Build the params object with additional category and prefix
            const params = {
                skin: skinName, // Use the actual skin name
                item: weaponName, // Use the actual item (weapon) name
                type: priceType, // Determine the type (e.g., Normal, StatTrak™, Souvenir, etc.)
                exterior: selectedExterior, // Use the selected exterior
                category: weaponCategory, // Use the provided weapon category
                prefix, // Add the determined prefix
            };

            const url = generator(params);
            window.open(url, "_blank"); // Open the marketplace link in a new tab
        }
    };

    return (
        <Box sx={{ marginTop: 0 }}>
            <Typography variant="h6" sx={{ marginBottom: 1 }}>
                Offers
            </Typography>
            {sortedPrices.map(({ marketplace, price }) => {
                // Format the price back to a string with currency symbol
                const formattedPrice = isFinite(price)
                    ? `€${price.toFixed(2)}`
                    : "N/A";

                const logoUrl = prices[marketplace]?.image_url ?? "";
                const prettyMarketplaceName =
                    prices[marketplace]?.pretty_name ?? "";

                return (
                    <Box
                        key={marketplace}
                        sx={{
                            display: "flex",
                            alignItems: "center",
                            padding: "8px 16px",
                            borderRadius: "8px",
                            marginBottom: 2,
                            backgroundColor: theme.palette.background.paper,
                            border: `1px solid ${theme.palette.divider}`,
                        }}
                    >
                        {/* Marketplace Logo and Name */}
                        <Box
                            sx={{
                                display: "flex",
                                alignItems: "center",
                                minWidth: 150, // Fixed width for consistent alignment
                                flexShrink: 0,
                                marginRight: 2, // Space between name/logo and price
                            }}
                        >
                            <img
                                src={logoUrl}
                                alt={`${marketplace} logo`}
                                style={{
                                    width: 40,
                                    height: 40,
                                    marginRight: 16,
                                }}
                                onError={(e) =>
                                    (e.currentTarget.style.display = "none")
                                } // Hide image if it fails to load
                            />
                            <Typography variant="body1">
                                {prettyMarketplaceName}
                            </Typography>
                        </Box>

                        {/* Price */}
                        <Box
                            sx={{
                                flexGrow: 1, // Allow price to use remaining space
                                display: "flex",
                                justifyContent: "flex-end", // Align price to the end
                            }}
                        >
                            <Typography
                                variant="body1"
                                sx={{
                                    textAlign: "left", // Ensure text alignment within its container
                                    marginRight: 10, // Space between price and button
                                }}
                            >
                                {formattedPrice}
                            </Typography>
                        </Box>

                        {/* Button */}
                        <Button
                            variant="contained"
                            size="small"
                            sx={{
                                backgroundColor: theme.palette.primary.light,
                                marginLeft: 2, // Space between price and button
                            }}
                            onClick={() => handleBuyClick(marketplace)}
                        >
                            Buy
                        </Button>
                    </Box>
                );
            })}
        </Box>
    );
};

export default PriceDetails;
