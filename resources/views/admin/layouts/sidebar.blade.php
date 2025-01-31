@auth
    <div class="main-sidebar sidebar-style-2">
        <aside id="sidebar-wrapper">
            <div class="sidebar-brand">
                <a> <img alt="image" src="{{ asset('assets/img/logo.png') }}" class="wao's logo" /> <span
                        class="logo-name"></span>
                </a>
            </div>
            <ul class="sidebar-menu">
                <li class="menu-header"></li>

                <li class="dropdown {{ request()->is('home') ? 'active' : '' }} ">
                    <a href="{{ route('admin.home') }}" class="nav-link"><i data-feather="monitor"></i><span>Wao
                            Dashboard</span></a>
                </li>

                <!-- super admin routes display -->
                @if (auth()->user()->role == 1)
                    <!--Product-->
                    <li class="dropdown {{ request()->segment(2) == 'product' ? 'active' : '' }}">
                        <a href="#" class="menu-toggle nav-link has-dropdown"><i
                                class="fa-brands fa-product-hunt"></i>
                            <span>Item-Management</span></a>
                        <ul class="dropdown-menu">
                            @can('view_products')
                                <li><a class="nav-link" href="{{ route('all') }}">Products</a></li>
                            @endcan
                            @can('view_articles')
                                <li><a class=" nav-link" href="{{ route('viewarticle') }}">Articles</a></li>
                            @endcan
                            @can('manage_markeet_pickup')
                                <li><a class=" nav-link" href="{{ route('markeetPickupQty') }}">Markeet Pickup</a></li>
                            @endcan
                            @can('view_categories')
                                <li><a class=" nav-link" href="{{ route('category.index') }}">Categories</a></li>
                            @endcan
                        </ul>
                    </li>

                    <!--Orders-->
                    @can('view_orders')
                        <li class="dropdown {{ request()->segment(2) == 'order' ? 'active' : '' }}">
                            <a href="#" class="menu-toggle nav-link has-dropdown"><i
                                    class="fa-solid fa-cart-shopping"></i>
                                <span>Orders</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="{{ route('allorders') }}">View App Orders</a></li>
                            </ul>
                            <!-- <ul class="dropdown-menu">
                  <li><a class="nav-link" href="{{ route('allorders', ['is_reseller_order' => 1]) }}">View Seller Orders</a></li>
                </ul> -->
                            <ul class="dropdown-menu">
                                <li><a class="nav-link text-nowrap" href="{{ route('order.shipper.advice') }}">View Shipper
                                        Advice &nbsp;&nbsp; @if (Cache::get('shipper_advice_count') > 0)
                                            <span class="rounded bg-danger text-white px-1 text-center">
                                                {{ Cache::get('shipper_advice_count') }}</span>
                                        @endif
                                    </a>
                                </li>
                            </ul>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="{{ route('order.shipper.profit') }}">View Shipper Profit</a></li>
                            </ul>
                        </li>
                    @endcan


                    @can('view_admins')
                        <li class="dropdown {{ request()->routeIs('inventory.seller.index') ? 'active' : '' }}">
                            <a href="#" class="menu-toggle nav-link has-dropdown"><i class="fa-solid fa-users-gear"></i>
                                <span>Admins</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="{{ route('inventory.seller.index') }}">Profiles</a></li>
                                @can('view_balance_history')
                                    <li><a class="nav-link" href="{{ route('waoseller.balance.history') }}">Balance History</a>
                                    </li>
                                @endcan
                            </ul>
                        </li>
                    @endcan

                    @can('view_inventory')
                        <!-- wao Inventory -->
                        <li
                            class="dropdown {{ request()->routeIs('inventory.index') || request()->routeIs('waoseller.order.index') || request()->routeIs('ordersProfit') ? 'active' : '' }}">
                            <a href="#" class="menu-toggle nav-link has-dropdown"><i
                                    class="fa-solid fa-gear"></i><span>General Inventory-Sale</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="{{ route('inventory.index') }}">Inventory</a></li>
                                @can('view_general_inventory_orders')
                                    <li><a class="nav-link" href="{{ route('waoseller.order.index') }}">Seller Orders</a></li>
                                @endcan
                                <li><a class="nav-link"
                                        href="{{ route('ordersProfit') }}?profit_transaction_status=pending">Reseller Profit
                                        Orders</a></li>
                            </ul>
                        </li>
                    @endcan

                    @can('view_general_inventory_orders')
                        <li class="dropdown">
                            <a href="{{ route('admin.reviews') }}" class="nav-link">
                                <i class="fa-solid fa-pen-nib"></i>
                                <span>General Reviews @if (Cache::get('pending_reviews_count') > 0)
                                        <span class="rounded bg-danger text-white px-1">
                                            {{ Cache::get('pending_reviews_count') }}</span>
                                    @endif
                                </span>
                            </a>
                        </li>
                    @endcan

                    @can('view_customers_reviews')
                        <!--Category Reviews-->
                        <li class="dropdown">
                            <a href="#" class="menu-toggle nav-link has-dropdown"><i class="fa-solid fa-pen-nib"></i>
                                <span>All Category reviews</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="{{ route('viewCatReview') }}">Add Customer Reviews</a></li>
                            </ul>
                        </li>
                        <!--rates-->
                        <li class="dropdown">
                            <a href="#" class="menu-toggle nav-link has-dropdown"><i class="fa-solid fa-star"></i>
                                <span>Customer Reviews/Rating</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="{{ route('viewReview') }}">View All Reviews</a></li>
                            </ul>
                        </li>
                    @endcan

                    @can('view_users')
                        <li class="dropdown">
                            <a href="#" class="menu-toggle nav-link has-dropdown"><i class="fa-solid fa-exclamation"></i>
                                <span>User & Complaints</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="{{ route('viewUsers') }}">View All Users</a></li>
                                @can('view_complaints')
                                    <li><a class="nav-link" href="{{ route('viewProblems') }}">View All Complaints</a></li>
                                @endcan
                            </ul>
                        </li>
                    @endcan
                    <!--Charges-->
                    <!-- <li class="dropdown">
            <a href="#" class="menu-toggle nav-link has-dropdown"><i class="fa-solid fa-money-bill"></i>
              <span>Delivery Charges</span></a>
            <ul class="dropdown-menu">

            </ul>
          </li> -->
                    <!--App Setting-->
                    @can('view_complaints')
                        <li class="dropdown {{ request()->segment(2) == 'setting' ? 'active' : '' }}">
                            <a href="#" class="menu-toggle nav-link has-dropdown"><i
                                    class="fa-solid fa-gear"></i><span>App Manage</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="{{ route('viewCharges') }}">View Delivery Charges</a></li>
                                <li><a class="nav-link" href="{{ route('viewResAddress') }}">Add Restrict Addresses</a></li>
                                <li><a class="nav-link" href="{{ route('trackPlatformSetting') }}">TrackApi Setting</a></li>
                                <li><a class="nav-link" href="{{ route('viewMessage') }}">Message + Password</a></li>
                                <li><a class="nav-link" href="{{ route('clear_cache') }}">Clear Cache</a></li>
                                <li><a class="nav-link" href="{{ route('admin.settings') }}">Settings</a>
                                </li>
                            </ul>
                        </li>
                    @endcan
                @endif

                <!-- seller/warehouse team  routes display -->
                @if (in_array(auth()->user()->role, [3, 4]))
                    <!--Product-->
                    <li class="dropdown {{ request()->routeIs('waoseller.products') ? 'active' : '' }}">
                        <a href="#" class="menu-toggle nav-link has-dropdown"><i
                                class="fa-solid fa-cart-shopping"></i>
                            <span>Item-Management</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="{{ route('waoseller.products') }}">Products</a></li>
                        </ul>
                    </li>

                    <li
                        class="dropdown {{ request()->routeIs('waoseller.order.index') || request()->routeIs('waoseller.balance.history') || request()->routeIs('waoseller.order.create') ? 'active' : '' }}">
                        <a href="#" class="menu-toggle nav-link has-dropdown"><i
                                class="fa-solid fa-cart-shopping"></i>
                            <span>General Inventory-Order</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="{{ route('waoseller.order.index') }}">Generall Orders</a></li>
                            {{-- only for partners --}}
                            @if (auth()->user()->role === 3 && auth()->user()->is_partner === 1)
                                <li><a class="nav-link"
                                        href="{{ route('waoseller.getWebOrders', ['is_partner' => 1, 'status' => 'PENDING']) }}">TeamMember-Orders</a>
                                </li>
                                <li><a class="nav-link"
                                        href="{{ route('inventory.seller.index', ['is_partner' => 1]) }}">Profiles</a>
                                </li>
                            @endif
                            <li><a class="nav-link" href="{{ route('waoseller.balance.history') }}">Balance History</a>
                            </li>
                        </ul>
                    </li>

                    <li
                        class="dropdown {{ request()->routeIs('waoseller.getUsers') || request()->routeIs('waoseller.editUser') || request()->routeIs('waoseller.getWebOrders') || request()->routeIs('waoseller.editOrder') ? 'active' : '' }}">
                        <a href="#" class="menu-toggle nav-link has-dropdown"><i
                                class="fa-solid fa-cart-shopping"></i>
                            <span>Website-Management</span></a>
                        <ul class="dropdown-menu">
                            <li><a class="nav-link" href="{{ route('waoseller.getWebOrders') }}">App-Orders</a></li>
                            @can('view_users')
                                <li><a class="nav-link" href="{{ route('waoseller.getUsers') }}">Users</a></li>
                            @endcan
                        </ul>
                    </li>
                @endif
            </ul>
        </aside>
    </div>
@endauth
