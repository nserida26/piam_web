<?php

namespace App\Imports;

use App\Models\Operateur;
use App\Models\Marche;
use App\Models\Projet;
use App\Models\Partenaire;
use App\Models\Wilaya;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class OperateurImport implements ToModel, SkipsEmptyRows, WithHeadingRow, SkipsOnFailure, WithValidation
{
    use Importable, SkipsFailures;

    private $operateurs = [];
    private $projets = [];
    private $partenaires = [];
    private $wilayas = [];

    public function __construct()
    {
        // Charger les données existantes en cache pour éviter des requêtes répétitives
        $this->operateurs = Operateur::pluck('id', 'intitule')->toArray();
        $this->projets = Projet::pluck('id', 'intitule_resume')->toArray();
        $this->partenaires = Partenaire::pluck('id', 'nom')->toArray();
        $this->wilayas = Wilaya::pluck('id', 'nom')->toArray();
    }

    public function model(array $row)
    {
        try {
            DB::beginTransaction();

            $created_at = now();

            // 1. Traitement de l'opérateur
            $operateurId = $this->getOrCreateOperateur($row, $created_at);

            // 2. Traitement du projet
            $projetId = $this->getOrCreateProjet($row, $created_at);

            // 3. Création du marché
            $marche = $this->createMarche($row, $operateurId, $projetId, $created_at);

            // 4. Traitement des partenaires (sources de financement)
            $this->attachPartenaires($marche->id, $row, $created_at);

            // 5. Traitement des wilayas
            $this->attachWilayas($marche->id, $row, $created_at);

            DB::commit();

            return $marche;

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Récupère ou crée un opérateur
     */
    private function getOrCreateOperateur($row, $created_at)
    {
        $intituleOperateur = $row['operateur'] ?? null;
        
        if (!$intituleOperateur) {
            throw new \Exception("Le champ opérateur est requis");
        }

        if (!isset($this->operateurs[$intituleOperateur])) {
            $operateur = Operateur::create([
                'intitule' => $intituleOperateur,
                'intitule_fr' => $intituleOperateur,
                'type_struct' => $row['type'] ?? '',
                'created_at' => $created_at,
                'updated_at' => $created_at
            ]);
            
            $this->operateurs[$intituleOperateur] = $operateur->id;
            return $operateur->id;
        }

        return $this->operateurs[$intituleOperateur];
    }

    /**
     * Récupère ou crée un projet
     */
    private function getOrCreateProjet($row, $created_at)
    {
        $intituleResume = $row['intitule_resume'] ?? null;
        
        if (!$intituleResume) {
            throw new \Exception("Le champ intitule_resume est requis");
        }

        if (!isset($this->projets[$intituleResume])) {
            $projet = Projet::create([
                'intitule_resume' => $intituleResume,
                'intitule_complet' => $intituleResume,
                'created_at' => $created_at,
                'updated_at' => $created_at
            ]);
            
            $this->projets[$intituleResume] = $projet->id;
            return $projet->id;
        }

        return $this->projets[$intituleResume];
    }

    /**
     * Crée un marché
     */
    private function createMarche($row, $operateurId, $projetId, $created_at)
    {
        return Marche::create([
            'num' => $row['numlot'] ?? '',
            'lot' => $row['numlot'] ?? '',
            'date_sign' => $this->formatDate($row['date_sig'] ?? null),
            'duree' => $row['duree'] ?? '',
            'montant' => $this->parseMontant($row['montant'] ?? 0),
            'nb_loc_adecl' => $row['nb_loc_adecl'] ?? 0,
            'nb_loc_asuiv_postfdal' => $row['nb_loc_asui_v_postfdal'] ?? 0,
            'nb_latfam_asubv' => $row['nb_latlam_a_subv'] ?? '0',
            'nb_site_ims' => $row['nb_cab_a_const'] ?? 0,
            'nb_autre_cible' => $row['nb_cab_a_ehab12'] ?? 0,
            'nb_bloc_a_const' => $row['nb_cab_a_const'] ?? 0,
            'nb_cab_a_const' => $row['nb_cab_a_const'] ?? 0,
            'nb_cab_a_rehab' => $row['nb_cab_a_ehab12'] ?? 0,
            'operateur_id' => $operateurId,
            'projet_id' => $projetId,
            'created_at' => $created_at,
            'updated_at' => $created_at
        ]);
    }

    /**
     * Attache les partenaires au marché
     */
    private function attachPartenaires($marcheId, $row, $created_at)
    {
        $partenaireNom = $row['partenaire'] ?? null;
        
        if ($partenaireNom && isset($this->partenaires[$partenaireNom])) {
            DB::table('marche_partenaires')->insert([
                'partenaire_id' => $this->partenaires[$partenaireNom],
                'marche_id' => $marcheId,
                'created_at' => $created_at,
                'updated_at' => $created_at
            ]);
        }
    }

    /**
     * Attache les wilayas au marché
     */
    private function attachWilayas($marcheId, $row, $created_at)
    {
        $wilayaNom = $row['wilaya'] ?? null;
        
        if ($wilayaNom && isset($this->wilayas[$wilayaNom])) {
            DB::table('marche_wilayas')->insert([
                'wilaya_id' => $this->wilayas[$wilayaNom],
                'marche_id' => $marcheId,
                'created_at' => $created_at,
                'updated_at' => $created_at
            ]);
        }
    }

    /**
     * Formate une date au format MySQL
     */
    private function formatDate($date)
    {
        if (empty($date)) {
            return null;
        }

        // Si c'est un numéro de série Excel
        if (is_numeric($date)) {
            $unix = ($date - 25569) * 86400;
            return date('Y-m-d', $unix);
        }

        // Essayer de parser la date
        try {
            return date('Y-m-d', strtotime($date));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse le montant (supprime les espaces, convertit en nombre)
     */
    private function parseMontant($montant)
    {
        if (is_numeric($montant)) {
            return (float) $montant;
        }

        // Supprimer les espaces et convertir les virgules en points
        $montant = preg_replace('/[^0-9,.-]/', '', (string) $montant);
        $montant = str_replace(',', '.', $montant);
        
        return (float) $montant;
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'operateur' => 'required|string',
            'intitule_resume' => 'required|string',
            'partenaire' => 'nullable|string',
            'numlot' => 'nullable|string',
            'date_sig' => 'nullable',
            'duree' => 'nullable|string',
            'montant' => 'nullable|numeric',
            'wilaya' => 'nullable|string',
            'nb_loc_adecl' => 'nullable|integer',
            'nb_loc_asui_v_postfdal' => 'nullable|integer',
            'nb_latlam_a_subv' => 'nullable|string',
            'nb_cab_a_const' => 'nullable|integer',
            'nb_cab_a_ehab12' => 'nullable|integer',
        ];
    }

    /**
     * Noms des colonnes personnalisés pour les messages d'erreur
     */
    public function customValidationAttributes()
    {
        return [
            'operateur' => 'Opérateur',
            'type' => 'Type',
            'intitule_resume' => 'Projet/programme',
            'partenaire' => 'Partenaire',
            'numlot' => 'Numéro de lot',
            'date_sig' => 'Date de signature',
            'duree' => 'Durée',
            'montant' => 'Montant',
            'wilaya' => 'Wilaya',
            'nb_loc_adecl' => 'Nombre de localités ATPC',
            'nb_loc_asui_v_postfdal' => 'Nombre de localités suivi post-FDAL',
            'nb_latlam_a_subv' => 'Nombre de latrines familiales à subventionner',
            'nb_cab_a_const' => 'Nombre de cabines à construire',
            'nb_cab_a_ehab12' => 'Nombre de cabines à réhabiliter',
        ];
    }
}