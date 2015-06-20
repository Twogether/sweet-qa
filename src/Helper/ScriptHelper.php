<?php

namespace Twogether\SweetQA\Helper;

use Symfony\Component\DomCrawler\Crawler;

/**
 * ScriptHelper
 *
 * @author Zac Sturgess <zac.sturgess@wearetwogether.com>
 */
class ScriptHelper {
    public static function findScripts($url) {
        $sources[] = file_get_contents($url);
        
        $crawler = new Crawler($sources[0]);
        $scripts = $crawler->filter('script:not([src*="//"])[src*=".js"]')->extract("src");
        
        foreach ($scripts as $script) {
            $sources[] = file_get_contents($url . $script);
        }
        
        return $sources;
    }
}
