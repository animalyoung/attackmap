<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KasperskyController extends Controller
{
  public function index()
  {
    return view('kaspersky_iframe.index');
  }
}
