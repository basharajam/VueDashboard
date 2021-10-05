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


$(document).on('click','#UpdConfig',function(){

    var configId=$(this).data('id')
    var form={
        id:configId
    }

    $.ajax({
        method:'post',
        url:'{{ route("getConfig") }}',
        data:form,
        success:function(resp){

            //Fill Inputs 
            $('input[name=ConfigNameUI]').val(resp.name);
            $('input[name=ConfigKeyUI]').val(resp.key);
            $('input[name=ConfigTypeUI]').val(resp.type);
            $('input[name=ConfigValueUI]').val(resp.value);
            $('input[name=ConfigSubValueUI]').val(resp.subValue);
            $('input[name=ConfigIdI]').val(resp.id)
        }
    })
})





$(document).on('click','#UpdConfigBtn',function(){

    var ConfigNameUI=$('input[name=ConfigNameUI]').val();
    var ConfigKeyUI=$('input[name=ConfigKeyUI]').val();
    var ConfigTypeUI=$('input[name=ConfigTypeUI]').val();
    var ConfigValueUI=$('input[name=ConfigValueUI]').val();
    var ConfigSubValueUI=$('input[name=ConfigSubValueUI]').val();
    var ConfigIdUI=$('input[name=ConfigIdI]').val();
    var form={
        ConfigNameUI,
        ConfigKeyUI,
        ConfigTypeUI,
        ConfigValueUI,
        ConfigSubValueUI,
        ConfigIdUI,
        _token:'{{ csrf_token() }}'
    }

    $.ajax({
        method:'post',
        url:'{{ route("UpdConfig") }}',
        data:form,
        success:function(resp){

            console.log(resp)

        }
    })
})

</script>