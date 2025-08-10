@if(!auth()->check())
    <a
        href="{{ route('login') }}"
        class="inline-flex items-center justify-center text-xs min-h-[36px] px-5 py-1.5 bg-blue-600 text-white rounded-md transition duration-100 hover:bg-blue-700"
        role="button"
        aria-label="Login"
    >
        <i class="fas fa-sign-in-alt mr-2"></i>
        Login
    </a>
@else
    <x-user-nav-dropdown class="!bg-gray-700 !text-gray-200 hover:!text-gray-400" />
@endif
