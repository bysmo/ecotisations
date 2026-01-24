<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    /**
     * Afficher la liste des templates
     */
    public function index()
    {
        $templates = EmailTemplate::orderBy('type', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('email-templates.index', compact('templates'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('email-templates.create');
    }

    /**
     * Enregistrer un nouveau template
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:email_templates,nom',
            'sujet' => 'required|string|max:255',
            'corps' => 'required|string',
            'type' => 'required|in:paiement,engagement,autre',
            'actif' => 'boolean',
        ]);

        $validated['actif'] = $request->has('actif') ? true : false;

        EmailTemplate::create($validated);

        return redirect()->route('email-templates.index')
            ->with('success', 'Template créé avec succès.');
    }

    /**
     * Afficher les détails d'un template
     */
    public function show(EmailTemplate $emailTemplate)
    {
        return view('email-templates.show', compact('emailTemplate'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        return view('email-templates.edit', compact('emailTemplate'));
    }

    /**
     * Mettre à jour un template
     */
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255|unique:email_templates,nom,' . $emailTemplate->id,
            'sujet' => 'required|string|max:255',
            'corps' => 'required|string',
            'type' => 'required|in:paiement,engagement,autre',
            'actif' => 'boolean',
        ]);

        $validated['actif'] = $request->has('actif') ? true : false;

        $emailTemplate->update($validated);

        return redirect()->route('email-templates.index')
            ->with('success', 'Template mis à jour avec succès.');
    }

    /**
     * Supprimer un template
     */
    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();

        return redirect()->route('email-templates.index')
            ->with('success', 'Template supprimé avec succès.');
    }
}
