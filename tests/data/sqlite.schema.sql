CREATE TABLE `department` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `owners` integer  DEFAULT NULL
,  `name` varchar(191) DEFAULT NULL
);
CREATE TABLE `department_staff` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `department_id` integer  DEFAULT NULL
,  `staff_id` integer  DEFAULT NULL
,  UNIQUE (`department_id`,`staff_id`)
,  CONSTRAINT `c_fk_department_staff_department_id` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
,  CONSTRAINT `c_fk_department_staff_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE `language` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `code` varchar(191) DEFAULT NULL
,  `allowed` integer  DEFAULT NULL
,  `supported` integer  DEFAULT NULL
);
CREATE TABLE `log` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `type` varchar(191) DEFAULT NULL
,  `to` integer  DEFAULT NULL
,  `date` double DEFAULT NULL
,  `author_user_id` integer  DEFAULT NULL
,  CONSTRAINT `c_fk_log_author_user_id` FOREIGN KEY (`author_user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
);
CREATE TABLE `mailtemplate` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `type` varchar(191) DEFAULT NULL
,  `subject` varchar(191) DEFAULT NULL
,  `language` varchar(191) DEFAULT NULL
,  `body` text COLLATE BINARY
);
CREATE TABLE `sessioncookie` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `token` varchar(191) DEFAULT NULL
,  `ip` varchar(191) DEFAULT NULL
,  `creation_date` varchar(191) DEFAULT NULL
,  `user_id` integer  DEFAULT NULL
,  CONSTRAINT `c_fk_sessioncookie_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
);
CREATE TABLE `setting` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `name` varchar(191) DEFAULT NULL
,  `value` varchar(191) DEFAULT NULL
);
CREATE TABLE `staff` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `level` integer  DEFAULT NULL
,  `name` varchar(191) DEFAULT NULL
,  `email` varchar(191) DEFAULT NULL
,  `password` varchar(191) DEFAULT NULL
,  `profile_pic` varchar(191) DEFAULT NULL
,  `verification_token` integer  DEFAULT NULL
,  `last_login` double DEFAULT NULL
);
CREATE TABLE `stat` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `date` integer  DEFAULT NULL
,  `type` varchar(191) DEFAULT NULL
,  `general` integer  DEFAULT NULL
,  `value` integer  DEFAULT NULL
,  `staff_id` integer  DEFAULT NULL
,  CONSTRAINT `c_fk_stat_staff_id` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
);
CREATE TABLE `ticket` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `ticket_number` integer  DEFAULT NULL
,  `unread` integer  DEFAULT NULL
,  `priority` varchar(191) DEFAULT NULL
,  `unread_staff` integer  DEFAULT NULL
,  `title` varchar(191) DEFAULT NULL
,  `content` varchar(255) DEFAULT NULL
,  `language` varchar(191) DEFAULT NULL
,  `file` integer  DEFAULT NULL
,  `date` double DEFAULT NULL
,  `closed` integer  DEFAULT NULL
,  `author_email` varchar(191) DEFAULT NULL
,  `author_name` varchar(191) DEFAULT NULL
,  `department_id` integer  DEFAULT NULL
,  `author_id` integer  DEFAULT NULL
,  CONSTRAINT `c_fk_ticket_author_id` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
,  CONSTRAINT `c_fk_ticket_department_id` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
);
CREATE TABLE `ticket_user` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `ticket_id` integer  DEFAULT NULL
,  `user_id` integer  DEFAULT NULL
,  UNIQUE (`ticket_id`,`user_id`)
,  CONSTRAINT `c_fk_ticket_user_ticket_id` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
,  CONSTRAINT `c_fk_ticket_user_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE TABLE `user` (
  `id` integer  NOT NULL PRIMARY KEY AUTOINCREMENT
,  `email` varchar(191) DEFAULT NULL
,  `password` varchar(191) DEFAULT NULL
,  `name` varchar(191) DEFAULT NULL
,  `signup_date` double DEFAULT NULL
,  `tickets` integer  DEFAULT NULL
,  `verification_token` varchar(191) DEFAULT NULL
);
CREATE INDEX "idx_log_index_foreignkey_log_author_user" ON "log" (`author_user_id`);
CREATE INDEX "idx_stat_index_foreignkey_stat_staff" ON "stat" (`staff_id`);
CREATE INDEX "idx_ticket_user_index_foreignkey_ticket_user_ticket" ON "ticket_user" (`ticket_id`);
CREATE INDEX "idx_ticket_user_index_foreignkey_ticket_user_user" ON "ticket_user" (`user_id`);
CREATE INDEX "idx_department_staff_index_foreignkey_department_staff_department" ON "department_staff" (`department_id`);
CREATE INDEX "idx_department_staff_index_foreignkey_department_staff_staff" ON "department_staff" (`staff_id`);
CREATE INDEX "idx_sessioncookie_index_foreignkey_sessioncookie_user" ON "sessioncookie" (`user_id`);
CREATE INDEX "idx_ticket_index_foreignkey_ticket_department" ON "ticket" (`department_id`);
CREATE INDEX "idx_ticket_index_foreignkey_ticket_author" ON "ticket" (`author_id`);
