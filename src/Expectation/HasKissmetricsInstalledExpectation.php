<?php

namespace Twogether\SweetQA\Expectation;

use Twogether\SweetQA\Helper\ScriptHelper;

/**
 * HasKissmetricsInstalledExpectation
 *
 * @author Zac Sturgess <zac.sturgess@wearetwogether.com>
 */
class HasKissmetricsInstalledExpectation extends BaseExpectation {
    public static function describe() {
        return [
            "Tests the given URL to see if Kissmetrics is installed",
            "A warning is issued if the Kissmetrics snippet could not be found on the page or any linked javascript files on the same domain",
            "A warning is issued if the Kissmetrics snippet appears to be incomplete",
            "A warning is issued if no calls to the Kissmetics 'identify' method could be found",
            "A warning is issued if not other events are being tracked with Kissmetrics"
        ];
    }
    
    public function run() {
        $kms = 0;
        $kmq_identify = 0;
        $kmq_other = 0;
        
        foreach (ScriptHelper::findScripts($this->url) as $script) {
            preg_match_all("/_kmq\.push\(.*\)/", $script, $matches);
            
            foreach ($matches[0] as $match) {
                if (strstr($match, "identify") !== FALSE) {
                    $kmq_identify++;
                } else {
                    $kmq_other++;
                }
            }
            
            $kms += preg_match_all("/_kms\('.*'\)/", $script);
        }
        
        switch ($kms) {
            case 0:
                $this->addResult("WARN", "No Kissmetrics tracking installed (No call to _kms found)");
                return $this->result;
            case 2:
                $this->addResult("PASS", "Kissmetrics tracking installed correctly");
                break;
            default:
                $this->addResult("WARN", "Kissmetrics tracking detected, but appears to be improperly installed (There should only be two calls to _kms())");
                break;
        }
        
        if ($kmq_identify === 0) {
            $this->addResult("WARN", "Kissmetrics has not been set up to identify users (No identify calls found)");
        } else {
            $this->addResult("PASS", "Kissmetrics is set up to identify users in $kmq_identify ways");
        }
        
        if ($kmq_other === 0) {
            $this->addResult("WARN", "No other events appear to be tracked with Kissmetrics");
        } else {
            $this->addResult("PASS", "Kissmetrics is set up to track $kmq_other other events");
        }
        
        return $this->result;
    }
}
