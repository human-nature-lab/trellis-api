<?php

namespace App\Console\Commands;

use App\Library\FileHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ExportSQLite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trellis:export:sqlite {--exclude=*} {storage_path?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Report SQLite database to storage_path (or stdout if not specified). --exclude=<table> can be specified multiple times to exclude table(s) from the dump';

    const MYSQL_2_SQLITE = 'app/Console/Scripts/mysql2sqlite/mysql2sqlite';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mysql2sqlite = base_path() . '/' . self::MYSQL_2_SQLITE;

        if (!is_executable($mysql2sqlite)) {
            $this->error("Please run `chmod 0770 $mysql2sqlite` to make the script executable");

            return 1;
        }

        ///// dump sqlite /////

        $excludeTablesString = implode(' ', array_map(function ($table) {
            return "--exclude=" . escapeshellarg($table);
        }, $this->option('exclude')));

        if (!is_null($this->argument('storage_path'))) {
            $dumpPath = FileHelper::storagePath($this->argument('storage_path'));

            FileHelper::mkdir(dirname($dumpPath));

            $dumpPathString = '> ' . escapeshellarg($dumpPath);
        } else {
            $dumpPathString = '';
        }

        /*

        SQLite uses PIPES_AS_CONCAT SQL mode instead of escaping the linefeed character, which means that insert statements cross line boundaries.
        This script converts ...\n... to ...' || x'0A' || '... to allow for easier splitting of statements on ";\n" by the client.
        The conversion is then passed to mysql2sqlite, which supports PIPES_AS_CONCAT mode for input and passes it through.
        Note that '\n' is ASCII [92 110], not the "\n" linefeed LF character [10].  '\n' will never appear outside of field values in the SQL dump.
        Here are some variations of converting a 100 MB SQL dump on a 2.2 GHz Intel Core i7 for comparison (sed and awk need optimization):

        $ time php artisan trellis:export:mysql > /dev/null
        real	0m1.769s

        $ time php artisan trellis:export:mysql | php -r 'while($s = fgets(STDIN)) echo preg_replace("/(?<=[^\\\\])(\\\\{2})*\\\\n/", "'\'' || x'\''0A'\'' || '\''", $s);' > /dev/null
        real	0m2.114s

        $ time php artisan trellis:export:mysql | perl -ne "s/(?<=[^\\\\])(\\\\{2})*\\\\n/' || x'0A' || '/g; print;" > /dev/null
        real	0m2.297s

        $ time php artisan trellis:export:mysql | sed 's/\([^\\]\)\(\\\{2\}\)*\\n/\1'\'' || x'\''0A'\'' || '\''/g' > /dev/null
        real	0m11.539s

        $ time php artisan trellis:export:mysql | awk '{ print match($0, /(.*)([^\\])(\\{2})*\\n(.*)/, a) ? a[1]a[2]"'"' || x'0A' || '"'"a[4] : $0 }' > /dev/null
        real	11m9.513s

         */
        $process = new Process(<<<EOT
php artisan trellis:export:mysql $excludeTablesString |
php -r 'while(\$s = fgets(STDIN)) echo preg_replace("/(?<=[^\\\\\\\\])(\\\\\\\\{2})*\\\\\\\\n/", "'\'' || x'\''0A'\'' || '\''", \$s);' |
$mysql2sqlite - $dumpPathString
EOT
, base_path());

        $process->setTimeout(null)->run(function ($type, $buffer) {
            fwrite($type === Process::OUT ? STDOUT : STDERR, $buffer);
        });

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return 0;
    }
}
