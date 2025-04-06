import React, { useState, useEffect, useRef } from "react";
import ReactDOM from "react-dom";
import {
    TextField,
    CircularProgress,
    List,
    ListItem,
    ListItemText,
    ListItemIcon,
    useTheme,
    Box,
    ClickAwayListener,
} from "@mui/material";
import axiosClient from "../axiosClient";

interface ItemSkinSearchProps {
    compact?: boolean;
}

interface ItemSkin {
    id: number;
    combined_name: string;
    image_url: string;
    item_name: string;
    skin_name: string;
}

const ItemSkinSearch: React.FC<ItemSkinSearchProps> = ({ compact = false }) => {
    const [query, setQuery] = useState("");
    const [results, setResults] = useState<ItemSkin[]>([]);
    const [loading, setLoading] = useState(false);
    const [showDropdown, setShowDropdown] = useState(false);

    const [dropdownTop, setDropdownTop] = useState(0);
    const [dropdownLeft, setDropdownLeft] = useState(0);
    const [dropdownWidth, setDropdownWidth] = useState(0);

    const inputRef = useRef<HTMLInputElement>(null);
    const theme = useTheme();

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

    useEffect(() => {
        if (query && inputRef.current) {
            const rect = inputRef.current.getBoundingClientRect();
            setDropdownTop(rect.bottom + window.scrollY);
            setDropdownLeft(rect.left + window.scrollX);
            setDropdownWidth(rect.width);
            fetchItemSkins(query);
            setShowDropdown(true);
        } else {
            setResults([]);
            setShowDropdown(false);
        }
    }, [query]);

    const handleClickAway = (event: MouseEvent | TouchEvent) => {
        // Prevent closing if clicking inside the input field
        if (
            inputRef.current &&
            inputRef.current.contains(event.target as Node)
        ) {
            return;
        }
        setShowDropdown(false);
    };

    return (
        <>
            <Box
                sx={{
                    width: compact ? "250px" : "100%",
                    mx: "auto",
                }}
            >
                <TextField
                    type="text"
                    variant="outlined"
                    fullWidth
                    inputRef={inputRef}
                    placeholder="Search item skins..."
                    value={query}
                    onChange={(e) => setQuery(e.target.value)}
                    size={compact ? "small" : "medium"}
                    onFocus={() => {
                        if (results.length > 0) setShowDropdown(true);
                    }}
                    sx={{
                        "& .MuiOutlinedInput-root": {
                            "& fieldset": { borderColor: "#ccc" },
                            "&:hover fieldset": { borderColor: "#FFFFFF" },
                            "&.Mui-focused fieldset": {
                                borderColor: "#FFFFFF",
                            },
                        },
                    }}
                />
            </Box>

            {showDropdown &&
                ReactDOM.createPortal(
                    <ClickAwayListener onClickAway={handleClickAway}>
                        <Box
                            sx={{
                                position: "absolute",
                                top: dropdownTop,
                                left: dropdownLeft,
                                width: dropdownWidth,
                                backgroundColor: theme.palette.background.paper,
                                zIndex: 1300,
                                borderRadius: "4px",
                                boxShadow: "0px 4px 12px rgba(0,0,0,0.2)",
                                maxHeight: "500px",
                                overflowY: "auto",
                            }}
                        >
                            {loading ? (
                                <Box
                                    sx={{
                                        display: "flex",
                                        justifyContent: "center",
                                        p: 2,
                                    }}
                                >
                                    <CircularProgress size={24} />
                                </Box>
                            ) : (
                                <List>
                                    {results.map((item) => (
                                        <ListItem
                                            key={item.id}
                                            component="div"
                                            sx={{
                                                borderBottom: "1px solid #eee",
                                                cursor: "pointer",
                                                "&:hover": {
                                                    backgroundColor:
                                                        theme.palette.action
                                                            .hover,
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
                                                    style={{
                                                        width: "40px",
                                                        height: "auto",
                                                    }}
                                                />
                                            </ListItemIcon>
                                            <ListItemText
                                                primary={item.combined_name}
                                            />
                                        </ListItem>
                                    ))}
                                </List>
                            )}
                        </Box>
                    </ClickAwayListener>,
                    document.body,
                )}
        </>
    );
};

export default ItemSkinSearch;
