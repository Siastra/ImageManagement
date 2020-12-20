-- Generiert von Oracle SQL Developer Data Modeler 20.3.0.283.0710
--   am/um:        2020-12-20 12:18:44 MEZ
--   Site:      Oracle Database 11g
--   Typ:      Oracle Database 11g



-- predefined type, no DDL - MDSYS.SDO_GEOMETRY

-- predefined type, no DDL - XMLTYPE

CREATE TABLE `Comment` (
    post_id  INTEGER NOT NULL,
    user_id  INTEGER NOT NULL,
    text     VARCHAR(128) NOT NULL
);

ALTER TABLE `Comment` ADD CONSTRAINT comment_pk PRIMARY KEY ( post_id,
                                                              user_id );

CREATE TABLE is_assigned (
    post_id   INTEGER NOT NULL,
    tag_name  VARCHAR(32) NOT NULL
);

ALTER TABLE is_assigned ADD CONSTRAINT is_assigned_pk PRIMARY KEY ( post_id,
                                                                    tag_name );

CREATE TABLE Post (
    id          INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    path        VARCHAR(128) NOT NULL,
    restricted  CHAR(1) NOT NULL,
    user_id     INTEGER NOT NULL
);

CREATE TABLE Rating (
    user_id  INTEGER NOT NULL,
    post_id  INTEGER NOT NULL,
    type     CHAR(1) NOT NULL
);

ALTER TABLE Rating ADD CONSTRAINT rating_pk PRIMARY KEY ( user_id,
                                                          post_id );

CREATE TABLE tag (
    name VARCHAR(32) NOT NULL
);

ALTER TABLE Tag ADD CONSTRAINT tag_pk PRIMARY KEY ( name );

CREATE TABLE `User` (
    id        INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    title     VARCHAR(8) NOT NULL,
    fname     VARCHAR(32) NOT NULL,
    lname     VARCHAR(32) NOT NULL,
    email     VARCHAR(64) NOT NULL,
    username  VARCHAR(32) NOT NULL,
    password  VARCHAR(128) NOT NULL,
    admin     CHAR(1) NOT NULL
);

ALTER TABLE `Comment`
    ADD CONSTRAINT comment_post_fk FOREIGN KEY ( post_id )
        REFERENCES Post ( id );

ALTER TABLE `Comment`
    ADD CONSTRAINT comment_user_fk FOREIGN KEY ( user_id )
        REFERENCES `User` ( id );

ALTER TABLE is_assigned
    ADD CONSTRAINT is_assigned_post_fk FOREIGN KEY ( post_id )
        REFERENCES Post ( id );

ALTER TABLE is_assigned
    ADD CONSTRAINT is_assigned_tag_fk FOREIGN KEY ( tag_name )
        REFERENCES Tag ( name );

ALTER TABLE Post
    ADD CONSTRAINT post_user_fk FOREIGN KEY ( user_id )
        REFERENCES `User` ( id );

ALTER TABLE Rating
    ADD CONSTRAINT rating_post_fk FOREIGN KEY ( post_id )
        REFERENCES post ( id );

ALTER TABLE Rating
    ADD CONSTRAINT rating_user_fk FOREIGN KEY ( user_id )
        REFERENCES `User` ( id );



-- Zusammenfassungsbericht f�r Oracle SQL Developer Data Modeler: 
-- 
-- CREATE TABLE                             6
-- CREATE INDEX                             0
-- ALTER TABLE                             13
-- CREATE VIEW                              0
-- ALTER VIEW                               0
-- CREATE PACKAGE                           0
-- CREATE PACKAGE BODY                      0
-- CREATE PROCEDURE                         0
-- CREATE FUNCTION                          0
-- CREATE TRIGGER                           0
-- ALTER TRIGGER                            0
-- CREATE COLLECTION TYPE                   0
-- CREATE STRUCTURED TYPE                   0
-- CREATE STRUCTURED TYPE BODY              0
-- CREATE CLUSTER                           0
-- CREATE CONTEXT                           0
-- CREATE DATABASE                          0
-- CREATE DIMENSION                         0
-- CREATE DIRECTORY                         0
-- CREATE DISK GROUP                        0
-- CREATE ROLE                              0
-- CREATE ROLLBACK SEGMENT                  0
-- CREATE SEQUENCE                          0
-- CREATE MATERIALIZED VIEW                 0
-- CREATE MATERIALIZED VIEW LOG             0
-- CREATE SYNONYM                           0
-- CREATE TABLESPACE                        0
-- CREATE USER                              0
-- 
-- DROP TABLESPACE                          0
-- DROP DATABASE                            0
-- 
-- REDACTION POLICY                         0
-- 
-- ORDS DROP SCHEMA                         0
-- ORDS ENABLE SCHEMA                       0
-- ORDS ENABLE OBJECT                       0
-- 
-- ERRORS                                   0
-- WARNINGS                                 0
