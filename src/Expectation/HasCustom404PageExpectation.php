<?php

namespace Twogether\SweetQA\Expectation;

use Symfony\Component\DomCrawler\Crawler;

/**
 * HasCustom404PageExpectation
 *
 * @author Zac Sturgess <zac.sturgess@wearetwogether.com>
 */
class HasCustom404PageExpectation extends BaseExpectation {
    public static function describe() {
        return [
            "Tests the given URL to see if it has a custom 404 page",
            "A failure is issued if /sweet-qa-not-found has no stylesheets in common with /"
        ];
    }
    
    public function run() {        
        $homepageCrawler = new Crawler(file_get_contents($this->url));
        $notFoundCrawler = new Crawler(file_get_contents($this->url . "sweet-qa-not-found"));
        
        $homepageCSS = $homepageCrawler->filter('head link[rel*=stylesheet]')->extract("href");
        $notFoundCSS = $notFoundCrawler->filter('head link[rel*=stylesheet]')->extract("href");
        
        $matches = 0;
        
        foreach ($homepageCSS as $stylesheet) {
            if (in_array($stylesheet, $notFoundCSS)) {
                $matches++;
            }
        }
        
        if ($matches === 0) {
            $this->addResult("FAIL", "No custom 404 page found (the page at /sweet-qa-not-found had no stylesheets in common with the given page)");
        } else {
            $this->addResult("PASS", "Found a custom 404 page");
        }
        
        return $this->result;
    }
}
