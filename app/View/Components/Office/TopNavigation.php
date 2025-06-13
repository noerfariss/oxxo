<?php

namespace App\View\Components\Office;

use App\Models\Office;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TopNavigation extends Component
{
    public $office;
    /**
     * Create a new component instance.
     */
    public function __construct(Office $office)
    {
        $this->office = $office;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.office.top-navigation');
    }
}
