<?php

namespace App\View\Components\Filter;

use Illuminate\View\Component;

class XRecordSelect extends Component
{
    public $label;
    public $name;
    public $class;
    public $options;
    public $selected;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct( $name, $options, $selected, $label = null, $class= null,)
    {
        $this->label = $label;
        $this->name = $name;
        $this->class = $class;
        $this->options = $options;
        $this->selected = $selected;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.filter.x-record-select');
    }
}
