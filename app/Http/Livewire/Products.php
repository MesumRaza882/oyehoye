<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product;

class Products extends Component
{
    public $perPage = 12;
    public $product_name, $product_video;
    protected $listeners = [
        'load-more' => 'loadMore'
    ];

    public function render()
    {
        $products = Product::
        // latest('pinned_at')->orderby('id')->
        with('itemcategory:id')->paginate($this->perPage);
        $this->emit('productStore');
  
        return view('livewire.products', ['products' => $products]);
    }

    public function loadMore()
    {
        $this->perPage = $this->perPage + 6;
    }

    public function openProductVideoModal($id)
    {
        $product = Product::where('id',$id)->first(['id','video','name']);
        $this->product_name = $product->name;
        $this->product_video = $product->video;
        $this->dispatchBrowserEvent('openProductVideoModal');
    }
}
