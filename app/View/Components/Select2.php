<?php

namespace App\View\Components;


use Illuminate\View\Component;

class Select2 extends Component
{

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public string $variable)
    {

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.Select2');
    }

}