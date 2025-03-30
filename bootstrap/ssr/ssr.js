import { usePage, Link as Link$1, createInertiaApp } from "@inertiajs/react";
import * as React from "react";
import React__default, { useState, useEffect } from "react";
import { useTheme, Container, TextField, CircularProgress, List, ListItem, ListItemIcon, ListItemText, Typography, Grid, Card, CardActionArea, CardMedia, CardContent, createTheme, responsiveFontSizes, Box, Link, AppBar, Toolbar, ThemeProvider, CssBaseline } from "@mui/material";
import axios from "axios";
import ReactDOMServer from "react-dom/server";
import createServer from "@inertiajs/react/server";
import { usePopupState, bindHover, bindMenu } from "material-ui-popup-state/hooks";
import HoverMenu from "material-ui-popup-state/HoverMenu";
const axiosClient = axios.create({
  baseURL: "https://api.aboutcsgo.com/"
  // baseURL: "http://127.0.0.1:8000/",
});
const ItemSkinSearch = () => {
  const [query, setQuery] = useState("");
  const [results, setResults] = useState([]);
  const [loading, setLoading] = useState(false);
  const theme = useTheme();
  const fetchItemSkins = async (searchQuery) => {
    if (!searchQuery) {
      setResults([]);
      return;
    }
    setLoading(true);
    try {
      const response = await axiosClient.get(
        "/item-skin/search",
        {
          params: { query: searchQuery }
        }
      );
      setResults(response.data);
    } catch (error) {
      console.error("Error fetching data:", error);
    } finally {
      setLoading(false);
    }
  };
  useEffect(() => {
    if (query) {
      fetchItemSkins(query);
    } else {
      setResults([]);
    }
  }, [query]);
  return /* @__PURE__ */ React__default.createElement(Container, { maxWidth: "md", sx: { padding: "20px" } }, /* @__PURE__ */ React__default.createElement(
    TextField,
    {
      type: "text",
      variant: "outlined",
      fullWidth: true,
      placeholder: "Search item skins...",
      value: query,
      onChange: (e) => setQuery(e.target.value),
      sx: {
        "& .MuiOutlinedInput-root": {
          "& fieldset": { borderColor: "#ccc" },
          "&:hover fieldset": { borderColor: "#FFFFFF" },
          "&.Mui-focused fieldset": { borderColor: "#FFFFFF" }
        }
      }
    }
  ), loading && /* @__PURE__ */ React__default.createElement(
    CircularProgress,
    {
      sx: { display: "block", margin: "10px auto" }
    }
  ), /* @__PURE__ */ React__default.createElement(List, null, results.map((item) => /* @__PURE__ */ React__default.createElement(
    ListItem,
    {
      key: item.id,
      component: "div",
      sx: {
        border: "1px solid #ccc",
        marginBottom: "10px",
        borderRadius: "4px",
        cursor: "pointer",
        "&:hover": {
          backgroundColor: theme.palette.action.hover
        }
      },
      onClick: () => {
        window.location.href = `/skin/${item.item_name}/${item.skin_name}`;
      }
    },
    /* @__PURE__ */ React__default.createElement(ListItemIcon, null, /* @__PURE__ */ React__default.createElement(
      "img",
      {
        src: item.image_url,
        alt: item.combined_name,
        style: { width: "50px", height: "auto" }
      }
    )),
    /* @__PURE__ */ React__default.createElement(ListItemText, { primary: item.combined_name })
  ))));
};
const Home = () => {
  const { props } = usePage();
  const { popularItems } = props;
  const theme = useTheme();
  return /* @__PURE__ */ React__default.createElement(React__default.Fragment, null, /* @__PURE__ */ React__default.createElement(Typography, { variant: "h2", gutterBottom: true, sx: { marginBottom: "20px" } }, "AboutCSGO"), /* @__PURE__ */ React__default.createElement(ItemSkinSearch, null), /* @__PURE__ */ React__default.createElement("div", null, /* @__PURE__ */ React__default.createElement(Typography, { variant: "h4", gutterBottom: true }, "Popular Items"), /* @__PURE__ */ React__default.createElement(Grid, { container: true, spacing: 2, justifyContent: "center" }, popularItems.slice(0, 20).map((item) => /* @__PURE__ */ React__default.createElement(
    Grid,
    {
      item: true,
      xs: 6,
      sm: 4,
      md: 3,
      lg: 1.2,
      key: item.id
    },
    /* @__PURE__ */ React__default.createElement(
      Card,
      {
        sx: {
          border: `2px solid #000000`,
          // Uniform border color
          borderColor: "#ffffff",
          transition: "border-color 0.3s",
          "&:hover": {
            borderColor: theme.palette.primary.light
            // Border turns white on hover
          }
        }
      },
      /* @__PURE__ */ React__default.createElement(
        CardActionArea,
        {
          onClick: () => {
            window.location.href = `/skin/${item.item_name}/${item.skin_name}`;
          }
        },
        /* @__PURE__ */ React__default.createElement(
          CardMedia,
          {
            component: "img",
            width: "auto",
            image: item.image_url,
            alt: `${item.item_name} ${item.skin_name}`
          }
        ),
        /* @__PURE__ */ React__default.createElement(CardContent, { sx: { padding: "0" } }, /* @__PURE__ */ React__default.createElement(
          Typography,
          {
            variant: "body2",
            component: "div",
            sx: {
              overflow: "hidden",
              textOverflow: "ellipsis",
              whiteSpace: "nowrap",
              fontSize: "0.9rem"
              // Correct the font size
            }
          },
          item.item_name
        ), /* @__PURE__ */ React__default.createElement(
          Typography,
          {
            variant: "body2",
            component: "div",
            sx: {
              overflow: "hidden",
              textOverflow: "ellipsis",
              whiteSpace: "nowrap",
              fontSize: "0.9rem"
              // Correct the font size
            }
          },
          item.skin_name
        ), /* @__PURE__ */ React__default.createElement(
          Typography,
          {
            variant: "body2",
            sx: {
              color: "#ffffff",
              // White text color
              backgroundColor: item.quality_color,
              // Background color based on quality
              margin: "4px",
              padding: "2px 4px",
              borderRadius: "8px",
              // Rounded corners for quality label
              display: "flex",
              // Use flexbox for centering
              justifyContent: "center",
              // Center text horizontally
              alignItems: "center",
              // Center text vertically
              marginTop: "4px",
              // Small margin for spacing
              fontSize: "0.7rem",
              // Smaller font size for quality label
              height: "2em",
              // Fixed height to accommodate two lines
              lineHeight: "1em",
              // Ensure line height matches text size
              overflow: "hidden",
              // Prevent overflow of text
              textAlign: "center",
              // Center text alignment
              whiteSpace: "normal"
              // Allow text to wrap
            }
          },
          item.quality
        ))
      )
    )
  )))));
};
const __vite_glob_0_0 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: Home
}, Symbol.toStringTag, { value: "Module" }));
let darkTheme = createTheme({
  palette: {
    primary: {
      light: "#4169E1",
      main: "#2d3844",
      dark: "#87a3bf"
    },
    background: {
      default: "#1b1f23",
      paper: "#2d3844"
    },
    text: {
      primary: "#FFFFFF",
      secondary: "#ecf041"
    }
  },
  typography: {
    fontFamily: ["Poppins", "Oswald", "sans-serif"].join(",")
  }
});
darkTheme = responsiveFontSizes(darkTheme);
const darkTheme$1 = darkTheme;
const CategoryDropdown = ({ category }) => {
  const popupState = usePopupState({
    variant: "popover",
    popupId: `category-${category.id}`
  });
  return /* @__PURE__ */ React.createElement(Box, null, /* @__PURE__ */ React.createElement(
    Typography,
    {
      variant: "body1",
      sx: {
        color: "white",
        // Default text color
        ml: 3,
        // Add margin to the left as before
        cursor: "pointer",
        // Make it clickable
        "&:hover": {
          color: "text.secondary"
          // Hover color from theme
        }
      },
      ...bindHover(popupState)
    },
    category.name
  ), /* @__PURE__ */ React.createElement(
    HoverMenu,
    {
      ...bindMenu(popupState),
      anchorOrigin: { vertical: "bottom", horizontal: "left" },
      transformOrigin: { vertical: "top", horizontal: "left" },
      sx: { pointerEvents: "none" }
    },
    category.subcategory.map((subCategory) => /* @__PURE__ */ React.createElement(Box, { key: subCategory.label }, /* @__PURE__ */ React.createElement(
      Typography,
      {
        variant: "subtitle1",
        sx: { px: 1, py: 1, fontWeight: "bold" }
      },
      subCategory.label
    ), subCategory.items.map((item) => /* @__PURE__ */ React.createElement(Box, { key: item.id, sx: { px: 2, py: 0.5 } }, /* @__PURE__ */ React.createElement(
      Link,
      {
        href: `/weapon/${item.name}`,
        sx: {
          textDecoration: "none",
          // Remove underline
          color: "inherit",
          // Inherit color from parent (no blue)
          "&:hover": {
            color: "text.secondary"
            // Hover color on links
          }
        }
      },
      item.name
    )))))
  ));
};
const categories = [
  {
    id: "combined-rifles-snipers",
    name: "Rifles",
    subcategory: [
      {
        label: "Rifles",
        items: [
          { id: 9, name: "SG 553", category_id: 3 },
          { id: 10, name: "AUG", category_id: 3 },
          { id: 11, name: "FAMAS", category_id: 3 },
          { id: 12, name: "AK-47", category_id: 3 },
          { id: 13, name: "Galil AR", category_id: 3 },
          { id: 14, name: "M4A1-S", category_id: 3 },
          { id: 15, name: "M4A4", category_id: 3 }
        ]
      },
      {
        label: "Snipers",
        items: [
          { id: 16, name: "AWP", category_id: 4 },
          { id: 17, name: "SSG 08", category_id: 4 },
          { id: 18, name: "G3SG1", category_id: 4 },
          { id: 19, name: "SCAR-20", category_id: 4 }
        ]
      }
    ]
  },
  {
    id: "combined-mid-tier",
    name: "Mid-Tier",
    subcategory: [
      {
        label: "Machinegun",
        items: [
          { id: 30, name: "M249", category_id: 6 },
          { id: 31, name: "Negev", category_id: 6 }
        ]
      },
      {
        label: "Shotgun",
        items: [
          { id: 32, name: "Nova", category_id: 7 },
          { id: 33, name: "XM1014", category_id: 7 },
          { id: 34, name: "MAG-7", category_id: 7 },
          { id: 35, name: "Sawed-Off", category_id: 7 }
        ]
      },
      {
        label: "SMG",
        items: [
          { id: 36, name: "MAC-10", category_id: 8 },
          { id: 37, name: "MP9", category_id: 8 },
          { id: 38, name: "MP7", category_id: 8 },
          { id: 39, name: "MP5-SD", category_id: 8 },
          { id: 40, name: "UMP-45", category_id: 8 },
          { id: 41, name: "P90", category_id: 8 },
          { id: 42, name: "PP-Bizon", category_id: 8 }
        ]
      }
    ]
  },
  {
    id: "combined-pistol-zeus",
    name: "Pistols",
    subcategory: [
      {
        label: "Pistols",
        items: [
          { id: 20, name: "Glock-18", category_id: 5 },
          { id: 21, name: "P250", category_id: 5 },
          { id: 22, name: "Five-SeveN", category_id: 5 },
          { id: 23, name: "Tec-9", category_id: 5 },
          { id: 24, name: "CZ75-Auto", category_id: 5 },
          { id: 25, name: "Desert Eagle", category_id: 5 },
          { id: 26, name: "USP-S", category_id: 5 },
          { id: 27, name: "R8 Revolver", category_id: 5 },
          { id: 28, name: "P2000", category_id: 5 },
          { id: 29, name: "Dual Berettas", category_id: 5 }
        ]
      },
      {
        label: "Zeus X27",
        items: [{ id: 40, name: "Zeus X27", category_id: 14 }]
      }
    ]
  },
  {
    id: 1,
    name: "Knives",
    subcategory: [
      {
        label: "All Knives",
        items: [
          { id: 52, name: "Shadow Daggers", category_id: 1 },
          { id: 53, name: "Huntsman Knife", category_id: 1 },
          { id: 54, name: "Skeleton Knife", category_id: 1 },
          { id: 55, name: "Talon Knife", category_id: 1 },
          { id: 56, name: "M9 Bayonet", category_id: 1 },
          { id: 57, name: "Ursus Knife", category_id: 1 },
          { id: 58, name: "Nomad Knife", category_id: 1 },
          { id: 59, name: "Stiletto Knife", category_id: 1 },
          { id: 60, name: "Flip Knife", category_id: 1 },
          { id: 61, name: "Butterfly Knife", category_id: 1 },
          { id: 62, name: "Paracord Knife", category_id: 1 },
          { id: 63, name: "Gut Knife", category_id: 1 },
          { id: 64, name: "Survival Knife", category_id: 1 },
          { id: 65, name: "Classic Knife", category_id: 1 },
          { id: 66, name: "Bowie Knife", category_id: 1 },
          { id: 67, name: "Bayonet", category_id: 1 },
          { id: 68, name: "Karambit", category_id: 1 },
          { id: 69, name: "Navaja Knife", category_id: 1 },
          { id: 70, name: "Falchion Knife", category_id: 1 }
        ]
      }
    ]
  },
  {
    id: 2,
    name: "Gloves",
    subcategory: [
      {
        label: "All Gloves",
        items: [
          { id: 1, name: "Driver Gloves", category_id: 2 },
          { id: 2, name: "Hand Wraps", category_id: 2 },
          { id: 3, name: "Hydra Gloves", category_id: 2 },
          { id: 4, name: "Moto Gloves", category_id: 2 },
          { id: 5, name: "Specialist Gloves", category_id: 2 },
          { id: 6, name: "Sport Gloves", category_id: 2 },
          { id: 7, name: "Broken Fang Gloves", category_id: 2 },
          { id: 8, name: "Bloodhound Gloves", category_id: 2 }
        ]
      }
    ]
  },
  {
    id: "combined-other",
    name: "Other",
    subcategory: [
      {
        label: "Graffiti",
        items: [{ id: 47, name: "Graffiti", category_id: 10 }]
      },
      {
        label: "Key",
        items: [{ id: 49, name: "Key", category_id: 12 }]
      },
      {
        label: "Agent",
        items: [{ id: 50, name: "Agent", category_id: 13 }]
      },
      {
        label: "Sticker",
        items: [
          { id: 43, name: "Team Logo", category_id: 9 },
          { id: 44, name: "Tournament", category_id: 9 },
          { id: 45, name: "Player Autograph", category_id: 9 },
          { id: 46, name: "Regular", category_id: 9 }
        ]
      },
      {
        label: "Container",
        items: [
          { id: 48, name: "Container", category_id: 11 },
          { id: 71, name: "Package", category_id: 11 },
          { id: 72, name: "Sticker_Capsule", category_id: 11 },
          { id: 73, name: "Collection", category_id: 11 },
          { id: 74, name: "Crate", category_id: 11 }
        ]
      }
    ]
  }
];
const Header = () => {
  return /* @__PURE__ */ React__default.createElement(
    Box,
    {
      sx: {
        display: "flex",
        flexDirection: "column"
      }
    },
    /* @__PURE__ */ React__default.createElement(
      AppBar,
      {
        position: "sticky",
        sx: { width: "100vw", overflowX: "hidden" }
      },
      /* @__PURE__ */ React__default.createElement(Container, { maxWidth: "xl", disableGutters: true, sx: { px: 0 } }, /* @__PURE__ */ React__default.createElement(Toolbar, { disableGutters: true }, /* @__PURE__ */ React__default.createElement(
        Box,
        {
          sx: {
            display: "flex",
            justifyContent: "space-between",
            width: "100%",
            alignItems: "center"
          }
        },
        /* @__PURE__ */ React__default.createElement(
          Box,
          {
            sx: {
              display: "flex",
              alignItems: "center",
              mr: 4,
              mb: 1,
              mt: 1
            }
          },
          /* @__PURE__ */ React__default.createElement(
            Link$1,
            {
              href: "/",
              style: {
                display: "inline-flex",
                alignItems: "center"
              }
            },
            /* @__PURE__ */ React__default.createElement(
              "img",
              {
                src: "/logo.png",
                alt: "AboutCSGO Logo",
                style: {
                  maxWidth: "150px",
                  maxHeight: "75px"
                }
              }
            )
          )
        ),
        /* @__PURE__ */ React__default.createElement(
          Box,
          {
            sx: {
              display: "flex",
              alignItems: "center",
              justifyContent: "center",
              flex: 1
            }
          },
          categories.map((category) => /* @__PURE__ */ React__default.createElement(
            CategoryDropdown,
            {
              key: category.id,
              category
            }
          ))
        ),
        /* @__PURE__ */ React__default.createElement(Box, { sx: { display: "flex", alignItems: "center" } }, /* @__PURE__ */ React__default.createElement(
          Typography,
          {
            variant: "body1",
            sx: { color: "white" }
          },
          "Some Text (Search bar placeholder)"
        ))
      )))
    )
  );
};
const Layout = ({ children }) => {
  const theme = useTheme();
  return /* @__PURE__ */ React__default.createElement("div", null, /* @__PURE__ */ React__default.createElement("header", null, /* @__PURE__ */ React__default.createElement(Header, null)), /* @__PURE__ */ React__default.createElement("main", null, /* @__PURE__ */ React__default.createElement(
    Container,
    {
      style: {
        backgroundColor: theme.palette.background.paper,
        borderRadius: "5px",
        textAlign: "center",
        // Align content (including the image) to center
        marginTop: "30px",
        paddingTop: "20px",
        minHeight: "calc(100vh - 64px)"
        // Subtract header height
      }
    },
    children
  )));
};
createServer(
  (page) => createInertiaApp({
    page,
    render: ReactDOMServer.renderToString,
    resolve: (name) => {
      const pages = /* @__PURE__ */ Object.assign({ "./Pages/Home.tsx": __vite_glob_0_0 });
      let page2 = pages[`./Pages/${name}.tsx`];
      page2.default.layout = page2.default.layout || ((page3) => /* @__PURE__ */ React__default.createElement(Layout, null, page3));
      return page2;
    },
    setup: ({ App, props }) => /* @__PURE__ */ React__default.createElement(ThemeProvider, { theme: darkTheme$1 }, /* @__PURE__ */ React__default.createElement(CssBaseline, null), /* @__PURE__ */ React__default.createElement(App, { ...props }))
  })
);
