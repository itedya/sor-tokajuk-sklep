CREATE TABLE `users`
(
    `id`          int          NOT NULL AUTO_INCREMENT,
    `email`       varchar(255) NOT NULL,
    `is_verified` boolean      NOT NULL DEFAULT FALSE,
    `password`    varchar(255) NOT NULL,
    `created_at`  datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_users_id PRIMARY KEY (`id`),
    CONSTRAINT uq_users_email UNIQUE (`email`)
);
CREATE TABLE `email_verification_attempts`
(
    `id`      int          NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `user_id` int          NOT NULL,
    `hash`    varchar(255) NOT NULL,

    CONSTRAINT UNIQUE KEY `uq_email_verification_attempts_hash` (`hash`),
    CONSTRAINT FOREIGN KEY `fk_email_verification_attempts_user_id` (`user_id`) REFERENCES `users` (`id`)
);
