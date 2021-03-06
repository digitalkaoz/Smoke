<?php

namespace whm\Smoke\Extensions\SmokeReporter;

use PhmLabs\Components\Init\Init;
use Symfony\Component\Console\Output\OutputInterface;
use whm\Smoke\Config\Configuration;
use whm\Smoke\Extensions\SmokeReporter\Reporter\OutputAwareReporter;
use whm\Smoke\Scanner\Result;

class ReporterExtension
{
    private $reporters;

    /**
     * @Event("ScannerCommand.Output.Register")
     */
    public function setOutput(OutputInterface $output)
    {
        foreach ($this->reporters as $reporter) {
            if ($reporter instanceof OutputAwareReporter) {
                $reporter->setOutput($output);
            }
        }
    }

    /**
     * @Event("ScannerCommand.Config.Register")
     */
    public function setReporter(Configuration $config)
    {
        if ($config->hasSection('reporter')) {
            $this->reporters = Init::initializeAll($config->getSection('reporter'));
        }
    }

    /**
     * @Event("Scanner.Scan.Validate")
     */
    public function process(Result $result)
    {
        foreach ($this->reporters as $reporter) {
            $reporter->processResult($result);
        }
    }

    /**
     * @Event("Scanner.Scan.Finish")
     */
    public function finish()
    {
        foreach ($this->reporters as $reporter) {
            $reporter->finish();
        }
    }
}
