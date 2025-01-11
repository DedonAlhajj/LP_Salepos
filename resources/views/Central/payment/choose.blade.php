@extends('Central.layout.main_guest1') @section('content')

@if(session()->has('not_permitted'))
  <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('not_permitted') }}</div>
@endif
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3>Review Your Registration Details</h3>
                    </div>
                    @if ($errors->any())
                           <div class="alert alert-danger">
                               <ul>
                                   @foreach ($errors->all() as $error)
                                       <li>{{ $error }}</li>
                                   @endforeach
                               </ul>
                           </div>
                       @endif

                    <div class="card-body">
                        <!-- عرض تفاصيل التسجيل -->
                        <h5 class="mb-3">Your Information:</h5>
                        <ul class="list-group mb-4">
                            <li class="list-group-item"><strong>Name:</strong> {{ $pendingUser->name }}</li>
                            <li class="list-group-item"><strong>Email:</strong> {{ $pendingUser->email }}</li>
                            <li class="list-group-item"><strong>Store Name:</strong> {{ $pendingUser->store_name }}</li>
                            <li class="list-group-item"><strong>Domain:</strong> {{ $pendingUser->domain }}.LP_Salepos</li>
                        </ul>

                        <!-- عرض تفاصيل الباقة -->
                        <h5 class="mb-3">Selected Package:</h5>
                        <ul class="list-group mb-4">
                            <li class="list-group-item"><strong>Package Name:</strong> {{ $package->name }}</li>
                            <li class="list-group-item"><strong>Price:</strong> ${{ $package->price }}</li>
                        </ul>

                        <!-- اختيار طريقة الدفع -->
                        <h5 class="mb-3">Choose Payment Method:</h5>
                        <form action="{{ route('payment.process') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select id="payment_method" name="payment_method" class="form-select" required>
                                    <option value="" selected disabled> Select Payment Method </option>
                                    <option value="myfatoora">Myfatoora</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="pending_user_id" value="{{$pendingUser->id}}" required
                                       class="form-control">
                            </div>
                            <button type="submit" class="btn btn-success w-100">Proceed to Payment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


