<?php

namespace App\Support\Concerns;

trait NormalizesAdministrativeNames
{
    /**
     * Nettoie une chaîne pour la comparaison
     */
    protected function normalizeString($string)
    {
        if (empty($string)) {
            return '';
        }

        $string = mb_strtolower(trim($string), 'UTF-8');
        $string = preg_replace('/\s+/', ' ', $string);

        $search = ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'î', 'ï', 'ô', 'ö', 'ù', 'û', 'ü', 'ç', 'ñ', 'œ', 'æ'];
        $replace = ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'i', 'i', 'o', 'o', 'u', 'u', 'u', 'c', 'n', 'oe', 'ae'];
        $string = str_replace($search, $replace, $string);

        // Remove special characters but keep spaces
        $string = preg_replace('/[^a-z0-9\s]/', '', $string);

        return trim($string);
    }

    /**
     * Vérifie si deux chaînes correspondent (recherche floue)
     */
    protected function stringsMatch($value1, $value2)
    {
        $normalized1 = $this->normalizeString($value1);
        $normalized2 = $this->normalizeString($value2);

        if (empty($normalized1) || empty($normalized2)) {
            return false;
        }

        if ($normalized1 === $normalized2) {
            return true;
        }

        if (strpos($normalized1, $normalized2) !== false || strpos($normalized2, $normalized1) !== false) {
            return true;
        }

        similar_text($normalized1, $normalized2, $percent);
        return $percent > 85;
    }
}
