<?php

namespace app\Http\Controllers;

use App\Library\DatabaseHelper;
use App\Library\FileHelper;
use App\Library\TimeHelper;
use App\Models\Device;
use App\Models\Epoch;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Laravel\Lumen\Routing\Controller;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Log;
use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Statements\InsertStatement;
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
            Log::debug($insertQuery);
            Log::debug(implode(", ", array_values($row)));
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

        $content = gzdecode($request->getContent());//file_get_contents('php://input'));	//TODO investigate inflating gzip (which is single-file, zip is file/folder) directly from php://input
        $parser = new Parser($content);

        // recursive method to get all inserts (even within other statements like transactions), "use (&$getInserts)" allows recursion
        $getInserts = function ($statements) use (&$getInserts) {
            $inserts = [];

            foreach ($statements as $statement) {
                if (get_class($statement) == InsertStatement::class) {
                    $inserts []= $statement;
                }

                if (isset($statement->statements)) {
                    $insertStatements = $getInserts($statement->statements);

                    if (count($insertStatements)) {
                        array_push($inserts, ...$insertStatements); // splat operator appends array to array
                    }
                }
            }

            return $inserts;
        };

        $inserts = $getInserts($parser->statements);

        $data = [];

        foreach ($inserts as $insert) {
            $table = $insert->into->dest->table;

            if (!$table || !count($insert->into->columns)) {
                continue;   // require "insert into `table` (`field`) values ('value')" syntax where fields are specified
            }

            if (!isset($data[$table])) {
                $data[$table] = [];
            }

            $fields = $insert->into->columns;
            $values = $insert->values[0]->values;

            foreach ($insert->values[0]->raw as $key => $raw) {
                if (strtolower($raw) == 'null') {
                    $values[$key] = null;   // fix issue where null is parsed as "null" instead of null
                }
            }

            $data[$table] []= array_combine($fields, $values);
        }

        // $insert = (new Parser("insert into `table` (`field`) values ('value')"))->statements[0];
        //
        // $insert = new \PhpMyAdmin\SqlParser\Statements\InsertStatement;
        // $insert->into = new \PhpMyAdmin\SqlParser\Components\IntoKeyword;
        // $insert->into->dest = new \PhpMyAdmin\SqlParser\Components\Expression;
        // $insert->into->dest->table = 'table';
        // $insert->into->columns = ['field1', 'field2'];
        // $insert->values = [new \PhpMyAdmin\SqlParser\Components\ArrayObj];
        // $insert->values[0]->values = ['value 1', 'value 2'];
        // dd($insert->build());   // doesn't quite work because values aren't quoted

        // $data = $request->json()->all();  // returns array of nested key-values [table: [field: value, ...], ...]

        $tableColumnTypes = collect(DatabaseHelper::tables())->flip()->map(function ($index, $table) {
            return array_map(function ($attributes) {
                return $attributes['type'];
            }, DatabaseHelper::columns($table));
        });

        DB::transaction(function () use ($request, $data, $tableColumnTypes) {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');

            foreach ($data as $table => $rows) {
                foreach ($rows as $row) {
                    if (!isset($tableColumnTypes[$table])) {
                        continue;   // skip unknown tables  //TODO make this a whitelist
                    }

                    foreach ($row as $field => $value) {
                        if (!is_null($value)) {
                            if (in_array(array_get($tableColumnTypes[$table], $field), ['date', 'datetime', 'time', 'timestamp', 'year'])) {
                                $row[$field] = TimeHelper::utc($value); // ensure that timestamp/datetime is formatted properly for insertion
                            }
                        }
                    }

                    $tableEscaped = DatabaseHelper::escape($table);
                    $fields = implode(',', array_map(DatabaseHelper::class . '::escape', array_keys($row)));
                    $values = implode(',', array_fill(0, count($row), '?'));
                    $insertQuery = <<<EOT
insert ignore into $tableEscaped (
    $fields
) values (
    $values
);
EOT
;    // use INSERT IGNORE to prevent duplicate inserts (this is generally safe since id is a UUID)   //TODO update this to use INSERT ... ON DUPLICATE KEY UPDATE with most recent wins strategy and logging previous values

                    // Log::debug($insertQuery);
                    // Log::debug(implode(", ", array_values($row)));

                    try {
                        DB::insert($insertQuery, array_values($row));
                    } catch (\Illuminate\Database\QueryException $e) {
                        if ($e->getCode() != "42S02") {
                            throw $e;
                        }   // ignore table doesn't exist, but throw all other errors
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
        });

        $epoch = Epoch::inc();

        Device::where('device_id', $deviceId)->update([
            'epoch' => $epoch
        ]); // update device's epoch.  <epoch>.sqlite.sql.zip must exist in order to download

        return response()->json([], Response::HTTP_OK);
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

        $snapshotPath = FileHelper::storagePath(config('snapshot.directory'));
        $files = glob("$snapshotPath/*");

        if (count($files)) {
            $files = array_combine($files, array_map("filemtime", $files));
            $newestFilePath = array_keys($files, max($files))[0];
            $newestFileName = basename($newestFilePath);
            $hex = explode('.', $newestFileName)[0];
            $snapshotEpoch = Epoch::dec($hex)*1;
            $deviceEpoch = Device::where('device_id', $deviceId)->first()->epoch;

            if ($snapshotEpoch >= $deviceEpoch) {
                return response()->download($newestFilePath);   // if snapshot epoch >= than device's epoch, then return binary file download
            }
        }

        return response()->json([], Response::HTTP_ACCEPTED);   // if no snapshot epoch >= than device's epoch, then return 202 Accepted to make client retry later
    }
}
