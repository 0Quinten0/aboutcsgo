<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function getItemSkins($item_name)
    {
        // Find the item by its name
        $item = Item::where('name', $item_name)->firstOrFail();

        // Retrieve the item skins for the found item, with the related skin and quality names
        $itemSkins = $item->itemSkins()->with(['skin', 'quality'])->get();

        // Transform the data to include the name values instead of IDs
        $itemSkinsTransformed = $itemSkins->map(function ($itemSkin) {
            return [
                'id' => $itemSkin->id,
                'item_id' => $itemSkin->item->name,
                'skin' => $itemSkin->skin->name,
                'quality' => $itemSkin->quality->name,
                'stattrak' => $itemSkin->stattrak,
                'souvenir' => $itemSkin->souvenir,
                'description' => $itemSkin->description,
                'image_url' => $itemSkin->image_url,
                'created_at' => $itemSkin->created_at,
                'updated_at' => $itemSkin->updated_at,
            ];
        });

        return response()->json($itemSkinsTransformed);
    }
    public function getItemSkin($item_name, $skin_name)
    {
        // Find the item by its name
        $item = Item::where('name', $item_name)->firstOrFail();

        // Retrieve the specific item skin for the found item, with the related skin and quality names
        $itemSkin = $item->itemSkins()
            ->whereHas('skin', function ($query) use ($skin_name) {
                $query->where('name', $skin_name);
            })
            ->with(['skin', 'quality'])
            ->firstOrFail();

        // Transform the data to include the name values instead of IDs
        $itemSkinTransformed = [
            'id' => $itemSkin->id,
            'item_id' => $itemSkin->item->name,
            'skin' => $itemSkin->skin->name,
            'quality' => $itemSkin->quality->name,
            'stattrak' => $itemSkin->stattrak,
            'souvenir' => $itemSkin->souvenir,
            'description' => $itemSkin->description,
            'image_url' => $itemSkin->image_url,
            'created_at' => $itemSkin->created_at,
            'updated_at' => $itemSkin->updated_at,
        ];

        return response()->json($itemSkinTransformed);
    }
}

