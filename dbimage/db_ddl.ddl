DROP TABLE `Comment`;
DROP TABLE is_assigned;
DROP TABLE Rating;
DROP TABLE tag;
DROP TABLE Post;
DROP TABLE `User`;

CREATE TABLE `Comment` (
    id        INTEGER NOT NULL AUTO_INCREMENT,
    post_id   INTEGER NOT NULL,
    user_id   INTEGER NOT NULL,
    text      VARCHAR(128) NOT NULL,
    createdAt DATE NOT NULL,
    PRIMARY KEY(id)
);


CREATE TABLE is_assigned (
    post_id   INTEGER NOT NULL,
    tag_name  VARCHAR(32) NOT NULL,
    PRIMARY KEY(post_id, tag_name)
);


CREATE TABLE Post (
    id          INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    title       VARCHAR(64) NOT NULL,
    path        VARCHAR(128) NOT NULL,
    restricted  CHAR(1) NOT NULL,
    user_id     INTEGER NOT NULL,
    createdAt   DATE NOT NULL,
    text        VARCHAR(128) NOT NULL
);

CREATE TABLE Rating (
    user_id  INTEGER NOT NULL,
    post_id  INTEGER NOT NULL,
    type     CHAR(1) NOT NULL,
    PRIMARY KEY(user_id, post_id)
);

CREATE TABLE tag (
    name VARCHAR(32) NOT NULL PRIMARY KEY
);

CREATE TABLE `User` (
    id        INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    title     VARCHAR(8) NOT NULL,
    fname     VARCHAR(32) NOT NULL,
    lname     VARCHAR(32) NOT NULL,
    email     VARCHAR(64) NOT NULL,
    username  VARCHAR(32) NOT NULL,
    password  VARCHAR(128) NOT NULL,
    admin     CHAR(1) NOT NULL,
    activated CHAR(1) NOT NULL,
    picture   VARCHAR(64) NOT NULL,
    UNIQUE(username)
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
