<?php

namespace App\Http\Controllers;

use App\Models\Localite;
use App\Models\Moughata;
use App\Models\Operation;
use App\Models\Wilaya;

class WelcomeController extends Controller
{
    public function index()
    {
        $stats = [
            'wilayas' => Wilaya::count(),
            'moughatas' => Moughata::count(),
            'localites' => Localite::count(),
            'types_operations' => Operation::count(),
        ];

        return view('welcome', compact('stats'));
    }
}
