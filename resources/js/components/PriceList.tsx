import React from "react";
import { Button, Typography, Box, useTheme } from "@mui/material";
import { MarketplacePricesWithDetails } from "../types";

interface PriceListProps {
    exteriorOrder: string[];
    type: keyof MarketplacePricesWithDetails;
    label: string | null;
    color: string;
    onPriceClick: (
        exterior: string,
        type: keyof MarketplacePricesWithDetails,
    ) => void;
    getLowestPriceAcrossMarketplaces: (
        condition: string,
        type: keyof MarketplacePricesWithDetails,
    ) => string | null;
    selectedExterior: string;
    selectedType: keyof MarketplacePricesWithDetails;
}

const PriceList: React.FC<PriceListProps> = ({
    exteriorOrder,
    type,
    label,
    color,
    onPriceClick,
    getLowestPriceAcrossMarketplaces,
    selectedExterior,
    selectedType,
}) => {
    const theme = useTheme();

    return (
        <>
            {exteriorOrder.map((condition) => {
                const displayCondition =
                    condition === "No Exterior" ? "Vanilla" : condition;

                const lowestPrice = getLowestPriceAcrossMarketplaces(
                    condition,
                    type,
                );
                const isSelected =
                    selectedExterior === condition && selectedType === type;

                return (
                    <Box key={condition} sx={{ marginBottom: 1 }}>
                        <Button
                            fullWidth
                            variant="contained"
                            sx={{
                                justifyContent: "flex-start",
                                textTransform: "none",
                                backgroundColor: isSelected
                                    ? theme.palette.primary.dark
                                    : lowestPrice
                                      ? undefined
                                      : theme.palette.background.paper, // Light gray for disabled state
                                color: isSelected
                                    ? theme.palette.primary.contrastText
                                    : lowestPrice
                                      ? undefined
                                      : "#a0a0a0", // Slightly darker white for text
                                "&:hover": {
                                    backgroundColor: isSelected
                                        ? theme.palette.primary.dark
                                        : lowestPrice
                                          ? theme.palette.primary.dark // Change hover color for unselected
                                          : theme.palette.background.paper, // Darker gray on hover for disabled state
                                },
                                "&:disabled": {
                                    backgroundColor:
                                        theme.palette.background.paper, // Ensure the background is light gray when disabled
                                    color: "#a0a0a0", // Ensure text is slightly darker white
                                },
                            }}
                            onClick={() => onPriceClick(condition, type)}
                            disabled={!lowestPrice} // Disable the button if there's no price
                        >
                            {label && ( // Only render the label if it's not null
                                <Typography variant="body1" style={{ color }}>
                                    {label}
                                </Typography>
                            )}
                            <Typography
                                variant="body1"
                                style={{ marginRight: "auto" }}
                            >
                                &nbsp;{displayCondition}
                            </Typography>
                            <Typography variant="body1">
                                {lowestPrice ? `€${lowestPrice}` : "No offers"}
                            </Typography>
                        </Button>
                    </Box>
                );
            })}
        </>
    );
};

export default PriceList;
