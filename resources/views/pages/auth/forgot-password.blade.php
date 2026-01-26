<x-auth-layout>

    <form class="form w-100" novalidate="novalidate" id="kt_password_reset_form" data-kt-redirect-url="{{ route('login') }}"
        action="{{ route('password.request') }}">
        @csrf
        <div class="mb-10 text-center">
            <h1 class="mb-3 text-gray-900 fw-bolder">
                Forgot Password ?
            </h1>

            <div class="text-gray-500 fw-semibold fs-6">
                Enter your email to reset your password.
            </div>
        </div>

        <div class="mb-8 fv-row">
            <input type="text" placeholder="Email" name="email" autocomplete="off"
                class="bg-transparent form-control" value="demo@demo.com" />
        </div>

        <div class="flex-wrap d-flex justify-content-center pb-lg-0">
            <button type="button" id="kt_password_reset_submit" class="btn btn-primary me-4">
                @include('partials/general/_button-indicator', ['label' => 'Submit'])
            </button>

            <a href="{{ route('login') }}" class="btn btn-light">Cancel</a>
        </div>
    </form>

</x-auth-layout>
