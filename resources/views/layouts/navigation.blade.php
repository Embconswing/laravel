<nav x-data="{ open: false }"
     class="bg-white shadow-md border-b border-gray-200">

 <!-- MEA Style Top Accent -->
<div class="w-full">
    <div style="background:#FF9933; height:5px;"></div>
</div>



    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative flex items-center justify-between h-20">

            <!-- LEFT: Application Logo -->
            <div class="flex items-center z-10">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                    <x-application-logo class="block h-12 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    <div class="hidden sm:block leading-tight">
                       
                    </div>
                </a>
            </div>

            <!-- CENTER: Embassy Title (true center of screen width) -->
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none px-2">
                <a href="{{ route('dashboard') }}"
                   class="text-xl sm:text-2xl font-bold tracking-wide text-gray-900 dark:text-white pointer-events-auto">
                    Embassy of India, Berlin
                </a>
                <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-300 mt-1">
                    Consular Services Portal
                </div>
            </div>

            <!-- RIGHT: Profile / Login -->
            <div class="flex items-center z-10">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md
                                           text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700
                                           hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none transition">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-2">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium
                              text-white bg-green-600 hover:bg-green-700 transition">
                        Login
                    </a>
                @endauth
            </div>

        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500 dark:text-gray-300">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
    <div style="background:#138808; height:5px;"></div>


</nav>
