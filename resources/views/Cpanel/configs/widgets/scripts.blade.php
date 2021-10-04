<script>



// SaveConfigBtn
$(document).on('click','.SaveConfigBtn',function(e){

    e.preventDefault();


    //Save Config Form
    var ConfigNameI=$('input[name=ConfigNameI]').val();
    var ConfigKeyI=$('input[name=ConfigKeyI]').val();
    var ConfigTypeI=$('input[name=ConfigTypeI]').val();
    var ConfigValueI=$('input[name=ConfigValueI]').val();
    var ConfigSubValueI=$('input[name=ConfigSubValueI]').val();
    var form={
        ConfigNameI,
        ConfigKeyI,
        ConfigTypeI,
        ConfigValueI,
        ConfigSubValueI,
        _token:'{{ csrf_token() }}'
    }

    //Save Config Request 
    $.ajax({
        method:'post',
        url:'{{ route("SaveConfig") }}',
        data:form,
        success:function(resp){

            console.log(resp)

            //Updaate Config List

                //remove Old Config

                //Display New Config list

        }
    }) 



})

</script>