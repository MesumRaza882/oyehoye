<?php

namespace App\View\Components;

use Illuminate\View\Component;

class XSelect extends Component
{
    public $label;
    public $name;
    public $options;
    public $required;
    public $placeholder;
    public $selected;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($label, $name, $options, $required = false, $placeholder = 'Select',$selected = null)
    {
        $this->label = $label;
        $this->name = $name;
        $this->options = $options;
        $this->required = $required;
        $this->placeholder = $placeholder;
        $this->selected = $selected;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.x-select');
    }
}
