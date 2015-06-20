<?php

namespace Twogether\SweetQA\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twogether\SweetQA\Helper\ExpectationHelper;

/**
 * TestCommand
 *
 * @author Zac Sturgess <zac.sturgess@wearetwogether.com>
 */
class ExpectationsCommand extends Command {
    protected function configure() {
        $this
            ->setName("expectations")
            ->setDescription("Lists expectations and their conditions")
            ->addArgument(
                "expectation",
                InputArgument::OPTIONAL,
                "An expectation to show. Omit to show all"
            )
        ;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        $expectations = [$input->getArgument('expectation')];

        if ($expectations === [NULL]) {
            $expectations = ExpectationHelper::getAll();
        }
        
        foreach ($expectations as $expectation) {
            if (!ExpectationHelper::has($expectation)) {
                $output->writeln("<error>Expectation '$expectation' not found</error>");
                continue;
            }
            
            $output->writeln("<info>$expectation:</info>");
            
            $expectation = ExpectationHelper::get($expectation);
                
            if (method_exists($expectation, "describe")) {
                $output->writeln($expectation::describe());
            }
            
            $output->writeln("");
        }      
    }
}
