import { usePage } from "@inertiajs/react";
import React from "react";
import { Head } from "@inertiajs/inertia-react"; // Import Head from Inertia
import { PopularItem } from "../types";
import {
    Card,
    CardActionArea,
    CardContent,
    CardMedia,
    Container,
    Grid,
    Typography,
    useTheme,
} from "@mui/material";
import ItemSkinSearch from "../components/ItemSkinSearch";

interface HomeProps extends Record<string, any> {
    popularItems: PopularItem[];
    errors: Record<string, string[] | undefined>;
}

const Home: React.FC = () => {
    const { props } = usePage<HomeProps>(); // Now correctly typed
    const { popularItems } = props; // Destructure only what we need

    const theme = useTheme();

    return (
        <>
            <Typography variant="h2" gutterBottom sx={{ marginBottom: "20px" }}>
                AboutCSGO
            </Typography>
            <ItemSkinSearch />

            <div>
                <Typography variant="h4" gutterBottom>
                    Popular Items
                </Typography>
                <Grid container spacing={2} justifyContent="center">
                    {popularItems.slice(0, 20).map((item) => (
                        <Grid
                            item
                            xs={6} // 2 items per row on extra-small screens
                            sm={4} // 3 items per row on small screens
                            md={3} // 4 items per row on medium screens
                            lg={1.2} // Try to make 10 items per row
                            key={item.id}
                        >
                            <Card
                                sx={{
                                    border: `2px solid #000000`, // Uniform border color
                                    borderColor: "#ffffff",
                                    transition: "border-color 0.3s",
                                    "&:hover": {
                                        borderColor:
                                            theme.palette.primary.light, // Border turns white on hover
                                    },
                                }}
                            >
                                <CardActionArea
                                    onClick={() => {
                                        // Navigate to the item page
                                        window.location.href = `/skin/${item.item_name}/${item.skin_name}`;
                                    }}
                                >
                                    <CardMedia
                                        component="img"
                                        width="auto"
                                        image={item.image_url}
                                        alt={`${item.item_name} ${item.skin_name}`}
                                    />
                                    <CardContent sx={{ padding: "0" }}>
                                        <Typography
                                            variant="body2"
                                            component="div"
                                            sx={{
                                                overflow: "hidden",
                                                textOverflow: "ellipsis",
                                                whiteSpace: "nowrap",
                                                fontSize: "0.9rem", // Correct the font size
                                            }}
                                        >
                                            {item.item_name}
                                        </Typography>

                                        <Typography
                                            variant="body2"
                                            component="div"
                                            sx={{
                                                overflow: "hidden",
                                                textOverflow: "ellipsis",
                                                whiteSpace: "nowrap",
                                                fontSize: "0.9rem", // Correct the font size
                                            }}
                                        >
                                            {item.skin_name}
                                        </Typography>

                                        <Typography
                                            variant="body2"
                                            sx={{
                                                color: "#ffffff", // White text color
                                                backgroundColor:
                                                    item.quality_color, // Background color based on quality
                                                margin: "4px",
                                                padding: "2px 4px",
                                                borderRadius: "8px", // Rounded corners for quality label
                                                display: "flex", // Use flexbox for centering
                                                justifyContent: "center", // Center text horizontally
                                                alignItems: "center", // Center text vertically
                                                marginTop: "4px", // Small margin for spacing
                                                fontSize: "0.7rem", // Smaller font size for quality label
                                                height: "2em", // Fixed height to accommodate two lines
                                                lineHeight: "1em", // Ensure line height matches text size
                                                overflow: "hidden", // Prevent overflow of text
                                                textAlign: "center", // Center text alignment
                                                whiteSpace: "normal", // Allow text to wrap
                                            }}
                                        >
                                            {item.quality}
                                        </Typography>
                                    </CardContent>
                                </CardActionArea>
                            </Card>
                        </Grid>
                    ))}
                </Grid>
            </div>

            {/* Integrate the NewsList component */}
            {/* <div style={{ marginTop: "40px" }}>
              <NewsList news={news} />
            </div> */}
        </>
    );
};

export default Home;
