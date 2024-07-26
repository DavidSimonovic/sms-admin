@extends('layouts.base')

@section('page_header')
    <div class="dashboard_bar">
        Create Campaign
    </div>
@stop

@section('breadcrumbs')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/campaigns">Campaigns</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0)">Create Campaign</a></li>
    </ol>
@stop

@section('body')
    <div class="col-lg-12" id="step_1">
        <div class="card">
            <div class="card-body">
                <div class="basic-form">
                    <form method="post" action="/campaign/save" autocomplete="off" data-parsley-validate>
                        @csrf <!-- CSRF token -->

                        <h4 class="card-title">Name</h4>
                        <div class="form-row" style="margin-bottom:15px;">
                            <div class="col-sm-12">
                                <input data-parsley-error-message="Name is required" data-parsley-required type="text" name="name" id="name" class="form-control" value="" autocomplete="off" required>
                            </div>
                        </div>

                        <h4 class="card-title">Sites</h4>
                        <div class="form-row" style="margin-bottom:15px;">
                            <div class="col-sm-12">
                                <select class="form-select form-select-solid" id="sites" name="sites[]" multiple>
                                    @foreach($sites as $s)
                                        <option value="{{$s->id}}">{{$s->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <h4 class="card-title">Templates</h4>
                        @foreach($templates as $template)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="{{$template->id}}" name="template[]" id="check_{{$template->id}}">
                                <label class="form-check-label" for="check_{{$template->id}}">
                                    {{$template->title}}
                                </label>
                            </div>
                        @endforeach


                        <h4 class="card-title">Status</h4>
                        <div class="form-row" style="margin-bottom:15px;">
                            <div class="col-sm-12">
                                <select class="form-select form-select-solid" id="status" name="status">
                                    <option value="1">Enabled</option>
                                    <option value="0">Disabled</option>
                                </select>
                            </div>
                        </div>

                        <h4 class="card-title">Originator</h4>
                        <div class="form-row" style="margin-bottom:15px;">
                            <div class="col-sm-12">
                                <input data-parsley-error-message="Originator is required" data-parsley-required type="text" name="originator" id="originator" class="form-control" value="" autocomplete="off" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-lg btn-primary">Save</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer')
    <!-- Include Parsley CSS and JS -->
    <link href="{{ URL::asset('js/parsley/parsley.css') }}" rel="stylesheet" media="screen">
    <script src="{{ URL::asset('js/parsley/parsley.min.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('div.hamburger').click();
        });
    </script>

    <script>
        // Initialize Parsley validation
        $(document).ready(function(){
            $('form').parsley();
        });
    </script>
@stop
