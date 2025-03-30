import * as React from "react";
import { Box, Typography, Link, Popover } from "@mui/material";
import {
    usePopupState,
    bindHover,
    bindMenu,
} from "material-ui-popup-state/hooks";
import HoverMenu from "material-ui-popup-state/HoverMenu";
import { CombinedCategory } from "../types"; // Import types

const CategoryDropdown: React.FC<{
    category: CombinedCategory;
}> = ({ category }) => {
    const popupState = usePopupState({
        variant: "popover",
        popupId: `category-${category.id}`,
    });

    return (
        <Box>
            <Typography
                variant="body1"
                sx={{
                    color: "white", // Default text color
                    ml: 3, // Add margin to the left as before
                    cursor: "pointer", // Make it clickable
                    "&:hover": {
                        color: "text.secondary", // Hover color from theme
                    },
                }}
                {...bindHover(popupState)} // Bind hover state to the typography
            >
                {category.name}
            </Typography>

            <HoverMenu
                {...bindMenu(popupState)} // Bind menu state to the hover menu
                anchorOrigin={{ vertical: "bottom", horizontal: "left" }}
                transformOrigin={{ vertical: "top", horizontal: "left" }}
                sx={{ pointerEvents: "none" }} // Prevents menu from blocking hover events
            >
                {category.subcategory.map((subCategory) => (
                    <Box key={subCategory.label}>
                        <Typography
                            variant="subtitle1"
                            sx={{ px: 1, py: 1, fontWeight: "bold" }}
                        >
                            {subCategory.label}
                        </Typography>
                        {subCategory.items.map((item) => (
                            <Box key={item.id} sx={{ px: 2, py: 0.5 }}>
                                <Link
                                    href={`/weapon/${item.name}`}
                                    sx={{
                                        textDecoration: "none", // Remove underline
                                        color: "inherit", // Inherit color from parent (no blue)
                                        "&:hover": {
                                            color: "text.secondary", // Hover color on links
                                        },
                                    }}
                                >
                                    {item.name}
                                </Link>
                            </Box>
                        ))}
                    </Box>
                ))}
            </HoverMenu>
        </Box>
    );
};

export default CategoryDropdown;
