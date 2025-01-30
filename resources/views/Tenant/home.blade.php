@extends('Tenant.layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if(Auth::user()->is_active)
                            <a href="{{url('/dashboard')}}">You are logged in! click to go to dashboard</a>
                    @else
                    You are logged in but id is not activated! Please contact with admin.
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
