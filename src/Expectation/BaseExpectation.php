<?php

namespace Twogether\SweetQA\Expectation;

use Twogether\SweetQA\Helper\ExpectationHelper;

/**
 * BaseExpectation
 *
 * @author Zac Sturgess <zac.sturgess@wearetwogether.com>
 */
class BaseExpectation {
    protected $url;
    protected $output;
    protected $result;
    
    public function __construct($url = NULL, $output = NULL) {
        $this->url = $url;
        $this->output = $output;
        $this->result = ExpectationHelper::initResults();
    }
    
    protected function addResult($level, $message) {
        $level = strtoupper($level);
        
        if (!in_array($level, ["INFO", "PASS", "WARN", "FAIL"])) {
            throw new \InvalidArgumentException("$level is not a valid result message level.");
        }
        
        $this->output->writeln($this->getLevels()[$level] . $message);
        $this->result[$level][] = $message;
    }
    
    public static function describe() {
        return "No description";
    }
    
    private function getLevels() {
        return [
            "INFO" => "[INFO] ",
            "PASS" => "<info>[PASS]</info> ",
            "WARN" => "<comment>[WARN]</comment> ",
            "FAIL" => "<error>[FAIL]</error> "
        ];
    }
}
