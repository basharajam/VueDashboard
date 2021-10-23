<script>
    
    //Update Item
    $('.SubmitSection').submit(function(e) {
        
        e.preventDefault();

        var compName =$('#TransKey').val();

        //form 
        var formD=$(this)
        
        var title=$('#title').val();
        var value =$('#value').val();
        var type =$('#type').val();
        var ItemNum =$('#ItemNum').val();
        var displayMobile =$('#displayMobile').val();
        var displayDesktop =$('#displayDesktop').val();
        var compName =$('#TransKey').val();
        var compId=$('#compId').val();
        var link =$('#link').val();
        var form= {
            title,
            value,
            type,
            ItemNum,
            displayMobile,
            displayDesktop,
            compName,
            link,
            compId,
            _token:'{{ csrf_token() }}'
        };
        console.log(form)

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

    $(document).on('click','.UpdItem',function(){

        //Get Item ID 
        var id=$(this).data('id');

        if(id !== null && id !== undefined && id !== ''){

            var form = {
                id:id
            }
            //get Item Data
            $.ajax({
                url:'{{ route("getLayout") }}',
                method:'post',
                data:form,
                success:function(resp){
                    console.log(resp)

                    //fill inputs
                    $('#title').val(resp.title)
                    $('#value').val(resp.value)
                    $('#type').val(resp.type)
                    $('#ItemNum').val(resp.itemNum)
                    $('#displayMobile').val(resp.mobileDisplay)
                    $('#displayDesktop').val(resp.Display)
                    $('#TransKey').val(resp.compName)
                    $('#link').val(resp.link) 
                    $('#compId').val(resp.id)

                }})
            }
        })

        //Delete Component Set Comp Id
        $(document).on('click','.DelComp',function() {
            
            var id = $(this).data('id');

            //Set Input value 
            $('input[name=CompIdI]').val(id);

        })
</script>