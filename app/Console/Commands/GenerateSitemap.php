<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Item;
use App\Models\Skin;
use App\Models\ItemSkin;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap XML file.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $baseUrl = 'https://aboutcsgo.com'; // Ensure this is set in your .env file
        $sitemapPath = 'sitemap.xml'; // The path where the sitemap will be saved

        // Start building the XML content
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');

        // Add the home page to the sitemap
        $this->addUrl($xml, $baseUrl, '1.0', 'daily');

        // Add manual URLs
        $manualUrls = [
            '/',
            '/terms-of-service',
            '/privacy-policy',
            '/level-calculator',
            '/csgo-gambling-sites',            
            // Add other static URLs as needed
        ];

        foreach ($manualUrls as $manualUrl) {
            $this->addUrl($xml, "{$baseUrl}{$manualUrl}", '0.5', 'monthly');
        }

        // Fetch items from the database and create URLs for each item
        $items = Item::all();
        foreach ($items as $item) {
            $itemName = Str::slug($item->name);
            $itemUrl = "{$baseUrl}/weapon/{$itemName}";
            $this->addUrl($xml, $itemUrl, '0.8', 'weekly');
        }

        // Fetch item-skin combinations and create URLs for each
        $itemSkins = ItemSkin::all();
        foreach ($itemSkins as $itemSkin) {
            $item = $itemSkin->item;  // Assuming you have defined the relationship
            $skin = $itemSkin->skin;  // Assuming you have defined the relationship
            if ($item && $skin) {
                $itemName = Str::slug($item->name);
                $skinName = Str::slug($skin->name);
                $skinUrl = "{$baseUrl}/skin/{$itemName}/{$skinName}";
                $this->addUrl($xml, $skinUrl, '0.8', 'weekly');
            }
        }

        // Save the XML to a file
        $xmlContent = $xml->asXML();
        Storage::disk('public')->put($sitemapPath, $xmlContent);

        $this->info("Sitemap generated and saved to storage/app/public/{$sitemapPath}");

        return 0;
    }

    /**
     * Helper method to add a URL to the XML sitemap.
     *
     * @param \SimpleXMLElement $xml
     * @param string $url
     * @param string $priority
     * @param string $changeFrequency
     */
    protected function addUrl(\SimpleXMLElement $xml, string $url, string $priority, string $changeFrequency)
    {
        $urlElement = $xml->addChild('url');
        $urlElement->addChild('loc', htmlspecialchars($url));
        $urlElement->addChild('changefreq', $changeFrequency);
        $urlElement->addChild('priority', $priority);
    }
}
