<?php

namespace App\Imports;

use App\Models\Projet;
use App\Models\DetailProjet;
use App\Models\Partenaire;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProjetImport implements ToModel, SkipsEmptyRows, WithHeadingRow, SkipsOnFailure, WithValidation
{
    use Importable, SkipsFailures;

    private $partenaires = [];

    public function __construct()
    {
        // Charger les partenaires en cache
        $this->partenaires = Partenaire::pluck('id', 'nom')->toArray();
    }

    public function model(array $row)
    {
        try {
            DB::beginTransaction();

            $created_at = now();

            // 1. Création du projet
            $projet = $this->createProjet($row, $created_at);

            // 2. Traitement du partenaire
            $partenaireId = $this->getPartenaireId($row);

            // 3. Création des détails du projet
            $this->createDetailProjet($projet->id, $partenaireId, $row, $created_at);

            DB::commit();

            return $projet;

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Crée un nouveau projet
     */
    private function createProjet($row, $created_at)
    {
        $intituleResume = $row['intitule_resume'] ?? $row['intitule_resume'] ?? '';
        $intituleComplet = $row['intitule_complet'] ?? $intituleResume;

        return Projet::create([
            'intitule_resume' => $intituleResume,
            'intitule_resume_fr' => $intituleResume,
            'intitule_complet' => $intituleComplet,
            'intitule_complet_fr' => $intituleComplet,
            'created_at' => $created_at,
            'updated_at' => $created_at
        ]);
    }

    /**
     * Récupère l'ID du partenaire
     */
    private function getPartenaireId($row)
    {
        $partenaireNom = $row['partenaire'] ?? '';
        
        if (empty($partenaireNom)) {
            return null;
        }

        // Chercher le partenaire par son nom
        if (isset($this->partenaires[$partenaireNom])) {
            return $this->partenaires[$partenaireNom];
        }

        // Si le partenaire n'existe pas, on le crée
        $partenaire = Partenaire::create([
            'nom' => $partenaireNom,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->partenaires[$partenaireNom] = $partenaire->id;
        
        return $partenaire->id;
    }

    /**
     * Crée les détails du projet
     */
    private function createDetailProjet($projetId, $partenaireId, $row, $created_at)
    {
        // Calcul du nombre total de sites
        $nbSitesEcole = (int) ($row['nb_sites_ecole'] ?? 0);
        $nbSitesSante = (int) ($row['nb_sites_sante'] ?? 0);
        $nbSitesAutre = (int) ($row['nb_sites_autre'] ?? 0);
        

        $nbSitesTotal = $nbSitesEcole + $nbSitesSante + $nbSitesAutre;

        return DetailProjet::create([
            'projet_id' => $projetId,
            'partenaire_id' => $partenaireId,
            'periode_mise_en_oeuvre' => $row['periode'] ?? null,
            'nb_localites_atpc' => $row['nb_localites_atpc'] ?? 0,
            'nb_localites_suivi_post_fdal' => 0, // Non présent dans le fichier, valeur par défaut
            'nb_latrines_familiales_subvention' => $row['nb_latines_familiales_subvention'] ?? 0,
            'nb_sites_construction_latrines' => $nbSitesTotal,
            'nb_sites_ecole' => $nbSitesEcole,
            'nb_sites_centre_sante' => $nbSitesSante,
            'nb_sites_autre' => $nbSitesAutre,
            'nb_blocs_2_cabines' => $row['nb_blocs_2_cabines'] ?? 0,
            'nb_blocs_3_cabines' => $row['nb_blocs_3_cabines'] ?? 0,
            'nb_blocs_4_cabines' => $row['nb_blocs_4_cabines'] ?? 0,
            'created_at' => $created_at,
            'updated_at' => $created_at
        ]);
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'intitule_resume' => 'required|string',
            'intitule_complet' => 'nullable|string',
            'partenaire' => 'nullable|string',
            'periode' => 'nullable|string',
            'nb_localites_atpc' => 'nullable|integer',
            'nb_latines_familiales_subvention' => 'nullable|integer',
            'nb_sites_ecole' => 'nullable|integer',
            'nb_sites_sante' => 'nullable|integer',
            'nb_blocs_2_cabines' => 'nullable|integer',
            'nb_blocs_3_cabines' => 'nullable|integer',
            'nb_blocs_4_cabines' => 'nullable|integer',
        ];
    }

    /**
     * Noms des colonnes personnalisés pour les messages d'erreur
     */
    public function customValidationAttributes()
    {
        return [
            'intitule_resume' => 'Intitulé résumé',
            'intitule_complet' => 'Intitulé complet',
            'partenaire' => 'Partenaire',
            'periode' => 'Période',
            'nb_localites_atpc' => 'Nombre de localités ATPC',
            'nb_latines_familiales_subvention' => 'Nombre de latrines familiales à subventionner',
            'nb_sites_colle' => 'Nombre de sites écoles',
            'nb_sites_sante' => 'Nombre de sites centres de santé',
            'nb_blocs_2_cabines' => 'Nombre de blocs à 2 cabines',
            'nb_blocs_3_cabines' => 'Nombre de blocs à 3 cabines',
            'nb_blocs_4_cabines' => 'Nombre de blocs à 4 cabines',
        ];
    }
}