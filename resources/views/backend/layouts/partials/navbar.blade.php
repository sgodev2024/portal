<nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
    <div class="container-fluid">
        <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
            <div class="input-group">
                {{-- <div class="input-group-prepend">
                    <button type="submit" class="btn btn-search pe-1">
                        <i class="fa fa-search search-icon"></i>
                    </button>
                </div>
                <input type="text" placeholder="Search ..." class="form-control" /> --}}
            </div>
        </nav>

        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
            <li class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
                <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button"
                    aria-expanded="false" aria-haspopup="true">
                    <i class="fa fa-search"></i>
                </a>
                <ul class="dropdown-menu dropdown-search animated fadeIn">
                    <form class="navbar-left navbar-form nav-search">
                        <div class="input-group">
                            <input type="text" placeholder="Search ..." class="form-control" />
                        </div>
                    </form>
                </ul>
            </li>
            {{-- @if (Auth::user()->role_id != 1)

                <li>
                    <a href="{{ route('qrcode.index') }}" class="dropdown-title d-flex justify-content-between align-items-center" style="border-bottom: none !important;">
                        <span>
                            Qr Code<i class="fas fa-qrcode " style="margin-left: 10px"></i>
                        </span>
                    </a>
                </li>

                <li class="nav-item topbar-icon dropdown hidden-caret">
                    <a class="nav-link dropdown-toggle" href="#" id="walletDropdown" role="button"
                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                        <i class="fas fa-wallet"></i>

                    </a>
                    <ul class="dropdown-menu wallet-menu animated fadeIn" aria-labelledby="walletDropdown">
                        <li>
                            <div class="dropdown-title d-flex justify-content-between align-items-center"
                                style="width: 250px;">
                                Ví : {{ number_format(Auth::user()->wallet, 0, ',', '.') }} đ
                            </div>
                        </li>
                        <li>
                            <a href="{{ route('payment.recharge') }}"
                                class="dropdown-title d-flex justify-content-between align-items-center">
                                <i class="fas fa-coins"></i> Nạp tiền
                            </a>
                        </li>
                        <li>
                        <a href="#" class="dropdown-title d-flex justify-content-between align-items-center">
                            <i class="fas fa-history"></i> Lịch sử giao dịch
                        </a>
                    </li>
                    </ul>
                </li>
                <li class="nav-item topbar-icon dropdown hidden-caret">
                    @php
                        $cartDetails = optional(Auth::user()->cart)->details ?? false;
                        $renewCount = \App\Models\RenewService::where('email', Auth::user()->email)->count();
                    @endphp

                    @if ($cartDetails)
                        <a class="nav-link" href="{{ route('customer.cart.listcart') }}" id="notifDropdown">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="notification">
                                {{ count($cartDetails) }}
                            </span>
                        </a>
                    @elseif ($renewCount > 0)
                        <a class="nav-link" href="{{ route('customer.cart.listrenews') }}" id="notifDropdown">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="notification">
                                {{ $renewCount }}
                            </span>
                        </a>
                    @else
                        <a class="nav-link" href="#" id="notifDropdown">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="notification">0</span>
                        </a>
                    @endif

                </li>
            @endif --}}
            
            {{-- Language Switcher --}}
            <li class="nav-item" style="display: flex; align-items: center; margin-right: 15px;">
                @include('components.language-switcher')
            </li>
            
            {{-- Google Translate Hidden Element --}}
            <li class="nav-item" style="display: none;">
                <div id="google_translate_element"></div>
            </li>
            
            <li class="nav-item topbar-user dropdown hidden-caret">
                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                    <div class="avatar-sm">
                        @if(Auth::user()->avatar)
                            <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                                class="avatar-img rounded-circle" />
                        @else
                            <img src="{{ asset('backend/assets/img/jm_denis.jpg') }}" alt="Default Avatar"
                                class="avatar-img rounded-circle" />
                        @endif
                    </div>
                    <span class="profile-username">
                        <span class="op-7">Hi,</span>
                        <span class="fw-bold">{{ Auth::user()->name }}</span>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-user animated fadeIn" style="width: 287px">
                    <div class="dropdown-user-scroll scrollbar-outer">
                        <li>
                            <div class="user-box">
                                <div class="avatar-lg">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar"
                                            class="avatar-img rounded" />
                                    @else
                                        <img src="{{ asset('backend/assets/img/jm_denis.jpg') }}" alt="Default Avatar"
                                            class="avatar-img rounded" />
                                    @endif
                                </div>
                                <div class="u-text">
                                    <h4>{{ Auth::user()->name }}</h4>
                                    <p class="text-muted">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                            @if(Auth::user()->role_id == 3)
                                <a class="dropdown-item" href="{{ route('customer.profile.edit') }}">
                                    <i class="fas fa-user-cog"></i> Cập nhật thông tin
                                </a>
                            @endif
                            <a class="dropdown-item" href="{{ route('logout') }}">
                                <i class="fas fa-sign-out-alt"></i> Đăng xuất
                            </a>
                        </li>
                    </div>
                </ul>
            </li>
        </ul>
    </div>
</nav>


<!-- Đảm bảo jQuery được tải trước -->
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#notifDropdown').on('click', function(event) {
            let cartItems = $('.notification').text(); // Giả sử dữ liệu giỏ hàng là null
            // alert(cartItems);
            if (!cartItems == null || cartItems == 0) {
                event.preventDefault(); // Ngăn điều hướng
                Swal.fire({
                    title: 'Giỏ hàng trống!',
                    text: 'Vui lòng thêm đơn hàng vào giỏ hàng trước.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
</script>
