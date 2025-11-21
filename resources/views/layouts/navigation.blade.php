<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    {{-- 1. Link Trang chủ --}}
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('Trang chủ') }}
                    </x-nav-link>

                    {{-- 2. Các link quản lý --}}
                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'author')
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        
                        {{-- CHỈ ADMIN MỚI THẤY QUẢN LÝ DANH MỤC & BÌNH LUẬN --}}
                        @if(Auth::user()->role === 'admin')
                            <x-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                                {{ __('QL Danh mục') }}
                            </x-nav-link>

                            {{-- Link QL Bình luận cho Desktop --}}
                            <x-nav-link :href="route('admin.comments.index')" :active="request()->routeIs('admin.comments.*')">
                                {{ __('QL Bình luận') }}
                            </x-nav-link>
                            {{-- -Link QL quảng cáo --}}
                            <x-nav-link :href="route('admin.ads.index')" :active="request()->routeIs('admin.ads.*')">
                                {{ __('QL Quảng cáo') }}
                            </x-nav-link>

                        @endif

                        <x-nav-link :href="route('admin.posts.index')" :active="request()->routeIs('admin.posts.*')">
                            {{ __('QL Bài viết') }}
                        </x-nav-link>
                        
                        @if(Auth::user()->role === 'admin')
                             <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                {{ __('QL Thành viên') }}
                            </x-nav-link>
                        @endif
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center gap-2">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-8 h-8 rounded-full object-cover border border-gray-200">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                @endif
                                <div>{{ Auth::user()->name }}</div>
                            </div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Hồ sơ cá nhân') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Đăng xuất') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('Trang chủ') }}
            </x-responsive-nav-link>
            
            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'author')
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>

                @if(Auth::user()->role === 'admin')
                    <x-responsive-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                        {{ __('QL Danh mục') }}
                    </x-responsive-nav-link>
                    {{-- Link QL Bình luận cho Mobile --}}
                    <x-responsive-nav-link :href="route('admin.comments.index')" :active="request()->routeIs('admin.comments.*')">
                        {{ __('QL Bình luận') }}
                    </x-responsive-nav-link>
                    {{-- Link quảng cáo --}}
                    <x-nav-link :href="route('admin.ads.index')" :active="request()->routeIs('admin.ads.*')">
                        {{ __('QL Quảng cáo') }}
                    </x-nav-link>
                @endif

                <x-responsive-nav-link :href="route('admin.posts.index')" :active="request()->routeIs('admin.posts.*')">
                    {{ __('QL Bài viết') }}
                </x-responsive-nav-link>
                
                @if(Auth::user()->role === 'admin')
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                       {{ __('QL Thành viên') }}
                   </x-responsive-nav-link>
               @endif
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4 flex items-center gap-3">
                 @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                @else
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Hồ sơ cá nhân') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Đăng xuất') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>