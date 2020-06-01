<?php

namespace Horat1us\Houses\CLI\Data;

use Symfony\Component\String\AbstractUnicodeString;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;

class Slugger
{
    private SluggerInterface $slugger;

    public function __construct()
    {
        $this->slugger = new AsciiSlugger('uk');
    }

    public function slug(string $string, string $separator = '', string $locale = null): string
    {
        return mb_strtolower($this->slugger->slug($string, $separator, $locale));
    }
}
