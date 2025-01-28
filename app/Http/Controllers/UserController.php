<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Rate;
use App\Models\{Order, Admin, City, Problem, Cart};
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;


class UserController extends Controller
{
    //
    public function register()
    {
        $cities = City::all();
        $users = User::with('city')->get();
        // return $users;
        return view('users.register', compact('cities', 'users'));
    }
    //create
    public function create(Request $req)
    {
        $req->validate([
            'country' => 'required',
            'city' => 'required',
            'user_name' => 'required',
            'phone' => 'required|digits:11',
        ]);
        $user = new User();
        $user->phone = $req->phone;
        $user->name = $req->user_name;
        $user->country = $req->country;
        $user->city_id = $req->city;
        $city = City::find($req->city);
        $user->city_name = $city->name;
        $user->save();
        return redirect()->route('register');
    }

    //profile
    public function profile(Request $req)
    {
        $user = User::findorFail($req->id);
        return view('users.profile', compact('user'));
    }

    //update profile
    public function updateprofile(Request $req)
    {
        $req->validate([
            'address' => 'required',
            'city' => 'required',
            'user_name' => 'required',
            'phone' => 'required|digits:11',
            'id' => 'required',
        ]);
        $user = User::find($req->id);
        $user->address = $req->address;
        $user->city_id = $req->city;
        $city = City::find($req->city);
        $user->city_name = $city->name;
        $user->phone = $req->phone;
        $user->name = $req->user_name;
        $user->save();
        return redirect()->back();
    }


    public function viewUsers(Request $req)
    {
        $paginate_record = $req->records ? $req->records : 50;
        $startdate = $req->fromDate;
        $enddate =  $req->toDate;
        $status = $req->status === '1' ? '0' : $req->status;

        if ($req->vcf == 1) {
            return $this->VcfDownload($req);
        }

        // Generate a unique cache key based on request parameters
        $cacheKey = 'viewUsers_' . md5(json_encode($req->all())) . '_page_' . $req->page;
        // Cache results for hour
        $users = Cache::remember($cacheKey, 1 * 1, function () use ($req, $paginate_record, $startdate, $enddate, $status) {
            $records = User::select(['id', 'city_id', 'city_name', 'whatsapp', 'status', 'created_at', 'is_active_row', 'name', 'is_reseller'])
                // ->withCount([
                //     'order as real_orders' => function ($query) {
                //         $query->where('is_blocked_customer_order', 0);
                //     }
                // ])
                ->distinct('whatsapp')
                ->with('city:id,name')
                ->when($status, function ($query) use ($status) {
                    $query->where('status', 'Like', "%{$status}%");
                })
                ->when($req->admin_id, function ($query) use ($req) {
                    // Apply this only if admin_id is present in the request
                    $query->whereHas('order', function ($q) use ($req) {
                        $q->where(function ($subQuery) use ($req) {
                            if ($req->orderType === 'general') {
                                // When orderType is 'general', only check 'wao_seller_id'
                                $subQuery->where('wao_seller_id', $req->admin_id);
                            } elseif ($req->orderType === 'app') {
                                // When orderType is 'app', check where 'wao_seller_id' is NULL and 'admin_id' matches
                                $subQuery->whereNull('wao_seller_id')
                                    ->where('admin_id', $req->admin_id);
                            } else {
                                // Default behavior when orderType is empty, fetch both "general" and "app"
                                $subQuery->where(function ($orQuery) use ($req) {
                                    $orQuery->where('wao_seller_id', $req->admin_id)  // General condition
                                        ->orWhere(function ($orSubQuery) use ($req) {
                                            $orSubQuery->whereNull('wao_seller_id')     // App condition
                                                ->where('admin_id', $req->admin_id);
                                        });
                                });
                            }
                        });
                    });
                })
                ->filterByDate($startdate, $enddate)
                ->where(function ($q) use ($req) {
                    $q->when($req->search_input, function ($q) use ($req) {
                        $q->where('name', 'Like', "%{$req->search_input}%")
                            ->orWhere('whatsapp', 'Like', "%{$req->search_input}%");
                    });
                })
                ->when($req->city, function ($query) use ($req) {
                    $query->whereHas('city', function ($q) use ($req) {
                        $q->where('id', 'Like', "%{$req->city}%");
                    });
                })
                ->when($req->user_type === '1', function ($query) {
                    $query->whereHas('order', function ($query) {
                        $query->where('is_blocked_customer_order', '=', 0);
                    });
                })
                ->whereFilterReseller($req->is_reseller)
                ->orderByRaw("id DESC");

            return $records->paginate($paginate_record);
        });

        // Cache the total count of users separately, if needed
        $total_users_key = 'total_users_' . md5(json_encode($req->all()));
        $total_users = Cache::remember($total_users_key, 1, function () use ($users) {
            return $users->total(); // Use the `total()` method from pagination result
        });

        // Cache cities
        $cities = Cache::remember('cities', 1, function () {
            return City::get(['id', 'name']);
        });

        // Cache admins where there are sellerOrders or adminOrders
        $admins = Cache::remember('admins_with_orders', 1, function () {
            return Admin::whereHas('sellerOrders')
                ->orWhereHas('adminOrders')
                ->get(['id', 'name']);
        });

        // return $users;

        return view('admin.rates.viewUsers', compact('users', 'total_users', 'cities', 'admins'));
    }

    public function VcfDownload($req)
    {
        $startdate = $req->fromDate;
        $enddate =  $req->toDate;
        $status = $req->status === '1' ? '0' : $req->status;
        // Fetch all users' WhatsApp numbers without pagination for VCF export
        $contacts = User::select('name', 'whatsapp')
            ->distinct('whatsapp')
            ->when($status, function ($query) use ($status) {
                $query->where('status', 'Like', "%{$status}%");
            })
            ->when($req->admin_id, function ($query) use ($req) {
                // Apply filters as in your original query
                $query->whereHas('order', function ($q) use ($req) {
                    $q->where(function ($subQuery) use ($req) {
                        if ($req->orderType === 'general') {
                            $subQuery->where('wao_seller_id', $req->admin_id);
                        } elseif ($req->orderType === 'app') {
                            $subQuery->whereNull('wao_seller_id')
                                ->where('admin_id', $req->admin_id);
                        } else {
                            $subQuery->where(function ($orQuery) use ($req) {
                                $orQuery->where('wao_seller_id', $req->admin_id)
                                    ->orWhere(function ($orSubQuery) use ($req) {
                                        $orSubQuery->whereNull('wao_seller_id')
                                            ->where('admin_id', $req->admin_id);
                                    });
                            });
                        }
                    });
                });
            })
            ->filterByDate($startdate, $enddate)
            ->whereFilterReseller($req->is_reseller)
            ->get();

        // Prepare VCF data
        $vcfData = '';
        foreach ($contacts as $contact) {
            $vcfData .= "BEGIN:VCARD\n";
            $vcfData .= "VERSION:3.0\n";
            $vcfData .= "FN:{$contact->name}\n";
            $vcfData .= "TEL;TYPE=CELL:{$contact->whatsapp}\n";
            $vcfData .= "END:VCARD\n";
        }

        // Set headers for VCF download
        $fileName = 'user_contacts_' . date('Ymd_His') . '.vcf';
        return response($vcfData)
            ->header('Content-Type', 'text/vcard')
            ->header('Content-Disposition', "attachment; filename={$fileName}");
    }


    //single order details page
    public function singleUser($id)
    {
        $user = User::with(['bussiness_detail', 'city:id,name'])->withcount('order')
            ->where('id', $id)->with('bussiness_detail')->first();

        $orders = Order::where('user_id', $id)->select(['id', 'user_id', 'status', 'grandTotal', 'is_blocked_customer_order', 'date', 'time'])
            ->take(10)->orderBy('id', 'desc')->withcount('orderitems')->get();
        // active row
        if ($already_active = User::where('id', '!=', $user->id)->where('is_active_row', 1)->first()) {
            $already_active->is_active_row = 0;
            $already_active->save();
        }
        $user->is_active_row = 1;
        $user->save();

        return view('admin.rates.singleUser', compact('user', 'orders'));
    }

    //  update User password/status
    public function updateUser(Request $req)
    {
        $user = User::find($req->user_id);
        $user_same_whatsapp = User::where('whatsapp', $user->whatsapp)->get();
        $status = $req->status == 2 ? 0 : 1;

        $user_same_whatsapp->each(function ($user) use ($status, $req) {
            $user->update([
                'status' => $status,
                'password' => $req->password ? Hash::make($req->password) : $user->password,
            ]);
        });

        if ($user->bussiness_detail) {
            $userbussiness_detail = $user->bussiness_detail;
            $userbussiness_detail->postex_address_code = $req->postex_address_code;
            $userbussiness_detail->save();
        }

        // $user->save();
        if (!$req->ajax()) {
            return redirect()->back()->with('message', 'User Update Successfully');
        }
        session()->put('message', 'User with Same WhatsNumber Update Successfully');
        session()->put('end_time',  Carbon::now()->addSecond(3));
        echo '<script type="text/javascript">', 'history.go(-2);', '</script>';
    }

    // trashedUser
    public function trashedUser()
    {
        $users = User::onlyTrashed()->get();
        return view('admin.rates.trashUsers', compact('users'));
    }

    // restoreUser
    public function restoreUser(Request $req, $id)
    {
        $user = User::withTrashed()->find($id);
        if ($user) {
            $user->restore();
            $user->status = 0;
            $user->save();
            return redirect()->route('trashedUser')->with('success', 'User Restored Successfully');
        }
        return redirect()->route('trashedUser')->with('fail', 'Issue somemthing');
    }

    // perDelUser
    public function perDelUser(Request $req, $id)
    {
        $user = User::withTrashed()->find($id);
        if ($user) {
            // Rates and Problems delete
            $rates = Rate::where('user_id', $user->id)->get();
            $prob = Problem::where('user_id', $user->id)->get();
            $cart = Cart::where('user_id', $user->id)->get();
            if ($rates) {
                $rates->each->delete();
            }
            if ($prob) {
                $prob->each->delete();
            }
            if ($cart) {
                $cart->each->delete();
            }
            $user->forceDelete();
            return redirect()->route('trashedUser')->with('success', 'User Permanently Deleted Successfully');
        }
        return redirect()->route('trashedUser')->with('fail', 'Error to delete User');
    }

    // del User
    public function delUser(Request $req, $id)
    {
        $user = User::find($id);
        $user->status = 1;
        $user->save();
        if ($user) {
            // Rates and Problems delete
            $rates = Rate::where('user_id', $user->id)->get();
            $prob = Problem::where('user_id', $user->id)->get();
            $cart = Cart::where('user_id', $user->id)->get();
            if ($rates) {
                $rates->each->delete();
            }
            if ($prob) {
                $prob->each->delete();
            }
            if ($cart) {
                $cart->each->delete();
            }
            $user->delete();
            return redirect()->route('viewUsers')->with('success', 'User Moved to Recycle Bin Successfully');
        }
        return redirect()->route('viewUsers')->with('fail', 'Error to delete User');
    }

    // blockStatus
    public function blockStatusUser(Request $req, $id, $b_status)
    {
        $user = User::find($id);
        if ($user) {
            $user->status = $b_status;
            $user->save();
            return redirect()->route('viewUsers')->with('success', 'User Status Changed  Successfully');
        }
        return redirect()->route('viewUsers')->with('fail', 'Error to Status update User');
    }
}
