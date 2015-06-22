<?php

namespace Twogether\SweetQA\Expectation;

use Twogether\SweetQA\Helper\ScriptHelper;

/**
 * HasGoogleAnalyticsInstalledExpectation
 *
 * @author Zac Sturgess <zac.sturgess@wearetwogether.com>
 */
class HasGoogleAnalyticsInstalledExpectation extends BaseExpectation {
    public static function describe() {
        return [
            "Tests the given URL to see if Google Analytics is installed",
            "A failure is issued if the Universal Analytics snippet could not be found on the page or any linked javascript files on the same domain",
            "A warning is issued if multiple instances of the Universal Analytics snippet are found",
            "A failure is issued if the page load is tracked more than once",
            "A warning is issued if the page load is not tracked at all",
            "A warning is issued if not other events are being tracked with Google Analytics"
        ];
    }
    
    public function run() {
        $ga_setAccount = 0;
        $ga_account = "";
        $ga_trackPageview = 0;
        $ga_other = 0;
        
        foreach (ScriptHelper::findScripts($this->url) as $script) {
            preg_match_all("/_gaq\.push\(.*\)/", $script, $matches);
            foreach ($matches[0] as $match) {
                if (strstr($match, "_setAccount") !== FALSE) {
                    $ga_setAccount++;
                    $ga_account = substr($match, -16, 13);
                } else if (strstr($match, "_trackPageview") !== FALSE) {
                    $ga_trackPageview++;
                } else {
                    $ga_other++;
                }
            }
            
            preg_match_all("/ga\(.*\)/", $script, $uamatches);
            foreach ($uamatches[0] as $match) {
                if (strstr($match, "'create'") !== FALSE) {
                    $ga_setAccount++;
                    $ga_account = substr($match, strpos($match, 'UA-'), 13);
                } else if (strstr($match, "'pageview'") !== FALSE) {
                    $ga_trackPageview++;
                } else {
                    $ga_other++;
                }
            }
        }
        
        switch ($ga_setAccount) {
            case 0:
                $this->addResult("FAIL", "No Google Analytics installed (No call to _setAccount found)");
                return $this->result;
            case 1:
                if (count($matches[0]) > 0) {
                    $this->addResult("WARN", "Legacy Google Analytics tracking installed with tracking code $ga_account (Consider upgrading to Universal Analytics)");
                } else {
                    $this->addResult("PASS", "Google Analytics installed with tracking code $ga_account");
                }
                break;
            default:
                $this->addResult("WARN", "Multiple Google Analytics installations detected");
                break;
        }
        
        switch ($ga_trackPageview) {
            case 0:
                $this->addResult("WARN", "Google Analytics is not tracking page views");
                break;
            case 1:
                $this->addResult("PASS", "Google Analytics is set up to track page views");
                break;
            default:
                $this->addResult("WARN", "Google Analytics appears to be tracking page views multiple times.");
                break;
        }
        
        if ($ga_other === 0) {
            $this->addResult("WARN", "No other events appear to be tracked with Google Analytics");
        } else {
            $this->addResult("PASS", "Google Analytics is set up to track $ga_other other events");
        }
        
        return $this->result;
    }
}
