@extends('layout.master')

@section('content')
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <div class="order-2 p-10 d-flex flex-column flex-lg-row-fluid w-lg-50 order-lg-1">
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <div class="p-10 w-lg-500px">
                        {{ $slot }}
                    </div>
                </div>
            </div>

            <div class="order-1 d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-lg-2"
                style="background-image: url('{{ asset('assets/media/auth/login.png') }}');">
                <div class="px-5 d-flex flex-column justify-content-between flex-center py-7 py-lg-15 px-md-15 w-100">

                    <!-- Top: Logo -->
                    <div class="mt-10 text-center w-100">
                        <img alt="Logo" src="{{ asset('assets/media/logos/logo.png') }}" class="h-40px h-lg-58px" />
                    </div>

                    <!-- Bottom: Text content -->
                    <div class="text-center text-white d-none d-lg-block">
                        <h1 class="text-white fs-2qx fw-bolder mb-7">
                            Refinish. Don't Replace.
                        </h1>

                        <div class="max-w-sm mx-auto fs-base" style="width: 400px;">
                            Transform your residential and commercial spaces with our premium interior films.
                            Quick installation, durable finish, and endless design possibilities.
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection
