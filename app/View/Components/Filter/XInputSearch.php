<?php

namespace App\View\Components\Filter;

use Illuminate\View\Component;

class XInputSearch extends Component
{
    public $label;
    public $name;
    public $class;
    public $placeholder;
    public $value;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct( $name, $class, $placeholder, $value, $label = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->class = $class;
        $this->placeholder = $placeholder;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.filter.x-input-search');
    }
}
