<?php

namespace Twogether\SweetQA\Expectation;

/**
 * HasBlockedHiddenFilesExpectation
 *
 * @author Zac Sturgess <zac.sturgess@wearetwogether.com>
 */
class HasBlockedHiddenFilesExpectation extends BaseExpectation {
    public static function describe() {
        return [
            "Tests the given URL to see if any hidden files are publicly accessible",
            "Files tested: " . implode(", ", self::getHiddenFiles()),
            "A warning is issued for each hidden file that is exposed"
        ];
    }
    
    public function run() {
        foreach (self::getHiddenFiles() as $file) {
            $headers = get_headers($this->url . $file);
            
            if (strstr($headers[0], "HTTP/1.1 2") !== FALSE) {
                $this->addResult("WARN", "The file at '" . $this->url . $file . "' is publicly accessible");
            }
        }
        
        if (count($this->result["WARN"]) === 0) {
            $this->addResult("PASS", "None of the hidden files checked were publicly accessible");
        }
        
        return $this->result;
    }
    
    private static function getHiddenFiles() {
        return [
            ".htaccess",
            ".gitignore",
            ".svn/format",
            ".git/config",
            "README.md",
            "LICENSE",
            "app_dev.php",
            "frontend_dev.php",
            "index_dev.html",
            "check.php",
        ];
    }
}
