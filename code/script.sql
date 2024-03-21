-----------------------
----DROP STATEMENTS----
-----------------------
DROP TABLE Question_Has;
DROP TABLE Participates;
DROP TABLE Earns;
DROP TABLE Bot_Monitors4;
DROP TABLE Bot_Monitors3;
DROP TABLE Bot_Monitors1;
DROP TABLE Website;
DROP TABLE App;
DROP TABLE Book;
DROP TABLE Achievement2;
DROP TABLE Achievement1;
DROP TABLE Forum4;
DROP TABLE Forum3;
DROP TABLE Forum1;
DROP TABLE Completes;
DROP TABLE Uses;
DROP TABLE Learns;
DROP TABLE Specializes;
DROP TABLE Exercise4;
DROP TABLE Exercise3;
DROP TABLE Exercise1;
DROP TABLE Material;
DROP TABLE Learner_Consults;
DROP TABLE Language2;
DROP TABLE Language1;
DROP TABLE Expert4;
DROP TABLE Expert3;
DROP TABLE Expert1;
-----------------------
---CREATE STATEMENTS---
-----------------------
CREATE TABLE Expert1(
    DeliveryMode VARCHAR(50) PRIMARY KEY,
    Capacity	 INTEGER
);

CREATE TABLE Expert3(
    ExpertName   VARCHAR(50),
    City	  	 VARCHAR(50),
    DeliveryMode VARCHAR(50),
    PRIMARY KEY (ExpertName, City),
    FOREIGN KEY (DeliveryMode) REFERENCES Expert1
	    ON DELETE CASCADE
);

CREATE TABLE Expert4(
    ExpertEmail  VARCHAR(50) PRIMARY KEY,
    ExpertName	 VARCHAR(50),
    City	  	 VARCHAR(50),
    FOREIGN KEY (ExpertName, City) REFERENCES Expert3
	    ON DELETE CASCADE
);

CREATE TABLE Language1(
    LanguageName VARCHAR(50) PRIMARY KEY,
    NumChars	 INTEGER
);

CREATE TABLE Language2(
    LanguageName VARCHAR(50),
    Dialect	     VARCHAR(50),
    PRIMARY KEY (LanguageName, Dialect),
    FOREIGN KEY (LanguageName) REFERENCES Language1
	    ON DELETE CASCADE
);

CREATE TABLE Learner_Consults(
	UserID   	 INTEGER PRIMARY KEY,
	Username 	 VARCHAR(50) UNIQUE,
	Age      	 INTEGER,
	Password 	 VARCHAR(50),
	ExpertEmail  VARCHAR(50),
	FOREIGN KEY (ExpertEmail) REFERENCES Expert4
		ON DELETE CASCADE
);

CREATE TABLE Material(
	MaterialID	 INTEGER PRIMARY KEY,
	MaterialName VARCHAR(50),
	Purpose	     VARCHAR(100)
);

CREATE TABLE Exercise1(
	ExerciseName VARCHAR(50) PRIMARY KEY,
    Purpose	     VARCHAR(100)
);

CREATE TABLE Exercise3(
	ExerciseName VARCHAR(50) PRIMARY KEY,
    TimeLimit	 INTEGER,
    FOREIGN KEY (ExerciseName) REFERENCES Exercise1
	    ON DELETE CASCADE
);

CREATE TABLE Exercise4(
	ExerciseName   VARCHAR(50),
	ExerciseNumber INTEGER,
    Score		   REAL,
    PRIMARY KEY (ExerciseName, ExerciseNumber),
    FOREIGN KEY (ExerciseName) REFERENCES Exercise1
	    ON DELETE CASCADE
);

CREATE TABLE Specializes(
	ExpertEmail     VARCHAR(50),
	LanguageName    VARCHAR(50),
	Dialect         VARCHAR(50),
	YearsExperience INTEGER,
	PRIMARY KEY(ExpertEmail, LanguageName, Dialect),
	FOREIGN KEY (ExpertEmail) REFERENCES Expert4
		ON DELETE CASCADE,
	FOREIGN KEY (LanguageName, Dialect) REFERENCES Language2
		ON DELETE CASCADE
);

CREATE TABLE Learns(
	UserID 	     INTEGER,
	LanguageName VARCHAR(50),
	Dialect 	 VARCHAR(50),
	StartDate 	 DATE,
	PRIMARY KEY (UserID, LanguageName, Dialect),
    FOREIGN KEY (UserID) REFERENCES Learner_Consults
        ON DELETE CASCADE,
    FOREIGN KEY (LanguageName, Dialect) REFERENCES Language2
        ON DELETE CASCADE
);

CREATE TABLE Uses(
	UserID 	     INTEGER,
	LanguageName VARCHAR(50),
	Dialect 	 VARCHAR(50),
	MaterialID   INTEGER,
	PRIMARY KEY (UserID, LanguageName, Dialect),
    FOREIGN KEY (UserID) REFERENCES Learner_Consults
        ON DELETE CASCADE,
    FOREIGN KEY (LanguageName, Dialect) REFERENCES Language2
        ON DELETE CASCADE,
    FOREIGN KEY (MaterialID) REFERENCES Material
        ON DELETE CASCADE
);

CREATE TABLE Completes(
	UserID 	       INTEGER,
	LanguageName   VARCHAR(50),
	Dialect 	   VARCHAR(50),
	ExerciseName   VARCHAR(50),
	ExerciseNumber INTEGER,
	CompletionDate DATE,
	PRIMARY KEY (UserID, LanguageName, Dialect, ExerciseName, ExerciseNumber),
	FOREIGN KEY (UserID) REFERENCES Learner_Consults 
		ON DELETE CASCADE,
	FOREIGN KEY (LanguageName, Dialect) REFERENCES Language2
		ON DELETE CASCADE,
	FOREIGN KEY (ExerciseName, ExerciseNumber) REFERENCES Exercise4
		ON DELETE CASCADE
);

CREATE TABLE Forum1(
	Title   VARCHAR(50) PRIMARY KEY,
	Purpose VARCHAR(100)
);

CREATE TABLE Forum3(
	Title   	 VARCHAR(50) PRIMARY KEY,
	UserCapacity INTEGER,
	FOREIGN KEY (Title) REFERENCES Forum1
		ON DELETE CASCADE
);

CREATE TABLE Forum4(
	URL    VARCHAR(100) PRIMARY KEY,
	Status VARCHAR(50),
	Title  VARCHAR(50),
	FOREIGN KEY (Title) REFERENCES Forum1
		ON DELETE CASCADE
);

CREATE TABLE Achievement1(
	RewardID INTEGER PRIMARY KEY,
	RewardName VARCHAR(50) UNIQUE
);

CREATE TABLE Achievement2(
	AchievementID   	   INTEGER PRIMARY KEY,
	AchievementName 	   VARCHAR(50),
	AchievementDescription VARCHAR(50),
	RewardID 			   INTEGER,
	ReceivalDate		   DATE,
	FOREIGN KEY (RewardID) REFERENCES Achievement1
		ON DELETE CASCADE
);

CREATE TABLE Book(
	MaterialID INTEGER PRIMARY KEY,
	Author	   VARCHAR(50),
	FOREIGN KEY (MaterialID) REFERENCES Material
		ON DELETE CASCADE
);

CREATE TABLE App(
	MaterialID INTEGER PRIMARY KEY,
	Developer  VARCHAR(50),
	FOREIGN KEY (MaterialID) REFERENCES Material
		ON DELETE CASCADE
);

CREATE TABLE Website(
	MaterialID INTEGER PRIMARY KEY,
	URL		   VARCHAR(100),
	FOREIGN KEY (MaterialID) REFERENCES Material
		ON DELETE CASCADE
);

CREATE TABLE Bot_Monitors1(
	BotName VARCHAR(50) PRIMARY KEY,
	Purpose VARCHAR(100)
);

CREATE TABLE Bot_Monitors3(
	BotName 	 VARCHAR(50),
	Creator 	 VARCHAR(50),
	CreationDate DATE,
	PRIMARY KEY (BotName, Creator),
	FOREIGN KEY (BotName) REFERENCES Bot_Monitors1
ON DELETE CASCADE
);

CREATE TABLE Bot_Monitors4(
    BotName VARCHAR(50),
    Version VARCHAR(50),
    Creator VARCHAR(50), 
    URL     VARCHAR(100) NOT NULL,
    PRIMARY KEY (BotName, Version),
	FOREIGN KEY (BotName, Creator) REFERENCES Bot_Monitors3
        ON DELETE CASCADE,
    FOREIGN KEY (URL) REFERENCES Forum4
	    ON DELETE CASCADE
);

CREATE TABLE Question_Has(
	ExerciseName   VARCHAR(50),
	ExerciseNumber INTEGER, 
	QuestionName   VARCHAR(50),
	Type		   VARCHAR(50),
	PRIMARY KEY (ExerciseName, ExerciseNumber, QuestionName),
	FOREIGN KEY (ExerciseName, ExerciseNumber) REFERENCES Exercise4
		ON DELETE CASCADE
);

CREATE TABLE Participates(
	UserID INTEGER,
	URL    VARCHAR(100),
	PRIMARY KEY (UserID, URL),
	FOREIGN KEY (UserID) REFERENCES Learner_Consults
        ON DELETE CASCADE,
	FOREIGN KEY (URL) REFERENCES Forum4
		ON DELETE CASCADE
);

CREATE TABLE Earns(
	UserID 	      INTEGER,
	AchievementID INTEGER,
	ReceivalDate  DATE,
	PRIMARY KEY (UserID, AchievementID),
	FOREIGN KEY (UserID) REFERENCES Learner_Consults
        ON DELETE CASCADE,
    FOREIGN KEY (AchievementID) REFERENCES Achievement2
	    ON DELETE CASCADE
);
-----------------------
---INSERT STATEMENTS---
-----------------------
INSERT INTO Expert1(DeliveryMode, Capacity) VALUES ('Online Morning', 20);
INSERT INTO Expert1(DeliveryMode, Capacity) VALUES ('Online Afternoon', 20);
INSERT INTO Expert1(DeliveryMode, Capacity) VALUES ('Online Evening', 15);
INSERT INTO Expert1(DeliveryMode, Capacity) VALUES ('In-Person Morning', 15);
INSERT INTO	Expert1(DeliveryMode, Capacity) VALUES ('In-Person Afternoon', 15);
INSERT INTO	Expert1(DeliveryMode, Capacity) VALUES ('In-Person Evening', 10);

INSERT INTO Expert3(ExpertName, City, DeliveryMode) VALUES ('Romina M', 'Vancouver', 'Online Morning');
INSERT INTO Expert3(ExpertName, City, DeliveryMode) VALUES ('Annie W', 'Vancouver', 'Online Afternoon');
INSERT INTO Expert3(ExpertName, City, DeliveryMode) VALUES ('Sohbat S', 'Vancouver', 'In-Person Morning');
INSERT INTO Expert3(ExpertName, City, DeliveryMode) VALUES ('James W', 'Toronto', 'Online Morning');
INSERT INTO Expert3(ExpertName, City, DeliveryMode) VALUES ('Kayla K', 'Toronto', 'Online Morning');
INSERT INTO Expert3(ExpertName, City, DeliveryMode) VALUES ('Kate M', 'Edmonton', 'In-Person Afternoon');

INSERT INTO Expert4(ExpertEmail, ExpertName, City) VALUES ('romina.m@mail.com', 'Romina M', 'Vancouver');
INSERT INTO Expert4(ExpertEmail, ExpertName, City) VALUES ('annie.w@mail.com', 'Annie W', 'Vancouver');
INSERT INTO Expert4(ExpertEmail, ExpertName, City) VALUES ('sohbat.s@mail.com', 'Sohbat S', 'Vancouver');
INSERT INTO Expert4(ExpertEmail, ExpertName, City) VALUES ('james.w@mail.com', 'James W', 'Toronto');
INSERT INTO Expert4(ExpertEmail, ExpertName, City) VALUES ('kayla.k@mail.com', 'Kayla K', 'Toronto');
INSERT INTO Expert4(ExpertEmail, ExpertName, City) VALUES ('kate.m@mail.com', 'Kate M', 'Edmonton');

INSERT INTO Language1(LanguageName, NumChars) VALUES ('English', 26);
INSERT INTO Language1(LanguageName, NumChars) VALUES ('Spanish', 27);
INSERT INTO Language1(LanguageName, NumChars) VALUES ('French', 26);
INSERT INTO Language1(LanguageName, NumChars) VALUES ('German', 30);
INSERT INTO Language1(LanguageName, NumChars) VALUES ('Chinese', 26);
INSERT INTO Language1(LanguageName, NumChars) VALUES ('Korean', 24);

INSERT INTO Language2(LanguageName, Dialect) VALUES ('English', 'American English');
INSERT INTO Language2(LanguageName, Dialect) VALUES	('English', 'British English');
INSERT INTO Language2(LanguageName, Dialect) VALUES	('Spanish', 'Latin American Spanish');
INSERT INTO Language2(LanguageName, Dialect) VALUES	('Spanish', 'European Spanish');
INSERT INTO Language2(LanguageName, Dialect) VALUES	('French', 'Standard French');
INSERT INTO Language2(LanguageName, Dialect) VALUES	('French', 'Canadian French');
INSERT INTO Language2(LanguageName, Dialect) VALUES	('French', 'Belgian French');
INSERT INTO Language2(LanguageName, Dialect) VALUES	('French', 'African French');
INSERT INTO Language2(LanguageName, Dialect) VALUES	('German', 'Standard German');
INSERT INTO Language2(LanguageName, Dialect) VALUES	('German', 'Swiss German');
INSERT INTO Language2(LanguageName, Dialect) VALUES	('Chinese', 'Mandarin');
INSERT INTO Language2(LanguageName, Dialect) VALUES	('Chinese', 'Shanghainese');
INSERT INTO Language2(LanguageName, Dialect) VALUES ('Chinese', 'Cantonese');
INSERT INTO Language2(LanguageName, Dialect) VALUES	('Korean', 'Gyeonggi Dialect');  

INSERT INTO Learner_Consults(UserID, UserName, Age, Password, ExpertEmail) VALUES (1, 'User 1', 21, 'pass1', 'romina.m@mail.com');
INSERT INTO Learner_Consults(UserID, UserName, Age, Password, ExpertEmail) VALUES (2, 'User 2', 22, 'pass2', 'annie.w@mail.com');
INSERT INTO Learner_Consults(UserID, UserName, Age, Password, ExpertEmail) VALUES (3, 'User 3', 25, 'pass3', 'sohbat.s@mail.com');
INSERT INTO Learner_Consults(UserID, UserName, Age, Password, ExpertEmail) VALUES (4, 'User 4', 19, 'pass4', 'romina.m@mail.com');
INSERT INTO Learner_Consults(UserID, UserName, Age, Password, ExpertEmail) VALUES (5, 'User 5', 18, 'pass5', 'annie.w@mail.com');
INSERT INTO Learner_Consults(UserID, UserName, Age, Password, ExpertEmail) VALUES (6, 'User 6', 20, 'pass6', 'sohbat.s@mail.com');

INSERT INTO Material(MaterialID, MaterialName, Purpose) VALUES (11, 'English in Use', 'A Self-Study Reference and Practice Book for Intermediate Learners of English with Answers');
INSERT INTO Material(MaterialID, MaterialName, Purpose) VALUES (12, 'Spanish Maestro', 'A Self-Study Reference and Practice Book for Intermediate Learners of Spanish with Answers');
INSERT INTO Material(MaterialID, MaterialName, Purpose) VALUES (13, 'Elegance in French', 'A Self-Study Reference and Practice Book for Intermediate Learners of French with Answers');
INSERT INTO Material(MaterialID, MaterialName, Purpose) VALUES (14, 'Chinese Stories for Language Learners', 'A treasury of proverbs and folktales in Chinese and English');
INSERT INTO Material(MaterialID, MaterialName, Purpose) VALUES (15, 'German for Dummies', 'The fun and easy way to learn the fascinating language of German with integrated audio clips!');
INSERT INTO Material(MaterialID, MaterialName, Purpose) VALUES (21, 'Example Bookstore', 'Find Your Favorite and Necessary books');
INSERT INTO Material(MaterialID, MaterialName, Purpose) VALUES (22, 'Example Lingo Fitness App', 'Can your tongue pronounce correctly?');
INSERT INTO Material(MaterialID, MaterialName, Purpose) VALUES (23, 'Example Language Learning', 'Get help to learn new language on your own time');
INSERT INTO Material(MaterialID, MaterialName, Purpose) VALUES (24, 'Example Pronunciation Platform', 'Learn the correct pronunciations in over 100 languages');
INSERT INTO Material(MaterialID, MaterialName, Purpose) VALUES (25, 'Example Forum', 'Get access to all online forums');
INSERT INTO Material(MaterialID, MaterialName, Purpose) VALUES (31, 'Multi-Language Dictionary', 'Get words definitions and translations in over 20 languages');
INSERT INTO Material(MaterialID, MaterialName, Purpose) VALUES (32, 'How Good is Your Spanish?', 'Comprehensive Spanish Language Quizzes');
INSERT INTO Material(MaterialID, MaterialName, Purpose) VALUES (33, 'Teach A Tongue', 'Learn New languages');
INSERT INTO Material(MaterialID, MaterialName, Purpose) VALUES (34, 'Contact a LingoXpert', 'Get in touch with language experts from all over the world');
INSERT INTO Material(MaterialID, MaterialName, Purpose) VALUES (35, 'Are you speaking Correctly?', 'Learn all the nuances of the language of your choice');

INSERT INTO Exercise1(ExerciseName, Purpose) VALUES ('Active to Passive Voice English', 'Test identification and conversion in Passive and Active voice in English');
INSERT INTO Exercise1(ExerciseName, Purpose) VALUES ('Parisian Culture', 'Test your knowledge in Parisian Culture');
INSERT INTO Exercise1(ExerciseName, Purpose) VALUES ('Chinese Vocabulary Quiz', 'Test Chinese vocabulary knowledge');
INSERT INTO Exercise1(ExerciseName, Purpose) VALUES ('Spanish Grammar Quiz', 'Practice Spanish grammar rules');
INSERT INTO Exercise1(ExerciseName, Purpose) VALUES ('German Pronunciation Workout', 'Practice pronunciations for common German words');
INSERT INTO Exercise1(ExerciseName, Purpose) VALUES ('Korean Alphabet Quiz', 'Test your knowledge in Hangul');
INSERT INTO Exercise1(ExerciseName, Purpose) VALUES ('Mock Spelling Bee', 'Test your ability to spell English words');
INSERT INTO Exercise1(ExerciseName, Purpose) VALUES ('French Pronunciation Quiz', 'Practice your French pronunciation');
INSERT INTO Exercise1(ExerciseName, Purpose) VALUES ('Chinese Oral Exercise', 'Test your Chinese pronunciation');
INSERT INTO Exercise1(ExerciseName, Purpose) VALUES ('Advanced Spanish Grammar Quiz', 'Practice advanced Spanish grammar rules');
INSERT INTO Exercise1(ExerciseName, Purpose) VALUES ('Must-Know Korean Phrases', 'Test how well you know common Korean words and phrases');

INSERT INTO Exercise3(ExerciseName, TimeLimit) VALUES ('Active to Passive Voice English', '100');
INSERT INTO Exercise3(ExerciseName, TimeLimit) VALUES ('Parisian Culture', '100');
INSERT INTO Exercise3(ExerciseName, TimeLimit) VALUES ('Chinese Vocabulary Quiz', '90');
INSERT INTO Exercise3(ExerciseName, TimeLimit) VALUES ('Spanish Grammar Quiz', '60');
INSERT INTO Exercise3(ExerciseName, TimeLimit) VALUES ('German Pronunciation Workout', NULL);
INSERT INTO Exercise3(ExerciseName, TimeLimit) VALUES ('Korean Alphabet Quiz', '45');
INSERT INTO Exercise3(ExerciseName, TimeLimit) VALUES ('Mock Spelling Bee', '15');
INSERT INTO Exercise3(ExerciseName, TimeLimit) VALUES ('French Pronunciation Quiz', '30');
INSERT INTO Exercise3(ExerciseName, TimeLimit) VALUES ('Chinese Oral Exercise', '20');
INSERT INTO Exercise3(ExerciseName, TimeLimit) VALUES ('Advanced Spanish Grammar Quiz', '60');
INSERT INTO Exercise3(ExerciseName, TimeLimit) VALUES ('Must-Know Korean Phrases', '75');

INSERT INTO Exercise4(ExerciseName, ExerciseNumber, Score) VALUES ('Active to Passive Voice English', 61, 0.90);
INSERT INTO Exercise4(ExerciseName, ExerciseNumber, Score) VALUES ('Parisian Culture', 62, 0.85);
INSERT INTO Exercise4(ExerciseName, ExerciseNumber, Score) VALUES ('Chinese Vocabulary Quiz', 63, NULL);
INSERT INTO Exercise4(ExerciseName, ExerciseNumber, Score) VALUES ('Spanish Grammar Quiz', 64, 0.95);
INSERT INTO Exercise4(ExerciseName, ExerciseNumber, Score) VALUES ('German Pronunciation Workout', 65, 0.80);
INSERT INTO Exercise4(ExerciseName, ExerciseNumber, Score) VALUES ('Korean Alphabet Quiz', 92, 0.95);
INSERT INTO Exercise4(ExerciseName, ExerciseNumber, Score) VALUES ('Mock Spelling Bee', 93, 0.49);
INSERT INTO Exercise4(ExerciseName, ExerciseNumber, Score) VALUES ('French Pronunciation Quiz', 94, 0.85);
INSERT INTO Exercise4(ExerciseName, ExerciseNumber, Score) VALUES ('Chinese Oral Exercise', 95, 0.88);
INSERT INTO Exercise4(ExerciseName, ExerciseNumber, Score) VALUES ('Advanced Spanish Grammar Quiz', 96, 0.75);
INSERT INTO Exercise4(ExerciseName, ExerciseNumber, Score) VALUES ('Must-Know Korean Phrases', 97, 0.97);

INSERT INTO Specializes(ExpertEmail, LanguageName, Dialect, YearsExperience) VALUES ('romina.m@mail.com', 'Korean', 'Gyeonggi Dialect', 10);
INSERT INTO Specializes(ExpertEmail, LanguageName, Dialect, YearsExperience) VALUES ('annie.w@mail.com', 'Chinese', 'Mandarin', 10);
INSERT INTO Specializes(ExpertEmail, LanguageName, Dialect, YearsExperience) VALUES ('sohbat.s@mail.com', 'English', 'American English', 20);
INSERT INTO Specializes(ExpertEmail, LanguageName, Dialect, YearsExperience) VALUES ('james.w@mail.com', 'French', 'Canadian French', 10);
INSERT INTO Specializes(ExpertEmail, LanguageName, Dialect, YearsExperience) VALUES ('kayla.k@mail.com', 'Spanish', 'Latin American Spanish', 15);
INSERT INTO Specializes(ExpertEmail, LanguageName, Dialect, YearsExperience) VALUES ('kate.m@mail.com', 'English', 'American English', 5);
INSERT INTO Specializes(ExpertEmail, LanguageName, Dialect, YearsExperience) VALUES ('kate.m@mail.com', 'Spanish', 'European Spanish', 5);
INSERT INTO Specializes(ExpertEmail, LanguageName, Dialect, YearsExperience) VALUES ('kate.m@mail.com', 'French', 'Belgian French', 5);
INSERT INTO Specializes(ExpertEmail, LanguageName, Dialect, YearsExperience) VALUES ('kate.m@mail.com', 'German', 'Swiss German', 3);
INSERT INTO Specializes(ExpertEmail, LanguageName, Dialect, YearsExperience) VALUES ('kate.m@mail.com', 'Chinese', 'Mandarin', 2);
INSERT INTO Specializes(ExpertEmail, LanguageName, Dialect, YearsExperience) VALUES ('kate.m@mail.com', 'Korean', 'Gyeonggi Dialect', 2);

INSERT INTO Learns(UserID, LanguageName, Dialect, StartDate) VALUES (1, 'Korean', 'Gyeonggi Dialect', '01-SEP-23');
INSERT INTO Learns(UserID, LanguageName, Dialect, StartDate) VALUES (2, 'Chinese', 'Mandarin', '21-SEP-23');
INSERT INTO Learns(UserID, LanguageName, Dialect, StartDate) VALUES (3, 'English', 'American English', '12-OCT-23');
INSERT INTO Learns(UserID, LanguageName, Dialect, StartDate) VALUES (4, 'French', 'Canadian French', '10-APR-23');
INSERT INTO Learns(UserID, LanguageName, Dialect, StartDate) VALUES (5, 'Spanish', 'Latin American Spanish', '22-NOV-22');
INSERT INTO Learns(UserID, LanguageName, Dialect, StartDate) VALUES (6, 'German', 'Swiss German', '11-DEC-23');

INSERT INTO Uses(UserID, LanguageName, Dialect, MaterialID) VALUES (1, 'Korean', 'Gyeonggi Dialect', 35);
INSERT INTO Uses(UserID, LanguageName, Dialect, MaterialID) VALUES (2, 'Chinese', 'Mandarin', 14);
INSERT INTO Uses(UserID, LanguageName, Dialect, MaterialID) VALUES (3, 'English', 'American English', 24);
INSERT INTO Uses(UserID, LanguageName, Dialect, MaterialID) VALUES (4, 'French', 'Canadian French', 31);
INSERT INTO Uses(UserID, LanguageName, Dialect, MaterialID) VALUES (5, 'Spanish', 'Latin American Spanish', 31);
INSERT INTO Uses(UserID, LanguageName, Dialect, MaterialID) VALUES (6, 'German', 'Swiss German', 15);

INSERT INTO Completes(UserID, LanguageName, Dialect, ExerciseName, ExerciseNumber, CompletionDate) VALUES (1, 'German', 'Swiss German', 'German Pronunciation Workout', 65, '01-OCT-23');
INSERT INTO Completes(UserID, LanguageName, Dialect, ExerciseName, ExerciseNumber, CompletionDate) VALUES (2, 'Chinese', 'Mandarin', 'Chinese Vocabulary Quiz', 63, '13-OCT-23');
INSERT INTO Completes(UserID, LanguageName, Dialect, ExerciseName, ExerciseNumber, CompletionDate) VALUES (2, 'Chinese', 'Mandarin', 'Chinese Oral Exercise', 95, '13-OCT-23');
INSERT INTO Completes(UserID, LanguageName, Dialect, ExerciseName, ExerciseNumber, CompletionDate) VALUES (2, 'Korean', 'Gyeonggi Dialect', 'Must-Know Korean Phrases', 97, '01-SEP-23');
INSERT INTO Completes(UserID, LanguageName, Dialect, ExerciseName, ExerciseNumber, CompletionDate) VALUES (2, 'English', 'American English', 'Active to Passive Voice English', 61, '04-DEC-23');
INSERT INTO Completes(UserID, LanguageName, Dialect, ExerciseName, ExerciseNumber, CompletionDate) VALUES (3, 'English', 'American English', 'Mock Spelling Bee', 93, '09-NOV-22');
INSERT INTO Completes(UserID, LanguageName, Dialect, ExerciseName, ExerciseNumber, CompletionDate) VALUES (3, 'English', 'American English', 'Active to Passive Voice English', 61, '09-NOV-22');
INSERT INTO Completes(UserID, LanguageName, Dialect, ExerciseName, ExerciseNumber, CompletionDate) VALUES (3, 'Korean', 'Gyeonggi Dialect', 'Korean Alphabet Quiz', 92, '12-SEP-23');
INSERT INTO Completes(UserID, LanguageName, Dialect, ExerciseName, ExerciseNumber, CompletionDate) VALUES (3, 'Korean', 'Gyeonggi Dialect', 'Must-Know Korean Phrases', 97, '13-SEP-23');
INSERT INTO Completes(UserID, LanguageName, Dialect, ExerciseName, ExerciseNumber, CompletionDate) VALUES (4, 'French', 'Canadian French', 'Parisian Culture', 62, '11-SEP-23');
INSERT INTO Completes(UserID, LanguageName, Dialect, ExerciseName, ExerciseNumber, CompletionDate) VALUES (4, 'French', 'Canadian French', 'French Pronunciation Quiz', 94, '11-SEP-23');
INSERT INTO Completes(UserID, LanguageName, Dialect, ExerciseName, ExerciseNumber, CompletionDate) VALUES (5, 'Spanish', 'Latin American Spanish', 'Spanish Grammar Quiz', 64, '02-FEB-23');
INSERT INTO Completes(UserID, LanguageName, Dialect, ExerciseName, ExerciseNumber, CompletionDate) VALUES (5, 'Spanish', 'Latin American Spanish', 'Advanced Spanish Grammar Quiz', 96, '04-FEB-23');

INSERT INTO Forum1(Title, Purpose) VALUES ('General Discussion', 'Discuss various topics');
INSERT INTO Forum1(Title, Purpose) VALUES ('Beginner Mandarin Chinese', 'Get help with Beginner Mandarin');
INSERT INTO Forum1(Title, Purpose) VALUES ('French In Canada', 'Moving to Quebec, you will need this');
INSERT INTO Forum1(Title, Purpose) VALUES ('Language Learning Book Club', 'Discuss and share thoughts on books in any language');
INSERT INTO Forum1(Title, Purpose) VALUES ('German Speaking Community', 'Get help to learn different languages with translation from German Speakers'); 
    
INSERT INTO Forum3(Title, UserCapacity) VALUES ('General Discussion', 1000);
INSERT INTO Forum3(Title, UserCapacity) VALUES ('Beginner Mandarin Chinese', 200);
INSERT INTO Forum3(Title, UserCapacity) VALUES ('French In Canada', 400);
INSERT INTO Forum3(Title, UserCapacity) VALUES ('Language Learning Book Club', 1000);
INSERT INTO Forum3(Title, UserCapacity) VALUES ('German Speaking Community', 350);
    
INSERT INTO Forum4(URL, Status, Title) VALUES ('http://example.com/general-discussion', 'Active', 'General Discussion');
INSERT INTO Forum4(URL, Status, Title) VALUES ('http://example.com/beginner-mandarin-chinese', 'Active', 'Beginner Mandarin Chinese');
INSERT INTO Forum4(URL, Status, Title) VALUES ('http://example.com/french-in-canada', 'Inactive', 'French In Canada');
INSERT INTO Forum4(URL, Status, Title) VALUES ('http://example.com/language-learning-book-club', 'Active', 'Language Learning Book Club');
INSERT INTO Forum4(URL, Status, Title) VALUES ('http://example.com/german-speaking-community', 'Active', 'German Speaking Community');

INSERT INTO Achievement1(RewardID, RewardName) VALUES (51, 'Gold Medal');
INSERT INTO Achievement1(RewardID, RewardName) VALUES (52, 'Silver Medal');
INSERT INTO Achievement1(RewardID, RewardName) VALUES (53, 'Bronze Medal');
INSERT INTO Achievement1(RewardID, RewardName) VALUES (54, 'Certificate of Achievement');
INSERT INTO Achievement1(RewardID, RewardName) VALUES (55, 'Badge of Honor');

INSERT INTO Achievement2(AchievementID, AchievementName, AchievementDescription, RewardID, ReceivalDate) VALUES (41, 'Completionist', 'Complete all exercises', 54, '01-SEP-23');
INSERT INTO Achievement2(AchievementID, AchievementName, AchievementDescription, RewardID, ReceivalDate) VALUES (42, 'Code Master', 'Complete all coding challenges', 51, '11-FEB-20');
INSERT INTO Achievement2(AchievementID, AchievementName, AchievementDescription, RewardID, ReceivalDate) VALUES (43, 'Language Pro', 'Achieve fluency in a language', 55, '12-MAR-22');
INSERT INTO Achievement2(AchievementID, AchievementName, AchievementDescription, RewardID, ReceivalDate) VALUES (44, 'Fitness Guru', 'Complete all workout routines', 52, '08-NOV-21');
INSERT INTO Achievement2(AchievementID, AchievementName, AchievementDescription, RewardID, ReceivalDate) VALUES (45, 'Bookworm', 'Read 100 books', 53, '29-OCT-23'); 

INSERT INTO Book(MaterialID, Author) VALUES (11, 'John Doe');
INSERT INTO Book(MaterialID, Author) VALUES (12, 'Gabriel Martinez');
INSERT INTO Book(MaterialID, Author) VALUES (13, 'Celestine Dior');
INSERT INTO Book(MaterialID, Author) VALUES (14, 'Vivian Ling');
INSERT INTO Book(MaterialID, Author) VALUES (15, 'Paulina Christensen');

INSERT INTO App(MaterialID, Developer) VALUES (31, 'DD Dev Ops');
INSERT INTO App(MaterialID, Developer) VALUES (32, '3Cent Games');
INSERT INTO App(MaterialID, Developer) VALUES (33, 'Foreign Lingo');
INSERT INTO App(MaterialID, Developer) VALUES (34, 'AllInfo Techs');
INSERT INTO App(MaterialID, Developer) VALUES (35, 'NoSure Info Corps');

INSERT INTO Website(MaterialID, URL) VALUES (21, 'http://examplebookstore.com');
INSERT INTO Website(MaterialID, URL) VALUES (22, 'http://examplelingofitnessapp.com');
INSERT INTO Website(MaterialID, URL) VALUES (23, 'http://examplelanguagelearning.com');
INSERT INTO Website(MaterialID, URL) VALUES (24, 'http://examplepronunicationplatform.com');
INSERT INTO Website(MaterialID, URL) VALUES (25, 'http://exampleforum.com'); 

INSERT INTO Bot_Monitors1(BotName, Purpose) VALUES ('SpamGuardBot', 'Monitor spam messages');
INSERT INTO Bot_Monitors1(BotName, Purpose) VALUES ('AutoResponderBot', 'Automatically respond to messages');
INSERT INTO Bot_Monitors1(BotName, Purpose) VALUES ('AnalyticsBot', 'Analyze user interactions');
INSERT INTO Bot_Monitors1(BotName, Purpose) VALUES ('SecurityBot', 'Monitor security threats');
INSERT INTO Bot_Monitors1(BotName, Purpose) VALUES ('FeedbackBot', 'Collect user feedback'); 

INSERT INTO Bot_Monitors3(BotName, Creator, CreationDate) VALUES ('SpamGuardBot', 'TechzCorp', '11-SEP-23');
INSERT INTO Bot_Monitors3(BotName, Creator, CreationDate) VALUES ('AutoResponderBot', 'TechzCorp', '21-JAN-20');
INSERT INTO Bot_Monitors3(BotName, Creator, CreationDate) VALUES ('AnalyticsBot', 'DataInsightsAnswers', '11-SEP-22');
INSERT INTO Bot_Monitors3(BotName, Creator, CreationDate) VALUES ('SecurityBot', 'CyberSafeSols', '19-OCT-23');
INSERT INTO Bot_Monitors3(BotName, Creator, CreationDate) VALUES ('FeedbackBot', 'UserInsights4All', '18-NOV-21'); 

INSERT INTO Bot_Monitors4(BotName, Version, Creator, URL) VALUES ('SpamGuardBot', 'v1.0', 'TechzCorp', 'http://example.com/general-discussion');
INSERT INTO Bot_Monitors4(BotName, Version, Creator, URL) VALUES ('AutoResponderBot', 'v2.0', 'TechzCorp', 'http://example.com/beginner-mandarin-chinese');
INSERT INTO Bot_Monitors4(BotName, Version, Creator, URL) VALUES ('AnalyticsBot', 'v1.5.2', 'DataInsightsAnswers', 'http://example.com/french-in-canada');
INSERT INTO Bot_Monitors4(BotName, Version, Creator, URL) VALUES ('SecurityBot', 'v1.2', 'CyberSafeSols', 'http://example.com/language-learning-book-club');
INSERT INTO Bot_Monitors4(BotName, Version, Creator, URL) VALUES ('FeedbackBot', 'v1.8', 'UserInsights4All', 'http://example.com/german-speaking-community');

INSERT INTO Question_Has(ExerciseName, ExerciseNumber, QuestionName, Type) VALUES ('Active to Passive Voice English', 61, 'Translate verb tenses', 'Intermediate');
INSERT INTO Question_Has(ExerciseName, ExerciseNumber, QuestionName, Type) VALUES ('Parisian Culture', 62, 'Name Parisian musicians', 'Advanced');
INSERT INTO Question_Has(ExerciseName, ExerciseNumber, QuestionName, Type) VALUES ('Parisian Culture', 62, 'Name Parisian actors', 'Advanced');
INSERT INTO Question_Has(ExerciseName, ExerciseNumber, QuestionName, Type) VALUES ('Chinese Vocabulary Quiz', 63, 'Daily life vocabulary', 'Beginner');
INSERT INTO Question_Has(ExerciseName, ExerciseNumber, QuestionName, Type) VALUES ('Chinese Vocabulary Quiz', 63, 'Work office vocabulary', 'Beginner');
INSERT INTO Question_Has(ExerciseName, ExerciseNumber, QuestionName, Type) VALUES ('Korean Alphabet Quiz', 92, 'List all basic vowels', 'Beginner');
INSERT INTO Question_Has(ExerciseName, ExerciseNumber, QuestionName, Type) VALUES ('Korean Alphabet Quiz', 92, 'List all basic consonants', 'Beginner');
INSERT INTO Question_Has(ExerciseName, ExerciseNumber, QuestionName, Type) VALUES ('Mock Spelling Bee', 93, 'Spell the following words', 'Beginner');
INSERT INTO Question_Has(ExerciseName, ExerciseNumber, QuestionName, Type) VALUES ('Must-Know Korean Phrases', 97, 'Pick the correct translation', 'Novice');

INSERT INTO Participates(UserID, URL) VALUES (1, 'http://example.com/general-discussion');
INSERT INTO Participates(UserID, URL) VALUES (1, 'http://example.com/beginner-mandarin-chinese');
INSERT INTO Participates(UserID, URL) VALUES (2, 'http://example.com/french-in-canada');
INSERT INTO Participates(UserID, URL) VALUES (3, 'http://example.com/french-in-canada');
INSERT INTO Participates(UserID, URL) VALUES (4, 'http://example.com/language-learning-book-club'); 

INSERT INTO Earns(UserID, AchievementID) VALUES (1, 41);
INSERT INTO Earns(UserID, AchievementID) VALUES (2, 42);
INSERT INTO Earns(UserID, AchievementID) VALUES (2, 43);
INSERT INTO Earns(UserID, AchievementID) VALUES (4, 44);
INSERT INTO Earns(UserID, AchievementID) VALUES (5, 44);
INSERT INTO Earns(UserID, AchievementID) VALUES (5, 45); 
