<?php

namespace CyberSec\Shield\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ScanVulnerabilitiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cybershield:scan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan application dependencies for known vulnerabilities (CVEs)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting CyberShield Vulnerability Scan...');

        // 1. Dependency Audit (using composer audit)
        $this->line('Running composer audit...');
        
        $process = new Process(['composer', 'audit', '--format=json']);
        $process->setWorkingDirectory(base_path());
        $process->run();

        $output = $process->getOutput();
        $errorOutput = $process->getErrorOutput();
        
        if ($process->isSuccessful() && empty($output)) {
            $this->info('✅ No vulnerabilities found in dependencies.');
        } else {
            $this->error('❌ Vulnerabilities detected in dependencies!');
            // In a real implementation, you would parse the JSON output 
            // and present a nicely formatted table of CVEs to the developer.
            $this->line($output);
            if ($errorOutput) {
                 $this->error($errorOutput);
            }
        }

        // 2. Application Config Scan (Basic Example)
        $this->line('Checking application configuration...');
        
        if (config('app.debug') && config('app.env') === 'production') {
            $this->error('❌ CRITICAL: APP_DEBUG is true in production environment!');
        } else {
             $this->info('✅ Environment debug settings are secure.');
        }

        return Command::SUCCESS;
    }
}
