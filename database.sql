CREATE TABLE `users`
(
    `id`          int          NOT NULL AUTO_INCREMENT,
    `email`       varchar(255) NOT NULL,
    `is_verified` boolean      NOT NULL DEFAULT FALSE,
    `is_admin`    boolean      NOT NULL DEFAULT FALSE,
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

CREATE TABLE `password_resets`
(
    `uuid`                 varchar(64),
    `user_id`              int          NOT NULL,
    `created_at_timestamp` int unsigned NOT NULL,

    CONSTRAINT PRIMARY KEY `pk_password_resets_uuid` (`uuid`),
    CONSTRAINT FOREIGN KEY `fk_password_resets_user_id` (`user_id`) REFERENCES `users` (`id`)
);

CREATE TABLE `categories`
(
    `id`   int          NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,

    CONSTRAINT pk_categories_id PRIMARY KEY (`id`),
    CONSTRAINT UNIQUE KEY `uq_categories_name` (`name`)
);


CREATE TABLE `products`
(
    `id`          int          NOT NULL AUTO_INCREMENT,
    `name`        varchar(255) NOT NULL,
    `description` text         NOT NULL,
    `category_id` int          NOT NULL,
    `price`       int          NOT NULL,
    `created_at`  datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_products_id PRIMARY KEY (`id`)
);

CREATE TABLE `products_images`
(
    `id`         int          NOT NULL AUTO_INCREMENT,
    `product_id` int          NOT NULL,
    `image`      varchar(255) NOT NULL,
    `created_at` datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_products_images_id PRIMARY KEY (`id`),
    CONSTRAINT FOREIGN KEY `fk_products_images_product_id` (`product_id`) REFERENCES `products` (`id`),
    CONSTRAINT UNIQUE KEY `uq_products_images_image` (`image`)
);

CREATE TABLE `parameters`
(
    `id`         VARCHAR(64) NOT NULL,
    `name`       varchar(32) NOT NULL,
    `created_at` datetime    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT PRIMARY KEY `pk_parameters_id` (`id`),
    CONSTRAINT UNIQUE KEY `uq_parameters_name` (`name`)
);

CREATE TABLE `products_have_parameters`
(
    `parameter_id` VARCHAR(64)  NOT NULL,
    `product_id`   int          NOT NULL,
    `value`        varchar(128) NOT NULL
);

CREATE TABLE `orders`
(
    `id`                 int      NOT NULL AUTO_INCREMENT,
    `user_id`            int      NOT NULL,
    `status`             int      NOT NULL,
    `delivery_method_id` int      NOT NULL,
    `created_at`         datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT PRIMARY KEY `pk_orders_id` (`id`),
    CONSTRAINT FOREIGN KEY `fk_orders_user_id` (`user_id`) REFERENCES `users` (`id`)
);

CREATE TABLE `orders_have_products`
(
    `order_id`   int NOT NULL,
    `product_id` int NOT NULL,
    `quantity`   int NOT NULL,

    CONSTRAINT PRIMARY KEY `pk_orders_have_products_order_id_product_id` (`order_id`, `product_id`),
    CONSTRAINT FOREIGN KEY `fk_orders_have_products_order_id` (`order_id`) REFERENCES `orders` (`id`),
    CONSTRAINT FOREIGN KEY `fk_orders_have_products_product_id` (`product_id`) REFERENCES `products` (`id`)
);

CREATE TABLE `delivery_methods`
(
    id         int          NOT NULL AUTO_INCREMENT,
    name       varchar(255) NOT NULL,
    price      int          NOT NULL,
    created_at datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at datetime              DEFAULT NULL,

    CONSTRAINT pk_delivery_methods_id PRIMARY KEY (`id`),
    CONSTRAINT UNIQUE KEY `uq_delivery_methods_name` (`name`)
);