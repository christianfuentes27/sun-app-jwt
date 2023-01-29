<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends Controller
{
    function login(Request $request) {
        $users = [
            'christian@gmail.com' => 'christian',
            'test@gmail.com' => 'test',
            'test2@gmail.com' => 'test2'
        ];
        $user = $request->user;
        $password = $request->password;
        if (isset($users[$user]) && $users[$user] == $password) {
            $key = $_ENV['TOKEN_SECRET'];
            $payload = [
                'user' => $user,
                'expiration_date' => Carbon::now()->addHour()
            ];
            $jwt = JWT::encode($payload, $key, 'HS256');
            return response()->json(['token' => $jwt]);
        }
        return response()->json(['message' => 'User not valid'], 401);
    }
    
    function getdata() {
        $lat = '37.16147109102704';
        $lng = '-3.5912354132361344';
        $date = Carbon::now()->format('Y-m-d');
        $url = sprintf('https://api.sunrise-sunset.org/json?lat=%s&lng=%s&date=%s', $lat, $lng, $date);
        
        $response = Http::get($url);
        
        $sunData = $response->json();
        $sunset = $sunData['results']['sunset'];
        $sunrise = $sunData['results']['sunrise'];
        
        return ['sunrise' => $sunrise, 'sunset' => $sunset];
    }
    
    // x0 = sunrise
    // x = current
    // x1 = sunset
    
    // y0 = -pi/2
    // y = 
    // y1 = pi/2
         
    //       (pi/2 + pi/2) (current - sunrise)
    // y = ------------------------------------ -pi/2     
    //               sunset - sunrise
    
    function interpolate() {
        $suntime = $this->getdata();
        $sunrise = Carbon::parse($suntime['sunrise'])->format('H:i:s');
        $sunset = Carbon::parse($suntime['sunset'])->format('H:i:s');
        $currentTime = Carbon::now()->format('H:i:s');
        $interpolateTime = ((pi()/2 + pi()/2)*(Carbon::parse($currentTime)->diffInSeconds(Carbon::parse($sunrise))))
                            / (Carbon::parse($sunset)->diffInSeconds(Carbon::parse($sunrise))) - pi()/2;
        $cos = cos($interpolateTime);
        $sin = sin($interpolateTime);
        return response()->json([
            'sunrise' => $sunrise, 
            'sunset' => $sunset, 
            'current' => $currentTime, 
            'interpolate' => $interpolateTime, 
            'cos' => $cos,
            'sin' => $sin,
            'sensor1' => rand(0, 100) / 100,
            'sensor2' => rand(0, 100) / 100,
            'sensor3' => rand(0, 100) / 100,
            'sensor4' => rand(0, 100) / 100
        ]);
    }
}
