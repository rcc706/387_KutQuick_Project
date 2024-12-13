-- GENERATES TABLES FOR 387 PROJECT
-- EXECUTE (in mysql) --> source tables.sql;

-- Drop existing tables
DROP TABLES IF EXISTS PENDING_VIDEOS, USER_TO_FILES, FILES, USERS; 

-- Users (Admins and normal users)
CREATE TABLE USERS (
        USER_ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        USER_NAME varchar(50) NOT NULL,
        USER_EMAIL varchar(75) NOT NULL,
	USER_ROLE ENUM('user', 'admin') NOT NULL,
        USER_PASSWORD varchar(100) NOT NULL
) Engine=InnoDB;

-- All files successfully uploaded
CREATE TABLE FILES (
        FILE_ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        FILE_NAME varchar(50) NOT NULL,
        FILE_LABEL varchar(50),
        PARTIAL_PATH varchar(100) NOT NULL
) Engine=InnoDB;

-- Maps the user to the file uploaded
CREATE TABLE USER_TO_FILES (
        USER_ID INT NOT NULL,
        FILE_ID INT NOT NULl,
        PRIMARY KEY (USER_ID, FILE_ID),
		FOREIGN KEY (USER_ID) REFERENCES USERS(USER_ID),
		FOREIGN KEY (FILE_ID) REFERENCES FILES(FILE_ID)
) Engine=InnoDB;


