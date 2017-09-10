<?php

namespace niiknow;

class StringTokenizer implements iTokenizer
{
    /**
     * regex constant for string parsing
     */
    const WORD_REGEX = '/[[:alpha:]]+/u';

    /**
     * Tokenizer implementation - splits string to words
     * @param string $string
     * @return TokenArray
     */
    public function tokenize($string)
    {
        $string = strtolower($string);

        preg_match_all(self::WORD_REGEX, $string, $matches);

        return $matches[0];
    }
}
