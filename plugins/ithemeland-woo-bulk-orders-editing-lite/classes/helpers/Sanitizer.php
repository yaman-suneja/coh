<?php

namespace wobef\classes\helpers;

class Sanitizer
{
    public static function array($val)
    {
        $sanitized = null;
        if (is_array($val)) {
            if (count($val) > 0) {
                foreach ($val as $key => $value) {
                    $sanitized[$key] = (is_array($value)) ? self::array($value) : sanitize_text_field($value);
                }
            }
        } else {
            $sanitized = sanitize_text_field($val);
        }
        return $sanitized;
    }
}
