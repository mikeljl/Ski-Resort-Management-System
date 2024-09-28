DROP TABLE VIPLesson;
DROP TABLE StandardLesson;
DROP TABLE Takes;
DROP TABLE Lesson;
DROP TABLE Buys;
DROP TABLE CanAccess;
DROP TABLE Inhabits;
DROP TABLE SkiPass;
DROP TABLE PassType;
DROP TABLE WeatherCondition;
DROP TABLE IncidentsOccurs;
DROP TABLE SkiSlope;
DROP TABLE Lift;
DROP TABLE RescueTeam;
DROP TABLE RescueLocation;
DROP TABLE Wildlife;
DROP TABLE Skier;
DROP TABLE Terrain;
DROP TABLE WindSpeedInformation;
DROP TABLE TemperatureInformation;
DROP TABLE PrecipitationInformation;



CREATE TABLE PassType (
                          Type 		VARCHAR(255),
                          StartDate 	DATE,
                          EndDate 	DATE,
                          PassStatus 	VARCHAR(255),
                          PRIMARY KEY(Type, StartDate)
);

INSERT ALL
    INTO PassType (Type, StartDate, EndDate, PassStatus) VALUES ('Seasonal', DATE '2023-11-01', DATE '2024-04-30', 'Active')
INTO PassType (Type, StartDate, EndDate, PassStatus) VALUES ('Weekly', DATE '2024-01-01', DATE '2024-01-07', 'Active')
INTO PassType (Type, StartDate, EndDate, PassStatus) VALUES ('Daily', DATE '2024-02-01', DATE '2024-02-01', 'Expired')
INTO PassType (Type, StartDate, EndDate, PassStatus) VALUES ('Weekend', DATE '2024-02-05', DATE '2024-02-06', 'Active')
INTO PassType (Type, StartDate, EndDate, PassStatus) VALUES ('Holiday', DATE '2023-12-23', DATE '2023-12-26', 'Expired')
SELECT * FROM dual;

CREATE TABLE SkiPass(
                        PassNumber 		INT PRIMARY KEY,
                        StartDate 		DATE,
                        Type 			VARCHAR(255),
                        FOREIGN KEY (Type, StartDate) REFERENCES PassType(Type, StartDate)
);

INSERT ALL
    INTO SkiPass (PassNumber, StartDate, Type) VALUES (101, TO_DATE('2023-11-01', 'YYYY-MM-DD'), 'Seasonal')
INTO SkiPass (PassNumber, StartDate, Type) VALUES (102, TO_DATE('2024-01-01', 'YYYY-MM-DD'), 'Weekly')
INTO SkiPass (PassNumber, StartDate, Type) VALUES (103, TO_DATE('2024-02-01', 'YYYY-MM-DD'), 'Daily')
INTO SkiPass (PassNumber, StartDate, Type) VALUES (104, TO_DATE('2024-02-05', 'YYYY-MM-DD'), 'Weekend')
INTO SkiPass (PassNumber, StartDate, Type) VALUES (105, TO_DATE('2023-12-23', 'YYYY-MM-DD'), 'Holiday')
SELECT * FROM dual;

CREATE TABLE Lift (
                      LiftID 	VARCHAR(255) PRIMARY KEY,
                      Capacity 	INT,
                      OperatingHours 	INT
);

INSERT INTO Lift (LiftID, Capacity, OperatingHours) VALUES ('LiftA', 8, 10);
INSERT INTO Lift (LiftID, Capacity, OperatingHours) VALUES ('LiftB', 4, 12);
INSERT INTO Lift (LiftID, Capacity, OperatingHours) VALUES ('LiftC', 6, 8);
INSERT INTO Lift (LiftID, Capacity, OperatingHours) VALUES ('LiftD', 10, 9);
INSERT INTO Lift (LiftID, Capacity, OperatingHours) VALUES ('LiftE', 5, 11);

CREATE TABLE RescueLocation (
                                Location 		VARCHAR(255) PRIMARY KEY,
                                MaxResponseTime 	INT,
                                Equipment 		VARCHAR(255)
);

INSERT ALL
    INTO RescueLocation (Location, MaxResponseTime, Equipment) VALUES ('Basecamp Ranger Station', 1, 'First Aid Kit')
INTO RescueLocation (Location, MaxResponseTime, Equipment) VALUES ('Summit Watchtower', 15, 'Radio Communication Set')
INTO RescueLocation (Location, MaxResponseTime, Equipment) VALUES ('Northern Trailhead', 20, 'GPS Devices')
INTO RescueLocation (Location, MaxResponseTime, Equipment) VALUES ('Southern Access Point', 25, 'Emergency Flares')
INTO RescueLocation (Location, MaxResponseTime, Equipment) VALUES ('East Ridge Outpost', 30, 'Avalanche Probes')
SELECT * FROM dual;

CREATE TABLE RescueTeam (
                            TeamNumber 		INT PRIMARY KEY,
                            TeamLeader 		VARCHAR(255) DEFAULT NULL,
                            Location 		VARCHAR(255) DEFAULT NULL,
                            NumMembers		INT,
                            FOREIGN KEY (Location) REFERENCES RescueLocation(Location)
);

INSERT ALL
    INTO RescueTeam (TeamNumber, Location, TeamLeader, NumMembers) VALUES (1, 'Basecamp Ranger Station', 'John Smith', 10)
INTO RescueTeam (TeamNumber, Location, TeamLeader, NumMembers) VALUES (2, 'Summit Watchtower', 'Jane Doe', 5)
INTO RescueTeam (TeamNumber, Location, TeamLeader, NumMembers) VALUES (3, 'Northern Trailhead', 'Mike Johnson', 20)
INTO RescueTeam (TeamNumber, Location, TeamLeader, NumMembers) VALUES (4, 'Southern Access Point', 'Emily Davis', 10)
INTO RescueTeam (TeamNumber, Location, TeamLeader, NumMembers) VALUES (5, 'East Ridge Outpost', 'David Wilson', 6)
SELECT * FROM dual;

CREATE TABLE Wildlife (
                          Species VARCHAR(255) PRIMARY KEY,
                          LastObservedDate DATE
);

INSERT ALL
    INTO Wildlife (Species, LastObservedDate) VALUES ('Elk', DATE '2024-02-25')
INTO Wildlife (Species, LastObservedDate) VALUES ('Mountain Goat', DATE '2024-02-20')
INTO Wildlife (Species, LastObservedDate) VALUES ('Snowshoe Hare', DATE '2024-02-18')
INTO Wildlife (Species, LastObservedDate) VALUES ('Lynx', DATE '2024-02-15')
INTO Wildlife (Species, LastObservedDate) VALUES ('Brown Bear', DATE '2024-02-10')
SELECT * FROM dual;

-- change date attribute to lessondate, changed time to time stamp
-- need assertion to check for total and disjoint
CREATE TABLE Lesson (
                        LessonDate DATE,
                        LessonTime TIMESTAMP,
                        InstructorName VARCHAR(255),
                        Cost INT,
                        PRIMARY KEY (LessonDate, LessonTime, InstructorName)
);

INSERT INTO Lesson (LessonDate, LessonTime, InstructorName, Cost) VALUES (DATE '2024-03-01', TIMESTAMP '2024-03-01 09:00:00', 'Emily Johnson', 100);
INSERT INTO Lesson (LessonDate, LessonTime, InstructorName, Cost) VALUES (DATE '2024-03-01', TIMESTAMP '2024-03-01 10:00:00', 'Michael Smith', 120);
INSERT INTO Lesson (LessonDate, LessonTime, InstructorName, Cost) VALUES (DATE '2024-03-02', TIMESTAMP '2024-03-02 11:00:00', 'Sophia Brown', 110);
INSERT INTO Lesson (LessonDate, LessonTime, InstructorName, Cost) VALUES (DATE '2024-03-02', TIMESTAMP '2024-03-02 12:00:00', 'Daniel Garcia', 90);
INSERT INTO Lesson (LessonDate, LessonTime, InstructorName, Cost) VALUES (DATE '2024-03-03', TIMESTAMP '2024-03-03 13:00:00', 'Olivia Martinez', 95);
INSERT INTO Lesson (LessonDate, LessonTime, InstructorName, Cost) VALUES (DATE '2024-03-04', TIMESTAMP '2024-03-04 09:00:00', 'Lucas Allen', 70);
INSERT INTO Lesson (LessonDate, LessonTime, InstructorName, Cost) VALUES (DATE '2024-03-04', TIMESTAMP '2024-03-04 10:30:00', 'Eva Turner', 60);
INSERT INTO Lesson (LessonDate, LessonTime, InstructorName, Cost) VALUES (DATE '2024-03-05', TIMESTAMP '2024-03-05 11:00:00', 'Grace Lee', 50);
INSERT INTO Lesson (LessonDate, LessonTime, InstructorName, Cost) VALUES (DATE '2024-03-05', TIMESTAMP '2024-03-05 13:30:00', 'Samuel Walker', 80);
INSERT INTO Lesson (LessonDate, LessonTime, InstructorName, Cost) VALUES (DATE '2024-03-06', TIMESTAMP '2024-03-06 14:00:00', 'Emma Thomas', 90);

-- need assertion to check for total and disjoint
CREATE TABLE VIPLesson (
                           LessonDate DATE,
                           LessonTime TIMESTAMP,
                           InstructorName VARCHAR(255),
                           SpecialRequest VARCHAR(255),
                           PRIMARY KEY (LessonDate, LessonTime, InstructorName),
                           FOREIGN KEY (LessonDate, LessonTime, InstructorName) REFERENCES Lesson(LessonDate, LessonTime, InstructorName)
                               ON DELETE CASCADE
);

INSERT INTO VIPLesson (LessonDate, LessonTime, InstructorName, SpecialRequest) VALUES (DATE '2024-03-01', TIMESTAMP '2024-03-01 09:00:00', 'Emily Johnson', 'Private slope session');
INSERT INTO VIPLesson (LessonDate, LessonTime, InstructorName, SpecialRequest) VALUES (DATE '2024-03-01', TIMESTAMP '2024-03-01 10:00:00', 'Michael Smith', 'Video analysis of technique');
INSERT INTO VIPLesson (LessonDate, LessonTime, InstructorName, SpecialRequest) VALUES (DATE '2024-03-02', TIMESTAMP '2024-03-02 11:00:00', 'Sophia Brown', 'Focus on carving skills');
INSERT INTO VIPLesson (LessonDate, LessonTime, InstructorName, SpecialRequest) VALUES (DATE '2024-03-02', TIMESTAMP '2024-03-02 12:00:00', 'Daniel Garcia', 'Early access to lifts');
INSERT INTO VIPLesson (LessonDate, LessonTime, InstructorName, SpecialRequest) VALUES (DATE '2024-03-03', TIMESTAMP '2024-03-03 13:00:00', 'Olivia Martinez', 'Extended lesson time');

-- need assertion to check for total and disjoint
CREATE TABLE StandardLesson (
                                LessonDate DATE,
                                LessonTime TIMESTAMP,
                                InstructorName VARCHAR(255),
                                Discount INT,
                                PRIMARY KEY (LessonDate, LessonTime, InstructorName),
                                FOREIGN KEY (LessonDate, LessonTime, InstructorName) REFERENCES Lesson(LessonDate, LessonTime, InstructorName)
                                    ON DELETE CASCADE
);

INSERT INTO StandardLesson (LessonDate, LessonTime, InstructorName, Discount) VALUES (DATE '2024-03-04', TIMESTAMP '2024-03-04 09:00:00', 'Lucas Allen', 10);
INSERT INTO StandardLesson (LessonDate, LessonTime, InstructorName, Discount) VALUES (DATE '2024-03-04', TIMESTAMP '2024-03-04 10:30:00', 'Eva Turner', 20);
INSERT INTO StandardLesson (LessonDate, LessonTime, InstructorName, Discount) VALUES (DATE '2024-03-05', TIMESTAMP '2024-03-05 11:00:00', 'Grace Lee', 20);
INSERT INTO StandardLesson (LessonDate, LessonTime, InstructorName, Discount) VALUES (DATE '2024-03-05', TIMESTAMP '2024-03-05 13:30:00', 'Samuel Walker', 15);
INSERT INTO StandardLesson (LessonDate, LessonTime, InstructorName, Discount) VALUES (DATE '2024-03-06', TIMESTAMP '2024-03-06 14:00:00', 'Emma Thomas', 5);

CREATE TABLE Skier (
                       SkierID 		INT PRIMARY KEY,
                       LastName 	VARCHAR(255) DEFAULT NULL,
                       FirstName 	VARCHAR(255) DEFAULT NULL,
                       Email 		VARCHAR(255) DEFAULT NULL UNIQUE,
                       PhoneNumber  VARCHAR(255) DEFAULT NULL UNIQUE
);

INSERT INTO Skier (SkierID, LastName, FirstName, Email, PhoneNumber) VALUES (1, 'Li', 'Joe', 'joeli@gmail.com', '7786668888');
INSERT INTO Skier (SkierID, LastName, FirstName, Email, PhoneNumber) VALUES (2, 'Wang', 'Jason', 'jason@gmail.com', '7781231234');
INSERT INTO Skier (SkierID, LastName, FirstName, Email, PhoneNumber) VALUES (3, 'Zhang', 'Ken', 'ken@gmail.com', '7789990909');
INSERT INTO Skier (SkierID, LastName, FirstName, Email, PhoneNumber) VALUES (4, 'Lai', 'Jonathan', 'jk@gmail.com', '13300918291');
INSERT INTO Skier (SkierID, LastName, FirstName, Email, PhoneNumber) VALUES (5, 'Zheng', 'Bob', 'bz@gmail.com', '18899087866');

CREATE TABLE Terrain (
                         TerrainType 	VARCHAR(255),
                         Length 		INT,
                         Difficulty 		VARCHAR(255),
                         PRIMARY KEY (TerrainType, Length)
);

INSERT ALL
    INTO Terrain (TerrainType, Length, Difficulty) VALUES ('Alpine', 500, 'Intermediate')
INTO Terrain (TerrainType, Length, Difficulty) VALUES ('Freestyle', 400, 'Advanced')
INTO Terrain (TerrainType, Length, Difficulty) VALUES ('Groomed', 600, 'Beginner')
INTO Terrain (TerrainType, Length, Difficulty) VALUES ('Off-Piste', 700, 'Expert')
INTO Terrain (TerrainType, Length, Difficulty) VALUES ('Backcountry', 800, 'Expert')
SELECT * FROM dual;

-- removed on update cascade, since oracle does not support this
CREATE TABLE SkiSlope (
                          SlopeName 		VARCHAR(255) PRIMARY KEY,
                          TerrainType 		VARCHAR(255),
                          Status 			VARCHAR(255),
                          Length 			INT,
                          TeamNumber 		INT NOT NULL,
                          LiftID 			VARCHAR(255) NOT NULL UNIQUE,
                          FOREIGN KEY (TerrainType, Length) REFERENCES Terrain(TerrainType, Length),
                          FOREIGN KEY (TeamNumber) REFERENCES RescueTeam(TeamNumber),
                          FOREIGN KEY (LiftID) REFERENCES Lift(LiftID)
);

INSERT INTO SkiSlope (SlopeName, TerrainType, Status, Length, TeamNumber, LiftId) VALUES ('Slope 1', 'Alpine', 'Open', 500, 1, 'LiftA');
INSERT INTO SkiSlope (SlopeName, TerrainType, Status, Length, TeamNumber, LiftId) VALUES ('Slope 2', 'Freestyle', 'Closed', 400, 2, 'LiftB');
INSERT INTO SkiSlope (SlopeName, TerrainType, Status, Length, TeamNumber, LiftId) VALUES ('Slope 3', 'Groomed', 'Open', 600, 3, 'LiftC');
INSERT INTO SkiSlope (SlopeName, TerrainType, Status, Length, TeamNumber, LiftId) VALUES ('Slope 4', 'Off-Piste', 'Open', 700, 4, 'LiftD');
INSERT INTO SkiSlope (SlopeName, TerrainType, Status, Length, TeamNumber, LiftId) VALUES ('Slope 5', 'Backcountry', 'Closed', 800, 5, 'LiftE');

-- need to have assertions to check total participation on Species(every wildlife must participate)
CREATE TABLE Inhabits (
                          SlopeName VARCHAR(255),
                          Species VARCHAR(255),
                          PRIMARY KEY (SlopeName, Species),
                          FOREIGN KEY (SlopeName) REFERENCES SkiSlope(SlopeName),
                          FOREIGN KEY (Species) REFERENCES Wildlife(Species)
);

INSERT INTO Inhabits (SlopeName, Species) VALUES ('Slope 1', 'Elk');
INSERT INTO Inhabits (SlopeName, Species) VALUES ('Slope 2', 'Elk');
INSERT INTO Inhabits (SlopeName, Species) VALUES ('Slope 3', 'Elk');
INSERT INTO Inhabits (SlopeName, Species) VALUES ('Slope 4', 'Elk');
INSERT INTO Inhabits (SlopeName, Species) VALUES ('Slope 5', 'Elk');
INSERT INTO Inhabits (SlopeName, Species) VALUES ('Slope 1', 'Mountain Goat');
INSERT INTO Inhabits (SlopeName, Species) VALUES ('Slope 2', 'Mountain Goat');
INSERT INTO Inhabits (SlopeName, Species) VALUES ('Slope 3', 'Mountain Goat');
INSERT INTO Inhabits (SlopeName, Species) VALUES ('Slope 4', 'Mountain Goat');
INSERT INTO Inhabits (SlopeName, Species) VALUES ('Slope 5', 'Mountain Goat');
INSERT INTO Inhabits (SlopeName, Species) VALUES ('Slope 3', 'Snowshoe Hare');
INSERT INTO Inhabits (SlopeName, Species) VALUES ('Slope 4', 'Lynx');
INSERT INTO Inhabits (SlopeName, Species) VALUES ('Slope 5', 'Brown Bear');

CREATE TABLE PrecipitationInformation (
                                          Precipitation 	INT PRIMARY KEY,
                                          SnowType 		VARCHAR(255),
                                          SnowDepth 		INT
);


INSERT INTO PrecipitationInformation (Precipitation, SnowType, SnowDepth) VALUES (0, 'No precipitation', 0);
INSERT INTO PrecipitationInformation (Precipitation, SnowType, SnowDepth) VALUES (10, 'Light snow', 10);
INSERT INTO PrecipitationInformation (Precipitation, SnowType, SnowDepth) VALUES (20, 'Moderate snow', 15);
INSERT INTO PrecipitationInformation (Precipitation, SnowType, SnowDepth) VALUES (30, 'Heavy snow', 30);
INSERT INTO PrecipitationInformation (Precipitation, SnowType, SnowDepth) VALUES (40, 'Blizzard', 50);


CREATE TABLE TemperatureInformation (
                                        Temperature 		INT PRIMARY KEY,
                                        Precipitation 	INT,
                                        FOREIGN KEY (Precipitation) REFERENCES
                                            PrecipitationInformation(Precipitation)
);


INSERT INTO TemperatureInformation (Temperature, Precipitation) VALUES (-5, 0);
INSERT INTO TemperatureInformation (Temperature, Precipitation) VALUES (-2, 10);
INSERT INTO TemperatureInformation (Temperature, Precipitation) VALUES (-10, 20);
INSERT INTO TemperatureInformation (Temperature, Precipitation) VALUES (-7, 30);
INSERT INTO TemperatureInformation (Temperature, Precipitation) VALUES (-15, 20);
INSERT INTO TemperatureInformation (Temperature, Precipitation) VALUES (-20, 30);
INSERT INTO TemperatureInformation (Temperature, Precipitation) VALUES (-25, 40);


CREATE TABLE WindSpeedInformation (
                                      WindSpeed 		INT PRIMARY KEY,
                                      Temperature 		INT,
                                      AvalancheRiskLevel 	INT,
                                      FOREIGN KEY (Temperature) REFERENCES TemperatureInformation(Temperature)
);



INSERT INTO WindSpeedInformation (WindSpeed, Temperature, AvalancheRiskLevel) VALUES (5, -5, 1);
INSERT INTO WindSpeedInformation (WindSpeed, Temperature, AvalancheRiskLevel) VALUES (10, -2, 2);
INSERT INTO WindSpeedInformation (WindSpeed, Temperature, AvalancheRiskLevel) VALUES (20, -10, 3);
INSERT INTO WindSpeedInformation (WindSpeed, Temperature, AvalancheRiskLevel) VALUES (15, -7, 2);
INSERT INTO WindSpeedInformation (WindSpeed, Temperature, AvalancheRiskLevel) VALUES (25, -15, 4);
INSERT INTO WindSpeedInformation (WindSpeed, Temperature, AvalancheRiskLevel) VALUES (30, -20, 6);
INSERT INTO WindSpeedInformation (WindSpeed, Temperature, AvalancheRiskLevel) VALUES (35, -25, 7);


-- need assertion to check for total participation on SkiSlope (every ski slope must have at least one weather condition)
CREATE TABLE WeatherCondition (
                                  Time 		TIMESTAMP,
                                  SlopeName 	VARCHAR(255),
                                  WindSpeed 	INT,
                                  PRIMARY KEY (Time, SlopeName),
                                  FOREIGN KEY (SlopeName) REFERENCES SkiSlope(SlopeName) ON DELETE CASCADE,
                                  FOREIGN KEY (WindSpeed) REFERENCES WindSpeedInformation(WindSpeed)
);

INSERT INTO WeatherCondition (Time, SlopeName, WindSpeed) VALUES (TIMESTAMP '2024-02-26 09:00:00', 'Slope 1', 5);
INSERT INTO WeatherCondition (Time, SlopeName, WindSpeed) VALUES (TIMESTAMP '2024-02-26 09:00:00', 'Slope 2', 10);
INSERT INTO WeatherCondition (Time, SlopeName, WindSpeed) VALUES (TIMESTAMP '2024-02-26 11:00:00', 'Slope 3', 20);
INSERT INTO WeatherCondition (Time, SlopeName, WindSpeed) VALUES (TIMESTAMP '2024-02-26 11:00:00', 'Slope 4', 15);
INSERT INTO WeatherCondition (Time, SlopeName, WindSpeed) VALUES (TIMESTAMP '2024-02-26 13:00:00', 'Slope 5', 25);
INSERT INTO WeatherCondition (Time, SlopeName, WindSpeed) VALUES (TIMESTAMP '2024-02-27 09:00:00', 'Slope 1', 10);
INSERT INTO WeatherCondition (Time, SlopeName, WindSpeed) VALUES (TIMESTAMP '2024-02-27 09:00:00', 'Slope 2', 5);
INSERT INTO WeatherCondition (Time, SlopeName, WindSpeed) VALUES (TIMESTAMP '2024-02-27 11:00:00', 'Slope 3', 15);
INSERT INTO WeatherCondition (Time, SlopeName, WindSpeed) VALUES (TIMESTAMP '2024-02-27 11:00:00', 'Slope 4', 10);
INSERT INTO WeatherCondition (Time, SlopeName, WindSpeed) VALUES (TIMESTAMP '2024-02-27 13:00:00', 'Slope 5', 30);
INSERT INTO WeatherCondition (Time, SlopeName, WindSpeed) VALUES (TIMESTAMP '2024-02-28 09:00:00', 'Slope 1', 5);
INSERT INTO WeatherCondition (Time, SlopeName, WindSpeed) VALUES (TIMESTAMP '2024-02-28 09:00:00', 'Slope 2', 5);
INSERT INTO WeatherCondition (Time, SlopeName, WindSpeed) VALUES (TIMESTAMP '2024-02-28 11:00:00', 'Slope 3', 10);
INSERT INTO WeatherCondition (Time, SlopeName, WindSpeed) VALUES (TIMESTAMP '2024-02-28 11:00:00', 'Slope 4', 30);
INSERT INTO WeatherCondition (Time, SlopeName, WindSpeed) VALUES (TIMESTAMP '2024-02-28 13:00:00', 'Slope 5', 35);


-- changed Data attribute to IncidentDate
CREATE TABLE IncidentsOccurs (
                                 IncidentID INT PRIMARY KEY,
                                 IncidentDate DATE,
                                 Description VARCHAR(255),
                                 SlopeName VARCHAR(255) NOT NULL,
                                 SkierID INT NOT NULL,
                                 FOREIGN KEY (SlopeName) REFERENCES SkiSlope(SlopeName),
                                 FOREIGN KEY (SkierID) REFERENCES Skier(SkierID)
);

INSERT INTO IncidentsOccurs (IncidentID, IncidentDate, Description, SlopeName, SkierID) VALUES (1, TO_DATE('2023-11-01', 'YYYY-MM-DD'), 'broke leg', 'Slope 1', 1);
INSERT INTO IncidentsOccurs (IncidentID, IncidentDate, Description, SlopeName, SkierID) VALUES (2, TO_DATE('2023-11-21', 'YYYY-MM-DD'), 'broke arm', 'Slope 2', 3);
INSERT INTO IncidentsOccurs (IncidentID, IncidentDate, Description, SlopeName, SkierID) VALUES (3, TO_DATE('2023-12-25', 'YYYY-MM-DD'), 'broke bone', 'Slope 2', 4);
INSERT INTO IncidentsOccurs (IncidentID, IncidentDate, Description, SlopeName, SkierID) VALUES (4, TO_DATE('2023-12-26', 'YYYY-MM-DD'), 'broke head', 'Slope 5', 1);
INSERT INTO IncidentsOccurs (IncidentID, IncidentDate, Description, SlopeName, SkierID) VALUES (5, TO_DATE('2023-11-03', 'YYYY-MM-DD'), 'broke leg', 'Slope 1', 4);
INSERT INTO IncidentsOccurs (IncidentID, IncidentDate, Description, SlopeName, SkierID) VALUES (6, TO_DATE('2023-11-05', 'YYYY-MM-DD'), 'heart attack', 'Slope 3', 2);
INSERT INTO IncidentsOccurs (IncidentID, IncidentDate, Description, SlopeName, SkierID) VALUES (7, TO_DATE('2023-11-01', 'YYYY-MM-DD'), 'broke arm', 'Slope 2', 3);

-- need assertions to check for total participation on SkiPass and SkiSlope(every ski pass and ski slope must participate in here)
CREATE TABLE CanAccess (
                           PassNumber INT,
                           SlopeName VARCHAR(255),
                           PRIMARY KEY (PassNumber, SlopeName),
                           FOREIGN KEY (PassNumber) REFERENCES SkiPass(PassNumber),
                           FOREIGN KEY (SlopeName) REFERENCES SkiSlope(SlopeName)
);

INSERT ALL
    INTO CanAccess (PassNumber, SlopeName) VALUES (101, 'Slope 1')
INTO CanAccess (PassNumber, SlopeName) VALUES (102, 'Slope 2')
INTO CanAccess (PassNumber, SlopeName) VALUES (103, 'Slope 3')
INTO CanAccess (PassNumber, SlopeName) VALUES (104, 'Slope 4')
INTO CanAccess (PassNumber, SlopeName) VALUES (105, 'Slope 5')
SELECT * FROM dual;

-- need assertion to check total participation on Skier(every Skier must participate)
CREATE TABLE Buys (
                      SkierID INT,
                      PassNumber INT,
                      PRIMARY KEY (SkierID, PassNumber),
                      FOREIGN KEY (SkierID) REFERENCES Skier(SkierID),
                      FOREIGN KEY (PassNumber) REFERENCES SkiPass(PassNumber)
);

INSERT ALL
    INTO Buys (SkierID, PassNumber) VALUES (1, 101)
INTO Buys (SkierID, PassNumber) VALUES (2, 102)
INTO Buys (SkierID, PassNumber) VALUES (3, 103)
INTO Buys (SkierID, PassNumber) VALUES (4, 104)
INTO Buys (SkierID, PassNumber) VALUES (5, 105)
SELECT * FROM dual;

CREATE TABLE Takes (
                       SkierID INT,
                       InstructorName VARCHAR(255),
                       LessonDate DATE,
                       LessonTime TIMESTAMP,
                       PRIMARY KEY (SkierID, InstructorName, LessonDate, LessonTime),
                       FOREIGN KEY (SkierID) REFERENCES Skier(SkierID),
                       FOREIGN KEY (InstructorName, LessonDate, LessonTime) REFERENCES Lesson(InstructorName, LessonDate, LessonTime)
);

INSERT INTO Takes (SkierID, InstructorName, LessonDate, LessonTime) VALUES (1, 'Emily Johnson', DATE '2024-03-01', TIMESTAMP '2024-03-01 09:00:00');
INSERT INTO Takes (SkierID, InstructorName, LessonDate, LessonTime) VALUES (2, 'Michael Smith', DATE '2024-03-01', TIMESTAMP '2024-03-01 10:00:00');
INSERT INTO Takes (SkierID, InstructorName, LessonDate, LessonTime) VALUES (3, 'Sophia Brown', DATE '2024-03-02', TIMESTAMP '2024-03-02 11:00:00');
INSERT INTO Takes (SkierID, InstructorName, LessonDate, LessonTime) VALUES (4, 'Daniel Garcia', DATE '2024-03-02', TIMESTAMP '2024-03-02 12:00:00');
INSERT INTO Takes (SkierID, InstructorName, LessonDate, LessonTime) VALUES (5, 'Olivia Martinez', DATE '2024-03-03', TIMESTAMP '2024-03-03 13:00:00');

