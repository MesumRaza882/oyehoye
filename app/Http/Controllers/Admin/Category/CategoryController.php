<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{category};
use App\Http\Requests\CategoryRule;
use Carbon\Carbon;
use App\Helpers\Helper;

class CategoryController extends Controller
{
    public function index(Request $req)
    {
        $paginate_record = $req->records ? $req->records :  10;
        $status = $req->status === 'all' ? '' : $req->status;
        $records  = category::withCount([
            'product as active' => function ($query) {
                $query->where('soldstatus', 1)->where('soldItem', '>=', 1);
            },
            'product as soldout' => function ($query) {
                $query->where('soldstatus', 0)->orwhere('soldItem', '<', 1);
            },
            'product'
        ])->orderByRaw('ISNULL(order_number), order_number ASC')
            ->WhereIf('name', 'Like', "%{$req->search_input}%")
            ->WhereIf('status', 'Like', "%{$status}%");
        $total_records = $records->count();
        $categories = $records->paginate($paginate_record);
        return view('admin.categories.index', compact('categories', 'total_records'));
    }

    public function store(CategoryRule $req)
    {
        $category = new category();
        $category->name = $req->category_name;
        if ($req->hasFile('image')) {
            $category->image = Helper::upload_image($req->file('image'), 'image/category');
        }
        $category->save();
        return $this->success('Category Added Successfully', 'Category Added Successfully', 2);
    }

    function edit($id)
    {
        $category = category::find($id);
        return view('admin.categories.edit', compact('category'));
    }

    //updatecategory
    public function update(Request $req, $id)
    {
        $req->validate([
            'name' => 'required',
            'status' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // active row when click
        category::where('is_active_row', 1)->update(['is_active_row' => 0]);

        $category = category::find($id);
        $category->update([
            'name' => $req->name,
            'status' => $req->status,
        ]);
        
        if ($req->hasFile('image')) {
            Helper::delete_previous_image($category->image);
            $category->image = Helper::upload_image($req->file('image'), 'image/category');
            $category->save();
        }

        session()->put('message', 'Category Updated Successfully');
        session()->put('end_time',  Carbon::now()->addSecond(5));
        echo '<script type="text/javascript">', 'history.go(-2);', '</script>';
    }

    function destroy(Request $req)
    {
        $category = category::find($req->RecordId);
        if ($category->image) {
            Helper::delete_previous_image($category->image);
        }
        $category->delete();
        return $this->success('Category Deleted Successfully', 'Category Deleted Successfully', 2);
    }

    public function deleteImage($id)
    {
        $category = Category::find($id);
        if ($category->image) {
            Helper::delete_previous_image($category->image);
            $category->update(['image' => null]);
        }
        return response()->json(['success' => true, 'message' => 'Category image deleted successfully.']);
    }

    public function pinnedCheckedTop(Request $request)
    {
        $ids = $request->ids;
        $items = category::whereIn('id', $ids)->get();

        category::whereNotIn('id', $ids)->update(['order_number' => null]);

        foreach ($items as $index => $item) {
            $item->order_number = $index + 1;
            $item->save();
        }
        return response()->json(['status' => "Pin to Start Successfully"]);
    }
}
