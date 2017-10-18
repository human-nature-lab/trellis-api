<?php

namespace Database\MigrationsEnd;

use DB;
use Illuminate\Console\Command;

//TODO remove this command once every user has Android Trellis version 2.1.4 or higher.  until then, run `php artisan migrate ` periodically
class UpdateRosterDatums extends Command
{
    // /**
    //  * The name and signature of the console command.
    //  *
    //  * @var string
    //  */
    protected $signature = 'Not registered in app/Console/Kernel.php';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::transaction(function () {
            // create datums with val = roster_parent for any roster questions that have been answered using the old system
            DB::unprepared(<<<'EOT'
insert into datum (
	id,
	name,
	val,
	choice_id,
	survey_id,
	question_id,
	repetition,
	parent_datum_id,
	datum_type_id,
	sort_order,
	created_at,
	updated_at,
	deleted_at
)
select distinct
	(select uuid()),
	'roster_variable',
	'roster_parent',
	null,
	datum.survey_id,
	question.id,
	0,
	null,
	0,
	null,
	(select now()),
	(select now()),
	null
from question
-- inner join any datum that doesn't have a parent
join datum on question.id = datum.question_id
and datum.parent_datum_id is null
and datum.val != 'roster_parent'
and question.question_type_id = (select id from question_type where name = 'roster' limit 1)
-- left join any datum from the same survey that is a parent
left join datum as parent_datum on datum.survey_id = parent_datum.survey_id
and datum.question_id = parent_datum.question_id
and parent_datum.parent_datum_id is null
and parent_datum.val = 'roster_parent'
and question.question_type_id = (select id from question_type where name = 'roster' limit 1)
-- filter so that only datums for which there is no parent datum in the same survey are selected
where parent_datum.id is null;
EOT
            );

            // set roster question child datum parent_datum_id to parent datums
            DB::unprepared(<<<'EOT'
update datum
join question on datum.question_id = question.id and question.question_type_id = (select id from question_type where name = 'roster' limit 1)
join datum as parent_datum on datum.survey_id = parent_datum.survey_id and datum.question_id = parent_datum.question_id and parent_datum.parent_datum_id is null and parent_datum.val = 'roster_parent' and datum.parent_datum_id is null and datum.val != 'roster_parent'
set datum.parent_datum_id = parent_datum.id;
EOT
            );
        });
    }
}
