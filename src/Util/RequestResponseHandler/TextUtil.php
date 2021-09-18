<?php 

namespace App\Util\RequestResponseHandler;
 
class TextUtil
{
    public function makeSnakeCase(string $text): string
    {
        if (!trim($text)) {
            return $text;
        }
 
        return strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $text));
    }
}
