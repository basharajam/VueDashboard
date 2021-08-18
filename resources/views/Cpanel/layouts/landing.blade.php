@extends('layout.master')



@section('content')

    <div class="container">
        <div class="row">
            <div class="col-sm-8">

                @foreach ($Layout as $item)
                    <form class="SubmitSection" data-comp="{{ $item['compName'] }}">
                        <label class="form-label">Section // ProdByTax</label>
                        <div class="mb-3">
                            <input type="text" id='title{{ $item['compName']}}' placeholder="Section Title" name="SectionTitleI" value="{{ $item['title'] }}"  class="form-control"  >
                        </div>
                        <div class="mb-3">
                            <input type="number" id="value{{ $item['compName']}}" placeholder="Value" name="SectionValI" value="{{ $item['value'] }}" class="form-control" >
                        </div>
                        <div class="mb-3">
                            <select name="ProdByTaxType" id="type{{ $item['compName']}}" name="SectionTypeI" class="form-control" disabled>
                                <option value="Tag" @if ($item['type'] === 'tag') selected   @endif >Tag</option>
                                <option value="Category" @if ($item['type'] === 'Category') selected   @endif >Category</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="number" id="value{{ $item['itemNum']}}" placeholder="Items number" name="ItemNumI" value="{{ $item['itemNum'] }}" class="form-control" >
                        </div>
                        <input type="hidden" name="compNameI" value="{{ $item['compName'] }}">
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                @endforeach
            </div>
        </div>
    </div>

@endsection



@section('script')
    

    <script>
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