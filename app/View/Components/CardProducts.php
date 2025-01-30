<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CardProducts extends Component
{
    public $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function render()
    {
        return view('components.products.card-products');
    }
}
