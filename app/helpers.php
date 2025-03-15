<?php

if (!function_exists('convertNumberToArabicWords')) {
    /**
     * Convert a number to its Arabic words representation using PHP's NumberFormatter.
     * This function assumes that the intl extension is enabled.
     * 
     * @param float|int $number The number to convert
     * @return string The Arabic words representation of the number
     */
    function convertNumberToArabicWords($number) {
        if (class_exists('\\NumberFormatter')) {
            $formatter = new \NumberFormatter('ar', \NumberFormatter::SPELLOUT);
            if (strpos((string)$number, '.') !== false) {
                list($intPart, $decimalPart) = explode('.', (string)$number);
                $intWords = $formatter->format((int)$intPart);
                $decimalWordsArray = [];
                foreach (str_split($decimalPart) as $digit) {
                    $decimalWordsArray[] = $formatter->format((int)$digit);
                }
                $decimalWords = implode(' ', $decimalWordsArray);
                return $intWords . ' فاصل ' . $decimalWords;
            } else {
                return $formatter->format((int)$number);
            }
        } else {
            // Fallback conversion if NumberFormatter class is not available
            $map = [
                '0' => 'صفر',
                '1' => 'واحد',
                '2' => 'اثنان',
                '3' => 'ثلاثة',
                '4' => 'أربعة',
                '5' => 'خمسة',
                '6' => 'ستة',
                '7' => 'سبعة',
                '8' => 'ثمانية',
                '9' => 'تسعة'
            ];
            $numberStr = (string)$number;
            $output = [];
            foreach (str_split($numberStr) as $char) {
                if (isset($map[$char])) {
                    $output[] = $map[$char];
                } else {
                    $output[] = $char;
                }
            }
            return implode(' ', $output);
        }
    }
}
