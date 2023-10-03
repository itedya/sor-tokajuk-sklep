CREATE TABLE `users`
(
    `id`              int          NOT NULL AUTO_INCREMENT,
    `email`           varchar(255) NOT NULL,
    `is_verified`     boolean NOT NULL DEFAULT FALSE,
    `password`           varchar(255) NOT NULL,
    `created_at` datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_users_id PRIMARY KEY (`id`),
    CONSTRAINT uq_users_email UNIQUE (`email`)
);
CREATE TABLE `email_verification_attempts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `email_verification_attempts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_email_verification_attempts_hash` (`hash`),
  ADD KEY `fk_email_verification_attempts_user_id` (`user_id`);