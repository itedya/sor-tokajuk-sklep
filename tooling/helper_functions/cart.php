<?php

function cart_get_products(): array
{
    $rawItems = session_get('cart', []);
    $db = get_db_connection();

    $items = [];

    foreach ($rawItems as $item) {
        $product = database_products_get_by_id($db, $item['id']);
        if ($product === null) continue;

        $product['quantity'] = $item['quantity'];
        $items[] = $product;
    }

    $db->close();

    return $items;
}

function cart_get_total(): float
{
    $items = cart_get_products();
    $total = 0;

    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    return $total;
}

function cart_add_product(int $id, int $quantity = 1): void
{
    $items = session_get('cart', []);
    $index = array_search($id, array_column($items, 'id'));

    if ($index === false) {
        $items[] = ['id' => $id, 'quantity' => $quantity];
    } else {
        $items[$index]['quantity'] += $quantity;
    }

    session_set('cart', $items);
}

function cart_get_count(): int
{
    $items = session_get('cart', []);
    $count = 0;

    foreach ($items as $item) {
        $count += $item['quantity'];
    }

    return $count;
}