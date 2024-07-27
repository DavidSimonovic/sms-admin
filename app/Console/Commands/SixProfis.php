<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class SixProfis extends Command
{
    protected $signature = 'node:sixprofis_links';
    protected $description = 'Run a Node.js script';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $process = new Process(['/usr/bin/node', '/var/www/html/sms-admin/scripts/sixprofis.js']);
        $process->setTimeout(3600);

        try {
            $process->run();

            // Executes after the command finishes
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $this->info('Node.js script executed successfully');
            $this->info($process->getOutput());
        } catch (ProcessFailedException $exception) {
            $this->error('Node.js script failed to execute');
            $this->error($exception->getMessage());
        }
    }
}
