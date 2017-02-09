# Shopping cart component

Shopping cart component includes a fully intergratable shopping cart with lots of rich features.

**Laravel 5.4 Support for version 0.2.0**

## Installation

To install through composer simply use the following command:

```PHP
composer require mathewberry/cart
```

Next add 

```PHP
Cart` => \Mathewberry\Cart\Facades\Cart::class
```

to ```config/app.php```

## Features

- Get the subtotal
- Get the total
- Get the delivery price
- Get the voucher price
- Get the quantity
- Get the cart total
- Add a product
- Add a voucher
- Check if the cart has a product
- Update the cart
- Remove an item from the cart
- Clear the cart

## Usage

### Get product

```PHP 
$product = Cart::get($product_id);
```

#### Array Structure
```PHP
// Example
[
    "id" => 24,
    "price" => 99.99,
    "quantity" => 2,
    "options" => [...]
]
```

#### Displaying
```PHP
{{ $product['options']['name'] }} <small>{{ $product['options']['model'] }}</small><br>
<img src="{{ $product['options']['image'] }}">
<a href="{{ route('products', ['id' => $product['id']]) }}"><br>
```

#### Usages

### Get all products

```PHP
$products = Cart::content();
```

#### Array Structure
```PHP
// Example
[
    [
        "id" => 24,
        "price" => 99.99,
        "quantity" => 2,
        "options" => [...]
    ],
    [
        "id" => 34,
        "price" => 79.99,
        "quantity" => 1,
        "options" => [...]
    ]
]
```


### Get the subtotal

```PHP
// Return the total with shipping or voucher
Cart::subtotal();
```

### Add a new product

```PHP
/* DATABASE ROW
 *
 * Id: 24
 * Name: 'My Product'
 * Model: 'AAA900'
 * Price: 99.99
 *
 */

$product = \App\Models\Product::select(['id', 'model', 'price'])->find(24);
$product_id = $product->id;
$price = $product->price;
$quantity = 1;

// You may add any options you wish.
$options = [
     'name' => $product->name,
     'image' => asset('images/my-product-image.jpg'),
     'model' => $product->model
];

Cart::add($product_id, $price, $quantity, $options)
```

### Get the total

```PHP
// Returns the total of the cart
Cart::total();
```

### Get the delivery cost

```PHP
// Returns the delivery cost
Cart::delivery();
```

### Set the voucher

```PHP
// Example Data
$id = 24;
$code = "MB25OFF"; // 25% Off
$discount = 25.00; // 25%
$is_fixed = false; // Percentage
$display = '-' . number_format($discount, 2) . '%'; // How it should be displayed to the customer.

Cart::voucher($id, $code, $discount, $is_fixed, $display);
```

### Get the voucher

```PHP
Cart::voucher();
```

#### Structure

```PHP
[
    "id" => $voucher_id, // Voucher id
    "code" => $voucher_code, // Voucher code
    "display" => $voucher_display, // Customer friendly, e.g: "25%"
    "is_fixed" => $voucher_fixed, // Percentage voucher or fixed price
    "discount" => $voucher_discount_value // Value of voucher
]
```

#### Displaying

```PHP
Code: {{ Cart::voucher()['code'] }}<br>
Deducted: {{ Cart::voucher()['display'] }}
```

### Has product

```PHP
// Returns true or false whether or not the product exists in the cart.
Cart::has($product_id);
```

### Get the total of a product

```PHP
// Calculates the product price times quantity.
Cart::sum($product_id);
```

### Get the quantity of the cart

```PHP
// Return a list of all the products in the cart.
Cart::products();
```

### Update cart item

```PHP
// Add one to the current quantity
Cart::update($product_id);
```
```PHP
// Custom quantity to add to existing quantity
Cart::update($product_id, $quantity);
```

### Remove from cart

```PHP
// Remove a specific product from the cart.
Cart::remove($product_id);
```

### Clear the cart

```PHP
// Clear the cart of ALL of it's data.
Cart::clear();
```
