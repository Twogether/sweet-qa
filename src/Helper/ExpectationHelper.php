<?php

namespace Twogether\SweetQA\Helper;

/**
 * ExpectationHelper
 *
 * @author Zac Sturgess <zac.sturgess@wearetwogether.com>
 */
class ExpectationHelper {
    public static function getAll() {
        $getClass = function ($a) {
            return str_replace([
                "src/Expectation/",
                "Expectation.php"
            ], '', $a);
        };
        
        return array_map($getClass, glob("src/Expectation/Has*Expectation.php"));
    }
    
    public static function get($expectation) {
        if (self::has($expectation)) {
            return "Twogether\SweetQA\Expectation\\" . $expectation . "Expectation";
        }
        
        $results = self::initResults();
        $results["FAIL"][] = "The expectation '$expectation' could not be found";
        return $results;
    }
    
    public static function has($expectation) {
        return in_array($expectation, self::getAll());
    }
    
    public static function initResults() {
        return [
            "OK" => [],
            "INFO" => [],
            "WARN" => [],
            "FAIL" => []
        ];
    }
}
