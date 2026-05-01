<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EcranDemarrageController extends Controller
{
    public function index()
    {
        return view('ecran-demarrage');
    }
}