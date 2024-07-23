<?php

namespace App\Helper;

class TextCleaner
{

    public static function cleanText($text, $name, $city)
    {


        $placeholders = [
            '{{ name }}' => $name,
            '{{ city }}' => $city
        ];

        foreach ($placeholders as $placeholder => $value) {
            $text = str_replace($placeholder, $value, $text);
        }

        return $text;
    }

}
