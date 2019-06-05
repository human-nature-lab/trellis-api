insert ignore into role (id, name, can_delete, can_edit, created_at, updated_at) values
('admin', 'Admin', 0, 0, now(), now()),
('supervisor', 'Supervisor', 1, 1, now(), now()),
('surveyor', 'Surveyor', 1, 1, now(), now());