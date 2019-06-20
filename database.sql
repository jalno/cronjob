CREATE TABLE `cronjob_tasks` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(255) NOT NULL,
 `process` varchar(255) NOT NULL,
 `parameters` text,
 `status` tinyint(4) NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `cronjob_schedules` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `task` int(11) NOT NULL,
 `year` smallint(6) DEFAULT NULL,
 `month` tinyint(4) DEFAULT NULL,
 `day` tinyint(4) DEFAULT NULL,
 `hour` tinyint(4) DEFAULT NULL,
 `minute` tinyint(4) DEFAULT NULL,
 PRIMARY KEY (`id`),
 KEY `task` (`task`),
 CONSTRAINT `cronjob_schedules_ibfk_1` FOREIGN KEY (`task`) REFERENCES `cronjob_tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `cronjob_runs` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `task` int(11) NOT NULL,
 `process` int(11) NOT NULL,
 PRIMARY KEY (`id`),
 KEY `task` (`task`),
 KEY `process` (`process`),
 CONSTRAINT `cronjob_runs_ibfk_1` FOREIGN KEY (`task`) REFERENCES `cronjob_tasks` (`id`) ON DELETE CASCADE,
 CONSTRAINT `cronjob_runs_ibfk_2` FOREIGN KEY (`process`) REFERENCES `base_processes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `userpanel_usertypes_permissions` (`type`, `name`) VALUES
  (1, 'cronjob_task_list'),
  (1, 'cronjob_task_edit'),
  (1, 'cronjob_delete'),
  (1, 'cronjob_create');