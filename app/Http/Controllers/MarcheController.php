<?php

namespace App\Http\Controllers;

use App\Models\Marche;
use App\Models\Projet;
use App\Models\Operateur;
use App\Models\Wilaya;
use App\Models\Partenaire;
use Illuminate\Http\Request;

class MarcheController extends Controller
{
    public function index()
    {
        $marches = Marche::with(['projet', 'operateur', 'wilayas', 'partenaires'])->get();
        return view('admin.marches.index', compact('marches'));
    }

    public function show($id)
    {
        // Charger toutes les relations nécessaires
        $marche = Marche::with([
            'projet', 
            'operateur', 
            'wilayas', 
            'partenaires'
        ])->findOrFail($id);
        
        return view('admin.marches.show', compact('marche'));
    }
}