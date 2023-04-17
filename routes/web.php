<?php

use Illuminate\Support\Facades\Route;

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
Route::get('/repositories', function()
{
    $curl = curl_init();

    $id = \App\Models\Repository::orderBy('id','desc')->first()->id ?? 0;

    curl_setopt_array($curl, 
        [
            CURLOPT_URL => 'https://api.github.com/repositories?since='.$id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer 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',
                'Accept: application/vnd.github+json',
                'X-GitHub-Api-Version: 2022-11-28',
                'User-agent: *'
            ],
        ]
    );

    $response = curl_exec($curl);
    $error = curl_error($curl);

    $response = json_decode($response);

    curl_close($curl);


    // for ($i=$count; $i < count($response); $i++) { 
    foreach ($response as $key) {
        $r = \App\Models\Repository::firstOrNew(['repo_id'=>$key->id]);
        $r->repo_id = $key->id;
        $r->name = $key->full_name;
        $r->save();
    }

    return response()->json($response);
});