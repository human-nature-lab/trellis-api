<?php

namespace App\Console\Commands;

use App\Library\DatabaseHelper;
use DB;
use Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use PDO;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class MergeMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:merge:migrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merges all of the migrations from the database/migrations directory and outputs the result.  Usage: composer dump-autoload && php artisan trellis:merge:migrations > "database/migrations/$(date +%Y_%m_%d_%H%M%S)_create_tables.php"';

    /**
     * Execute the console command.
     *
     * @return integer
     */
    public function handle()
    {
        // doesn't work because autoloader isn't updated in currently running instance
//         $process = new Process(<<<EOT
// composer dump-autoload
// EOT
// , base_path());
//
//         $process->setTimeout(null)->run(function ($type, $buffer) {
//             fwrite($type === Process::OUT ? STDOUT : STDERR, $buffer);
//         });
//
//         if (!$process->isSuccessful()) {
//             throw new ProcessFailedException($process);
//         }
//
//         if (stripos($process->getErrorOutput(), 'warning') !== false) {
//             $this->error('Sorry, a problem with the autoloader occurred.');
//
//             return 1;
//         }

        // doesn't work for some reason
        // $loader = require base_path() . '/vendor/autoload.php';
        // $loader->addPsr4("App\\", base_path() . '/database/migrations/');
        // $loader->register();

        $ups = [];
        $downs = [];

        foreach (glob(base_path() . '/database/migrations/*.php') as $filePath) {
            $lines = file($filePath);

            try {
                $class = head($this->getClasses(implode('', $lines)));

                if ($class) {
                    $reflection = new \ReflectionClass($class);

                    $ups []= $this->getBody($reflection->getMethod('up'), $lines);
                    $downs []= $this->getBody($reflection->getMethod('down'), $lines);
                }
            } catch (\ReflectionException $e) {
                $this->error("Class $class not found.  Always run `composer dump-autoload` before `php artisan {$this->signature}`.");  //NOTE get rid of this once figure out how to run autoload.php programmatically

                return 1;
            }
        }

        $downs = array_reverse($downs); // list down() calls in reverse to match `php artisan migrate:rollback` order

        echo <<<EOT
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

EOT
. implode("\n", $ups) . <<<EOT
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{

EOT
. implode("\n", $downs) . <<<EOT
	}

}

EOT
;

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

    protected function getBody($reflectionMethod, $lines)
    {
        $startLine = $reflectionMethod->getStartLine();
        $endLine = $reflectionMethod->getEndLine();
        $length = $endLine - $startLine;
        $bodyLines = array_slice($lines, $startLine, $length);

        // trim whitespace lines
        while (true) {
            if (trim(array_get($bodyLines, 0)) == '') {
                array_shift($bodyLines);
            } else {
                if (trim(array_get($bodyLines, count($bodyLines) - 1)) == '') {
                    array_pop($bodyLines);
                } else {
                    break;
                }
            }
        }

        // trim {} lines
        if (trim(preg_replace('/\s*\{/', '', array_get($bodyLines, 0), 1)) == '' && trim(preg_replace('/\s*\}/', '', strrev(array_get($bodyLines, count($bodyLines) - 1)), 1)) == '') {
            array_shift($bodyLines);
            array_pop($bodyLines);
        }

        return implode('', $bodyLines);
    }
}
