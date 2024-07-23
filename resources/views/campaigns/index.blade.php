@extends('layouts.base')

@section('page_header')
<div class="dashboard_bar">
Campaigns
</div>
@stop

@section('breadcrumbs')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="javascript:void(0)">Campaigns</a></li>
</ol>
@stop

@section('body')

<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <div class="row">

                <div class="col-xl-2">
                    <a href="/campaign/create" class="btn btn-md btn-primary">Create Campaign</a>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="col-lg-12">
    <div class="card">
        <div class="card-body">

            <div class="row">

                <div class="table-responsive">

                    <table class="table table-responsive-sm" id="campaigns-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>When</th>
                                <th></th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($m as $c)
                        <tr>
                            <td>{{$c->id}}</td>
                            <td>{{$c->name}}</td>
                            <td>{{$c->day}} - {{$c->frequency}}</td>
                            <td>@if($c->status==1) <span class="badge badge-success">Enabled</span> @else <span class="badge badge-danger">Disabled</span> @endif</td>
                            <td>
                                <div class="d-flex">
                                    <a href="/campaign/delete/{{$c->id}}" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
                                    <a href="/campaign/edit/{{$c->id}}" class="btn btn-info shadow btn-xs sharp"><i class="fa fa-edit"></i></a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('footer')
<style>

</style>
<link href="{{URL::asset('js/select2/select2.min.css')}}" rel="stylesheet" />
<script src="{{URL::asset('js/select2/select2.min.js')}}"></script>
<script src="https://cdn.ckeditor.com/4.17.1/standard/ckeditor.js"></script>
<script>
$(document).ready(function(){

    $('div.hamburger').click()

})
</script>
<script>
$(document).ready(function(){


})
</script>

@stop
