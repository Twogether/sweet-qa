<?php

namespace Twogether\SweetQA\Expectation;

use Symfony\Component\DomCrawler\Crawler;

/**
 * HasNoMixedContentExpectation
 *
 * @author Zac Sturgess <zac.sturgess@wearetwogether.com>
 */
class HasNoMixedContentExpectation extends BaseExpectation {
    public static function describe() {
        return [
            "Tests the given URL to see if Kissmetrics is installed",
            "A failure is issued if the URL being tested is https and the string 'http://' appears on the page or any linked stylesheets",
            "A warning is issued if the URL being tested is http and the string 'http://' appears on the page or any linked stylesheets"
        ];
    }
    
    public function run() {
        $crawler = new Crawler(file_get_contents($this->url));
        
        $stylesheets = $crawler->filter('head link[rel*=stylesheet]')->extract("href");
        $httpReferences = $crawler->filter("[href*='http://']")->count() + $crawler->filter("[src*='http://']")->count();
        
        foreach ($stylesheets as $stylesheet) {
            if (parse_url($stylesheet, PHP_URL_HOST) === NULL) {
                $stylesheet = $this->url . $stylesheet;
            }
            
            if (strstr(file_get_contents($stylesheet), "http://") !== FALSE) {
                $httpReferences++;
            }
        }
        
        if ($httpReferences > 0) {
            if (strstr($this->url, "https://") !== FALSE) {
                $this->addResult("FAIL", "Mixed content was found (Ensure assets are not hard-coded to use http:// connections)");
            } else {
                $this->addResult("WARN", "Potenetial mixed content found (If the site might be hosted over HTTPS, ensure assets are not hard-coded to use http:// connections)");
            }
        } else {
            $this->addResult("PASS", "No mixed content found");
        }
        
        return $this->result;
    }
}
