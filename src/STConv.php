<?php

namespace Codatsoft\Codatbase;

class STConv
{
    public static function getStringBetween($sentence,$mark1,$mark2)
    {
        return substr($sentence,strpos($sentence,$mark1) + strlen($mark1),strlen($sentence) - (strlen(substr($sentence,0,strpos($sentence,$mark1) + strlen($mark1))) + strlen(substr($sentence,strpos($sentence, $mark2)))));
    }

    public static function getUSBasePhone($value)
    {
        //dbPhone
        $fix1 = str_replace('whatsapp:','',$value);
        if (str_contains($fix1, '+1'))
        {
            $fix2 = str_replace('+1','',$fix1);
        }

        return $fix2;
    }

    public static function properPhone($value) {
        if ($value[0] != '+')
        {
            return '+1' . $value;
        }

        return $value;
    }

    public static function getMoneyFromLong($value)
    {
        return '$' . number_format(($value /100), 2, '.', ' ');
    }



}
