<?php

namespace App\Console\Commands;

use App\Services\MenageAggregator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Contrôle de la fusion ménages → fiche localité (MenageAggregator).
 *
 *   php artisan menages:verifier-fusion            (contrôle seul)
 *   php artisan menages:verifier-fusion --reparer  (recalcule les écarts)
 *
 * Pour chaque groupe localité + opération + projet de la table menages,
 * recalcule les totaux et les compare à la fiche fusionnée de
 * localite_operations (mobile_ref « menages:… »).
 */
class VerifierFusionMenages extends Command
{
    protected $signature = 'menages:verifier-fusion {--reparer : recalcule les fiches en écart ou manquantes}';

    protected $description = 'Vérifie que les ménages sont bien fusionnés en fiches localité';

    public function handle()
    {
        $groupes = DB::table('menages')
            ->whereNotNull('projet_id')
            ->selectRaw('localite_id, operation_id, projet_id, COUNT(*) nb,
                COALESCE(SUM(nbr_h_c),0) h, COALESCE(SUM(nbr_f_c),0) f,
                COALESCE(SUM(latr_fam),0) latr, MAX(operateur_id) operateur_id, MAX(user_id) user_id')
            ->groupBy('localite_id', 'operation_id', 'projet_id')
            ->get();

        $sansProjet = DB::table('menages')->whereNull('projet_id')->count();
        $ecarts = 0;
        $repares = 0;

        foreach ($groupes as $g) {
            $ref = MenageAggregator::ref($g->operation_id, $g->localite_id, $g->projet_id);
            $fiche = DB::table('localite_operations')->where('mobile_ref', $ref)->first();

            $probleme = null;
            if (!$fiche) {
                $probleme = 'fiche localité manquante';
            } elseif ((int) $fiche->nb_menages_comptabilises !== (int) $g->nb
                || (int) $fiche->nbr_h_c !== (int) $g->h
                || (int) $fiche->nbr_f_c !== (int) $g->f
                || (int) $fiche->nb_tot_latr_fam !== (int) $g->latr) {
                $probleme = sprintf('fiche %d ménages / H %d / F %d / latrines %d, attendu %d / %d / %d / %d',
                    $fiche->nb_menages_comptabilises, $fiche->nbr_h_c, $fiche->nbr_f_c, $fiche->nb_tot_latr_fam,
                    $g->nb, $g->h, $g->f, $g->latr);
            }

            if ($probleme) {
                $ecarts++;
                $this->warn("ECART {$ref} : {$probleme}");
                if ($this->option('reparer')) {
                    MenageAggregator::sync((int) $g->localite_id, (int) $g->operation_id, (int) $g->projet_id,
                        $g->operateur_id ? (int) $g->operateur_id : null, (int) $g->user_id);
                    $repares++;
                }
            }
        }

        // Fiches fusionnées dont tous les ménages ont disparu
        $orphelines = DB::table('localite_operations')->where('mobile_ref', 'like', 'menages:%')->get()
            ->filter(function ($f) {
                [, $op, $loc, $projet] = explode(':', $f->mobile_ref) + [null, null, null, null];
                return !DB::table('menages')->where('operation_id', $op)->where('localite_id', $loc)
                    ->where('projet_id', $projet)->exists();
            });
        foreach ($orphelines as $f) {
            $ecarts++;
            $this->warn("ECART {$f->mobile_ref} : fiche sans aucun ménage");
            if ($this->option('reparer')) {
                DB::table('localite_operations')->where('id', $f->id)->delete();
                $repares++;
            }
        }

        $this->info(sprintf('%d groupe(s) localité/opération/projet contrôlé(s), %d fiche(s) fusionnée(s) en base.',
            $groupes->count(), DB::table('localite_operations')->where('mobile_ref', 'like', 'menages:%')->count()));
        if ($sansProjet) {
            $this->line("{$sansProjet} ménage(s) sans projet (reçus avant la mise à jour) : non fusionnés.");
        }
        if ($ecarts === 0) {
            $this->info('Tout est cohérent.');
        } else {
            $this->line("{$ecarts} écart(s)" . ($this->option('reparer') ? ", {$repares} réparé(s)." : ' : relancer avec --reparer.'));
        }

        return 0;
    }
}
