<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OneMapController extends Controller
{
    public function index(){
      $content = require base_path('resources/views/onemap/index.html');
      return $content;

    }
}
