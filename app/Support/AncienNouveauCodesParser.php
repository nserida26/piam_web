<?php

namespace App\Support;

/**
 * Parses the pandoc-style ASCII table export listing, per localité, its target
 * (new) Wilaya/Moughataa/Commune plus its old and new numeric codes.
 *
 * The table is fixed-width but pandoc wraps long "Nom de la localité" values
 * onto a following, heavily-indented line with no numbers on it — that line
 * is treated as a continuation of the previous row's name rather than a new
 * row. Columns are otherwise separated by runs of 2+ spaces; a locality name
 * can itself contain single spaces (or, rarely, an accidental double space),
 * which is why matching is anchored on the trailing "<code>   <code>   <name>   <population>"
 * shape rather than a naive split on whitespace runs.
 */
class AncienNouveauCodesParser
{
    /**
     * Known wilaya names as they appear in the file, longest first so a
     * multi-word wilaya (e.g. "Dakhlet Nouadhibou") is matched before any
     * shorter prefix could be. Matching the wilaya by name (rather than by
     * column position) is what lets us parse rows where the next column
     * starts after only a single space instead of the usual 2+.
     */
    private const WILAYAS = [
        'Hodh Chargui',
        'Hodh El Gharbi',
        'Hodh Gharbi',
        'Assaba',
        'Gorgol',
        'Brakna',
        'Trarza',
        'Adrar',
        'Guidimakha',
        'Tagant',
        'Inchiri',
        'Tiris Zemmour',
        'Dakhlet Nouadhibou',
        'Nouakchott-Nord',
        'Nouakchott-Ouest',
        'Nouakchott-Sud',
    ];

    private const ROW_PATTERN = '/^(?<moughataa>\S.*?)\s{2,}(?<commune>\S.*?)\s{2,}(?<ancien>\d+)\s{2,}(?<nouveau>\d+)\s{2,}(?<nom>.+\S)\s{2,}(?<pop>\d+)$/u';

    private array $wilayasByLengthDesc;

    public function __construct()
    {
        $this->wilayasByLengthDesc = self::WILAYAS;
        usort($this->wilayasByLengthDesc, fn($a, $b) => mb_strlen($b) <=> mb_strlen($a));
    }

    /**
     * @return array<int, array{wilaya:string,moughataa:string,commune:string,ancien_code:string,nouveau_code:string,nom_localite:string,population:int}>
     */
    public function parse(string $path): array
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            throw new \RuntimeException("Impossible de lire le fichier: {$path}");
        }

        $rows = [];
        $currentIndex = null;

        foreach ($lines as $line) {
            if (trim($line) === '') {
                $currentIndex = null;
                continue;
            }

            $parsed = $this->tryParseRow($line);
            if ($parsed !== null) {
                $rows[] = $parsed;
                $currentIndex = count($rows) - 1;
                continue;
            }

            if ($currentIndex !== null) {
                $fragment = trim($line);
                if ($fragment !== '') {
                    $rows[$currentIndex]['nom_localite'] .= ' ' . $fragment;
                }
            }
        }

        return $rows;
    }

    private function tryParseRow(string $line): ?array
    {
        $trimmed = ltrim($line);
        $wilaya = $this->matchWilayaPrefix($trimmed);
        if ($wilaya === null) {
            return null;
        }

        $remainder = ltrim(mb_substr($trimmed, mb_strlen($wilaya)));
        if (!preg_match(self::ROW_PATTERN, $remainder, $m)) {
            return null;
        }

        return [
            'wilaya' => $wilaya,
            'moughataa' => trim($m['moughataa']),
            'commune' => trim($m['commune']),
            'ancien_code' => $m['ancien'],
            'nouveau_code' => $m['nouveau'],
            'nom_localite' => trim($m['nom']),
            'population' => (int) $m['pop'],
        ];
    }

    private function matchWilayaPrefix(string $trimmedLine): ?string
    {
        foreach ($this->wilayasByLengthDesc as $wilaya) {
            if (str_starts_with($trimmedLine, $wilaya)) {
                $rest = substr($trimmedLine, strlen($wilaya));
                if ($rest === '' || ctype_space($rest[0])) {
                    return $wilaya;
                }
            }
        }

        return null;
    }
}
