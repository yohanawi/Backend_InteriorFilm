<x-auth-layout>

    <form class="form w-100" method="POST" novalidate="novalidate" id="kt_sign_in_form"
        data-kt-redirect-url="{{ route('dashboard') }}" action="{{ route('login') }}">
        @csrf
        <div class="text-center mb-11">
            <h1 class="mb-3 text-gray-900 fw-bolder">
                Sign In
            </h1>
        </div>

        <div class="mb-8 fv-row">
            <input type="text" placeholder="Email" name="email" autocomplete="off"
                class="bg-transparent form-control" value="" />
        </div>

        <div class="mb-3 fv-row">
            <input type="password" placeholder="Password" name="password" autocomplete="off"
                class="bg-transparent form-control" value="" />
        </div>

        <div class="flex-wrap gap-3 mb-8 d-flex flex-stack fs-base fw-semibold">
            <div></div>

            <a href="{{ route('password.request') }}" class="link-primary">
                Forgot Password ?
            </a>
        </div>

        <div class="mb-10 d-grid">
            <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                @include('partials/general/_button-indicator', ['label' => 'Sign In'])
            </button>
        </div>


    </form>

</x-auth-layout>
