<?php

namespace App\Http\Controllers;

use App\Models\Membre;
use App\Models\Segment;
use Illuminate\Http\Request;

class SegmentController extends Controller
{
    /**
     * Afficher la liste des segments
     */
    public function index(Request $request)
    {
        // Récupérer tous les segments de la table segments
        $query = Segment::query();
        
        // Recherche si fournie
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nom', 'like', "%{$search}%");
        }
        
        $segments = $query->orderBy('nom')->get();
        
        // Le champ segment a été retiré de la table membres
        foreach ($segments as $segment) {
            $segment->nombre_membres = 0;
        }
        
        $membresSansSegment = Membre::count();
        
        return view('segments.index', compact('segments', 'membresSansSegment'));
    }
    
    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('segments.create');
    }
    
    /**
     * Enregistrer un nouveau segment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:segments,nom',
            'description' => 'nullable|string',
        ], [
            'nom.required' => 'Le nom du segment est obligatoire.',
            'nom.unique' => 'Ce segment existe déjà.',
        ]);

        Segment::create($validated);

        return redirect()->route('segments.index')
            ->with('success', 'Segment créé avec succès.');
    }
    
    /**
     * Afficher les membres d'un segment
     */
    public function show(Request $request, $segment)
    {
        $segmentNom = urldecode($segment);
        
        // Le champ segment a été retiré de la table membres
        $membres = new \Illuminate\Pagination\LengthAwarePaginator(
            [],
            0,
            15,
            \Illuminate\Pagination\Paginator::resolveCurrentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
        
        return view('segments.show', compact('segmentNom', 'membres'));
    }
}
