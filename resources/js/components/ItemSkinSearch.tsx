import React, { useState, useEffect } from "react";
import {
    TextField,
    CircularProgress,
    List,
    ListItem,
    ListItemText,
    ListItemIcon,
    Container,
    useTheme,
} from "@mui/material";
import axiosClient from "../axiosClient"; // Adjust path as needed

interface ItemSkin {
    id: number;
    combined_name: string;
    image_url: string;
    item_name: string;
    skin_name: string;
}

const ItemSkinSearch: React.FC = () => {
    const [query, setQuery] = useState<string>("");
    const [results, setResults] = useState<ItemSkin[]>([]);
    const [loading, setLoading] = useState<boolean>(false);

    const theme = useTheme();

    // Function to fetch item skins from the API
    const fetchItemSkins = async (searchQuery: string) => {
        if (!searchQuery) {
            setResults([]);
            return;
        }

        setLoading(true);
        try {
            const response = await axiosClient.get<ItemSkin[]>(
                "/item-skin/search",
                {
                    params: { query: searchQuery },
                },
            );
            setResults(response.data);
        } catch (error) {
            console.error("Error fetching data:", error);
        } finally {
            setLoading(false);
        }
    };

    // UseEffect to trigger search when query changes
    useEffect(() => {
        if (query) {
            fetchItemSkins(query);
        } else {
            setResults([]); // Clear results if query is empty
        }
    }, [query]);

    return (
        <Container maxWidth="md" sx={{ padding: "20px" }}>
            <TextField
                type="text"
                variant="outlined"
                fullWidth
                placeholder="Search item skins..."
                value={query}
                onChange={(e) => setQuery(e.target.value)}
                sx={{
                    "& .MuiOutlinedInput-root": {
                        "& fieldset": { borderColor: "#ccc" },
                        "&:hover fieldset": { borderColor: "#FFFFFF" },
                        "&.Mui-focused fieldset": { borderColor: "#FFFFFF" },
                    },
                }}
            />
            {loading && (
                <CircularProgress
                    sx={{ display: "block", margin: "10px auto" }}
                />
            )}
            <List>
                {results.map((item) => (
                    <ListItem
                        key={item.id}
                        component="div"
                        sx={{
                            border: "1px solid #ccc",
                            marginBottom: "10px",
                            borderRadius: "4px",
                            cursor: "pointer",
                            "&:hover": {
                                backgroundColor: theme.palette.action.hover,
                            },
                        }}
                        onClick={() => {
                            window.location.href = `/skin/${item.item_name}/${item.skin_name}`;
                        }}
                    >
                        <ListItemIcon>
                            <img
                                src={item.image_url}
                                alt={item.combined_name}
                                style={{ width: "50px", height: "auto" }}
                            />
                        </ListItemIcon>
                        <ListItemText primary={item.combined_name} />
                    </ListItem>
                ))}
            </List>
        </Container>
    );
};

export default ItemSkinSearch;
