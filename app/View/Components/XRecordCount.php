<?php

namespace App\View\Components;

use Illuminate\View\Component;

class XRecordCount extends Component
{
    public $totalRecords;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($totalRecords)
    {
        $this->totalRecords = $totalRecords;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.x-record-count');
    }
}
