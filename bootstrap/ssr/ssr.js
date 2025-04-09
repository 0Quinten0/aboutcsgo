import { usePage, Head, Link, createInertiaApp } from "@inertiajs/react";
import * as React from "react";
import React__default, { useState, useRef, useEffect, useContext } from "react";
import { useTheme, Box, TextField, ClickAwayListener, CircularProgress, List, ListItem, ListItemIcon, ListItemText, Typography, Grid, Card, CardActionArea, CardMedia, CardContent, Button, Container, GlobalStyles, createTheme, responsiveFontSizes, Link as Link$1, AppBar, Toolbar, ThemeProvider, CssBaseline } from "@mui/material";
import ReactDOM from "react-dom";
import axios from "axios";
import { useParams } from "react-router-dom";
import ReactDOMServer from "react-dom/server";
import createServer from "@inertiajs/react/server";
import { usePopupState, bindHover, bindMenu } from "material-ui-popup-state/hooks";
import HoverMenu from "material-ui-popup-state/HoverMenu";
const axiosClient = axios.create({
  baseURL: "https://api.aboutcsgo.com/"
  // baseURL: "http://127.0.0.1:8000/api/v1/",
});
const ItemSkinSearch = ({ compact = false }) => {
  const [query, setQuery] = useState("");
  const [results, setResults] = useState([]);
  const [loading, setLoading] = useState(false);
  const [showDropdown, setShowDropdown] = useState(false);
  const [dropdownTop, setDropdownTop] = useState(0);
  const [dropdownLeft, setDropdownLeft] = useState(0);
  const [dropdownWidth, setDropdownWidth] = useState(0);
  const inputRef = useRef(null);
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
  const handleClickAway = (event) => {
    if (inputRef.current && inputRef.current.contains(event.target)) {
      return;
    }
    setShowDropdown(false);
  };
  return /* @__PURE__ */ React__default.createElement(React__default.Fragment, null, /* @__PURE__ */ React__default.createElement(
    Box,
    {
      sx: {
        width: compact ? "250px" : "100%",
        mx: "auto"
      }
    },
    /* @__PURE__ */ React__default.createElement(
      TextField,
      {
        type: "text",
        variant: "outlined",
        fullWidth: true,
        inputRef,
        placeholder: "Search item skins...",
        value: query,
        onChange: (e) => setQuery(e.target.value),
        size: compact ? "small" : "medium",
        onFocus: () => {
          if (results.length > 0) setShowDropdown(true);
        },
        sx: {
          "& .MuiOutlinedInput-root": {
            "& fieldset": { borderColor: "#ccc" },
            "&:hover fieldset": { borderColor: "#FFFFFF" },
            "&.Mui-focused fieldset": {
              borderColor: "#FFFFFF"
            }
          }
        }
      }
    )
  ), showDropdown && ReactDOM.createPortal(
    /* @__PURE__ */ React__default.createElement(ClickAwayListener, { onClickAway: handleClickAway }, /* @__PURE__ */ React__default.createElement(
      Box,
      {
        sx: {
          position: "absolute",
          top: dropdownTop,
          left: dropdownLeft,
          width: dropdownWidth,
          backgroundColor: theme.palette.background.paper,
          zIndex: 1300,
          borderRadius: "4px",
          boxShadow: "0px 4px 12px rgba(0,0,0,0.2)",
          maxHeight: "500px",
          overflowY: "auto"
        }
      },
      loading ? /* @__PURE__ */ React__default.createElement(
        Box,
        {
          sx: {
            display: "flex",
            justifyContent: "center",
            p: 2
          }
        },
        /* @__PURE__ */ React__default.createElement(CircularProgress, { size: 24 })
      ) : /* @__PURE__ */ React__default.createElement(List, null, results.map((item) => /* @__PURE__ */ React__default.createElement(
        ListItem,
        {
          key: item.id,
          component: "div",
          sx: {
            borderBottom: "1px solid #eee",
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
            style: {
              width: "40px",
              height: "auto"
            }
          }
        )),
        /* @__PURE__ */ React__default.createElement(
          ListItemText,
          {
            primary: item.combined_name
          }
        )
      )))
    )),
    document.body
  ));
};
const Home = () => {
  const { props } = usePage();
  const { popularItems } = props;
  const theme = useTheme();
  return /* @__PURE__ */ React__default.createElement(React__default.Fragment, null, /* @__PURE__ */ React__default.createElement(Head, null, /* @__PURE__ */ React__default.createElement("title", null, "Home | AboutCSGO"), /* @__PURE__ */ React__default.createElement(
    "meta",
    {
      "head-key": "description",
      name: "description",
      content: "AboutCSGO home page where you can find all the prices and info about CS2 skins"
    }
  )), /* @__PURE__ */ React__default.createElement(Typography, { variant: "h2", gutterBottom: true, sx: { marginBottom: "20px" } }, "AboutCSGO"), /* @__PURE__ */ React__default.createElement(ItemSkinSearch, null), /* @__PURE__ */ React__default.createElement(Box, null, /* @__PURE__ */ React__default.createElement(Typography, { variant: "h4", gutterBottom: true }, "Popular Items"), /* @__PURE__ */ React__default.createElement(Grid, { container: true, spacing: 2, justifyContent: "center" }, popularItems.slice(0, 20).map((item) => /* @__PURE__ */ React__default.createElement(
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
const NotFound = () => {
  return /* @__PURE__ */ React__default.createElement(React__default.Fragment, null, /* @__PURE__ */ React__default.createElement(Head, null, /* @__PURE__ */ React__default.createElement("title", null, "Not Found | AboutCSGO"), /* @__PURE__ */ React__default.createElement(
    "meta",
    {
      "head-key": "description",
      name: "description",
      content: "Learn more about CS:GO skins and weapons."
    }
  )), /* @__PURE__ */ React__default.createElement("style", null, `
          body {
            margin: 0;
            padding: 0;
          }
        `), /* @__PURE__ */ React__default.createElement(
    Box,
    {
      sx: {
        display: "flex",
        flexDirection: "column",
        alignItems: "center",
        justifyContent: "center",
        height: "100vh",
        textAlign: "center",
        padding: 2
        // MUI v5 uses the theme's spacing unit, equivalent to 8px by default
      }
    },
    /* @__PURE__ */ React__default.createElement(Typography, { variant: "h1" }, "404"),
    /* @__PURE__ */ React__default.createElement(Typography, { variant: "h5" }, "Page Not Found"),
    /* @__PURE__ */ React__default.createElement(Typography, { variant: "body1" }, "Sorry, the page you are looking for does not exist."),
    /* @__PURE__ */ React__default.createElement(
      Button,
      {
        variant: "contained",
        color: "primary",
        component: Link,
        href: "/",
        sx: { mt: 2 }
      },
      "Go to Home"
    )
  ));
};
const __vite_glob_0_1 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: NotFound
}, Symbol.toStringTag, { value: "Module" }));
const helmetContext = React__default.createContext({});
const useHelmet = () => {
  return useContext(helmetContext);
};
const PrivacyPolicy = () => {
  useHelmet();
  const theme = useTheme();
  return /* @__PURE__ */ React__default.createElement(React__default.Fragment, null, /* @__PURE__ */ React__default.createElement(Head, null, /* @__PURE__ */ React__default.createElement("title", null, "Privacy Policy | AboutCSGO"), /* @__PURE__ */ React__default.createElement(
    "meta",
    {
      "head-key": "description",
      name: "description",
      content: `The privacy policy of AboutCSGO`
    }
  ), /* @__PURE__ */ React__default.createElement(
    "link",
    {
      "head-key": "canonical",
      rel: "canonical",
      href: `https://www.aboutcsgo.com/privacy-policy`
    }
  )), /* @__PURE__ */ React__default.createElement(
    Container,
    {
      style: {
        backgroundColor: theme.palette.background.paper,
        borderRadius: "5px",
        textAlign: "center",
        // Align content (including the image) to center
        marginBottom: "30px",
        paddingTop: "20px",
        minHeight: "calc(100vh - 64px)"
        // Subtract header height
      }
    },
    /* @__PURE__ */ React__default.createElement(Typography, { variant: "h2", gutterBottom: true }, "Privacy Policy"),
    /* @__PURE__ */ React__default.createElement(Typography, { variant: "body1", paragraph: true }, "At AboutCSGO, we are committed to protecting your privacy. This Privacy Policy outlines how we collect, use, and disclose your personal information when you use our website."),
    /* @__PURE__ */ React__default.createElement(Typography, { variant: "body1", paragraph: true }, "When you log in to AboutCSGO using Steam OpenID, we collect and store basic information from your Steam profile, including your steam_id, nickname, profile URL, and avatar. This information is stored in our database to enhance your user experience on our platform."),
    /* @__PURE__ */ React__default.createElement(Typography, { variant: "body1", paragraph: true }, "Please note that the data we collect from your Steam profile is considered public data since it can be accessed from your public Steam profile. We do not collect any sensitive information beyond what is publicly available on Steam."),
    /* @__PURE__ */ React__default.createElement(Typography, { variant: "body1", paragraph: true }, "In addition to Steam profile data, we also store user actions such as votes and other interactions with our platform. This data helps us improve our services and tailor the user experience to your needs."),
    /* @__PURE__ */ React__default.createElement(Typography, { variant: "body1", paragraph: true }, "We are committed to protecting your personal information and ensuring its confidentiality. We do not sell, trade, or otherwise transfer your personal information to third parties without your consent.")
  ));
};
const __vite_glob_0_2 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: PrivacyPolicy
}, Symbol.toStringTag, { value: "Module" }));
const PriceList = ({
  exteriorOrder,
  type,
  label,
  color,
  onPriceClick,
  getLowestPriceAcrossMarketplaces,
  selectedExterior,
  selectedType
}) => {
  const theme = useTheme();
  return /* @__PURE__ */ React__default.createElement(React__default.Fragment, null, exteriorOrder.map((condition) => {
    const displayCondition = condition === "No Exterior" ? "Vanilla" : condition;
    const lowestPrice = getLowestPriceAcrossMarketplaces(
      condition,
      type
    );
    const isSelected = selectedExterior === condition && selectedType === type;
    return /* @__PURE__ */ React__default.createElement(Box, { key: condition, sx: { marginBottom: 1 } }, /* @__PURE__ */ React__default.createElement(
      Button,
      {
        fullWidth: true,
        variant: "contained",
        sx: {
          justifyContent: "flex-start",
          textTransform: "none",
          backgroundColor: isSelected ? theme.palette.primary.dark : lowestPrice ? void 0 : theme.palette.background.paper,
          // Light gray for disabled state
          color: isSelected ? theme.palette.primary.contrastText : lowestPrice ? void 0 : "#a0a0a0",
          // Slightly darker white for text
          "&:hover": {
            backgroundColor: isSelected ? theme.palette.primary.dark : lowestPrice ? theme.palette.primary.dark : theme.palette.background.paper
            // Darker gray on hover for disabled state
          },
          "&:disabled": {
            backgroundColor: theme.palette.background.paper,
            // Ensure the background is light gray when disabled
            color: "#a0a0a0"
            // Ensure text is slightly darker white
          }
        },
        onClick: () => onPriceClick(condition, type),
        disabled: !lowestPrice
      },
      label && // Only render the label if it's not null
      /* @__PURE__ */ React__default.createElement(Typography, { variant: "body1", style: { color } }, label),
      /* @__PURE__ */ React__default.createElement(
        Typography,
        {
          variant: "body1",
          style: { marginRight: "auto" }
        },
        " ",
        displayCondition
      ),
      /* @__PURE__ */ React__default.createElement(Typography, { variant: "body1" }, lowestPrice ? `€${lowestPrice}` : "No offers")
    ));
  }));
};
const PriceDetails = ({
  selectedExterior,
  priceType,
  prices,
  skinName,
  weaponName,
  weaponCategory
}) => {
  const theme = useTheme();
  const exteriorOrder = [
    "Factory New",
    // 0
    "Minimal Wear",
    // 1
    "Field-Tested",
    // 2
    "Well-Worn",
    // 3
    "Battle-Scarred"
    // 4
  ];
  const determineTypeDetails = (type, category) => {
    let prefix = "";
    const isSpecialCategory = category === "Knives" || category === "Gloves";
    if (isSpecialCategory) {
      prefix = "★ ";
    }
    switch (type) {
      case "stattrak":
        prefix += "StatTrak™ ";
        break;
      case "souvenir":
        prefix += "Souvenir ";
        break;
    }
    return { prefix };
  };
  const urlGenerators = {
    gamerpay: ({ item, skin, exterior, prefix }) => {
      const queryString = `${prefix}${item} | ${skin} (${exterior})`;
      return `https://gamerpay.gg/?query=${encodeURIComponent(
        queryString
      )}&sortBy=price&ascending=true&ref=aboutcsgo`;
    },
    csfloat: ({
      item,
      skin,
      exterior,
      category,
      prefix,
      type
    }) => {
      const typeToCategoryNumber = {
        souvenir: 3,
        stattrak: 2,
        default: 1
      };
      const categoryNumber = typeToCategoryNumber[type] || typeToCategoryNumber["default"];
      const queryString = `${prefix}${item} | ${skin} (${exterior})`;
      return `https://csfloat.com/search?category=${categoryNumber}&sort_by=lowest_price&type=buy_now&market_hash_name=${encodeURIComponent(
        queryString
      )}`;
    },
    skinbaron: ({ item, skin, exterior, type, category }) => {
      let queryParams = `?sort=CF`;
      if (type === "stattrak") {
        queryParams += `&statTrak=true`;
      } else if (type === "souvenir") {
        queryParams += `&souvenir=true`;
      }
      const formattedItem = item.replace(/\s+/g, "-");
      const formattedSkin = skin.replace(/\s+/g, "-");
      const formattedExterior = exterior.replace(/\s+/g, "-");
      const formattedCategory = category === "Knives" ? "Knife" : category;
      return `https://skinbaron.de/en/csgo/${encodeURIComponent(
        formattedCategory
      )}/${encodeURIComponent(formattedItem)}/${encodeURIComponent(
        formattedSkin
      )}/${encodeURIComponent(formattedExterior)}${queryParams}`;
    },
    shadowpay: ({ item, skin, exterior, type }) => {
      const baseUrl = `https://shadowpay.com/csgo-items`;
      const prefix = type === "stattrak" ? "StatTrak%E2%84%A2%20" : type === "souvenir" ? "Souvenir%20" : "★%20";
      const queryString = `${prefix}${item.replace(
        /\s+/g,
        "%20"
      )}%20%7C%20${skin.replace(/\s+/g, "%20")}`;
      const encodedExterior = `[${encodeURIComponent(`"${exterior}"`)}]`;
      const isStattrak = type === "stattrak" ? 1 : 0;
      return `${baseUrl}?search=${queryString}&is_stattrak=${isStattrak}&sort_column=price&sort_dir=asc&utm_campaign=ptPfUYVmLE4KjaH&exteriors=${encodedExterior}`;
    },
    skinwallet: ({ item, skin, exterior, type, category }) => {
      const baseUrl = `https://www.skinwallet.com/market/offers?appId=730`;
      const prefix = category === "Knives" || category === "Gloves" ? "★ " : "";
      const queryString = `${prefix}${item.replace(
        /\s+/g,
        "%20"
      )}%20%7C%20${skin.replace(/\s+/g, "%20")}`;
      const exteriorCode = exteriorOrder.indexOf(exterior);
      return `${baseUrl}&search=${queryString}&sortBy=HotDeals${exteriorCode !== -1 ? `&exterior%5B%5D=WearCategory${exteriorCode}` : ""}`;
    },
    waxpeer: ({ item, skin, exterior, type, category }) => {
      const baseUrl = `https://waxpeer.com/?sort=ASC&order=price&all=0&skip=0&game=csgo`;
      const prefix = category === "Knives" || category === "Gloves" ? "★ " : "";
      const typePrefix = type === "stattrak" ? "StatTrak™ " : type === "souvenir" ? "Souvenir " : "";
      const queryString = `${prefix}${typePrefix}${item.replace(
        /\s+/g,
        "%20"
      )}%20%7C%20${skin.replace(/\s+/g, "%20")}%20(${exterior.replace(
        /\s+/g,
        "%20"
      )})`;
      return `${baseUrl}&search=${encodeURIComponent(
        queryString
      )}&exact=1&ref=aboutcsgo`;
    },
    market_csgo: ({ item, skin, exterior, type, category }) => {
      const baseUrl = `https://market.csgo.com/en/?order=asc&sort=price`;
      const prefix = category === "Knives" || category === "Gloves" ? "★ " : "";
      const typePrefix = type === "stattrak" ? "StatTrak™ " : type === "souvenir" ? "Souvenir " : "";
      const queryString = `${prefix}${typePrefix}${item} | ${skin} (${exterior})`;
      return `${baseUrl}&search=${encodeURIComponent(queryString)}`;
    },
    skinport: ({ item, skin, exterior, type, category }) => {
      const baseUrl = `https://skinport.com/item`;
      const typePrefix = type === "stattrak" ? "stattrak-" : type === "souvenir" ? "souvenir-" : "";
      const formattedItem = item.replace(/\s+/g, "-").toLowerCase();
      const formattedSkin = skin.replace(/\s+/g, "-").toLowerCase();
      const formattedExterior = exterior.replace(/\s+/g, "-").toLowerCase();
      const urlPath = `${typePrefix}${formattedItem}-${formattedSkin}-${formattedExterior}`;
      return `${baseUrl}/${urlPath}?r=aboutcsgo`;
    },
    steam: ({ item, skin, exterior, type, category }) => {
      const baseUrl = `https://steamcommunity.com/market/listings/730/`;
      const { prefix } = determineTypeDetails(type, category);
      if (category === "Knives" && skin === "Vanilla") {
        const formattedItem2 = item.replace(/\s+/g, "%20").replace("|", "%7C").replace(/\(/g, "%28").replace(/\)/g, "%29");
        return `${baseUrl}${prefix}${formattedItem2}`;
      }
      const formattedItem = item.replace(/\s+/g, "%20").replace("|", "%7C").replace(/\(/g, "%28").replace(/\)/g, "%29");
      const formattedSkin = skin.replace(/\s+/g, "%20").replace("|", "%7C").replace(/\(/g, "%28").replace(/\)/g, "%29");
      const formattedExterior = exterior.replace(/\s+/g, "%20").replace("|", "%7C").replace(/\(/g, "%28").replace(/\)/g, "%29");
      const urlPath = `${prefix}${formattedItem}%20%7C%20${formattedSkin}%20%28${formattedExterior}%29`;
      return `${baseUrl}${urlPath}`;
    },
    bitskins: ({ item, skin, exterior, type, category }) => {
      const { prefix } = determineTypeDetails(type, category);
      const searchName = category === "Knives" && skin === "Vanilla" ? `${prefix}${item}` : `${prefix}${item} | ${skin} (${exterior})`;
      const encodedSearchName = encodeURIComponent(searchName);
      return `https://bitskins.com/market/cs2?search={"order":[{"field":"price","order":"ASC"}],"where":{"skin_name":"${encodedSearchName}"}}&ref_alias=aboutcsgo`;
    }
    // Add more marketplaces here...
  };
  const sortedPrices = Object.entries(prices).map(([marketplace, priceData]) => {
    const priceCategory = priceData[priceType];
    const price = (priceCategory == null ? void 0 : priceCategory[selectedExterior]) ?? "N/A";
    return {
      marketplace,
      price: price === "N/A" ? Infinity : parseFloat(price.replace("€", "").replace(",", "."))
      // Convert to a number for sorting
    };
  }).sort((a, b) => a.price - b.price);
  const handleBuyClick = (marketplace) => {
    const generator = urlGenerators[marketplace];
    if (generator) {
      const { prefix } = determineTypeDetails(priceType, weaponCategory);
      const params = {
        skin: skinName,
        // Use the actual skin name
        item: weaponName,
        // Use the actual item (weapon) name
        type: priceType,
        // Determine the type (e.g., Normal, StatTrak™, Souvenir, etc.)
        exterior: selectedExterior,
        // Use the selected exterior
        category: weaponCategory,
        // Use the provided weapon category
        prefix
        // Add the determined prefix
      };
      const url = generator(params);
      window.open(url, "_blank");
    }
  };
  return /* @__PURE__ */ React__default.createElement(Box, { sx: { marginTop: 0 } }, /* @__PURE__ */ React__default.createElement(Typography, { variant: "h6", sx: { marginBottom: 1 } }, "Offers"), sortedPrices.map(({ marketplace, price }) => {
    var _a, _b;
    const formattedPrice = isFinite(price) ? `€${price.toFixed(2)}` : "N/A";
    const logoUrl = ((_a = prices[marketplace]) == null ? void 0 : _a.image_url) ?? "";
    const prettyMarketplaceName = ((_b = prices[marketplace]) == null ? void 0 : _b.pretty_name) ?? "";
    return /* @__PURE__ */ React__default.createElement(
      Box,
      {
        key: marketplace,
        sx: {
          display: "flex",
          alignItems: "center",
          padding: "8px 16px",
          borderRadius: "8px",
          marginBottom: 2,
          backgroundColor: theme.palette.background.paper,
          border: `1px solid ${theme.palette.divider}`
        }
      },
      /* @__PURE__ */ React__default.createElement(
        Box,
        {
          sx: {
            display: "flex",
            alignItems: "center",
            minWidth: 150,
            // Fixed width for consistent alignment
            flexShrink: 0,
            marginRight: 2
            // Space between name/logo and price
          }
        },
        /* @__PURE__ */ React__default.createElement(
          "img",
          {
            src: logoUrl,
            alt: `${marketplace} logo`,
            style: {
              width: 40,
              height: 40,
              marginRight: 16
            },
            onError: (e) => e.currentTarget.style.display = "none"
          }
        ),
        /* @__PURE__ */ React__default.createElement(Typography, { variant: "body1" }, prettyMarketplaceName)
      ),
      /* @__PURE__ */ React__default.createElement(
        Box,
        {
          sx: {
            flexGrow: 1,
            // Allow price to use remaining space
            display: "flex",
            justifyContent: "flex-end"
            // Align price to the end
          }
        },
        /* @__PURE__ */ React__default.createElement(
          Typography,
          {
            variant: "body1",
            sx: {
              textAlign: "left",
              // Ensure text alignment within its container
              marginRight: 10
              // Space between price and button
            }
          },
          formattedPrice
        )
      ),
      /* @__PURE__ */ React__default.createElement(
        Button,
        {
          variant: "contained",
          size: "small",
          sx: {
            backgroundColor: theme.palette.primary.light,
            marginLeft: 2
            // Space between price and button
          },
          onClick: () => handleBuyClick(marketplace)
        },
        "Buy"
      )
    );
  }));
};
const SkinLayout = () => {
  var _a;
  const { weaponName, skinName } = useParams();
  const { skinData } = usePage().props;
  const weaponNameTitle = (skinData == null ? void 0 : skinData.item_id) ?? "Unknown Weapon";
  const skinNameTitle = (skinData == null ? void 0 : skinData.skin) ?? "Unknown Skin";
  const theme = useTheme();
  const isVanillaSkin = skinData.skin === "Vanilla";
  const exteriorOrder = isVanillaSkin ? ["No Exterior"] : [
    "Factory New",
    "Minimal Wear",
    "Field-Tested",
    "Well-Worn",
    "Battle-Scarred"
  ];
  const [selectedExterior, setSelectedExterior] = useState(exteriorOrder[0]);
  const [priceType, setPriceType] = useState("normal");
  const getLowestPriceAcrossMarketplaces = (condition, type) => {
    let lowestPrice = null;
    for (const marketplacePrices of Object.values(skinData.prices)) {
      const price = getLowestPriceForCondition(
        condition,
        type,
        marketplacePrices
      );
      if (price && (!lowestPrice || parseFloat(price) < parseFloat(lowestPrice))) {
        lowestPrice = price;
      }
    }
    return lowestPrice;
  };
  const getLowestPriceForCondition = (condition, type, prices) => {
    var _a2, _b, _c;
    switch (type) {
      case "normal":
        return ((_a2 = prices.normal) == null ? void 0 : _a2[condition]) ?? null;
      case "stattrak":
        return ((_b = prices.stattrak) == null ? void 0 : _b[condition]) ?? null;
      case "souvenir":
        return ((_c = prices.souvenir) == null ? void 0 : _c[condition]) ?? null;
      default:
        return null;
    }
  };
  const handlePriceClick = (exterior, type) => {
    setSelectedExterior(exterior);
    setPriceType(type);
  };
  return /* @__PURE__ */ React__default.createElement(React__default.Fragment, null, /* @__PURE__ */ React__default.createElement(Head, null, /* @__PURE__ */ React__default.createElement("title", null, `${weaponNameTitle} | ${skinNameTitle} - AboutCSGO`), /* @__PURE__ */ React__default.createElement(
    "meta",
    {
      "head-key": "description",
      name: "description",
      content: `All the information and best prices for ${weaponNameTitle} | ${skinNameTitle} | AboutCSGO`
    }
  ), /* @__PURE__ */ React__default.createElement(
    "link",
    {
      "head-key": "canonical",
      rel: "canonical",
      href: `https://www.aboutcsgo.com/skin/${weaponNameTitle}/${skinNameTitle}`
    }
  )), /* @__PURE__ */ React__default.createElement(
    GlobalStyles,
    {
      styles: {
        body: {
          margin: 0,
          padding: 0
        }
      }
    }
  ), /* @__PURE__ */ React__default.createElement(
    Container,
    {
      style: {
        backgroundColor: theme.palette.background.default,
        borderRadius: "5px",
        marginTop: 20,
        minHeight: "calc(100vh - 64px)"
      }
    },
    /* @__PURE__ */ React__default.createElement(
      Grid,
      {
        container: true,
        spacing: 2,
        justifyContent: "center",
        style: { maxWidth: 1152, margin: "auto" }
      },
      /* @__PURE__ */ React__default.createElement(Grid, { item: true, xs: 12, md: 4 }, /* @__PURE__ */ React__default.createElement(
        Box,
        {
          sx: {
            backgroundColor: theme.palette.background.paper,
            display: "flex",
            flexDirection: "column",
            alignItems: "center",
            justifyContent: "center",
            width: "100%",
            height: "100%",
            borderRadius: "5px"
          }
        },
        /* @__PURE__ */ React__default.createElement(
          Typography,
          {
            variant: "h6",
            component: "h1",
            gutterBottom: true
          },
          weaponName,
          " ",
          skinName
        ),
        skinData && /* @__PURE__ */ React__default.createElement(React__default.Fragment, null, /* @__PURE__ */ React__default.createElement(
          Typography,
          {
            sx: {
              backgroundColor: skinData.quality_color,
              color: "#fff",
              padding: "4px 8px",
              borderRadius: "4px",
              width: "80%",
              textAlign: "center"
            },
            gutterBottom: true,
            align: "center"
          },
          skinData.quality
        ), skinData.stattrak === 1 && /* @__PURE__ */ React__default.createElement(
          Typography,
          {
            variant: "body2",
            align: "center",
            sx: {
              backgroundColor: "#F89406",
              color: "#fff",
              padding: "4px 8px",
              borderRadius: "4px",
              width: "80%",
              textAlign: "center"
            }
          },
          "StatTrak available"
        ), /* @__PURE__ */ React__default.createElement(
          "img",
          {
            src: skinData.image_url,
            alt: skinData.skin,
            style: {
              width: "100%",
              marginTop: "16px",
              objectFit: "cover"
            }
          }
        ))
      )),
      /* @__PURE__ */ React__default.createElement(Grid, { item: true, xs: 12, md: 8 }, /* @__PURE__ */ React__default.createElement(
        Box,
        {
          sx: {
            width: "100%",
            height: "auto",
            borderRadius: "5px",
            display: "flex",
            flexDirection: "row",
            justifyContent: "space-between"
          }
        },
        /* @__PURE__ */ React__default.createElement(Box, { sx: { width: "48%" } }, /* @__PURE__ */ React__default.createElement(Typography, { variant: "subtitle1", gutterBottom: true }, "Normal Prices"), /* @__PURE__ */ React__default.createElement(
          PriceList,
          {
            exteriorOrder,
            type: "normal",
            label: null,
            color: "#ffffff",
            onPriceClick: handlePriceClick,
            getLowestPriceAcrossMarketplaces,
            selectedExterior,
            selectedType: priceType
          }
        )),
        (skinData.stattrak === 1 || skinData.souvenir === 1) && /* @__PURE__ */ React__default.createElement(Box, { sx: { width: "48%" } }, /* @__PURE__ */ React__default.createElement(
          Typography,
          {
            variant: "subtitle1",
            gutterBottom: true
          },
          skinData.stattrak === 1 ? "StatTrak™ Prices" : "Souvenir Prices"
        ), /* @__PURE__ */ React__default.createElement(
          PriceList,
          {
            exteriorOrder,
            type: skinData.stattrak === 1 ? "stattrak" : "souvenir",
            label: skinData.stattrak === 1 ? "StatTrak™" : "Souvenir",
            color: skinData.stattrak === 1 ? "#F89406" : "#ffd900",
            onPriceClick: handlePriceClick,
            getLowestPriceAcrossMarketplaces,
            selectedExterior,
            selectedType: priceType
          }
        ))
      )),
      /* @__PURE__ */ React__default.createElement(Grid, { item: true, xs: 12, md: 4 }, /* @__PURE__ */ React__default.createElement(
        Box,
        {
          sx: {
            backgroundColor: theme.palette.background.paper,
            width: "100%",
            height: "auto",
            borderRadius: "5px"
          }
        },
        /* @__PURE__ */ React__default.createElement(
          Typography,
          {
            variant: "body2",
            sx: {
              paddingTop: 2,
              marginLeft: 1,
              paddingBottom: 2
            },
            dangerouslySetInnerHTML: {
              __html: ((_a = skinData == null ? void 0 : skinData.description) == null ? void 0 : _a.replace(
                /\\n\\n/g,
                "<br/> <br/>"
              )) ?? ""
            }
          }
        )
      )),
      /* @__PURE__ */ React__default.createElement(Grid, { item: true, xs: 12, md: 8 }, /* @__PURE__ */ React__default.createElement(
        Box,
        {
          sx: {
            borderRadius: "5px",
            width: "100%",
            height: "auto",
            padding: "0px"
          }
        },
        selectedExterior && priceType && /* @__PURE__ */ React__default.createElement(
          PriceDetails,
          {
            selectedExterior,
            priceType,
            prices: skinData.prices,
            weaponName: weaponName ?? "DefaultWeaponName",
            skinName: skinName ?? "DefaultSkinName",
            weaponCategory: skinData.category
          }
        )
      ))
    )
  ));
};
const __vite_glob_0_3 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: SkinLayout
}, Symbol.toStringTag, { value: "Module" }));
const TermsOfService = () => {
  useHelmet();
  const theme = useTheme();
  return /* @__PURE__ */ React__default.createElement(React__default.Fragment, null, /* @__PURE__ */ React__default.createElement("style", null, `
          body {
            margin: 0;
            padding: 0;
          }
        `), /* @__PURE__ */ React__default.createElement(Head, null, /* @__PURE__ */ React__default.createElement("title", null, "Terms Of Service | AboutCSGO"), /* @__PURE__ */ React__default.createElement(
    "meta",
    {
      "head-key": "description",
      name: "description",
      content: `The terms of service of AboutCSGO`
    }
  ), /* @__PURE__ */ React__default.createElement(
    "link",
    {
      "head-key": "canonical",
      rel: "canonical",
      href: `https://www.aboutcsgo.com/terms-of-service`
    }
  )), /* @__PURE__ */ React__default.createElement(
    Container,
    {
      style: {
        backgroundColor: theme.palette.background.paper,
        borderRadius: "5px",
        textAlign: "center",
        // Align content (including the image) to center
        paddingTop: "20px",
        marginBottom: "30px",
        minHeight: "calc(100vh - 64px)"
        // Subtract header height
      }
    },
    /* @__PURE__ */ React__default.createElement(Typography, { variant: "h2", gutterBottom: true }, "Terms of Service"),
    /* @__PURE__ */ React__default.createElement(Typography, { variant: "body1", paragraph: true }, "Welcome to AboutCSGO!"),
    /* @__PURE__ */ React__default.createElement(Typography, { variant: "body1", paragraph: true }, "These terms and conditions outline the rules and regulations for the use of AboutCSGO's Website, located at aboutcsgo.com."),
    /* @__PURE__ */ React__default.createElement(Typography, { variant: "body1", paragraph: true }, "By accessing this website we assume you accept these terms and conditions. Do not continue to use AboutCSGO if you do not agree to take all of the terms and conditions stated on this page."),
    /* @__PURE__ */ React__default.createElement(Typography, { variant: "body1", paragraph: true }, `The following terminology applies to these Terms and Conditions, Privacy Statement and Disclaimer Notice and all Agreements: "Client", "You" and "Your" refers to you, the person log on this website and compliant to the Company's terms and conditions. "The Company", "Ourselves", "We", "Our" and "Us", refers to our Company. "Party", "Parties", or "Us", refers to both the Client and ourselves. All terms refer to the offer, acceptance and consideration of payment necessary to undertake the process of our assistance to the Client in the most appropriate manner for the express purpose of meeting the Client's needs in respect of provision of the Company's stated services, in accordance with and subject to, prevailing law of Netherlands. Any use of the above terminology or other words in the singular, plural, capitalization and/or he/she or they, are taken as interchangeable and therefore as referring to same.`),
    /* @__PURE__ */ React__default.createElement(Typography, { variant: "body1", paragraph: true }, "Cookies: We employ the use of cookies. By accessing AboutCSGO, you agreed to use cookies in agreement with AboutCSGO's Privacy Policy. Most interactive websites use cookies to let us retrieve the user's details for each visit. Cookies are used by our website to enable the functionality of certain areas to make it easier for people visiting our website. Some of our affiliate/advertising partners may also use cookies."),
    /* @__PURE__ */ React__default.createElement(Typography, { variant: "body1", paragraph: true }, "License: Unless otherwise stated, AboutCSGO and/or its licensors own the intellectual property rights for all material on AboutCSGO. All intellectual property rights are reserved. You may access this from AboutCSGO for your own personal use subjected to restrictions set in these terms and conditions.")
  ));
};
const __vite_glob_0_4 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: TermsOfService
}, Symbol.toStringTag, { value: "Module" }));
const ItemCategoryLayout = () => {
  const theme = useTheme();
  const { weaponName, skins } = usePage().props;
  const weaponNameTitle = weaponName ?? "Unknown Weapon";
  return /* @__PURE__ */ React__default.createElement(React__default.Fragment, null, /* @__PURE__ */ React__default.createElement(Head, null, /* @__PURE__ */ React__default.createElement("title", null, `All ${weaponNameTitle} Skins | AboutCSGO`), /* @__PURE__ */ React__default.createElement(
    "meta",
    {
      "head-key": "description",
      name: "description",
      content: `All the ${weaponNameTitle} skins and prices | AboutCSGO`
    }
  ), /* @__PURE__ */ React__default.createElement(
    "link",
    {
      "head-key": "canonical",
      rel: "canonical",
      href: `https://www.aboutcsgo.com/weapon/${weaponNameTitle}`
    }
  )), /* @__PURE__ */ React__default.createElement(
    Container,
    {
      style: { backgroundColor: theme.palette.background.default }
    },
    /* @__PURE__ */ React__default.createElement(Box, { sx: { padding: 2, justifyContent: "center" } }, /* @__PURE__ */ React__default.createElement(Typography, { variant: "h1", gutterBottom: true, align: "center" }, weaponName, " Skins"), /* @__PURE__ */ React__default.createElement(Grid, { container: true, spacing: 2 }, skins.map((item) => /* @__PURE__ */ React__default.createElement(Grid, { item: true, xs: 12, sm: 4, md: 4, key: item.id }, /* @__PURE__ */ React__default.createElement(
      Link,
      {
        href: `/skin/${weaponName}/${item.skin}`,
        style: {
          textDecoration: "none",
          color: "inherit"
        }
      },
      /* @__PURE__ */ React__default.createElement(
        Card,
        {
          sx: {
            height: "100%",
            display: "flex",
            flexDirection: "column"
          }
        },
        /* @__PURE__ */ React__default.createElement(CardContent, null, /* @__PURE__ */ React__default.createElement(
          Typography,
          {
            variant: "h6",
            component: "h1",
            gutterBottom: true,
            sx: { textAlign: "center" }
          },
          item.skin
        ), /* @__PURE__ */ React__default.createElement(
          Typography,
          {
            sx: {
              backgroundColor: item.quality_color,
              color: "#fff",
              padding: "4px 8px",
              borderRadius: "4px",
              textAlign: "center"
            },
            gutterBottom: true,
            align: "center"
          },
          item.quality
        ), item.stattrak === 1 && /* @__PURE__ */ React__default.createElement(
          Typography,
          {
            variant: "body2",
            align: "center",
            sx: {
              backgroundColor: "#F89406",
              color: "#fff",
              padding: "4px 8px",
              borderRadius: "4px",
              textAlign: "center",
              textShadow: "1px 1px 2px black, 0 0 1em black, 0 0 0.2em black"
            }
          },
          "Stattrak available"
        ), item.souvenir === 1 && /* @__PURE__ */ React__default.createElement(
          Typography,
          {
            variant: "body2",
            align: "center",
            sx: {
              backgroundColor: "#ffd900",
              color: "#fff",
              padding: "4px 8px",
              borderRadius: "4px",
              textAlign: "center",
              textShadow: "1px 1px 2px black, 0 0 1em black, 0 0 0.2em black"
            }
          },
          "Souvenir available"
        )),
        /* @__PURE__ */ React__default.createElement(
          Box,
          {
            sx: {
              flexGrow: 1,
              display: "flex",
              alignItems: "center",
              justifyContent: "center"
            }
          },
          /* @__PURE__ */ React__default.createElement(
            CardMedia,
            {
              component: "img",
              width: "100%",
              image: item.image_url,
              alt: item.skin,
              style: {
                backgroundColor: "transparent",
                objectFit: "cover"
              }
            }
          )
        ),
        /* @__PURE__ */ React__default.createElement(CardContent, { sx: { flexGrow: 0 } }, /* @__PURE__ */ React__default.createElement(
          Typography,
          {
            variant: "body2",
            align: "center",
            sx: {
              color: "#fff",
              padding: "4px 8px"
            }
          },
          "€",
          item.prices.normal.lowest,
          " - €",
          item.prices.normal.highest
        ), item.prices.stattrak ? /* @__PURE__ */ React__default.createElement(
          Typography,
          {
            variant: "body2",
            align: "center",
            sx: {
              color: "#F89406",
              padding: "4px 8px",
              marginTop: "0px"
            }
          },
          "€",
          item.prices.stattrak.lowest,
          " ",
          "- €",
          item.prices.stattrak.highest
        ) : item.prices.souvenir && /* @__PURE__ */ React__default.createElement(
          Typography,
          {
            variant: "body2",
            align: "center",
            sx: {
              color: "#ffd900",
              padding: "4px 8px",
              marginTop: "0px"
            }
          },
          "€",
          item.prices.souvenir.lowest,
          " ",
          "- €",
          item.prices.souvenir.highest
        ))
      )
    )))))
  ));
};
const __vite_glob_0_5 = /* @__PURE__ */ Object.freeze(/* @__PURE__ */ Object.defineProperty({
  __proto__: null,
  default: ItemCategoryLayout
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
      Link$1,
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
    id: "combined-knives",
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
    id: "combined-gloves",
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
            Link,
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
        /* @__PURE__ */ React__default.createElement(Box, { sx: { display: "flex", alignItems: "center" } }, /* @__PURE__ */ React__default.createElement(ItemSkinSearch, { compact: true }))
      )))
    )
  );
};
const Footer = () => {
  const theme = useTheme();
  return /* @__PURE__ */ React__default.createElement(
    Box,
    {
      sx: {
        backgroundColor: theme.palette.background.paper,
        color: "#fff",
        padding: "20px 0",
        textAlign: "center",
        marginTop: "auto"
        // Make sure it stays at the bottom
      }
    },
    /* @__PURE__ */ React__default.createElement(Container, null, /* @__PURE__ */ React__default.createElement(Typography, { variant: "body2", align: "center" }, "© ", (/* @__PURE__ */ new Date()).getFullYear(), " AboutCSGO. All rights reserved."), /* @__PURE__ */ React__default.createElement(Typography, { variant: "body2", align: "center" }, /* @__PURE__ */ React__default.createElement(Link$1, { href: "/privacy-policy", color: "inherit" }, "Privacy Policy"), " ", "|", " ", /* @__PURE__ */ React__default.createElement(Link$1, { href: "/terms-of-service", color: "inherit" }, "Terms of Service")))
  );
};
const Layout = ({ children }) => {
  const theme = useTheme();
  return /* @__PURE__ */ React__default.createElement(React__default.Fragment, null, /* @__PURE__ */ React__default.createElement(Head, null, /* @__PURE__ */ React__default.createElement("title", null, "AboutCSGO"), /* @__PURE__ */ React__default.createElement(
    "meta",
    {
      "head-key": "description",
      name: "description",
      content: "AboutCSGO home page where you can find all the prices and info about CS2 skins"
    }
  ), /* @__PURE__ */ React__default.createElement("link", { rel: "icon", type: "image/svg+xml", href: "/favicon.svg" })), /* @__PURE__ */ React__default.createElement("div", null, /* @__PURE__ */ React__default.createElement("header", null, /* @__PURE__ */ React__default.createElement(Header, null)), /* @__PURE__ */ React__default.createElement("main", null, /* @__PURE__ */ React__default.createElement(
    Container,
    {
      style: {
        backgroundColor: theme.palette.background.default,
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
  )), /* @__PURE__ */ React__default.createElement("footer", null, /* @__PURE__ */ React__default.createElement(Footer, null))));
};
createServer(
  (page) => createInertiaApp({
    page,
    render: ReactDOMServer.renderToString,
    resolve: (name) => {
      const pages = /* @__PURE__ */ Object.assign({ "./Pages/HomePage.tsx": __vite_glob_0_0, "./Pages/NotFoundPage.tsx": __vite_glob_0_1, "./Pages/PrivacyPolicyPage.tsx": __vite_glob_0_2, "./Pages/SkinPage.tsx": __vite_glob_0_3, "./Pages/TermsOfServicePage.tsx": __vite_glob_0_4, "./Pages/WeaponPage.tsx": __vite_glob_0_5 });
      let page2 = pages[`./Pages/${name}.tsx`];
      page2.default.layout = page2.default.layout || ((page3) => /* @__PURE__ */ React__default.createElement(Layout, null, page3));
      return page2;
    },
    setup: ({ App, props }) => (
      // <HelmetProvider>
      /* @__PURE__ */ React__default.createElement(ThemeProvider, { theme: darkTheme$1 }, /* @__PURE__ */ React__default.createElement(CssBaseline, null), /* @__PURE__ */ React__default.createElement(App, { ...props }))
    )
  })
);
