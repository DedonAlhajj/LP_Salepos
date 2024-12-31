

CREATE TABLE `accounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `account_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `initial_balance` double DEFAULT NULL,
  `total_balance` double NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `is_default` tinyint(1) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO accounts VALUES("1","11111","Sales Account","1000","1000","this is first account","0","1","2018-12-18 08:58:02","2023-07-09 11:37:23");
INSERT INTO accounts VALUES("3","21211","Sa","","0","","0","1","2018-12-18 08:58:56","2023-07-09 11:37:26");
INSERT INTO accounts VALUES("5","bank-1","zuhair","100000","100000","","","0","2022-11-28 11:58:18","2023-07-09 11:38:00");
INSERT INTO accounts VALUES("6","DBBL-1995","Irfan Chowdhury","20000","20000","This is for testing purpose","1","1","2023-07-09 11:36:48","2023-07-09 11:37:26");



CREATE TABLE `adjustments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warehouse_id` int NOT NULL,
  `document` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_qty` double NOT NULL,
  `item` int NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO adjustments VALUES("13","adr-20230401-093050","1","","3","1","","2023-04-01 21:30:50","2023-04-01 21:30:50");
INSERT INTO adjustments VALUES("16","adr-20230710-032127","2","","1","1","Unde cillum suscipit","2023-07-10 15:21:27","2023-07-10 15:21:27");



CREATE TABLE `attendances` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `employee_id` int NOT NULL,
  `user_id` int NOT NULL,
  `checkin` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `checkout` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO attendances VALUES("1","2019-01-02","1","1","10:00am","6:30pm","1","","2019-01-02 09:30:50","2019-01-02 09:30:50");
INSERT INTO attendances VALUES("6","2020-02-03","1","1","11:30am","6:00pm","0","","2020-02-03 15:57:30","2020-02-03 15:57:30");
INSERT INTO attendances VALUES("7","2023-07-09","4","1","10:00am","6:00pm","1","Test","2023-07-09 15:26:10","2023-07-09 15:26:10");



CREATE TABLE `billers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vat_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO billers VALUES("1","yousuf","aks.jpg","aks","31123","yousuf@kds.com","442343324","halishahar","chittagong","","","sdgs","1","2018-05-13 03:49:30","2019-03-02 11:20:38");
INSERT INTO billers VALUES("2","tariq","","big tree","","tariq@bigtree.com","321312","khulshi","chittagong","","","","1","2018-05-13 03:57:54","2018-06-15 06:07:11");
INSERT INTO billers VALUES("3","test","","test","","test@test.com","3211","erewrwqre","afsf","","","","0","2018-05-30 08:38:58","2018-05-30 08:39:57");
INSERT INTO billers VALUES("5","modon","mogaTel.jpg","mogaTel","","modon@gmail.com","32321","nasirabad","chittagong","","","bd","1","2018-09-01 09:59:54","2018-10-07 08:35:51");
INSERT INTO billers VALUES("6","a","","a","","a@a.com","q","q","q","","","","0","2018-10-07 08:33:39","2018-10-07 08:34:18");
INSERT INTO billers VALUES("7","a","","a","","a@a.com","a","a","a","","","","0","2018-10-07 08:34:36","2018-10-07 08:36:07");
INSERT INTO billers VALUES("8","x","20230710015706.jpg","x","","x@x.com","x","x","x","","","","1","2019-03-18 17:02:42","2023-07-10 13:57:06");
INSERT INTO billers VALUES("22","asdad345","","ssdfsfs","","ashfaqdev.php@gmail.com","5353453","dsdsa","SFSDFSDF","","","","0","2023-05-03 12:21:51","2023-05-06 16:00:20");
INSERT INTO billers VALUES("23","Test","20230710015718.jpg","lioncoders","5534","irfanchowdhury80@gmail.com","654654","hfghg","hfghfg","fghfgh","6546","Bangladesh","1","2023-07-09 16:26:55","2023-07-10 13:57:18");
INSERT INTO billers VALUES("24","Regina Velazquez","20230710015551.jpg","Adkins Dale Inc","757","camezyryl@mailinator.com","+1 (917) 298-3092","Impedit adipisicing","Quas eius aperiam ni","Qui esse saepe illum","Laboris consequat C","Corporis aperiam non","0","2023-07-10 13:52:45","2023-07-10 13:56:45");



CREATE TABLE `brands` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO brands VALUES("3","HP","HP.jpg","1","2018-05-12 15:06:14","2019-03-02 11:32:21");
INSERT INTO brands VALUES("4","samsung","samsung.jpg","1","2018-05-12 15:08:41","2018-07-04 09:38:37");
INSERT INTO brands VALUES("5","Apple","Apple.jpg","1","2018-09-01 05:34:49","2018-12-06 09:05:38");
INSERT INTO brands VALUES("6","jjjj","20201019093419.jpg","0","2020-10-19 21:33:52","2020-10-19 21:35:58");
INSERT INTO brands VALUES("7","Lotto","","1","2020-11-16 10:13:41","2020-11-16 10:13:41");
INSERT INTO brands VALUES("8","cocacola","","0","2022-01-12 19:26:22","2022-01-12 19:26:30");
INSERT INTO brands VALUES("9","pepsi","20220112072659.jpg","0","2022-01-12 19:26:59","2022-01-12 19:27:07");



CREATE TABLE `cash_registers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cash_in_hand` double NOT NULL,
  `user_id` int NOT NULL,
  `warehouse_id` int NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO cash_registers VALUES("1","100","9","1","0","2020-10-13 13:32:54","2020-10-24 06:27:42");
INSERT INTO cash_registers VALUES("2","50","9","1","1","2020-10-13 21:25:26","2020-10-13 21:25:26");
INSERT INTO cash_registers VALUES("3","200","1","1","0","2020-10-22 13:53:07","2021-06-19 10:52:48");
INSERT INTO cash_registers VALUES("4","100","1","2","0","2020-10-24 07:04:39","2022-03-12 20:18:24");
INSERT INTO cash_registers VALUES("5","1000","1","1","0","2021-06-19 10:53:22","2022-03-12 20:18:31");
INSERT INTO cash_registers VALUES("6","120","1","2","1","2022-03-31 11:29:13","2022-03-31 11:29:13");
INSERT INTO cash_registers VALUES("7","50000","1","1","1","2023-02-15 00:11:12","2023-02-15 00:11:12");



CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO categories VALUES("1","Fruits","","9","0","2018-05-12 09:27:25","2023-07-10 15:24:20");
INSERT INTO categories VALUES("2","electronics","","","1","2018-05-12 09:35:57","2019-03-01 21:07:21");
INSERT INTO categories VALUES("3","computer","20200701093146.jpg","2","1","2018-05-12 09:36:08","2020-07-01 21:31:46");
INSERT INTO categories VALUES("4","toy","","","0","2018-05-24 04:57:48","2023-07-10 14:21:48");
INSERT INTO categories VALUES("7","jacket","","","0","2018-05-28 04:39:51","2018-05-28 04:40:48");
INSERT INTO categories VALUES("9","food","","","1","2018-06-25 07:21:40","2018-09-03 09:41:28");
INSERT INTO categories VALUES("10","anda","","","0","2018-08-29 05:36:31","2018-08-29 05:37:34");
INSERT INTO categories VALUES("11","anda","","","0","2018-08-29 05:48:06","2018-08-29 05:53:22");
INSERT INTO categories VALUES("12","accessories","","","1","2018-12-05 05:28:53","2019-04-10 10:17:03");
INSERT INTO categories VALUES("14","nn","","","0","2019-04-10 10:22:30","2019-04-10 11:38:47");
INSERT INTO categories VALUES("15","nm","","","0","2019-04-10 10:22:36","2019-04-10 11:41:43");
INSERT INTO categories VALUES("16","desktop","","3","1","2020-03-11 16:42:33","2020-03-11 16:42:33");
INSERT INTO categories VALUES("17","tostos","20200701080042.png","","0","2020-07-01 20:00:42","2020-07-01 21:35:34");
INSERT INTO categories VALUES("19","Paracetamol","","","0","2021-03-07 13:16:01","2023-07-10 14:24:03");
INSERT INTO categories VALUES("20","Automobile","","","1","2021-07-15 18:35:12","2021-07-15 18:35:12");
INSERT INTO categories VALUES("21","Productos Caballero","","","0","2022-10-15 21:42:44","2023-07-10 14:22:15");
INSERT INTO categories VALUES("22","Nora Mccray Test","","4","0","2023-07-08 11:01:19","2023-07-08 11:05:55");
INSERT INTO categories VALUES("23","Test 1","","9","0","2023-07-11 12:21:37","2023-07-11 12:21:53");
INSERT INTO categories VALUES("24","Test 2","","9","0","2023-07-11 12:21:37","2023-07-11 12:21:53");



CREATE TABLE `coupons` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double NOT NULL,
  `minimum_amount` double DEFAULT NULL,
  `quantity` int NOT NULL,
  `used` int NOT NULL,
  `expired_date` date NOT NULL,
  `user_id` int NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO coupons VALUES("1","sonar bangla","percentage","20","0","100","4","2020-11-19","1","1","2018-10-26 04:38:50","2020-11-18 06:52:39");
INSERT INTO coupons VALUES("2","i love bangladesh","fixed","200","1000","50","1","2018-12-31","1","1","2018-10-27 08:59:26","2019-03-02 11:46:48");



CREATE TABLE `currencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exchange_rate` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO currencies VALUES("1","US Dollar","USD","1","2020-11-01 06:22:58","2023-04-02 15:51:28");
INSERT INTO currencies VALUES("2","Euro","Euro","0.95","2020-11-01 07:29:12","2023-06-08 16:10:32");



CREATE TABLE `custom_fields` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `belongs_to` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_value` text COLLATE utf8mb4_unicode_ci,
  `option_value` text COLLATE utf8mb4_unicode_ci,
  `grid_value` int NOT NULL,
  `is_table` tinyint(1) NOT NULL,
  `is_invoice` tinyint(1) NOT NULL,
  `is_required` tinyint(1) NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  `is_disable` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO custom_fields VALUES("1","sale","Test","text","Test","","3","1","0","1","0","0","2023-07-11 12:02:46","2023-07-11 12:02:46");



CREATE TABLE `customer_groups` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `percentage` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO customer_groups VALUES("1","general","0","1","2018-05-12 14:09:36","2019-03-02 12:01:35");
INSERT INTO customer_groups VALUES("2","distributor","-10","1","2018-05-12 14:12:14","2019-03-02 12:02:12");
INSERT INTO customer_groups VALUES("3","reseller","5","1","2018-05-12 14:12:26","2018-05-30 07:18:14");
INSERT INTO customer_groups VALUES("4","test","12","0","2018-05-30 07:17:16","2018-05-30 07:17:57");
INSERT INTO customer_groups VALUES("5","test","0","0","2018-08-03 15:10:27","2018-08-03 15:10:34");



CREATE TABLE `customers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_group_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tax_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `points` double DEFAULT NULL,
  `deposit` double DEFAULT NULL,
  `expense` double DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO customers VALUES("1","1","22","dhiman","lioncoders","dhiman@gmail.com","+8801111111101","","kajir deuri","chittagong","","","bd","11","190","20","1","2018-05-12 16:00:48","2021-08-08 20:39:20");
INSERT INTO customers VALUES("2","2","","moinul","lioncoders","","+8801200000001","","jamalkhan","chittagong","","","bd","","100","20","1","2018-05-12 16:04:51","2019-02-22 11:38:08");
INSERT INTO customers VALUES("3","3","","Tariq","big tree","irfanchowdhury43@gmail.com","3424","54654","khulshi","chittagong","Chittagong","43343","bd","","","","1","2018-05-12 16:07:52","2023-07-10 15:55:08");
INSERT INTO customers VALUES("4","1","","test","","","4234","","frwerw","qwerwqr","","","","","","","0","2018-05-30 07:35:28","2018-05-30 07:37:38");
INSERT INTO customers VALUES("8","1","","anwar","smart it","anwar@smartit.com","32321","","nasirabad","chittagong","","","bd","","","","0","2018-09-01 09:26:13","2018-09-01 09:29:55");
INSERT INTO customers VALUES("11","1","","walk-in-customer","","","01923000001","11111","mohammadpur","dhaka","","","","321","","0","1","2018-09-02 07:30:54","2023-05-06 16:06:46");
INSERT INTO customers VALUES("15","1","","s","","","2","","s","3e","","","","","","","0","2018-11-05 10:00:39","2018-11-08 09:37:08");
INSERT INTO customers VALUES("16","1","","asas","","","2121","","dasd","asdd","","","","","","","0","2018-12-01 06:07:53","2018-12-04 03:55:46");
INSERT INTO customers VALUES("17","1","","sadman","","","312312","","khulshi","ctg","","","","","","","0","2020-06-22 15:45:35","2020-06-22 15:45:51");
INSERT INTO customers VALUES("19","1","19","Ashfaq","Digital image","shakalaka@gmail.com","1212","999","Andorkillah","Chittagong","Chittagong","1234","Bangladesh","6","","","1","2020-11-09 06:07:16","2021-10-14 15:58:20");
INSERT INTO customers VALUES("21","1","21","Modon Miya","modon company","modon@gmail.com","2222","","kuril road","Dhaka","","","","","","","1","2020-11-13 13:12:11","2020-11-13 13:12:11");
INSERT INTO customers VALUES("25","1","28","Imran miya","","imran@gmail.com","01923000001","","kljkj","hhjhh","","","","","","","0","2021-02-04 12:26:47","2021-02-04 12:26:47");
INSERT INTO customers VALUES("35","1","","Debu","","","32423","","dsfs","fsdfs","","","","","","","0","2023-03-13 16:11:21","2023-07-09 16:24:11");
INSERT INTO customers VALUES("44","1","","Irfan Chowdhury","Lion Coders","irfanchowdhury80@gmail.com","23123434","42342","Chittagong","Chittagong","Chittagong","4544","Bangladesh","","50","","1","2023-07-09 16:19:33","2023-07-10 15:55:26");



CREATE TABLE `deliveries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivered_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recieved_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO deliveries VALUES("1","dr-20180808-044431","1","1","kajir deuri chittagong bd","abul","dhiman","","Instrument 1","3","2018-08-08 16:44:55","2020-09-26 18:25:39");
INSERT INTO deliveries VALUES("2","dr-20181106-105936","88","1","mohammadpur dhaka","","","","","2","2018-11-06 10:59:43","2018-11-06 11:10:38");
INSERT INTO deliveries VALUES("3","dr-20181106-111321","79","1","mohammadpur dhaka","Harun","Amjad","","","3","2018-11-06 11:13:25","2020-09-26 19:22:11");
INSERT INTO deliveries VALUES("5","dr-20211218-113624","354","1","mohammadpur dhaka","","","","","2","2021-12-18 11:36:30","2021-12-18 11:36:30");
INSERT INTO deliveries VALUES("7","dr-20230708-043511","473","1","mohammadpur dhaka","irfan","juhair","dr-20230708-043511.png","test","1","2023-07-08 16:35:55","2023-07-08 16:35:55");



CREATE TABLE `departments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO departments VALUES("1","Sale","1","2018-12-27 11:16:47","2018-12-27 16:40:23");
INSERT INTO departments VALUES("2","xyz","1","2018-12-27 16:28:47","2018-12-27 16:28:47");
INSERT INTO departments VALUES("3","Marketing","1","2023-07-09 14:45:13","2023-07-09 14:45:13");
INSERT INTO departments VALUES("4","Kimberly Guy","0","2023-07-10 14:23:34","2023-07-10 14:23:42");
INSERT INTO departments VALUES("5","Test","1","2023-07-11 12:01:18","2023-07-11 12:01:18");



CREATE TABLE `deposits` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `amount` double NOT NULL,
  `customer_id` int NOT NULL,
  `user_id` int NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO deposits VALUES("1","90","1","1","first deposit","2018-08-26 04:48:23","2018-08-26 07:18:55");
INSERT INTO deposits VALUES("3","100","2","1","","2018-08-26 06:53:16","2018-08-27 03:42:39");
INSERT INTO deposits VALUES("4","50","1","1","","2018-09-05 04:56:19","2018-09-05 04:56:19");
INSERT INTO deposits VALUES("5","50","1","1","","2018-09-10 06:08:40","2018-09-10 06:08:40");
INSERT INTO deposits VALUES("6","100","41","1","","2023-04-29 12:39:32","2023-04-29 12:39:32");
INSERT INTO deposits VALUES("8","50","44","1","test","2023-07-09 16:23:39","2023-07-09 16:23:39");



CREATE TABLE `discount_plan_customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `discount_plan_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO discount_plan_customers VALUES("1","1","1","2022-02-03 18:58:12","2022-02-03 18:58:12");
INSERT INTO discount_plan_customers VALUES("2","1","19","2022-02-03 18:58:12","2022-02-03 18:58:12");
INSERT INTO discount_plan_customers VALUES("3","1","3","2022-12-06 18:07:17","2022-12-06 18:07:17");
INSERT INTO discount_plan_customers VALUES("4","2","21","2023-07-11 11:06:45","2023-07-11 11:06:45");
INSERT INTO discount_plan_customers VALUES("5","2","44","2023-07-11 11:06:45","2023-07-11 11:06:45");



CREATE TABLE `discount_plan_discounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `discount_id` int NOT NULL,
  `discount_plan_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO discount_plan_discounts VALUES("1","1","1","2022-02-03 19:02:17","2022-02-03 19:02:17");



CREATE TABLE `discount_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO discount_plans VALUES("1","VIP plan","1","2022-02-03 18:58:12","2022-02-03 18:58:12");
INSERT INTO discount_plans VALUES("2","winter2023","1","2023-07-11 11:06:45","2023-07-11 11:07:27");



CREATE TABLE `discounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `applicable_for` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_list` longtext COLLATE utf8mb4_unicode_ci,
  `valid_from` date NOT NULL,
  `valid_till` date NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` double NOT NULL,
  `minimum_qty` double NOT NULL,
  `maximum_qty` double NOT NULL,
  `days` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO discounts VALUES("1","10% discount","Specific","5,3","2022-02-03","2023-12-31","percentage","10","2","3","Mon,Tue,Wed,Thu,Fri,Sat,Sun","1","2022-02-03 19:02:17","2023-05-06 20:19:30");



CREATE TABLE `dso_alerts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_info` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `number_of_products` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO dso_alerts VALUES("1","[{"name":"Baby doll","code":"31261512"}]","1","2022-05-28 11:27:46","2022-05-28 11:27:46");



CREATE TABLE `employees` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO employees VALUES("1","john","john@gmail.com","10001","2","12","20230710115123.jpg","GEC","chittagong","Bangladesh","0","2018-12-30 06:48:37","2023-07-10 12:41:29");
INSERT INTO employees VALUES("3","tests","test@test.com","111","1","","","","","","1","2018-12-31 04:20:51","2019-01-03 06:03:54");
INSERT INTO employees VALUES("4","Fahim Chowdhury","irfanchowdhury80@gmail.com","01829498634","1","41","20230709025541.png","Chowmohoni","Chittagong","Bangladesh","1","2023-07-09 14:55:41","2023-07-09 14:55:41");
INSERT INTO employees VALUES("5","Susan Morgan","mybit@mailinator.com","+1 (808) 195-8703","2","","20230710124256.jpg","Dolores ad dolor com","Est quis neque delen","Et voluptatem qui er","0","2023-07-10 12:42:56","2023-07-10 13:05:37");
INSERT INTO employees VALUES("6","Drew Francis","bogibytom@mailinator.com","+1 (226) 806-3892","3","","20230710010226.jpg","Sint perferendis sae","Quia placeat vel un","Eaque quisquam optio","0","2023-07-10 13:02:26","2023-07-10 13:03:43");
INSERT INTO employees VALUES("7","Kylynn Bowen","zonosiqy@mailinator.com","+1 (199) 415-2711","3","","20230710010452.jpg","Assumenda officiis n","Culpa exercitation o","Amet aspernatur qui","0","2023-07-10 13:04:52","2023-07-10 13:05:07");
INSERT INTO employees VALUES("8","Kaden Tyler","cuxegy@mailinator.com","+1 (619) 971-5507","2","","20230710010521.jpg","Explicabo Irure vol","Nesciunt ad natus d","Eos ducimus explica","0","2023-07-10 13:05:21","2023-07-10 13:05:37");
INSERT INTO employees VALUES("9","Elton Ingram","vufoboxok@mailinator.com","+1 (309) 222-7887","3","","","Aut rerum dolore min","Dolores sit dignissi","Sunt modi aut rerum","0","2023-07-10 13:06:02","2023-07-10 13:19:48");
INSERT INTO employees VALUES("10","Kelly Cross","qytanoroq@mailinator.com","+1 (314) 926-1161","1","","20230710010616.jpg","Error officia quod u","Omnis molestiae laud","Minim aut libero nih","0","2023-07-10 13:06:16","2023-07-10 13:06:39");
INSERT INTO employees VALUES("11","Mia Torres","puqot@mailinator.com","+1 (875) 298-7612","2","","20230710011024.jpg","Est adipisicing dolo","Voluptate vitae alia","Qui dolor ut et nihi","1","2023-07-10 13:10:24","2023-07-10 13:10:24");
INSERT INTO employees VALUES("12","Brittany Stark","voje@mailinator.com","+1 (472) 158-5653","2","","20230710011102.jpg","Ut voluptatem Eius","Mollitia qui error q","In quia reiciendis s","0","2023-07-10 13:11:02","2023-07-10 13:11:43");
INSERT INTO employees VALUES("13","Doris Curry","hyzuvagope@mailinator.com","+1 (618) 861-2596","2","","20230710011312.jpg","Eum magni voluptatem","Ea quod sunt cupidat","Quis deserunt incidu","0","2023-07-10 13:13:12","2023-07-10 13:13:19");
INSERT INTO employees VALUES("14","Dieter Bass","satefoguvi@mailinator.com","+1 (149) 155-3496","3","","","Nobis maiores atque","Molestiae animi eli","Cupidatat vitae natu","0","2023-07-10 13:13:44","2023-07-10 13:13:50");
INSERT INTO employees VALUES("15","Lisandra Petersen","falaxuni@mailinator.com","+1 (934) 624-8173","2","","20230710011406.jpg","Perspiciatis dolore","Id rerum distinctio","Qui nostrud eu ea ex","0","2023-07-10 13:14:06","2023-07-10 13:14:42");
INSERT INTO employees VALUES("16","Ashton Holt","niqojaxywu@mailinator.com","+1 (219) 159-3886","2","","20230710011421.jpg","Quas ipsam iure reru","Eu et totam iusto iu","Ut sed laboris dolor","0","2023-07-10 13:14:21","2023-07-10 13:14:42");
INSERT INTO employees VALUES("17","Noel Solis","telybebyb@mailinator.com","+1 (597) 543-5372","3","44","20230710011612.jpg","Sunt omnis vero volu","Ea dolores qui magna","Proident corrupti","0","2023-07-10 13:16:12","2023-07-10 13:16:38");
INSERT INTO employees VALUES("18","Kirestin Fowler","caba@mailinator.com","+1 (858) 491-1696","2","","20230710011624.jpg","Eveniet ut hic mole","Sint molestiae faci","Ullam voluptas dolor","0","2023-07-10 13:16:24","2023-07-10 13:16:38");
INSERT INTO employees VALUES("19","Grant Singleton","codegako@mailinator.com","+1 (319) 808-5154","1","","20230710011929.jpg","Dolores dolores aute","Ipsa tenetur repell","Sequi quia nostrum i","0","2023-07-10 13:19:29","2023-07-10 13:20:13");
INSERT INTO employees VALUES("20","Florence Freeman","xewy@mailinator.com","+1 (646) 659-2641","3","45","20230710012001.jpg","Natus eius eiusmod d","Quas quia doloremque","Assumenda sit dolor","0","2023-07-10 13:20:01","2023-07-10 13:21:15");
INSERT INTO employees VALUES("21","Imani Herman","viqovony@mailinator.com","+1 (331) 387-1219","1","46","20230710012135.jpg","Dolore culpa suscip","Fugiat dolorum expli","Maxime sed non dolor","0","2023-07-10 13:21:35","2023-07-10 13:22:01");
INSERT INTO employees VALUES("22","Faith Buchanan","hipubeneni@mailinator.com","+1 (208) 494-7622","3","","20230710012147.jpg","Quod enim irure dolo","Sit deserunt velit","Vel illum asperiore","0","2023-07-10 13:21:47","2023-07-10 13:22:01");
INSERT INTO employees VALUES("23","Keelie Wolf","naguf@mailinator.com","+1 (565) 711-4018","3","","20230710013724.jpg","Tempor provident al","Rerum quia repudiand","Mollit eum dolor fug","0","2023-07-10 13:36:59","2023-07-10 13:39:39");



CREATE TABLE `expense_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO expense_categories VALUES("1","16718342","washing","1","2018-08-16 06:32:48","2019-03-02 13:02:07");
INSERT INTO expense_categories VALUES("2","60128975","electric bill","1","2018-08-16 06:39:18","2018-08-16 06:39:18");
INSERT INTO expense_categories VALUES("3","83954970","test","0","2018-08-16 06:50:28","2018-08-16 06:51:40");
INSERT INTO expense_categories VALUES("4","1234","snacks","1","2018-09-01 08:40:04","2018-09-01 08:40:04");
INSERT INTO expense_categories VALUES("5","test123","Test 123","1","2023-07-09 10:06:14","2023-07-09 10:06:14");
INSERT INTO expense_categories VALUES("6","87211567","Eight Seven","0","2023-07-09 10:06:41","2023-07-09 10:09:34");
INSERT INTO expense_categories VALUES("7","75495","Test 95","1","2023-07-09 10:08:27","2023-07-09 10:08:27");



CREATE TABLE `expenses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expense_category_id` int NOT NULL,
  `warehouse_id` int NOT NULL,
  `account_id` int NOT NULL,
  `user_id` int NOT NULL,
  `cash_register_id` int DEFAULT NULL,
  `amount` double NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO expenses VALUES("42","er-20220529-033101","1","1","1","1","","100","","2022-05-27 00:00:00","2022-05-29 15:31:01");
INSERT INTO expenses VALUES("43","er-20220529-033133","2","2","1","1","6","200","fbfbxbc","2022-05-29 00:00:00","2022-05-29 15:32:09");
INSERT INTO expenses VALUES("44","er-20220606-051446","2","2","1","1","6","350","","2022-06-06 17:14:46","2022-06-06 17:14:46");
INSERT INTO expenses VALUES("45","er-20220809-115624","2","1","1","1","","200","","2022-08-09 11:56:24","2022-08-09 11:56:24");
INSERT INTO expenses VALUES("46","er-20220901-123715","2","2","1","1","6","200","","2022-09-01 12:37:15","2022-09-01 12:37:15");
INSERT INTO expenses VALUES("47","er-20220915-085925","2","2","1","1","6","50","","2022-09-15 20:59:25","2022-09-15 20:59:25");
INSERT INTO expenses VALUES("48","er-20221002-081726","4","1","1","1","","100","","2022-10-02 20:17:26","2022-10-02 20:17:26");
INSERT INTO expenses VALUES("49","er-20221018-021106","2","2","1","1","6","230","","2022-10-18 14:11:06","2022-10-18 14:11:06");
INSERT INTO expenses VALUES("50","er-20221211-025348","2","2","1","1","6","100","","2022-12-11 14:53:48","2022-12-11 14:53:48");
INSERT INTO expenses VALUES("53","er-20230405-013950","1","2","1","1","6","500","","2023-04-05 13:39:50","2023-04-05 13:39:50");
INSERT INTO expenses VALUES("54","er-20230506-040751","2","2","1","1","6","500","","2023-05-06 16:07:51","2023-05-06 16:07:51");
INSERT INTO expenses VALUES("55","er-20230604-034915","2","2","1","1","6","200","","2023-06-04 15:49:15","2023-06-04 15:49:15");
INSERT INTO expenses VALUES("57","er-20230709-101303","2","1","1","1","7","1500","Test","2023-07-10 00:00:00","2023-07-09 10:13:03");



CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




CREATE TABLE `general_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `site_title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_logo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_rtl` tinyint(1) DEFAULT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `package_id` int DEFAULT NULL,
  `staff_access` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_format` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `developed_by` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_format` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `decimal` int DEFAULT '2',
  `state` int DEFAULT NULL,
  `theme` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `currency_position` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `is_zatca` tinyint(1) DEFAULT NULL,
  `company_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vat_registration_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO general_settings VALUES("1","SalePros","20220905125905.png","0","1","","own","d-m-Y","LionCoders","standard","2","1","default.css","2018-07-06 12:13:11","2023-06-16 19:42:32","prefix","","0","","980980");



CREATE TABLE `gift_card_recharges` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `gift_card_id` int NOT NULL,
  `amount` double NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO gift_card_recharges VALUES("1","2","100","1","2018-08-25 05:08:29","2018-08-25 05:08:29");
INSERT INTO gift_card_recharges VALUES("2","1","200","1","2018-08-25 05:08:50","2018-08-25 05:08:50");
INSERT INTO gift_card_recharges VALUES("3","1","100","1","2018-09-05 05:50:41","2018-09-05 05:50:41");
INSERT INTO gift_card_recharges VALUES("4","1","50","1","2018-09-05 05:51:38","2018-09-05 05:51:38");
INSERT INTO gift_card_recharges VALUES("5","1","50","1","2018-09-05 05:53:36","2018-09-05 05:53:36");
INSERT INTO gift_card_recharges VALUES("6","2","50","1","2018-09-05 05:54:34","2018-09-05 05:54:34");
INSERT INTO gift_card_recharges VALUES("7","5","10","1","2018-09-30 06:19:48","2018-09-30 06:19:48");
INSERT INTO gift_card_recharges VALUES("8","5","10","1","2018-09-30 06:20:04","2018-09-30 06:20:04");
INSERT INTO gift_card_recharges VALUES("9","2","100","1","2018-10-07 09:13:05","2018-10-07 09:13:05");
INSERT INTO gift_card_recharges VALUES("10","1","200","1","2018-10-07 09:13:39","2018-10-07 09:13:39");
INSERT INTO gift_card_recharges VALUES("11","1","300","1","2018-10-23 06:22:49","2018-10-23 06:22:49");
INSERT INTO gift_card_recharges VALUES("12","2","130","1","2021-02-11 13:36:01","2021-02-11 13:36:01");
INSERT INTO gift_card_recharges VALUES("13","2","155","1","2023-07-08 16:39:25","2023-07-08 16:39:25");



CREATE TABLE `gift_cards` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `card_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double NOT NULL,
  `expense` double NOT NULL,
  `customer_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `expired_date` date DEFAULT NULL,
  `created_by` int NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO gift_cards VALUES("1","3571097513020486","1400","450","1","","2020-12-31","1","1","2018-08-18 07:57:40","2020-10-18 11:14:26");
INSERT INTO gift_cards VALUES("2","0452297501931931","655","100","2","","2021-12-31","1","1","2018-08-18 08:46:43","2023-07-08 16:39:25");
INSERT INTO gift_cards VALUES("3","123","13123","0","1","","2018-08-19","1","0","2018-08-19 04:38:21","2018-08-19 04:38:28");
INSERT INTO gift_cards VALUES("4","1862381252690499","100","0","","1","2018-10-04","1","0","2018-09-30 06:16:28","2018-09-30 06:17:21");
INSERT INTO gift_cards VALUES("5","2300813717254199","143","0","","1","2018-10-04","1","0","2018-09-30 06:18:49","2018-09-30 06:20:20");
INSERT INTO gift_cards VALUES("6","8327019475026421","1","0","1","","2018-10-07","1","0","2018-10-07 09:12:41","2018-10-07 09:12:55");
INSERT INTO gift_cards VALUES("7","2063379780590151","1","0","1","","2018-10-23","1","0","2018-10-23 06:23:22","2018-10-23 06:23:39");



CREATE TABLE `holidays` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `is_approved` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO holidays VALUES("1","1","2023-07-09","2023-07-09","Test","1","2023-07-09 15:31:58","2023-07-09 15:31:58");
INSERT INTO holidays VALUES("2","1","2023-07-12","2023-07-14","Gov Holiday","1","2023-07-09 15:32:44","2023-07-09 15:33:49");



CREATE TABLE `hrm_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `checkin` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `checkout` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO hrm_settings VALUES("1","10:00am","6:00pm","2019-01-02 08:20:08","2019-01-02 10:20:53");



CREATE TABLE `languages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO languages VALUES("1","en","2018-07-08 04:59:17","2019-12-24 23:34:20");



CREATE TABLE `mail_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `driver` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `host` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `port` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `encryption` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO mail_settings VALUES("2","smtp","smtp.gmail.com","587","irfanchowdhury80@gmail.com","Irfan","irfanchowdhury80@gmail.com","ksmbqskmnzvzxnhx","tls","2023-07-10 15:53:50","2023-07-10 15:53:50");



CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=179 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO migrations VALUES("1","2014_10_12_000000_create_users_table","1");
INSERT INTO migrations VALUES("2","2014_10_12_100000_create_password_resets_table","1");
INSERT INTO migrations VALUES("3","2018_02_17_060412_create_categories_table","1");
INSERT INTO migrations VALUES("4","2018_02_20_035727_create_brands_table","1");
INSERT INTO migrations VALUES("5","2018_02_25_100635_create_suppliers_table","1");
INSERT INTO migrations VALUES("6","2018_02_27_101619_create_warehouse_table","1");
INSERT INTO migrations VALUES("7","2018_03_03_040448_create_units_table","1");
INSERT INTO migrations VALUES("8","2018_03_04_041317_create_taxes_table","1");
INSERT INTO migrations VALUES("9","2018_03_10_061915_create_customer_groups_table","1");
INSERT INTO migrations VALUES("10","2018_03_10_090534_create_customers_table","1");
INSERT INTO migrations VALUES("11","2018_03_11_095547_create_billers_table","1");
INSERT INTO migrations VALUES("12","2018_04_05_054401_create_products_table","1");
INSERT INTO migrations VALUES("13","2018_04_06_133606_create_purchases_table","1");
INSERT INTO migrations VALUES("14","2018_04_06_154600_create_product_purchases_table","1");
INSERT INTO migrations VALUES("15","2018_04_06_154915_create_product_warhouse_table","1");
INSERT INTO migrations VALUES("16","2018_04_10_085927_create_sales_table","1");
INSERT INTO migrations VALUES("17","2018_04_10_090133_create_product_sales_table","1");
INSERT INTO migrations VALUES("18","2018_04_10_090254_create_payments_table","1");
INSERT INTO migrations VALUES("19","2018_04_10_090341_create_payment_with_cheque_table","1");
INSERT INTO migrations VALUES("20","2018_04_10_090509_create_payment_with_credit_card_table","1");
INSERT INTO migrations VALUES("21","2018_04_13_121436_create_quotation_table","1");
INSERT INTO migrations VALUES("22","2018_04_13_122324_create_product_quotation_table","1");
INSERT INTO migrations VALUES("23","2018_04_14_121802_create_transfers_table","1");
INSERT INTO migrations VALUES("24","2018_04_14_121913_create_product_transfer_table","1");
INSERT INTO migrations VALUES("25","2018_05_13_082847_add_payment_id_and_change_sale_id_to_payments_table","2");
INSERT INTO migrations VALUES("26","2018_05_13_090906_change_customer_id_to_payment_with_credit_card_table","3");
INSERT INTO migrations VALUES("27","2018_05_20_054532_create_adjustments_table","4");
INSERT INTO migrations VALUES("28","2018_05_20_054859_create_product_adjustments_table","4");
INSERT INTO migrations VALUES("29","2018_05_21_163419_create_returns_table","5");
INSERT INTO migrations VALUES("30","2018_05_21_163443_create_product_returns_table","5");
INSERT INTO migrations VALUES("31","2018_06_02_050905_create_roles_table","6");
INSERT INTO migrations VALUES("32","2018_06_02_073430_add_columns_to_users_table","7");
INSERT INTO migrations VALUES("33","2018_06_03_053738_create_permission_tables","8");
INSERT INTO migrations VALUES("36","2018_06_21_063736_create_pos_setting_table","9");
INSERT INTO migrations VALUES("37","2018_06_21_094155_add_user_id_to_sales_table","10");
INSERT INTO migrations VALUES("38","2018_06_21_101529_add_user_id_to_purchases_table","11");
INSERT INTO migrations VALUES("39","2018_06_21_103512_add_user_id_to_transfers_table","12");
INSERT INTO migrations VALUES("40","2018_06_23_061058_add_user_id_to_quotations_table","13");
INSERT INTO migrations VALUES("41","2018_06_23_082427_add_is_deleted_to_users_table","14");
INSERT INTO migrations VALUES("42","2018_06_25_043308_change_email_to_users_table","15");
INSERT INTO migrations VALUES("43","2018_07_06_115449_create_general_settings_table","16");
INSERT INTO migrations VALUES("44","2018_07_08_043944_create_languages_table","17");
INSERT INTO migrations VALUES("45","2018_07_11_102144_add_user_id_to_returns_table","18");
INSERT INTO migrations VALUES("46","2018_07_11_102334_add_user_id_to_payments_table","18");
INSERT INTO migrations VALUES("47","2018_07_22_130541_add_digital_to_products_table","19");
INSERT INTO migrations VALUES("49","2018_07_24_154250_create_deliveries_table","20");
INSERT INTO migrations VALUES("50","2018_08_16_053336_create_expense_categories_table","21");
INSERT INTO migrations VALUES("51","2018_08_17_115415_create_expenses_table","22");
INSERT INTO migrations VALUES("55","2018_08_18_050418_create_gift_cards_table","23");
INSERT INTO migrations VALUES("56","2018_08_19_063119_create_payment_with_gift_card_table","24");
INSERT INTO migrations VALUES("57","2018_08_25_042333_create_gift_card_recharges_table","25");
INSERT INTO migrations VALUES("58","2018_08_25_101354_add_deposit_expense_to_customers_table","26");
INSERT INTO migrations VALUES("59","2018_08_26_043801_create_deposits_table","27");
INSERT INTO migrations VALUES("60","2018_09_02_044042_add_keybord_active_to_pos_setting_table","28");
INSERT INTO migrations VALUES("61","2018_09_09_092713_create_payment_with_paypal_table","29");
INSERT INTO migrations VALUES("62","2018_09_10_051254_add_currency_to_general_settings_table","30");
INSERT INTO migrations VALUES("63","2018_10_22_084118_add_biller_and_store_id_to_users_table","31");
INSERT INTO migrations VALUES("65","2018_10_26_034927_create_coupons_table","32");
INSERT INTO migrations VALUES("66","2018_10_27_090857_add_coupon_to_sales_table","33");
INSERT INTO migrations VALUES("67","2018_11_07_070155_add_currency_position_to_general_settings_table","34");
INSERT INTO migrations VALUES("68","2018_11_19_094650_add_combo_to_products_table","35");
INSERT INTO migrations VALUES("69","2018_12_09_043712_create_accounts_table","36");
INSERT INTO migrations VALUES("70","2018_12_17_112253_add_is_default_to_accounts_table","37");
INSERT INTO migrations VALUES("71","2018_12_19_103941_add_account_id_to_payments_table","38");
INSERT INTO migrations VALUES("72","2018_12_20_065900_add_account_id_to_expenses_table","39");
INSERT INTO migrations VALUES("73","2018_12_20_082753_add_account_id_to_returns_table","40");
INSERT INTO migrations VALUES("74","2018_12_26_064330_create_return_purchases_table","41");
INSERT INTO migrations VALUES("75","2018_12_26_144210_create_purchase_product_return_table","42");
INSERT INTO migrations VALUES("76","2018_12_26_144708_create_purchase_product_return_table","43");
INSERT INTO migrations VALUES("77","2018_12_27_110018_create_departments_table","44");
INSERT INTO migrations VALUES("78","2018_12_30_054844_create_employees_table","45");
INSERT INTO migrations VALUES("79","2018_12_31_125210_create_payrolls_table","46");
INSERT INTO migrations VALUES("80","2018_12_31_150446_add_department_id_to_employees_table","47");
INSERT INTO migrations VALUES("81","2019_01_01_062708_add_user_id_to_expenses_table","48");
INSERT INTO migrations VALUES("82","2019_01_02_075644_create_hrm_settings_table","49");
INSERT INTO migrations VALUES("83","2019_01_02_090334_create_attendances_table","50");
INSERT INTO migrations VALUES("84","2019_01_27_160956_add_three_columns_to_general_settings_table","51");
INSERT INTO migrations VALUES("85","2019_02_15_183303_create_stock_counts_table","52");
INSERT INTO migrations VALUES("86","2019_02_17_101604_add_is_adjusted_to_stock_counts_table","53");
INSERT INTO migrations VALUES("87","2019_04_13_101707_add_tax_no_to_customers_table","54");
INSERT INTO migrations VALUES("89","2019_10_14_111455_create_holidays_table","55");
INSERT INTO migrations VALUES("90","2019_11_13_145619_add_is_variant_to_products_table","56");
INSERT INTO migrations VALUES("91","2019_11_13_150206_create_product_variants_table","57");
INSERT INTO migrations VALUES("92","2019_11_13_153828_create_variants_table","57");
INSERT INTO migrations VALUES("93","2019_11_25_134041_add_qty_to_product_variants_table","58");
INSERT INTO migrations VALUES("94","2019_11_25_134922_add_variant_id_to_product_purchases_table","58");
INSERT INTO migrations VALUES("95","2019_11_25_145341_add_variant_id_to_product_warehouse_table","58");
INSERT INTO migrations VALUES("96","2019_11_29_182201_add_variant_id_to_product_sales_table","59");
INSERT INTO migrations VALUES("97","2019_12_04_121311_add_variant_id_to_product_quotation_table","60");
INSERT INTO migrations VALUES("98","2019_12_05_123802_add_variant_id_to_product_transfer_table","61");
INSERT INTO migrations VALUES("100","2019_12_08_114954_add_variant_id_to_product_returns_table","62");
INSERT INTO migrations VALUES("101","2019_12_08_203146_add_variant_id_to_purchase_product_return_table","63");
INSERT INTO migrations VALUES("102","2020_02_28_103340_create_money_transfers_table","64");
INSERT INTO migrations VALUES("103","2020_07_01_193151_add_image_to_categories_table","65");
INSERT INTO migrations VALUES("105","2020_09_26_130426_add_user_id_to_deliveries_table","66");
INSERT INTO migrations VALUES("107","2020_10_11_125457_create_cash_registers_table","67");
INSERT INTO migrations VALUES("108","2020_10_13_155019_add_cash_register_id_to_sales_table","68");
INSERT INTO migrations VALUES("109","2020_10_13_172624_add_cash_register_id_to_returns_table","69");
INSERT INTO migrations VALUES("110","2020_10_17_212338_add_cash_register_id_to_payments_table","70");
INSERT INTO migrations VALUES("111","2020_10_18_124200_add_cash_register_id_to_expenses_table","71");
INSERT INTO migrations VALUES("112","2020_10_21_121632_add_developed_by_to_general_settings_table","72");
INSERT INTO migrations VALUES("113","2019_08_19_000000_create_failed_jobs_table","73");
INSERT INTO migrations VALUES("114","2020_10_30_135557_create_notifications_table","73");
INSERT INTO migrations VALUES("115","2020_11_01_044954_create_currencies_table","74");
INSERT INTO migrations VALUES("116","2020_11_01_140736_add_price_to_product_warehouse_table","75");
INSERT INTO migrations VALUES("117","2020_11_02_050633_add_is_diff_price_to_products_table","76");
INSERT INTO migrations VALUES("118","2020_11_09_055222_add_user_id_to_customers_table","77");
INSERT INTO migrations VALUES("119","2020_11_17_054806_add_invoice_format_to_general_settings_table","78");
INSERT INTO migrations VALUES("120","2021_02_10_074859_add_variant_id_to_product_adjustments_table","79");
INSERT INTO migrations VALUES("121","2021_03_07_093606_create_product_batches_table","80");
INSERT INTO migrations VALUES("122","2021_03_07_093759_add_product_batch_id_to_product_warehouse_table","80");
INSERT INTO migrations VALUES("123","2021_03_07_093900_add_product_batch_id_to_product_purchases_table","80");
INSERT INTO migrations VALUES("124","2021_03_11_132603_add_product_batch_id_to_product_sales_table","81");
INSERT INTO migrations VALUES("127","2021_03_25_125421_add_is_batch_to_products_table","82");
INSERT INTO migrations VALUES("128","2021_05_19_120127_add_product_batch_id_to_product_returns_table","82");
INSERT INTO migrations VALUES("130","2021_05_22_105611_add_product_batch_id_to_purchase_product_return_table","83");
INSERT INTO migrations VALUES("131","2021_05_23_124848_add_product_batch_id_to_product_transfer_table","84");
INSERT INTO migrations VALUES("132","2021_05_26_153106_add_product_batch_id_to_product_quotation_table","85");
INSERT INTO migrations VALUES("133","2021_06_08_213007_create_reward_point_settings_table","86");
INSERT INTO migrations VALUES("134","2021_06_16_104155_add_points_to_customers_table","87");
INSERT INTO migrations VALUES("135","2021_06_17_101057_add_used_points_to_payments_table","88");
INSERT INTO migrations VALUES("136","2021_07_06_132716_add_variant_list_to_products_table","89");
INSERT INTO migrations VALUES("137","2021_09_27_161141_add_is_imei_to_products_table","90");
INSERT INTO migrations VALUES("138","2021_09_28_170052_add_imei_number_to_product_warehouse_table","91");
INSERT INTO migrations VALUES("139","2021_09_28_170126_add_imei_number_to_product_purchases_table","91");
INSERT INTO migrations VALUES("140","2021_10_03_170652_add_imei_number_to_product_sales_table","92");
INSERT INTO migrations VALUES("141","2021_10_10_145214_add_imei_number_to_product_returns_table","93");
INSERT INTO migrations VALUES("142","2021_10_11_104504_add_imei_number_to_product_transfer_table","94");
INSERT INTO migrations VALUES("143","2021_10_12_160107_add_imei_number_to_purchase_product_return_table","95");
INSERT INTO migrations VALUES("144","2021_10_12_205146_add_is_rtl_to_general_settings_table","96");
INSERT INTO migrations VALUES("145","2021_10_23_142451_add_is_approve_to_payments_table","97");
INSERT INTO migrations VALUES("146","2022_01_13_191242_create_discount_plans_table","97");
INSERT INTO migrations VALUES("147","2022_01_14_174318_create_discount_plan_customers_table","97");
INSERT INTO migrations VALUES("148","2022_01_14_202439_create_discounts_table","98");
INSERT INTO migrations VALUES("149","2022_01_16_153506_create_discount_plan_discounts_table","98");
INSERT INTO migrations VALUES("150","2022_02_05_174210_add_order_discount_type_and_value_to_sales_table","99");
INSERT INTO migrations VALUES("154","2022_05_26_195506_add_daily_sale_objective_to_products_table","100");
INSERT INTO migrations VALUES("155","2022_05_28_104209_create_dso_alerts_table","101");
INSERT INTO migrations VALUES("156","2022_06_01_112100_add_is_embeded_to_products_table","102");
INSERT INTO migrations VALUES("157","2022_06_14_130505_add_sale_id_to_returns_table","103");
INSERT INTO migrations VALUES("159","2022_07_19_115504_add_variant_data_to_products_table","104");
INSERT INTO migrations VALUES("160","2022_07_25_194300_add_additional_cost_to_product_variants_table","104");
INSERT INTO migrations VALUES("161","2022_09_04_195610_add_purchase_id_to_return_purchases_table","105");
INSERT INTO migrations VALUES("162","2023_01_18_123842_alter_table_pos_setting","106");
INSERT INTO migrations VALUES("164","2023_01_18_125040_alter_table_general_settings","107");
INSERT INTO migrations VALUES("165","2023_01_18_133701_alter_table_pos_setting","108");
INSERT INTO migrations VALUES("166","2023_01_25_145309_add_expiry_date_to_general_settings_table","109");
INSERT INTO migrations VALUES("167","2023_02_23_125656_alter_table_sales","110");
INSERT INTO migrations VALUES("168","2023_02_26_124100_add_package_id_to_general_settings_table","111");
INSERT INTO migrations VALUES("169","2023_03_04_120325_create_custom_fields_table","111");
INSERT INTO migrations VALUES("170","2023_03_22_174352_add_currency_id_and_exchange_rate_to_returns_table","112");
INSERT INTO migrations VALUES("171","2023_03_27_114320_add_currency_id_and_exchange_rate_to_purchases_table","113");
INSERT INTO migrations VALUES("172","2023_03_27_132747_add_currency_id_and_exchange_rate_to_return_purchases_table","114");
INSERT INTO migrations VALUES("173","2023_04_25_150236_create_mail_settings_table","115");
INSERT INTO migrations VALUES("174","2023_05_13_125424_add_zatca_to_general_settings_table","116");
INSERT INTO migrations VALUES("175","2023_05_28_155540_create_tables_table","117");
INSERT INTO migrations VALUES("176","2023_05_29_115039_add_is_table_to_pos_setting_table","117");
INSERT INTO migrations VALUES("177","2023_05_29_115301_add_table_id_to_sales_table","117");
INSERT INTO migrations VALUES("178","2023_05_31_165049_add_queue_no_to_sales_table","117");



CREATE TABLE `money_transfers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_account_id` int NOT NULL,
  `to_account_id` int NOT NULL,
  `amount` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO money_transfers VALUES("2","mtr-20200228-071852","1","3","100","2020-02-28 19:18:52","2020-02-28 19:18:52");
INSERT INTO money_transfers VALUES("3","mtr-20221128-120014","5","1","10000","2022-11-28 12:00:14","2022-11-28 12:00:14");
INSERT INTO money_transfers VALUES("4","mtr-20230709-010712","1","6","500","2023-07-09 13:07:12","2023-07-09 13:07:12");



CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO notifications VALUES("55018eeb-b30f-4ddd-a2d3-2e69d8293e64","App\Notifications\SendNotification","App\Models\User","9","{"sender_id":"1","receiver_id":"9","reminder_date":"2023-04-04","document_name":null,"message":"first reminder"}","2023-04-04 12:04:41","2023-04-04 12:03:50","2023-04-04 12:04:41");
INSERT INTO notifications VALUES("9bf0de38-65d8-462d-b7e2-58ca84f88e57","App\Notifications\SendNotification","App\Models\User","9","{"sender_id":"1","receiver_id":"9","reminder_date":"2023-04-05","document_name":null,"message":"second reminder"}","","2023-04-04 12:05:33","2023-04-04 12:05:33");
INSERT INTO notifications VALUES("d0623880-4199-459c-af55-3bc1464cbe71","App\Notifications\SendNotification","App\Models\User","9","{"sender_id":"1","receiver_id":"9","document_name":"20220523012859.jpg","message":"hjhjhh"}","2022-05-23 15:50:47","2022-05-23 13:29:00","2022-05-23 15:50:47");
INSERT INTO notifications VALUES("ea3e6ccc-a25a-44b7-8e43-b0ab09204ee6","App\Notifications\SendNotification","App\Models\User","9","{"sender_id":"1","receiver_id":"9","document_name":null,"message":"hello"}","2022-05-23 16:24:31","2022-05-23 16:24:06","2022-05-23 16:24:31");



CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




CREATE TABLE `payment_with_cheque` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` int NOT NULL,
  `cheque_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO payment_with_cheque VALUES("1","19","23425235235","2018-07-01 09:09:48","2018-07-01 09:09:48");
INSERT INTO payment_with_cheque VALUES("2","24","3123123123","2018-07-10 07:21:32","2018-07-10 07:21:32");
INSERT INTO payment_with_cheque VALUES("3","31","767867678","2018-08-08 16:36:22","2018-08-08 16:36:22");
INSERT INTO payment_with_cheque VALUES("4","34","3123412","2018-08-09 05:55:54","2018-08-09 05:55:54");
INSERT INTO payment_with_cheque VALUES("5","35","7765","2018-08-09 06:32:14","2018-08-09 06:32:14");
INSERT INTO payment_with_cheque VALUES("6","44","3124142412","2018-08-30 09:07:18","2018-08-30 09:07:18");
INSERT INTO payment_with_cheque VALUES("7","51","6576764646","2018-09-03 10:08:21","2018-09-03 10:08:21");
INSERT INTO payment_with_cheque VALUES("8","53","111111111","2018-09-03 10:15:24","2018-09-03 10:15:24");
INSERT INTO payment_with_cheque VALUES("9","79","1111","2018-10-06 06:51:55","2018-10-06 06:51:55");
INSERT INTO payment_with_cheque VALUES("10","147","221133","2018-12-04 06:58:35","2018-12-04 06:58:35");
INSERT INTO payment_with_cheque VALUES("11","175","1111","2019-02-07 12:38:23","2019-02-07 12:38:23");
INSERT INTO payment_with_cheque VALUES("12","176","1111","2019-02-07 12:54:59","2019-02-07 12:54:59");
INSERT INTO payment_with_cheque VALUES("13","178","420","2019-02-07 13:07:04","2019-02-07 13:07:04");
INSERT INTO payment_with_cheque VALUES("14","216","12344321","2019-11-11 10:39:11","2019-11-11 10:39:11");
INSERT INTO payment_with_cheque VALUES("15","295","111122222","2020-10-18 11:17:24","2020-10-18 11:17:24");
INSERT INTO payment_with_cheque VALUES("16","406","13123123","2022-03-11 21:15:29","2022-03-11 21:15:29");



CREATE TABLE `payment_with_credit_card` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` int NOT NULL,
  `customer_id` int DEFAULT NULL,
  `customer_stripe_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `charge_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO payment_with_credit_card VALUES("4","294","1","cus_IDw8z9yJZn4qH3","ch_1HdUGJKwOmA8HLXePiqphlky","2020-10-18 11:16:55","2020-10-18 11:16:55");



CREATE TABLE `payment_with_gift_card` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` int NOT NULL,
  `gift_card_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO payment_with_gift_card VALUES("1","36","2","2018-08-25 03:48:36","2018-08-25 06:57:35");
INSERT INTO payment_with_gift_card VALUES("4","39","1","2018-08-25 08:36:34","2018-08-25 08:36:34");
INSERT INTO payment_with_gift_card VALUES("6","50","1","2018-09-03 05:01:38","2018-09-03 05:01:38");
INSERT INTO payment_with_gift_card VALUES("8","293","1","2020-10-18 11:14:26","2020-10-18 11:14:26");



CREATE TABLE `payment_with_paypal` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` int NOT NULL,
  `transaction_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




CREATE TABLE `payments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `payment_reference` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `purchase_id` int DEFAULT NULL,
  `sale_id` int DEFAULT NULL,
  `cash_register_id` int DEFAULT NULL,
  `account_id` int NOT NULL,
  `amount` double NOT NULL,
  `used_points` double DEFAULT NULL,
  `change` double NOT NULL,
  `paying_method` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=510 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO payments VALUES("33","spr-20180809-055453","1","","2","","1","1000","","0","Cash","","2018-08-09 05:54:53","2018-08-09 05:54:53");
INSERT INTO payments VALUES("34","spr-20180809-055553","1","","2","","1","1200","","0","Cheque","","2018-08-09 05:55:53","2018-08-09 05:56:36");
INSERT INTO payments VALUES("35","spr-20180809-063214","1","","3","","1","897","","0","Cheque","","2018-08-09 06:32:14","2018-08-09 06:32:14");
INSERT INTO payments VALUES("36","spr-20180825-034836","1","","4","","1","100","","0","Gift Card","100 bucks paid...","2018-08-25 03:48:36","2018-08-25 06:57:35");
INSERT INTO payments VALUES("39","spr-20180825-083634","1","","4","","1","200","","0","Gift Card","","2018-08-25 08:36:34","2018-08-25 08:36:34");
INSERT INTO payments VALUES("41","spr-20180826-094836","1","","6","","1","20","","0","Deposit","20 bucks paid","2018-08-26 09:48:36","2018-08-27 03:42:13");
INSERT INTO payments VALUES("42","spr-20180827-073545","1","","7","","1","880","","0","Cash","","2018-08-27 07:35:45","2018-08-27 07:35:45");
INSERT INTO payments VALUES("43","ppr-20180830-071637","1","13","","","1","100","","0","Cash","100 bucks paid...","2018-08-30 07:16:37","2018-08-30 07:16:37");
INSERT INTO payments VALUES("44","ppr-20180830-090718","1","13","","","1","200","","0","Cheque","","2018-08-30 09:07:18","2018-08-30 09:07:18");
INSERT INTO payments VALUES("46","spr-20180902-053954","1","","8","","1","3529.8","","0","Cash","fully paid","2018-09-02 05:39:54","2018-09-02 05:39:54");
INSERT INTO payments VALUES("49","spr-20180903-033314","1","","9","","1","20","","0","Deposit","fully paid","2018-09-03 03:33:14","2018-09-03 03:33:14");
INSERT INTO payments VALUES("50","spr-20180903-050138","1","","10","","1","200","","0","Gift Card","50 bucks due...","2018-09-03 05:01:38","2018-09-10 03:40:28");
INSERT INTO payments VALUES("51","spr-20180903-100821","1","","11","","1","5500","","0","Cheque","","2018-09-03 10:08:21","2018-09-03 10:08:21");
INSERT INTO payments VALUES("53","ppr-20180903-101524","1","16","","","1","1750","","0","Cheque","","2018-09-03 10:15:24","2018-10-06 07:09:20");
INSERT INTO payments VALUES("78","spr-20180926-092105","1","","31","","1","560","","0","Cash","","2018-09-26 09:21:05","2018-09-26 09:21:05");
INSERT INTO payments VALUES("79","spr-20181006-065017","1","","30","","1","100","","0","Cheque","","2018-10-06 06:50:17","2018-10-06 06:51:55");
INSERT INTO payments VALUES("80","spr-20181006-065222","1","","30","","1","20","","0","Cash","","2018-10-06 06:52:22","2018-10-06 06:52:22");
INSERT INTO payments VALUES("82","ppr-20181006-070935","1","16","","","1","1600","","0","Cash","","2018-10-06 07:09:35","2018-10-06 07:09:35");
INSERT INTO payments VALUES("83","spr-20181010-041636","1","","41","","1","461","","0","Cash","","2018-10-10 04:16:36","2018-10-10 04:16:36");
INSERT INTO payments VALUES("84","spr-20181010-053456","1","","42","","1","440","","0","Cash","","2018-10-10 05:34:56","2018-10-10 05:34:56");
INSERT INTO payments VALUES("91","spr-20181021-065338","1","","55","","1","250","","0","Cash","","2018-10-21 06:53:38","2018-10-21 06:53:38");
INSERT INTO payments VALUES("92","spr-20181021-082618","1","","57","","1","575.2","","0","Cash","","2018-10-21 08:26:18","2018-10-21 08:26:18");
INSERT INTO payments VALUES("93","spr-20181022-032730","1","","58","","1","1220","","0","Cash","","2018-10-22 15:27:30","2018-10-22 15:27:30");
INSERT INTO payments VALUES("104","spr-20181023-071548","11","","73","","1","5500","","0","Cash","","2018-10-23 07:15:48","2018-10-23 07:15:48");
INSERT INTO payments VALUES("105","spr-20181023-071648","1","","74","","1","2320","","0","Cash","","2018-10-23 07:16:48","2018-10-23 07:16:48");
INSERT INTO payments VALUES("126","spr-20181101-050033","1","","75","","1","7678","","0","Cash","","2018-11-01 05:00:33","2018-11-01 05:00:33");
INSERT INTO payments VALUES("127","spr-20181101-050130","1","","76","","1","1424","","0","Cash","","2018-11-01 05:01:30","2018-11-08 09:44:51");
INSERT INTO payments VALUES("129","spr-20181105-091523","1","","79","","1","14454","","0","Cash","","2018-11-05 09:15:23","2018-11-05 09:15:23");
INSERT INTO payments VALUES("130","spr-20181105-092002","1","","80","","1","2500","","0","Cash","","2018-11-05 09:20:02","2018-11-05 09:20:02");
INSERT INTO payments VALUES("131","ppr-20181105-092128","1","24","","","1","15950","","0","Cash","","2018-11-05 09:21:28","2018-11-05 09:21:28");
INSERT INTO payments VALUES("137","spr-20181105-095952","1","","86","","1","1100","","0","Cash","","2018-11-05 09:59:52","2018-11-05 09:59:52");
INSERT INTO payments VALUES("138","spr-20181105-100310","1","","88","","1","1100","","0","Cash","","2018-11-05 10:03:10","2018-11-05 10:03:10");
INSERT INTO payments VALUES("139","spr-20181126-020534","1","","94","","1","120","","0","Cash","","2018-11-26 14:05:34","2018-11-26 14:05:34");
INSERT INTO payments VALUES("140","spr-20181128-071515","1","","96","","1","132","","0","Cash","","2018-11-28 07:15:15","2018-11-28 07:15:15");
INSERT INTO payments VALUES("141","spr-20181201-060524","1","","97","","1","200","","300","Cash","","2018-12-01 06:05:24","2018-12-04 06:21:05");
INSERT INTO payments VALUES("148","ppr-20181204-065932","1","23","","","1","500","","500","Cash","","2018-12-04 06:59:32","2018-12-04 06:59:44");
INSERT INTO payments VALUES("149","ppr-20181205-053443","1","25","","","1","4450","","550","Cash","","2018-12-05 05:34:43","2018-12-05 05:34:43");
INSERT INTO payments VALUES("150","spr-20181205-053608","1","","98","","1","800","","200","Cash","","2018-12-05 05:36:08","2018-12-05 05:36:08");
INSERT INTO payments VALUES("151","spr-20181205-053724","1","","99","","1","800","","0","Cash","","2018-12-05 05:37:24","2018-12-05 05:37:24");
INSERT INTO payments VALUES("152","spr-20181208-062032","1","","101","","1","100","","400","Cash","","2018-12-08 06:20:32","2018-12-11 09:19:39");
INSERT INTO payments VALUES("157","ppr-20181220-063439","1","27","","","1","10","","0","Cash","","2018-12-20 06:34:39","2018-12-20 06:35:01");
INSERT INTO payments VALUES("159","spr-20181224-045832","1","","103","","1","120","","0","Cash","","2018-12-24 04:58:32","2018-12-24 04:58:32");
INSERT INTO payments VALUES("160","spr-20190101-054544","1","","105","","1","21","","0","Cash","","2019-01-01 05:45:44","2019-01-01 05:45:44");
INSERT INTO payments VALUES("161","spr-20190101-091040","1","","106","","1","860","","0","Cash","","2019-01-01 09:10:40","2019-01-01 09:10:40");
INSERT INTO payments VALUES("162","spr-20190103-065627","1","","107","","1","5040","","960","Cash","","2019-01-03 06:56:27","2019-01-03 06:56:27");
INSERT INTO payments VALUES("163","spr-20190120-035824","1","","108","","1","120","","0","Cash","","2019-01-20 15:58:24","2019-01-20 15:58:24");
INSERT INTO payments VALUES("164","ppr-20190129-100302","9","36","","","1","650","","350","Cash","","2019-01-29 10:03:02","2019-01-29 10:03:02");
INSERT INTO payments VALUES("165","ppr-20190129-100324","9","34","","","1","2860","","140","Cash","","2019-01-29 10:03:24","2019-01-29 10:03:24");
INSERT INTO payments VALUES("166","spr-20190129-101451","9","","109","","1","540","","460","Cash","","2019-01-29 10:14:51","2019-01-29 10:14:51");
INSERT INTO payments VALUES("167","spr-20190129-115048","9","","110","","1","1700","","300","Cash","","2019-01-29 11:50:48","2019-01-29 11:50:48");
INSERT INTO payments VALUES("168","spr-20190131-110839","9","","111","","1","271","","0","Cash","","2019-01-31 11:08:39","2019-01-31 11:08:39");
INSERT INTO payments VALUES("169","spr-20190202-104045","1","","112","","1","440","","0","Cash","","2019-02-02 10:40:45","2019-02-02 10:40:45");
INSERT INTO payments VALUES("170","spr-20190202-114117","1","","113","","1","350","","0","Cash","","2019-02-02 11:41:17","2019-02-02 11:41:17");
INSERT INTO payments VALUES("171","spr-20190205-030454","1","","114","","1","440","","0","Cash","","2019-02-05 15:04:54","2019-02-05 15:04:54");
INSERT INTO payments VALUES("176","ppr-20190207-125418","1","35","","","1","50","","50","Cash","","2019-02-07 12:54:18","2019-02-07 13:05:23");
INSERT INTO payments VALUES("178","ppr-20190207-010640","1","35","","","1","50","","50","Cheque","","2019-02-07 13:06:40","2019-02-07 13:07:04");
INSERT INTO payments VALUES("179","spr-20190207-010915","1","","120","","1","50","","50","Cash","","2019-02-07 13:09:15","2019-02-07 13:09:15");
INSERT INTO payments VALUES("180","spr-20190209-104816","1","","121","","1","1272","","728","Cash","","2019-02-09 10:48:16","2019-02-09 10:48:16");
INSERT INTO payments VALUES("181","ppr-20190209-104940","1","38","","","1","1660","","0","Cash","","2019-02-09 10:49:40","2019-02-09 10:49:40");
INSERT INTO payments VALUES("182","ppr-20190209-104959","1","39","","","1","973.5","","0","Cash","","2019-02-09 10:49:59","2019-02-09 10:49:59");
INSERT INTO payments VALUES("183","spr-20190219-023214","1","","123","","1","440","","0","Cash","","2019-02-19 14:32:14","2019-02-19 14:32:14");
INSERT INTO payments VALUES("189","spr-20190303-104010","1","","127","","1","2500","","0","Cash","","2019-03-03 10:40:10","2019-03-03 10:40:10");
INSERT INTO payments VALUES("190","ppr-20190303-104046","1","40","","","1","100","","0","Cash","","2019-03-03 10:40:46","2019-03-03 10:40:46");
INSERT INTO payments VALUES("191","ppr-20190303-104222","1","37","","","1","4000","","0","Cash","","2019-03-03 10:42:22","2019-03-03 10:42:22");
INSERT INTO payments VALUES("192","ppr-20190303-104414","1","41","","","1","1000","","0","Cash","","2019-03-03 10:44:14","2019-03-03 10:44:14");
INSERT INTO payments VALUES("193","spr-20190404-095555","1","","128","","1","560","","0","Cash","","2019-04-04 09:55:55","2019-04-04 09:55:55");
INSERT INTO payments VALUES("194","ppr-20190404-095910","1","42","","","1","300","","200","Cash","","2019-04-04 09:59:10","2019-04-13 16:52:38");
INSERT INTO payments VALUES("195","spr-20190404-095937","1","","129","","1","120","","0","Cash","","2019-04-04 09:59:37","2019-04-04 09:59:37");
INSERT INTO payments VALUES("196","spr-20190421-122124","1","","130","","1","586","","0","Cash","","2019-04-21 12:21:24","2019-04-21 12:21:24");
INSERT INTO payments VALUES("197","spr-20190528-103229","1","","131","","1","2890","","0","Cash","","2019-05-28 10:32:29","2019-05-28 10:32:29");
INSERT INTO payments VALUES("198","ppr-20190613-101351","1","37","","","1","2390","","0","Cash","","2019-06-13 10:13:51","2019-06-13 10:13:51");
INSERT INTO payments VALUES("199","spr-20190613-101637","1","","132","","1","840","","0","Cash","","2019-06-13 10:16:37","2019-06-13 10:16:37");
INSERT INTO payments VALUES("200","ppr-20190613-101713","1","43","","","1","1000","","0","Cash","","2019-06-13 10:17:13","2019-06-13 10:17:13");
INSERT INTO payments VALUES("201","spr-20190613-101752","1","","133","","1","2700","","0","Cash","","2019-06-13 10:17:52","2019-06-13 10:17:52");
INSERT INTO payments VALUES("202","ppr-20191019-032925","1","43","","","1","3290","","710","Cash","","2019-10-19 15:29:25","2019-10-19 15:29:25");
INSERT INTO payments VALUES("203","spr-20191019-033028","1","","134","","1","2940","","60","Cash","","2019-10-19 15:30:28","2019-10-19 15:30:28");
INSERT INTO payments VALUES("205","spr-20191103-114044","1","","139","","1","488","","12","Cash","","2019-11-03 11:40:44","2019-11-03 11:40:44");
INSERT INTO payments VALUES("206","ppr-20191103-114222","1","46","","","1","200","","0","Cash","","2019-11-03 11:42:22","2019-11-03 11:42:22");
INSERT INTO payments VALUES("211","spr-20191109-074131","1","","144","","1","1220","","0","Cash","","2019-11-09 19:41:31","2019-11-09 19:41:31");
INSERT INTO payments VALUES("216","ppr-20191111-103911","1","49","","","1","5000","","0","Cheque","","2019-11-11 10:39:11","2019-11-11 10:39:11");
INSERT INTO payments VALUES("217","spr-20191111-104008","1","","147","","1","2220","","780","Cash","","2019-11-11 10:40:08","2019-11-11 10:40:08");
INSERT INTO payments VALUES("222","spr-20191203-115128","1","","163","","1","3","","0","Cash","","2019-12-03 11:51:28","2019-12-03 11:51:28");
INSERT INTO payments VALUES("227","ppr-20191204-111124","1","57","","","1","220","","280","Cash","","2019-12-04 23:11:24","2019-12-04 23:11:24");
INSERT INTO payments VALUES("228","spr-20191205-092712","1","","173","","1","621","","0","Cash","","2019-12-05 09:27:12","2019-12-05 09:27:12");
INSERT INTO payments VALUES("239","spr-20191222-104058","1","","187","","1","288","","212","Cash","","2019-12-22 10:40:58","2019-12-22 10:40:58");
INSERT INTO payments VALUES("241","spr-20191223-125946","1","","190","","1","1100","","400","Cash","","2019-12-23 12:59:46","2019-12-23 12:59:46");
INSERT INTO payments VALUES("244","ppr-20200101-010750","1","61","","","1","60","","0","Cash","","2020-01-01 13:07:50","2020-01-01 13:07:50");
INSERT INTO payments VALUES("246","spr-20200101-022028","1","","193","","1","1100","","400","Cash","","2020-01-01 14:20:28","2020-01-01 14:20:28");
INSERT INTO payments VALUES("247","ppr-20200101-022131","1","59","","","1","6","","0","Cash","","2020-01-01 14:21:31","2020-01-01 14:21:31");
INSERT INTO payments VALUES("248","ppr-20200101-022137","1","58","","","1","4","","0","Cash","","2020-01-01 14:21:37","2020-01-01 14:21:37");
INSERT INTO payments VALUES("249","ppr-20200101-022144","1","56","","","1","2","","0","Cash","","2020-01-01 14:21:44","2020-01-01 14:21:44");
INSERT INTO payments VALUES("250","ppr-20200101-022152","1","55","","","1","4","","0","Cash","","2020-01-01 14:21:52","2020-01-01 14:21:52");
INSERT INTO payments VALUES("251","ppr-20200101-022225","1","49","","","1","2000","","0","Cash","","2020-01-01 14:22:25","2020-01-01 14:22:25");
INSERT INTO payments VALUES("252","spr-20200102-043947","1","","194","","1","892","","108","Cash","","2020-01-02 16:39:47","2020-01-02 16:39:47");
INSERT INTO payments VALUES("258","spr-20200203-035256","1","","201","","1","120","","880","Cash","","2020-02-03 15:52:56","2020-02-03 15:52:56");
INSERT INTO payments VALUES("259","spr-20200204-105853","1","","202","","1","1400","","100","Cash","","2020-02-04 22:58:53","2020-02-04 22:58:53");
INSERT INTO payments VALUES("260","ppr-20200204-110050","1","67","","","1","300","","0","Cash","","2020-02-04 23:00:50","2020-02-04 23:00:50");
INSERT INTO payments VALUES("261","spr-20200302-115414","1","","203","","1","350","","150","Cash","","2020-03-02 11:54:14","2020-03-02 11:54:14");
INSERT INTO payments VALUES("262","spr-20200302-115741","1","","204","","1","40","","10","Cash","","2020-03-02 11:57:41","2020-03-02 11:57:41");
INSERT INTO payments VALUES("263","ppr-20200302-115811","1","70","","","1","50","","0","Cash","","2020-03-02 11:58:11","2020-03-02 11:58:11");
INSERT INTO payments VALUES("264","ppr-20200302-115820","1","69","","","1","50","","0","Cash","","2020-03-02 11:58:20","2020-03-02 11:58:20");
INSERT INTO payments VALUES("265","spr-20200311-044642","1","","205","","1","352","","148","Cash","","2020-03-11 16:46:42","2020-03-11 16:46:42");
INSERT INTO payments VALUES("266","ppr-20200406-073823","1","71","","","1","2000","","1000","Cash","First Payment","2020-04-06 19:38:23","2020-04-06 19:38:55");
INSERT INTO payments VALUES("267","spr-20200406-074024","1","","207","","1","500","","500","Cash","","2020-04-06 19:40:24","2020-04-06 19:40:24");
INSERT INTO payments VALUES("268","spr-20200406-074201","1","","207","","1","144","","56","Cash","","2020-04-06 19:42:01","2020-04-06 19:42:01");
INSERT INTO payments VALUES("269","spr-20200506-105950","1","","208","","1","1540","","460","Cash","","2020-05-06 22:59:50","2020-05-06 22:59:50");
INSERT INTO payments VALUES("270","spr-20200609-124248","1","","209","","1","1220","","780","Cash","","2020-06-09 12:42:48","2020-06-09 12:42:48");
INSERT INTO payments VALUES("273","spr-20200703-063914","1","","212","","1","2585","","415","Cash","","2020-07-03 18:39:14","2020-07-03 18:39:14");
INSERT INTO payments VALUES("274","spr-20200712-095153","1","","213","","1","13","","37","Cash","","2020-07-12 21:51:53","2020-07-12 21:51:53");
INSERT INTO payments VALUES("276","spr-20200727-083808","1","","217","","1","385","","0","Cash","","2020-07-27 20:38:08","2020-07-27 20:38:08");
INSERT INTO payments VALUES("277","spr-20200727-084024","1","","218","","1","385","","0","Cash","","2020-07-27 20:40:24","2020-07-27 20:40:24");
INSERT INTO payments VALUES("278","spr-20200727-084645","1","","219","","1","385","","0","Cash","","2020-07-27 20:46:45","2020-07-27 20:46:45");
INSERT INTO payments VALUES("279","spr-20200812-062806","1","","220","","1","760","","240","Cash","","2020-08-12 18:28:06","2020-08-12 18:28:06");
INSERT INTO payments VALUES("280","ppr-20200812-062853","1","62","","","1","1650","","0","Cash","","2020-08-12 18:28:53","2020-08-12 18:28:53");
INSERT INTO payments VALUES("281","spr-20200812-063035","1","","221","","1","1100","","0","Cash","","2020-08-12 18:30:35","2020-08-12 18:30:35");
INSERT INTO payments VALUES("282","spr-20200816-100426","1","","222","","1","23000","","2000","Cash","","2020-08-16 22:04:26","2020-08-16 22:04:26");
INSERT INTO payments VALUES("283","spr-20200816-100523","1","","223","","1","300","","200","Cash","","2020-08-16 22:05:23","2020-08-16 22:05:23");
INSERT INTO payments VALUES("284","spr-20200816-100632","1","","223","","1","100","","0","Cash","","2020-08-16 22:06:32","2020-08-16 22:06:32");
INSERT INTO payments VALUES("285","spr-20200816-100735","1","","223","","1","40","","0","Cash","","2020-08-16 22:07:35","2020-08-16 22:07:35");
INSERT INTO payments VALUES("290","spr-20201017-092854","9","","230","2","1","200","","0","Cash","","2020-10-17 21:28:54","2020-10-18 10:48:54");
INSERT INTO payments VALUES("291","spr-20201018-105138","9","","230","2","1","50","","0","Cash","","2020-10-18 10:51:38","2020-10-18 10:51:38");
INSERT INTO payments VALUES("292","spr-20201018-111333","9","","231","2","1","100","","0","Cash","","2020-10-18 11:13:33","2020-10-18 11:13:33");
INSERT INTO payments VALUES("293","spr-20201018-111426","9","","231","2","1","50","","0","Gift Card","","2020-10-18 11:14:26","2020-10-18 11:14:26");
INSERT INTO payments VALUES("294","spr-20201018-111651","9","","231","2","1","50","","0","Credit Card","","2020-10-18 11:16:51","2020-10-18 11:16:51");
INSERT INTO payments VALUES("295","spr-20201018-111724","9","","231","2","1","50","","0","Cheque","","2020-10-18 11:17:24","2020-10-18 11:17:24");
INSERT INTO payments VALUES("296","spr-20201022-013018","9","","232","2","1","100","","0","Cash","","2020-10-22 13:30:18","2020-10-22 13:30:18");
INSERT INTO payments VALUES("297","spr-20201022-015606","1","","233","3","1","250","","0","Cash","","2020-10-22 13:56:06","2020-10-22 13:56:06");
INSERT INTO payments VALUES("298","spr-20201024-070508","1","","234","4","1","11500","","0","Cash","","2020-10-24 07:05:08","2020-10-24 07:05:08");
INSERT INTO payments VALUES("299","spr-20201024-070753","1","","235","4","1","250","","0","Cash","","2020-10-24 07:07:53","2020-10-24 07:07:53");
INSERT INTO payments VALUES("300","spr-20201024-034619","1","","237","4","1","61900","","0","Cash","","2020-10-24 15:46:19","2020-10-24 15:46:19");
INSERT INTO payments VALUES("302","spr-20201027-054004","1","","239","3","1","2","","0","Cash","","2020-10-27 05:40:04","2020-10-27 05:40:04");
INSERT INTO payments VALUES("303","spr-20201027-054207","1","","240","3","1","6","","0","Cash","","2020-10-27 05:42:07","2020-10-27 05:42:07");
INSERT INTO payments VALUES("304","spr-20201027-063202","1","","241","4","1","250","","0","Cash","","2020-10-27 06:32:02","2020-10-27 06:32:02");
INSERT INTO payments VALUES("305","spr-20201029-073033","1","","242","4","1","250","","0","Cash","","2020-10-29 07:30:33","2020-10-29 07:30:33");
INSERT INTO payments VALUES("306","spr-20201101-072115","1","","243","4","1","250","","0","Cash","","2020-11-01 07:21:15","2020-11-01 07:21:15");
INSERT INTO payments VALUES("307","spr-20201101-074225","1","","245","3","1","1130","","0","Cash","","2020-11-01 07:42:25","2020-11-01 07:42:25");
INSERT INTO payments VALUES("308","spr-20201101-075019","1","","246","4","1","440","","0","Cash","","2020-11-01 07:50:19","2020-11-01 07:50:19");
INSERT INTO payments VALUES("310","spr-20201106-013042","1","","250","4","1","378.4","","0","Cash","","2020-11-06 13:30:42","2020-11-06 13:30:42");
INSERT INTO payments VALUES("311","spr-20201109-011527","1","","251","4","1","500","","0","Cash","","2020-11-09 13:15:27","2020-11-09 13:15:27");
INSERT INTO payments VALUES("312","spr-20201111-055902","1","","252","3","1","229.5","","0","Cash","","2020-11-11 05:59:02","2020-11-11 05:59:02");
INSERT INTO payments VALUES("313","spr-20201114-064739","1","","253","4","1","10242.5","","0","Cash","","2020-11-14 06:47:39","2020-11-14 06:47:39");
INSERT INTO payments VALUES("316","spr-20201117-064751","1","","256","4","1","715","","0","Cash","","2020-11-17 06:47:51","2020-11-17 06:47:51");
INSERT INTO payments VALUES("317","spr-20201117-070920","1","","257","4","1","250","","0","Cash","","2020-11-17 07:09:20","2020-11-17 07:09:20");
INSERT INTO payments VALUES("320","ppr-20201118-062036","1","90","","","1","33000","","0","Cash","","2020-11-18 06:20:36","2020-11-18 06:20:36");
INSERT INTO payments VALUES("321","spr-20201118-065242","1","","258","4","1","27200","","0","Cash","","2020-11-18 06:52:42","2020-11-18 06:52:42");
INSERT INTO payments VALUES("324","spr-20201208-094000","1","","263","4","1","300","","200","Cash","","2020-12-08 09:40:00","2020-12-08 09:40:00");
INSERT INTO payments VALUES("325","spr-20210106-022350","1","","263","4","1","50","","0","Cash","","2021-01-06 14:23:50","2021-01-06 14:23:50");
INSERT INTO payments VALUES("330","spr-20210111-021625","1","","271","3","1","1274","","0","Cash","","2021-01-11 14:16:25","2021-01-11 14:16:25");
INSERT INTO payments VALUES("332","spr-20210113-070243","1","","274","4","1","1556","","0","Cash","","2021-01-13 07:02:43","2021-01-13 07:02:43");
INSERT INTO payments VALUES("333","spr-20210201-023748","1","","275","4","1","484","","0","Cash","","2021-02-01 14:37:48","2021-02-01 14:37:48");
INSERT INTO payments VALUES("335","spr-20210216-070948","1","","277","4","1","15156","","0","Cash","","2021-02-16 07:09:48","2021-02-16 07:09:48");
INSERT INTO payments VALUES("336","spr-20210217-073238","1","","278","4","1","26949.9","","0","Cash","","2021-02-17 07:32:38","2021-02-17 07:32:38");
INSERT INTO payments VALUES("338","spr-20210524-074506","1","","290","3","1","250","","0","Cash","","2021-05-24 19:45:06","2021-05-24 19:45:06");
INSERT INTO payments VALUES("339","spr-20210524-074647","1","","290","3","1","-130","","0","Cash","","2021-05-24 19:46:47","2021-05-24 19:46:47");
INSERT INTO payments VALUES("341","spr-20210601-123530","1","","294","4","1","440","","0","Cash","","2021-06-01 12:35:30","2021-06-01 12:35:30");
INSERT INTO payments VALUES("342","spr-20210601-032209","1","","295","3","1","2","","0","Cash","","2021-06-01 15:22:09","2021-06-01 15:22:09");
INSERT INTO payments VALUES("343","spr-20210601-034624","1","","296","3","1","2","","0","Cash","","2021-06-01 15:46:24","2021-06-01 15:46:24");
INSERT INTO payments VALUES("344","spr-20210601-074150","9","","297","2","1","250","","0","Cash","","2021-06-01 19:41:50","2021-06-01 19:41:50");
INSERT INTO payments VALUES("345","spr-20210601-075547","9","","298","2","1","2","","0","Cash","","2021-06-01 19:55:47","2021-06-01 19:55:47");
INSERT INTO payments VALUES("346","spr-20210616-105325","1","","299","4","1","1100","","0","Cash","","2021-06-16 10:53:25","2021-06-16 10:53:25");
INSERT INTO payments VALUES("347","spr-20210616-111044","1","","300","4","1","440","","0","Cash","","2021-06-16 11:10:44","2021-06-16 11:10:44");
INSERT INTO payments VALUES("348","spr-20210616-111120","1","","301","4","1","2200","","0","Cash","","2021-06-16 11:11:20","2021-06-16 11:11:20");
INSERT INTO payments VALUES("349","spr-20210616-080543","1","","303","4","1","440","2","0","Points","","2021-06-16 20:05:43","2021-06-16 20:05:43");
INSERT INTO payments VALUES("351","spr-20210616-082851","1","","305","4","1","440","","0","Cash","","2021-06-16 20:28:51","2021-06-16 20:28:51");
INSERT INTO payments VALUES("352","spr-20210617-115553","1","","306","3","1","1100","4","0","Points","","2021-06-17 11:55:53","2021-06-17 11:55:53");
INSERT INTO payments VALUES("353","spr-20210617-121359","1","","304","4","1","440","2","0","Points","","2021-06-17 12:13:59","2021-06-17 12:13:59");
INSERT INTO payments VALUES("354","spr-20210617-030013","1","","307","3","1","100","","0","Cash","","2021-06-17 15:00:13","2021-06-17 15:00:13");
INSERT INTO payments VALUES("355","ppr-20210618-082857","1","115","","","1","101","","0","Cash","","2021-06-18 20:28:57","2021-06-18 20:28:57");
INSERT INTO payments VALUES("356","spr-20210618-083127","1","","308","4","1","1608","","392","Cash","","2021-06-18 20:31:27","2021-06-18 20:31:27");
INSERT INTO payments VALUES("357","spr-20210618-083254","1","","309","3","1","2","","0","Cash","","2021-06-18 20:32:54","2021-06-18 20:32:54");
INSERT INTO payments VALUES("358","ppr-20210619-104903","1","116","","","1","101","","0","Cash","","2021-06-19 10:49:03","2021-06-19 10:49:03");
INSERT INTO payments VALUES("359","spr-20210619-105121","1","","310","4","1","958","","42","Cash","","2021-06-19 10:51:21","2021-06-19 10:51:21");
INSERT INTO payments VALUES("360","spr-20210619-110231","1","","312","4","1","440","2","0","Points","","2021-06-19 11:02:31","2021-06-19 11:02:31");
INSERT INTO payments VALUES("361","spr-20210619-110638","1","","313","4","1","250","1","0","Points","","2021-06-19 11:06:38","2021-06-19 11:06:38");
INSERT INTO payments VALUES("362","spr-20210627-124247","1","","314","4","1","1100","","0","Cash","","2021-06-27 12:42:47","2021-06-27 12:42:47");
INSERT INTO payments VALUES("364","spr-20210707-103455","1","","317","4","1","18","","0","Cash","","2021-07-07 10:34:55","2021-07-07 10:34:55");
INSERT INTO payments VALUES("367","spr-20210814-092805","1","","325","4","1","1250","","0","Cash","","2021-08-14 21:28:05","2021-08-14 21:28:05");
INSERT INTO payments VALUES("371","spr-20210921-010722","1","","333","4","1","2","","0","Cash","","2021-09-21 13:07:22","2021-09-21 13:07:22");
INSERT INTO payments VALUES("372","spr-20210921-035020","1","","334","4","1","120","","0","Cash","","2021-09-21 15:50:20","2021-09-21 15:50:20");
INSERT INTO payments VALUES("373","spr-20210923-024704","1","","335","4","1","11","","0","Cash","","2021-09-23 14:47:04","2021-09-23 14:47:04");
INSERT INTO payments VALUES("374","spr-20211003-051829","1","","336","4","1","1220","","0","Cash","","2021-10-03 17:18:29","2021-10-03 17:18:29");
INSERT INTO payments VALUES("376","spr-20211003-054404","1","","338","4","1","1100","","0","Cash","","2021-10-03 17:44:04","2021-10-03 17:44:04");
INSERT INTO payments VALUES("378","spr-20211007-025229","1","","340","4","1","1100","","0","Cash","","2021-10-07 14:52:29","2021-10-07 14:52:29");
INSERT INTO payments VALUES("379","spr-20211007-033840","1","","341","4","1","1100","","0","Cash","","2021-10-07 15:38:40","2021-10-07 15:38:40");
INSERT INTO payments VALUES("384","spr-20211014-035825","1","","348","4","1","1100","","0","Cash","","2021-10-14 15:58:25","2021-10-14 15:58:25");
INSERT INTO payments VALUES("385","spr-20211020-011209","1","","349","4","1","102","","0","Cash","","2021-10-20 13:12:09","2021-10-20 13:12:09");
INSERT INTO payments VALUES("386","spr-20211114-013929","1","","353","4","1","374","","0","Cash","","2021-11-14 13:39:29","2021-11-14 13:39:29");
INSERT INTO payments VALUES("387","spr-20211124-062858","1","","355","4","1","3161","","0","Cash","","2021-11-24 18:28:58","2021-11-24 18:28:58");
INSERT INTO payments VALUES("388","spr-20211207-070932","1","","356","4","1","573","","0","Cash","","2021-12-07 19:09:32","2021-12-07 19:09:32");
INSERT INTO payments VALUES("391","spr-20211222-070313","1","","360","4","1","24","","0","Cash","","2021-12-22 19:03:13","2021-12-22 19:03:13");
INSERT INTO payments VALUES("393","spr-20220203-035948","1","","362","4","1","8.5","","0","Cash","","2022-02-03 15:59:48","2022-02-03 15:59:48");
INSERT INTO payments VALUES("394","spr-20220203-070602","1","","363","4","1","466","","34","Cash","","2022-02-03 19:06:02","2022-02-03 19:06:02");
INSERT INTO payments VALUES("395","spr-20220205-054526","1","","364","4","1","396","","0","Cash","","2022-02-05 17:45:26","2022-02-05 17:45:26");
INSERT INTO payments VALUES("396","spr-20220205-054616","1","","365","4","1","240","","0","Cash","","2022-02-05 17:46:16","2022-02-05 17:46:16");
INSERT INTO payments VALUES("403","spr-20220227-115905","1","","377","4","1","500","2","0","Points","","2022-02-27 11:59:05","2022-02-27 11:59:05");
INSERT INTO payments VALUES("405","spr-20220309-115901","1","","379","4","1","1790","","0","Cash","","2022-03-09 11:59:01","2022-03-09 11:59:01");
INSERT INTO payments VALUES("406","spr-20220311-091529","1","","377","4","1","10","","0","Cheque","cheque payment","2022-03-11 21:15:29","2022-03-11 21:15:29");
INSERT INTO payments VALUES("408","payment-101","1","","377","4","1","11","","0","Cash","","2022-03-12 19:12:38","2022-03-12 19:43:28");
INSERT INTO payments VALUES("409","spr-20220312-082027","1","","380","","1","440","","0","Cash","","2022-03-12 20:20:27","2022-03-12 20:20:27");
INSERT INTO payments VALUES("410","spr-20220312-082452","1","","381","","1","250","","0","Cash","","2022-03-12 20:24:52","2022-03-12 20:24:52");
INSERT INTO payments VALUES("411","spr-20220312-082622","1","","381","","1","100","","0","Cash","","2022-03-12 20:26:22","2022-03-12 20:26:22");
INSERT INTO payments VALUES("412","spr-20220424-110144","1","","382","6","1","1225","","775","Cash","","2022-04-24 11:01:44","2022-04-24 11:01:44");
INSERT INTO payments VALUES("413","ppr-20220516-112315","1","146","","","1","5500","","0","Cash","","2022-05-16 11:23:15","2022-05-16 11:23:15");
INSERT INTO payments VALUES("414","spr-20220516-112427","1","","383","6","1","3300","","700","Cash","","2022-05-16 11:24:27","2022-05-16 11:24:27");
INSERT INTO payments VALUES("416","spr-20220526-090112","1","","385","6","1","370","","0","Cash","","2022-05-26 21:01:12","2022-05-26 21:01:12");
INSERT INTO payments VALUES("417","spr-20220531-122028","1","","386","6","1","440","","0","Cash","","2022-05-31 12:20:28","2022-05-31 12:20:28");
INSERT INTO payments VALUES("427","spr-20220606-051413","1","","397","6","1","2320","","0","Cash","","2022-06-06 17:14:13","2022-06-06 17:14:13");
INSERT INTO payments VALUES("429","spr-20220611-064206","1","","1","","1","380","","0","Cash","Paid by bank cheque","2022-06-11 18:42:06","2022-06-11 18:42:06");
INSERT INTO payments VALUES("430","spr-20220611-064206","1","","4","","1","580","","0","Cash","Paid by bank cheque","2022-06-11 18:42:06","2022-06-11 18:42:06");
INSERT INTO payments VALUES("431","spr-20220611-064207","1","","29","","1","40","","0","Cash","Paid by bank cheque","2022-06-11 18:42:07","2022-06-11 18:42:07");
INSERT INTO payments VALUES("432","spr-20220611-064655","1","","29","","1","92","","0","Cash","","2022-06-11 18:46:55","2022-06-11 18:46:55");
INSERT INTO payments VALUES("433","spr-20220611-064655","1","","37","","1","208","","0","Cash","","2022-06-11 18:46:55","2022-06-11 18:46:55");
INSERT INTO payments VALUES("434","spr-20220611-065042","1","","37","","1","42","","0","Cash","","2022-06-11 18:50:42","2022-06-11 18:50:42");
INSERT INTO payments VALUES("435","spr-20220611-065042","1","","43","","1","440","","0","Cash","","2022-06-11 18:50:42","2022-06-11 18:50:42");
INSERT INTO payments VALUES("436","spr-20220611-065042","1","","104","","1","18","","0","Cash","","2022-06-11 18:50:42","2022-06-11 18:50:42");
INSERT INTO payments VALUES("437","spr-20220611-065205","1","","104","","1","100","","0","Cash","","2022-06-11 18:52:05","2022-06-11 18:52:05");
INSERT INTO payments VALUES("438","spr-20220614-110245","1","","10","6","1","50","","0","Cash","","2022-06-14 11:02:45","2022-06-14 11:02:45");
INSERT INTO payments VALUES("439","spr-20220614-110245","1","","30","6","1","320","","0","Cash","","2022-06-14 11:02:45","2022-06-14 11:02:45");
INSERT INTO payments VALUES("440","spr-20220614-110245","1","","32","6","1","130","","0","Cash","","2022-06-14 11:02:45","2022-06-14 11:02:45");
INSERT INTO payments VALUES("441","spr-20220614-115719","1","","400","6","1","3300","","0","Cash","","2022-06-14 11:57:19","2022-06-14 11:57:19");
INSERT INTO payments VALUES("444","spr-20220614-050144","1","","403","6","1","111","","0","Cash","","2022-06-14 17:01:44","2022-06-14 17:01:44");
INSERT INTO payments VALUES("445","spr-20220615-102330","1","","404","6","1","300","","0","Cash","","2022-06-15 10:23:30","2022-06-15 10:23:30");
INSERT INTO payments VALUES("448","spr-20220809-115444","1","","409","","1","591","","0","Cash","","2022-08-09 11:54:44","2022-08-09 11:54:44");
INSERT INTO payments VALUES("449","spr-20220825-013137","1","","410","6","1","120","","0","Cash","","2022-08-25 13:31:37","2022-08-25 13:31:37");
INSERT INTO payments VALUES("450","ppr-20220831-044029","1","14","","","1","100","","0","Cash","First automated payment","2022-08-31 16:40:29","2022-08-31 16:40:29");
INSERT INTO payments VALUES("451","spr-20220901-123619","1","","411","6","1","690","","0","Cash","","2022-09-01 12:36:19","2022-09-01 12:36:19");
INSERT INTO payments VALUES("452","spr-20220904-035525","1","","400","6","1","-1100","","0","Cash","payment to customer for returned products.","2022-09-04 15:55:25","2022-09-04 15:55:25");
INSERT INTO payments VALUES("453","ppr-20220911-010840","1","162","","","1","10000","","0","Cash","This is the first payment for iphone 14.","2022-09-11 13:08:40","2022-09-11 13:08:40");
INSERT INTO payments VALUES("454","spr-20220911-011826","1","","413","","1","1210","","40","Cash","","2022-09-11 13:18:26","2022-09-11 13:18:26");
INSERT INTO payments VALUES("455","spr-20220915-085900","1","","414","6","1","440","","0","Cash","","2022-09-15 20:59:00","2022-09-15 20:59:00");
INSERT INTO payments VALUES("456","ppr-20220915-085956","1","162","","","1","100","","0","Cash","","2022-09-15 20:59:56","2022-09-15 20:59:56");
INSERT INTO payments VALUES("457","spr-20221002-081636","1","","415","6","1","560","","0","Cash","","2022-10-02 20:16:36","2022-10-02 20:16:36");
INSERT INTO payments VALUES("458","ppr-20221018-020939","1","162","","","1","1000","","0","Cash","","2022-10-18 14:09:39","2022-10-18 14:09:39");
INSERT INTO payments VALUES("465","spr-20221124-055716","1","","421","6","1","1250","","0","Cash","","2022-11-24 17:57:16","2022-11-24 17:57:16");
INSERT INTO payments VALUES("466","spr-20221124-062601","9","","422","2","1","144","","0","Cash","","2022-11-24 18:26:01","2022-11-24 18:26:01");
INSERT INTO payments VALUES("467","spr-20221124-062653","9","","423","2","1","250","","0","Cash","","2022-11-24 18:26:53","2022-11-24 18:26:53");
INSERT INTO payments VALUES("468","spr-20221211-025325","1","","424","6","1","286","","0","Cash","","2022-12-11 14:53:25","2022-12-11 14:53:25");
INSERT INTO payments VALUES("469","ppr-20221211-025429","1","164","","","1","90","","0","Cash","","2022-12-11 14:54:29","2022-12-11 14:54:29");
INSERT INTO payments VALUES("470","spr-20230111-123925","1","","426","6","1","144","","0","Cash","","2023-01-11 00:39:25","2023-01-11 00:39:25");
INSERT INTO payments VALUES("471","spr-20230111-124000","1","","427","6","1","144","","0","Cash","","2023-01-11 00:40:00","2023-01-11 00:40:00");
INSERT INTO payments VALUES("472","spr-20230118-042810","1","","428","6","1","544","","0","Cash","","2023-01-18 16:28:10","2023-01-18 16:28:10");
INSERT INTO payments VALUES("473","spr-20230121-032002","1","","429","6","1","350","","0","Credit Card","hbhb","2023-01-21 15:20:02","2023-01-21 15:20:02");
INSERT INTO payments VALUES("474","spr-20230121-070440","1","","430","6","1","350","","0","Credit Card","paid by credit card","2023-01-21 19:04:40","2023-01-21 19:04:40");
INSERT INTO payments VALUES("475","spr-20230121-071710","1","","431","6","1","350","","0","Credit Card","paid by credit card","2023-01-21 19:17:10","2023-01-21 19:17:10");
INSERT INTO payments VALUES("476","spr-20230121-071843","1","","432","6","1","350","","0","Cash","paid by credit card","2023-01-21 19:18:43","2023-01-21 19:18:43");
INSERT INTO payments VALUES("477","spr-20230222-030251","1","","433","6","1","400","","0","Cash","","2023-02-22 15:02:51","2023-02-22 15:02:51");
INSERT INTO payments VALUES("478","spr-20230223-045811","1","","434","6","1","144","","0","Cash","","2023-02-23 16:58:11","2023-02-23 16:58:11");
INSERT INTO payments VALUES("479","spr-20230224-011142","1","","435","6","1","794","","0","Cash","","2023-02-24 01:11:42","2023-02-24 01:11:42");
INSERT INTO payments VALUES("480","spr-20230224-011448","1","","436","6","1","794","","0","Credit Card","","2023-02-24 01:14:48","2023-02-24 01:14:48");
INSERT INTO payments VALUES("481","spr-20230224-011503","1","","437","6","1","794","","0","Cash","","2023-02-24 01:15:03","2023-02-24 01:15:03");
INSERT INTO payments VALUES("482","spr-20230224-115203","1","","438","6","1","816","","0","Cash","","2023-02-24 11:52:03","2023-02-24 11:52:03");
INSERT INTO payments VALUES("487","spr-20230313-053110","1","","455","6","1","360","","0","Cash","","2023-03-13 17:31:10","2023-03-13 17:31:10");
INSERT INTO payments VALUES("488","spr-20230323-053359","1","","458","6","1","99","","0","Cash","","2023-03-23 17:33:59","2023-03-23 17:33:59");
INSERT INTO payments VALUES("489","spr-20230327-110818","1","","460","6","1","709","","0","Cash","","2023-03-27 11:08:18","2023-03-27 11:08:18");
INSERT INTO payments VALUES("491","spr-20230405-013919","1","","462","6","1","538","","0","Cash","","2023-04-05 13:39:19","2023-04-05 13:39:19");
INSERT INTO payments VALUES("492","ppr-20230405-014016","1","171","","","1","288","","0","Cash","","2023-04-05 13:40:16","2023-04-05 13:40:16");
INSERT INTO payments VALUES("493","ppr-20230405-014053","1","173","","","1","400","","0","Cash","","2023-04-05 13:40:53","2023-04-05 13:40:53");
INSERT INTO payments VALUES("499","spr-20230506-040646","1","","469","6","1","1700","","0","Cash","","2023-05-06 16:06:46","2023-05-06 16:06:46");
INSERT INTO payments VALUES("500","ppr-20230506-040820","1","174","","","1","400","","0","Cash","","2023-05-06 16:08:20","2023-05-06 16:08:20");
INSERT INTO payments VALUES("501","spr-20230517-013623","1","","470","6","1","340","","0","Cash","","2023-05-17 13:36:23","2023-05-17 13:36:23");
INSERT INTO payments VALUES("502","spr-20230604-021435","1","","471","6","1","250","","0","Cash","","2023-06-04 14:14:35","2023-06-04 14:14:35");
INSERT INTO payments VALUES("503","spr-20230604-034829","1","","472","6","1","166","","0","Cash","","2023-06-04 15:48:29","2023-06-04 15:48:29");
INSERT INTO payments VALUES("504","ppr-20230604-035004","1","174","","","1","200","","0","Cash","","2023-06-04 15:50:04","2023-06-04 15:50:04");
INSERT INTO payments VALUES("505","spr-20230605-030712","1","","473","6","1","225","","0","Cash","","2023-06-05 15:07:12","2023-06-05 15:07:12");
INSERT INTO payments VALUES("506","spr-20230708-043449","1","","473","6","1","0","","0","Cash","test","2023-07-08 16:34:49","2023-07-08 16:34:49");
INSERT INTO payments VALUES("507","ppr-20230709-045418","1","12","","6","6","500","","0","Cash","tt","2023-07-09 16:54:18","2023-07-09 16:54:18");
INSERT INTO payments VALUES("508","ppr-20230709-045447","1","12","","6","6","975","","0","Cash","jjj","2023-07-09 16:54:47","2023-07-09 16:54:47");



CREATE TABLE `payrolls` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `employee_id` int NOT NULL,
  `account_id` int NOT NULL,
  `user_id` int NOT NULL,
  `amount` double NOT NULL,
  `paying_method` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO payrolls VALUES("9","payroll-20191204-113802","1","1","1","10000","0","","2019-12-04 23:38:02","2019-12-04 23:38:02");
INSERT INTO payrolls VALUES("10","payroll-20210619-123706","1","1","1","200","0","","2021-06-19 12:37:06","2021-06-19 12:37:06");
INSERT INTO payrolls VALUES("11","payroll-20230709-033102","4","6","1","100","0","test","2023-07-09 15:31:02","2023-07-09 15:31:02");



CREATE TABLE `permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permissions VALUES("4","products-edit","web","2018-06-03 07:00:09","2018-06-03 07:00:09");
INSERT INTO permissions VALUES("5","products-delete","web","2018-06-04 04:54:22","2018-06-04 04:54:22");
INSERT INTO permissions VALUES("6","products-add","web","2018-06-04 06:34:14","2018-06-04 06:34:14");
INSERT INTO permissions VALUES("7","products-index","web","2018-06-04 09:34:27","2018-06-04 09:34:27");
INSERT INTO permissions VALUES("8","purchases-index","web","2018-06-04 14:03:19","2018-06-04 14:03:19");
INSERT INTO permissions VALUES("9","purchases-add","web","2018-06-04 14:12:25","2018-06-04 14:12:25");
INSERT INTO permissions VALUES("10","purchases-edit","web","2018-06-04 15:47:36","2018-06-04 15:47:36");
INSERT INTO permissions VALUES("11","purchases-delete","web","2018-06-04 15:47:36","2018-06-04 15:47:36");
INSERT INTO permissions VALUES("12","sales-index","web","2018-06-04 16:49:08","2018-06-04 16:49:08");
INSERT INTO permissions VALUES("13","sales-add","web","2018-06-04 16:49:52","2018-06-04 16:49:52");
INSERT INTO permissions VALUES("14","sales-edit","web","2018-06-04 16:49:52","2018-06-04 16:49:52");
INSERT INTO permissions VALUES("15","sales-delete","web","2018-06-04 16:49:53","2018-06-04 16:49:53");
INSERT INTO permissions VALUES("16","quotes-index","web","2018-06-05 04:05:10","2018-06-05 04:05:10");
INSERT INTO permissions VALUES("17","quotes-add","web","2018-06-05 04:05:10","2018-06-05 04:05:10");
INSERT INTO permissions VALUES("18","quotes-edit","web","2018-06-05 04:05:10","2018-06-05 04:05:10");
INSERT INTO permissions VALUES("19","quotes-delete","web","2018-06-05 04:05:10","2018-06-05 04:05:10");
INSERT INTO permissions VALUES("20","transfers-index","web","2018-06-05 04:30:03","2018-06-05 04:30:03");
INSERT INTO permissions VALUES("21","transfers-add","web","2018-06-05 04:30:03","2018-06-05 04:30:03");
INSERT INTO permissions VALUES("22","transfers-edit","web","2018-06-05 04:30:03","2018-06-05 04:30:03");
INSERT INTO permissions VALUES("23","transfers-delete","web","2018-06-05 04:30:03","2018-06-05 04:30:03");
INSERT INTO permissions VALUES("24","returns-index","web","2018-06-05 04:50:24","2018-06-05 04:50:24");
INSERT INTO permissions VALUES("25","returns-add","web","2018-06-05 04:50:24","2018-06-05 04:50:24");
INSERT INTO permissions VALUES("26","returns-edit","web","2018-06-05 04:50:25","2018-06-05 04:50:25");
INSERT INTO permissions VALUES("27","returns-delete","web","2018-06-05 04:50:25","2018-06-05 04:50:25");
INSERT INTO permissions VALUES("28","customers-index","web","2018-06-05 05:15:54","2018-06-05 05:15:54");
INSERT INTO permissions VALUES("29","customers-add","web","2018-06-05 05:15:55","2018-06-05 05:15:55");
INSERT INTO permissions VALUES("30","customers-edit","web","2018-06-05 05:15:55","2018-06-05 05:15:55");
INSERT INTO permissions VALUES("31","customers-delete","web","2018-06-05 05:15:55","2018-06-05 05:15:55");
INSERT INTO permissions VALUES("32","suppliers-index","web","2018-06-05 05:40:12","2018-06-05 05:40:12");
INSERT INTO permissions VALUES("33","suppliers-add","web","2018-06-05 05:40:12","2018-06-05 05:40:12");
INSERT INTO permissions VALUES("34","suppliers-edit","web","2018-06-05 05:40:12","2018-06-05 05:40:12");
INSERT INTO permissions VALUES("35","suppliers-delete","web","2018-06-05 05:40:12","2018-06-05 05:40:12");
INSERT INTO permissions VALUES("36","product-report","web","2018-06-25 05:05:33","2018-06-25 05:05:33");
INSERT INTO permissions VALUES("37","purchase-report","web","2018-06-25 05:24:56","2018-06-25 05:24:56");
INSERT INTO permissions VALUES("38","sale-report","web","2018-06-25 05:33:13","2018-06-25 05:33:13");
INSERT INTO permissions VALUES("39","customer-report","web","2018-06-25 05:36:51","2018-06-25 05:36:51");
INSERT INTO permissions VALUES("40","due-report","web","2018-06-25 05:39:52","2018-06-25 05:39:52");
INSERT INTO permissions VALUES("41","users-index","web","2018-06-25 06:00:10","2018-06-25 06:00:10");
INSERT INTO permissions VALUES("42","users-add","web","2018-06-25 06:00:10","2018-06-25 06:00:10");
INSERT INTO permissions VALUES("43","users-edit","web","2018-06-25 06:01:30","2018-06-25 06:01:30");
INSERT INTO permissions VALUES("44","users-delete","web","2018-06-25 06:01:30","2018-06-25 06:01:30");
INSERT INTO permissions VALUES("45","profit-loss","web","2018-07-15 03:50:05","2018-07-15 03:50:05");
INSERT INTO permissions VALUES("46","best-seller","web","2018-07-15 04:01:38","2018-07-15 04:01:38");
INSERT INTO permissions VALUES("47","daily-sale","web","2018-07-15 04:24:21","2018-07-15 04:24:21");
INSERT INTO permissions VALUES("48","monthly-sale","web","2018-07-15 04:30:41","2018-07-15 04:30:41");
INSERT INTO permissions VALUES("49","daily-purchase","web","2018-07-15 04:36:46","2018-07-15 04:36:46");
INSERT INTO permissions VALUES("50","monthly-purchase","web","2018-07-15 04:48:17","2018-07-15 04:48:17");
INSERT INTO permissions VALUES("51","payment-report","web","2018-07-15 05:10:41","2018-07-15 05:10:41");
INSERT INTO permissions VALUES("52","warehouse-stock-report","web","2018-07-15 05:16:55","2018-07-15 05:16:55");
INSERT INTO permissions VALUES("53","product-qty-alert","web","2018-07-15 05:33:21","2018-07-15 05:33:21");
INSERT INTO permissions VALUES("54","supplier-report","web","2018-07-30 09:00:01","2018-07-30 09:00:01");
INSERT INTO permissions VALUES("55","expenses-index","web","2018-09-05 07:07:10","2018-09-05 07:07:10");
INSERT INTO permissions VALUES("56","expenses-add","web","2018-09-05 07:07:10","2018-09-05 07:07:10");
INSERT INTO permissions VALUES("57","expenses-edit","web","2018-09-05 07:07:10","2018-09-05 07:07:10");
INSERT INTO permissions VALUES("58","expenses-delete","web","2018-09-05 07:07:11","2018-09-05 07:07:11");
INSERT INTO permissions VALUES("59","general_setting","web","2018-10-20 05:10:04","2018-10-20 05:10:04");
INSERT INTO permissions VALUES("60","mail_setting","web","2018-10-20 05:10:04","2018-10-20 05:10:04");
INSERT INTO permissions VALUES("61","pos_setting","web","2018-10-20 05:10:04","2018-10-20 05:10:04");
INSERT INTO permissions VALUES("62","hrm_setting","web","2019-01-02 16:30:23","2019-01-02 16:30:23");
INSERT INTO permissions VALUES("63","purchase-return-index","web","2019-01-03 03:45:14","2019-01-03 03:45:14");
INSERT INTO permissions VALUES("64","purchase-return-add","web","2019-01-03 03:45:14","2019-01-03 03:45:14");
INSERT INTO permissions VALUES("65","purchase-return-edit","web","2019-01-03 03:45:14","2019-01-03 03:45:14");
INSERT INTO permissions VALUES("66","purchase-return-delete","web","2019-01-03 03:45:14","2019-01-03 03:45:14");
INSERT INTO permissions VALUES("67","account-index","web","2019-01-03 04:06:13","2019-01-03 04:06:13");
INSERT INTO permissions VALUES("68","balance-sheet","web","2019-01-03 04:06:14","2019-01-03 04:06:14");
INSERT INTO permissions VALUES("69","account-statement","web","2019-01-03 04:06:14","2019-01-03 04:06:14");
INSERT INTO permissions VALUES("70","department","web","2019-01-03 04:30:01","2019-01-03 04:30:01");
INSERT INTO permissions VALUES("71","attendance","web","2019-01-03 04:30:01","2019-01-03 04:30:01");
INSERT INTO permissions VALUES("72","payroll","web","2019-01-03 04:30:01","2019-01-03 04:30:01");
INSERT INTO permissions VALUES("73","employees-index","web","2019-01-03 04:52:19","2019-01-03 04:52:19");
INSERT INTO permissions VALUES("74","employees-add","web","2019-01-03 04:52:19","2019-01-03 04:52:19");
INSERT INTO permissions VALUES("75","employees-edit","web","2019-01-03 04:52:19","2019-01-03 04:52:19");
INSERT INTO permissions VALUES("76","employees-delete","web","2019-01-03 04:52:19","2019-01-03 04:52:19");
INSERT INTO permissions VALUES("77","user-report","web","2019-01-16 12:48:18","2019-01-16 12:48:18");
INSERT INTO permissions VALUES("78","stock_count","web","2019-02-17 16:32:01","2019-02-17 16:32:01");
INSERT INTO permissions VALUES("79","adjustment","web","2019-02-17 16:32:02","2019-02-17 16:32:02");
INSERT INTO permissions VALUES("80","sms_setting","web","2019-02-22 11:18:03","2019-02-22 11:18:03");
INSERT INTO permissions VALUES("81","create_sms","web","2019-02-22 11:18:03","2019-02-22 11:18:03");
INSERT INTO permissions VALUES("82","print_barcode","web","2019-03-07 11:02:19","2019-03-07 11:02:19");
INSERT INTO permissions VALUES("83","empty_database","web","2019-03-07 11:02:19","2019-03-07 11:02:19");
INSERT INTO permissions VALUES("84","customer_group","web","2019-03-07 11:37:15","2019-03-07 11:37:15");
INSERT INTO permissions VALUES("85","unit","web","2019-03-07 11:37:15","2019-03-07 11:37:15");
INSERT INTO permissions VALUES("86","tax","web","2019-03-07 11:37:15","2019-03-07 11:37:15");
INSERT INTO permissions VALUES("87","gift_card","web","2019-03-07 12:29:38","2019-03-07 12:29:38");
INSERT INTO permissions VALUES("88","coupon","web","2019-03-07 12:29:38","2019-03-07 12:29:38");
INSERT INTO permissions VALUES("89","holiday","web","2019-10-19 14:57:15","2019-10-19 14:57:15");
INSERT INTO permissions VALUES("90","warehouse-report","web","2019-10-22 12:00:23","2019-10-22 12:00:23");
INSERT INTO permissions VALUES("91","warehouse","web","2020-02-26 12:47:32","2020-02-26 12:47:32");
INSERT INTO permissions VALUES("92","brand","web","2020-02-26 12:59:59","2020-02-26 12:59:59");
INSERT INTO permissions VALUES("93","billers-index","web","2020-02-26 13:11:15","2020-02-26 13:11:15");
INSERT INTO permissions VALUES("94","billers-add","web","2020-02-26 13:11:15","2020-02-26 13:11:15");
INSERT INTO permissions VALUES("95","billers-edit","web","2020-02-26 13:11:15","2020-02-26 13:11:15");
INSERT INTO permissions VALUES("96","billers-delete","web","2020-02-26 13:11:15","2020-02-26 13:11:15");
INSERT INTO permissions VALUES("97","money-transfer","web","2020-03-02 11:41:48","2020-03-02 11:41:48");
INSERT INTO permissions VALUES("98","category","web","2020-07-13 18:13:16","2020-07-13 18:13:16");
INSERT INTO permissions VALUES("99","delivery","web","2020-07-13 18:13:16","2020-07-13 18:13:16");
INSERT INTO permissions VALUES("100","send_notification","web","2020-10-31 12:21:31","2020-10-31 12:21:31");
INSERT INTO permissions VALUES("101","today_sale","web","2020-10-31 12:57:04","2020-10-31 12:57:04");
INSERT INTO permissions VALUES("102","today_profit","web","2020-10-31 12:57:04","2020-10-31 12:57:04");
INSERT INTO permissions VALUES("103","currency","web","2020-11-09 06:23:11","2020-11-09 06:23:11");
INSERT INTO permissions VALUES("104","backup_database","web","2020-11-15 06:16:55","2020-11-15 06:16:55");
INSERT INTO permissions VALUES("105","reward_point_setting","web","2021-06-27 10:34:42","2021-06-27 10:34:42");
INSERT INTO permissions VALUES("106","revenue_profit_summary","web","2022-02-08 19:57:21","2022-02-08 19:57:21");
INSERT INTO permissions VALUES("107","cash_flow","web","2022-02-08 19:57:22","2022-02-08 19:57:22");
INSERT INTO permissions VALUES("108","monthly_summary","web","2022-02-08 19:57:22","2022-02-08 19:57:22");
INSERT INTO permissions VALUES("109","yearly_report","web","2022-02-08 19:57:22","2022-02-08 19:57:22");
INSERT INTO permissions VALUES("110","discount_plan","web","2022-02-16 15:12:26","2022-02-16 15:12:26");
INSERT INTO permissions VALUES("111","discount","web","2022-02-16 15:12:38","2022-02-16 15:12:38");
INSERT INTO permissions VALUES("112","product-expiry-report","web","2022-03-30 11:39:20","2022-03-30 11:39:20");
INSERT INTO permissions VALUES("113","purchase-payment-index","web","2022-06-05 20:12:27","2022-06-05 20:12:27");
INSERT INTO permissions VALUES("114","purchase-payment-add","web","2022-06-05 20:12:28","2022-06-05 20:12:28");
INSERT INTO permissions VALUES("115","purchase-payment-edit","web","2022-06-05 20:12:28","2022-06-05 20:12:28");
INSERT INTO permissions VALUES("116","purchase-payment-delete","web","2022-06-05 20:12:28","2022-06-05 20:12:28");
INSERT INTO permissions VALUES("117","sale-payment-index","web","2022-06-05 20:12:28","2022-06-05 20:12:28");
INSERT INTO permissions VALUES("118","sale-payment-add","web","2022-06-05 20:12:28","2022-06-05 20:12:28");
INSERT INTO permissions VALUES("119","sale-payment-edit","web","2022-06-05 20:12:28","2022-06-05 20:12:28");
INSERT INTO permissions VALUES("120","sale-payment-delete","web","2022-06-05 20:12:28","2022-06-05 20:12:28");
INSERT INTO permissions VALUES("121","all_notification","web","2022-06-05 20:12:29","2022-06-05 20:12:29");
INSERT INTO permissions VALUES("122","sale-report-chart","web","2022-06-05 20:12:29","2022-06-05 20:12:29");
INSERT INTO permissions VALUES("123","dso-report","web","2022-06-05 20:12:29","2022-06-05 20:12:29");
INSERT INTO permissions VALUES("124","product_history","web","2022-08-25 20:04:05","2022-08-25 20:04:05");
INSERT INTO permissions VALUES("125","supplier-due-report","web","2022-08-31 15:46:33","2022-08-31 15:46:33");
INSERT INTO permissions VALUES("126","custom_field","web","2023-05-02 13:41:35","2023-05-02 13:41:35");



CREATE TABLE `pos_setting` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `warehouse_id` int NOT NULL,
  `biller_id` int NOT NULL,
  `product_number` int NOT NULL,
  `keybord_active` tinyint(1) NOT NULL,
  `is_table` tinyint(1) NOT NULL DEFAULT '0',
  `stripe_public_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stripe_secret_key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paypal_live_api_username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paypal_live_api_password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paypal_live_api_secret` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_options` text COLLATE utf8mb4_unicode_ci,
  `invoice_option` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `pos_setting_id_unique` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO pos_setting VALUES("1","11","2","1","3","0","0","pk_test_ITN7KOYiIsHSCQ0UMRcgaYUB","sk_test_TtQQaawhEYRwa3mU9CzttrEy","admin","admin","bkbkh","cash,card,cheque,gift_card,deposit,paypal","thermal","2018-09-02 09:17:04","2023-06-04 16:15:51");



CREATE TABLE `product_adjustments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `adjustment_id` int NOT NULL,
  `product_id` int NOT NULL,
  `variant_id` int DEFAULT NULL,
  `qty` double NOT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO product_adjustments VALUES("16","13","5","","3","-","2023-04-01 21:30:50","2023-04-01 21:30:50");
INSERT INTO product_adjustments VALUES("19","16","32","","1","-","2023-07-10 15:21:27","2023-07-10 15:21:27");



CREATE TABLE `product_batches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `batch_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expired_date` date NOT NULL,
  `qty` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO product_batches VALUES("1","71","1001","2023-05-31","13","2021-03-07 14:14:05","2022-05-14 14:50:17");
INSERT INTO product_batches VALUES("2","71","1002","2023-05-31","6","2021-03-07 14:55:06","2021-06-18 20:32:54");
INSERT INTO product_batches VALUES("3","71","1003","2023-09-30","2","2021-03-09 15:42:13","2021-09-21 15:49:37");
INSERT INTO product_batches VALUES("4","22","10001","2021-11-30","1","2021-10-20 13:47:09","2021-10-20 13:47:09");



CREATE TABLE `product_purchases` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `purchase_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_batch_id` int DEFAULT NULL,
  `variant_id` int DEFAULT NULL,
  `imei_number` text COLLATE utf8mb4_unicode_ci,
  `qty` double NOT NULL,
  `recieved` double NOT NULL,
  `purchase_unit_id` int NOT NULL,
  `net_unit_cost` double NOT NULL,
  `discount` double NOT NULL,
  `tax_rate` double NOT NULL,
  `tax` double NOT NULL,
  `total` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=457 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO product_purchases VALUES("59","12","4","","","","200","200","1","1","0","0","0","200","2018-08-09 05:48:36","2018-08-09 05:48:36");
INSERT INTO product_purchases VALUES("60","12","5","","","","100","100","1","100","0","0","0","10000","2018-08-09 05:48:36","2018-08-09 05:48:36");
INSERT INTO product_purchases VALUES("66","13","2","","","","100","100","3","166.96","0","15","2504.35","19200","2018-08-09 05:49:55","2018-08-09 05:49:55");
INSERT INTO product_purchases VALUES("67","13","3","","","","100","100","1","200","0","0","0","20000","2018-08-09 05:49:55","2018-08-09 05:49:55");
INSERT INTO product_purchases VALUES("68","13","1","","","","150","150","1","320","0","10","4800","52800","2018-08-09 05:49:55","2018-08-09 05:49:55");
INSERT INTO product_purchases VALUES("69","13","10","","","","60","60","7","10","0","0","0","600","2018-08-09 05:49:55","2018-08-09 05:49:55");
INSERT INTO product_purchases VALUES("70","14","1","","","","100","100","1","320","0","10","3200","35200","2018-08-09 13:23:48","2018-08-09 13:23:48");
INSERT INTO product_purchases VALUES("71","14","2","","","","50","50","3","166.96","0","15","1252.17","9600","2018-08-09 13:23:48","2018-08-09 13:23:48");
INSERT INTO product_purchases VALUES("72","14","3","","","","100","100","1","200","0","0","0","20000","2018-08-09 13:23:49","2018-08-09 13:23:49");
INSERT INTO product_purchases VALUES("73","14","5","","","","100","100","1","100","0","0","0","10000","2018-08-09 13:23:49","2018-08-09 13:23:49");
INSERT INTO product_purchases VALUES("74","14","10","","","","50","50","7","10","0","0","0","500","2018-08-09 13:23:49","2018-08-09 13:23:49");
INSERT INTO product_purchases VALUES("76","15","22","","","","20","20","1","800","0","10","1600","17600","2018-09-03 10:06:46","2018-09-03 10:06:46");
INSERT INTO product_purchases VALUES("87","16","22","","","","20","20","1","800","0","10","1600","17600","2018-09-20 15:09:12","2018-09-20 15:09:12");
INSERT INTO product_purchases VALUES("89","18","4","","","","50","50","1","1","0","0","0","50","2018-10-22 16:26:25","2018-10-22 16:26:25");
INSERT INTO product_purchases VALUES("90","19","4","","","","50","50","1","1","0","0","0","50","2018-10-22 16:26:52","2018-10-22 16:26:52");
INSERT INTO product_purchases VALUES("91","20","25","","","","15","15","1","500","0","10","750","8250","2018-10-23 07:14:21","2018-10-23 07:14:21");
INSERT INTO product_purchases VALUES("93","21","25","","","","15","15","1","500","0","10","750","8250","2018-10-23 07:14:58","2018-10-23 07:14:58");
INSERT INTO product_purchases VALUES("94","22","22","","","","5","5","1","800","0","10","400","4400","2018-11-01 04:59:03","2018-11-01 04:59:03");
INSERT INTO product_purchases VALUES("96","23","22","","","","5","5","1","800","0","10","400","4400","2018-11-03 09:23:52","2018-11-03 09:23:52");
INSERT INTO product_purchases VALUES("97","24","22","","","","15","15","1","800","0","10","1200","13200","2018-11-05 09:18:19","2018-11-05 09:18:19");
INSERT INTO product_purchases VALUES("98","24","25","","","","5","5","1","500","0","10","250","2750","2018-11-05 09:18:19","2018-11-05 09:18:19");
INSERT INTO product_purchases VALUES("99","25","31","","","","15","15","1","250","0","0","0","3750","2018-12-05 05:34:30","2018-12-05 05:34:30");
INSERT INTO product_purchases VALUES("100","25","30","","","","15","15","1","50","0","0","0","750","2018-12-05 05:34:30","2018-12-05 05:34:30");
INSERT INTO product_purchases VALUES("101","26","31","","","","15","15","1","250","0","0","0","3750","2018-12-05 05:35:08","2018-12-05 05:35:08");
INSERT INTO product_purchases VALUES("102","26","30","","","","15","15","1","50","0","0","0","750","2018-12-05 05:35:08","2018-12-05 05:35:08");
INSERT INTO product_purchases VALUES("104","27","32","","","","10","10","1","1","0","0","0","10","2018-12-19 05:57:41","2018-12-19 05:57:41");
INSERT INTO product_purchases VALUES("112","33","33","","","","10","10","1","1","0","0","0","10","2018-12-24 09:04:21","2018-12-24 09:04:21");
INSERT INTO product_purchases VALUES("113","34","25","","","","2","2","1","500","0","10","100","1100","2019-01-03 07:01:24","2019-01-03 07:01:24");
INSERT INTO product_purchases VALUES("114","34","22","","","","2","2","1","800","0","10","160","1760","2019-01-03 07:01:24","2019-01-03 07:01:24");
INSERT INTO product_purchases VALUES("115","35","31","","","","2","2","1","250","0","0","0","500","2019-01-29 09:54:48","2019-01-29 09:54:48");
INSERT INTO product_purchases VALUES("116","35","30","","","","2","2","1","50","0","0","0","100","2019-01-29 09:54:48","2019-01-29 09:54:48");
INSERT INTO product_purchases VALUES("117","36","30","","","","3","3","1","50","0","0","0","150","2019-01-29 09:55:58","2019-01-29 09:55:58");
INSERT INTO product_purchases VALUES("118","36","31","","","","2","2","1","250","0","0","0","500","2019-01-29 09:55:58","2019-01-29 09:55:58");
INSERT INTO product_purchases VALUES("121","39","1","","","","2","2","1","315","10","10","63","693","2019-02-09 10:44:13","2019-02-09 10:44:13");
INSERT INTO product_purchases VALUES("122","39","2","","","","1","1","3","192","0","0","0","192","2019-02-09 10:44:13","2019-02-09 10:44:13");
INSERT INTO product_purchases VALUES("123","38","32","","","","10","10","1","1","0","0","0","10","2019-02-09 10:45:24","2019-02-09 10:45:24");
INSERT INTO product_purchases VALUES("124","38","25","","","","3","3","1","500","0","10","150","1650","2019-02-09 10:45:24","2019-02-09 10:45:24");
INSERT INTO product_purchases VALUES("125","37","33","","","","10","10","1","1","0","0","0","10","2019-02-09 10:46:22","2019-02-09 10:46:22");
INSERT INTO product_purchases VALUES("126","37","25","","","","2","2","1","500","0","10","100","1100","2019-02-09 10:46:23","2019-02-09 10:46:23");
INSERT INTO product_purchases VALUES("127","37","22","","","","6","6","1","800","0","10","480","5280","2019-02-09 10:46:23","2019-02-09 10:46:23");
INSERT INTO product_purchases VALUES("128","40","33","","","","10","10","1","10","0","0","0","100","2019-03-03 10:39:17","2019-03-03 10:39:17");
INSERT INTO product_purchases VALUES("129","41","33","","","","5","5","1","10","0","0","0","50","2019-03-03 10:43:58","2019-03-03 10:43:58");
INSERT INTO product_purchases VALUES("130","41","1","","","","10","10","1","320","0","10","320","3520","2019-03-03 10:43:59","2019-03-03 10:43:59");
INSERT INTO product_purchases VALUES("133","42","30","","","","1","1","1","50","0","0","0","50","2019-04-13 19:50:08","2019-04-13 19:50:08");
INSERT INTO product_purchases VALUES("134","42","31","","","","1","1","1","250","0","0","0","250","2019-04-13 19:50:08","2019-04-13 19:50:08");
INSERT INTO product_purchases VALUES("135","43","25","","","","3","3","1","500","0","10","150","1650","2019-06-13 10:16:00","2019-06-13 10:16:00");
INSERT INTO product_purchases VALUES("136","43","22","","","","3","3","1","800","0","10","240","2640","2019-06-13 10:16:01","2019-06-13 10:16:01");
INSERT INTO product_purchases VALUES("137","44","25","","","","1","1","1","500","0","10","50","550","2019-10-19 15:31:19","2019-10-19 15:31:19");
INSERT INTO product_purchases VALUES("138","44","22","","","","1","1","1","800","0","10","80","880","2019-10-19 15:31:20","2019-10-19 15:31:20");
INSERT INTO product_purchases VALUES("140","46","33","","","","10","10","1","10","0","0","0","100","2019-11-03 11:39:49","2019-11-03 11:39:49");
INSERT INTO product_purchases VALUES("141","46","32","","","","10","10","1","5","0","0","0","50","2019-11-03 11:39:49","2019-11-03 11:39:49");
INSERT INTO product_purchases VALUES("142","47","1","","","","2","2","1","315","10","10","63","693","2019-11-09 11:25:10","2019-11-09 11:25:10");
INSERT INTO product_purchases VALUES("143","47","2","","","","1","1","3","192","0","0","0","192","2019-11-09 11:25:10","2019-11-09 11:25:10");
INSERT INTO product_purchases VALUES("144","48","4","","","","100","100","1","1","0","0","0","100","2019-11-10 19:02:21","2019-11-10 19:02:21");
INSERT INTO product_purchases VALUES("149","49","22","","","","10","10","1","800","0","10","800","8800","2019-11-11 10:28:59","2019-11-11 10:28:59");
INSERT INTO product_purchases VALUES("158","55","48","","3","","1","1","1","2","0","0","0","2","2019-11-27 22:28:35","2019-11-27 22:28:35");
INSERT INTO product_purchases VALUES("159","55","48","","2","","1","1","1","2","0","0","0","2","2019-11-27 22:28:35","2019-11-27 22:28:35");
INSERT INTO product_purchases VALUES("161","57","3","","","","1","1","1","200","0","0","0","200","2019-12-04 23:07:49","2019-12-04 23:07:49");
INSERT INTO product_purchases VALUES("162","58","48","","2","","1","1","1","2","0","0","0","2","2019-12-05 10:21:10","2019-12-05 10:21:10");
INSERT INTO product_purchases VALUES("163","58","48","","3","","1","1","1","2","0","0","0","2","2019-12-05 10:21:10","2019-12-05 10:21:10");
INSERT INTO product_purchases VALUES("169","59","48","","3","","1","1","1","2","0","0","0","2","2019-12-21 16:22:29","2019-12-21 16:22:29");
INSERT INTO product_purchases VALUES("170","59","48","","2","","1","1","1","2","0","0","0","2","2019-12-21 16:22:29","2019-12-21 16:22:29");
INSERT INTO product_purchases VALUES("171","59","48","","5","","1","1","1","2","0","0","0","2","2019-12-21 16:22:29","2019-12-21 16:22:29");
INSERT INTO product_purchases VALUES("174","56","48","","2","","1","1","1","2","0","0","0","2","2019-12-21 19:27:16","2019-12-21 19:27:16");
INSERT INTO product_purchases VALUES("178","61","48","","3","","10","10","1","2","0","0","0","20","2020-01-01 13:06:31","2020-01-01 13:06:31");
INSERT INTO product_purchases VALUES("179","61","48","","2","","10","10","1","2","0","0","0","20","2020-01-01 13:06:31","2020-01-01 13:06:31");
INSERT INTO product_purchases VALUES("180","61","48","","5","","10","10","1","2","0","0","0","20","2020-01-01 13:06:31","2020-01-01 13:06:31");
INSERT INTO product_purchases VALUES("181","62","25","","","","3","3","1","500","0","10","150","1650","2020-01-01 14:24:02","2020-01-01 14:24:02");
INSERT INTO product_purchases VALUES("209","67","31","","","","1","1","1","250","0","0","0","250","2020-02-04 23:00:41","2020-02-04 23:00:41");
INSERT INTO product_purchases VALUES("210","67","30","","","","1","1","1","50","0","0","0","50","2020-02-04 23:00:41","2020-02-04 23:00:41");
INSERT INTO product_purchases VALUES("212","69","4","","","","50","50","1","1","0","0","0","50","2020-03-02 11:55:10","2020-03-02 11:55:10");
INSERT INTO product_purchases VALUES("213","70","4","","","","50","50","1","1","0","0","0","50","2020-03-02 11:56:03","2020-03-02 11:56:03");
INSERT INTO product_purchases VALUES("214","71","25","","","","3","3","1","500","0","10","150","1650","2020-04-06 19:35:12","2020-04-06 19:35:12");
INSERT INTO product_purchases VALUES("215","71","31","","","","5","5","1","250","0","0","0","1250","2020-04-06 19:35:12","2020-04-06 19:35:12");
INSERT INTO product_purchases VALUES("216","71","30","","","","3","3","1","50","0","0","0","150","2020-04-06 19:35:12","2020-04-06 19:35:12");
INSERT INTO product_purchases VALUES("217","72","61","","","","10","10","1","2500","5000","15","3750","28750","2020-08-16 22:02:07","2020-08-16 22:02:07");
INSERT INTO product_purchases VALUES("219","73","62","","12","","2","2","1","1","0","0","0","2","2020-09-27 15:07:44","2020-09-27 15:07:44");
INSERT INTO product_purchases VALUES("221","74","61","","","","11","11","1","3000","0","15","4950","37950","2020-10-24 07:03:16","2020-10-24 07:03:16");
INSERT INTO product_purchases VALUES("222","74","22","","","","4","4","1","800","0","10","320","3520","2020-10-24 07:03:16","2020-10-24 07:03:16");
INSERT INTO product_purchases VALUES("232","83","60","","9","","2","2","1","1","0","0","0","2","2020-10-27 04:56:11","2020-10-27 04:56:11");
INSERT INTO product_purchases VALUES("233","84","60","","9","","2","2","1","2","0","0","0","4","2020-10-27 04:56:58","2020-10-27 04:56:58");
INSERT INTO product_purchases VALUES("234","85","1","","","","1","1","1","320","0","10","32","352","2020-11-02 09:29:52","2020-11-02 09:29:52");
INSERT INTO product_purchases VALUES("239","87","1","","","","1","1","1","320","0","10","32","352","2020-11-02 11:20:34","2020-11-02 11:20:34");
INSERT INTO product_purchases VALUES("242","89","62","","12","","1","1","1","1","0","0","0","1","2020-11-16 15:01:45","2020-11-16 15:01:45");
INSERT INTO product_purchases VALUES("243","89","1","","","","1","1","1","320","0","10","32","352","2020-11-16 15:01:45","2020-11-16 15:01:45");
INSERT INTO product_purchases VALUES("244","90","61","","","","10","10","1","3000","0","15","4500","34500","2020-11-18 06:15:44","2020-11-18 06:15:44");
INSERT INTO product_purchases VALUES("271","98","71","1","","","10","10","1","1","0","0","0","10","2021-03-07 14:14:05","2021-03-07 14:14:05");
INSERT INTO product_purchases VALUES("281","100","71","1","","","10","10","1","1","0","0","0","10","2021-03-09 15:45:19","2021-03-09 15:45:19");
INSERT INTO product_purchases VALUES("282","99","71","2","","","5","5","1","1","0","0","0","5","2021-03-09 15:46:50","2021-03-09 15:46:50");
INSERT INTO product_purchases VALUES("283","102","71","1","","","5","5","1","1","0","0","0","5","2021-03-11 09:47:53","2021-03-11 09:47:53");
INSERT INTO product_purchases VALUES("295","105","71","1","","","1","1","1","1","0","0","0","1","2021-04-09 21:53:48","2021-04-09 21:53:48");
INSERT INTO product_purchases VALUES("296","105","5","","","","1","1","1","100","0","0","0","100","2021-04-09 21:53:48","2021-04-09 21:53:48");
INSERT INTO product_purchases VALUES("297","106","1","","","","1","1","1","320","0","10","32","352","2021-05-24 19:18:54","2021-05-24 19:18:54");
INSERT INTO product_purchases VALUES("309","113","3","","","","1","1","1","200","0","0","0","200","2021-05-30 13:41:19","2021-05-30 13:41:19");
INSERT INTO product_purchases VALUES("310","113","71","1","","","1","1","1","1","0","0","0","1","2021-05-30 13:41:19","2021-05-30 13:41:19");
INSERT INTO product_purchases VALUES("311","114","71","3","","","3","3","1","1.5","0","0","0","4.5","2021-06-01 15:21:27","2021-06-01 15:21:27");
INSERT INTO product_purchases VALUES("312","115","71","1","","","1","1","1","1","0","0","0","1","2021-06-18 20:28:36","2021-06-18 20:28:36");
INSERT INTO product_purchases VALUES("313","115","5","","","","1","1","1","100","0","0","0","100","2021-06-18 20:28:36","2021-06-18 20:28:36");
INSERT INTO product_purchases VALUES("314","116","71","1","","","1","1","1","1","0","0","0","1","2021-06-19 10:48:22","2021-06-19 10:48:22");
INSERT INTO product_purchases VALUES("315","116","5","","","","1","1","1","100","0","0","0","100","2021-06-19 10:48:22","2021-06-19 10:48:22");
INSERT INTO product_purchases VALUES("316","117","72","","","","5","5","1","2","0","0","0","10","2021-07-06 13:10:42","2021-07-06 13:10:42");
INSERT INTO product_purchases VALUES("317","118","72","","","","5","5","1","2","0","0","0","10","2021-07-06 13:11:07","2021-07-06 13:11:07");
INSERT INTO product_purchases VALUES("324","121","71","3","","","1","1","1","1","0","0","0","1","2021-09-21 15:49:38","2021-09-21 15:49:38");
INSERT INTO product_purchases VALUES("325","121","5","","","","1","1","1","100","0","0","0","100","2021-09-21 15:49:38","2021-09-21 15:49:38");
INSERT INTO product_purchases VALUES("328","123","5","","","","1","1","1","100","0","0","0","100","2021-10-03 13:04:53","2021-10-03 13:04:53");
INSERT INTO product_purchases VALUES("329","123","22","","","11101","1","1","1","800","0","10","80","880","2021-10-03 13:04:53","2021-10-03 13:04:53");
INSERT INTO product_purchases VALUES("330","124","48","","2","","1","1","1","2","0","0","0","2","2021-10-03 13:06:21","2021-10-03 13:06:21");
INSERT INTO product_purchases VALUES("331","124","22","","","11102","1","1","1","800","0","10","80","880","2021-10-03 13:06:21","2021-10-03 13:06:21");
INSERT INTO product_purchases VALUES("345","128","22","","","","10","5","1","800","0","10","800","8800","2021-10-20 13:45:25","2021-10-20 13:45:25");
INSERT INTO product_purchases VALUES("346","129","22","4","","","1","1","1","800","0","10","80","880","2021-10-20 13:47:09","2021-10-20 13:47:09");
INSERT INTO product_purchases VALUES("356","131","3","","","","3","3","1","200","0","0","0","600","2021-11-29 13:29:18","2021-11-29 13:29:18");
INSERT INTO product_purchases VALUES("357","131","1","","","","2","2","1","320","0","10","64","704","2021-11-29 13:29:18","2021-11-29 13:29:18");
INSERT INTO product_purchases VALUES("363","132","22","","","11103","1","1","1","800","0","10","80","880","2021-12-07 19:01:11","2021-12-07 19:01:11");
INSERT INTO product_purchases VALUES("364","132","48","","5","","1","1","1","2","0","0","0","2","2021-12-07 19:01:11","2021-12-07 19:01:11");
INSERT INTO product_purchases VALUES("365","132","5","","","","1","1","1","100","0","0","0","100","2021-12-07 19:01:11","2021-12-07 19:01:11");
INSERT INTO product_purchases VALUES("366","133","5","","","","2","2","1","100","0","0","0","200","2022-02-20 21:31:02","2022-02-20 21:31:02");
INSERT INTO product_purchases VALUES("368","134","1","","","","1","1","1","320","0","10","32","352","2022-03-11 22:01:52","2022-03-11 22:01:52");
INSERT INTO product_purchases VALUES("369","135","3","","","","1","1","1","200","0","0","0","200","2022-03-11 22:02:15","2022-03-11 22:02:15");
INSERT INTO product_purchases VALUES("370","136","2","","","","1","1","3","166.96","0","15","25.04","192","2022-03-27 11:07:27","2022-03-27 11:07:27");
INSERT INTO product_purchases VALUES("371","136","1","","","","1","1","1","320","0","10","32","352","2022-03-27 11:07:27","2022-03-27 11:07:27");
INSERT INTO product_purchases VALUES("385","144","2","","","","10","10","1","6.96","0","15","10.4","80","2022-03-31 14:05:49","2022-03-31 14:05:49");
INSERT INTO product_purchases VALUES("386","144","25","","","","10","10","1","500","0","10","500","5500","2022-03-31 14:05:49","2022-03-31 14:05:49");
INSERT INTO product_purchases VALUES("387","144","72","","","","10","10","1","2","0","0","0","20","2022-03-31 14:05:49","2022-03-31 14:05:49");
INSERT INTO product_purchases VALUES("388","145","72","","","","10","10","1","2","0","0","0","20","2022-04-24 11:00:38","2022-04-24 11:00:38");
INSERT INTO product_purchases VALUES("389","145","25","","","","1","1","1","500","0","10","50","550","2022-04-24 11:00:38","2022-04-24 11:00:38");
INSERT INTO product_purchases VALUES("390","146","25","","","","10","10","1","500","0","10","500","5500","2022-05-16 11:21:00","2022-05-16 11:21:00");
INSERT INTO product_purchases VALUES("391","147","25","","","","10","10","1","500","0","10","500","5500","2022-05-31 12:23:57","2022-05-31 12:23:57");
INSERT INTO product_purchases VALUES("392","147","1","","","","10","10","1","320","0","10","320","3520","2022-05-31 12:23:57","2022-05-31 12:23:57");
INSERT INTO product_purchases VALUES("395","148","79","","","","12","12","7","12","0","0","0","144","2022-06-05 12:26:35","2022-06-05 12:26:35");
INSERT INTO product_purchases VALUES("396","150","2","","","","1","1","3","347.83","0","15","52.17","400","2022-06-07 16:38:46","2022-06-07 16:38:46");
INSERT INTO product_purchases VALUES("400","153","1","","","","1","1","1","320","0","10","32","352","2022-07-17 10:52:12","2022-07-17 10:52:12");
INSERT INTO product_purchases VALUES("401","153","5","","","","1","1","1","100","0","0","0","100","2022-07-17 10:52:12","2022-07-17 10:52:12");
INSERT INTO product_purchases VALUES("402","154","1","","","","1","1","1","320","0","10","32","352","2022-07-17 10:52:57","2022-07-17 10:52:57");
INSERT INTO product_purchases VALUES("403","154","5","","","","2","2","1","100","0","0","0","200","2022-07-17 10:52:57","2022-07-17 10:52:57");
INSERT INTO product_purchases VALUES("404","155","5","","","","1","1","1","100","0","0","0","100","2022-07-17 10:53:44","2022-07-17 10:53:44");
INSERT INTO product_purchases VALUES("416","158","1","","","","1","1","1","320","0","10","32","352","2022-08-08 21:13:57","2022-08-08 21:13:57");
INSERT INTO product_purchases VALUES("417","158","48","","5","","1","1","1","12","0","0","0","12","2022-08-08 21:13:57","2022-08-08 21:13:57");
INSERT INTO product_purchases VALUES("418","158","87","","30","","1","1","1","13","0","0","0","13","2022-08-08 21:13:57","2022-08-08 21:13:57");
INSERT INTO product_purchases VALUES("419","158","87","","22","","1","1","1","12","0","0","0","12","2022-08-08 21:13:57","2022-08-08 21:13:57");
INSERT INTO product_purchases VALUES("420","158","48","","2","","1","1","1","7","0","0","0","7","2022-08-08 21:13:57","2022-08-08 21:13:57");
INSERT INTO product_purchases VALUES("425","161","25","","","","1","1","1","500","0","10","50","550","2022-09-04 20:01:09","2022-09-04 20:01:09");
INSERT INTO product_purchases VALUES("426","161","3","","","","1","1","1","200","0","0","0","200","2022-09-04 20:01:10","2022-09-04 20:01:10");
INSERT INTO product_purchases VALUES("427","161","1","","","","1","1","1","320","0","10","32","352","2022-09-04 20:01:10","2022-09-04 20:01:10");
INSERT INTO product_purchases VALUES("428","162","90","","35","","10","10","1","550","0","10","550","6050","2022-09-11 13:04:41","2022-09-11 13:04:41");
INSERT INTO product_purchases VALUES("429","162","90","","34","","10","10","1","500","0","10","500","5500","2022-09-11 13:04:41","2022-09-11 13:04:41");
INSERT INTO product_purchases VALUES("430","162","90","","33","","10","10","1","500","0","10","500","5500","2022-09-11 13:04:41","2022-09-11 13:04:41");
INSERT INTO product_purchases VALUES("431","163","1","","","","1","1","1","290.91","0","10","29.09","320","2022-11-20 12:20:35","2022-11-20 12:20:35");
INSERT INTO product_purchases VALUES("432","164","3","","","","1","1","1","190","0","0","0","190","2022-11-24 16:37:10","2022-11-24 16:37:10");
INSERT INTO product_purchases VALUES("433","165","3","","","","1","1","1","200","0","0","0","200","2022-12-11 14:54:54","2022-12-11 14:54:54");
INSERT INTO product_purchases VALUES("439","171","1","","","","1","1","1","261.82","0","10","26.18","288","2023-03-27 12:10:54","2023-03-27 12:10:54");
INSERT INTO product_purchases VALUES("441","172","5","","","","2","2","1","90","0","0","0","180","2023-03-27 12:21:35","2023-03-27 12:21:35");
INSERT INTO product_purchases VALUES("442","172","1","","","","2","2","1","261.82","0","10","52.36","576","2023-03-27 12:21:35","2023-03-27 12:21:35");
INSERT INTO product_purchases VALUES("443","173","3","","","","2","2","1","200","0","0","0","400","2023-04-05 13:40:44","2023-04-05 13:40:44");
INSERT INTO product_purchases VALUES("444","174","1","","","","2","2","1","286.36","10","10","57.27","630","2023-04-29 16:53:30","2023-04-29 16:53:30");
INSERT INTO product_purchases VALUES("445","174","2","","","","1","1","3","192","0","0","0","192","2023-04-29 16:53:30","2023-04-29 16:53:30");
INSERT INTO product_purchases VALUES("452","178","1","","","","1","1","1","290.91","0","10","29.09","320","2023-06-04 15:50:51","2023-06-04 15:50:51");
INSERT INTO product_purchases VALUES("453","178","3","","","","1","1","1","200","0","0","0","200","2023-06-04 15:50:51","2023-06-04 15:50:51");
INSERT INTO product_purchases VALUES("454","179","48","","3","","1","1","1","2","0","0","0","2","2023-07-09 10:34:45","2023-07-09 10:34:45");
INSERT INTO product_purchases VALUES("455","179","10","","","","1","1","7","10","0","0","0","10","2023-07-09 10:34:45","2023-07-09 10:34:45");
INSERT INTO product_purchases VALUES("456","179","5","","","","1","1","1","100","0","0","0","100","2023-07-09 10:34:45","2023-07-09 10:34:45");



CREATE TABLE `product_quotation` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_batch_id` int DEFAULT NULL,
  `variant_id` int DEFAULT NULL,
  `qty` double NOT NULL,
  `sale_unit_id` int NOT NULL,
  `net_unit_price` double NOT NULL,
  `discount` double NOT NULL,
  `tax_rate` double NOT NULL,
  `tax` double NOT NULL,
  `total` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO product_quotation VALUES("2","1","1","","","1","2","5030","10","15","754.5","5784.5","2018-08-09 05:52:50","2018-08-28 06:09:57");
INSERT INTO product_quotation VALUES("3","1","4","","","50","1","1.5","25","10","7.5","82.5","2018-08-09 05:53:25","2018-08-28 06:34:36");
INSERT INTO product_quotation VALUES("4","1","2","","","6","1","9.55","0","10","5.73","63","2018-08-28 07:03:48","2018-08-28 07:07:07");
INSERT INTO product_quotation VALUES("5","2","10","","","2.5","7","22","0","0","0","55","2018-09-04 04:02:58","2018-09-20 16:37:41");
INSERT INTO product_quotation VALUES("6","2","13","","","1","0","21","0","0","0","21","2018-09-04 04:02:58","2018-09-04 04:02:58");
INSERT INTO product_quotation VALUES("7","3","1","","","1","1","400","0","10","40","440","2018-10-23 06:12:49","2019-12-21 12:41:37");
INSERT INTO product_quotation VALUES("19","3","48","","2","1","1","13","0","0","0","13","2019-12-07 14:50:02","2019-12-21 12:41:37");
INSERT INTO product_quotation VALUES("27","11","61","","","2","1","10000","0","15","3000","23000","2020-10-24 09:08:14","2020-10-24 09:28:35");
INSERT INTO product_quotation VALUES("28","12","3","","","10","1","250","0","0","0","2500","2020-12-10 05:34:01","2020-12-10 05:34:01");
INSERT INTO product_quotation VALUES("29","12","22","","","5","1","1200","0","10","600","6600","2020-12-10 05:34:01","2020-12-10 05:34:01");
INSERT INTO product_quotation VALUES("30","13","2","","","10","1","10.43","0","15","15.65","120","2021-01-12 12:58:03","2021-01-12 12:59:15");
INSERT INTO product_quotation VALUES("32","13","1","","","1","1","400","0","10","40","440","2021-01-12 12:58:03","2021-01-12 12:59:15");
INSERT INTO product_quotation VALUES("33","13","22","","","1","1","1000","0","10","100","1100","2021-01-12 12:59:15","2021-01-12 12:59:15");
INSERT INTO product_quotation VALUES("37","15","71","1","","1","1","2","0","0","0","2","2021-05-29 10:53:56","2021-05-30 14:04:07");
INSERT INTO product_quotation VALUES("38","15","1","","","1","1","400","0","10","40","440","2021-05-29 10:53:56","2021-05-30 14:04:07");
INSERT INTO product_quotation VALUES("39","16","5","","","1","1","120","0","0","0","120","2022-02-19 12:20:55","2022-02-19 12:20:55");
INSERT INTO product_quotation VALUES("40","17","1","","","1","1","400","0","10","40","440","2022-02-19 12:41:03","2022-02-19 12:41:03");
INSERT INTO product_quotation VALUES("41","18","1","","","1","1","400","0","10","40","440","2022-02-19 12:46:38","2022-02-19 12:46:38");
INSERT INTO product_quotation VALUES("42","19","75","","","1","0","197","0","0","0","197","2022-03-08 16:05:21","2022-03-13 11:38:58");
INSERT INTO product_quotation VALUES("43","19","5","","","1","1","121","0","0","0","121","2022-03-08 16:05:21","2022-03-13 11:38:58");
INSERT INTO product_quotation VALUES("44","19","3","","","1","1","248","0","0","0","248","2022-03-08 16:06:04","2022-03-13 11:38:58");
INSERT INTO product_quotation VALUES("49","22","5","","","1","1","108","0","0","0","108","2023-07-09 10:36:15","2023-07-09 10:36:15");



CREATE TABLE `product_returns` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `return_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_batch_id` int DEFAULT NULL,
  `variant_id` int DEFAULT NULL,
  `imei_number` text COLLATE utf8mb4_unicode_ci,
  `qty` double NOT NULL,
  `sale_unit_id` int NOT NULL,
  `net_unit_price` double NOT NULL,
  `discount` double NOT NULL,
  `tax_rate` double NOT NULL,
  `tax` double NOT NULL,
  `total` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO product_returns VALUES("3","2","4","","","","20","1","2","0","0","0","40","","");
INSERT INTO product_returns VALUES("4","3","10","","","","2","7","22","0","0","0","44","","2018-10-07 08:19:40");
INSERT INTO product_returns VALUES("6","5","3","","","","1","1","250","0","0","0","250","","2018-12-26 04:16:08");
INSERT INTO product_returns VALUES("12","6","1","","","","1","1","400","0","10","40","440","","");
INSERT INTO product_returns VALUES("23","11","13","","","","1","0","21","0","0","0","21","2019-12-24 11:20:29","2019-12-24 11:20:29");
INSERT INTO product_returns VALUES("26","13","61","","","","1","1","10000","0","15","1500","11500","2020-08-16 22:25:02","2020-08-16 22:25:02");
INSERT INTO product_returns VALUES("27","14","1","","","","1","1","400","0","10","40","440","2020-10-13 17:39:54","2020-10-13 17:39:54");
INSERT INTO product_returns VALUES("31","18","61","","","","1","1","10000","0","15","1500","11500","2020-11-18 07:02:18","2020-11-18 07:02:18");
INSERT INTO product_returns VALUES("32","19","3","","","","1","1","250","0","0","0","250","2020-12-10 06:40:25","2020-12-10 06:40:25");
INSERT INTO product_returns VALUES("58","33","25","","","","1","1","1000","0","10","100","1100","2022-05-31 12:33:40","2022-05-31 12:33:40");
INSERT INTO product_returns VALUES("59","34","1","","","","1","1","400","0","10","40","440","2022-05-31 12:48:17","2022-05-31 12:48:17");
INSERT INTO product_returns VALUES("61","36","22","","","","1","1","1000","0","10","100","1100","2022-06-14 15:20:48","2022-06-14 15:20:48");
INSERT INTO product_returns VALUES("63","38","3","","","","1","1","250","0","0","0","250","2022-09-04 13:19:08","2022-09-04 13:19:08");
INSERT INTO product_returns VALUES("64","39","5","","","","1","1","108","0","0","0","108","2023-03-23 12:50:50","2023-07-09 12:37:20");



CREATE TABLE `product_sales` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_batch_id` int DEFAULT NULL,
  `variant_id` int DEFAULT NULL,
  `imei_number` text COLLATE utf8mb4_unicode_ci,
  `qty` double NOT NULL,
  `sale_unit_id` int NOT NULL,
  `net_unit_price` double NOT NULL,
  `discount` double NOT NULL,
  `tax_rate` double NOT NULL,
  `tax` double NOT NULL,
  `total` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=760 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO product_sales VALUES("1","1","2","","","","1","2","104.35","0","15","15.65","120","2018-08-08 16:36:23","2018-08-08 17:13:27");
INSERT INTO product_sales VALUES("3","1","5","","","","2","1","115","10","0","0","230","2018-08-08 17:13:28","2018-08-08 17:13:28");
INSERT INTO product_sales VALUES("4","2","1","","","","10","1","420","0","10","420","4620","2018-08-09 05:54:53","2018-08-09 05:54:53");
INSERT INTO product_sales VALUES("5","2","4","","","","50","1","2.1","0","0","0","105","2018-08-09 05:54:53","2018-08-09 05:54:53");
INSERT INTO product_sales VALUES("6","2","2","","","","3","2","109.57","0","15","49.3","378","2018-08-09 05:54:53","2018-08-09 05:54:53");
INSERT INTO product_sales VALUES("7","3","4","","","","20","1","2.1","0","0","0","42","2018-08-09 06:32:15","2018-08-09 06:32:15");
INSERT INTO product_sales VALUES("8","3","5","","","","5","1","126","0","0","0","630","2018-08-09 06:32:15","2018-08-09 06:32:15");
INSERT INTO product_sales VALUES("9","3","3","","","","1","1","225","0","0","0","225","2018-08-09 06:32:15","2018-08-09 06:32:15");
INSERT INTO product_sales VALUES("10","4","1","","","","2","1","400","0","10","80","880","2018-08-25 03:48:37","2018-08-25 03:48:37");
INSERT INTO product_sales VALUES("12","6","13","","","","1","0","18.9","0","0","0","18.9","2018-08-26 09:48:36","2018-08-26 11:48:05");
INSERT INTO product_sales VALUES("13","7","1","","","","2","1","400","0","10","80","880","2018-08-27 07:35:45","2018-08-27 07:35:45");
INSERT INTO product_sales VALUES("14","8","5","","","","2","2","1440","0","10","288","3168","2018-09-02 05:39:54","2018-09-02 05:39:54");
INSERT INTO product_sales VALUES("15","9","4","","","","10","1","2","0","0","0","20","2018-09-03 03:33:14","2018-09-03 03:33:14");
INSERT INTO product_sales VALUES("16","10","3","","","","1","1","250","0","0","0","250","2018-09-03 05:01:39","2018-09-03 05:01:39");
INSERT INTO product_sales VALUES("17","11","22","","","","5","1","1000","0","10","500","5500","2018-09-03 10:08:21","2018-09-03 10:08:21");
INSERT INTO product_sales VALUES("18","12","22","","","","10","1","1050","0","10","1050","11550","2018-09-03 10:10:33","2018-09-03 10:10:33");
INSERT INTO product_sales VALUES("46","29","5","","","","1","1","120","0","0","0","120","2018-09-09 09:38:41","2018-09-09 09:38:41");
INSERT INTO product_sales VALUES("47","30","1","","","","1","1","400","0","10","40","440","2018-09-10 04:57:06","2018-09-20 15:12:38");
INSERT INTO product_sales VALUES("48","31","1","","","","1","1","400","0","10","40","440","2018-09-26 09:20:59","2018-09-26 09:21:25");
INSERT INTO product_sales VALUES("49","31","2","","","","1","2","104.35","0","15","15.65","120","2018-09-26 09:20:59","2018-09-26 09:21:25");
INSERT INTO product_sales VALUES("50","32","1","","","","1","1","400","0","10","40","440","2018-10-04 09:55:48","2018-10-04 09:55:48");
INSERT INTO product_sales VALUES("51","33","1","","","","1","1","400","0","10","40","440","2018-10-04 10:00:23","2018-10-04 10:00:23");
INSERT INTO product_sales VALUES("57","37","3","","","","1","1","250","0","0","0","250","2018-10-07 06:46:05","2018-10-07 06:46:05");
INSERT INTO product_sales VALUES("58","38","1","","","","1","1","400","0","10","40","440","2018-10-07 06:47:19","2018-10-07 06:47:19");
INSERT INTO product_sales VALUES("61","40","1","","","","1","1","400","0","10","40","440","2018-10-07 07:13:12","2018-10-07 07:13:12");
INSERT INTO product_sales VALUES("62","41","1","","","","1","1","400","0","10","40","440","2018-10-10 04:16:21","2018-10-10 04:16:21");
INSERT INTO product_sales VALUES("63","41","13","","","","1","0","21","0","0","0","21","2018-10-10 04:16:21","2018-10-10 04:16:21");
INSERT INTO product_sales VALUES("64","42","1","","","","1","1","400","0","10","40","440","2018-10-10 05:34:51","2018-10-10 05:34:51");
INSERT INTO product_sales VALUES("65","43","1","","","","1","1","400","0","10","40","440","2018-10-16 03:34:35","2018-10-16 03:34:35");
INSERT INTO product_sales VALUES("78","55","3","","","","1","1","250","0","0","0","250","2018-10-21 06:53:34","2018-10-21 06:53:34");
INSERT INTO product_sales VALUES("80","57","1","","","","1","1","400","0","10","40","440","2018-10-21 08:26:12","2018-10-21 08:26:12");
INSERT INTO product_sales VALUES("81","57","13","","","","2","0","21","0","0","0","42","2018-10-21 08:26:13","2018-10-21 08:26:13");
INSERT INTO product_sales VALUES("82","58","22","","","","1","1","1000","0","10","100","1100","2018-10-22 15:27:24","2018-10-22 15:27:24");
INSERT INTO product_sales VALUES("83","58","5","","","","1","1","120","0","0","0","120","2018-10-22 15:27:24","2018-10-22 15:27:24");
INSERT INTO product_sales VALUES("101","73","25","","","","3","1","1000","0","10","300","3300","2018-10-23 07:15:43","2018-10-23 07:15:43");
INSERT INTO product_sales VALUES("102","73","22","","","","2","1","1000","0","10","200","2200","2018-10-23 07:15:44","2018-10-23 07:15:44");
INSERT INTO product_sales VALUES("103","74","22","","","","1","1","1000","0","10","100","1100","2018-10-23 07:16:44","2018-10-23 07:16:44");
INSERT INTO product_sales VALUES("104","74","25","","","","1","1","1000","0","10","100","1100","2018-10-23 07:16:44","2018-10-23 07:16:44");
INSERT INTO product_sales VALUES("105","74","5","","","","1","1","120","0","0","0","120","2018-10-23 07:16:44","2018-10-23 07:16:44");
INSERT INTO product_sales VALUES("106","75","2","","","","3","2","104.35","0","15","46.96","360","2018-11-01 05:00:27","2018-11-01 05:00:27");
INSERT INTO product_sales VALUES("107","75","22","","","","2","1","1000","0","10","200","2200","2018-11-01 05:00:27","2018-11-01 05:00:27");
INSERT INTO product_sales VALUES("108","75","25","","","","3","1","1000","0","10","300","3300","2018-11-01 05:00:27","2018-11-01 05:00:27");
INSERT INTO product_sales VALUES("109","75","1","","","","2","1","400","0","10","80","880","2018-11-01 05:00:27","2018-11-01 05:00:27");
INSERT INTO product_sales VALUES("110","75","5","","","","2","1","120","0","0","0","240","2018-11-01 05:00:27","2018-11-01 05:00:27");
INSERT INTO product_sales VALUES("111","76","3","","","","1","1","250","0","0","0","250","2018-11-01 05:01:26","2018-11-03 09:28:56");
INSERT INTO product_sales VALUES("112","76","22","","","","1","1","1000","0","10","100","1100","2018-11-01 05:01:27","2018-11-03 09:28:56");
INSERT INTO product_sales VALUES("113","76","13","","","","4","0","21","0","0","0","84","2018-11-01 05:01:27","2018-11-03 09:28:56");
INSERT INTO product_sales VALUES("117","79","1","","","","4","1","400","0","10","160","1760","2018-11-05 09:15:17","2018-11-05 09:15:17");
INSERT INTO product_sales VALUES("118","79","2","","","","7","2","104.35","0","15","109.57","840","2018-11-05 09:15:17","2018-11-05 09:15:17");
INSERT INTO product_sales VALUES("119","79","3","","","","7","1","250","0","0","0","1750","2018-11-05 09:15:17","2018-11-05 09:15:17");
INSERT INTO product_sales VALUES("120","79","4","","","","7","1","2","0","0","0","14","2018-11-05 09:15:17","2018-11-05 09:15:17");
INSERT INTO product_sales VALUES("121","79","22","","","","8","1","1000","0","10","800","8800","2018-11-05 09:15:17","2018-11-05 09:15:17");
INSERT INTO product_sales VALUES("122","79","13","","","","10","0","21","0","0","0","210","2018-11-05 09:15:17","2018-11-05 09:15:17");
INSERT INTO product_sales VALUES("123","79","5","","","","9","1","120","0","0","0","1080","2018-11-05 09:15:17","2018-11-05 09:15:17");
INSERT INTO product_sales VALUES("124","80","2","","","","2","2","104.35","0","15","31.3","240","2018-11-05 09:19:58","2018-11-05 09:19:58");
INSERT INTO product_sales VALUES("125","80","3","","","","2","1","250","0","0","0","500","2018-11-05 09:19:58","2018-11-05 09:19:58");
INSERT INTO product_sales VALUES("126","80","1","","","","4","1","400","0","10","160","1760","2018-11-05 09:19:58","2018-11-05 09:19:58");
INSERT INTO product_sales VALUES("132","86","22","","","","1","1","1000","0","10","100","1100","2018-11-05 09:59:48","2018-11-05 09:59:48");
INSERT INTO product_sales VALUES("134","88","22","","","","1","1","1000","0","10","100","1100","2018-11-05 10:02:58","2018-11-05 10:02:58");
INSERT INTO product_sales VALUES("142","94","2","","","","1","2","104.35","0","15","15.65","120","2018-11-26 14:05:34","2018-11-26 14:05:34");
INSERT INTO product_sales VALUES("143","95","5","","","","3","1","120","0","0","0","360","2018-11-27 09:36:08","2018-11-27 09:36:08");
INSERT INTO product_sales VALUES("144","95","5","","","","3","1","120","0","0","0","360","2018-11-27 09:36:08","2018-11-27 09:36:08");
INSERT INTO product_sales VALUES("145","96","2","","","","1","2","104.35","0","15","15.65","120","2018-11-28 07:15:09","2018-11-28 07:15:09");
INSERT INTO product_sales VALUES("146","97","2","","","","2","2","104.35","0","15","31.3","240","2018-12-01 06:05:18","2018-12-01 06:05:18");
INSERT INTO product_sales VALUES("147","97","10","","","","1","7","22","0","0","0","22","2018-12-01 06:05:18","2018-12-01 06:05:18");
INSERT INTO product_sales VALUES("148","98","30","","","","2","1","100","0","0","0","200","2018-12-05 05:35:58","2018-12-05 05:35:58");
INSERT INTO product_sales VALUES("149","98","31","","","","2","1","300","0","0","0","600","2018-12-05 05:35:58","2018-12-05 05:35:58");
INSERT INTO product_sales VALUES("150","99","30","","","","2","1","100","0","0","0","200","2018-12-05 05:37:19","2018-12-05 05:37:19");
INSERT INTO product_sales VALUES("151","99","31","","","","2","1","300","0","0","0","600","2018-12-05 05:37:20","2018-12-05 05:37:20");
INSERT INTO product_sales VALUES("153","101","30","","","","1","1","100","0","0","0","100","2018-12-08 06:20:26","2018-12-08 06:20:26");
INSERT INTO product_sales VALUES("155","103","2","","","","1","2","104.35","0","15","15.65","120","2018-12-24 04:58:32","2018-12-24 04:58:32");
INSERT INTO product_sales VALUES("156","104","33","","","","4","1","2","0","0","0","8","2018-12-24 09:15:27","2018-12-25 03:55:23");
INSERT INTO product_sales VALUES("157","104","26","","","","2","0","1250","0","0","0","2500","2018-12-25 03:47:54","2018-12-25 03:55:23");
INSERT INTO product_sales VALUES("158","105","13","","","","1","0","21","0","0","0","21","2019-01-01 05:45:38","2019-01-01 05:45:38");
INSERT INTO product_sales VALUES("159","106","2","","","","1","2","104.35","0","15","15.65","120","2019-01-01 09:10:40","2019-01-01 09:10:40");
INSERT INTO product_sales VALUES("160","106","3","","","","2","1","250","0","0","0","500","2019-01-01 09:10:40","2019-01-01 09:10:40");
INSERT INTO product_sales VALUES("161","106","5","","","","2","1","120","0","0","0","240","2019-01-01 09:10:40","2019-01-01 09:10:40");
INSERT INTO product_sales VALUES("162","107","3","","","","2","1","250","0","0","0","500","2019-01-03 06:56:27","2019-01-03 06:56:27");
INSERT INTO product_sales VALUES("163","107","5","","","","2","1","120","0","0","0","240","2019-01-03 06:56:27","2019-01-03 06:56:27");
INSERT INTO product_sales VALUES("164","107","22","","","","2","1","1000","0","10","200","2200","2019-01-03 06:56:27","2019-01-03 06:56:27");
INSERT INTO product_sales VALUES("165","107","25","","","","1","1","1000","0","10","100","1100","2019-01-03 06:56:27","2019-01-03 06:56:27");
INSERT INTO product_sales VALUES("166","107","1","","","","2","1","400","0","10","80","880","2019-01-03 06:56:27","2019-01-03 06:56:27");
INSERT INTO product_sales VALUES("167","107","2","","","","1","2","104.35","0","15","15.65","120","2019-01-03 06:56:27","2019-01-03 06:56:27");
INSERT INTO product_sales VALUES("168","108","2","","","","1","2","104.35","0","15","15.65","120","2019-01-20 15:58:24","2019-01-20 15:58:24");
INSERT INTO product_sales VALUES("169","109","1","","","","1","1","400","0","10","40","440","2019-01-29 10:14:43","2019-01-29 10:14:43");
INSERT INTO product_sales VALUES("170","109","30","","","","1","1","100","0","0","0","100","2019-01-29 10:14:43","2019-01-29 10:14:43");
INSERT INTO product_sales VALUES("171","110","31","","","","2","1","300","0","0","0","600","2019-01-29 11:50:41","2019-01-29 11:50:41");
INSERT INTO product_sales VALUES("172","110","25","","","","1","1","1000","0","10","100","1100","2019-01-29 11:50:41","2019-01-29 11:50:41");
INSERT INTO product_sales VALUES("173","111","3","","","","1","1","250","0","0","0","250","2019-01-31 11:08:39","2019-01-31 11:08:39");
INSERT INTO product_sales VALUES("174","111","13","","","","1","0","21","0","0","0","21","2019-01-31 11:08:39","2019-01-31 11:08:39");
INSERT INTO product_sales VALUES("175","112","1","","","","1","1","400","0","10","40","440","2019-02-02 10:40:45","2019-02-02 10:40:45");
INSERT INTO product_sales VALUES("176","113","3","","","","1","1","250","0","0","0","250","2019-02-02 11:41:17","2019-02-02 11:41:17");
INSERT INTO product_sales VALUES("177","113","30","","","","1","1","100","0","0","0","100","2019-02-02 11:41:17","2019-02-02 11:41:17");
INSERT INTO product_sales VALUES("178","114","1","","","","1","1","400","0","10","40","440","2019-02-05 15:04:45","2019-02-05 15:04:45");
INSERT INTO product_sales VALUES("183","118","1","","","","1","1","400","0","10","40","440","2019-02-07 11:15:42","2019-02-07 11:15:42");
INSERT INTO product_sales VALUES("185","120","1","","","","1","1","400","0","10","40","440","2019-02-07 11:40:37","2019-02-07 11:40:37");
INSERT INTO product_sales VALUES("186","121","3","","","","1","1","250","0","0","0","250","2019-02-09 10:48:14","2019-02-09 10:48:14");
INSERT INTO product_sales VALUES("187","121","2","","","","1","2","104.35","0","15","15.65","120","2019-02-09 10:48:14","2019-02-09 10:48:14");
INSERT INTO product_sales VALUES("188","121","4","","","","10","1","2","0","0","0","20","2019-02-09 10:48:15","2019-02-09 10:48:15");
INSERT INTO product_sales VALUES("189","121","13","","","","2","0","21","0","0","0","42","2019-02-09 10:48:15","2019-02-09 10:48:15");
INSERT INTO product_sales VALUES("190","121","1","","","","1","1","400","0","10","40","440","2019-02-09 10:48:15","2019-02-09 10:48:15");
INSERT INTO product_sales VALUES("191","121","31","","","","1","1","300","0","0","0","300","2019-02-09 10:48:15","2019-02-09 10:48:15");
INSERT INTO product_sales VALUES("192","121","30","","","","1","1","100","0","0","0","100","2019-02-09 10:48:15","2019-02-09 10:48:15");
INSERT INTO product_sales VALUES("194","123","1","","","","1","1","400","0","10","40","440","2019-02-19 14:32:14","2019-02-19 14:32:14");
INSERT INTO product_sales VALUES("198","127","31","","","","1","1","300","0","0","0","300","2019-03-03 10:40:10","2019-03-03 10:40:10");
INSERT INTO product_sales VALUES("199","127","25","","","","1","1","1000","0","10","100","1100","2019-03-03 10:40:10","2019-03-03 10:40:10");
INSERT INTO product_sales VALUES("200","127","22","","","","1","1","1000","0","10","100","1100","2019-03-03 10:40:10","2019-03-03 10:40:10");
INSERT INTO product_sales VALUES("201","128","1","","","","1","1","400","0","10","40","440","2019-04-04 09:55:55","2019-04-04 09:55:55");
INSERT INTO product_sales VALUES("202","128","2","","","","1","2","104.35","0","15","15.65","120","2019-04-04 09:55:55","2019-04-04 09:55:55");
INSERT INTO product_sales VALUES("203","129","5","","","","2","1","120","0","0","0","240","2019-04-04 09:59:37","2019-04-11 10:50:16");
INSERT INTO product_sales VALUES("204","130","1","","","","1","1","400","0","10","40","440","2019-04-21 12:21:24","2019-04-21 12:21:24");
INSERT INTO product_sales VALUES("205","130","2","","","","1","2","125.22","0","15","18.78","144","2019-04-21 12:21:24","2019-04-21 12:21:24");
INSERT INTO product_sales VALUES("206","130","4","","","","1","1","2","0","0","0","2","2019-04-21 12:21:24","2019-04-21 12:21:24");
INSERT INTO product_sales VALUES("207","131","1","","","","1","1","400","0","10","40","440","2019-05-28 10:32:29","2019-05-28 10:32:29");
INSERT INTO product_sales VALUES("208","131","3","","","","1","1","250","0","0","0","250","2019-05-28 10:32:29","2019-05-28 10:32:29");
INSERT INTO product_sales VALUES("209","131","25","","","","1","1","1000","0","10","100","1100","2019-05-28 10:32:29","2019-05-28 10:32:29");
INSERT INTO product_sales VALUES("210","131","22","","","","1","1","1000","0","10","100","1100","2019-05-28 10:32:29","2019-05-28 10:32:29");
INSERT INTO product_sales VALUES("211","132","1","","","","1","1","400","0","10","40","440","2019-06-13 10:16:37","2019-06-13 10:16:37");
INSERT INTO product_sales VALUES("212","132","30","","","","1","1","100","0","0","0","100","2019-06-13 10:16:37","2019-06-13 10:16:37");
INSERT INTO product_sales VALUES("213","132","31","","","","1","1","300","0","0","0","300","2019-06-13 10:16:37","2019-06-13 10:16:37");
INSERT INTO product_sales VALUES("214","133","3","","","","2","1","250","0","0","0","500","2019-06-13 10:17:51","2019-06-13 10:17:51");
INSERT INTO product_sales VALUES("215","133","25","","","","1","1","1000","0","10","100","1100","2019-06-13 10:17:52","2019-06-13 10:17:52");
INSERT INTO product_sales VALUES("216","133","22","","","","1","1","1000","0","10","100","1100","2019-06-13 10:17:52","2019-06-13 10:17:52");
INSERT INTO product_sales VALUES("217","134","1","","","","1","1","400","0","10","40","440","2019-10-19 15:30:28","2019-10-19 15:30:28");
INSERT INTO product_sales VALUES("218","134","22","","","","1","1","1000","0","10","100","1100","2019-10-19 15:30:28","2019-10-19 15:30:28");
INSERT INTO product_sales VALUES("219","134","25","","","","1","1","1000","0","10","100","1100","2019-10-19 15:30:28","2019-10-19 15:30:28");
INSERT INTO product_sales VALUES("220","134","31","","","","1","1","300","0","0","0","300","2019-10-19 15:30:28","2019-10-19 15:30:28");
INSERT INTO product_sales VALUES("224","138","5","","","","1","1","120","0","0","0","120","2019-10-31 12:29:37","2019-10-31 12:29:37");
INSERT INTO product_sales VALUES("225","139","2","","","","2","2","125.22","0","15","37.57","288","2019-11-03 11:40:44","2019-11-03 11:40:44");
INSERT INTO product_sales VALUES("226","139","4","","","","100","1","2","0","0","0","200","2019-11-03 11:40:44","2019-11-03 11:40:44");
INSERT INTO product_sales VALUES("236","144","22","","","","1","1","1000","0","10","100","1100","2019-11-09 19:41:31","2019-11-09 19:41:31");
INSERT INTO product_sales VALUES("237","144","5","","","","1","1","120","0","0","0","120","2019-11-09 19:41:31","2019-11-09 19:41:31");
INSERT INTO product_sales VALUES("241","147","22","","","","1","1","1000","0","10","100","1100","2019-11-11 10:40:08","2019-11-11 10:40:08");
INSERT INTO product_sales VALUES("242","147","25","","","","1","1","1000","0","10","100","1100","2019-11-11 10:40:08","2019-11-11 10:40:08");
INSERT INTO product_sales VALUES("243","147","4","","","","10","1","2","0","0","0","20","2019-11-11 10:40:08","2019-11-11 10:40:08");
INSERT INTO product_sales VALUES("282","172","1","","","","1","1","400","0","10","40","440","2019-12-03 15:46:31","2019-12-03 15:46:31");
INSERT INTO product_sales VALUES("283","173","3","","","","1","1","225","0","0","0","225","2019-12-04 23:19:40","2019-12-04 23:19:40");
INSERT INTO product_sales VALUES("284","173","1","","","","1","1","360","0","10","36","396","2019-12-04 23:19:40","2019-12-04 23:19:40");
INSERT INTO product_sales VALUES("306","187","2","","","","2","2","125.22","0","15","37.57","288","2019-12-22 10:40:58","2019-12-22 10:40:58");
INSERT INTO product_sales VALUES("308","190","22","","","","1","1","1000","0","10","100","1100","2019-12-23 12:59:46","2019-12-23 12:59:46");
INSERT INTO product_sales VALUES("312","193","25","","","","1","1","1000","0","10","100","1100","2020-01-01 14:20:28","2020-01-01 14:20:28");
INSERT INTO product_sales VALUES("313","194","1","","","","2","1","400","0","10","80","880","2020-01-02 16:39:47","2020-01-02 16:39:47");
INSERT INTO product_sales VALUES("314","194","2","","","","1","1","10.43","0","15","1.57","12","2020-01-02 16:39:47","2020-01-02 16:39:47");
INSERT INTO product_sales VALUES("323","201","5","","","","1","1","120","0","0","0","120","2020-02-03 15:52:56","2020-02-03 15:52:56");
INSERT INTO product_sales VALUES("324","202","25","","","","1","1","1000","0","10","100","1100","2020-02-04 22:58:53","2020-02-04 22:58:53");
INSERT INTO product_sales VALUES("325","202","31","","","","1","1","300","0","0","0","300","2020-02-04 22:58:53","2020-02-04 22:58:53");
INSERT INTO product_sales VALUES("326","203","3","","","","1","1","250","0","0","0","250","2020-03-02 11:54:14","2020-03-02 11:54:14");
INSERT INTO product_sales VALUES("327","203","30","","","","1","1","100","0","0","0","100","2020-03-02 11:54:14","2020-03-02 11:54:14");
INSERT INTO product_sales VALUES("328","204","4","","","","20","1","2","0","0","0","40","2020-03-02 11:57:41","2020-03-02 11:57:41");
INSERT INTO product_sales VALUES("329","205","1","","","","1","1","400","0","10","40","440","2020-03-11 16:46:42","2020-03-11 16:46:42");
INSERT INTO product_sales VALUES("330","206","5","","","","1","1","120","0","0","0","120","2020-03-11 16:52:30","2020-03-11 16:54:04");
INSERT INTO product_sales VALUES("331","207","30","","","","2","1","100","0","0","0","200","2020-04-06 19:40:24","2020-04-06 19:41:11");
INSERT INTO product_sales VALUES("332","207","31","","","","1","1","300","0","0","0","300","2020-04-06 19:40:24","2020-04-06 19:41:11");
INSERT INTO product_sales VALUES("333","207","2","","","","1","2","125.22","0","15","18.78","144","2020-04-06 19:41:11","2020-04-06 19:41:11");
INSERT INTO product_sales VALUES("334","208","25","","","","1","1","1000","0","10","100","1100","2020-05-06 22:59:50","2020-05-06 22:59:50");
INSERT INTO product_sales VALUES("335","208","1","","","","1","1","400","0","10","40","440","2020-05-06 22:59:50","2020-05-06 22:59:50");
INSERT INTO product_sales VALUES("336","209","5","","","","1","1","120","0","0","0","120","2020-06-09 12:42:48","2020-06-09 12:42:48");
INSERT INTO product_sales VALUES("337","209","25","","","","1","1","1000","0","10","100","1100","2020-06-09 12:42:48","2020-06-09 12:42:48");
INSERT INTO product_sales VALUES("340","212","1","","","","1","1","350","0","10","35","385","2020-07-03 18:39:14","2020-07-03 18:39:14");
INSERT INTO product_sales VALUES("341","212","25","","","","1","1","1000","0","10","100","1100","2020-07-03 18:39:14","2020-07-03 18:39:14");
INSERT INTO product_sales VALUES("342","212","22","","","","1","1","1000","0","10","100","1100","2020-07-03 18:39:14","2020-07-03 18:39:14");
INSERT INTO product_sales VALUES("343","213","48","","2","","1","1","13","0","0","0","13","2020-07-12 21:51:53","2020-07-12 21:51:53");
INSERT INTO product_sales VALUES("347","217","1","","","","1","1","350","0","10","35","385","2020-07-27 20:38:08","2020-07-27 20:38:08");
INSERT INTO product_sales VALUES("348","218","1","","","","1","1","350","0","10","35","385","2020-07-27 20:40:24","2020-07-27 20:40:24");
INSERT INTO product_sales VALUES("349","219","1","","","","1","1","350","0","10","35","385","2020-07-27 20:46:45","2020-07-27 20:46:45");
INSERT INTO product_sales VALUES("350","220","31","","","","2","1","300","0","0","0","600","2020-08-12 18:28:04","2020-08-12 18:28:04");
INSERT INTO product_sales VALUES("351","220","33","","","","3","1","20","0","0","0","60","2020-08-12 18:28:04","2020-08-12 18:28:04");
INSERT INTO product_sales VALUES("352","220","30","","","","1","1","100","0","0","0","100","2020-08-12 18:28:04","2020-08-12 18:28:04");
INSERT INTO product_sales VALUES("353","221","25","","","","1","1","1000","0","10","100","1100","2020-08-12 18:30:35","2020-08-12 18:30:35");
INSERT INTO product_sales VALUES("354","222","61","","","","2","1","10000","0","15","3000","23000","2020-08-16 22:04:24","2020-08-16 22:04:24");
INSERT INTO product_sales VALUES("355","223","1","","","","1","1","400","0","10","40","440","2020-08-16 22:05:23","2020-08-16 22:05:23");
INSERT INTO product_sales VALUES("356","224","1","","","","3","1","390","30","10","117","1287","2020-08-26 20:01:39","2020-08-26 20:01:39");
INSERT INTO product_sales VALUES("362","230","3","","","","1","1","250","0","0","0","250","2020-10-17 21:28:52","2020-10-17 21:28:52");
INSERT INTO product_sales VALUES("363","231","1","","","","1","1","400","0","10","40","440","2020-10-18 11:13:33","2020-10-18 11:13:33");
INSERT INTO product_sales VALUES("364","232","30","","","","1","1","100","0","0","0","100","2020-10-22 13:30:14","2020-10-22 13:30:14");
INSERT INTO product_sales VALUES("365","233","3","","","","1","1","250","0","0","0","250","2020-10-22 13:56:04","2020-10-22 13:56:04");
INSERT INTO product_sales VALUES("366","234","61","","","","1","1","10000","0","15","1500","11500","2020-10-24 07:05:06","2020-10-24 07:05:06");
INSERT INTO product_sales VALUES("367","235","3","","","","1","1","250","0","0","0","250","2020-10-24 07:07:52","2020-10-24 07:07:52");
INSERT INTO product_sales VALUES("369","237","25","","","","2","1","1000","0","10","200","2200","2020-10-24 15:46:01","2020-10-24 15:46:01");
INSERT INTO product_sales VALUES("370","237","22","","","","2","1","1000","0","10","200","2200","2020-10-24 15:46:01","2020-10-24 15:46:01");
INSERT INTO product_sales VALUES("371","237","61","","","","5","1","10000","0","15","7500","57500","2020-10-24 15:46:01","2020-10-24 15:46:01");
INSERT INTO product_sales VALUES("373","239","60","","9","","1","1","2","0","0","0","2","2020-10-27 05:40:02","2020-10-27 05:40:02");
INSERT INTO product_sales VALUES("374","240","60","","9","","2","1","3","0","0","0","6","2020-10-27 05:42:06","2020-10-27 05:42:06");
INSERT INTO product_sales VALUES("375","241","3","","","","1","1","250","0","0","0","250","2020-10-27 06:32:00","2020-10-27 06:32:00");
INSERT INTO product_sales VALUES("376","242","3","","","","1","1","250","0","0","0","250","2020-10-29 07:30:30","2020-10-29 07:30:30");
INSERT INTO product_sales VALUES("377","243","3","","","","1","1","250","0","0","0","250","2020-11-01 07:21:12","2020-11-01 07:21:12");
INSERT INTO product_sales VALUES("380","245","1","","","","2","1","400","0","10","80","880","2020-11-01 07:42:23","2020-11-01 07:42:23");
INSERT INTO product_sales VALUES("381","245","3","","","","1","1","250","0","0","0","250","2020-11-01 07:42:23","2020-11-01 07:42:23");
INSERT INTO product_sales VALUES("382","246","1","","","","1","1","400","0","10","40","440","2020-11-01 07:50:18","2020-11-02 16:39:17");
INSERT INTO product_sales VALUES("386","250","1","","","","1","1","344","0","10","34.4","378.4","2020-11-06 13:30:41","2020-11-06 13:30:41");
INSERT INTO product_sales VALUES("387","251","1","","","","1","1","344","0","10","34.4","378.4","2020-11-09 13:15:24","2020-11-09 13:15:24");
INSERT INTO product_sales VALUES("388","251","3","","","","1","1","232.2","0","0","0","232.2","2020-11-09 13:15:24","2020-11-09 13:15:24");
INSERT INTO product_sales VALUES("389","252","3","","","","1","1","229.5","0","0","0","229.5","2020-11-11 05:58:58","2020-11-11 05:58:58");
INSERT INTO product_sales VALUES("390","253","3","","","","1","1","212.5","0","0","0","212.5","2020-11-14 06:47:36","2020-11-14 06:47:36");
INSERT INTO product_sales VALUES("391","253","31","","","","1","1","255","0","0","0","255","2020-11-14 06:47:36","2020-11-14 06:47:36");
INSERT INTO product_sales VALUES("392","253","61","","","","1","1","8500","0","15","1275","9775","2020-11-14 06:47:36","2020-11-14 06:47:36");
INSERT INTO product_sales VALUES("395","256","1","","","","1","1","400","0","10","40","440","2020-11-17 06:47:48","2020-11-17 06:47:48");
INSERT INTO product_sales VALUES("396","256","3","","","","1","1","250","0","10","25","275","2020-11-17 06:47:48","2020-11-17 06:47:48");
INSERT INTO product_sales VALUES("397","257","3","","","","1","1","250","0","0","0","250","2020-11-17 07:09:19","2020-11-17 07:09:19");
INSERT INTO product_sales VALUES("398","258","61","","","","3","1","10000","0","15","4500","34500","2020-11-18 06:52:39","2020-11-18 06:52:39");
INSERT INTO product_sales VALUES("402","262","61","","","","2","1","10000","0","15","3000","23000","2020-11-29 05:21:30","2020-11-29 05:21:30");
INSERT INTO product_sales VALUES("403","263","1","","","","1","1","400","0","10","40","440","2020-12-08 09:39:57","2020-12-08 09:39:57");
INSERT INTO product_sales VALUES("416","270","2","","","","1","3","250.43","0","15","37.57","288","2021-01-11 13:45:35","2021-01-11 13:58:44");
INSERT INTO product_sales VALUES("419","270","22","","","","1","1","1000","0","10","100","1100","2021-01-11 13:54:04","2021-01-11 13:58:44");
INSERT INTO product_sales VALUES("420","271","2","","","","1","2","125.22","0","15","18.78","144","2021-01-11 14:16:25","2021-01-11 14:16:25");
INSERT INTO product_sales VALUES("421","271","3","","","","1","1","250","0","0","0","250","2021-01-11 14:16:25","2021-01-11 14:16:25");
INSERT INTO product_sales VALUES("422","271","1","","","","2","1","400","0","10","80","880","2021-01-11 14:16:25","2021-01-11 14:16:25");
INSERT INTO product_sales VALUES("429","274","2","","","","10","1","10.43","0","15","15.65","120","2021-01-13 07:02:43","2021-01-13 07:02:43");
INSERT INTO product_sales VALUES("430","274","30","","","","1","1","100","0","0","0","100","2021-01-13 07:02:43","2021-01-13 07:02:43");
INSERT INTO product_sales VALUES("431","274","3","","","","2","1","250","0","0","0","500","2021-01-13 07:02:43","2021-01-13 07:02:43");
INSERT INTO product_sales VALUES("432","274","1","","","","2","1","380","40","10","76","836","2021-01-13 07:02:43","2021-01-13 07:02:43");
INSERT INTO product_sales VALUES("433","275","1","","","","1","1","400","0","10","40","440","2021-02-01 14:37:48","2021-02-01 14:37:48");
INSERT INTO product_sales VALUES("437","277","32","","","","1","1","10","0","0","0","10","2021-02-16 07:09:48","2021-02-16 07:09:48");
INSERT INTO product_sales VALUES("438","277","48","","5","","1","1","53","0","0","0","53","2021-02-16 07:09:48","2021-02-16 07:09:48");
INSERT INTO product_sales VALUES("439","277","48","","2","","1","1","13","0","0","0","13","2021-02-16 07:09:48","2021-02-16 07:09:48");
INSERT INTO product_sales VALUES("440","277","48","","3","","1","1","3","0","0","0","3","2021-02-16 07:09:48","2021-02-16 07:09:48");
INSERT INTO product_sales VALUES("441","277","61","","","","1","1","10000","0","15","1500","11500","2021-02-16 07:09:48","2021-02-16 07:09:48");
INSERT INTO product_sales VALUES("442","277","31","","","","1","1","300","0","0","0","300","2021-02-16 07:09:48","2021-02-16 07:09:48");
INSERT INTO product_sales VALUES("443","277","30","","","","1","1","100","0","0","0","100","2021-02-16 07:09:48","2021-02-16 07:09:48");
INSERT INTO product_sales VALUES("444","277","25","","","","1","1","1000","0","10","100","1100","2021-02-16 07:09:48","2021-02-16 07:09:48");
INSERT INTO product_sales VALUES("445","277","22","","","","1","1","1000","0","10","100","1100","2021-02-16 07:09:48","2021-02-16 07:09:48");
INSERT INTO product_sales VALUES("446","277","13","","","","1","0","21","0","0","0","21","2021-02-16 07:09:48","2021-02-16 07:09:48");
INSERT INTO product_sales VALUES("447","277","5","","","","1","1","120","0","0","0","120","2021-02-16 07:09:48","2021-02-16 07:09:48");
INSERT INTO product_sales VALUES("448","277","4","","","","1","1","2","0","0","0","2","2021-02-16 07:09:48","2021-02-16 07:09:48");
INSERT INTO product_sales VALUES("449","277","3","","","","1","1","250","0","0","0","250","2021-02-16 07:09:48","2021-02-16 07:09:48");
INSERT INTO product_sales VALUES("450","277","2","","","","1","2","125.22","0","15","18.78","144","2021-02-16 07:09:48","2021-02-16 07:09:48");
INSERT INTO product_sales VALUES("451","277","1","","","","1","1","400","0","10","40","440","2021-02-16 07:09:48","2021-02-16 07:09:48");
INSERT INTO product_sales VALUES("452","278","1","","","","1","1","400","0","10","40","440","2021-02-17 07:32:38","2021-02-17 07:32:38");
INSERT INTO product_sales VALUES("453","278","61","","","","2","1","10000","0","15","3000","23000","2021-02-17 07:32:38","2021-02-17 07:32:38");
INSERT INTO product_sales VALUES("454","278","22","","","","1","1","1000","0","10","100","1100","2021-02-17 07:32:38","2021-02-17 07:32:38");
INSERT INTO product_sales VALUES("455","278","3","","","","1","1","250","0","0","0","250","2021-02-17 07:32:38","2021-02-17 07:32:38");
INSERT INTO product_sales VALUES("456","278","5","","","","1","1","120","0","0","0","120","2021-02-17 07:32:38","2021-02-17 07:32:38");
INSERT INTO product_sales VALUES("457","279","71","1","","","2","1","2","0","0","0","4","2021-03-11 13:40:34","2021-03-11 13:40:34");
INSERT INTO product_sales VALUES("458","280","71","2","","","1","1","2","0","0","0","2","2021-03-17 04:14:11","2021-03-17 04:14:11");
INSERT INTO product_sales VALUES("459","282","71","1","","","1","1","2","0","0","0","2","2021-03-18 05:47:29","2021-03-20 10:59:57");
INSERT INTO product_sales VALUES("468","288","5","","","","1","1","120","0","0","0","120","2021-04-12 13:40:46","2021-04-12 13:40:46");
INSERT INTO product_sales VALUES("473","290","5","","","","1","1","120","0","0","0","120","2021-05-24 19:45:58","2021-05-24 19:45:58");
INSERT INTO product_sales VALUES("478","294","1","","","","1","1","400","0","10","40","440","2021-06-01 12:35:30","2021-06-01 12:35:30");
INSERT INTO product_sales VALUES("479","295","71","3","","","1","1","2","0","0","0","2","2021-06-01 15:22:09","2021-06-01 15:22:09");
INSERT INTO product_sales VALUES("480","296","71","1","","","1","1","2","0","0","0","2","2021-06-01 15:46:24","2021-06-01 15:46:24");
INSERT INTO product_sales VALUES("481","297","3","","","","1","1","250","0","0","0","250","2021-06-01 19:41:50","2021-06-01 19:41:50");
INSERT INTO product_sales VALUES("482","298","71","3","","","1","1","2","0","0","0","2","2021-06-01 19:55:47","2021-06-01 19:55:47");
INSERT INTO product_sales VALUES("483","299","22","","","","1","1","1000","0","10","100","1100","2021-06-16 10:53:25","2021-06-16 10:53:25");
INSERT INTO product_sales VALUES("484","300","1","","","","1","1","400","0","10","40","440","2021-06-16 11:10:33","2021-06-16 11:10:33");
INSERT INTO product_sales VALUES("485","301","22","","","","1","1","1000","0","10","100","1100","2021-06-16 11:11:11","2021-06-16 11:11:11");
INSERT INTO product_sales VALUES("486","301","25","","","","1","1","1000","0","10","100","1100","2021-06-16 11:11:11","2021-06-16 11:11:11");
INSERT INTO product_sales VALUES("487","302","1","","","","1","1","400","0","10","40","440","2021-06-16 20:04:52","2021-06-16 20:04:52");
INSERT INTO product_sales VALUES("488","303","1","","","","1","1","400","0","10","40","440","2021-06-16 20:05:43","2021-06-16 20:05:43");
INSERT INTO product_sales VALUES("489","304","1","","","","1","1","400","0","10","40","440","2021-06-16 20:06:34","2021-06-16 20:06:34");
INSERT INTO product_sales VALUES("490","305","1","","","","1","1","400","0","10","40","440","2021-06-16 20:28:42","2021-06-16 20:28:42");
INSERT INTO product_sales VALUES("491","306","22","","","","1","1","1000","0","10","100","1100","2021-06-17 11:55:53","2021-06-17 11:55:53");
INSERT INTO product_sales VALUES("492","307","22","","","","2","1","1000","0","10","200","2200","2021-06-17 12:23:49","2021-06-17 14:59:33");
INSERT INTO product_sales VALUES("493","308","2","","","","2","2","125.22","0","15","37.57","288","2021-06-18 20:31:27","2021-06-18 20:31:27");
INSERT INTO product_sales VALUES("494","308","1","","","","3","1","400","0","10","120","1320","2021-06-18 20:31:27","2021-06-18 20:31:27");
INSERT INTO product_sales VALUES("495","309","71","2","","","1","1","2","0","0","0","2","2021-06-18 20:32:54","2021-06-18 20:32:54");
INSERT INTO product_sales VALUES("496","310","71","","","","1","1","2","0","0","0","2","2021-06-19 10:51:21","2021-06-19 10:51:21");
INSERT INTO product_sales VALUES("497","310","5","","","","1","1","120","0","0","0","120","2021-06-19 10:51:21","2021-06-19 10:51:21");
INSERT INTO product_sales VALUES("498","310","4","","","","1","1","2","0","0","0","2","2021-06-19 10:51:21","2021-06-19 10:51:21");
INSERT INTO product_sales VALUES("499","310","3","","","","1","1","250","0","0","0","250","2021-06-19 10:51:21","2021-06-19 10:51:21");
INSERT INTO product_sales VALUES("500","310","2","","","","1","2","125.22","0","15","18.78","144","2021-06-19 10:51:21","2021-06-19 10:51:21");
INSERT INTO product_sales VALUES("501","310","1","","","","1","1","400","0","10","40","440","2021-06-19 10:51:21","2021-06-19 10:51:21");
INSERT INTO product_sales VALUES("502","311","1","","","","1","1","400","0","10","40","440","2021-06-19 10:53:44","2021-06-19 10:53:44");
INSERT INTO product_sales VALUES("503","312","1","","","","1","1","400","0","10","40","440","2021-06-19 11:02:31","2021-06-19 11:02:31");
INSERT INTO product_sales VALUES("504","313","3","","","","1","1","250","0","0","0","250","2021-06-19 11:06:28","2021-06-19 11:06:28");
INSERT INTO product_sales VALUES("505","314","22","","","","1","1","1000","0","10","100","1100","2021-06-27 12:42:47","2021-06-27 12:42:47");
INSERT INTO product_sales VALUES("508","317","74","","","","1","0","18","0","0","0","18","2021-07-07 10:34:55","2021-07-07 10:34:55");
INSERT INTO product_sales VALUES("536","325","26","","","","2","0","1200","0","0","0","2400","2021-08-14 21:28:05","2021-08-14 21:34:27");
INSERT INTO product_sales VALUES("547","333","71","","","","1","1","2","0","0","0","2","2021-09-21 13:07:22","2021-09-21 13:07:22");
INSERT INTO product_sales VALUES("548","334","5","","","","1","1","120","0","0","0","120","2021-09-21 15:50:20","2021-09-21 15:50:20");
INSERT INTO product_sales VALUES("549","335","10","","","","0.5","7","22","0","0","0","11","2021-09-23 14:47:04","2021-09-23 14:47:04");
INSERT INTO product_sales VALUES("550","336","5","","","","1","1","120","0","0","0","120","2021-10-03 17:18:29","2021-10-03 17:18:29");
INSERT INTO product_sales VALUES("551","336","22","","","1103","1","1","1000","0","10","100","1100","2021-10-03 17:18:29","2021-10-03 17:18:29");
INSERT INTO product_sales VALUES("553","338","22","","","11103","1","1","1000","0","10","100","1100","2021-10-03 17:44:04","2021-10-03 17:44:04");
INSERT INTO product_sales VALUES("555","340","22","","","11104","1","1","1000","0","10","100","1100","2021-10-07 14:52:29","2021-10-07 14:52:29");
INSERT INTO product_sales VALUES("556","341","22","","","91101","1","1","1000","0","10","100","1100","2021-10-07 15:38:40","2021-10-07 15:38:40");
INSERT INTO product_sales VALUES("557","342","25","","","99901,99902","2","1","1000","0","10","200","2200","2021-10-09 12:12:42","2021-10-09 16:22:39");
INSERT INTO product_sales VALUES("558","342","5","","","","1","1","120","0","0","0","120","2021-10-09 16:22:39","2021-10-09 16:22:39");
INSERT INTO product_sales VALUES("559","343","22","","","1105,91102","2","1","1000","0","10","200","2200","2021-10-09 16:32:32","2021-10-09 16:32:32");
INSERT INTO product_sales VALUES("564","348","22","","","11101","1","1","1000","0","10","100","1100","2021-10-14 15:58:20","2021-10-14 15:58:20");
INSERT INTO product_sales VALUES("565","349","5","","","","1","1","102","0","0","0","102","2021-10-20 13:12:09","2021-10-20 13:12:09");
INSERT INTO product_sales VALUES("568","353","1","","","","1","1","340","0","10","34","374","2021-11-14 13:39:29","2021-11-14 13:39:29");
INSERT INTO product_sales VALUES("569","354","22","","","","1","1","1000","0","10","100","1100","2021-11-15 12:03:09","2021-11-15 12:03:09");
INSERT INTO product_sales VALUES("570","354","71","1","","","1","1","2","0","0","0","2","2021-11-15 12:03:09","2021-11-15 12:03:09");
INSERT INTO product_sales VALUES("571","354","1","","","","1","1","400","0","10","40","440","2021-11-15 12:03:09","2021-11-15 12:03:09");
INSERT INTO product_sales VALUES("572","355","25","","","","1","1","1000","0","10","100","1100","2021-11-24 18:28:58","2021-11-24 18:28:58");
INSERT INTO product_sales VALUES("573","355","22","","","","1","1","1000","0","10","100","1100","2021-11-24 18:28:58","2021-11-24 18:28:58");
INSERT INTO product_sales VALUES("574","355","13","","","","1","0","21","0","0","0","21","2021-11-24 18:28:58","2021-11-24 18:28:58");
INSERT INTO product_sales VALUES("575","355","3","","","","2","1","250","0","0","0","500","2021-11-24 18:28:58","2021-11-24 18:28:58");
INSERT INTO product_sales VALUES("576","355","1","","","","1","1","400","0","10","40","440","2021-11-24 18:28:58","2021-11-24 18:28:58");
INSERT INTO product_sales VALUES("577","356","48","","2","","1","1","13","0","0","0","13","2021-12-07 19:09:32","2021-12-07 19:09:32");
INSERT INTO product_sales VALUES("578","356","5","","","","1","1","120","0","0","0","120","2021-12-07 19:09:32","2021-12-07 19:09:32");
INSERT INTO product_sales VALUES("579","356","1","","","","1","1","400","0","10","40","440","2021-12-07 19:09:32","2021-12-07 19:09:32");
INSERT INTO product_sales VALUES("584","360","2","","","","2","1","10.43","0","15","3.13","24","2021-12-22 19:03:13","2022-01-30 13:37:54");
INSERT INTO product_sales VALUES("587","362","72","","","","2","1","4.25","0","0","0","8.5","2022-02-03 15:59:31","2022-02-03 15:59:31");
INSERT INTO product_sales VALUES("588","363","5","","","","2","1","118","0","0","0","236","2022-02-03 19:05:47","2022-05-22 16:39:48");
INSERT INTO product_sales VALUES("589","363","3","","","","1","1","250","0","0","0","250","2022-02-03 19:05:47","2022-05-22 16:39:48");
INSERT INTO product_sales VALUES("590","364","1","","","","1","1","400","0","10","40","440","2022-02-05 17:45:26","2022-02-05 19:31:58");
INSERT INTO product_sales VALUES("591","365","3","","","","1","1","250","0","0","0","250","2022-02-05 17:46:16","2022-02-05 19:31:09");
INSERT INTO product_sales VALUES("598","372","5","","","","1","1","120","0","0","0","120","2022-02-19 12:08:15","2022-02-20 21:15:54");
INSERT INTO product_sales VALUES("603","376","1","","","","1","1","400","0","10","40","440","2022-02-20 20:36:38","2022-02-20 21:15:23");
INSERT INTO product_sales VALUES("604","377","3","","","","2","1","250","0","0","0","500","2022-02-27 11:59:05","2022-03-05 12:16:22");
INSERT INTO product_sales VALUES("608","377","5","","","","1","1","120","0","0","0","120","2022-03-05 12:12:38","2022-03-05 12:16:22");
INSERT INTO product_sales VALUES("609","363","13","","","","1","0","15","0","0","0","15","2022-03-08 14:25:13","2022-05-22 16:39:48");
INSERT INTO product_sales VALUES("610","363","75","","","","1","0","20","0","0","0","20","2022-03-08 14:25:13","2022-05-22 16:39:48");
INSERT INTO product_sales VALUES("611","379","22","","","","1","1","1000","0","10","100","1100","2022-03-09 11:59:01","2022-03-09 11:59:01");
INSERT INTO product_sales VALUES("612","379","3","","","","1","1","250","0","0","0","250","2022-03-09 11:59:01","2022-03-09 11:59:01");
INSERT INTO product_sales VALUES("613","379","1","","","","1","1","400","0","10","40","440","2022-03-09 11:59:01","2022-03-09 11:59:01");
INSERT INTO product_sales VALUES("614","380","1","","","","1","1","400","0","10","40","440","2022-03-12 20:20:27","2022-03-13 11:37:35");
INSERT INTO product_sales VALUES("615","381","3","","","","1","1","250","0","0","0","250","2022-03-12 20:24:52","2022-03-12 20:25:35");
INSERT INTO product_sales VALUES("616","381","5","","","","1","1","110","0","0","0","110","2022-03-12 20:25:35","2022-03-12 20:25:35");
INSERT INTO product_sales VALUES("617","382","5","","","","1","1","120","0","0","0","120","2022-04-24 11:01:44","2022-04-24 11:01:44");
INSERT INTO product_sales VALUES("618","382","72","","","","1","1","5","0","0","0","5","2022-04-24 11:01:44","2022-04-24 11:01:44");
INSERT INTO product_sales VALUES("619","382","25","","","","1","1","1000","0","10","100","1100","2022-04-24 11:01:44","2022-04-24 11:01:44");
INSERT INTO product_sales VALUES("620","383","25","","","","2","1","1000","0","10","200","2200","2022-05-16 11:24:27","2022-05-16 11:24:27");
INSERT INTO product_sales VALUES("621","383","22","","","","1","1","1000","0","10","100","1100","2022-05-16 11:24:27","2022-05-16 11:24:27");
INSERT INTO product_sales VALUES("623","385","3","","","","1","1","250","0","0","0","250","2022-05-26 21:01:12","2022-05-26 21:01:12");
INSERT INTO product_sales VALUES("624","385","5","","","","1","1","120","0","0","0","120","2022-05-26 21:01:12","2022-05-26 21:01:12");
INSERT INTO product_sales VALUES("625","386","1","","","","2","1","400","0","10","80","880","2022-05-31 12:20:26","2022-06-06 17:11:27");
INSERT INTO product_sales VALUES("644","397","5","","","","1","1","120","0","0","0","120","2022-06-06 17:14:13","2022-06-06 17:14:13");
INSERT INTO product_sales VALUES("645","397","22","","","","1","1","1000","0","10","100","1100","2022-06-06 17:14:13","2022-06-06 17:14:13");
INSERT INTO product_sales VALUES("646","397","25","","","","1","1","1000","0","10","100","1100","2022-06-06 17:14:13","2022-06-06 17:14:13");
INSERT INTO product_sales VALUES("649","400","25","","","","1","1","1000","0","10","100","1100","2022-06-14 11:57:19","2022-06-14 11:57:19");
INSERT INTO product_sales VALUES("650","400","22","","","","2","1","1000","0","10","200","2200","2022-06-14 11:57:19","2022-06-14 11:57:19");
INSERT INTO product_sales VALUES("653","403","5","","","","1","1","120","0","0","0","120","2022-06-14 17:01:44","2022-06-14 17:01:44");
INSERT INTO product_sales VALUES("654","404","31","","","","1","1","300","0","0","0","300","2022-06-15 10:23:30","2022-06-15 10:23:30");
INSERT INTO product_sales VALUES("665","409","1","","","","1","1","400","0","10","40","440","2022-08-09 11:54:44","2022-08-09 11:54:44");
INSERT INTO product_sales VALUES("666","409","5","","","","1","1","120","0","0","0","120","2022-08-09 11:54:44","2022-08-09 11:54:44");
INSERT INTO product_sales VALUES("667","409","87","","30","","1","1","31","0","0","0","31","2022-08-09 11:54:44","2022-08-09 11:54:44");
INSERT INTO product_sales VALUES("668","410","5","","","","1","1","120","0","0","0","120","2022-08-25 13:31:37","2022-08-25 13:31:37");
INSERT INTO product_sales VALUES("669","411","1","","","","1","1","400","0","10","40","440","2022-09-01 12:36:19","2022-09-01 12:36:19");
INSERT INTO product_sales VALUES("670","411","3","","","","1","1","250","0","0","0","250","2022-09-01 12:36:19","2022-09-01 12:36:19");
INSERT INTO product_sales VALUES("671","412","3","","","","1","1","250","0","0","0","250","2022-09-04 13:17:55","2022-09-04 13:17:55");
INSERT INTO product_sales VALUES("672","412","1","","","","1","1","400","0","10","40","440","2022-09-04 13:17:55","2022-09-04 13:17:55");
INSERT INTO product_sales VALUES("673","413","90","","35","","1","1","1100","0","10","110","1210","2022-09-11 13:18:26","2022-09-11 13:18:26");
INSERT INTO product_sales VALUES("674","414","1","","","","1","1","400","0","10","40","440","2022-09-15 20:59:00","2022-09-15 20:59:00");
INSERT INTO product_sales VALUES("675","415","1","","","","1","1","400","0","10","40","440","2022-10-02 20:16:36","2022-10-02 20:16:36");
INSERT INTO product_sales VALUES("676","415","5","","","","1","1","120","0","0","0","120","2022-10-02 20:16:36","2022-10-02 20:16:36");
INSERT INTO product_sales VALUES("677","416","5","","","","1","1","120","0","0","0","120","2022-10-18 14:10:13","2022-10-18 14:10:13");
INSERT INTO product_sales VALUES("682","421","26","","","","1","0","1250","0","0","0","1250","2022-11-24 17:57:16","2022-11-24 17:57:16");
INSERT INTO product_sales VALUES("683","422","2","","","","1","2","125.22","0","15","18.78","144","2022-11-24 18:26:01","2022-11-24 18:26:01");
INSERT INTO product_sales VALUES("684","423","3","","","","1","1","250","0","0","0","250","2022-11-24 18:26:53","2022-11-24 18:26:53");
INSERT INTO product_sales VALUES("685","424","5","","","","1","1","120","0","0","0","120","2022-12-11 14:53:25","2022-12-11 14:53:25");
INSERT INTO product_sales VALUES("686","424","10","","","","1","7","22","0","0","0","22","2022-12-11 14:53:25","2022-12-11 14:53:25");
INSERT INTO product_sales VALUES("687","424","2","","","","1","2","125.22","0","15","18.78","144","2022-12-11 14:53:25","2022-12-11 14:53:25");
INSERT INTO product_sales VALUES("688","425","2","","","","1","2","125.22","0","15","18.78","144","2023-01-10 16:08:32","2023-01-10 16:08:32");
INSERT INTO product_sales VALUES("689","426","2","","","","1","2","125.22","0","15","18.78","144","2023-01-11 00:39:25","2023-01-11 00:39:25");
INSERT INTO product_sales VALUES("690","427","2","","","","1","2","125.22","0","15","18.78","144","2023-01-11 00:40:00","2023-01-11 00:40:00");
INSERT INTO product_sales VALUES("691","428","1","","","","1","1","363.64","0","10","36.36","400","2023-01-18 16:28:10","2023-01-18 16:28:10");
INSERT INTO product_sales VALUES("692","428","2","","","","1","2","125.22","0","15","18.78","144","2023-01-18 16:28:10","2023-01-18 16:28:10");
INSERT INTO product_sales VALUES("693","429","1","","","","1","1","363.64","0","10","36.36","400","2023-01-21 15:20:01","2023-01-21 15:20:01");
INSERT INTO product_sales VALUES("694","430","1","","","","1","1","363.64","0","10","36.36","400","2023-01-21 19:04:40","2023-01-21 19:04:40");
INSERT INTO product_sales VALUES("695","431","1","","","","1","1","363.64","0","10","36.36","400","2023-01-21 19:17:10","2023-01-21 19:17:10");
INSERT INTO product_sales VALUES("696","432","1","","","","1","1","363.64","0","10","36.36","400","2023-01-21 19:18:43","2023-01-21 19:18:43");
INSERT INTO product_sales VALUES("697","433","1","","","","1","1","363.64","0","10","36.36","400","2023-02-22 15:02:51","2023-02-22 15:02:51");
INSERT INTO product_sales VALUES("698","434","2","","","","1","2","125.22","0","15","18.78","144","2023-02-23 16:58:11","2023-02-23 16:58:11");
INSERT INTO product_sales VALUES("699","435","2","","","","1","2","125.22","0","15","18.78","144","2023-02-24 01:11:42","2023-02-24 01:11:42");
INSERT INTO product_sales VALUES("700","435","3","","","","1","1","250","0","0","0","250","2023-02-24 01:11:42","2023-02-24 01:11:42");
INSERT INTO product_sales VALUES("701","435","1","","","","1","1","363.64","0","10","36.36","400","2023-02-24 01:11:42","2023-02-24 01:11:42");
INSERT INTO product_sales VALUES("702","436","2","","","","1","2","125.22","0","15","18.78","144","2023-02-24 01:14:48","2023-02-24 01:14:48");
INSERT INTO product_sales VALUES("703","436","3","","","","1","1","250","0","0","0","250","2023-02-24 01:14:48","2023-02-24 01:14:48");
INSERT INTO product_sales VALUES("704","436","1","","","","1","1","363.64","0","10","36.36","400","2023-02-24 01:14:48","2023-02-24 01:14:48");
INSERT INTO product_sales VALUES("705","437","2","","","","1","2","125.22","0","15","18.78","144","2023-02-24 01:15:03","2023-02-24 01:15:03");
INSERT INTO product_sales VALUES("706","437","3","","","","1","1","250","0","0","0","250","2023-02-24 01:15:03","2023-02-24 01:15:03");
INSERT INTO product_sales VALUES("707","437","1","","","","1","1","363.64","0","10","36.36","400","2023-02-24 01:15:03","2023-02-24 01:15:03");
INSERT INTO product_sales VALUES("708","438","10","","","","1","7","22","0","0","0","22","2023-02-24 11:52:03","2023-02-24 11:52:03");
INSERT INTO product_sales VALUES("709","438","2","","","","1","2","125.22","0","15","18.78","144","2023-02-24 11:52:03","2023-02-24 11:52:03");
INSERT INTO product_sales VALUES("710","438","3","","","","1","1","250","0","0","0","250","2023-02-24 11:52:03","2023-02-24 11:52:03");
INSERT INTO product_sales VALUES("711","438","1","","","","1","1","363.64","0","10","36.36","400","2023-02-24 11:52:03","2023-02-24 11:52:03");
INSERT INTO product_sales VALUES("725","455","1","","","","1","1","327.27","0","10","32.73","360","2023-03-13 17:31:10","2023-03-13 17:31:10");
INSERT INTO product_sales VALUES("727","457","5","","","","1","1","108","0","0","0","108","2023-03-23 12:45:27","2023-03-23 12:45:27");
INSERT INTO product_sales VALUES("728","457","1","","","","1","1","327.27","0","10","32.73","360","2023-03-23 12:45:27","2023-03-23 12:45:27");
INSERT INTO product_sales VALUES("729","458","5","","","","1","1","108","0","0","0","108","2023-03-23 17:33:59","2023-03-23 17:33:59");
INSERT INTO product_sales VALUES("730","459","1","","","","1","1","327.27","0","10","32.73","360","2023-03-27 10:54:27","2023-03-27 10:54:27");
INSERT INTO product_sales VALUES("731","460","1","","","","1","1","327.27","0","10","32.73","360","2023-03-27 11:08:18","2023-03-27 11:08:18");
INSERT INTO product_sales VALUES("732","460","5","","","","3","1","108","0","0","0","324","2023-03-27 11:08:18","2023-03-27 11:08:18");
INSERT INTO product_sales VALUES("735","462","3","","","","1","1","250","0","0","0","250","2023-04-05 13:39:19","2023-04-05 13:39:19");
INSERT INTO product_sales VALUES("736","462","2","","","","2","2","125.22","0","15","37.57","288","2023-04-05 13:39:19","2023-04-05 13:39:19");
INSERT INTO product_sales VALUES("737","463","5","","","","1","1","100","0","0","0","100","2023-04-24 19:56:49","2023-04-24 19:56:49");
INSERT INTO product_sales VALUES("746","469","31","","","","1","1","300","0","0","0","300","2023-05-06 16:06:46","2023-05-06 16:06:46");
INSERT INTO product_sales VALUES("747","469","22","","","","1","1","909.09","0","10","90.91","1000","2023-05-06 16:06:46","2023-05-06 16:06:46");
INSERT INTO product_sales VALUES("748","469","1","","","","1","1","363.64","0","10","36.36","400","2023-05-06 16:06:46","2023-05-06 16:06:46");
INSERT INTO product_sales VALUES("749","470","30","","","","1","1","100","0","0","0","100","2023-05-17 13:36:23","2023-05-17 13:36:23");
INSERT INTO product_sales VALUES("750","470","5","","","","2","1","120","0","0","0","240","2023-05-17 13:36:23","2023-05-17 13:36:23");
INSERT INTO product_sales VALUES("751","471","3","","","","1","1","250","0","0","0","250","2023-06-04 14:14:35","2023-06-04 14:14:35");
INSERT INTO product_sales VALUES("752","472","2","","","","1","2","125.22","0","15","18.78","144","2023-06-04 15:48:29","2023-06-04 15:48:29");
INSERT INTO product_sales VALUES("753","472","10","","","","1","7","22","0","0","0","22","2023-06-04 15:48:29","2023-06-04 15:48:29");
INSERT INTO product_sales VALUES("754","473","3","","","","1","1","250","0","0","0","250","2023-06-05 15:07:12","2023-06-05 15:07:12");
INSERT INTO product_sales VALUES("755","474","10","","","","1","7","23.1","0","0","0","23.1","2023-07-09 10:32:57","2023-07-09 10:32:57");
INSERT INTO product_sales VALUES("756","474","5","","","","2","1","126","0","0","0","252","2023-07-09 10:32:57","2023-07-09 10:32:57");
INSERT INTO product_sales VALUES("758","476","1","","","","1","1","363.64","0","10","36.36","400","2023-07-10 15:35:04","2023-07-10 15:35:04");
INSERT INTO product_sales VALUES("759","477","30","","","","1","1","100","0","0","0","100","2023-07-10 15:44:13","2023-07-10 15:44:13");



CREATE TABLE `product_transfer` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `transfer_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_batch_id` int DEFAULT NULL,
  `variant_id` int DEFAULT NULL,
  `imei_number` text COLLATE utf8mb4_unicode_ci,
  `qty` double NOT NULL,
  `purchase_unit_id` int NOT NULL,
  `net_unit_cost` double NOT NULL,
  `tax_rate` double NOT NULL,
  `tax` double NOT NULL,
  `total` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO product_transfer VALUES("1","1","4","","","","100","1","1","0","0","100","2018-08-08 17:17:10","2018-12-25 04:16:55");
INSERT INTO product_transfer VALUES("7","6","48","","3","","1","1","2","0","0","2","2019-12-05 19:55:04","2019-12-05 20:09:42");
INSERT INTO product_transfer VALUES("11","8","5","","","","10","1","100","0","0","1000","2020-01-22 12:30:59","2020-01-22 12:30:59");
INSERT INTO product_transfer VALUES("13","10","1","","","","1","1","320","10","32","352","2020-10-08 13:27:35","2020-10-08 13:29:35");
INSERT INTO product_transfer VALUES("14","11","62","","12","","1","1","1","0","0","1","2020-10-18 18:17:08","2020-10-18 18:17:08");
INSERT INTO product_transfer VALUES("15","12","61","","","","10","1","3000","15","4500","34500","2020-10-24 09:01:46","2020-10-24 09:01:46");
INSERT INTO product_transfer VALUES("32","21","5","","","","6","1","100","0","0","600","2022-03-09 11:44:11","2022-06-05 12:37:31");
INSERT INTO product_transfer VALUES("33","22","10","","","","1","7","10","0","0","10","2023-07-09 10:48:55","2023-07-09 10:53:39");



CREATE TABLE `product_variants` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `variant_id` int NOT NULL,
  `position` int NOT NULL,
  `item_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `additional_cost` double DEFAULT NULL,
  `additional_price` double DEFAULT NULL,
  `qty` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO product_variants VALUES("3","48","3","1","S-93475396","","","13","2019-11-21 13:03:04","2023-07-09 10:34:45");
INSERT INTO product_variants VALUES("5","48","5","3","L-93475396","10","50","12","2019-11-24 12:07:20","2022-08-08 21:34:26");
INSERT INTO product_variants VALUES("6","48","2","2","M-93475396","5","10","13","2019-11-24 13:17:07","2022-08-08 21:13:57");
INSERT INTO product_variants VALUES("10","60","9","1","a-32081679","","","1","2020-05-18 22:44:14","2020-10-27 05:42:06");
INSERT INTO product_variants VALUES("11","60","11","2","b-32081679","","","0","2020-05-18 22:58:31","2020-05-18 22:58:31");
INSERT INTO product_variants VALUES("12","62","12","1","variant 1-81145830","","","3","2020-09-27 12:08:27","2021-02-11 07:28:16");
INSERT INTO product_variants VALUES("13","62","13","2","variant 2-81145830","","","0","2020-09-27 12:08:27","2020-09-27 12:08:27");
INSERT INTO product_variants VALUES("26","87","18","1","s/red-82490498","","","0","2022-08-03 11:25:19","2022-08-03 11:25:19");
INSERT INTO product_variants VALUES("27","87","24","2","s/blue-82490498","","","0","2022-08-03 11:25:19","2022-08-03 11:25:19");
INSERT INTO product_variants VALUES("28","87","20","4","m/red-82490498","","","0","2022-08-03 11:25:19","2022-08-06 11:54:37");
INSERT INTO product_variants VALUES("29","87","25","5","m/blue-82490498","","","0","2022-08-03 11:25:20","2022-08-06 11:54:37");
INSERT INTO product_variants VALUES("30","87","22","7","l/red-82490498","2","5","1","2022-08-03 11:25:20","2022-08-08 21:13:57");
INSERT INTO product_variants VALUES("31","87","26","8","l/blue-82490498","2","5","0","2022-08-03 11:25:20","2022-08-06 11:54:37");
INSERT INTO product_variants VALUES("32","87","27","3","s/green-82490498","","","0","2022-08-06 11:54:37","2022-08-06 11:54:37");
INSERT INTO product_variants VALUES("33","87","28","6","m/green-82490498","","","0","2022-08-06 11:54:37","2022-08-06 11:54:37");
INSERT INTO product_variants VALUES("34","87","29","9","l/green-82490498","2","5","0","2022-08-06 11:54:37","2022-08-08 19:58:47");
INSERT INTO product_variants VALUES("38","87","30","10","xl/red-82490498","3","6","0","2022-08-08 20:05:58","2022-08-09 11:54:44");
INSERT INTO product_variants VALUES("39","87","31","11","xl/blue-82490498","3","6","0","2022-08-08 20:05:58","2022-08-08 20:05:58");
INSERT INTO product_variants VALUES("40","87","32","12","xl/green-82490498","3","6","0","2022-08-08 20:05:58","2022-08-08 20:05:58");
INSERT INTO product_variants VALUES("43","90","33","1","red-17628500","","","10","2022-09-11 12:56:55","2022-09-11 13:04:41");
INSERT INTO product_variants VALUES("44","90","34","2","black-17628500","","","10","2022-09-11 12:56:55","2022-09-11 13:04:41");
INSERT INTO product_variants VALUES("45","90","35","3","blue-17628500","50","100","9","2022-09-11 12:56:55","2022-09-11 13:18:26");



CREATE TABLE `product_warehouse` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_batch_id` int DEFAULT NULL,
  `variant_id` int DEFAULT NULL,
  `imei_number` text COLLATE utf8mb4_unicode_ci,
  `warehouse_id` int NOT NULL,
  `qty` double NOT NULL,
  `price` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO product_warehouse VALUES("10","1","","","","1","148","","2018-08-08 14:30:12","2023-07-10 15:35:04");
INSERT INTO product_warehouse VALUES("11","2","","","","1","1454","","2018-08-08 14:30:12","2023-07-08 16:37:08");
INSERT INTO product_warehouse VALUES("12","3","","","","1","102","","2018-08-08 14:30:13","2022-12-11 14:54:54");
INSERT INTO product_warehouse VALUES("13","5","","","","1","68","","2018-08-08 14:30:13","2023-07-09 12:37:20");
INSERT INTO product_warehouse VALUES("14","4","","","","1","171","","2018-08-08 15:16:09","2020-03-02 11:56:03");
INSERT INTO product_warehouse VALUES("15","4","","","","2","119","","2018-08-08 17:16:15","2021-07-15 18:25:45");
INSERT INTO product_warehouse VALUES("16","2","","","","2","1653","","2018-08-08 17:26:49","2023-06-04 15:48:29");
INSERT INTO product_warehouse VALUES("17","1","","","","2","28","","2018-08-08 17:33:33","2023-07-10 14:30:18");
INSERT INTO product_warehouse VALUES("18","3","","","","2","37","","2018-08-09 05:47:23","2023-06-05 15:07:12");
INSERT INTO product_warehouse VALUES("19","5","","","","2","47","","2018-08-09 05:48:36","2023-05-17 13:36:23");
INSERT INTO product_warehouse VALUES("20","10","","","","1","49","","2018-08-09 05:49:29","2023-07-10 15:20:35");
INSERT INTO product_warehouse VALUES("21","10","","","","2","58.5","","2018-08-09 05:49:55","2023-07-09 10:53:39");
INSERT INTO product_warehouse VALUES("22","22","","","11101,11102,11103,,7001","1","29","","2018-09-03 10:06:09","2022-06-01 10:54:30");
INSERT INTO product_warehouse VALUES("23","22","","","7001","2","8","","2018-09-03 10:07:14","2023-05-06 16:06:46");
INSERT INTO product_warehouse VALUES("24","24","","","","2","0","","2018-09-16 03:49:30","2018-09-16 03:50:49");
INSERT INTO product_warehouse VALUES("25","25","","","","1","25","","2018-10-23 07:14:21","2022-05-31 12:33:40");
INSERT INTO product_warehouse VALUES("26","25","","","","2","20","","2018-10-23 07:14:41","2022-11-24 17:57:16");
INSERT INTO product_warehouse VALUES("27","31","","","","1","14","","2018-12-05 05:34:30","2021-01-10 07:04:28");
INSERT INTO product_warehouse VALUES("28","30","","","","1","13","","2018-12-05 05:34:30","2023-07-10 15:44:13");
INSERT INTO product_warehouse VALUES("29","31","","","","2","9","","2018-12-05 05:35:08","2023-05-06 16:06:46");
INSERT INTO product_warehouse VALUES("30","30","","","","2","9","","2018-12-05 05:35:08","2023-05-17 13:36:23");
INSERT INTO product_warehouse VALUES("31","32","","","","1","10","","2018-12-19 05:57:16","2019-02-09 10:45:23");
INSERT INTO product_warehouse VALUES("32","32","","","","2","18","","2018-12-19 05:57:41","2023-07-10 15:21:27");
INSERT INTO product_warehouse VALUES("33","33","","","","1","16","","2018-12-24 06:38:40","2019-03-03 10:39:17");
INSERT INTO product_warehouse VALUES("34","33","","","","2","22","","2019-02-09 10:21:38","2021-07-15 18:25:45");
INSERT INTO product_warehouse VALUES("35","48","","3","","1","3","","2019-11-25 20:23:02","2023-07-09 10:34:45");
INSERT INTO product_warehouse VALUES("36","48","","2","","1","5","","2019-11-26 12:47:42","2022-08-08 21:13:57");
INSERT INTO product_warehouse VALUES("37","48","","3","","2","9","","2019-11-26 14:12:08","2021-07-15 18:25:45");
INSERT INTO product_warehouse VALUES("38","48","","2","","2","8","","2019-11-26 14:12:08","2021-12-07 19:09:32");
INSERT INTO product_warehouse VALUES("39","48","","5","","1","3","","2019-12-21 16:18:51","2022-08-08 21:34:26");
INSERT INTO product_warehouse VALUES("40","48","","5","","2","9","","2019-12-22 14:36:39","2021-07-15 18:25:45");
INSERT INTO product_warehouse VALUES("45","61","","","","2","8","","2020-08-16 22:02:07","2021-07-15 18:25:45");
INSERT INTO product_warehouse VALUES("46","62","","12","","1","2","","2020-09-27 12:55:33","2020-11-16 15:01:45");
INSERT INTO product_warehouse VALUES("47","62","","12","","2","1","","2020-10-18 18:17:08","2021-02-11 07:28:16");
INSERT INTO product_warehouse VALUES("48","61","","","","1","8","","2020-10-24 09:01:46","2020-11-29 05:21:30");
INSERT INTO product_warehouse VALUES("49","60","","9","","1","1","","2020-10-26 16:34:05","2020-10-27 05:42:06");
INSERT INTO product_warehouse VALUES("51","71","1","","","1","18","","2021-03-07 14:14:05","2022-05-14 14:50:17");
INSERT INTO product_warehouse VALUES("52","71","1","","","2","4","","2021-03-07 14:21:16","2022-03-05 12:16:22");
INSERT INTO product_warehouse VALUES("53","71","2","","","1","3","","2021-03-07 14:55:06","2021-06-18 20:32:54");
INSERT INTO product_warehouse VALUES("54","71","3","","","1","2","","2021-03-09 15:42:13","2021-09-21 15:49:37");
INSERT INTO product_warehouse VALUES("55","71","2","","","2","0","","2021-05-23 13:09:03","2021-05-26 13:38:06");
INSERT INTO product_warehouse VALUES("56","72","","","","1","5","","2021-07-06 13:10:42","2021-07-06 13:10:42");
INSERT INTO product_warehouse VALUES("57","72","","","","2","21","","2021-07-06 13:11:07","2022-04-24 11:01:44");
INSERT INTO product_warehouse VALUES("58","22","4","","","1","1","","2021-10-20 13:47:09","2021-10-20 13:47:09");
INSERT INTO product_warehouse VALUES("59","79","","","","2","12","","2022-06-01 12:41:19","2022-06-06 17:13:37");
INSERT INTO product_warehouse VALUES("67","87","","30","","1","0","","2022-08-08 21:13:57","2022-08-09 11:54:44");
INSERT INTO product_warehouse VALUES("68","87","","22","","1","1","","2022-08-08 21:13:57","2022-08-08 21:13:57");
INSERT INTO product_warehouse VALUES("69","90","","35","","1","9","","2022-09-11 13:04:41","2022-09-11 13:18:26");
INSERT INTO product_warehouse VALUES("70","90","","34","","1","10","","2022-09-11 13:04:41","2022-09-11 13:04:41");
INSERT INTO product_warehouse VALUES("71","90","","33","","1","10","","2022-09-11 13:04:41","2022-09-11 13:04:41");



CREATE TABLE `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `barcode_symbology` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_id` int DEFAULT NULL,
  `category_id` int NOT NULL,
  `unit_id` int NOT NULL,
  `purchase_unit_id` int NOT NULL,
  `sale_unit_id` int NOT NULL,
  `cost` double NOT NULL,
  `price` double NOT NULL,
  `qty` double DEFAULT NULL,
  `alert_quantity` double DEFAULT NULL,
  `daily_sale_objective` double DEFAULT NULL,
  `promotion` tinyint DEFAULT NULL,
  `promotion_price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `starting_date` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_date` date DEFAULT NULL,
  `tax_id` int DEFAULT NULL,
  `tax_method` int DEFAULT NULL,
  `image` longtext COLLATE utf8mb4_unicode_ci,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_embeded` tinyint(1) DEFAULT NULL,
  `is_variant` tinyint(1) DEFAULT NULL,
  `is_batch` tinyint(1) DEFAULT NULL,
  `is_diffPrice` tinyint(1) DEFAULT NULL,
  `is_imei` tinyint(1) DEFAULT NULL,
  `featured` tinyint DEFAULT NULL,
  `product_list` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `variant_list` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty_list` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_list` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_details` text COLLATE utf8mb4_unicode_ci,
  `variant_option` text COLLATE utf8mb4_unicode_ci,
  `variant_value` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO products VALUES("1","Mouse","6952897986810","standard","UPCE","4","3","1","1","1","320","400","176","20","","","350","2020-06-30","2020-07-31","1","2","toponemouse.jpg","","0","","","0","","1","","","","","11:11:30 AMhello world","","","1","2018-05-13 04:23:03","2023-07-10 15:35:04");
INSERT INTO products VALUES("2","mango","72782608","standard","C128","","1","1","3","2","8","12","3107","3180","","","","","","2","2","mango.jpg","","","","","0","","1","","","","","","","","0","2018-05-13 04:38:31","2023-07-10 15:24:20");
INSERT INTO products VALUES("3","Earphone","85415108","standard","C128","4","2","1","1","1","200","250","139","25","","","220","2020-11-02","2020-11-30","","2","airphonesamsung.jpg","","","","","0","","1","","","","","Earphone with good sound quality.
jhjkhjkhjh @@","","","1","2018-05-13 04:39:55","2023-06-05 15:07:12");
INSERT INTO products VALUES("4","lychee","38314290","standard","C128","","1","1","1","1","1","2","290","50","","","","","","","2","lychee.jpg","","","","","","","1","","","","","sweet lychee from kalipur, bashkhali","","","0","2018-05-24 04:54:56","2023-07-10 15:24:20");
INSERT INTO products VALUES("5","Baby doll","31261512","standard","C128","3","4","1","1","1","100","120","115","50","2","1","100","2023-03-29","2023-04-30","","2","","","0","","","0","","1","","","","","&lt;IMG SRC=@javascript:alert('XSS');@&gt;","","","0","2018-05-24 04:58:20","2023-07-10 14:21:48");
INSERT INTO products VALUES("6","test","78041363","standard","C128","","2","1","1","1","12","21","0","","","","","","","","1","","","","","","","","","","","","","","","","0","2018-05-28 05:50:25","2018-05-28 05:53:24");
INSERT INTO products VALUES("10","potato","212132","standard","C128","","9","7","7","7","10","22","107.5","","","","","","","","2","potato.jpeg","","0","","","0","","1","","","","","","","","1","2018-06-25 07:34:53","2023-07-10 15:20:35");
INSERT INTO products VALUES("13","ldms","40624536","digital","C128","","3","0","0","0","0","21","0","","","","","","","","2","ldms.jpg","1532330693.JPG","","","","","","1","","","","","Amazon Prime 1 MonthPrice: 300tk
User Ability: Max 2 Mobile UserDuration: 1 MonthPackage: Premium","","","1","2018-07-23 07:09:29","2020-03-07 22:45:01");
INSERT INTO products VALUES("14","ni","40237871","standard","C128","","9","8","8","8","55","56","0","","","","","","","","1","","","","","","","","","","","","","","","","0","2018-08-01 04:36:51","2018-08-01 04:41:48");
INSERT INTO products VALUES("15","edawe","awd","standard","C128","","1","4","4","4","312","132","0","","","","","","","","1","","","","","","","","","","","","","","","","0","2018-08-05 07:26:19","2023-07-10 15:24:20");
INSERT INTO products VALUES("16","weq","eqw","digital","C128","","2","0","0","0","0","2","0","","","","","","","","1","","1533454125.jpg","","","","","","","","","","","","","","0","2018-08-05 07:28:45","2018-08-05 07:29:23");
INSERT INTO products VALUES("17","et","wer","standard","C128","","2","4","4","4","12","21","0","","","","","","","","1","","","","","","","","","","","","","","","","0","2018-08-06 10:25:43","2018-08-06 10:27:41");
INSERT INTO products VALUES("18","wre","wre","standard","C128","","1","4","4","4","3","2","0","","","","","","","","1","","","","","","","","","","","","","","","","0","2018-08-06 10:45:42","2023-07-10 15:24:20");
INSERT INTO products VALUES("19","ewr","wer","standard","C128","","2","4","4","4","3","23","0","","","1","","2018-08-06","2018-08-10","","1","","","","","","","","","","","","","","","","0","2018-08-06 11:41:46","2019-11-28 11:16:47");
INSERT INTO products VALUES("20","dim","4234","standard","C128","","10","1","1","1","1","2","0","","","","","","","","1","","","","","","","","","","","","","","","","0","2018-08-29 05:37:07","2018-08-29 05:43:18");
INSERT INTO products VALUES("21","dim","1","standard","C128","","11","1","1","1","1","2","0","","","","","","","","1","","","","","","","","","","","","","","","","0","2018-08-29 05:49:27","2018-08-29 05:53:21");
INSERT INTO products VALUES("22","iphone-X","97103461","standard","C128","5","2","1","1","1","800","1000","38","20","","","","","","1","2","iphoneX.jpg","","","","","0","1","1","","","","","","","","1","2018-09-03 10:05:17","2023-05-06 16:06:46");
INSERT INTO products VALUES("23","qwq","1237920612311a","standard","C39","","2","1","1","1","1","2","0","","","","","","","","1","","","","","","","","","","","","","","","","0","2018-09-11 05:53:18","2018-09-11 05:56:04");
INSERT INTO products VALUES("24","chocolate","84802317","standard","C128","","9","1","1","1","1","2","0","","","","","","","","1","","","","","","","","1","","","","","","","","0","2018-09-16 03:42:01","2018-09-16 04:10:43");
INSERT INTO products VALUES("25","Galaxy S9","72100265","standard","C128","","2","1","1","1","500","1000","45","50","","","","","","1","2","SamsungGalaxyS9.jpg","","","","","0","","1","","","","","","","","1","2018-10-23 07:13:22","2022-11-24 17:57:16");
INSERT INTO products VALUES("26","Samsung Set","39053411","combo","C128","4","2","0","0","0","0","1250","0","","","","","","","","2","zummXD2dvAtI.png","","","","","","","","3,25","","1,1","250,1000","","","","1","2018-11-19 09:59:06","2018-11-20 08:12:02");
INSERT INTO products VALUES("27","mixed fruit juice","24805187","combo","C128","","9","0","0","0","0","16","0","","","","","","","","1","zummXD2dvAtI.png","","","","","","","","4,2","","3,1","2,10","","","","0","2018-11-19 15:39:26","2018-11-25 04:35:51");
INSERT INTO products VALUES("28","ssaaa","sasas","standard","C128","","2","1","1","2","1","2","0","","","","","","","","1","zummXD2dvAtI.png","","","","","","","","","","","","","","","0","2018-11-20 10:14:12","2018-11-20 10:34:02");
INSERT INTO products VALUES("29","testcloth","16055884","standard","C128","","4","10","10","10","10","15","0","","","","","2018-11-29","","","1","zummXD2dvAtI.png","","","","","","","","","","","","","","","0","2018-11-29 09:49:25","2023-07-10 14:21:48");
INSERT INTO products VALUES("30","Polo Shirt","53467102","standard","C128","","12","1","1","1","50","100","22","10","","","","","","","2","PoloShirt.jpg","","","","","","","1","","","","","","","","1","2018-12-05 05:29:43","2023-07-10 15:44:13");
INSERT INTO products VALUES("31","Shoe","90471412","standard","C128","","12","1","1","1","250","300","23","10","","","","","","","2","Shoe.jpg","","","","","","","1","","","","","","","","1","2018-12-05 05:33:09","2023-05-06 16:06:46");
INSERT INTO products VALUES("32","Toothpaste","859875003032","standard","C128","","12","1","1","1","5","10","28","","","","","","","","2","1572759415477product-page-fresh-breath.jpg","","","","","","","","","","","","","","","1","2018-12-19 05:56:08","2023-07-10 15:21:27");
INSERT INTO products VALUES("33","tissue","8941161008066","standard","C128","","12","1","1","1","10","20","38","","","","","","","","2","zummXD2dvAtI.png","","","","","","","0","","","","","","","","1","2018-12-24 05:33:58","2021-07-15 18:25:45");
INSERT INTO products VALUES("48","T-shirt","93475396","standard","C128","","12","1","1","1","2","3","37","","","","","","","","2","1577165120189220px-Blue_Tshirt.jpg","","0","1","","0","","0","","","","","","","","1","2019-11-21 13:03:04","2023-07-09 10:34:45");
INSERT INTO products VALUES("60","testvar","32081679","standard","C128","","12","1","1","1","1","2","1","","","","","","","","2","zummXD2dvAtI.png","","","1","","","","0","","","","","","","","1","2020-05-18 22:44:14","2020-10-27 05:42:06");
INSERT INTO products VALUES("61","TV","32703342","standard","C128","4","2","1","1","1","3000","10000","16","","","","","","","2","2","zummXD2dvAtI.png","","","","","","","1","","","","","","","","1","2020-08-16 21:58:39","2021-07-15 18:25:45");
INSERT INTO products VALUES("62","Test variant","81145830","standard","C128","","4","1","1","1","1","2","3","","","","","","","","2","zummXD2dvAtI.png","","","1","","","","","","","","","","","","0","2020-09-27 12:08:27","2023-07-10 14:21:48");
INSERT INTO products VALUES("71","Napa","41930312","standard","C128","","19","1","1","1","1","2","27","","","","","","","","2","zummXD2dvAtI.png","","","","1","0","","0","","","","","","","","0","2021-03-07 14:12:53","2023-07-10 14:24:03");
INSERT INTO products VALUES("72","Denim Jeans","19243053","standard","C128","","12","1","1","1","2","5","26","10","","","","","","","2","16255551802975de6825f-5bbc-4d74-bce9-2aa4ab2311ce.jpg","","0","","","0","","1","","","","","","","","1","2021-05-31 14:05:38","2022-11-30 12:53:07");
INSERT INTO products VALUES("74","Medium T shirt and Jeans","10203123","combo","C128","","12","0","0","0","0","18","0","","","","","","","","2","zummXD2dvAtI.png","","","","","","","","48,72","2,","1,1","13,5","","","","1","2021-07-06 13:31:58","2021-07-06 13:31:58");
INSERT INTO products VALUES("75","Car Repair","16972383","service","C128","","20","0","0","0","0","200","0","","","","","","","","2","zummXD2dvAtI.png","","","","","","","","","","","","","","","1","2021-07-15 18:38:41","2021-07-15 18:38:41");
INSERT INTO products VALUES("76","dasdas","32781116","standard","C128","","4","1","1","1","1","2","0","","","","","","","","2","zummXD2dvAtI.png","","","","","","1","","","","","","","","","0","2021-11-09 16:57:02","2023-07-10 14:21:48");
INSERT INTO products VALUES("77","chukus","48547006","standard","C128","","2","1","1","1","1","2","0","","","","","","","","1","163886942338193136640_10156697499916330_2126367939505422336_n.jpg,1638869424762105705782_159243222380186_243702843374862028_o.jpg,202112070415034.jpg,202112070427354.jpg","","","","","0","","0","","","","","","","","0","2021-12-07 15:30:43","2021-12-07 18:47:06");
INSERT INTO products VALUES("79","Alpha Cheese","2000004","standard","C128","","9","7","7","7","12","25","12","","","","","","","","2","zummXD2dvAtI.png","","1","","","0","","0","","","","","","","","1","2022-06-01 11:37:28","2022-06-06 17:13:37");
INSERT INTO products VALUES("87","Cotton Punjabi","82490498","standard","C128","7","12","1","1","1","10","25","1","","","","","","","","2","zummXD2dvAtI.png","","0","1","","0","","0","","","","","","["Size","Color"]","["s,m,l,xl","red,blue,green"]","1","2022-08-03 11:25:19","2022-08-09 11:54:44");
INSERT INTO products VALUES("90","Iphone 14","17628500","standard","C128","5","2","1","1","1","500","1000","29","","","","","","","1","2","1662879338234iphone-14-pro-model-unselect-gallery-2-202209_GEO_US.jpg","","","1","","","","1","","","","","


NETWORK
Technology
GSM / CDMA / HSPA / EVDO / LTE / 5G






Dimensions
160.7 x 77.6 x 7.9 mm (6.33 x 3.06 x 0.31 in)


","["color"]","["red,black,blue"]","1","2022-09-11 12:56:55","2022-09-11 13:18:26");



CREATE TABLE `purchase_product_return` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `return_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_batch_id` int DEFAULT NULL,
  `variant_id` int DEFAULT NULL,
  `imei_number` text COLLATE utf8mb4_unicode_ci,
  `qty` double NOT NULL,
  `purchase_unit_id` int NOT NULL,
  `net_unit_cost` double NOT NULL,
  `discount` double NOT NULL,
  `tax_rate` double NOT NULL,
  `tax` double NOT NULL,
  `total` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO purchase_product_return VALUES("1","1","3","","","","1","1","200","0","0","0","200","","2019-12-07 17:19:03");
INSERT INTO purchase_product_return VALUES("23","12","1","","","","1","1","320","0","10","32","352","2022-07-27 16:04:44","2023-07-09 11:18:50");
INSERT INTO purchase_product_return VALUES("26","14","25","","","","1","1","500","0","10","50","550","2022-09-04 20:02:14","2022-09-04 20:02:14");
INSERT INTO purchase_product_return VALUES("27","15","5","","","","1","1","90","0","0","0","90","2023-03-27 13:33:37","2023-03-27 13:33:37");



CREATE TABLE `purchases` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `warehouse_id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `currency_id` int DEFAULT NULL,
  `exchange_rate` double DEFAULT NULL,
  `item` int NOT NULL,
  `total_qty` double NOT NULL,
  `total_discount` double NOT NULL,
  `total_tax` double NOT NULL,
  `total_cost` double NOT NULL,
  `order_tax_rate` double DEFAULT NULL,
  `order_tax` double DEFAULT NULL,
  `order_discount` double DEFAULT NULL,
  `shipping_cost` double DEFAULT NULL,
  `grand_total` double NOT NULL,
  `paid_amount` double NOT NULL,
  `status` int NOT NULL,
  `payment_status` int NOT NULL,
  `document` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=180 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO purchases VALUES("12","pr-20180808-051614","1","2","3","","","2","300","0","0","10200","0","0","0","0","10200","1475","1","1","","","2018-08-08 17:16:14","2023-07-09 16:54:47");
INSERT INTO purchases VALUES("13","pr-20180809-054723","1","2","3","","","4","410","0","7304.35","92600","10","9260","0","500","102360","300","1","1","","","2018-08-09 05:47:23","2018-08-30 09:07:18");
INSERT INTO purchases VALUES("14","pr-20180809-012348","1","1","1","","","5","400","0","4452.17","75300","10","7480","500","1000","83280","100","1","1","","","2018-08-09 13:23:48","2022-08-31 16:40:29");
INSERT INTO purchases VALUES("15","pr-20180903-100609","1","1","1","","","1","20","0","1600","17600","0","0","0","100","17700","0","1","1","","","2018-09-03 10:06:09","2018-10-08 04:11:24");
INSERT INTO purchases VALUES("16","pr-20180903-100714","1","2","3","","","1","20","0","1600","17600","0","0","0","150","17750","3350","1","1","","","2018-09-03 10:07:14","2018-10-07 06:57:36");
INSERT INTO purchases VALUES("18","pr-20181022-042625","1","1","1","","","1","50","0","0","50","0","0","","","50","0","1","1","","","2018-10-22 16:26:25","2018-10-22 16:26:25");
INSERT INTO purchases VALUES("19","pr-20181022-042652","1","2","3","","","1","50","0","0","50","0","0","","","50","0","1","1","","","2018-10-22 16:26:52","2018-10-22 16:26:52");
INSERT INTO purchases VALUES("20","pr-20181023-071420","11","1","1","","","1","15","0","750","8250","0","0","","","8250","0","1","1","","","2018-10-23 07:14:20","2018-10-23 07:14:20");
INSERT INTO purchases VALUES("21","pr-20181023-071441","11","2","3","","","1","15","0","750","8250","0","0","0","0","8250","0","1","1","","","2018-10-23 07:14:41","2018-10-23 07:14:58");
INSERT INTO purchases VALUES("22","pr-20181101-045903","1","1","1","","","1","5","0","400","4400","0","0","","","4400","0","1","1","","","2018-11-01 04:59:03","2018-11-01 04:59:03");
INSERT INTO purchases VALUES("23","pr-20181101-045928","1","2","3","","","1","5","0","400","4400","10","430","100","0","4730","500","1","1","","","2018-11-01 04:59:28","2018-12-04 07:01:34");
INSERT INTO purchases VALUES("24","pr-20181105-091819","1","2","1","","","2","20","0","1450","15950","0","0","","","15950","15950","1","2","","","2018-11-05 09:18:19","2018-11-05 09:21:27");
INSERT INTO purchases VALUES("25","pr-20181205-053429","1","1","1","","","2","30","0","0","4500","0","0","100","50","4450","4450","1","2","","","2018-12-05 05:34:29","2018-12-05 05:34:43");
INSERT INTO purchases VALUES("26","pr-20181205-053508","1","2","3","","","2","30","0","0","4500","0","0","","","4500","0","1","1","","","2018-12-05 05:35:08","2018-12-10 06:20:52");
INSERT INTO purchases VALUES("27","pr-20181219-055716","1","2","","","","1","10","0","0","10","0","0","0","0","10","10","1","2","","","2018-12-19 05:57:16","2018-12-20 06:34:39");
INSERT INTO purchases VALUES("33","pr-20181224-063840","1","1","","","","1","10","0","0","10","0","0","0","0","10","0","1","1","","","2018-12-24 06:38:40","2018-12-24 09:04:21");
INSERT INTO purchases VALUES("34","pr-20190103-070123","1","1","1","","","2","4","0","260","2860","0","0","","","2860","2860","1","2","","","2019-01-03 07:01:23","2019-01-29 10:03:24");
INSERT INTO purchases VALUES("35","pr-20190129-095448","9","1","1","","","2","4","0","0","600","0","0","","","600","100","1","1","","","2019-01-29 09:54:48","2019-02-07 13:06:40");
INSERT INTO purchases VALUES("36","pr-20190129-095558","9","2","1","","","2","5","0","0","650","0","0","","","650","650","1","2","","","2019-01-29 09:55:58","2019-01-29 10:03:02");
INSERT INTO purchases VALUES("37","pr-20190209-102138","1","2","1","","","3","18","0","580","6390","0","0","0","0","6390","6390","1","2","","","2019-02-09 10:21:38","2019-06-13 10:13:51");
INSERT INTO purchases VALUES("38","pr-20190209-102208","1","1","1","","","2","13","0","150","1660","0","0","0","0","1660","1660","1","2","","","2019-02-09 10:22:08","2019-02-09 10:49:40");
INSERT INTO purchases VALUES("39","pr-20190209-104413","1","1","1","","","2","3","10","63","885","10","88.5","","","973.5","973.5","1","2","","","2019-02-09 10:44:13","2019-02-09 10:49:59");
INSERT INTO purchases VALUES("40","pr-20190303-103917","1","1","1","","","1","10","0","0","100","0","0","","","100","100","1","2","","","2019-03-03 10:39:17","2019-03-03 10:40:46");
INSERT INTO purchases VALUES("41","pr-20190303-104358","1","2","","","","2","15","0","320","3570","0","0","","","3570","1000","1","1","","","2019-03-03 10:43:58","2019-04-13 17:02:41");
INSERT INTO purchases VALUES("42","pr-20190404-095757","1","1","3","","","2","2","0","0","300","0","0","0","0","300","300","1","2","","","2019-04-04 09:57:57","2019-04-13 19:50:08");
INSERT INTO purchases VALUES("43","pr-20190613-101600","1","2","1","","","2","6","0","390","4290","0","0","","","4290","4290","1","2","","","2019-06-13 10:16:00","2019-10-19 15:29:25");
INSERT INTO purchases VALUES("44","pr-20191019-033119","1","2","3","","","2","2","0","130","1430","0","0","","","1430","0","1","1","","","2019-10-19 15:31:19","2019-10-19 15:31:19");
INSERT INTO purchases VALUES("46","pr-20191103-113949","1","2","3","","","2","20","0","0","150","0","0","","50","200","200","1","2","","","2019-11-03 11:39:49","2019-11-03 11:42:22");
INSERT INTO purchases VALUES("47","pr-20191109-112510","1","1","","","","2","3","10","63","885","0","0","","66","951","0","1","1","","","2019-11-09 11:25:10","2019-11-09 11:25:10");
INSERT INTO purchases VALUES("48","pr-20191110-070221","1","2","","","","1","100","0","0","100","0","0","","40","140","0","1","1","","","2019-11-10 19:02:21","2019-11-10 19:02:21");
INSERT INTO purchases VALUES("49","pr-20191111-102155","1","2","1","","","1","10","0","800","8800","0","0","0","50","8850","7000","1","1","","","2019-11-11 10:21:55","2020-01-01 14:22:25");
INSERT INTO purchases VALUES("55","pr-20191127-102835","1","1","","","","2","2","0","0","4","0","0","","","4","4","1","2","","","2019-11-27 22:28:35","2020-01-01 14:21:52");
INSERT INTO purchases VALUES("56","pr-20191127-102906","1","2","","","","1","1","0","0","2","0","0","0","0","2","2","1","2","","","2019-11-27 22:29:06","2020-01-01 14:21:44");
INSERT INTO purchases VALUES("57","pr-20191204-110749","1","1","1","","","1","1","0","0","200","0","0","","20","220","220","1","2","","","2019-12-04 23:07:49","2019-12-04 23:11:24");
INSERT INTO purchases VALUES("58","pr-20191205-102110","1","1","1","","","2","2","0","0","4","0","0","0","0","4","4","1","2","","","2019-12-05 10:21:10","2020-01-01 14:21:37");
INSERT INTO purchases VALUES("59","pr-20191221-041851","1","1","","","","3","3","0","0","6","0","0","0","0","6","6","1","2","","","2019-12-21 16:18:51","2020-01-01 14:21:31");
INSERT INTO purchases VALUES("61","pr-20200101-010631","1","2","1","","","3","30","0","0","60","0","0","","","60","60","1","2","","","2020-01-01 13:06:31","2020-01-01 13:07:50");
INSERT INTO purchases VALUES("62","pr-20200101-022402","1","2","","","","1","3","0","150","1650","0","0","","","1650","1650","1","2","","","2020-01-01 14:24:02","2020-08-12 18:28:52");
INSERT INTO purchases VALUES("67","pr-20200204-110041","1","2","1","","","2","2","0","0","300","0","0","","","300","300","1","2","","","2020-02-04 23:00:41","2020-02-04 23:00:50");
INSERT INTO purchases VALUES("69","pr-20200302-115510","1","2","","","","1","50","0","0","50","0","0","","","50","50","1","2","","","2020-03-02 11:55:10","2020-03-02 11:58:20");
INSERT INTO purchases VALUES("70","pr-20200302-115603","1","1","1","","","1","50","0","0","50","0","0","","","50","50","1","2","","","2020-03-02 11:56:03","2020-03-02 11:58:11");
INSERT INTO purchases VALUES("71","pr-20200406-073512","1","2","3","","","3","11","0","150","3050","10","305","","","3355","2000","1","1","","","2020-04-06 19:35:12","2020-04-06 19:38:23");
INSERT INTO purchases VALUES("72","pr-20200816-100207","1","2","1","","","1","10","5000","3750","28750","0","0","","2000","30750","0","1","1","","","2020-08-16 22:02:07","2020-08-16 22:02:07");
INSERT INTO purchases VALUES("73","pr-20200927-125533","1","1","","","","1","2","0","0","2","0","0","0","0","2","0","1","1","","","2020-09-27 12:55:33","2020-09-27 15:07:44");
INSERT INTO purchases VALUES("74","pr-20201024-070201","1","2","1","","","2","15","0","5270","41470","0","0","0","0","41470","0","1","1","","","2020-10-24 07:02:01","2020-10-24 07:03:16");
INSERT INTO purchases VALUES("83","pr-20201027-045611","1","1","","","","1","2","0","0","2","0","0","","","2","0","1","1","","","2020-10-27 04:56:11","2020-10-27 04:56:11");
INSERT INTO purchases VALUES("84","pr-20201027-045658","1","1","","","","1","2","0","0","4","0","0","","","4","0","1","1","","","2020-10-27 04:56:58","2020-10-27 04:56:58");
INSERT INTO purchases VALUES("85","pr-20201102-092952","1","1","","","","1","1","0","32","352","0","0","","","352","0","1","1","","asasda
dasdasd","2020-11-02 09:29:52","2020-11-02 09:29:52");
INSERT INTO purchases VALUES("87","pr-20201102-093644","1","1","","","","1","1","0","32","352","0","0","0","0","352","0","1","1","","kjkljklj
lljkljkljkl
jhjhjh.","2020-11-02 09:36:44","2020-11-02 11:20:34");
INSERT INTO purchases VALUES("89","pr-20201116-030145","1","1","","","","2","2","0","32","353","0","0","","","353","0","1","1","","","2020-11-16 15:01:45","2020-11-16 15:01:45");
INSERT INTO purchases VALUES("90","pr-20201118-061543","1","2","","","","1","10","0","4500","34500","0","0","1000","100","33600","33000","1","1","","","2020-11-18 06:15:43","2020-11-18 06:20:36");
INSERT INTO purchases VALUES("98","pr-20210307-021405","1","1","","","","1","10","0","0","10","0","0","","","10","0","1","1","","","2021-03-07 14:14:05","2021-03-07 14:14:05");
INSERT INTO purchases VALUES("99","pr-20210307-021955","1","1","","","","1","5","0","0","5","0","0","0","0","5","0","1","1","","","2021-03-07 14:19:55","2021-03-09 15:43:48");
INSERT INTO purchases VALUES("100","pr-20210307-022116","1","2","","","","1","10","0","0","10","0","0","0","0","10","0","1","1","","","2021-03-07 14:21:16","2021-03-09 15:45:19");
INSERT INTO purchases VALUES("102","pr-20210309-023435","1","1","","","","1","5","0","0","5","0","0","0","0","5","0","1","1","","","2021-03-09 14:34:35","2021-03-11 09:47:53");
INSERT INTO purchases VALUES("105","pr-20210326-113914","1","1","","","","2","2","0","0","101","0","0","0","0","101","0","1","1","","","2021-03-26 11:39:14","2021-04-09 21:53:48");
INSERT INTO purchases VALUES("106","pr-20210524-071854","1","1","","","","1","1","0","32","352","0","0","","","352","0","1","1","","","2021-05-24 19:18:54","2021-05-24 19:18:54");
INSERT INTO purchases VALUES("113","pr-20210530-014119","1","1","","","","2","2","0","0","201","0","0","0","0","201","0","1","1","","","2021-05-30 13:41:19","2021-05-30 13:41:19");
INSERT INTO purchases VALUES("114","pr-20210601-032127","1","1","","","","1","3","0","0","4.5","0","0","","","4.5","0","1","1","","","2021-06-01 15:21:27","2021-06-01 15:21:27");
INSERT INTO purchases VALUES("115","pr-20210618-082836","1","1","","","","2","2","0","0","101","0","0","","","101","101","1","2","","","2021-06-18 20:28:36","2021-06-18 20:28:57");
INSERT INTO purchases VALUES("116","pr-20210619-104822","1","1","","","","2","2","0","0","101","0","0","","","101","101","1","2","","","2021-06-19 10:48:22","2021-06-19 10:49:03");
INSERT INTO purchases VALUES("117","pr-20210706-011042","1","1","","","","1","5","0","0","10","0","0","","","10","0","1","1","","","2021-07-06 13:10:42","2021-07-06 13:10:42");
INSERT INTO purchases VALUES("118","pr-20210706-011107","1","2","","","","1","5","0","0","10","0","0","","","10","0","1","1","","","2021-07-06 13:11:07","2021-07-06 13:11:07");
INSERT INTO purchases VALUES("119","pr-20210921-032813","1","1","","","","1","1","0","32","352","0","0","","","352","0","3","1","","","2021-09-21 15:28:13","2021-09-21 15:28:13");
INSERT INTO purchases VALUES("121","pr-20210921-034739","1","1","","","","2","2","0","0","101","0","0","0","0","101","0","1","1","","","2021-09-21 15:47:39","2021-09-21 15:49:38");
INSERT INTO purchases VALUES("123","pr-20211003-010453","1","1","","","","2","2","0","80","980","0","0","","","980","0","1","1","","","2021-10-03 13:04:53","2021-10-03 13:04:53");
INSERT INTO purchases VALUES("124","pr-20211003-010621","1","1","","","","2","2","0","80","882","0","0","","","882","0","1","1","","","2021-10-03 13:06:21","2021-10-03 13:06:21");
INSERT INTO purchases VALUES("128","pr-20211020-014234","1","1","","","","1","10","0","800","8800","0","0","0","0","8800","0","2","1","","","2021-10-20 13:42:34","2021-10-20 13:43:40");
INSERT INTO purchases VALUES("129","pr-20211020-014709","1","1","","","","1","1","0","80","880","0","0","","","880","0","1","1","","","2021-10-20 13:47:09","2021-10-20 13:47:09");
INSERT INTO purchases VALUES("131","pr-20211129-012605","1","1","","","","2","5","0","64","1304","0","0","0","0","1304","0","1","1","","","2021-11-29 13:26:05","2021-11-29 13:29:18");
INSERT INTO purchases VALUES("132","pr-20211207-065825","1","1","1","","","3","3","0","80","982","0","0","0","0","982","0","1","1","","","2021-12-07 18:58:25","2021-12-07 19:01:11");
INSERT INTO purchases VALUES("133","pr-20220220-093102","1","2","","","","1","2","0","0","200","0","0","","","200","0","1","1","","","2022-02-10 00:00:00","2022-02-20 21:31:02");
INSERT INTO purchases VALUES("134","P1001","1","2","","","","1","1","0","32","352","0","0","0","0","352","0","1","1","","","2022-03-11 00:00:00","2022-03-11 22:01:52");
INSERT INTO purchases VALUES("135","pr-20220311-100215","1","1","","","","1","1","0","0","200","0","0","","","200","0","1","1","","","2022-03-11 22:02:15","2022-03-11 22:02:15");
INSERT INTO purchases VALUES("136","pr-20220327-110727","1","1","","","","2","2","0","57.04","544","0","0","","","544","0","1","1","","","2022-03-27 11:07:27","2022-03-27 11:07:27");
INSERT INTO purchases VALUES("144","pr-20220331-020549","1","2","","","","3","30","0","510.4","5600","","0","","","5600","0","1","1","","","2022-03-31 14:05:49","2022-03-31 14:05:49");
INSERT INTO purchases VALUES("145","pr-20220424-110038","1","2","","","","2","11","0","50","570","0","0","","","570","0","1","1","","","2022-04-24 11:00:38","2022-04-24 11:00:38");
INSERT INTO purchases VALUES("146","pr-20220516-112100","1","2","1","","","1","10","0","500","5500","0","0","","","5500","5500","1","2","","","2022-05-16 11:21:00","2022-05-16 11:23:15");
INSERT INTO purchases VALUES("147","pr-20220531-122357","1","1","1","","","2","20","0","820","9020","0","0","","","9020","0","1","1","","","2022-05-31 12:23:57","2022-05-31 12:23:57");
INSERT INTO purchases VALUES("148","pr-20220601-124119","1","2","","","","1","12","0","0","144","0","0","0","0","144","0","1","1","","","2022-06-01 00:00:00","2022-06-05 12:26:35");
INSERT INTO purchases VALUES("150","pr-20220607-043846","1","1","","","","1","1","0","52.17","400","0","0","","","400","0","1","1","","","2022-06-07 16:38:46","2022-06-07 16:38:46");
INSERT INTO purchases VALUES("153","pr-20220717-105212","1","1","","","","2","2","0","32","452","0","0","","","452","0","1","1","","","2022-07-13 00:00:00","2022-07-17 10:52:12");
INSERT INTO purchases VALUES("154","pr-20220717-105257","1","2","","","","2","3","0","32","552","0","0","","","552","0","1","1","","","2022-07-14 00:00:00","2022-07-17 10:52:57");
INSERT INTO purchases VALUES("155","pr-20220717-105344","1","1","","","","1","1","0","0","100","0","0","","","100","0","1","1","","","2022-07-12 00:00:00","2022-07-17 10:53:44");
INSERT INTO purchases VALUES("158","pr-20220808-091357","1","1","","","","5","5","0","32","396","0","0","","","396","0","1","1","","","2022-08-08 21:13:57","2022-08-08 21:13:57");
INSERT INTO purchases VALUES("161","pr-20220904-080109","1","2","1","","","3","3","0","82","1102","0","0","","","1102","0","1","1","","","2022-09-04 20:01:09","2022-09-04 20:01:09");
INSERT INTO purchases VALUES("162","pr-20220911-010441","1","1","1","","","3","30","0","1550","17050","0","0","50","100","17100","11100","1","1","","","2022-09-11 13:04:41","2022-10-18 14:09:39");
INSERT INTO purchases VALUES("163","pr-20221120-122035","1","1","1","","","1","1","0","29.09","320","0","0","","","320","0","1","1","","","2022-11-20 12:20:35","2022-11-20 12:20:35");
INSERT INTO purchases VALUES("164","pr-20221124-043710","1","2","","","","1","1","0","0","190","0","0","","","190","90","1","1","","","2022-11-24 16:37:10","2022-12-11 14:54:29");
INSERT INTO purchases VALUES("165","pr-20221211-025454","1","1","1","","","1","1","0","0","200","0","0","","","200","0","1","1","","","2022-12-11 14:54:54","2022-12-11 14:54:54");
INSERT INTO purchases VALUES("171","pr-20230327-121054","1","1","","2","1","1","1","0","26.18","288","0","0","0","0","288","288","1","2","","","2023-03-27 12:10:54","2023-04-05 13:40:16");
INSERT INTO purchases VALUES("172","pr-20230327-121236","1","1","","2","0.9","2","4","0","52.36","756","0","0","0","0","756","0","1","1","","","2023-03-27 00:00:00","2023-03-27 12:21:35");
INSERT INTO purchases VALUES("173","pr-20230405-014043","1","2","","1","1","1","2","0","0","400","0","0","0","0","400","400","1","2","","","2023-04-05 13:40:43","2023-04-05 13:40:53");
INSERT INTO purchases VALUES("174","pr-20230429-045330","1","1","","","","2","3","10","57.27","822","0","0","","","822","600","1","1","","","2023-04-29 16:53:30","2023-06-04 15:50:04");
INSERT INTO purchases VALUES("178","pr-20230604-035051","1","2","","1","1","2","2","0","29.09","520","0","0","0","0","520","0","1","1","","","2023-06-04 15:50:51","2023-06-04 15:50:51");
INSERT INTO purchases VALUES("179","pr-20230709-103445","1","1","1","","","3","3","0","0","112","0","0","500","1000","612","0","1","1","","Test","2023-07-09 10:34:45","2023-07-09 10:34:45");



CREATE TABLE `quotations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `biller_id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `customer_id` int NOT NULL,
  `warehouse_id` int NOT NULL,
  `item` int NOT NULL,
  `total_qty` double NOT NULL,
  `total_discount` double NOT NULL,
  `total_tax` double NOT NULL,
  `total_price` double NOT NULL,
  `order_tax_rate` double DEFAULT NULL,
  `order_tax` double DEFAULT NULL,
  `order_discount` double DEFAULT NULL,
  `shipping_cost` double DEFAULT NULL,
  `grand_total` double NOT NULL,
  `quotation_status` int NOT NULL,
  `document` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO quotations VALUES("1","qr-20180809-055250","1","1","3","3","1","3","57","35","767.73","5930","10","583","100","500","6913","2","","first quotation...","2018-08-09 05:52:50","2018-09-04 09:32:16");
INSERT INTO quotations VALUES("2","qr-20180904-040257","1","1","","1","1","2","3.5","0","0","76","0","0","1.6","2.7","77.1","1","","","2018-09-04 04:02:57","2018-09-21 13:05:57");
INSERT INTO quotations VALUES("3","qr-20181023-061249","9","1","3","11","1","2","2","0","40","453","0","0","0","0","453","2","","","2018-10-23 06:12:49","2019-12-21 12:41:36");
INSERT INTO quotations VALUES("11","qr-20201024-090814","1","1","","1","1","1","2","0","3000","23000","0","0","0","0","23000","1","","","2020-10-24 09:08:14","2020-10-24 09:28:35");
INSERT INTO quotations VALUES("12","qr-20201210-053401","1","1","1","19","2","2","15","0","600","9100","0","0","100","500","9500","1","","","2020-12-10 05:34:01","2020-12-10 05:34:01");
INSERT INTO quotations VALUES("13","qr-20210112-125803","1","1","","11","1","3","12","0","155.65","1660","0","0","0","0","1660","1","","","2021-01-12 12:58:03","2021-01-12 12:59:14");
INSERT INTO quotations VALUES("15","qr-20210529-105356","1","1","","11","2","2","2","0","40","442","0","0","0","0","442","1","","","2021-05-29 10:53:56","2021-05-30 14:04:07");
INSERT INTO quotations VALUES("16","qr-20220219-122055","1","1","","19","1","1","1","0","0","120","0","0","","","120","1","","","2022-02-19 12:20:55","2022-02-19 12:20:55");
INSERT INTO quotations VALUES("17","qr-20220219-124103","1","1","","11","1","1","1","0","40","440","0","0","10","","430","1","","","2022-02-19 12:41:03","2022-02-19 12:41:03");
INSERT INTO quotations VALUES("18","qr-20220219-124638","1","1","","11","1","1","1","0","40","440","0","0","10","","430","1","","","2022-02-19 12:46:38","2022-02-19 12:46:38");
INSERT INTO quotations VALUES("19","qr-20220308-040521","1","1","","1","1","3","3","0","0","566","0","0","0","0","566","1","","asfsdf
fsdfsdf
sfsdfsdfs","2022-03-08 16:05:21","2022-03-13 11:38:58");
INSERT INTO quotations VALUES("22","qr-20230709-103615","1","2","1","2","1","1","1","0","0","108","10","-39.2","500","1000","568.8","2","20230709103615.png","Test","2023-07-09 10:36:15","2023-07-09 10:36:15");



CREATE TABLE `return_purchases` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `warehouse_id` int NOT NULL,
  `user_id` int NOT NULL,
  `purchase_id` int DEFAULT NULL,
  `account_id` int NOT NULL,
  `currency_id` int DEFAULT NULL,
  `exchange_rate` double DEFAULT NULL,
  `item` int NOT NULL,
  `total_qty` double NOT NULL,
  `total_discount` double NOT NULL,
  `total_tax` double NOT NULL,
  `total_cost` double NOT NULL,
  `order_tax_rate` double DEFAULT NULL,
  `order_tax` double DEFAULT NULL,
  `grand_total` double NOT NULL,
  `document` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `return_note` text COLLATE utf8mb4_unicode_ci,
  `staff_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO return_purchases VALUES("1","prr-20190101-090759","3","1","1","","1","","","1","1","0","0","200","0","0","200","","","","2019-01-01 09:07:59","2019-12-07 17:19:03");
INSERT INTO return_purchases VALUES("12","prr-20220727-040444","","2","1","","1","","","1","1","0","32","352","0","0","352","","test","test","2022-07-27 16:04:44","2023-07-09 11:18:01");
INSERT INTO return_purchases VALUES("14","prr-20220904-080213","1","2","1","161","1","","","1","1","0","50","550","0","0","550","","","","2022-09-04 20:02:13","2022-09-04 20:02:13");
INSERT INTO return_purchases VALUES("15","prr-20230327-013337","","1","1","172","1","2","0.9","1","1","0","0","90","0","0","90","","","","2023-03-27 13:33:37","2023-03-27 13:33:37");



CREATE TABLE `returns` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `sale_id` int DEFAULT NULL,
  `cash_register_id` int DEFAULT NULL,
  `customer_id` int NOT NULL,
  `warehouse_id` int NOT NULL,
  `biller_id` int NOT NULL,
  `account_id` int NOT NULL,
  `currency_id` int DEFAULT NULL,
  `exchange_rate` double DEFAULT NULL,
  `item` int NOT NULL,
  `total_qty` double NOT NULL,
  `total_discount` double NOT NULL,
  `total_tax` double NOT NULL,
  `total_price` double NOT NULL,
  `order_tax_rate` double DEFAULT NULL,
  `order_tax` double DEFAULT NULL,
  `grand_total` double NOT NULL,
  `document` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `return_note` text COLLATE utf8mb4_unicode_ci,
  `staff_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO returns VALUES("2","rr-20180809-055834","1","","","1","1","1","1","","","1","20","0","0","40","10","4","44","","","","2018-08-09 05:58:34","2018-08-09 05:58:34");
INSERT INTO returns VALUES("3","rr-20180828-045527","1","","","1","2","1","1","","","1","2","0","0","44","0","0","44","","","","2018-08-28 04:55:27","2018-09-20 17:03:47");
INSERT INTO returns VALUES("5","rr-20181007-082129","1","","","11","2","2","1","","","1","1","0","0","250","0","0","250","","","","2018-10-07 08:21:29","2018-12-26 04:16:08");
INSERT INTO returns VALUES("6","rr-20190101-090630","9","","","1","1","1","1","","","1","1","0","40","440","0","0","440","","","","2019-01-01 09:06:30","2019-01-01 09:06:30");
INSERT INTO returns VALUES("13","rr-20200816-102502","1","","","1","2","2","1","","","1","1","0","1500","11500","0","0","11500","","","","2020-08-16 22:25:02","2020-08-16 22:25:02");
INSERT INTO returns VALUES("14","rr-20201013-053954","9","","1","1","1","1","1","","","1","1","0","40","440","0","0","440","","","","2020-10-13 17:39:54","2020-10-13 17:39:54");
INSERT INTO returns VALUES("18","rr-20201118-070218","1","","4","1","2","1","1","","","1","1","0","1500","11500","0","0","11500","","","","2020-11-18 07:02:18","2020-11-18 07:02:18");
INSERT INTO returns VALUES("19","rr-20201210-064025","1","","3","19","1","1","1","","","1","1","0","0","250","0","0","250","","","","2020-12-10 06:40:25","2020-12-10 06:40:25");
INSERT INTO returns VALUES("33","rr-20220531-123340","1","","","11","1","1","1","","","1","1","0","100","1100","0","0","1100","","","","2022-05-31 12:33:40","2022-05-31 12:33:40");
INSERT INTO returns VALUES("34","rr-20220531-124817","1","","","11","1","1","1","","","1","1","0","40","440","0","0","440","","","","2022-05-31 12:48:17","2022-05-31 12:48:17");
INSERT INTO returns VALUES("36","rr-20220614-032048","1","400","6","11","2","1","1","","","1","1","0","100","1100","0","0","1100","","","","2022-06-14 15:20:48","2022-06-14 15:20:48");
INSERT INTO returns VALUES("38","rr-20220904-011908","1","412","","11","1","5","1","","","1","1","0","0","250","0","0","250","","","","2022-09-04 13:19:08","2022-09-04 13:19:08");
INSERT INTO returns VALUES("39","rr-20230323-125050","1","457","7","1","1","1","1","2","0.9","2","2","0","0","130","0","0","130","20230709110628.png","Test","Test","2023-03-23 12:50:50","2023-07-09 11:06:29");



CREATE TABLE `reward_point_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `per_point_amount` double NOT NULL,
  `minimum_amount` double NOT NULL,
  `duration` int DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO reward_point_settings VALUES("1","300","1000","1","Year","1","2021-06-08 21:40:15","2021-06-27 11:20:55");



CREATE TABLE `role_has_permissions` (
  `permission_id` int unsigned NOT NULL,
  `role_id` int unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO role_has_permissions VALUES("4","1");
INSERT INTO role_has_permissions VALUES("5","1");
INSERT INTO role_has_permissions VALUES("6","1");
INSERT INTO role_has_permissions VALUES("7","1");
INSERT INTO role_has_permissions VALUES("8","1");
INSERT INTO role_has_permissions VALUES("9","1");
INSERT INTO role_has_permissions VALUES("10","1");
INSERT INTO role_has_permissions VALUES("11","1");
INSERT INTO role_has_permissions VALUES("12","1");
INSERT INTO role_has_permissions VALUES("13","1");
INSERT INTO role_has_permissions VALUES("14","1");
INSERT INTO role_has_permissions VALUES("15","1");
INSERT INTO role_has_permissions VALUES("16","1");
INSERT INTO role_has_permissions VALUES("17","1");
INSERT INTO role_has_permissions VALUES("18","1");
INSERT INTO role_has_permissions VALUES("19","1");
INSERT INTO role_has_permissions VALUES("20","1");
INSERT INTO role_has_permissions VALUES("21","1");
INSERT INTO role_has_permissions VALUES("22","1");
INSERT INTO role_has_permissions VALUES("23","1");
INSERT INTO role_has_permissions VALUES("24","1");
INSERT INTO role_has_permissions VALUES("25","1");
INSERT INTO role_has_permissions VALUES("26","1");
INSERT INTO role_has_permissions VALUES("27","1");
INSERT INTO role_has_permissions VALUES("28","1");
INSERT INTO role_has_permissions VALUES("29","1");
INSERT INTO role_has_permissions VALUES("30","1");
INSERT INTO role_has_permissions VALUES("31","1");
INSERT INTO role_has_permissions VALUES("32","1");
INSERT INTO role_has_permissions VALUES("33","1");
INSERT INTO role_has_permissions VALUES("34","1");
INSERT INTO role_has_permissions VALUES("35","1");
INSERT INTO role_has_permissions VALUES("36","1");
INSERT INTO role_has_permissions VALUES("37","1");
INSERT INTO role_has_permissions VALUES("38","1");
INSERT INTO role_has_permissions VALUES("39","1");
INSERT INTO role_has_permissions VALUES("40","1");
INSERT INTO role_has_permissions VALUES("41","1");
INSERT INTO role_has_permissions VALUES("42","1");
INSERT INTO role_has_permissions VALUES("43","1");
INSERT INTO role_has_permissions VALUES("44","1");
INSERT INTO role_has_permissions VALUES("45","1");
INSERT INTO role_has_permissions VALUES("46","1");
INSERT INTO role_has_permissions VALUES("47","1");
INSERT INTO role_has_permissions VALUES("48","1");
INSERT INTO role_has_permissions VALUES("49","1");
INSERT INTO role_has_permissions VALUES("50","1");
INSERT INTO role_has_permissions VALUES("51","1");
INSERT INTO role_has_permissions VALUES("52","1");
INSERT INTO role_has_permissions VALUES("53","1");
INSERT INTO role_has_permissions VALUES("54","1");
INSERT INTO role_has_permissions VALUES("55","1");
INSERT INTO role_has_permissions VALUES("56","1");
INSERT INTO role_has_permissions VALUES("57","1");
INSERT INTO role_has_permissions VALUES("58","1");
INSERT INTO role_has_permissions VALUES("59","1");
INSERT INTO role_has_permissions VALUES("60","1");
INSERT INTO role_has_permissions VALUES("61","1");
INSERT INTO role_has_permissions VALUES("62","1");
INSERT INTO role_has_permissions VALUES("63","1");
INSERT INTO role_has_permissions VALUES("64","1");
INSERT INTO role_has_permissions VALUES("65","1");
INSERT INTO role_has_permissions VALUES("66","1");
INSERT INTO role_has_permissions VALUES("67","1");
INSERT INTO role_has_permissions VALUES("68","1");
INSERT INTO role_has_permissions VALUES("69","1");
INSERT INTO role_has_permissions VALUES("70","1");
INSERT INTO role_has_permissions VALUES("71","1");
INSERT INTO role_has_permissions VALUES("72","1");
INSERT INTO role_has_permissions VALUES("73","1");
INSERT INTO role_has_permissions VALUES("74","1");
INSERT INTO role_has_permissions VALUES("75","1");
INSERT INTO role_has_permissions VALUES("76","1");
INSERT INTO role_has_permissions VALUES("77","1");
INSERT INTO role_has_permissions VALUES("78","1");
INSERT INTO role_has_permissions VALUES("79","1");
INSERT INTO role_has_permissions VALUES("80","1");
INSERT INTO role_has_permissions VALUES("81","1");
INSERT INTO role_has_permissions VALUES("82","1");
INSERT INTO role_has_permissions VALUES("83","1");
INSERT INTO role_has_permissions VALUES("84","1");
INSERT INTO role_has_permissions VALUES("85","1");
INSERT INTO role_has_permissions VALUES("86","1");
INSERT INTO role_has_permissions VALUES("87","1");
INSERT INTO role_has_permissions VALUES("88","1");
INSERT INTO role_has_permissions VALUES("89","1");
INSERT INTO role_has_permissions VALUES("90","1");
INSERT INTO role_has_permissions VALUES("91","1");
INSERT INTO role_has_permissions VALUES("92","1");
INSERT INTO role_has_permissions VALUES("93","1");
INSERT INTO role_has_permissions VALUES("94","1");
INSERT INTO role_has_permissions VALUES("95","1");
INSERT INTO role_has_permissions VALUES("96","1");
INSERT INTO role_has_permissions VALUES("97","1");
INSERT INTO role_has_permissions VALUES("98","1");
INSERT INTO role_has_permissions VALUES("99","1");
INSERT INTO role_has_permissions VALUES("100","1");
INSERT INTO role_has_permissions VALUES("101","1");
INSERT INTO role_has_permissions VALUES("102","1");
INSERT INTO role_has_permissions VALUES("103","1");
INSERT INTO role_has_permissions VALUES("104","1");
INSERT INTO role_has_permissions VALUES("105","1");
INSERT INTO role_has_permissions VALUES("106","1");
INSERT INTO role_has_permissions VALUES("107","1");
INSERT INTO role_has_permissions VALUES("108","1");
INSERT INTO role_has_permissions VALUES("109","1");
INSERT INTO role_has_permissions VALUES("110","1");
INSERT INTO role_has_permissions VALUES("111","1");
INSERT INTO role_has_permissions VALUES("112","1");
INSERT INTO role_has_permissions VALUES("113","1");
INSERT INTO role_has_permissions VALUES("114","1");
INSERT INTO role_has_permissions VALUES("115","1");
INSERT INTO role_has_permissions VALUES("116","1");
INSERT INTO role_has_permissions VALUES("117","1");
INSERT INTO role_has_permissions VALUES("118","1");
INSERT INTO role_has_permissions VALUES("119","1");
INSERT INTO role_has_permissions VALUES("120","1");
INSERT INTO role_has_permissions VALUES("121","1");
INSERT INTO role_has_permissions VALUES("122","1");
INSERT INTO role_has_permissions VALUES("123","1");
INSERT INTO role_has_permissions VALUES("124","1");
INSERT INTO role_has_permissions VALUES("125","1");
INSERT INTO role_has_permissions VALUES("126","1");
INSERT INTO role_has_permissions VALUES("4","2");
INSERT INTO role_has_permissions VALUES("5","2");
INSERT INTO role_has_permissions VALUES("6","2");
INSERT INTO role_has_permissions VALUES("7","2");
INSERT INTO role_has_permissions VALUES("8","2");
INSERT INTO role_has_permissions VALUES("9","2");
INSERT INTO role_has_permissions VALUES("10","2");
INSERT INTO role_has_permissions VALUES("11","2");
INSERT INTO role_has_permissions VALUES("12","2");
INSERT INTO role_has_permissions VALUES("13","2");
INSERT INTO role_has_permissions VALUES("14","2");
INSERT INTO role_has_permissions VALUES("15","2");
INSERT INTO role_has_permissions VALUES("16","2");
INSERT INTO role_has_permissions VALUES("17","2");
INSERT INTO role_has_permissions VALUES("18","2");
INSERT INTO role_has_permissions VALUES("19","2");
INSERT INTO role_has_permissions VALUES("20","2");
INSERT INTO role_has_permissions VALUES("21","2");
INSERT INTO role_has_permissions VALUES("22","2");
INSERT INTO role_has_permissions VALUES("23","2");
INSERT INTO role_has_permissions VALUES("24","2");
INSERT INTO role_has_permissions VALUES("25","2");
INSERT INTO role_has_permissions VALUES("26","2");
INSERT INTO role_has_permissions VALUES("27","2");
INSERT INTO role_has_permissions VALUES("28","2");
INSERT INTO role_has_permissions VALUES("29","2");
INSERT INTO role_has_permissions VALUES("30","2");
INSERT INTO role_has_permissions VALUES("31","2");
INSERT INTO role_has_permissions VALUES("32","2");
INSERT INTO role_has_permissions VALUES("33","2");
INSERT INTO role_has_permissions VALUES("34","2");
INSERT INTO role_has_permissions VALUES("35","2");
INSERT INTO role_has_permissions VALUES("36","2");
INSERT INTO role_has_permissions VALUES("37","2");
INSERT INTO role_has_permissions VALUES("38","2");
INSERT INTO role_has_permissions VALUES("39","2");
INSERT INTO role_has_permissions VALUES("40","2");
INSERT INTO role_has_permissions VALUES("41","2");
INSERT INTO role_has_permissions VALUES("42","2");
INSERT INTO role_has_permissions VALUES("43","2");
INSERT INTO role_has_permissions VALUES("44","2");
INSERT INTO role_has_permissions VALUES("45","2");
INSERT INTO role_has_permissions VALUES("46","2");
INSERT INTO role_has_permissions VALUES("47","2");
INSERT INTO role_has_permissions VALUES("48","2");
INSERT INTO role_has_permissions VALUES("49","2");
INSERT INTO role_has_permissions VALUES("50","2");
INSERT INTO role_has_permissions VALUES("51","2");
INSERT INTO role_has_permissions VALUES("52","2");
INSERT INTO role_has_permissions VALUES("53","2");
INSERT INTO role_has_permissions VALUES("54","2");
INSERT INTO role_has_permissions VALUES("55","2");
INSERT INTO role_has_permissions VALUES("56","2");
INSERT INTO role_has_permissions VALUES("57","2");
INSERT INTO role_has_permissions VALUES("58","2");
INSERT INTO role_has_permissions VALUES("59","2");
INSERT INTO role_has_permissions VALUES("60","2");
INSERT INTO role_has_permissions VALUES("61","2");
INSERT INTO role_has_permissions VALUES("62","2");
INSERT INTO role_has_permissions VALUES("63","2");
INSERT INTO role_has_permissions VALUES("64","2");
INSERT INTO role_has_permissions VALUES("65","2");
INSERT INTO role_has_permissions VALUES("66","2");
INSERT INTO role_has_permissions VALUES("67","2");
INSERT INTO role_has_permissions VALUES("68","2");
INSERT INTO role_has_permissions VALUES("69","2");
INSERT INTO role_has_permissions VALUES("70","2");
INSERT INTO role_has_permissions VALUES("71","2");
INSERT INTO role_has_permissions VALUES("72","2");
INSERT INTO role_has_permissions VALUES("73","2");
INSERT INTO role_has_permissions VALUES("74","2");
INSERT INTO role_has_permissions VALUES("75","2");
INSERT INTO role_has_permissions VALUES("76","2");
INSERT INTO role_has_permissions VALUES("77","2");
INSERT INTO role_has_permissions VALUES("78","2");
INSERT INTO role_has_permissions VALUES("79","2");
INSERT INTO role_has_permissions VALUES("80","2");
INSERT INTO role_has_permissions VALUES("81","2");
INSERT INTO role_has_permissions VALUES("82","2");
INSERT INTO role_has_permissions VALUES("83","2");
INSERT INTO role_has_permissions VALUES("84","2");
INSERT INTO role_has_permissions VALUES("85","2");
INSERT INTO role_has_permissions VALUES("86","2");
INSERT INTO role_has_permissions VALUES("87","2");
INSERT INTO role_has_permissions VALUES("88","2");
INSERT INTO role_has_permissions VALUES("89","2");
INSERT INTO role_has_permissions VALUES("90","2");
INSERT INTO role_has_permissions VALUES("91","2");
INSERT INTO role_has_permissions VALUES("92","2");
INSERT INTO role_has_permissions VALUES("93","2");
INSERT INTO role_has_permissions VALUES("94","2");
INSERT INTO role_has_permissions VALUES("95","2");
INSERT INTO role_has_permissions VALUES("96","2");
INSERT INTO role_has_permissions VALUES("97","2");
INSERT INTO role_has_permissions VALUES("98","2");
INSERT INTO role_has_permissions VALUES("99","2");
INSERT INTO role_has_permissions VALUES("100","2");
INSERT INTO role_has_permissions VALUES("101","2");
INSERT INTO role_has_permissions VALUES("102","2");
INSERT INTO role_has_permissions VALUES("103","2");
INSERT INTO role_has_permissions VALUES("104","2");
INSERT INTO role_has_permissions VALUES("105","2");
INSERT INTO role_has_permissions VALUES("4","4");
INSERT INTO role_has_permissions VALUES("6","4");
INSERT INTO role_has_permissions VALUES("7","4");
INSERT INTO role_has_permissions VALUES("8","4");
INSERT INTO role_has_permissions VALUES("9","4");
INSERT INTO role_has_permissions VALUES("12","4");
INSERT INTO role_has_permissions VALUES("13","4");
INSERT INTO role_has_permissions VALUES("20","4");
INSERT INTO role_has_permissions VALUES("21","4");
INSERT INTO role_has_permissions VALUES("22","4");
INSERT INTO role_has_permissions VALUES("24","4");
INSERT INTO role_has_permissions VALUES("25","4");
INSERT INTO role_has_permissions VALUES("28","4");
INSERT INTO role_has_permissions VALUES("29","4");
INSERT INTO role_has_permissions VALUES("55","4");
INSERT INTO role_has_permissions VALUES("56","4");
INSERT INTO role_has_permissions VALUES("57","4");
INSERT INTO role_has_permissions VALUES("63","4");
INSERT INTO role_has_permissions VALUES("64","4");
INSERT INTO role_has_permissions VALUES("106","4");
INSERT INTO role_has_permissions VALUES("4","6");
INSERT INTO role_has_permissions VALUES("5","6");
INSERT INTO role_has_permissions VALUES("6","6");
INSERT INTO role_has_permissions VALUES("7","6");
INSERT INTO role_has_permissions VALUES("8","6");
INSERT INTO role_has_permissions VALUES("9","6");
INSERT INTO role_has_permissions VALUES("10","6");
INSERT INTO role_has_permissions VALUES("11","6");
INSERT INTO role_has_permissions VALUES("12","6");
INSERT INTO role_has_permissions VALUES("13","6");
INSERT INTO role_has_permissions VALUES("14","6");
INSERT INTO role_has_permissions VALUES("15","6");
INSERT INTO role_has_permissions VALUES("16","6");
INSERT INTO role_has_permissions VALUES("17","6");
INSERT INTO role_has_permissions VALUES("18","6");
INSERT INTO role_has_permissions VALUES("19","6");
INSERT INTO role_has_permissions VALUES("20","6");
INSERT INTO role_has_permissions VALUES("21","6");
INSERT INTO role_has_permissions VALUES("22","6");
INSERT INTO role_has_permissions VALUES("23","6");
INSERT INTO role_has_permissions VALUES("24","6");
INSERT INTO role_has_permissions VALUES("25","6");
INSERT INTO role_has_permissions VALUES("26","6");
INSERT INTO role_has_permissions VALUES("27","6");
INSERT INTO role_has_permissions VALUES("28","6");
INSERT INTO role_has_permissions VALUES("29","6");
INSERT INTO role_has_permissions VALUES("30","6");
INSERT INTO role_has_permissions VALUES("31","6");
INSERT INTO role_has_permissions VALUES("32","6");
INSERT INTO role_has_permissions VALUES("33","6");
INSERT INTO role_has_permissions VALUES("34","6");
INSERT INTO role_has_permissions VALUES("35","6");
INSERT INTO role_has_permissions VALUES("36","6");
INSERT INTO role_has_permissions VALUES("37","6");
INSERT INTO role_has_permissions VALUES("38","6");
INSERT INTO role_has_permissions VALUES("39","6");
INSERT INTO role_has_permissions VALUES("40","6");
INSERT INTO role_has_permissions VALUES("41","6");
INSERT INTO role_has_permissions VALUES("42","6");
INSERT INTO role_has_permissions VALUES("43","6");
INSERT INTO role_has_permissions VALUES("44","6");
INSERT INTO role_has_permissions VALUES("45","6");
INSERT INTO role_has_permissions VALUES("46","6");
INSERT INTO role_has_permissions VALUES("47","6");
INSERT INTO role_has_permissions VALUES("48","6");
INSERT INTO role_has_permissions VALUES("49","6");
INSERT INTO role_has_permissions VALUES("50","6");
INSERT INTO role_has_permissions VALUES("51","6");
INSERT INTO role_has_permissions VALUES("52","6");
INSERT INTO role_has_permissions VALUES("53","6");
INSERT INTO role_has_permissions VALUES("54","6");
INSERT INTO role_has_permissions VALUES("55","6");
INSERT INTO role_has_permissions VALUES("56","6");
INSERT INTO role_has_permissions VALUES("57","6");
INSERT INTO role_has_permissions VALUES("58","6");
INSERT INTO role_has_permissions VALUES("59","6");
INSERT INTO role_has_permissions VALUES("60","6");
INSERT INTO role_has_permissions VALUES("61","6");
INSERT INTO role_has_permissions VALUES("62","6");
INSERT INTO role_has_permissions VALUES("63","6");
INSERT INTO role_has_permissions VALUES("64","6");
INSERT INTO role_has_permissions VALUES("65","6");
INSERT INTO role_has_permissions VALUES("66","6");
INSERT INTO role_has_permissions VALUES("67","6");
INSERT INTO role_has_permissions VALUES("68","6");
INSERT INTO role_has_permissions VALUES("69","6");
INSERT INTO role_has_permissions VALUES("70","6");
INSERT INTO role_has_permissions VALUES("71","6");
INSERT INTO role_has_permissions VALUES("72","6");
INSERT INTO role_has_permissions VALUES("73","6");
INSERT INTO role_has_permissions VALUES("74","6");
INSERT INTO role_has_permissions VALUES("75","6");
INSERT INTO role_has_permissions VALUES("76","6");
INSERT INTO role_has_permissions VALUES("77","6");
INSERT INTO role_has_permissions VALUES("78","6");
INSERT INTO role_has_permissions VALUES("79","6");
INSERT INTO role_has_permissions VALUES("80","6");
INSERT INTO role_has_permissions VALUES("81","6");
INSERT INTO role_has_permissions VALUES("82","6");
INSERT INTO role_has_permissions VALUES("83","6");
INSERT INTO role_has_permissions VALUES("84","6");
INSERT INTO role_has_permissions VALUES("85","6");
INSERT INTO role_has_permissions VALUES("86","6");
INSERT INTO role_has_permissions VALUES("87","6");
INSERT INTO role_has_permissions VALUES("88","6");
INSERT INTO role_has_permissions VALUES("89","6");
INSERT INTO role_has_permissions VALUES("90","6");
INSERT INTO role_has_permissions VALUES("91","6");
INSERT INTO role_has_permissions VALUES("92","6");
INSERT INTO role_has_permissions VALUES("93","6");
INSERT INTO role_has_permissions VALUES("94","6");
INSERT INTO role_has_permissions VALUES("95","6");
INSERT INTO role_has_permissions VALUES("96","6");
INSERT INTO role_has_permissions VALUES("97","6");
INSERT INTO role_has_permissions VALUES("98","6");
INSERT INTO role_has_permissions VALUES("99","6");
INSERT INTO role_has_permissions VALUES("100","6");
INSERT INTO role_has_permissions VALUES("101","6");
INSERT INTO role_has_permissions VALUES("102","6");
INSERT INTO role_has_permissions VALUES("103","6");
INSERT INTO role_has_permissions VALUES("104","6");
INSERT INTO role_has_permissions VALUES("105","6");
INSERT INTO role_has_permissions VALUES("106","6");
INSERT INTO role_has_permissions VALUES("107","6");
INSERT INTO role_has_permissions VALUES("108","6");
INSERT INTO role_has_permissions VALUES("109","6");
INSERT INTO role_has_permissions VALUES("110","6");
INSERT INTO role_has_permissions VALUES("111","6");
INSERT INTO role_has_permissions VALUES("112","6");
INSERT INTO role_has_permissions VALUES("113","6");
INSERT INTO role_has_permissions VALUES("114","6");
INSERT INTO role_has_permissions VALUES("115","6");
INSERT INTO role_has_permissions VALUES("116","6");
INSERT INTO role_has_permissions VALUES("117","6");
INSERT INTO role_has_permissions VALUES("118","6");
INSERT INTO role_has_permissions VALUES("119","6");
INSERT INTO role_has_permissions VALUES("120","6");
INSERT INTO role_has_permissions VALUES("121","6");
INSERT INTO role_has_permissions VALUES("122","6");
INSERT INTO role_has_permissions VALUES("123","6");
INSERT INTO role_has_permissions VALUES("124","6");
INSERT INTO role_has_permissions VALUES("125","6");
INSERT INTO role_has_permissions VALUES("126","6");



CREATE TABLE `roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO roles VALUES("1","Admin","admin can access all data...","web","1","2018-06-02 05:46:44","2018-06-03 05:13:05");
INSERT INTO roles VALUES("2","Owner","Staff of shop","web","1","2018-10-22 08:38:13","2022-02-01 19:13:30");
INSERT INTO roles VALUES("4","staff","staff has specific acess...","web","1","2018-06-02 06:05:27","2022-02-01 19:13:04");
INSERT INTO roles VALUES("5","Customer","","web","1","2020-11-05 12:43:16","2020-11-15 06:24:15");
INSERT INTO roles VALUES("6","Employee","Test","web","1","2023-07-11 11:09:00","2023-07-11 11:09:00");



CREATE TABLE `sales` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `cash_register_id` int DEFAULT NULL,
  `table_id` int DEFAULT NULL,
  `queue` int DEFAULT NULL,
  `customer_id` int NOT NULL,
  `warehouse_id` int NOT NULL,
  `biller_id` int DEFAULT NULL,
  `item` int NOT NULL,
  `total_qty` double NOT NULL,
  `total_discount` double NOT NULL,
  `total_tax` double NOT NULL,
  `total_price` double NOT NULL,
  `grand_total` double NOT NULL,
  `currency_id` int DEFAULT NULL,
  `exchange_rate` double DEFAULT NULL,
  `order_tax_rate` double DEFAULT NULL,
  `order_tax` double DEFAULT NULL,
  `order_discount_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_discount_value` double DEFAULT NULL,
  `order_discount` double DEFAULT NULL,
  `coupon_id` int DEFAULT NULL,
  `coupon_discount` double DEFAULT NULL,
  `shipping_cost` double DEFAULT NULL,
  `sale_status` int NOT NULL,
  `payment_status` int NOT NULL,
  `document` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_amount` double DEFAULT NULL,
  `sale_note` text COLLATE utf8mb4_unicode_ci,
  `staff_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `test` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Test',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=478 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO sales VALUES("1","sr-20180808-043622","1","","","","1","1","1","2","3","10","15.65","350","380","","","10","30","","","50","","","50","1","4","","380","ukgjkgjkgkj","gjkgjkgkujg","2018-08-08 16:36:22","2022-06-11 18:42:06","Test");
INSERT INTO sales VALUES("2","sr-20180809-055453","1","","","","3","1","1","3","63","0","469.3","5103","5503","","","0","0","","","100","","","500","1","2","","2200","","","2018-08-09 05:54:53","2018-08-09 05:56:35","Test");
INSERT INTO sales VALUES("3","posr-20180809-063214","1","","","","2","2","2","3","26","0","0","897","897","","","0","0","","","","","","","1","4","","897","","","2018-08-09 06:32:14","2018-08-09 06:32:14","Test");
INSERT INTO sales VALUES("4","sr-20180825-034836","1","","","","1","1","1","1","2","0","80","880","880","","","0","0","","","","","","","1","4","","880","","","2018-08-25 03:48:36","2022-06-11 18:42:06","Test");
INSERT INTO sales VALUES("6","sr-20180826-094836","1","","","","2","1","2","1","1","0","0","18.9","20","","","0","0","","","0","","","1.1","1","4","","20","","","2018-08-26 09:48:36","2018-08-26 11:48:05","Test");
INSERT INTO sales VALUES("7","sr-20180827-073545","1","","","","1","1","1","1","2","0","80","880","880","","","0","0","","","","","","","1","4","","880","","","2018-08-27 07:35:45","2018-08-27 07:35:45","Test");
INSERT INTO sales VALUES("8","posr-20180902-053954","1","","","","1","1","2","1","2","0","288","3168","3529.8","","","10","311.8","","","50","","","100","1","4","","3529.8","good customer","good customer","2018-09-02 05:39:54","2018-09-02 05:39:54","Test");
INSERT INTO sales VALUES("9","posr-20180903-033314","1","","","","1","2","1","1","10","0","0","20","20","","","0","0","","","","","","","1","4","","20","","","2018-09-03 03:33:14","2018-09-03 03:33:14","Test");
INSERT INTO sales VALUES("10","posr-20180903-050138","1","","","","11","2","1","1","1","0","0","250","250","","","0","0","","","","","","","1","4","","250","","","2018-09-03 05:01:38","2022-06-14 11:02:45","Test");
INSERT INTO sales VALUES("11","posr-20180903-100821","1","","","","11","2","1","1","5","0","500","5500","5500","","","0","0","","","","","","","1","4","","5500","","","2018-09-03 10:08:21","2018-09-03 10:08:21","Test");
INSERT INTO sales VALUES("12","sr-20180903-101026","1","","","","3","1","5","1","10","0","1050","11550","11550","","","0","0","","","","","","","1","2","","0","","","2018-09-03 10:10:26","2018-09-22 08:55:14","Test");
INSERT INTO sales VALUES("29","sr-20180909-093841","1","","","","1","1","1","1","1","0","0","120","132","","","10","12","","","","","","","1","4","","132","","","2018-09-09 09:38:41","2022-06-11 18:46:55","Test");
INSERT INTO sales VALUES("30","posr-20180910-045706","1","","","","11","2","1","1","1","0","40","440","440","","","0","0","","","0","","","0","1","4","","440","","","2018-09-10 04:57:06","2022-06-14 11:02:45","Test");
INSERT INTO sales VALUES("31","posr-20180926-092059","1","","","","11","2","1","2","2","0","55.65","560","560","","","0","0","","","0","","","0","1","4","","560","","","2018-09-26 09:20:59","2018-09-26 09:21:25","Test");
INSERT INTO sales VALUES("32","posr-20181004-095547","1","","","","11","2","1","1","1","0","40","440","440","","","0","0","","","","","","","1","2","","130","","","2018-10-04 09:55:47","2022-06-14 11:02:45","Test");
INSERT INTO sales VALUES("33","posr-20181004-100022","1","","","","11","2","1","1","1","0","40","440","440","","","0","0","","","","","","","1","2","","","","","2018-10-04 10:00:22","2018-10-04 10:00:22","Test");
INSERT INTO sales VALUES("37","sr-20181007-064605","1","","","","1","1","1","1","1","0","0","250","250","","","0","0","","","","","","","1","4","","250","","","2018-10-07 06:46:05","2022-06-11 18:50:42","Test");
INSERT INTO sales VALUES("38","posr-20181007-064719","1","","","","11","2","1","1","1","0","40","440","440","","","0","0","","","","","","","1","2","","0","","","2018-10-07 06:47:19","2018-10-07 09:17:02","Test");
INSERT INTO sales VALUES("40","posr-20181007-071312","1","","","","11","2","1","1","1","0","40","440","440","","","0","0","","","","","","","1","2","","0","","","2018-10-07 07:13:12","2018-10-07 09:17:39","Test");
INSERT INTO sales VALUES("41","posr-20181010-041621","1","","","","1","2","1","2","2","0","40","461","461","","","0","0","","","","","","","1","4","","461","","","2018-10-10 04:16:21","2018-10-10 04:16:21","Test");
INSERT INTO sales VALUES("42","posr-20181010-053450","1","","","","1","2","1","1","1","0","40","440","440","","","0","0","","","","","","","1","4","","440","","","2018-10-10 05:34:50","2018-10-10 05:34:50","Test");
INSERT INTO sales VALUES("43","sr-20181016-033434","1","","","","1","1","1","1","1","0","40","440","440","","","0","0","","","","","","","1","4","","440","sss
sss
s","","2018-10-16 03:34:34","2022-06-11 18:50:42","Test");
INSERT INTO sales VALUES("55","posr-20181021-065334","1","","","","11","2","1","1","1","0","0","250","250","","","0","0","","","","","","","1","4","","250","","","2018-10-21 06:53:34","2018-10-21 06:53:34","Test");
INSERT INTO sales VALUES("57","posr-20181021-082612","1","","","","11","2","1","2","3","0","40","482","575.2","","","10","43.2","","","50","","","100","1","4","","575.2","","","2018-10-21 08:26:12","2018-10-21 08:26:12","Test");
INSERT INTO sales VALUES("58","posr-20181022-032723","1","","","","11","2","1","2","2","0","100","1220","1220","","","0","0","","","","","","","1","4","","1220","","","2018-10-22 15:27:23","2018-10-22 15:27:23","Test");
INSERT INTO sales VALUES("73","posr-20181023-071543","11","","","","11","1","5","2","5","0","500","5500","5500","","","0","0","","","","","","","1","4","","5500","","","2018-10-23 07:15:43","2018-10-23 07:15:43","Test");
INSERT INTO sales VALUES("74","posr-20181023-071644","1","","","","11","2","1","3","3","0","200","2320","2320","","","0","0","","","","","","","1","4","","2320","","","2018-10-23 07:16:44","2018-10-23 07:16:44","Test");
INSERT INTO sales VALUES("75","posr-20181101-050027","1","","","","11","2","1","5","12","0","626.96","6980","7678","","","10","698","","","","","","","1","4","","7678","","","2018-11-01 05:00:27","2018-11-01 05:00:27","Test");
INSERT INTO sales VALUES("76","posr-20181101-050126","1","","","","11","2","1","3","6","0","100","1434","1424","","","0","0","","","10","","","0","1","4","","1424","","","2018-11-01 05:01:26","2018-11-08 09:44:51","Test");
INSERT INTO sales VALUES("79","posr-20181105-091516","1","","","","11","2","1","7","52","0","1069.57","14454","14454","","","0","0","","","","","","","1","4","","14454","","","2018-11-05 09:15:16","2018-11-05 09:15:16","Test");
INSERT INTO sales VALUES("80","posr-20181105-091958","1","","","","11","2","1","3","8","0","191.3","2500","2500","","","0","0","","","","","","","1","4","","2500","","","2018-11-05 09:19:58","2018-11-05 09:19:58","Test");
INSERT INTO sales VALUES("86","posr-20181105-095948","1","","","","11","2","1","1","1","0","100","1100","1100","","","0","0","","","","","","","1","4","","1100","","","2018-11-05 09:59:48","2018-11-05 09:59:48","Test");
INSERT INTO sales VALUES("88","posr-20181105-100258","1","","","","11","2","1","1","1","0","100","1100","1100","","","0","0","","","","","","","1","4","","1100","","","2018-11-05 10:02:58","2018-11-05 10:02:58","Test");
INSERT INTO sales VALUES("94","posr-20181126-020534","1","","","","11","2","1","1","1","0","15.65","120","120","","","0","0","","","","","","","1","4","","120","","","2018-11-26 14:05:34","2018-11-26 14:05:34","Test");
INSERT INTO sales VALUES("95","posr-20181127-093608","1","","","","11","2","1","1","3","0","0","360","360","","","0","0","","","","","","","1","2","","","","","2018-11-27 09:36:08","2018-11-27 09:36:08","Test");
INSERT INTO sales VALUES("96","posr-20181128-071509","1","","","","11","2","1","1","1","0","15.65","120","132","","","10","12","","","","","","","1","4","","132","","","2018-11-28 07:15:09","2018-11-28 07:15:09","Test");
INSERT INTO sales VALUES("97","posr-20181201-060518","1","","","","11","2","1","2","3","0","31.3","262","262","","","0","0","","","","","","","1","2","","200","","","2018-12-01 06:05:18","2018-12-04 06:21:05","Test");
INSERT INTO sales VALUES("98","posr-20181205-053558","1","","","","11","2","1","2","4","0","0","800","800","","","0","0","","","","","","","1","4","","800","","","2018-12-05 05:35:58","2018-12-05 05:35:58","Test");
INSERT INTO sales VALUES("99","posr-20181205-053719","1","","","","11","1","1","2","4","0","0","800","800","","","0","0","","","","","","","1","4","","800","","","2018-12-05 05:37:19","2018-12-05 05:37:19","Test");
INSERT INTO sales VALUES("101","posr-20181208-062026","1","","","","11","2","1","1","1","0","0","100","100","","","0","0","","","","","","","1","4","","100","","","2018-12-08 06:20:26","2018-12-08 06:20:26","Test");
INSERT INTO sales VALUES("103","posr-20181224-045832","1","","","","11","2","1","1","1","0","15.65","120","120","","","0","0","","","","","","","1","4","","120","","","2018-12-24 04:58:32","2018-12-24 04:58:32","Test");
INSERT INTO sales VALUES("104","sr-20181224-091527","1","","","","1","1","2","2","6","0","0","2508","2518","","","0","0","","","0","","","10","1","2","","118","","","2018-12-24 09:15:27","2022-06-11 18:52:05","Test");
INSERT INTO sales VALUES("105","posr-20190101-054538","1","","","","1","2","1","1","1","0","0","21","21","","","0","0","","","","","","","1","4","","21","","","2019-01-01 05:45:38","2019-01-01 05:45:38","Test");
INSERT INTO sales VALUES("106","posr-20190101-091040","1","","","","11","2","1","3","5","0","15.65","860","860","","","0","0","","","","","","","1","4","","860","","","2019-01-01 09:10:40","2019-01-01 09:10:40","Test");
INSERT INTO sales VALUES("107","posr-20190103-065626","1","","","","11","2","1","6","10","0","395.65","5040","5040","","","0","0","","","","","","","1","4","","5040","","","2019-01-03 06:56:26","2019-01-03 06:56:26","Test");
INSERT INTO sales VALUES("108","posr-20190120-035824","1","","","","11","2","1","1","1","0","15.65","120","120","","","0","0","","","","","","","1","4","","120","","","2019-01-20 15:58:24","2019-01-20 15:58:24","Test");
INSERT INTO sales VALUES("109","posr-20190129-101443","9","","","","11","1","5","2","2","0","40","540","540","","","0","0","","","","","","","1","4","","540","","","2019-01-29 10:14:43","2019-01-29 10:14:43","Test");
INSERT INTO sales VALUES("110","posr-20190129-115041","9","","","","11","1","5","2","3","0","100","1700","1700","","","0","0","","","","","","","1","4","","1700","","","2019-01-29 11:50:41","2019-01-29 11:50:41","Test");
INSERT INTO sales VALUES("111","posr-20190131-110839","9","","","","11","1","5","2","2","0","0","271","271","","","0","0","","","","","","","1","4","","271","","","2019-01-31 11:08:39","2019-01-31 11:08:39","Test");
INSERT INTO sales VALUES("112","posr-20190202-104045","1","","","","11","2","1","1","1","0","40","440","440","","","0","0","","","","","","","1","4","","440","","","2019-02-02 10:40:45","2019-02-02 10:40:45","Test");
INSERT INTO sales VALUES("113","posr-20190202-114117","1","","","","11","2","1","2","2","0","0","350","350","","","0","0","","","","","","","1","4","","350","","","2019-02-02 11:41:17","2019-02-02 11:41:17","Test");
INSERT INTO sales VALUES("114","posr-20190205-030445","1","","","","11","2","1","1","1","0","40","440","440","","","0","0","","","","","","","1","4","","440","","","2019-02-05 15:04:45","2019-02-05 15:04:45","Test");
INSERT INTO sales VALUES("118","posr-20190207-111542","1","","","","11","2","1","1","1","0","40","440","440","","","0","0","","","","","","","3","2","","","","","2019-02-07 11:15:42","2019-02-07 11:15:42","Test");
INSERT INTO sales VALUES("120","sr-20190207-114036","1","","","","1","1","2","1","1","0","40","440","440","","","0","0","","","","","","","1","2","","50","","","2019-02-07 11:40:36","2019-02-07 13:09:15","Test");
INSERT INTO sales VALUES("121","posr-20190209-104814","1","","","","11","2","1","7","17","0","55.65","1272","1272","","","0","0","","","","","","","1","4","","1272","","","2019-02-09 10:48:14","2019-02-09 10:48:14","Test");
INSERT INTO sales VALUES("123","posr-20190219-023214","1","","","","11","2","1","1","1","0","40","440","440","","","0","0","","","","","","","1","4","","440","","","2019-02-19 14:32:14","2019-02-19 14:32:14","Test");
INSERT INTO sales VALUES("127","posr-20190303-104010","1","","","","11","2","1","3","3","0","200","2500","2500","","","0","0","","","","","","","1","4","","2500","","","2019-03-03 10:40:10","2019-03-03 10:40:10","Test");
INSERT INTO sales VALUES("128","posr-20190404-095555","1","","","","11","2","1","2","2","0","55.65","560","560","","","0","0","","","","","","","1","4","","560","","","2019-04-04 09:55:55","2019-04-04 09:55:55","Test");
INSERT INTO sales VALUES("129","posr-20190404-095937","1","","","","11","2","1","1","2","0","0","240","240","","","0","0","","","0","","","0","1","2","","120","","","2019-04-04 09:59:37","2019-04-11 10:50:16","Test");
INSERT INTO sales VALUES("130","posr-20190421-122124","1","","","","11","2","1","3","3","0","58.78","586","586","","","0","0","","","","","","","1","4","","586","","","2019-04-21 12:21:24","2019-04-21 12:21:24","Test");
INSERT INTO sales VALUES("131","posr-20190528-103229","1","","","","11","2","1","4","4","0","240","2890","2890","","","0","0","","","","","","","1","4","","2890","","","2019-05-28 10:32:29","2019-05-28 10:32:29","Test");
INSERT INTO sales VALUES("132","posr-20190613-101637","1","","","","11","2","1","3","3","0","40","840","840","","","0","0","","","","","","","1","4","","840","","","2019-06-13 10:16:37","2019-06-13 10:16:37","Test");
INSERT INTO sales VALUES("133","posr-20190613-101751","1","","","","11","2","1","3","4","0","200","2700","2700","","","0","0","","","","","","","1","4","","2700","","","2019-06-13 10:17:51","2019-06-13 10:17:51","Test");
INSERT INTO sales VALUES("134","posr-20191019-033028","1","","","","11","2","1","4","4","0","240","2940","2940","","","0","0","","","","","","","1","4","","2940","","","2019-10-19 15:30:28","2019-10-19 15:30:28","Test");
INSERT INTO sales VALUES("138","sr-20191031-122937","1","","","","1","1","1","1","1","0","0","120","120","","","0","0","","","","","","","1","1","","","","","2019-10-31 12:29:37","2019-10-31 12:29:37","Test");
INSERT INTO sales VALUES("139","posr-20191103-114044","1","","","","11","2","1","2","102","0","37.57","488","488","","","0","0","","","","","","","1","4","","488","","","2019-11-03 11:40:44","2019-11-03 11:40:44","Test");
INSERT INTO sales VALUES("144","posr-20191109-074131","1","","","","11","2","1","2","2","0","100","1220","1220","","","0","0","","","0","","","0","1","4","","1220","","","2019-11-09 19:41:31","2019-11-09 19:41:31","Test");
INSERT INTO sales VALUES("147","posr-20191111-104008","1","","","","11","2","1","3","12","0","200","2220","2220","","","0","0","","","","","","","1","4","","2220","","","2019-11-11 10:40:08","2019-11-11 10:40:08","Test");
INSERT INTO sales VALUES("172","posr-20191203-034631","1","","","","11","2","1","1","1","0","40","440","440","","","0","0","","","","","","","1","2","","","","","2019-12-03 15:46:31","2019-12-03 15:46:31","Test");
INSERT INTO sales VALUES("173","sr-20191204-111940","1","","","","2","1","1","2","2","0","36","621","621","","","0","0","","","","","","","1","4","","621","","","2019-12-04 23:19:40","2019-12-05 09:27:12","Test");
INSERT INTO sales VALUES("187","posr-20191222-104058","1","","","","11","2","1","1","2","0","37.57","288","288","","","0","0","","","","","","","1","4","","288","","","2019-12-22 10:40:58","2019-12-22 10:40:58","Test");
INSERT INTO sales VALUES("190","posr-20191223-125946","1","","","","11","2","1","1","1","0","100","1100","1100","","","0","0","","","","","","","1","4","","1100","","","2019-12-23 12:59:46","2019-12-23 12:59:46","Test");
INSERT INTO sales VALUES("193","posr-20200101-022028","1","","","","11","2","1","1","1","0","100","1100","1100","","","0","0","","","","","","","1","4","","1100","","","2020-01-01 14:20:28","2020-01-01 14:20:28","Test");
INSERT INTO sales VALUES("194","posr-20200102-043947","1","","","","11","2","1","2","3","0","81.57","892","892","","","0","0","","","","","","","1","4","","892","","","2020-01-02 16:39:47","2020-01-02 16:39:47","Test");
INSERT INTO sales VALUES("201","posr-20200203-035256","1","","","","11","2","1","1","1","0","0","120","120","","","0","0","","","","","","","1","4","","120","","","2020-02-03 15:52:56","2020-02-03 15:52:56","Test");
INSERT INTO sales VALUES("202","posr-20200204-105853","1","","","","11","2","1","2","2","0","100","1400","1400","","","0","0","","","","","","","1","4","","1400","","","2020-02-04 22:58:53","2020-02-04 22:58:53","Test");
INSERT INTO sales VALUES("203","posr-20200302-115414","1","","","","11","2","1","2","2","0","0","350","350","","","0","0","","","","","","","1","4","","350","","","2020-03-02 11:54:14","2020-03-02 11:54:14","Test");
INSERT INTO sales VALUES("204","posr-20200302-115741","1","","","","11","2","1","1","20","0","0","40","40","","","0","0","","","","","","","1","4","","40","","","2020-03-02 11:57:41","2020-03-02 11:57:41","Test");
INSERT INTO sales VALUES("205","posr-20200311-044641","1","","","","11","2","1","1","1","0","40","440","352","","","0","0","","","","1","88","","1","4","","352","","","2020-03-11 16:46:42","2020-03-11 16:46:42","Test");
INSERT INTO sales VALUES("206","sr-20200311-045230","1","","","","1","1","1","1","1","0","0","120","120","","","0","0","","","0","","","0","1","2","","","","","2020-03-11 16:52:30","2020-03-11 16:54:04","Test");
INSERT INTO sales VALUES("207","posr-20200406-074024","1","","","","11","2","1","3","4","0","18.78","644","644","","","0","0","","","0","","","0","1","4","","644","","","2020-04-06 19:40:24","2020-04-06 19:42:01","Test");
INSERT INTO sales VALUES("208","posr-20200506-105950","1","","","","11","2","1","2","2","0","140","1540","1540","","","0","0","","","","","","","1","4","","1540","","","2020-05-06 22:59:50","2020-05-06 22:59:50","Test");
INSERT INTO sales VALUES("209","posr-20200609-124248","1","","","","11","2","1","2","2","0","100","1220","1220","","","0","0","","","","","","","1","4","","1220","","","2020-06-09 12:42:48","2020-06-09 12:42:48","Test");
INSERT INTO sales VALUES("212","posr-20200703-063914","1","","","","11","2","1","3","3","0","235","2585","2585","","","0","0","","","","","","","1","4","","2585","","","2020-07-03 18:39:14","2020-07-03 18:39:14","Test");
INSERT INTO sales VALUES("213","posr-20200712-095153","1","","","","11","2","1","1","1","0","0","13","13","","","0","0","","","","","","","1","4","","13","","","2020-07-12 21:51:53","2020-07-12 21:51:53","Test");
INSERT INTO sales VALUES("217","posr-20200727-083808","1","","","","11","2","1","1","1","0","35","385","385","","","0","0","","","","","","","1","4","","385","","","2020-07-27 20:38:08","2020-07-27 20:38:08","Test");
INSERT INTO sales VALUES("218","posr-20200727-084024","1","","","","11","2","1","1","1","0","35","385","385","","","0","0","","","","","","","1","4","","385","","","2020-07-27 20:40:24","2020-07-27 20:40:24","Test");
INSERT INTO sales VALUES("219","posr-20200727-084644","1","","","","11","2","1","1","1","0","35","385","385","","","0","0","","","","","","","1","4","","385","","","2020-07-27 20:46:45","2020-07-27 20:46:45","Test");
INSERT INTO sales VALUES("220","posr-20200812-062804","1","","","","11","2","1","3","6","0","0","760","760","","","0","0","","","","","","","1","4","","760","","","2020-08-12 18:28:04","2020-08-12 18:28:04","Test");
INSERT INTO sales VALUES("221","posr-20200812-063035","1","","","","11","2","1","1","1","0","100","1100","1100","","","0","0","","","","","","","1","4","","1100","","","2020-08-12 18:30:35","2020-08-12 18:30:35","Test");
INSERT INTO sales VALUES("222","posr-20200816-100424","1","","","","11","2","1","1","2","0","3000","23000","23000","","","0","0","","","","","","","1","4","","23000","","","2020-08-16 22:04:24","2020-08-16 22:04:24","Test");
INSERT INTO sales VALUES("223","posr-20200816-100523","1","","","","11","2","1","1","1","0","40","440","440","","","0","0","","","","","","","1","4","","440","","","2020-08-16 22:05:23","2020-08-16 22:07:35","Test");
INSERT INTO sales VALUES("224","sr-20200826-080139","1","","","","1","1","1","1","3","30","117","1287","1287","","","0","0","","","","","","","1","1","","","","","2020-08-26 20:01:39","2020-08-26 20:01:39","Test");
INSERT INTO sales VALUES("230","posr-20201017-092852","9","2","","","11","1","5","1","1","0","0","250","250","","","0","0","","","","","","","1","2","","350","","","2020-10-17 21:28:52","2020-10-18 10:51:38","Test");
INSERT INTO sales VALUES("231","sr-20201018-111333","9","2","","","1","1","5","1","1","0","40","440","440","","","0","0","","","","","","","1","2","","250","","","2020-10-18 11:13:33","2020-10-18 11:17:24","Test");
INSERT INTO sales VALUES("232","posr-20201022-013014","9","2","","","11","1","5","1","1","0","0","100","100","","","0","0","","","","","","","1","4","","100","","","2020-10-22 13:30:14","2020-10-22 13:30:14","Test");
INSERT INTO sales VALUES("233","posr-20201022-015604","1","3","","","11","1","1","1","1","0","0","250","250","","","0","0","","","","","","","1","4","","250","","","2020-10-22 13:56:04","2020-10-22 13:56:04","Test");
INSERT INTO sales VALUES("234","posr-20201024-070506","1","4","","","11","2","1","1","1","0","1500","11500","11500","","","0","0","","","","","","","1","4","","11500","","","2020-10-24 07:05:06","2020-10-24 07:05:06","Test");
INSERT INTO sales VALUES("235","posr-20201024-070751","1","4","","","11","2","1","1","1","0","0","250","250","","","0","0","","","","","","","1","4","","250","","","2020-10-24 07:07:51","2020-10-24 07:07:51","Test");
INSERT INTO sales VALUES("237","posr-20201024-034601","1","4","","","11","2","1","3","9","0","7900","61900","61900","","","0","0","","","","","","","1","4","","61900","","","2020-10-24 15:46:01","2020-10-24 15:46:01","Test");
INSERT INTO sales VALUES("239","posr-20201027-054002","1","3","","","11","1","1","1","1","0","0","2","2","","","0","0","","","","","","","1","4","","2","","","2020-10-27 05:40:02","2020-10-27 05:40:02","Test");
INSERT INTO sales VALUES("240","posr-20201027-054206","1","3","","","11","1","1","1","2","0","0","6","6","","","0","0","","","","","","","1","4","","6","","","2020-10-27 05:42:06","2020-10-27 05:42:06","Test");
INSERT INTO sales VALUES("241","posr-20201027-063200","1","4","","","11","2","1","1","1","0","0","250","250","","","0","0","","","","","","","1","4","","250","","","2020-10-27 06:32:00","2020-10-27 06:32:00","Test");
INSERT INTO sales VALUES("242","posr-20201029-073030","1","4","","","11","2","1","1","1","0","0","250","250","","","0","0","","","","","","","1","4","","250","","","2020-10-29 07:30:30","2020-10-29 07:30:30","Test");
INSERT INTO sales VALUES("243","posr-20201101-072112","1","4","","","11","2","1","1","1","0","0","250","250","","","0","0","","","","","","","1","4","","250","","","2020-11-01 07:21:12","2020-11-01 07:21:12","Test");
INSERT INTO sales VALUES("245","posr-20201101-074223","1","3","","","11","1","5","2","3","0","80","1130","1130","","","","0","","","0","","","0","1","4","","1130","","","2020-11-01 07:42:23","2020-11-01 07:42:23","Test");
INSERT INTO sales VALUES("246","posr-20201101-075017","1","4","","","11","2","1","1","1","0","40","440","440","","","0","0","","","0","","","0","1","4","","440","ssss
ssas","hhhh
kkkk","2020-11-01 07:50:17","2020-11-02 16:39:17","Test");
INSERT INTO sales VALUES("250","posr-20201106-013041","1","4","","","11","2","1","1","1","0","34.4","378.4","378.4","","","0","0","","","","","","","1","4","","378.4","","","2020-11-06 13:30:41","2020-11-06 13:30:41","Test");
INSERT INTO sales VALUES("251","posr-20201109-011524","1","4","","","19","2","1","2","2","0","34.4","610.6","610.6","","","0","0","","","","","","","1","2","","500","","","2020-11-09 13:15:24","2020-11-09 13:15:24","Test");
INSERT INTO sales VALUES("252","posr-20201111-055858","1","3","","","11","1","1","1","1","0","0","229.5","229.5","","","0","0","","","","","","","1","4","","229.5","","","2020-11-11 05:58:58","2020-11-11 05:58:58","Test");
INSERT INTO sales VALUES("253","posr-20201114-064736","1","4","","","11","2","1","3","3","0","1275","10242.5","10242.5","","","0","0","","","","","","","1","4","","10242.5","","","2020-11-14 06:47:36","2020-11-14 06:47:36","Test");
INSERT INTO sales VALUES("256","posr-20201117-064748","1","4","","","11","2","1","2","2","0","65","715","715","","","0","0","","","","","","","1","4","","715","","","2020-11-17 06:47:48","2020-11-17 06:47:48","Test");
INSERT INTO sales VALUES("257","posr-20201117-070919","1","4","","","11","2","1","1","1","0","0","250","250","","","0","0","","","","","","","1","4","","250","","","2020-11-17 07:09:19","2020-11-17 07:09:19","Test");
INSERT INTO sales VALUES("258","posr-20201118-065239","1","4","","","11","2","1","1","3","0","4500","34500","27200","","","0","0","","","600","1","6800","100","1","4","","27200","","","2020-11-18 06:52:39","2020-11-18 06:52:39","Test");
INSERT INTO sales VALUES("262","1111","1","3","","","1","1","1","1","2","0","3000","23000","23000","","","0","0","","","0","","","0","1","1","","","","","2020-11-29 05:21:30","2020-11-29 05:21:30","Test");
INSERT INTO sales VALUES("263","posr-20201208-093957","1","4","","","11","2","1","1","1","0","40","440","440","","","0","0","","","","","","","1","2","","350","","","2020-12-08 09:39:57","2021-01-06 14:23:50","Test");
INSERT INTO sales VALUES("270","sr-20210111-014535","1","3","","","1","1","1","2","2","0","137.57","1388","1388","","","0","0","","","0","","","0","1","2","","","","","2021-01-11 13:45:35","2021-01-11 13:58:44","Test");
INSERT INTO sales VALUES("271","posr-20210111-021625","1","3","","","19","1","2","3","4","0","98.78","1274","1274","","","0","0","","","","","","","1","4","","1274","","","2021-01-11 14:16:25","2021-01-11 14:16:25","Test");
INSERT INTO sales VALUES("274","posr-20210113-070243","1","4","","","11","2","1","4","15","40","91.65","1556","1556","","","0","0","","","","","","","1","4","","1556","","","2021-01-13 07:02:43","2021-01-13 07:02:43","Test");
INSERT INTO sales VALUES("275","posr-20210201-023748","1","4","","","11","2","1","1","1","0","40","440","484","","","10","44","","","","","","","1","4","","484","","","2021-02-01 14:37:48","2021-02-01 14:37:48","Test");
INSERT INTO sales VALUES("277","posr-20210216-070948","1","4","","","11","2","1","15","15","0","1758.78","15156","15156","","","0","0","","","","","","","1","4","","15156","","","2021-02-16 07:09:48","2021-02-16 07:09:48","Test");
INSERT INTO sales VALUES("278","posr-20210217-073238","1","4","","","11","2","1","5","6","0","3140","24910","26949.9","","","10","2440.9","","","501","","","100","1","4","","26949.9","","","2021-02-17 07:32:38","2021-02-17 07:32:38","Test");
INSERT INTO sales VALUES("279","sr-20210311-014034","1","3","","","1","1","1","1","2","0","0","4","4","","","0","0","","","","","","","1","1","","","","","2021-03-11 13:40:34","2021-03-11 13:40:34","Test");
INSERT INTO sales VALUES("280","sr-20210317-041411","1","3","","","1","1","1","1","1","0","0","2","2","","","0","0","","","","","","","1","1","","","","","2021-03-17 04:14:11","2021-03-17 04:14:11","Test");
INSERT INTO sales VALUES("282","sr-20210318-054729","1","3","","","1","2","1","1","1","0","0","2","2","","","0","0","","","0","","","0","1","2","","","","","2021-03-18 05:47:29","2021-03-20 10:59:57","Test");
INSERT INTO sales VALUES("288","sr-20210412-014046","1","3","","","11","1","1","1","1","0","0","120","120","","","0","0","","","","","","","1","1","","","","","2021-04-12 13:40:46","2021-04-12 13:40:46","Test");
INSERT INTO sales VALUES("289","posr-20210524-071424","1","4","","","11","2","1","2","2","0","40","690","640","","","0","0","","","50","","","","1","4","","640","","","2021-05-24 19:14:24","2021-05-24 19:14:24","Test");
INSERT INTO sales VALUES("290","sr-20210524-071512","1","3","","","1","1","1","1","1","0","0","120","120","","","0","0","","","0","","","0","1","4","","120","","","2021-05-24 19:15:12","2021-05-24 19:46:47","Test");
INSERT INTO sales VALUES("294","posr-20210601-123530","1","4","","","11","2","1","1","1","0","40","440","440","","","0","0","","","","","","","1","4","","440","","","2021-06-01 12:35:30","2021-06-01 12:35:30","Test");
INSERT INTO sales VALUES("295","posr-20210601-032209","1","3","","","11","1","1","1","1","0","0","2","2","","","0","0","","","","","","","1","4","","2","","","2021-06-01 15:22:09","2021-06-01 15:22:09","Test");
INSERT INTO sales VALUES("296","posr-20210601-034624","1","3","","","11","1","1","1","1","0","0","2","2","","","0","0","","","","","","","1","4","","2","","","2021-06-01 15:46:24","2021-06-01 15:46:24","Test");
INSERT INTO sales VALUES("297","posr-20210601-074150","9","2","","","11","1","5","1","1","0","0","250","250","","","0","0","","","","","","","1","4","","250","","","2021-06-01 19:41:50","2021-06-01 19:41:50","Test");
INSERT INTO sales VALUES("298","posr-20210601-075547","9","2","","","11","1","5","1","1","0","0","2","2","","","0","0","","","","","","","1","4","","2","","","2021-06-01 19:55:47","2021-06-01 19:55:47","Test");
INSERT INTO sales VALUES("299","posr-20210616-105325","1","4","","","11","2","1","1","1","0","100","1100","1100","","","0","0","","","","","","","1","4","","1100","","","2021-06-16 10:53:25","2021-06-16 10:53:25","Test");
INSERT INTO sales VALUES("300","posr-20210616-111033","1","4","","","19","2","1","1","1","0","40","440","440","","","0","0","","","","","","","1","4","","440","","","2021-06-16 11:10:33","2021-06-16 11:10:33","Test");
INSERT INTO sales VALUES("301","posr-20210616-111111","1","4","","","19","2","1","2","2","0","200","2200","2200","","","0","0","","","","","","","1","4","","2200","","","2021-06-16 11:11:11","2021-06-16 11:11:11","Test");
INSERT INTO sales VALUES("302","posr-20210616-080452","1","4","","","11","2","1","1","1","0","40","440","440","","","0","0","","","","","","","1","4","","440","","","2021-06-16 20:04:52","2021-06-16 20:04:52","Test");
INSERT INTO sales VALUES("303","posr-20210616-080543","1","4","","","11","2","1","1","1","0","40","440","440","","","0","0","","","","","","","1","4","","440","","","2021-06-16 20:05:43","2021-06-16 20:05:43","Test");
INSERT INTO sales VALUES("304","posr-20210616-080634","1","4","","","19","2","1","1","1","0","40","440","440","","","0","0","","","","","","","1","4","","440","","","2021-06-16 20:06:34","2021-06-17 12:13:59","Test");
INSERT INTO sales VALUES("305","posr-20210616-082842","1","4","","","19","2","1","1","1","0","40","440","440","","","0","0","","","","","","","1","4","","440","","","2021-06-16 20:28:42","2021-06-16 20:28:42","Test");
INSERT INTO sales VALUES("306","sr-20210617-115553","1","3","","","19","1","1","1","1","0","100","1100","1100","","","0","0","","","","","","","1","4","","1100","","","2021-06-17 11:55:53","2021-06-17 11:55:53","Test");
INSERT INTO sales VALUES("307","sr-20210617-122349","1","3","","","19","1","1","1","2","0","200","2200","2200","","","0","0","","","0","","","0","1","2","","100","","","2021-06-17 12:23:49","2021-06-17 15:00:13","Test");
INSERT INTO sales VALUES("308","posr-20210618-083127","1","4","","","11","2","1","2","5","0","157.57","1608","1608","","","0","0","","","","","","","1","4","","1608","","","2021-06-18 20:31:27","2021-06-18 20:31:27","Test");
INSERT INTO sales VALUES("309","posr-20210618-083254","1","3","","","11","1","1","1","1","0","0","2","2","","","0","0","","","","","","","1","4","","2","","","2021-06-18 20:32:54","2021-06-18 20:32:54","Test");
INSERT INTO sales VALUES("310","posr-20210619-105121","1","4","","","11","2","1","6","6","0","58.78","958","958","","","0","0","","","","","","","1","4","","958","","","2021-06-19 10:51:21","2021-06-19 10:51:21","Test");
INSERT INTO sales VALUES("311","sr-20210619-105344","1","5","","","11","1","1","1","1","0","40","440","440","","","0","0","","","","","","","1","1","","","","","2021-06-19 10:53:44","2021-06-19 10:53:44","Test");
INSERT INTO sales VALUES("312","posr-20210619-110231","1","4","","","11","2","1","1","1","0","40","440","440","","","0","0","","","","","","","1","4","","440","","","2021-06-19 11:02:31","2021-06-19 11:02:31","Test");
INSERT INTO sales VALUES("313","posr-20210619-110628","1","4","","","19","2","1","1","1","0","0","250","250","","","0","0","","","","","","","1","4","","250","","","2021-06-19 11:06:28","2021-06-19 11:06:28","Test");
INSERT INTO sales VALUES("314","posr-20210627-124247","1","4","","","11","2","1","1","1","0","100","1100","1100","","","0","0","","","","","","","1","4","","1100","","","2021-06-27 12:42:47","2021-06-27 12:42:47","Test");
INSERT INTO sales VALUES("317","posr-20210707-103455","1","4","","","11","2","1","1","1","0","0","18","18","","","0","0","","","","","","","1","4","","18","","","2021-07-07 10:34:55","2021-07-07 10:34:55","Test");
INSERT INTO sales VALUES("325","posr-20210814-092805","1","4","","","11","2","1","1","2","0","0","2400","2400","","","0","0","","","0","","","0","1","2","","1250","","","2021-08-14 21:28:05","2021-08-14 21:34:27","Test");
INSERT INTO sales VALUES("333","posr-20210921-010722","1","4","","","11","2","1","1","1","0","0","2","2","","","","0","","","0","","","0","1","4","","2","","","2021-09-21 13:07:22","2021-09-21 13:07:22","Test");
INSERT INTO sales VALUES("334","posr-20210921-035020","1","4","","","11","2","1","1","1","0","0","120","120","","","0","0","","","","","","","1","4","","120","","","2021-09-21 15:50:20","2021-09-21 15:50:20","Test");
INSERT INTO sales VALUES("335","posr-20210923-024704","1","4","","","11","2","1","1","0.5","0","0","11","11","","","0","0","","","","","","","1","4","","11","","","2021-09-23 14:47:04","2021-09-23 14:47:04","Test");
INSERT INTO sales VALUES("336","posr-20211003-051829","1","4","","","11","2","1","2","2","0","100","1220","1220","","","0","0","","","","","","","1","4","","1220","","","2021-10-03 17:18:29","2021-10-03 17:18:29","Test");
INSERT INTO sales VALUES("338","posr-20211003-054404","1","4","","","11","2","1","1","1","0","100","1100","1100","","","0","0","","","","","","","1","4","","1100","","","2021-10-03 17:44:04","2021-10-03 17:44:04","Test");
INSERT INTO sales VALUES("340","posr-20211007-025229","1","4","","","11","2","1","1","1","0","100","1100","1100","","","0","0","","","","","","","1","4","","1100","","","2021-10-07 14:52:29","2021-10-07 14:52:29","Test");
INSERT INTO sales VALUES("341","posr-20211007-033840","1","4","","","11","2","1","1","1","0","100","1100","1100","","","0","0","","","","","","","1","4","","1100","","","2021-10-07 15:38:40","2021-10-07 15:38:40","Test");
INSERT INTO sales VALUES("342","sr-20211009-121242","1","4","","","11","2","1","2","3","0","200","2320","2320","","","0","0","","","0","","","0","1","2","","","","","2021-10-09 12:12:42","2021-10-09 16:22:39","Test");
INSERT INTO sales VALUES("343","sr-20211009-043232","1","4","","","11","2","1","1","2","0","200","2200","2200","","","0","0","","","","","","","1","1","","","","","2021-10-09 16:32:32","2021-10-09 16:32:32","Test");
INSERT INTO sales VALUES("348","posr-20211014-035820","1","4","","","19","2","1","1","1","0","100","1100","1100","","","0","0","","","","","","","1","4","","1100","","","2021-10-14 15:58:20","2021-10-14 15:58:20","Test");
INSERT INTO sales VALUES("349","posr-20211020-011209","1","4","","","11","2","1","1","1","0","0","102","102","","","0","0","","","","","","","1","4","","102","","","2021-10-20 13:12:09","2021-10-20 13:12:09","Test");
INSERT INTO sales VALUES("353","posr-20211114-013929","1","4","","","11","2","1","1","1","0","34","374","374","","","","0","","","0","","","0","1","4","","374","","","2021-11-14 13:39:29","2021-11-14 13:39:29","Test");
INSERT INTO sales VALUES("354","sr-20211115-120309","1","4","","","11","2","1","3","3","0","140","1542","1542","","","0","0","","","0","","","0","1","1","","","","","2021-11-15 12:03:09","2021-11-15 12:03:09","Test");
INSERT INTO sales VALUES("355","posr-20211124-062857","1","4","","","11","2","1","5","6","0","240","3161","3161","","","0","0","","","","","","","1","4","","3161","","","2021-11-24 18:28:57","2021-11-24 18:28:57","Test");
INSERT INTO sales VALUES("356","posr-20211207-070932","1","4","","","11","2","1","3","3","0","40","573","573","","","0","0","","","","","","","1","4","","573","","","2021-12-07 19:09:32","2021-12-07 19:09:32","Test");
INSERT INTO sales VALUES("360","posr-20211222-070313","1","4","","","11","2","1","1","2","0","3.13","24","24","","","0","0","","","0","","","0","1","4","","24","","","2021-12-22 19:03:13","2022-01-30 13:37:54","Test");
INSERT INTO sales VALUES("362","posr-20220203-035931","1","4","","","19","2","1","1","2","0","0","8.5","8.5","","","0","0","","","","","","","1","4","","8.5","","","2022-02-03 15:59:31","2022-02-03 15:59:31","Test");
INSERT INTO sales VALUES("363","posr-20220203-070547","1","4","","","19","2","1","4","5","0","0","521","521","","","0","0","Flat","0","0","","","0","1","2","","466","","","2022-02-01 00:00:00","2022-05-22 16:39:48","Test");
INSERT INTO sales VALUES("364","posr-20220205-054526","1","4","","","11","2","1","1","1","0","40","440","396","","","0","0","Percentage","10","44","","","0","1","4","","396","","","2022-02-05 17:45:26","2022-02-05 19:31:58","Test");
INSERT INTO sales VALUES("365","posr-20220205-054616","1","4","","","11","2","1","1","1","0","0","250","240","","","0","0","Flat","10","10","","","0","1","4","","240","","","2022-02-05 17:46:16","2022-02-05 19:31:09","Test");
INSERT INTO sales VALUES("372","sr-20220219-120815","1","5","","","11","1","1","1","1","0","0","120","108","","","0","0","Percentage","10","12","","","0","1","2","","","","","2022-02-17 00:00:00","2022-02-20 21:15:54","Test");
INSERT INTO sales VALUES("376","sr-20220220-083638","1","5","","","11","1","1","1","1","0","40","440","440","","","0","0","Flat","0","0","","","0","1","2","","","","","2022-02-20 00:00:00","2022-02-20 21:15:23","Test");
INSERT INTO sales VALUES("377","posr-20220227-115905","1","4","","","11","2","1","2","3","0","0","620","620","","","0","0","Flat","0","0","","","0","1","2","","521","","","2022-02-27 00:00:00","2022-03-12 19:12:38","Test");
INSERT INTO sales VALUES("379","posr-20220309-115901","1","4","","","11","2","1","3","3","0","140","1790","1790","","","0","0","Flat","","0","","","","1","4","","1790","","","2022-03-09 11:59:01","2022-03-09 11:59:01","Test");
INSERT INTO sales VALUES("380","posr-20220312-082027","1","","","","11","2","1","1","1","0","40","440","440","","","0","0","Flat","0","0","","","0","1","4","","440","jhjkhjk
kjkljkljkl
kkljkljklj","","2022-03-12 00:00:00","2022-03-13 11:37:35","Test");
INSERT INTO sales VALUES("381","sr-20220312-082452","1","","","","11","1","1","2","2","0","0","360","360","","","0","0","Flat","0","0","","","0","1","2","","350","","","2022-03-12 00:00:00","2022-03-12 20:26:22","Test");
INSERT INTO sales VALUES("382","posr-20220424-110144","1","6","","","11","2","1","3","3","0","100","1225","1225","","","0","0","Flat","","0","","","","1","4","","1225","","","2022-04-24 11:01:44","2022-04-24 11:01:44","Test");
INSERT INTO sales VALUES("383","posr-20220516-112427","1","6","","","11","2","1","2","3","0","300","3300","3300","","","0","0","Flat","","0","","","","1","4","","3300","","","2022-05-16 11:24:27","2022-05-16 11:24:27","Test");
INSERT INTO sales VALUES("385","posr-20220526-090112","1","6","","","11","2","1","2","2","0","0","370","370","","","0","0","Flat","","0","","","","1","4","","370","","","2022-05-26 21:01:12","2022-05-26 21:01:12","Test");
INSERT INTO sales VALUES("386","posr-20220531-122026","1","6","","","11","2","1","1","2","0","80","880","880","","","0","0","Flat","0","0","","","0","2","2","","440","","","2022-05-31 00:00:00","2022-06-06 17:11:27","Test");
INSERT INTO sales VALUES("397","posr-20220606-051413","1","6","","","11","2","1","3","3","0","200","2320","2320","","","0","0","Flat","","0","","","","1","4","","2320","","","2022-06-06 17:14:13","2022-06-06 17:14:13","Test");
INSERT INTO sales VALUES("400","posr-20220614-115719","1","6","","","11","2","1","2","3","0","300","3300","3300","","","0","0","Flat","","0","","","","1","2","","2200","","","2022-06-14 11:57:19","2022-09-04 15:55:25","Test");
INSERT INTO sales VALUES("403","posr-20220614-050144","1","6","","","11","2","1","1","1","0","0","120","120","","","0","0","Flat","","0","","","","1","2","","111","","","2022-06-14 17:01:44","2022-06-14 17:01:44","Test");
INSERT INTO sales VALUES("404","posr-20220615-102330","1","6","","","11","2","1","1","1","0","0","300","300","","","0","0","Flat","","0","","","","1","4","","300","","","2022-06-15 10:23:30","2022-06-15 10:23:30","Test");
INSERT INTO sales VALUES("409","posr-20220809-115444","1","","","","11","1","1","3","3","0","40","591","591","","","0","0","Flat","","0","","","","1","4","","591","","","2022-08-09 11:54:44","2022-08-09 11:54:44","Test");
INSERT INTO sales VALUES("410","posr-20220825-013137","1","6","","","11","2","1","1","1","0","0","120","120","","","0","0","Flat","","0","","","","1","4","","120","","","2022-08-25 13:31:37","2022-08-25 13:31:37","Test");
INSERT INTO sales VALUES("411","posr-20220901-123619","1","6","","","11","2","1","2","2","0","40","690","690","","","0","0","Flat","","0","","","","1","4","","690","","","2022-09-01 12:36:19","2022-09-01 12:36:19","Test");
INSERT INTO sales VALUES("412","sr-20220904-011755","1","","","","11","1","5","2","2","0","40","690","690","","","0","0","Flat","","0","","","","1","1","","","","","2022-09-04 13:17:55","2022-09-04 13:17:55","Test");
INSERT INTO sales VALUES("413","posr-20220911-011826","1","","","","11","1","1","1","1","0","110","1210","1210","","","0","0","Flat","","0","","","","1","4","","1210","","","2022-09-11 13:18:26","2022-09-11 13:18:26","Test");
INSERT INTO sales VALUES("414","posr-20220915-085859","1","6","","","11","2","1","1","1","0","40","440","440","","","0","0","Flat","","0","","","","1","4","","440","","","2022-09-15 20:58:59","2022-09-15 20:58:59","Test");
INSERT INTO sales VALUES("415","posr-20221002-081636","1","6","","","11","2","1","2","2","0","40","560","560","","","0","0","Flat","","0","","","","1","4","","560","","","2022-10-02 20:16:36","2022-10-02 20:16:36","Test");
INSERT INTO sales VALUES("416","posr-20221018-021013","1","6","","","11","2","1","1","1","0","0","120","120","","","0","0","Flat","","0","","","","1","2","","0","","","2022-10-18 14:10:13","2022-11-15 15:34:11","Test");
INSERT INTO sales VALUES("421","posr-20221124-055716","1","6","","","11","2","1","1","1","0","0","1250","1250","","","0","0","Flat","","0","","","","1","4","","1250","","","2022-11-24 17:57:16","2022-11-24 17:57:16","Test");
INSERT INTO sales VALUES("422","posr-20221124-062601","9","2","","","11","1","5","1","1","0","18.78","144","144","","","0","0","Flat","","0","","","","1","4","","144","","","2022-11-24 18:26:01","2022-11-24 18:26:01","Test");
INSERT INTO sales VALUES("423","posr-20221124-062653","9","2","","","11","1","5","1","1","0","0","250","250","","","0","0","Flat","","0","","","","1","4","","250","","","2022-11-24 18:26:53","2022-11-24 18:26:53","Test");
INSERT INTO sales VALUES("424","posr-20221211-025325","1","6","","","11","2","1","3","3","0","18.78","286","286","","","0","0","Flat","","0","","","","1","4","","286","","","2022-12-11 14:53:25","2022-12-11 14:53:25","Test");
INSERT INTO sales VALUES("425","posr-20230110-040832","1","6","","","11","2","1","1","1","0","18.78","144","144","","","0","0","Flat","","0","","","","3","2","","0","","","2023-01-10 16:08:32","2023-01-10 16:08:32","Test");
INSERT INTO sales VALUES("426","posr-20230111-123924","1","6","","","11","2","1","1","1","0","18.78","144","144","","","0","0","Flat","","0","","","","1","4","","144","","","2023-01-11 00:39:24","2023-01-11 00:39:24","Test");
INSERT INTO sales VALUES("427","posr-20230111-123959","1","6","","","11","2","1","1","1","0","18.78","144","144","","","0","0","Flat","","0","","","","1","4","","144","","","2023-01-11 00:39:59","2023-01-11 00:39:59","Test");
INSERT INTO sales VALUES("428","posr-20230118-042810","1","6","","","11","2","1","2","2","0","55.14","544","544","","","0","0","Flat","","0","","","","1","4","","544","","","2023-01-18 16:28:10","2023-01-18 16:28:10","Test");
INSERT INTO sales VALUES("429","posr-20230121-032001","1","6","","","11","2","1","1","1","0","36.36","400","350","","","0","0","Flat","50","50","","","","1","4","","350","","","2023-01-21 15:20:01","2023-01-21 15:20:01","Test");
INSERT INTO sales VALUES("430","posr-20230121-070439","1","6","","","11","2","1","1","1","0","36.36","400","350","","","0","0","Flat","50","50","","","","1","4","","350","","","2023-01-21 19:04:39","2023-01-21 19:04:39","Test");
INSERT INTO sales VALUES("431","posr-20230121-071710","1","6","","","11","2","1","1","1","0","36.36","400","350","","","0","0","Flat","50","50","","","","1","4","","350","","","2023-01-21 19:17:10","2023-01-21 19:17:10","Test");
INSERT INTO sales VALUES("432","posr-20230121-071843","1","6","","","11","2","1","1","1","0","36.36","400","350","","","0","0","Flat","50","50","","","","1","4","","350","","","2023-01-21 19:18:43","2023-01-21 19:18:43","Test");
INSERT INTO sales VALUES("433","posr-20230222-030250","1","6","","","11","2","1","1","1","0","36.36","400","400","","","0","0","Flat","","0","","","","1","4","","400","","","2023-02-22 15:02:50","2023-02-22 15:02:50","Test");
INSERT INTO sales VALUES("434","posr-20230223-045811","1","6","","","11","2","1","1","1","0","18.78","144","144","2","0.9","0","0","Flat","","0","","","","1","4","","144","","","2023-02-23 16:58:11","2023-02-23 16:58:11","Test");
INSERT INTO sales VALUES("435","posr-20230224-011141","1","6","","","11","2","1","3","3","0","55.14","794","794","2","0.9","0","0","Flat","","0","","","","1","4","","794","","","2023-02-24 01:11:41","2023-02-24 01:11:41","Test");
INSERT INTO sales VALUES("436","posr-20230224-011447","1","6","","","11","2","1","3","3","0","55.14","794","794","2","0.9","0","0","Flat","","0","","","","1","4","","794","","","2023-02-24 01:14:47","2023-02-24 01:14:47","Test");
INSERT INTO sales VALUES("437","posr-20230224-011503","1","6","","","11","2","1","3","3","0","55.14","794","794","2","0.9","0","0","Flat","","0","","","","1","4","","794","","","2023-02-24 01:15:03","2023-02-24 01:15:03","Test");
INSERT INTO sales VALUES("438","posr-20230224-115202","1","6","","","11","2","1","4","4","0","55.14","816","816","2","0.9","0","0","Flat","","0","","","","1","4","","816","","","2023-02-24 11:52:02","2023-02-24 11:52:02","Test");
INSERT INTO sales VALUES("455","posr-20230313-053109","1","6","","","11","2","1","1","1","0","32.73","360","360","2","0.9","0","0","Flat","","0","","","","1","4","","360","","","2023-03-13 17:31:09","2023-03-13 17:31:09","Test");
INSERT INTO sales VALUES("457","sr-20230323-124527","1","7","","","1","1","1","2","2","0","32.73","468","468","2","0.9","0","0","Flat","","0","","","","1","1","","","","","2023-03-23 12:45:27","2023-03-23 12:45:27","Test");
INSERT INTO sales VALUES("458","posr-20230323-053358","1","6","","","11","2","1","1","1","0","0","108","99","2","0.9","0","0","Flat","10","9","","","","1","4","","99","","","2023-03-23 17:33:58","2023-03-23 17:33:58","Test");
INSERT INTO sales VALUES("459","sr-20230327-105427","1","7","","","1","1","1","1","1","0","32.73","360","392","2","0.9","0","0","Flat","13","13","","","45","1","1","","","","","2023-03-27 10:54:27","2023-03-27 10:54:27","Test");
INSERT INTO sales VALUES("460","posr-20230327-110818","1","6","","","11","2","1","2","4","0","32.73","684","709","2","0.9","0","0","Flat","20","20","","","45","1","4","","709","","","2023-03-27 11:08:18","2023-03-27 11:08:18","Test");
INSERT INTO sales VALUES("462","posr-20230405-013919","1","6","","","11","2","1","2","3","0","37.57","538","538","1","1","0","0","Flat","","0","","","0","1","4","","538","","","2023-04-05 13:39:19","2023-04-05 13:39:19","Test");
INSERT INTO sales VALUES("463","sr-20230424-075649","1","7","","","11","1","1","1","1","0","0","100","100","1","1","0","0","Flat","","0","","","0","1","1","","","","","2023-04-24 19:56:49","2023-04-24 19:56:49","Test");
INSERT INTO sales VALUES("469","posr-20230506-040646","1","6","","","11","2","1","3","3","0","127.27","1700","1700","1","1","0","0","Flat","","0","","","0","1","4","","1700","","","2023-05-06 16:06:46","2023-05-06 16:06:46","Test");
INSERT INTO sales VALUES("470","posr-20230517-013623","1","6","","","11","2","1","2","3","0","0","340","340","1","1","0","0","Flat","","0","","","0","1","4","","340","","","2023-05-17 13:36:23","2023-05-17 13:36:23","Test");
INSERT INTO sales VALUES("471","posr-20230604-021435","1","6","1","1","11","2","1","1","1","0","0","250","250","1","1","0","0","Flat","","0","","","0","1","4","","250","","","2023-06-04 14:14:35","2023-06-04 14:14:35","Test");
INSERT INTO sales VALUES("472","posr-20230604-034829","1","6","","","11","2","1","2","2","0","18.78","166","166","1","1","0","0","Flat","","0","","","0","1","4","","166","","","2023-06-04 15:48:29","2023-06-04 15:48:29","Test");
INSERT INTO sales VALUES("473","posr-20230605-030712","1","6","","","11","2","1","1","1","0","0","250","225","1","1","0","0","Percentage","10","25","","","0","1","4","","225","","","2023-06-05 15:07:12","2023-06-05 15:07:12","Test");
INSERT INTO sales VALUES("474","123458","1","7","","","3","1","1","2","3","0","0","275.1","775.1","","","0","0","Flat","500","500","","","1000","1","1","","","Test","","2023-07-09 10:32:57","2023-07-09 10:32:57","Test");
INSERT INTO sales VALUES("476","5654654","1","7","","","44","1","1","1","1","0","36.36","400","-165","1","1","0","0","Flat","565","565","","","0","1","1","","","hfgh","hfghfghfg","2023-07-11 00:00:00","2023-07-10 15:35:04","Test");
INSERT INTO sales VALUES("477","65464","1","7","","","44","1","1","1","1","0","0","100","100","1","1","0","0","Flat","","0","","","0","1","1","","","test","test","2023-07-10 00:00:00","2023-07-10 15:44:13","Test");



CREATE TABLE `stock_counts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warehouse_id` int NOT NULL,
  `category_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `initial_file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `final_file` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `is_adjusted` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO stock_counts VALUES("1","scr-20190228-124939","2","","","1","full","20190228-124939.csv","","","0","2019-02-28 12:49:39","2019-02-28 12:49:39");
INSERT INTO stock_counts VALUES("4","scr-20220202-083105","1","2","","1","partial","20220202-083105.csv","","","0","2022-02-02 20:31:05","2022-02-02 20:31:05");
INSERT INTO stock_counts VALUES("5","scr-20221012-122735","1","4","3","1","partial","20221012-122735.csv","20221012-123133.csv","","1","2022-10-12 12:27:35","2022-10-12 12:55:38");
INSERT INTO stock_counts VALUES("6","scr-20230401-092719","1","4","","1","partial","20230401-092719.csv","20230401-092947.csv","test stock count","1","2023-04-01 21:27:19","2023-04-01 21:30:50");



CREATE TABLE `suppliers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vat_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO suppliers VALUES("1","abdullah","globaltouch.jpg","global touch","","abdullah@gmail.com","231231","fsdfs","fsdfs","","","bd","1","2018-05-13 04:06:34","2022-06-06 11:25:06");
INSERT INTO suppliers VALUES("2","test","lion.jpg","lion","","lion@gmail.com","242","gfdg","fgd","","","","0","2018-05-30 05:59:41","2018-05-30 06:00:06");
INSERT INTO suppliers VALUES("3","ismail","","techbd","","ismail@test.com","23123123","mohammadpur","dhaka","","","bangladesh","1","2018-07-20 10:34:17","2018-07-20 10:34:17");
INSERT INTO suppliers VALUES("4","modon","mogaFruit.jpg","mogaFruit","","abdullah@gmail.com ","32321","nasirabad","chittagong","","","bd","0","2018-09-01 10:30:07","2023-07-09 16:55:05");
INSERT INTO suppliers VALUES("5","sadman","","anda boda","dsa","asd@dsa.com","3212313","dadas","sdad","Other","1312","Australia","0","2020-06-22 15:48:33","2020-06-22 15:48:52");
INSERT INTO suppliers VALUES("9","modon95","mogaFruit.jpg","moga95","","abdullah@gmail.com ","32321","nasirabad","chittagong","","","bd","0","2023-07-09 16:35:12","2023-07-09 16:55:05");
INSERT INTO suppliers VALUES("10","gfdgggdf","gfdg.jpg","gfdg","534","manattgertest@gmail.com","tert","terter","tert","tert","53434","gfdgf","0","2023-07-09 16:55:50","2023-07-09 16:56:13");



CREATE TABLE `tables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `number_of_person` int DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tables VALUES("1","Table 1","3","Right corner table","1","2023-06-04 14:13:33","2023-06-04 14:13:33");
INSERT INTO tables VALUES("2","Table 2","2","Middle table","1","2023-06-04 14:13:58","2023-06-04 14:13:58");



CREATE TABLE `taxes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rate` double NOT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO taxes VALUES("1","vat@10","10","1","2018-05-12 15:58:30","2019-03-02 17:46:10");
INSERT INTO taxes VALUES("2","vat@15","15","1","2018-05-12 15:58:43","2018-05-28 05:35:05");
INSERT INTO taxes VALUES("3","test","6","0","2018-05-28 05:32:54","2018-05-28 05:34:44");
INSERT INTO taxes VALUES("4","vat 20","20","1","2018-09-01 06:58:57","2018-09-01 06:58:57");



CREATE TABLE `transfers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `status` int NOT NULL,
  `from_warehouse_id` int NOT NULL,
  `to_warehouse_id` int NOT NULL,
  `item` int NOT NULL,
  `total_qty` double NOT NULL,
  `total_tax` double NOT NULL,
  `total_cost` double NOT NULL,
  `shipping_cost` double DEFAULT NULL,
  `grand_total` double NOT NULL,
  `document` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO transfers VALUES("1","tr-20180808-051710","1","1","2","1","1","100","0","100","0","100","","","2018-08-08 17:17:10","2018-12-25 04:16:55");
INSERT INTO transfers VALUES("6","tr-20191205-075504","1","1","1","2","1","1","0","2","0","2","","","2019-12-05 19:55:04","2019-12-05 20:09:42");
INSERT INTO transfers VALUES("8","tr-20200122-123058","1","1","1","2","1","10","0","1000","","1000","","","2020-01-22 12:30:58","2020-01-22 12:30:58");
INSERT INTO transfers VALUES("10","tr-20201008-012735","9","1","1","2","1","1","32","352","0","352","","","2020-10-08 13:27:35","2020-10-08 13:29:35");
INSERT INTO transfers VALUES("11","tr-20201018-061708","9","1","1","2","1","1","0","1","","1","","","2020-10-18 18:17:08","2020-10-18 18:17:08");
INSERT INTO transfers VALUES("12","tr-20201024-090146","1","1","2","1","1","10","4500","34500","","34500","","","2020-10-24 09:01:46","2020-10-24 09:01:46");
INSERT INTO transfers VALUES("21","tr-20220309-114411","1","1","1","2","1","6","0","600","0","600","","jhjkjk
jhkjhkhk
jjkjkhkhkh","2022-03-06 00:00:00","2022-06-05 12:37:31");
INSERT INTO transfers VALUES("22","tr-20230709-104855","1","1","1","2","1","1","0","10","5000","5010","Screenshot from 2023-07-08 14-52-01.png","Test","2023-07-09 00:00:00","2023-07-09 10:53:39");



CREATE TABLE `units` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `unit_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `base_unit` int DEFAULT NULL,
  `operator` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `operation_value` double DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO units VALUES("1","pc","Piece","","*","1","1","2018-05-12 08:27:46","2018-08-18 03:41:53");
INSERT INTO units VALUES("2","dozen","dozen box","1","*","12","1","2018-05-12 15:57:05","2022-10-19 11:33:46");
INSERT INTO units VALUES("3","cartoon","cartoon box","1","*","24","1","2018-05-12 15:57:45","2022-11-24 16:29:30");
INSERT INTO units VALUES("4","m","meter","","*","1","1","2018-05-12 15:58:07","2018-05-28 05:20:57");
INSERT INTO units VALUES("6","test","test","","*","1","0","2018-05-28 05:20:20","2018-05-28 05:20:25");
INSERT INTO units VALUES("7","kg","kilogram","","*","1","1","2018-06-25 06:49:26","2018-06-25 06:49:26");
INSERT INTO units VALUES("8","20","ni33","8","*","1","0","2018-08-01 04:35:51","2018-08-01 04:40:54");
INSERT INTO units VALUES("9","gm","gram","7","/","1000","1","2018-09-01 06:06:28","2018-09-01 06:06:28");
INSERT INTO units VALUES("10","gz","goz","","*","1","0","2018-11-29 09:40:29","2019-03-02 17:53:29");
INSERT INTO units VALUES("11","inch","Inch","","*","1","1","2022-06-07 16:42:40","2022-06-07 16:42:40");
INSERT INTO units VALUES("12","pza","pza","","*","1","1","2022-10-15 21:42:19","2022-10-15 21:42:19");



CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` int NOT NULL,
  `biller_id` int DEFAULT NULL,
  `warehouse_id` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users VALUES("1","admin","admin@gmail.com","$2y$10$KOKg3BOPxpj.u7UPLB439uXGqn5WpTnUhZO1XQYeiuliE5huwZOEa","3QHqi4wX2TU0O5Dk7vgXEezdzTlhHzxGb005X3FPTOVEx8LzXSLFvGfWWNPB","12112","lioncoders","1","","","1","0","2018-06-02 09:24:15","2023-02-16 15:42:51");
INSERT INTO users VALUES("3","dhiman da","dhiman@gmail.com","$2y$10$Fef6vu5E67nm11hX7V5a2u1ThNCQ6n9DRCvRF9TD7stk.Pmt2R6O.","5ehQM6JIfiQfROgTbB5let0Z93vjLHS7rd9QD5RPNgOxli3xdo7fykU7vtTt","212","lioncoders","1","","","0","1","2018-06-14 04:00:31","2020-11-05 13:06:51");
INSERT INTO users VALUES("6","test","test@gmail.com","$2y$10$TDAeHcVqHyCmurki0wjLZeIl1SngKX3WLOhyTiCoZG3souQfqv.LS","KpW1gYYlOFacumklO2IcRfSsbC3KcWUZzOI37gqoqM388Xie6KdhaOHIFEYm","1234","212312","4","","","0","1","2018-06-23 09:05:33","2018-06-23 09:13:45");
INSERT INTO users VALUES("8","test","test@yahoo.com","$2y$10$hlMigidZV0j2/IPkgE/xsOSb8WM2IRlsMv.1hg1NM7kfyd6bGX3hC","","31231","","4","","","0","1","2018-06-25 04:35:49","2018-07-02 07:07:39");
INSERT INTO users VALUES("9","staff","anda@gmail.com","$2y$10$kxDbnynB6mB1e1w3pmtbSOlSxy/WwbLPY5TJpMi0Opao5ezfuQjQm","5rvuIyVSIXn3PcUJAUF0RiTG13XS2DS7JHwM4QObTmt598lHV3mMipFif2pB","3123","","4","5","1","1","0","2018-07-02 07:08:08","2018-10-24 03:41:13");
INSERT INTO users VALUES("10","abul","abul@alpha.com","$2y$10$5zgB2OOMyNBNVAd.QOQIju5a9fhNnTqPx5H6s4oFlXhNiF6kXEsPq","x7HlttI5bM0vSKViqATaowHFJkLS3PHwfvl7iJdFl5Z1SsyUgWCVbLSgAoi0","1234","anda","1","","","0","0","2018-09-08 05:44:48","2018-09-08 05:44:48");
INSERT INTO users VALUES("11","teststaff","a@a.com","$2y$10$5KNBIIhZzvvZEQEhkHaZGu.Q8bbQNfqYvYgL5N55B8Pb4P5P/b/Li","DkHDEcCA0QLfsKPkUK0ckL0CPM6dPiJytNa0k952gyTbeAyMthW3vi7IRitp","111","aa","4","5","1","0","1","2018-10-22 08:47:56","2018-10-23 08:10:56");
INSERT INTO users VALUES("12","john","john@gmail.com","$2y$10$P/pN2J/uyTYNzQy2kRqWwuSv7P2f6GE/ykBwtHdda7yci3XsfOKWe","O0f1WJBVjT5eKYl3Js5l1ixMMtoU6kqrH7hbHDx9I1UCcD9CmiSmCBzHbQZg","10001","","4","2","2","0","1","2018-12-30 06:48:37","2023-07-10 12:41:29");
INSERT INTO users VALUES("13","jjj","test@test.com","$2y$10$/Qx3gHWYWUhlF1aPfzXaCeZA7fRzfSEyCIOnk/dcC4ejO8PsoaalG","","1213","","1","","","0","1","2019-01-03 06:08:31","2019-03-03 10:02:29");
INSERT INTO users VALUES("19","shakalaka","shakalaka@gmail.com","$2y$10$ketLWT0Ib/JXpo00eJlxoeSw.7leS8V1CUGInfbyOWT4F5.Xuo7S2","","1212","Digital image","5","","","1","0","2020-11-09 06:07:16","2020-11-09 06:07:16");
INSERT INTO users VALUES("21","modon","modon@gmail.com","$2y$10$7VpoeGMkP8QCvL5zLwFW..6MYJ5MRumDLDoX.TTQtClS561rpFHY.","","2222","modon company","5","","","1","0","2020-11-13 13:12:08","2020-11-13 13:12:08");
INSERT INTO users VALUES("22","dhiman","dhiman@gmail.com","$2y$10$3mPygsC6wwnDtw/Sg85IpuExtUhgaHx52Lwp7Rz0.FNfuFdfKVpRq","","+8801111111101","lioncoders","5","","","1","0","2020-11-15 12:14:58","2020-11-15 12:14:58");
INSERT INTO users VALUES("31","mbs","mbs@gmail.com","$2y$10$6Ldm1rWEVSrlTmpjIXkeQO9KwWJz/j0FB4U.fY1oCFeax47rvttEK","","2121","","4","1","2","0","0","2021-12-29 12:40:22","2021-12-29 12:40:22");
INSERT INTO users VALUES("39","maja","maja@maja.com","$2y$10$lrMVhNDE9AuKhFrJIgG2y.zdtrCltR8/JB1okO0W8GsUcMjSFW7rW","","444555","","4","5","2","1","0","2022-09-14 10:37:21","2022-09-14 10:37:21");
INSERT INTO users VALUES("41","irfan95","irfanchowdhury80@gmail.com","$2y$10$md/7IQ8URWmuztW3Tr4m3e9JUKRh2yaZm9woQTQSGejaIK/8YQLDO","","01829498634","","1","","","1","0","2023-07-09 14:55:40","2023-07-09 14:55:40");
INSERT INTO users VALUES("42","arman95","irfanchychowdhury80@gmail.com","$2y$10$h/vsE1IWcx.i9mbY8ncbq.5rtOkSXE.ElfDV4iQ7HLi7pAaqRcVnS","","123456789","Oranzetool","1","","","0","1","2023-07-09 15:58:53","2023-07-09 16:10:17");
INSERT INTO users VALUES("43","irfan88","irfan88@gmail.com","$2y$10$bWtgGv0Rb66Ci5TKZ2oI1erE8Evr2H5tit8FniaQpb4mjEL94ynSq","","45454","test cc","1","","","1","0","2023-07-10 11:49:38","2023-07-10 11:49:38");
INSERT INTO users VALUES("44","Bevis Lucas","telybebyb@mailinator.com","$2y$10$KZA7YbsOsyEGEl84nen4zOBDDliBnG0EmMkxLpDjg.W.g8ECNe29q","","+1 (597) 543-5372","","2","","","1","1","2023-07-10 13:16:12","2023-07-10 13:16:38");
INSERT INTO users VALUES("45","Keith Peterson","xewy@mailinator.com","$2y$10$9lT4420Exz5uEiheup2XrOuaWF2MGDiUEYGbWpiLT0F2LZ91Pe1IC","","+1 (646) 659-2641","","1","","","1","1","2023-07-10 13:20:01","2023-07-10 13:21:15");
INSERT INTO users VALUES("46","Christen Michael","viqovony@mailinator.com","$2y$10$Bm1YvkSLKZkD1hQc0XyApOKjPUSxiFuaaOzIT9wvzNWFVn0gZ4ux2","","+1 (331) 387-1219","","1","","","1","1","2023-07-10 13:21:35","2023-07-10 13:22:01");



CREATE TABLE `variants` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO variants VALUES("2","Medium","2019-11-21 13:03:04","2019-11-24 14:43:52");
INSERT INTO variants VALUES("3","Small","2019-11-21 13:03:04","2019-11-24 14:43:52");
INSERT INTO variants VALUES("5","Large","2019-11-24 12:07:20","2019-11-24 14:44:56");
INSERT INTO variants VALUES("9","a","2020-05-18 22:44:14","2020-05-18 22:44:14");
INSERT INTO variants VALUES("11","b","2020-05-18 22:53:49","2020-05-18 22:53:49");
INSERT INTO variants VALUES("12","variant 1","2020-09-27 12:08:27","2020-09-27 12:08:27");
INSERT INTO variants VALUES("13","variant 2","2020-09-27 12:08:27","2020-09-27 12:08:27");
INSERT INTO variants VALUES("15","s","2020-11-16 12:09:33","2022-08-09 12:02:12");
INSERT INTO variants VALUES("16","m","2020-11-16 12:09:33","2022-08-09 12:02:12");
INSERT INTO variants VALUES("17","L","2020-11-16 12:09:33","2020-11-16 12:09:33");
INSERT INTO variants VALUES("18","s/red","2022-07-25 19:54:50","2022-07-25 19:54:50");
INSERT INTO variants VALUES("19","s/black","2022-07-25 19:54:50","2022-07-25 19:54:50");
INSERT INTO variants VALUES("20","m/red","2022-07-25 19:54:50","2022-07-25 19:54:50");
INSERT INTO variants VALUES("21","m/black","2022-07-25 19:54:50","2022-07-25 19:54:50");
INSERT INTO variants VALUES("22","l/red","2022-07-25 19:54:50","2022-07-25 19:54:50");
INSERT INTO variants VALUES("23","l/black","2022-07-25 19:54:50","2022-07-25 19:54:50");
INSERT INTO variants VALUES("24","s/blue","2022-08-03 11:25:19","2022-08-03 11:25:19");
INSERT INTO variants VALUES("25","m/blue","2022-08-03 11:25:20","2022-08-03 11:25:20");
INSERT INTO variants VALUES("26","l/blue","2022-08-03 11:25:20","2022-08-03 11:25:20");
INSERT INTO variants VALUES("27","s/green","2022-08-06 11:54:37","2022-08-06 11:54:37");
INSERT INTO variants VALUES("28","m/green","2022-08-06 11:54:37","2022-08-06 11:54:37");
INSERT INTO variants VALUES("29","l/green","2022-08-06 11:54:37","2022-08-06 11:54:37");
INSERT INTO variants VALUES("30","xl/red","2022-08-08 20:02:07","2022-08-08 20:02:07");
INSERT INTO variants VALUES("31","xl/blue","2022-08-08 20:02:07","2022-08-08 20:02:07");
INSERT INTO variants VALUES("32","xl/green","2022-08-08 20:02:07","2022-08-08 20:02:07");
INSERT INTO variants VALUES("33","red","2022-09-11 12:56:55","2022-09-11 12:56:55");
INSERT INTO variants VALUES("34","black","2022-09-11 12:56:55","2022-09-11 12:56:55");
INSERT INTO variants VALUES("35","blue","2022-09-11 12:56:55","2022-09-11 12:56:55");



CREATE TABLE `warehouses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO warehouses VALUES("1","warehouse 1","2312121","war1@lion.com","khatungonj, chittagong","1","2018-05-12 13:51:44","2019-03-02 21:40:17");
INSERT INTO warehouses VALUES("2","warehouse 2","1234","","boropul, chittagong","1","2018-05-12 14:09:03","2018-06-20 04:30:38");
INSERT INTO warehouses VALUES("3","test","","","dqwdeqw","0","2018-05-30 06:14:23","2018-05-30 06:14:47");
INSERT INTO warehouses VALUES("6","gudam","2121","","gazipur","0","2018-09-01 04:53:26","2018-09-01 04:54:48");

