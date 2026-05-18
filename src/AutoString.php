<?php

namespace StringObject;

use StringObject\Utf8\Parser;

class AutoString
{
    public static function from(string $input): AsciiString|Win1252String|Utf8String
    {
        if (\preg_match('/^[\x00-\x7F]*$/', $input)) {
            return new AsciiString($input);
        }

        $mbstring = \extension_loaded('mbstring');
        if (
            ($mbstring && \mb_check_encoding($input, 'UTF-8'))
            || (!$mbstring && \preg_match('//u', $input))
        ) {
            return new Utf8String($input);
        }

        if (self::isLikelyWin1252($input)) {
            return new Win1252String($input);
        }

        throw new \InvalidArgumentException('Input string is not valid ASCII, Win1252, or UTF-8');
    }

    private static function isLikelyWin1252(string $input): bool
    {
        // Fail early if any explicitly invalid C1 positions are present
        if (\preg_match('/[\x81\x8D\x8F\x90\x9D]/', $input)) {
            return false;
        }

        // If it contains any other C1 bytes (0x80-0x9F), assume 1252
        if (\preg_match('/[\x80-\x9F]/', $input)) {
            return true;
        }

        // Heuristic: convert the bytes as CP1252 -> UTF-8 and check how much
        // of the result is printable (letters, numbers, punctuation).
        // A high printable ratio (>= 0.6) indicates a likely readable
        // Win1252 interpretation.
        // Perform a strict conversion: do not ignore unconvertible bytes.
        // If conversion fails or produces an empty result, treat as not 1252.
        $utf = false;
        if (\function_exists('iconv')) {
            $utf = @\iconv('CP1252', 'UTF-8', $input);
        }
        if (empty($utf)) {
            $utf = Parser::cp1252ToUtf8($input);
            if (empty($utf)) {
                return false;
            }
        }

        $printableCount = preg_match_all('/[\p{L}\p{N}\p{P}]/u', $utf);

        if (extension_loaded('mbstring')) {
            $charCount = mb_strlen($utf, 'UTF-8');
        } else {
            preg_match_all('/./us', $utf, $chars);
            $charCount = count($chars[0]);
        }

        if ($charCount > 0) {
            $ratio = $printableCount / $charCount;
            if ($ratio >= 0.6) {
                return true;
            }
        }

        return false;
    }
}
