@extends('portal.layout.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    @if(session('success'))
                        <div class="alert alert-success mt-3">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger mt-3">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ route('setting.update') }}" class="inner-form">
                                @csrf
                                <div class="row">
                                    <div class="col-12 col-md-4 mt-3">
                                        <label for="old_password" class="form-label">Old Password</label>
                                        <input type="password" class="form-control" id="old_password"
                                               name="old_password" placeholder="Old Password">
                                    </div>
                                    <div class="col-12 col-md-4 mt-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="new_password"
                                               name="new_password" placeholder="New Password">
                                    </div>
                                    <div class="col-12 col-md-4 mt-3">
                                        <label for="new_password_confirmation" class="form-label">Confirm Password</label>
                                        <input type="password" name="new_password_confirmation" class="form-control"
                                               id="new_password_confirmation" placeholder="Confirm Password">
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
