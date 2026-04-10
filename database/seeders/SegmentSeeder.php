<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Segment;

/**
 * SegmentSeeder
 *
 * Crée les segments de segmentation clientèle des membres.
 * Contextualisé pour le Burkina Faso et l'Afrique de l'Ouest.
 *
 * RÈGLE : Le segment "NON CLASSÉ" (is_default=true) DOIT toujours exister.
 * C'est le segment affecté par défaut à tout nouveau membre non encore classé.
 * Il ne peut pas être supprimé depuis l'interface admin.
 *
 * La table segments doit exister avant les membres (ordre dans DatabaseSeeder).
 */
class SegmentSeeder extends Seeder
{
    public function run(): void
    {
        $segments = [
            // ─── Segment par défaut (OBLIGATOIRE — ne pas supprimer) ──────────
            [
                'nom'        => 'NON CLASSÉ',
                'slug'       => 'non-classe',
                'description'=> 'Segment par défaut pour les membres non encore classifiés. Attribué automatiquement à la création du compte.',
                'couleur'    => '#6b7280',    // Gris neutre
                'icone'      => 'bi bi-person-dash',
                'is_default' => true,
                'actif'      => true,
            ],

            // ─── Segments sociaux & académiques ──────────────────────────────
            [
                'nom'        => 'Étudiant',
                'slug'       => 'etudiant',
                'description'=> 'Étudiants des universités, grandes écoles et instituts de formation (UGANC, USTA, INPHB, 2IE…).',
                'couleur'    => '#3b82f6',    // Bleu
                'icone'      => 'bi bi-mortarboard',
                'is_default' => false,
                'actif'      => true,
            ],
            [
                'nom'        => 'Fonctionnaire',
                'slug'       => 'fonctionnaire',
                'description'=> 'Agents de la fonction publique, militaires, forces de l\'ordre, enseignants du public.',
                'couleur'    => '#0891b2',    // Bleu canard
                'icone'      => 'bi bi-person-badge',
                'is_default' => false,
                'actif'      => true,
            ],
            [
                'nom'        => 'Retraité',
                'slug'       => 'retraite',
                'description'=> 'Personnes à la retraite (anciens fonctionnaires, anciens salariés du privé).',
                'couleur'    => '#7c3aed',    // Violet
                'icone'      => 'bi bi-person-check',
                'is_default' => false,
                'actif'      => true,
            ],

            // ─── Segments économiques ─────────────────────────────────────────
            [
                'nom'        => 'Commerçant',
                'slug'       => 'commercant',
                'description'=> 'Commerçants du secteur formel ou semi-formel : boutiquiers, marchands, grossistes, importateurs/exportateurs.',
                'couleur'    => '#f59e0b',    // Ambre
                'icone'      => 'bi bi-shop',
                'is_default' => false,
                'actif'      => true,
            ],
            [
                'nom'        => 'Artisan',
                'slug'       => 'artisan',
                'description'=> 'Artisans et petits producteurs : tailleurs, menuisiers, mécaniciens, maçons, coiffeurs, tisserands…',
                'couleur'    => '#d97706',    // Orange foncé
                'icone'      => 'bi bi-tools',
                'is_default' => false,
                'actif'      => true,
            ],
            [
                'nom'        => 'Entreprise Informelle',
                'slug'       => 'entreprise-informelle',
                'description'=> 'Micro-entrepreneurs du secteur informel : activités génératrices de revenus sans statut juridique formel.',
                'couleur'    => '#ea580c',    // Orange
                'icone'      => 'bi bi-basket',
                'is_default' => false,
                'actif'      => true,
            ],
            [
                'nom'        => 'Entreprise Privée',
                'slug'       => 'entreprise-privee',
                'description'=> 'Salariés et dirigeants d\'entreprises privées formelles (SARL, SA, SAS…) immatriculées au RCCM.',
                'couleur'    => '#16a34a',    // Vert
                'icone'      => 'bi bi-building',
                'is_default' => false,
                'actif'      => true,
            ],

            // ─── Segments communautaires & institutionnels ────────────────────
            [
                'nom'        => 'Communauté Religieuse',
                'slug'       => 'communaute-religieuse',
                'description'=> 'Membres issus de communautés religieuses : paroisses, mosquées, fraternités, groupements de prière…',
                'couleur'    => '#9333ea',    // Violet clair
                'icone'      => 'bi bi-heart',
                'is_default' => false,
                'actif'      => true,
            ],
            [
                'nom'        => 'Association',
                'slug'       => 'association',
                'description'=> 'Membres de groupements associatifs (associations de développement, groupements féminins, GIE…).',
                'couleur'    => '#0d9488',    // Teal
                'icone'      => 'bi bi-people',
                'is_default' => false,
                'actif'      => true,
            ],
            [
                'nom'        => 'ONG',
                'slug'       => 'ong',
                'description'=> 'Personnel et bénévoles d\'Organisations Non Gouvernementales nationales et internationales.',
                'couleur'    => '#dc2626',    // Rouge
                'icone'      => 'bi bi-globe-americas',
                'is_default' => false,
                'actif'      => true,
            ],
            [
                'nom'        => 'Diaspora',
                'slug'       => 'diaspora',
                'description'=> 'Burkinabè résidant à l\'étranger (Europe, Amérique, Asie…) souhaitant contribuer au développement de leur pays.',
                'couleur'    => '#1d4ed8',    // Bleu foncé
                'icone'      => 'bi bi-airplane',
                'is_default' => false,
                'actif'      => true,
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($segments as $data) {
            $existing = Segment::where('slug', $data['slug'])->first();
            if ($existing) {
                $existing->update($data);
                $updated++;
            } else {
                Segment::create($data);
                $created++;
            }
        }

        $this->command->info("✅ Segments : {$created} créé(s), {$updated} mis à jour.");
        $this->command->info('   → Segment par défaut : NON CLASSÉ');
        $this->command->info('   → Segments disponibles : ' . count($segments) . ' au total');
    }
}
