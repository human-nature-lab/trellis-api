start transaction;

set foreign_key_checks = 0;
set @ids_to_delete = (select group_concat(id) from (select min(id) as id from translation_text group by translation_id, locale_id having count(*) > 1) as temp_table);
delete from translation_text where find_in_set(id, @ids_to_delete);
delete from snapshot;
delete from upload;
delete from datum;
delete from question_datum;
delete from edge;
delete from respondent_condition_tag;
delete from form_condition_tag
delete from section_condition_tag;
delete from survey;
delete from interview;
set foreign_key_checks = 1;

commit;