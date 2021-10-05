@extends('layout.master')


@section('content')

  <div class="container-fluid my-1">
    <div class="row">
            <div class="col-sm-4">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#AddConfigBtn">
                    Add New Config
                </button>
            </div>
            @foreach ($Configs as $Config)
                <div class="col-sm-4">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between">
                            <p>{{ $Config['name'] }}</p>
                            <button class="btn btn-info" id="UpdConfig"  data-bs-toggle="modal" data-bs-target="#UpdConfigModal" data-id="{{ $Config['id'] }}" >E</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
  </div>

  @include('Cpanel.configs.widgets.modals')

@endSection



@section('script')

  <script>
    $(document).ready(function() {
        //Change WherePage Input Value
        $('input[name=ConfigTypeI]').val('currency');
    })
  </script>

  @include('Cpanel.configs.widgets.scripts')

@endSection