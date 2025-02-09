<?php

namespace App\View\Components;

use App\Constants\ViewNames;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class GuestLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view(ViewNames::LAYOUTS_GUEST);
    }
}
