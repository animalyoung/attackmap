<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class WorldMapController extends Controller
{
  public function index(){
    $attackjson = Storage::disk('local')->get('public/example.json');
    $attackraw = json_decode($attackjson);
    //Log::info(var_dump($attackraw));
    //var_dump($attackraw);
    $attackarray = $attackraw->attack;
   return view('worldmap.index', ['attacks' => $attackarray]);
  }
}
