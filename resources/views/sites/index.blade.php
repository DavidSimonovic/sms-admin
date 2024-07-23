@extends('layouts.base')

@section('page_header')
    <div class="dashboard_bar">
        Sites
    </div>
@stop

@section('breadcrumbs')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Sites</a></li>
    </ol>
@stop

@section('body')

    <div class="col-lg-12">

        @include("shared.flash")
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">

                        <div class="col-xl-2">
                            <a href="/sites/create" class="btn btn-md btn-primary">Create Site</a>
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

                        <table class="table table-responsive-sm" id="numbers-table">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Site Link</th>
                                <th>Script name</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($sites as $c)
                                <tr>
                                    <td>{{$c->name}}</td>
                                    <td>{{$c->site_url}}</td>
                                    <td>{{ $c->script }}</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="/sites/delete/{{$c->id}}" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
                                            <a href="/sites/edit/{{$c->id}}" class="btn btn-info shadow btn-xs sharp"><i class="fa fa-edit"></i></a>
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


            @if(@$_GET["site_id"])
            $('#sites').val('{{@$_GET["site_id"]}}');
            @endif
            $('#sites').on("change",function(){

                var site_id = $('#sites').val()
                window.location.href = `/numbers?site_id=${site_id}`

            })

            @foreach($sites as $c)

            $('#block_{{$c->id}}').on("click",function(){

                $.post(`/number/block`, {
                    'id': "{{$c->id}}",
                    'block': "{{$c->block}}",
                    '_token': "{{csrf_token()}}",
                }, function(response){

                })

            })

            @endforeach

            function formatSelect (data) {
                if (data.loading) {
                    return "Loading";
                }
                //return data.name
                return `${data.name.toUpperCase()}`;
            }

            $('.number_search').select2({
                ajax: {
                    url: '/number/search',
                    type: "POST",
                    data: function (term, page) {
                        return {
                            q: term,
                            _token: "{{csrf_token()}}"
                        };
                    },
                    results: function (data, page) {
                        return { results: data.results };
                    },

                },
                minimumInputLength: 3,
                formatResult: formatSelect,
                formatSelection: formatSelect

            }).on("change",function(){

                var number_id = $('.number_search').select2("val")
                window.location.href = `/numbers?number_id=${number_id}`

            })

        })
    </script>

@stop
