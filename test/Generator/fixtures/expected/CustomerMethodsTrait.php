<?php
// HEADER

namespace Hostnet\Component\AccessorGenerator\Generator\fixtures\Generated;

use Doctrine\ORM\Mapping as ORM;
use Hostnet\Component\AccessorGenerator\Annotation as AG;
use Hostnet\Component\AccessorGenerator\Generator\fixtures\Cart;
use Hostnet\Component\AccessorGenerator\Generator\fixtures\Customer;

trait CustomerMethodsTrait
{
    /**
     * Get cart
     *
     * @return Cart
     * @throws \InvalidArgumentException
     */
    public function getCart()
    {
        if (func_num_args() > 0) {
            throw new \BadMethodCallException(
                sprintf(
                    'getCart() has no arguments but %d given.',
                    func_num_args()
                )
            );
        }

        if ($this->cart === null) {
            throw new \Doctrine\ORM\EntityNotFoundException('Missing required property "cart".');
        }

        return $this->cart;
    }

    /**
     * Set cart
     *
     * @param Cart $cart
     * @return Customer
     * @throws \BadMethodCallException if the number of arguments is not correct
     */
    public function setCart(Cart $cart)
    {
        if (func_num_args() != 1) {
            throw new \BadMethodCallException(
                sprintf(
                    'setCart() has one argument but %d given.',
                    func_num_args()
                )
            );
        }
        if ($cart !== null && $this->cart !== $cart) {
            $this->cart = $cart;
            $method = new \ReflectionMethod($cart, 'setCustomer');
            $method->setAccessible(true);
            $method->invoke($cart, $this);
            $method->setAccessible(false);
        }
        return $this;
    }
}
