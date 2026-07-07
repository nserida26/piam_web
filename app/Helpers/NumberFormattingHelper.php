<?php

// app/Helpers/NumberFormattingHelper.php

function formatTaux($number, $decimals = 1)
{
    $number = floatval($number);
    $decimals = intval($decimals);

    if (!is_numeric($number)  || $number == 0 || $number < 0 || $number > 100) {
        return 'NR';
    }

    return number_format($number, 1, ',', ' ') . '%';
}

function formatNumberReport($number, $decimals = 0)
{
    $number = floatval($number);
    $decimals = intval($decimals);

    if (!is_numeric($number) || $number == 0 || !is_finite($number)) {
        return 'NR';
    }

    return number_format($number, 0, ',', ' ');
}

function formatNumberReport2($number, $decimals = 0)
{
    if (!is_numeric($number) || $number === null || $number === 0) {
        return 0;
    }
}
