<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\User; // Make sure to import your User model
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{public function handleSteamCallback(Request $request)
    {

        $steamID = $request->input('steamID');
    
    
        $apiKey = env('STEAM_API_KEY');
        $steamApiUrl = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$apiKey}&steamids={$steamID}";
    
        $client = new Client();
        
        try {
            $response = $client->request('GET', $steamApiUrl);
            $body = $response->getBody();
            $data = json_decode($body);
    
            if ($data && !empty($data->response->players)) {
                $player = $data->response->players[0];
    
                // Log the Steam ID and player data
                Log::info('Steam API Response Data:', [
                    'steamID' => $steamID,
                    'playerData' => (array) $player,
                ]);
                
                // Find or create a user based on the Steam ID
                $user = User::updateOrCreate(
                    ['steam_id' => $player->steamid],
                    [
                        'nickname' => $player->personaname,
                        'profile_url' => isset($player->profileurl) ? $player->profileurl : null,
                        'avatar' => isset($player->avatarfull) ? $player->avatarfull : null,
                        // Add any other necessary fields
                    ],
                );
    
                $token = $user->createToken('AboutCSGO')->plainTextToken;
    
                return response()->json([
                    'success' => true,
                    'user' => [
                        'steamID' => $user->steam_id,
                        'profileName' => $user->nickname,
                        'profileImageUrl' => $user->profile_url,
                        'avatar' => $user->avatar,
                        // Include other user details as needed, e.g., balance
                        'id' => $user->id,
                    ],
                    'token' => $token, // Return the token in the response
                ]);
            } else {
                Log::error('Failed to retrieve user data from Steam:', ['response' => $data]);
                return response()->json(['success' => false, 'message' => 'Failed to retrieve user data from Steam'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Failed to connect to Steam API:', ['exception' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to connect to Steam API'], 500);
        }
    }
    
}
