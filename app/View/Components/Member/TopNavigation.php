<?php

namespace App\View\Components\Member;

use App\Models\Member;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TopNavigation extends Component
{
    public $member;
    /**
     * Create a new component instance.
     */
    public function __construct(Member $member)
    {
        $this->member = $member;
    }
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.member.top-navigation');
    }
}
