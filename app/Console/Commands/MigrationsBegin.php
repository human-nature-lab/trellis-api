<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrationsBegin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:migrations:begin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs all of the commands in database/migrations_begin alphabetically';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (glob(base_path() . '/database/migrations_begin/*.php') as $filePath) {
            require_once $filePath;

            $class = '\\Database\\MigrationsBegin\\' . head($this->getClasses(file_get_contents($filePath)));
            $command = new $class;

            $command->setApplication($this->getApplication());
            $command->setLaravel($this->getLaravel());
            $code = $command->run($this->input, $this->output);

            if($code) {
                return $code;
            }
        }

        return 0;
    }

    protected function getClasses($code)
    {
        $classes = [];
        $tokens = token_get_all($code);
        $count = count($tokens);

        for ($i = 2; $i < $count; $i++) {
            if ($tokens[$i - 2][0] == T_CLASS && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
                $class_name = $tokens[$i][1];
                $classes[] = $class_name;
            }
        }

        return $classes;
    }
}
