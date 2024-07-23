@extends('layouts.base')

@section('page_header')
    <div class="dashboard_bar">
        Edit Campaign
    </div>
@stop

@section('breadcrumbs')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/campaigns">Campaigns</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0)">Edit Campaign</a></li>
    </ol>
@stop

@section('body')
    <div class="col-lg-12" id="step_1">
        <div class="card">
            <div class="card-body">
                <div class="basic-form">
                    <form method="post" action="/campaign/update" autocomplete="off" data-parsley-validate>
                        @csrf
                        <input type="hidden" name="id" value="{{$campaign->id}}">

                        <h4 class="card-title">Name</h4>
                        <div class="form-row" style="margin-bottom:15px;">
                            <div class="col-sm-12">
                                <input data-parsley-error-message="Name is required" data-parsley-required type="text" name="name" id="name" class="form-control" value="{{$campaign->name}}" autocomplete="off" required>
                            </div>
                        </div>

                        <h4 class="card-title">Site</h4>
                        <div class="form-row" style="margin-bottom:15px;">
                            <div class="col-sm-12">
                                <select class="form-select form-select-solid" id="sites" name="sites[]" multiple>
                                    @foreach($sites as $s)
                                        <option value="{{$s->id}}" @if(in_array($s->id, json_decode($campaign->site_ids, true))) selected @endif>
                                            {{$s->name}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <h4 class="card-title">Templates</h4>
                        @foreach($templates as $template)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="{{$template->id}}" name="template[]" id="check_{{$template->id}}" @if(in_array($template->id, json_decode($campaign->template_ids, true))) checked @endif>
                                <label class="form-check-label" for="check_{{$template->id}}">
                                    {{$template->title}}
                                </label>
                            </div>
                        @endforeach

                        <h4 class="card-title" style="margin-top:10px;">Frequency</h4>
                        <div class="form-row" style="margin-bottom:15px;">
                            <div class="col-sm-12">
                                <select class="form-select form-select-solid" id="frequency" name="frequency">
                                    <option value="weekly" @if($campaign->frequency == 'weekly') selected @endif>Weekly</option>
                                    <option value="monthly" @if($campaign->frequency == 'monthly') selected @endif>Monthly</option>
                                    <option value="test" @if($campaign->frequency == 'test') selected @endif>TEST (5min)</option>
                                </select>
                            </div>
                        </div>

                        <h4 class="card-title">Day</h4>
                        <div class="form-row" style="margin-bottom:15px;">
                            <div class="col-sm-12">
                                <select class="form-select form-select-solid" id="day" name="day">
                                    <option value="monday" @if($campaign->day == 'monday') selected @endif>Monday</option>
                                    <option value="tuesday" @if($campaign->day == 'tuesday') selected @endif>Tuesday</option>
                                    <option value="wednesday" @if($campaign->day == 'wednesday') selected @endif>Wednesday</option>
                                    <option value="thursday" @if($campaign->day == 'thursday') selected @endif>Thursday</option>
                                    <option value="friday" @if($campaign->day == 'friday') selected @endif>Friday</option>
                                    <option value="saturday" @if($campaign->day == 'saturday') selected @endif>Saturday</option>
                                    <option value="sunday" @if($campaign->day == 'sunday') selected @endif>Sunday</option>
                                </select>
                            </div>
                        </div>

                        <h4 class="card-title">Status</h4>
                        <div class="form-row" style="margin-bottom:15px;">
                            <div class="col-sm-12">
                                <select class="form-select form-select-solid" id="status" name="status">
                                    <option value="1" @if($campaign->status == 1) selected @endif>Enabled</option>
                                    <option value="0" @if($campaign->status == 0) selected @endif>Disabled</option>
                                </select>
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
    <link href="{{URL::asset('js/parsley/parsley.css')}}" rel="stylesheet" media="screen">
    <script src="{{URL::asset('js/parsley/parsley.min.js')}}"></script>
    <script>
        $(document).ready(function(){
            $('div.hamburger').click();

            // Initialize Parsley validation
            $('form').parsley();

            // Set selected values
            $('#sites').val({!! json_encode(json_decode($campaign->site_ids, true)) !!}).trigger('change');
            $('#frequency').val("{{$campaign->frequency}}").trigger('change');
            $('#day').val("{{$campaign->day}}").trigger('change');
            $('#status').val("{{$campaign->status}}").trigger('change');
        });
    </script>
@stop
