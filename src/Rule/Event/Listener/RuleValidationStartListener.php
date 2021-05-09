<?php

declare(strict_types=1);

namespace PhpAT\Rule\Event\Listener;

use PHPAT\EventDispatcher\EventInterface;
use PHPAT\EventDispatcher\EventListenerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAT\Rule\Event\RuleValidationStartEvent;

class RuleValidationStartListener implements EventListenerInterface
{
    private $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function __invoke(EventInterface $event)
    {
        /** @var RuleValidationStartEvent $event */
        $this->output->writeln('', OutputInterface::VERBOSITY_VERBOSE);
        $this->output->writeln(str_repeat('-', strlen($event->getRuleName()) + 4), OutputInterface::VERBOSITY_VERBOSE);
        $this->output->writeln('| ' . $event->getRuleName() . ' |', OutputInterface::VERBOSITY_VERBOSE);
        $this->output->writeln(str_repeat('-', strlen($event->getRuleName()) + 4), OutputInterface::VERBOSITY_VERBOSE);
    }
}
