<?php

namespace app\Http\Controllers;

use App\Library\DatabaseHelper;
use App\Library\FileHelper;
use App\Library\TimeHelper;
use App\Models\Device;
use App\Models\Epoch;
use App\Models\Log;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Laravel\Lumen\Routing\Controller;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Statements\InsertStatement;
use Symfony\Component\Process\Process;
use Validator;

class SyncController extends Controller
{
    public function heartbeat()
    {
        return response()->json([], Response::HTTP_OK);
    }

    public function store(Request $request, $deviceId)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $deviceId
        ]), [
            'id' => 'required|string|min:36|exists:device,device_id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        }
    }

    public function download(Request $request, $deviceId)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $deviceId
        ]), [
            'id' => 'required|string|min:14|exists:device,device_id',
            'table' => 'required|string|min:1'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };

        $response = [];

        $response["deviceId"] = $deviceId;
        $response["table"] = $request->input('table');

        $tableClass = str_replace(' ', '', ucwords(str_replace('_', ' ', $request->input('table'))));
        $className = "\\App\\Models\\$tableClass";
        $classModel = $className::all();

        $response["numRows"] = $classModel->count();
        $response["totalRows"] = $classModel->count();
        $response["rows"] = $classModel;

        return response()->json($response, Response::HTTP_OK);
    }

    public function upload(Request $request, $deviceId)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $deviceId
        ]), [
            'id' => 'required|string|min:14|exists:device,device_id',
            'table' => 'required|string|min:1'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($request->input('rows') as $row) {
            /*
            $newClassName = "\\App\\Models\\" . str_replace(' ', '', str_replace('_', '', ucwords($request->input('table'), '_')));
            $newClassName::create($row);
            */
            // Need to INSERT IGNORE to allow for resuming incomplete syncs
            $fields = implode(',', array_keys($row));
            $values = '?' . str_repeat(',?', count($row) - 1);
            $insertQuery = 'insert ignore into ' . $request->input('table') . ' (' . $fields . ') values (' . $values . ')';
            \Log::debug($insertQuery);
            \Log::debug(implode(", ", array_values($row)));
            DB::insert($insertQuery, array_values($row));
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        return response()->json([], Response::HTTP_OK);
    }

    public function listImages($deviceId)
    {
        //the fields are fileName:<string>, deviceId:<string>, action:<string>, length:<number>,base64:<string/base64>. Note that base64 uses no linefeeds
        $validator = Validator::make(array_merge([], [
            'id' => $deviceId
        ]), [
            'id' => 'required|string|min:14|exists:device,device_id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };

        $returnArray = array();
        $adapter = new Local(storage_path() . '/respondent-photos');
        $filesystem = new Filesystem($adapter);

        $contents = $filesystem->listContents();

        foreach ($contents as $object) {
            if ($object['extension'] == "jpg") {
                $returnArray[] = array('fileName' => $object['path'], 'length' => $object['size']);
            }
        }

        return response()->json($returnArray, Response::HTTP_OK);
    }

    public function syncImages(Request $request, $deviceId)
    {
        //the fields are fileName:<string>, deviceId:<string>, action:<string>, length:<number>,base64:<string/base64>. Note that base64 uses no linefeeds
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $deviceId
        ]), [
            'id' => 'required|string|min:14|exists:device,device_id',
            'fileName' => 'required|string|min:1',
            'action' => 'required|string|min:1',
            'base64' => 'string'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };

        if ($request->input('action') == 'up') {
            // TODO: replace hard-coded directory with config / env variable
            $adapter = new Local(storage_path() . '/respondent-photos');
            $filesystem = new Filesystem($adapter);
            $data = base64_decode($request->input('base64'));
            $filesystem->put($request->input('fileName'), $data);
        } else {
            $adapter = new Local(storage_path() . '/respondent-photos');
            $filesystem = new Filesystem($adapter);
            $exists = $filesystem->has($request->input('fileName'));
            if ($exists) {
                $contents = $filesystem->read($request->input('fileName'));
                $size = $filesystem->getSize($request->input('fileName'));

                $base64 = base64_encode($contents);

                return response()->json([
                    'fileName' => $request->input('fileName'),
                    'device_id' => $deviceId,
                    'length' => $size,
                    'base64' => $base64],
                    Response::HTTP_OK);
            }
        }
    }

    public function uploadSync(Request $request, $deviceId)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $deviceId
        ]), [
            'id' => 'required|string|min:14|exists:device,device_id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };

        $tableColumnTypes = collect(DatabaseHelper::tables())->flip()->map(function ($index, $table) {
            return array_map(function ($attributes) {
                return $attributes['type'];
            }, DatabaseHelper::columns($table));
        })->toArray();

        app()->configure('snapshot');   // save overhead by only loading config when needed

        $tableRows = [];

        $insertsToTableRows = function ($inserts) use ($tableColumnTypes, &$tableRows) {
            $substitutions = config('snapshot.substitutions.upload');

            foreach ($inserts as $insert) {
                $table = $insert->into->dest->table;

                if (!$table || !count($insert->into->columns)) {
                    continue;   // require "insert into `table` (`field`) values ('value')" syntax where fields are specified
                }

                if (!isset($tableColumnTypes[$table])) {
                    continue;   // skip unknown tables  //TODO make this a whitelist
                }

                if (!isset($tableRows[$table])) {
                    $tableRows[$table] = [];
                }

                $fields = $insert->into->columns;
                $values = $insert->values[0]->values;

                foreach ($insert->values[0]->raw as $key => $raw) {
                    if (strtolower($raw) == 'null') {
                        $values[$key] = null;   // fix issue where null is parsed as "null" instead of null
                    }
                }

                $fieldValues = array_combine($fields, $values);

                // skip any blacklisted fields
                foreach (array_get($substitutions, $table, []) as $field => $substitution) {
                    if ($field == '*') {
                        $fieldValues = array_fill_keys(array_keys($fieldValues), $substitution);  // if wildcard, substitute all fields
                    } else {
                        $fieldValues[$field] = $substitution;
                    }
                }

                $fieldValues = array_filter($fieldValues, function ($value) {
                    return isset($value);
                }); // array_filter($fieldValues, 'isset');

                foreach ($fieldValues as $field => $value) {
                    if (!is_null($value)) {
                        if (in_array(array_get($tableColumnTypes[$table], $field), ['date', 'datetime', 'time', 'timestamp', 'year'])) {
                            $fieldValues[$field] = TimeHelper::utc($value); // ensure that timestamp/datetime is formatted properly for insertion
                        }
                    }
                }

                if (count($fieldValues)) {
                    $tableRows[$table] []= $fieldValues;
                }
            }
        };

        $getInsertStatements = function ($statements) use (&$getInsertStatements) {
            $inserts = [];

            foreach ($statements as $statement) {
                if (get_class($statement) == InsertStatement::class) {
                    $inserts []= $statement;
                }

                if (isset($statement->statements)) {
                    $insertStatements = $getInsertStatements($statement->statements);

                    if (count($insertStatements)) {
                        array_push($inserts, ...$insertStatements); // splat operator appends array to array
                    }
                }
            }

            return $inserts;
        };

        $processStatements = function ($string) use ($getInsertStatements, $insertsToTableRows) {
            $parser = new Parser($string);
            $insertStatements = $getInsertStatements($parser->statements);
            $insertsToTableRows($insertStatements);
        };

        $process = new Process('gunzip');

        $process->setInput(fopen('php://input', 'rb'));

        $delimiter = ";\n";
        $temp = '';
        $start = 0;

        $processOutput = function ($output) use ($delimiter, &$temp, &$start, $processStatements) {
            $temp .= $output;

            $end = strrpos($temp, $delimiter, $start);

            if ($end !== false) {
                $processStatements(substr($temp, 0, $end + strlen($delimiter)));

                $temp = substr($temp, $end + strlen($delimiter));
                $start = 0;
            } else {
                $start = strlen($temp) - (strlen($delimiter) - 1);
            }
        };

        $process->setTimeout(null)->run(function ($type, $output) use ($processOutput) {
            if ($type === Process::OUT) {
                $processOutput($output);
            }
        });

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $processOutput($delimiter);    // terminate stream with delimiter in case client did not.  final result is in $tableRows

        $totalWrites = DB::transaction(function () use ($tableRows) {
            $totalWrites = 0;

            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            foreach ($tableRows as $table => $rows) {
                foreach ($rows as $row) {
                    if (!is_null(Log::writeRow($row, $table))) {
                        $totalWrites++;
                    }
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS = 1');

            ob_start();

            Artisan::call('trellis:check:mysql');

            $result = json_decode(ob_get_clean(), true);

            if (count($result)) {
                throw new \Exception('Foreign key consistency check failed for the following tables: ' . implode(', ', array_keys($result)));
            }

            return $totalWrites;
        });

        if ($totalWrites > 0) {
            $epoch = Epoch::inc();    // increment epoch if any rows were written or logged

            Device::where('device_id', $deviceId)
                ->update([
                    'epoch' => $epoch
                ]);
        }

        return response()->json([], Response::HTTP_OK); // now <name_greater_than_or_equal_to_epoch>.sqlite.sql.zip must exist in order to download snapshot, otherwise client must wait for next snapshot to be generated
    }

    public function downloadSync(Request $request, $deviceId)
    {
        $validator = Validator::make(array_merge($request->all(), [
            'id' => $deviceId
        ]), [
            'id' => 'required|string|min:14|exists:device,device_id'
        ]);

        if ($validator->fails() === true) {
            return response()->json([
                'msg' => 'Validation failed',
                'err' => $validator->errors()
            ], $validator->statusCode());
        };

        Artisan::call('trellis:export:snapshot');

        app()->configure('snapshot');   // save overhead by only loading config when needed

        $snapshotDirPath = FileHelper::storagePath(config('snapshot.directory.path'));
        $files = glob("$snapshotDirPath/*");

        if (count($files)) {
            $files = array_combine($files, array_map("filemtime", $files));
            $newestFilePath = array_keys($files, max($files))[0];
            $newestFileName = basename($newestFilePath);
            $hex = explode('.', $newestFileName)[0];
            $snapshotEpoch = Epoch::dec($hex)*1;
            $deviceEpoch = Device::where('device_id', $deviceId)->first()->epoch;

            if ($snapshotEpoch >= $deviceEpoch) {
                return response()->download($newestFilePath, $newestFileName);   // if snapshot epoch >= than device's epoch, then return binary file download
            }
        }

        return response()->json([], Response::HTTP_ACCEPTED);   // if no snapshot epoch >= than device's epoch, then return 202 Accepted to make client retry later
    }
}
