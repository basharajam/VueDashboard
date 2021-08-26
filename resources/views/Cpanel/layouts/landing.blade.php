@extends('layout.master')

@section('style')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<Link rel = "stylesheet" href = "http://jqueryui.com/resources/demos/style.css">
    
{{-- <style>
  #sortable {list-style-type: none; margin: 0; padding: 0; width: 60%;}
  #sortable li {margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px;}
  #sortable li span {position: absolute; margin-left: -1.3em;}
</style> --}}
@endsection



@section('content')

    <div class="container">
        <div class="row">

            <div class="col-sm-8">
                <div class="row" id="sortableProdBox">
                    @foreach ($ProdInBox as $item)
                        <div class="col-sm-3" data-sort="{{ $item['sort'] }}" data-sortdesk="{{ $item['Display'] }} ">
                            {{$item['title']}}
                            <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                            
                        </div>
                    @endforeach
                </div>
                <div class="row">
                    @foreach ($Layout as $item)
                    <div class="card">
                        <div class="card-body">
                          <h5 class="card-title">{{$item['title']}}</h5>
                          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Update
                          </button> 
                          <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            Delete
                          </button> 
                        </div>
                      </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
  
  <!-- Update Item Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form class="SubmitSection" data-comp="">
                <label class="form-label">Section // ProdByTax</label>
                <div class="mb-3">
                    <input type="text" id='titleCompName' placeholder="Section Title" name="SectionTitleI" value="title"  class="form-control"  >
                </div>
                <div class="mb-3">
                    <input type="number" id="valueCompName" placeholder="Value" name="SectionValI" value="vaue" class="form-control" >
                </div>
                <div class="mb-3">
                    <select name="ProdByTaxType" id="typeCompName" name="SectionTypeI" class="form-control" disabled>
                        <option value="Tag"  >Tag</option>
                        <option value="Category"  >Category</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <input type="number" id="valueItemNum" placeholder="Items number" name="ItemNumI" value="ItemNum" class="form-control" >
                </div>
                <div class="mb-3">
                    <input type="text" id="value" placeholder="Link" name="linkI" value="" class="form-control" >
                </div>
                <div class="mb-3">
                    <select name="displayMobile" class="form-control">
                        <option >Select Component Display mode in mobile</option>
                        <option value="grid"  >Grid 2 Items In Row</option>
                        <option value="grid3" >Grid 3 Items In Row</option>
                        <option value="slider" >slider</option>
                    </select>
                </div>
                <div class="mb-3">
                    <select name="displayDesktop" class="form-control">
                        <option >Select Component Display mode in Desktop</option>
                        <option value="list" >List</option>
                        <option value="slider">slider</option>
                    </select>
                </div>
                <input type="hidden" name="compNameI" value="CompName">
                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save changes</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Update Item Modal End -->

@endsection



@section('script')

<script src="https://cdn.jsdelivr.net/npm/@shopify/draggable@1.0.0-beta.8/lib/draggable.bundle.js"></script>
    <script>
      
         const sortable = new Draggable.Sortable(
		    document.querySelector('#sortableProdBox'), {
			draggable: 'div',
		}
        )
        sortable.on('sortable:start', () => {
           // console.log('sortable:start')
        })
        sortable.on('sortable:sort', () => {
            //console.log('sortable:sort')
        })
        sortable.on('sortable:sorted', () => {
           //console.log('sortable:sorted')
        })
        sortable.on('sortable:stop', () => {
            console.log('sortable:stop')
            var test=[];
            setTimeout(() => {
              $('#sortableProdBox div').each(function(){
                test.push($(this).data('sort'))
                
              })
              console.log('Done')
              console.log(test)
            }, 200);
     
            // console.log(test)

        })


        $('.SubmitSection').submit(function(e) {
            
            e.preventDefault();

            //form 
            var formD=$(this)
            var form= {}
            $.each($(this).serializeArray(), function(i, field) {
                form[field.name] = field.value;
            });

            //csrf
            form['_token'] = '{{ csrf_token() }}';
            //Update Value 
            $.ajax({
                method:'post',
                url:"{{ route('updateSectionLanding') }}",
                data:form,
                success:function(resp){

                //Display Success Error
                toastr["success"]("Section Successfully Updated")

                
                }
            })
            console.log('Its Working Fine')


        })
    </script>

@endsection