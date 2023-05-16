<?php

namespace wobef\classes\helpers;

class Formula
{
    const PATTERN = '/(?:\-?\d+(?:\.?\d+)?[\+\-\*\/])+\-?\d+(?:\.?\d+)?/';
    const PARENTHESIS_DEPTH = 10;

    public function calculate($input)
    {
        if (strpos($input, '+') != null || strpos($input, '-') != null || strpos($input, '/') != null || strpos($input, '*') != null) {
            $input = str_replace(',', '.', $input);
            $input = preg_replace('[^0-9\.\+\-\*\/\(\)]', '', $input);
            $i = 0;
            while (strpos($input, '(') || strpos($input, ')')) {
                $input = preg_replace_callback('/\(([^\(\)]+)\)/', 'self::callback', $input);
                $i++;
                if ($i > self::PARENTHESIS_DEPTH) {
                    break;
                }
            }
            if (preg_match(self::PATTERN, $input, $match)) {
                return $this->compute($match[0]);
            }
            if (is_numeric($input)) {
                return $input;
            }
            return 0;
        }
        return $input;
    }

    private function compute($input)
    {
        $result = 'return ' . $input . ';';
        return 0 + eval($result);
    }

    private function callback($input)
    {
        if (is_numeric($input[1])) {
            return $input[1];
        } elseif (preg_match(self::PATTERN, $input[1], $match)) {
            return $this->compute($match[0]);
        }
        return 0;
    }
}
