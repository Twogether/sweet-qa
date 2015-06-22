<?php

namespace Twogether\SweetQA\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Twogether\SweetQA\Helper\ExpectationHelper;

/**
 * TestCommand
 *
 * @author Zac Sturgess <zac.sturgess@wearetwogether.com>
 */
class TestCommand extends Command {
    private $url;
    private $output;
    
    protected function configure() {
        $this
            ->setName("test")
            ->setDescription("Tests a given bespoke site against Sweet QA's expectations")
            ->addArgument(
                "url",
                InputArgument::REQUIRED,
                "The site to test"
            )
            ->addOption(
                "expectation",
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                "Names of expectations to run. Will run all expectations if none passed."
            )
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->url = $this->normaliseURL($input->getArgument("url"));
        $this->output = $output;
        
        
        $expectations = $input->getOption('expectation');
        if (empty($expectations)) {
            $expectations = ExpectationHelper::getAll();
        }
        
        $results = ExpectationHelper::initResults();
            
        $output->writeln(">> Starting tests on " . $this->url);
        
        foreach ($expectations as $expectation) {
            $result = $this->runExpectation($expectation);
            $results = array_merge_recursive($results, $result);
        }
       
        $output->writeln(">> Tests complete.");
        $output->writeln(">> <info>" . count($results["PASS"]) ." passes</info>, <comment>". count($results["WARN"]) ." warnings</comment>, <error>". count($results["FAIL"]) ." failures</error>");
        $output->writeln(">> Failures represent items that in almost all cases should be fixed.");
        $output->writeln(">> Warnings represent items that in some cases might need to be fixed.");
        $output->writeln(">> Gated sites may produce false postives: Always review these results with a developer.");
 
        return count($results["FAIL"]);        
    }
    
    private function runExpectation($expectation) {
        $expectation = ExpectationHelper::get($expectation);
        
        if (is_string($expectation)) {
            $runner = new $expectation($this->url, $this->output);
            return $runner->run();
        }
        
        return $expectation;
    }
    
    private function normaliseURL($url) {
        $url = rtrim($url, '/') . '/';
        
        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            throw new \InvalidArgumentException("The URL $url is not valid. Make sure you copy the full address from your browser.");
        }
        
        return $url;
    }
}
