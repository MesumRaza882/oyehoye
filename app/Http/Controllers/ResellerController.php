<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Product, Admin, Category, ResellerSetting, ResellerAmountHistory};
use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use Exception;

class ResellerController extends Controller
{
    function products(Request $request)
    {
        $paginate_record = $request->records ?: 15;
        $status = 1;
        if ($request->status) {
            $status = $request->status;
        }
        $records = Product::latest()->with('itemcategory:id,name')
            ->with('resellerUploadProductObject', function ($q) {
                $q->where('admin_id', auth()->user()->id);
            })
            ->filterStatus($status)
            ->WhereIf('article', 'Like', "%{$request->article}%")
            ->where(function ($q) use ($request) {
                $q->WhereIf('name', 'Like', "%{$request->search_input}%")
                    ->OrWhereIf('price', 'Like', "%{$request->search_input}%");
            })
            ->Wherehas('itemcategory', function ($q) use ($request) {
                $q->WhereIf('name', 'Like', "%{$request->category}%");
            })
            ->Wherehas('resellerUploadProduct', function ($q) use ($request) {
                $q->where('admin_id', auth()->user()->id);
                if ($request->has('product_upload_status')) {
                    $q->WhereIf('product_upload_status', 'LIKE', "%{$request->product_upload_status}%");
                }
                if ($request->has('for_app_reseller')) {
                    $q->where('for_app_reseller', 1);
                }
            });
        $total_records = $records->count();
        $items = $records->paginate($paginate_record);
        return view('reseller.products', compact('items', 'total_records'));
    }

    public function productsEdit($id)
    {
        $product = Product::where('id', $id)->with('resellerUploadProductObject', function ($q) {
            $q->where('admin_id', auth()->user()->id);
        })->first(['id', 'name']);
        return view('reseller.productEdit', compact('product'));
    }

    public function productsUpdate(Request $request, $id)
    {
        $resellerSetting = ResellerSetting::findorFail($id);
        $resellerSetting->reseller_product_profit = $request->reseller_product_profit;
        $resellerSetting->save();
        return redirect()->route('waoseller.products')->with('message', 'Profit Update successfully');
    }

    public function balanceHistory(Request $request)
    {
        $paginate_record = $request->records ?: 15;
        $status = $request->status ?: 'cancel';
        $date = $request->date;
        $admin_id = auth()->user()->id;
        $startdate = $request->fromDate;
        $enddate =  $request->toDate;
        $admins = Admin::whereHas('resellerAmountHistories')->get(['id', 'email']);

        $allRecords = ResellerAmountHistory::
            // selectRaw('DATE(date) as date, status,admin_id,  SUM(balance) as balance')
            // ->groupBy('date', 'status', 'admin_id')
            whereIf('status', 'LIKE', "%{$status}%")
            ->when($request->search_input, function ($query) use ($request) {
                return $query->whereIf('order_id', 'LIKE', "%{$request->search_input}%");
            })
            ->filterByDateRange($startdate, $enddate)
            // filter by admin
            ->when($request->admin_id, function ($query) use ($request) {
                return $query->where('admin_id', $request->admin_id);
            })->orderBy('date', 'desc')->with('admin:id,email');

        // if not super admin then get admin his details only
        if (auth()->user()->id != 1) {
            $allRecords->where('admin_id', $admin_id);
        }

        $totalBalance = $allRecords->get()->sum('balance');

        $recordsCount = $allRecords->count();
        // Now, let's paginate the results
        $records = $allRecords->paginate($paginate_record);

        return view('reseller.balanceHistory', compact('records', 'recordsCount', 'totalBalance', 'admins'));
    }
}
