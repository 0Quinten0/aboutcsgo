<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestDMarketApi extends Command
{
    protected $signature = 'dmarket:test';
    protected $description = 'Test interaction with DMarket API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Testing DMarket API...');

        // Test GET request to fetch user offers
        $this->testGetUserOffers();

        // Optionally, test the POST request as well
        // $this->testPostRequest();
    }

    private function testGetUserOffers()
    {
        $publicKey = '6b42f4a9f1fd93dc5728dbfd50bfaf00cb21129030231c45fbe05e6c18bca302';
        $secretKey = 'cd593caaf6c10f65e5e0d4e82e694e2507d557598c66e80a3c1d7db144a4d8f86b42f4a9f1fd93dc5728dbfd50bfaf00cb21129030231c45fbe05e6c18bca302';
        $timestamp = now()->timestamp;
        $method = 'GET';
        $url = '/marketplace-api/v1/user-offers?GameID=a8db&Status=OfferStatusActive&SortType=UserOffersSortTypeDefault';

        // Generate signature for GET request
        $headers = [
            'X-Api-Key' => $publicKey,
            'X-Request-Sign' => $this->generateSignature($secretKey, $method, $url, $timestamp),
            'X-Sign-Date' => $timestamp,
            'Content-Type' => 'application/json'
        ];
        $this->info('Request Headers: ' . json_encode($headers));

        // Make the GET request
        $response = Http::withHeaders($headers)
            ->timeout(60)
            ->get('https://api.dmarket.com' . $url);

        // Output response
        $this->info('GET Request Response Status: ' . $response->status());
        $this->info('GET Request Response Body: ' . $response->body());
    }

    private function generateSignature($privateKey, $method, $route, $timestamp, array $postParams = [])
    {
        $text = $method . $route . (!empty($postParams) ? json_encode($postParams) : '') . $timestamp;
        return 'dmar ed25519 ' . sodium_bin2hex(sodium_crypto_sign_detached($text, sodium_hex2bin($privateKey)));
    }
}
