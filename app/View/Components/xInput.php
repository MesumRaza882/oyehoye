<?php

namespace App\View\Components;

use Illuminate\View\Component;

class xInput extends Component
{
    // public $label;
    // public $type;
    // public $min;
    // public $name;
    // public $required;
    // public $placeholder;
    // public $id;
    // public $for;
    // public $class;
    // public $accept;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->label = $label;
        // $this->type = $type;
        // $this->name = $name;
        // $this->required = $required;
        // $this->placeholder = $placeholder;
        // $this->id = $id;
        // $this->for = $for;
        // $this->min = $min;
        // $this->class = $class;
        // $this->accept = $accept;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.x-input');
    }
}
