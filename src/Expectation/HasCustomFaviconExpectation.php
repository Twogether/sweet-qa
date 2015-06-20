<?php

namespace Twogether\SweetQA\Expectation;

use Symfony\Component\DomCrawler\Crawler;

/**
 * HasCustomFaviconExpectation
 *
 * @author Zac Sturgess <zac.sturgess@wearetwogether.com>
 */
class HasCustomFaviconExpectation extends BaseExpectation {
    public static function describe() {
        return [
            "Tests the given URL to see if it has a custom favicon",
            "A failure is issued if there is no element matching the selector 'head link[rel*=icon][href*=favicon]'",
            "A warning is issued if there is no element matching the selector 'head link[rel*=icon][href*=apple-touch]'"
        ];
    }
    
    public function run() {        
        $crawler = new Crawler(file_get_contents($this->url));
        $favicons = $crawler->filter('head link[rel*=icon][href*=favicon]');
        $appleTouchIcons = $crawler->filter('head link[rel*=icon][href*=apple-touch]');
        
        if (count($favicons)) {
            $this->addResult("PASS", "Found " . count($favicons) . " favicons on the page");
        } else {
            $this->addResult("FAIL", "No favicons found (no elements matched selector 'head link[rel*=icon][href*=favicon]')");
        }
        
        if (count($appleTouchIcons)) {
            $this->addResult("PASS", "Found " . count($appleTouchIcons) . " apple-touch-icons on the page");
        } else {
            $this->addResult("WARN", "No apple-touch-icons found (no elements matched selector 'head link[rel*=icon][href*=apple-touch]')");
        }
        
        return $this->result;
    }
}
