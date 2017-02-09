<?php

namespace Mathewberry\Cart\Contracts;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class Cart {

    /**
     * The current customers session
     *
     * @var \Illuminate\Session\Store
     */
    protected $_session;

    /**
     * The subtotal of the cart.
     *
     * @var float
     */
    protected $_subtotal = 0.00;

    /**
     * The actual total of the cart.
     *
     * @var float
     */
    protected $_total = 0.00;

    /**
     * The delivery price, if the cart has one.
     *
     * @var float
     */
    protected $_delivery = 0.00;

    /**
     * The voucher price, if the cart has one.
     *
     * @var bool
     */
    protected $_voucher = false;

    /**
     * The content of the cart.
     *
     * @var array
     */
    protected $_content = [];

    /**
     * The quantity of the basket.
     *
     * @var int
     */
    protected $_quantity = 0;

    /**
     * Cart constructor.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function __construct(Request $request)
    {
        $this->_session = $request->session();

        if(!$this->_session->has('mathewberry.cart')) {
            $this->_session->set('mathewberry.cart', [
                'content' => [],
                'quantity' => $this->_quantity,
                'voucher' => $this->_voucher,
                'shipping' => $this->_delivery,
                'total' => $this->_total,
                'subtotal' => $this->_subtotal
            ]);
        } else {
            $this->setContent();

            $this->calculateTotal();
        }
    }

    /**
     * Retrieve the contents of the cart.
     *
     * @return  mixed
     */
    public function content()
    {
        return $this->_content;
    }

    /**
     * Retrieve the contents of a specific product.
     *
     * @param  int  $product_id
     *
     * @return  mixed
     */
    public function get($product_id)
    {
        $key = $this->identifier($product_id);

        if($key !== false) {
            return $this->_content[$key];
        }

        return false;
    }

    /**
     * Check if the item is already added to the cart.
     *
     * @param  $product_id
     *
     * @return  boolean
     */
    public function has($product_id)
    {
        $key = $this->identifier($product_id);

        if($key !== false) {
            return true;
        }

        return false;
    }

    /**
     * Get the total of the shopping cart.
     *
     * @param  $product_id
     *
     * @return  mixed
     */
    public function sum($product_id)
    {
        $product = $this->get($product_id);

        return $product['price'] * $product['quantity'];
    }

    /**
     * Add a product to the cart.
     *
     * @param  int  $product_id
     * @param  float  $price
     * @param  int  $quantity
     * @param  array  $optional
     *
     * @return bool
     */
    public function add($product_id, $price = 0.10, $quantity = 1, array $optional = [])
    {
        $this->_content = array_values($this->_content);

        $key = $this->identifier($product_id);

        if($key !== false) {

            $this->update($product_id);

            return false;
        }

        unset($key);

        $detail[] = [
            'id'       => $product_id,
            'price'    => $price,
            'quantity' => $quantity,
            'options'  => $optional
        ];

        $this->_content = array_merge($this->_content, $detail);

        $this->_quantity = $this->_quantity + 1;

        $this->_session->set('mathewberry.cart.content', $this->_content);

        $this->calculateTotal();

        return true;
    }

    /**
     * Update the cart quantity.
     *
     * @param  int  $product_id
     * @param  null  $quantity
     */
    public function update($product_id, $quantity = null)
    {
        $key = $this->identifier($product_id);

        if(empty($quantity)) {
            $quantity = $this->_content[$key]['quantity'] + 1;
        }

        $this->_content[$key]['quantity'] = $quantity;

        $this->_quantity = $quantity;

        $this->_session->set('mathewberry.cart.content', $this->_content);

        $this->calculateTotal();
    }

    /**
     * Remove a product from the cart.
     *
     * @param  int  $product_id
     *
     * @return bool
     */
    public function remove($product_id)
    {
        $key = $this->identifier($product_id);

        $product = $this->_content[$key];

        unset($this->_content[$key]);

        $this->_quantity = $this->_quantity - $product['quantity'];

        $this->_session->set('mathewberry.cart.content', $this->_content);

        $this->calculateTotal();

        return true;
    }

    /**
     * Retrieve the subtotal of the cart.
     *
     * @return mixed
     */
    public function subtotal()
    {
        return number_format($this->_subtotal, 2);
    }

    /**
     * Retrieve the total price of the cart.
     *
     * @return mixed
     */
    public function total()
    {
        return number_format($this->_total, 2);
    }

    /**
     * Retrieve the delivery price.
     *
     * @return mixed
     */
    public function delivery()
    {
        return number_format($this->_delivery, 2);
    }

    /**
     * Retrieve the discount price.
     *
     * @return mixed
     */
    public function voucher()
    {
        return $this->_voucher;
    }

    /**
     * Retrieve the quantity of the cart.
     *
     * @return mixed
     */
    public function products()
    {
        return $this->_quantity;
    }

    /**
     * Clear the basket ready to start over.
     */
    public function clear($keepProducts = false)
    {
        if($keepProducts) {
            $this->_session->set('mathewberry.cart.voucher', false);
        } else {
            $this->_session->forget('mathewberry.cart');
            $this->_session->set('mathewberry.cart', [
                'content' => [],
                'quantity' => 0,
                'voucher' => false,
                'shipping' => 0.00,
                'total' => 0.00,
                'subtotal' => 0.00
            ]);
        }

    }

    /**
     * Set the voucher discount amount.
     *
     * @param  int  $id
     * @param  string  $code
     * @param  float  $discount
     * @param  boolean  $is_fixed
     * @param  boolean  $display
     */
    public function setVoucher($id, $code, $discount, $is_fixed, $display)
    {
        $this->_voucher = [
            'id' => $id,
            'code' => $code,
            'display' => $display,
            'is_fixed' => $is_fixed ? true : false,
            'discount' => number_format($discount, 2),
            'value' => null
        ];

        $this->_session->set('mathewberry.cart.voucher', $this->_voucher);

        $this->calculateVoucher();
    }

    /**
     * Set the content of the basket.
     */
    private function setContent()
    {
        $this->_content = $this->_session->get('mathewberry.cart.content');
    }

    /**
     * Calculate the total of the basket.
     *
     * @return boolean
     */
    private function calculateTotal()
    {
        $this->calculateQuantity();

        $this->calculateSubtotal();

        $this->calculateVoucher();

        $this->calculateDelivery();

        $this->_total = ($this->_subtotal - (isset($this->_voucher['value']) ? $this->_voucher['value'] : 0)) + $this->_delivery;

        $this->_session->set('mathewberry.cart.total', number_format($this->_total, 2));

        return true;
    }

    /**
     * Calculate the quantity of the basket.
     */
    private function calculateQuantity()
    {
        $this->_quantity = array_sum(array_map(function($item) {
            return $item['quantity'];
        }, $this->_content));

        $this->_session->set('mathewberry.cart.quantity', $this->_quantity);
    }

    /**
     * Calculate the subtotal of the basket.
     * This value is after tax!
     */
    private function calculateSubtotal()
    {
        $this->_subtotal = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $this->_content));

        $this->_session->set('mathewberry.cart.subtotal', number_format($this->_subtotal, 2));
    }

    /**
     * Calculate the delivery charge if required.
     */
    private function calculateDelivery()
    {
        if($this->_subtotal > Config::get('mathewberry.cart.free_shipping_after')) {
            $this->_delivery = 0.00;
        } else {
            $this->_delivery = Config::get('mathewberry.cart.shipping_cost');
        }
    }

    /**
     * Calculate the value of the voucher.
     */
    private function calculateVoucher()
    {
        $this->_voucher = $this->_session->get('mathewberry.cart.voucher');

        if(empty($this->_voucher)) {
            return false;
        }

        if(!$this->_voucher['is_fixed']) {
            $this->_voucher['value'] = $value = number_format(($this->_subtotal / 100) * $this->_voucher['discount'], 2);
        } else {
            $this->_voucher['value'] = $this->_voucher['discount'];
        }

        $this->_session->set('mathewberry.cart.voucher', $this->_voucher);
    }

    /**
     * @param $product_id
     * @return int|bool
     */
    private function identifier($product_id)
    {
        foreach($this->_content as $key => $product) {
            if($product['id'] == $product_id) {
                return $key;
            }
        }
        return false;
    }
}