<?php

namespace App\Acme;

class Util {
    private array $acceptedUuidCharacters = ["a", "b", "c", "d", "e", "f", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];

    public function getRandomUuid($size = 32): string
    {
        $uuid = "";
        $charsLength = count($this->acceptedUuidCharacters);
        for ($i = 0; $i < $size; $i++) {
            $uuid .= $this->acceptedUuidCharacters[random_int(0, $charsLength-1)];
        }
        return $uuid;
    }
}
