@extends('layouts.base')

@section('page_header')
<div class="dashboard_bar">
Whatsapp Templates
</div>
@stop

@section('breadcrumbs')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="javascript:void(0)">Whatsapp Templates</a></li>
</ol>
@stop

@section('body')

<div class="col-lg-12">

    @include("shared.flash")

    <div class="card">
        <div class="card-body">
            <div class="row">

                <div class="col-xl-2">
                    <a href="/whatsapp_templates/create" class="btn btn-md btn-primary">Create Whatsapp Template</a>
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

                    <table class="table table-responsive-sm" id="templates-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($m as $c)
                        <tr>
                            <td>{{$c->id}}</td>
                            <td>{{$c->title}}</td>
                            <td>
                                <div class="d-flex">
                                    <a href="/whatsapp_templates/delete/{{$c->id}}" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
                                    <a href="/whatsapp_templates/edit/{{$c->id}}" class="btn btn-info shadow btn-xs sharp"><i class="fa fa-edit"></i></a>
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
