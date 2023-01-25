<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get("/login", function (Request $request) {
    // Esto sería el cliente
    $request->session()->put("state", $state = Str::random(40));
    $query = http_build_query([
        "client_id" => 4,
        "redirect_uri" => "http://localhost:8080/callback",
        "response_type" => "code",
        "scope" => "*",
        "state" => $state,
        "prompt" => true
    ]);

    return redirect("http://127.0.0.1:8000/oauth/authorize?" . $query);
});

Route::get('/callback', function (Request $request) {
    $state = $request->session()->pull("state");

    throw_unless(strlen($state) > 0 && $state == $request->state, InvalidArgumentException::class);
    $response = Http::asForm()->post(
        "http://127.0.0.1:8000/oauth/token",
        [
            "grant_type" => "authorization_code",
            "client_id" => 4,
            "client_secret" => "rHJqcjV2WGvogM5WItnBKVt2qMczP4Jsox5SUVCb",
            "redirect_uri" => "http://localhost:8080/callback",
            "code" => $request->code
        ]
    );
});
