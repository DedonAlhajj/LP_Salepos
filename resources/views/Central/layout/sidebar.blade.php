<ul id="side-main-menu" class="side-menu list-unstyled d-print-none">
    <li><a href="{{route('super.dashboard')}}"> <i class="dripicons-meter"></i><span>{{ __('file.dashboard') }}</span></a></li>

    <li>

        <ul id="product" class="collapse list-unstyled ">
            <li id="category-menu"><a href="">{{__('file.category')}}</a></li>
            <li id="product-list-menu"><a href="">{{__('file.product_list')}}</a></li>

            <li id="product-create-menu"><a href="">{{__('file.add_product')}}</a></li>

            <li id="printBarcode-menu"><a href="">{{__('file.print_barcode')}}</a></li>

        </ul>
    </li>
</ul>
