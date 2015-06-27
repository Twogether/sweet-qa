<?php

namespace Twogether\SweetQA\Expectation;

use Symfony\Component\DomCrawler\Crawler;

/**
 * HasMetaDescriptionTagExpectation
 *
 * @author Zac Sturgess <zac.sturgess@wearetwogether.com>
 */
class HasMetaDescriptionTagExpectation extends BaseExpectation {
    public static function describe() {
        return [
            "Tests the given URL to see if a meta description tag exists for snippet generation",
            "A failure is issued if there is no description tag present.",
            "A warning is issued if there is more than one description tag present."
        ];
    }
    
    public function run() {
        $crawler = new Crawler(file_get_contents($this->url));
        $descriptions = $crawler->filter('head meta[name=description]');
        
        if ($descriptions->count() === 0) {
            $descriptions = $crawler->filter('head meta[name=Description]');
        }
        
        switch ($descriptions->count()) {
            case 0:
                $this->addResult("FAIL", "No meta description tag is present. Search engines and social media sites will guess when trying to form page snippets.");
                break;
            case 1:
                $this->addResult("PASS", "Meta description is set to: '" . $descriptions->extract("content")[0] . "'.");
                break;
            default:
                $this->addResult("WARN", "Multiple meta description tags detected. (Multiple elements matching 'head meta[name=description]')");
                break;
        }
        
        return $this->result;
    }
}
