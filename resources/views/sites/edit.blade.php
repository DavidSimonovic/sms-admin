@extends('layouts.base')

@section('page_header')
    <div class="dashboard_bar">
        Edit Site
    </div>
@stop

@section('breadcrumbs')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/sites">Templates</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0)">Edit Site</a></li>
    </ol>
@stop


@section('body')
    <div class="col-lg-12" id="step_1">
        <div class="card">
            <div class="card-body">

                <div class="basic-form">

                    <form method="post" action="/sites/update/{{$m->id}}" autocomplete="off" data-parsley-validate>

                        <h4 class="card-title">Name</h4>
                        <div class="form-row" style="margin-bottom:15px;">
                            <div class="col-sm-6">
                                <input data-parsley-error-message="Name is required"  data-parsley-required type="text" name="name" id="name" class="form-control" value="{{$m->name}}" autocomplete="off" required>
                            </div>
                        </div>

                        <h4 class="card-title">Message</h4>
                        <div class="form-row" style="margin-bottom:15px;">
                            <div class="col-sm-6">
                                <input data-parsley-error-message="Site URL is required"  data-parsley-required name="site_url" id="site_url" value="{{$m->site_url}}" class="form-control">
                            </div>
                        </div>

                        <h4 class="card-title">Script</h4>
                        <div class="form-row" style="margin-bottom:15px;">
                            <div class="col-sm-6">
                                <input data-parsley-error-message="Script is required"  data-parsley-required name="script" id="script" value="{{$m->script}}" class="form-control">
                            </div>
                        </div>

                        <input type="hidden" name="id" value="{{$m->id}}">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <button type="submit" class="btn btn-lg btn-primary">Save</button>

                    </form>

                </div>

            </div>
        </div>
    </div>

@stop

@section('footer')
    <link href="{{URL::asset('js/parsley/parsley.css')}}" rel="stylesheet" media="screen">
    <script src="{{URL::asset('js/parsley/parsley.min.js')}}"></script>
    <script>
        $(document).ready(function(){

            $('div.hamburger').click()

            /* parsley form validation config */
            window.ParsleyConfig = {
                errorsWrapper: '<div class="parsley-errors-list"></div>',
                errorTemplate: '<span></span>',
                errorsContainer: function (ParsleyField) {
                    var element = ParsleyField.$element;
                    if( (element.is(':checkbox') || element.is(':radio')) && element.parent().is('label') ) {
                        return element.closest('div');
                    }
                },
                validators: {
                    selectmin: {
                        fn: function (value, requirement) {
                            if($("#media_type").val().length == "" || $("#creative_id").val() == "")
                            {return true}
                            else {return false}
                        },
                        priority: 32
                    }
                }
            };

        })
    </script>
@stop
