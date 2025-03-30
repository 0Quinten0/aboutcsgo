// types.ts
export interface Item {
    id: number;
    name: string;
    item_id: string;
    skin: string;
    quality: string;
    quality_color: string;
    stattrak: number;
    souvenir?: number;
    image_url: string;
    description: string;
    prices: Prices;
}

export interface DropdownItem {
    id: number;
    name: string;
    category_id: number;
}

interface Prices {
    normal: PriceRange;
    stattrak: PriceRange | null;
    souvenir: PriceRange;
}

interface PriceRange {
    lowest: string;
    highest: string;
}

export interface Subcategory {
    label: string;
    items: DropdownItem[];
}

export interface CombinedCategory {
    id: string | number; // ✅ Allow both string and number
    name: string;
    subcategory: Subcategory[];
}

export interface HeaderProps {
    categories: CombinedCategory[];
}

export interface PopularItem {
    id: number;
    item_name: string;
    skin_name: string;
    quality: string;
    quality_color: string;
    image_url: string;
    view_count: number;
}

export interface PageProps {
    weaponName: string;
    skins: Item[];
    [key: string]: any; // Allow additional props from Inertia
}

export interface MarketplaceData {
    pretty_name: string; // Pretty name of the marketplace (e.g., "BitSkins")
    image_url: string; // URL to the marketplace image
}

// Extend MarketplacePrices to include marketplace metadata
export interface MarketplacePricesWithDetails extends MarketplaceData {
    normal?: PriceCategory;
    stattrak?: PriceCategory;
    souvenir?: PriceCategory;
}

// Update PricesList to map marketplace names to MarketplacePricesWithDetails
export interface PricesListWithDetails {
    [marketplace: string]: MarketplacePricesWithDetails;
}

// Skin data interface that includes the updated PricesListWithDetails
export interface SkinData {
    id: number;
    item_id: string;
    category: string;
    skin: string;
    quality: string;
    quality_color: string;
    stattrak: number;
    souvenir: number;
    description: string;
    image_url: string;
    created_at: string;
    updated_at: string;
    prices: PricesListWithDetails; // Updated to hold marketplace details
    user_votes: number | null;
}

export interface PriceCategory {
    [condition: string]: string; // Maps condition (e.g., "Field-Tested") to the price (e.g., "0.64")
}
