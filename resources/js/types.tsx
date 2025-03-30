// types.ts

export interface Item {
    id: number;
    name: string;
}

export interface Subcategory {
    label: string;
    items: Item[];
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
