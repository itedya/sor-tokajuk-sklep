CREATE TABLE `users`
(
    `id`              int          NOT NULL AUTO_INCREMENT,
    `email`           varchar(255) NOT NULL,
    `haslo`           varchar(255) NOT NULL,
    `data_utworzenia` datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_users_id PRIMARY KEY (`id`),
    CONSTRAINT uq_users_email UNIQUE (`email`)
);