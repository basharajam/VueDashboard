@extends('layout.master')

@section('style')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<Link rel = "stylesheet" href = "http://jqueryui.com/resources/demos/style.css">
    
<style>
#AddCompBtn{
    padding: 0 !important;
    border: 0;
    background: white;
}
</style>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-11">
                <h4 class="text-center">Destop Layout</h4>
                <div class="row" id='sortableProdOne'>
                    @foreach ($Layout as $item)
                    <div class="card m-1" data-sort="{{ $item['sort'] }}" data-id="{{ $item['id'] }}" >
                        <div class="card-body d-flex justify-content-between align-items-center">
                        <h5 class="card-title">{{$item['title']}}</h5>
                        <div class="">
                            <button type="button" class="btn btn-primary UpdItem" data-id="{{$item['id']}}" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Update
                            </button> 
                            <button type="button" class="btn btn-danger DelComp" data-bs-toggle="modal" data-id="{{ $item['id'] }}" data-bs-target="#DelCompModal">
                            Delete
                            </button> 
                        </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="row">
                    <button id="AddCompBtn" data-bs-toggle="modal" data-bs-target="#AddCompModal">
                        <div class="card m-1">
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <h5 class="card-title">Add New Component</h5>
                                <i class="fas fa-plus"></i>
            
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-11">
                <h4 class="text-center">Mobile Layout</h4>
                <div class="row" id='sortableMobileProdOne'>
                    @foreach ($Layout as $item)
                    <div class="card m-1" data-sort="{{ $item['sort'] }}" data-id="{{ $item['id'] }}" >
                        <div class="card-body d-flex justify-content-between align-items-center">
                        <h5 class="card-title">{{$item['title']}}</h5>
                        <div class="">
                            <button type="button" class="btn btn-primary UpdItem" data-id="{{$item['id']}}" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Update
                            </button> 
                            <button type="button" class="btn btn-danger DelComp" data-bs-toggle="modal" data-id="{{ $item['id'] }}" data-bs-target="#DelCompModal">
                            Delete
                            </button> 
                        </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="row">
                    <button id="AddCompBtn" data-bs-toggle="modal" data-bs-target="#AddCompModal">
                        <div class="card m-1">
                            <div class="card-body d-flex justify-content-center align-items-center">
                                <h5 class="card-title">Add New Component</h5>
                                <i class="fas fa-plus"></i>
            
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @include('Cpanel.layouts.widgets.modals')
@endSection




@section('script')
    <script src="https://cdn.jsdelivr.net/npm/@shopify/draggable@1.0.0-beta.8/lib/draggable.bundle.js"></script>
    <script>

        $(document).ready(function() {
            
            //Change WherePage Input Value
            $('input[name=compwhereNI]').val('ProdOne');
        })

        //ProdOne  page Dragable Desktop
        const sortable = new Draggable.Sortable(
        document.querySelector('#sortableProdOne'), {
          draggable: 'div.card',
        })
        sortable.on('sortable:stop', () => {
            console.log('sortable:stop')
            var idArrLanding=[];
            var sortArrLanding=[];
            var i=1
            setTimeout(() => {
              $('#sortableProdOne div.card').each(function(){
                
                var increase = i++
                $(this).attr('data-sort',increase)
                idArrLanding.push($(this).data('id'))
                sortArrLanding.push($(this).attr('data-sort'))
                
              })
              $.ajax({
                  method:"post",
                  url:"{{ route('updateSort') }}",
                  data:{
                    idArr:idArrLanding,
                    sortArr:sortArrLanding,
                    type:'desktop',
                    _token:'{{ csrf_token() }}'
                  },
                  success:function(resp){


                    //Update Sort data id 
                    toastr["success"]("Sort Successfully Updated")
                  },
              })
            }, 1000);
        })

        //ProdByCat  page Dragable Mobile
        const sortableMobile = new Draggable.Sortable(
        document.querySelector('#sortableMobileProdOne'), {
          draggable: 'div.card',
        })
        sortableMobile.on('sortable:stop', () => {
            console.log('sortable:stop')
            var idArrLanding=[];
            var sortArrLanding=[];
            var i=1
            setTimeout(() => {
              $('#sortableMobileProdOne div.card').each(function(){
                
                var increase = i++
                $(this).attr('data-sort',increase)
                idArrLanding.push($(this).data('id'))
                sortArrLanding.push($(this).attr('data-sort'))
                
              })

              $.ajax({
                  method:"post",
                  url:"{{ route('updateSort') }}",
                  data:{
                    idArr:idArrLanding,
                    sortArr:sortArrLanding,
                    type:'mobile',
                    _token:'{{ csrf_token() }}'
                  },
                  success:function(resp){


                    //Update Sort data id 
                    toastr["success"]("Sort Successfully Updated")
                  },
              })
            }, 1000);
        })

    </script>
    @include('Cpanel.layouts.widgets.scripts')
@endsection