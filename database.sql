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
    `id`          int           NOT NULL AUTO_INCREMENT,
    `name`        varchar(255)  NOT NULL,
    `description` text          NOT NULL,
    `category_id` int           NOT NULL,
    `price`       DECIMAL(6, 2) NOT NULL,
    `created_at`  datetime      NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT `pk_products_id` PRIMARY KEY (`id`)
);

CREATE TABLE `products_images`
(
    `id`         int          NOT NULL AUTO_INCREMENT,
    `product_id` int          NOT NULL,
    `image`      varchar(255) NOT NULL,
    `created_at` datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT `pk_products_images_id` PRIMARY KEY (`id`),
    CONSTRAINT `fk_products_images_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
    CONSTRAINT `uq_products_images_image` UNIQUE KEY (`image`)
);

CREATE TABLE `parameters`
(
    `id`         VARCHAR(64) NOT NULL,
    `name`       varchar(32) NOT NULL,
    `created_at` datetime    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT `pk_parameters_id` PRIMARY KEY (`id`),
    CONSTRAINT `uq_parameters_name` UNIQUE KEY (`name`)
);

CREATE TABLE `products_have_parameters`
(
    `parameter_id` VARCHAR(64)  NOT NULL,
    `product_id`   int          NOT NULL,
    `value`        varchar(128) NOT NULL
);

CREATE TABLE `delivery_methods`
(
    id         int           NOT NULL AUTO_INCREMENT,
    name       varchar(255)  NOT NULL,
    price      DECIMAL(6, 2) NOT NULL,
    created_at datetime      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at datetime               DEFAULT NULL,

    CONSTRAINT `pk_delivery_methods_id` PRIMARY KEY (`id`),
    CONSTRAINT `uq_delivery_methods_name` UNIQUE KEY (`name`)
);

CREATE TABLE `addresses`
(
    `id`          int          NOT NULL AUTO_INCREMENT,
    `user_id`     int          NOT NULL,
    `first_line`  varchar(255) NOT NULL,
    `second_line` varchar(255) NOT NULL,
    `city`        varchar(255) NOT NULL,
    `postal_code` varchar(255) NOT NULL,
    `created_at`  datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at`  datetime              DEFAULT NULL,

    CONSTRAINT `pk_addresses_id` PRIMARY KEY (`id`),
    CONSTRAINT `fk_addresses_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
);

CREATE TABLE `payment_types`
(
    `id`         int          NOT NULL AUTO_INCREMENT,
    `name`       varchar(255) NOT NULL,
    `created_at` datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` datetime              DEFAULT NULL,

    CONSTRAINT `pk_payment_types_id` PRIMARY KEY (`id`)
);

CREATE TABLE `orders`
(
    `id`                  int      NOT NULL AUTO_INCREMENT,
    `user_id`             int      NOT NULL,
    `status`              int      NOT NULL,
    `delivery_method_id`  int      NOT NULL,
    `delivery_address_id` int      NOT NULL,
    `payment_type_id`     int      NOT NULL,
    `address_id`          int      NOT NULL,
    `created_at`          datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT `pk_orders_id` PRIMARY KEY (`id`),
    CONSTRAINT `fk_orders_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
    CONSTRAINT `fk_orders_delivery_method_id` FOREIGN KEY (`delivery_method_id`) REFERENCES `delivery_methods` (`id`),
    CONSTRAINT `fk_orders_address_id` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`),
    CONSTRAINT `fk_orders_payment_type_id` FOREIGN KEY (`payment_type_id`) REFERENCES `payment_types` (`id`)
);

CREATE TABLE `orders_have_products`
(
    `order_id`   int NOT NULL,
    `product_id` int NOT NULL,
    `quantity`   int NOT NULL,

    CONSTRAINT `pk_orders_have_products_order_id_product_id` PRIMARY KEY (`order_id`, `product_id`),
    CONSTRAINT `fk_orders_have_products_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
    CONSTRAINT `fk_orders_have_products_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
);

CREATE TABLE `users_favourite_products`
(
    `user_id`    int NOT NULL,
    `product_id` int NOT NULL,

    CONSTRAINT `pk_users_favourite_products_user_id_product_id` PRIMARY KEY (`user_id`, `product_id`),
    CONSTRAINT `fk_users_favourite_products_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
    CONSTRAINT `fk_users_favourite_products_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
);
