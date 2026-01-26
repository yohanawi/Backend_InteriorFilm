<x-auth-layout>

    <form class="form w-100" novalidate="novalidate" id="kt_sign_up_form" data-kt-redirect-url="{{ route('login') }}"
        action="{{ route('register') }}">
        @csrf
        <div class="text-center mb-11">
            <h1 class="mb-3 text-gray-900 fw-bolder">
                Sign Up
            </h1>
        </div>

        <div class="mb-8 fv-row">
            <input type="text" placeholder="Name" name="name" autocomplete="off"
                class="bg-transparent form-control" />
        </div>

        <div class="mb-8 fv-row">
            <input type="text" placeholder="Email" name="email" autocomplete="off"
                class="bg-transparent form-control" />
        </div>

        <div class="mb-8 fv-row" data-kt-password-meter="true">
            <div class="mb-1">
                <div class="mb-3 position-relative">
                    <input class="bg-transparent form-control" type="password" placeholder="Password" name="password"
                        autocomplete="off" />

                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                        data-kt-password-meter-control="visibility">
                        <i class="bi bi-eye-slash fs-2"></i>
                        <i class="bi bi-eye fs-2 d-none"></i>
                    </span>
                </div>

                <div class="mb-3 d-flex align-items-center" data-kt-password-meter-control="highlight">
                    <div class="rounded flex-grow-1 bg-secondary bg-active-success h-5px me-2"></div>
                    <div class="rounded flex-grow-1 bg-secondary bg-active-success h-5px me-2"></div>
                    <div class="rounded flex-grow-1 bg-secondary bg-active-success h-5px me-2"></div>
                    <div class="rounded flex-grow-1 bg-secondary bg-active-success h-5px"></div>
                </div>
            </div>

            <div class="text-muted">
                Use 8 or more characters with a mix of letters, numbers & symbols.
            </div>
        </div>

        <div class="mb-8 fv-row">
            <input placeholder="Repeat Password" name="password_confirmation" type="password" autocomplete="off"
                class="bg-transparent form-control" />
        </div>

        <div class="mb-10 fv-row">
            <div class="form-check form-check-custom form-check-solid form-check-inline">
                <input class="form-check-input" type="checkbox" name="toc" value="1" />

                <label class="text-gray-700 form-check-label fw-semibold fs-6">
                    I Agree &

                    <a href="#" class="ms-1 link-primary">Terms and conditions</a>.
                </label>
            </div>
        </div>

        <div class="mb-10 d-grid">
            <button type="submit" id="kt_sign_up_submit" class="btn btn-primary">
                @include('partials/general/_button-indicator', ['label' => 'Sign Up'])
            </button>
        </div>

        <div class="text-center text-gray-500 fw-semibold fs-6">
            Already have an Account?

            <a href="/login" class="link-primary fw-semibold">
                Sign in
            </a>
        </div>
    </form>

</x-auth-layout>
