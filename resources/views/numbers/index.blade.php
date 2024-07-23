@extends('layouts.base')

@section('page_header')
    <div class="dashboard_bar">
        Numbers
    </div>
@stop

@section('breadcrumbs')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Numbers</a></li>
    </ol>
@stop

@section('body')

    <div class="col-lg-12">

        @include("shared.flash")

        <div class="card">
            <div class="card-body">
                <div class="row">

                    <div class="col-xl-2">
                        <div class="form-group">
                            <div class="form-input">
                                <select class="form-select form-select-solid" id="sites">
                                    <option selected="selected" value="all">All Sites</option>
                                    @foreach($sites as $s)
                                        <option value="{{$s->id}}">{{$s->name}}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4" style="margin-left:50px;">
                        <div class="form-group" style="margin-top:-20px;">
                            <label for="">Search</label>
                            <input type="hidden" class="form-select form-select-solid number_search">
                            <div class="invalid-feedback"></div>
                        </div>
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
                                <th>Number</th>
                                <th>City</th>
                                <th>Active</th>
                                <th>Bounced</th>
                                <th>Site</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($m as $c)
                                <tr data-id="{{$c->id}}">
                                    <td contenteditable="true" class="editable" data-column="ad_title">{{$c->ad_title}}</td>
                                    <td contenteditable="true" class="editable" data-column="number">{{$c->number}}</td>
                                    <td contenteditable="true" class="editable" data-column="city">{{$c->city}}</td>

                                    <td>
                                        @if($c->active==0)
                                            <a href="/number/block/{{$c->id}}" class="btn btn-danger shadow sharp" id="block_{{$c->id}}"><i class="fa fa-toggle-off"></i></a>
                                        @else
                                            <a href="/number/unblock/{{$c->id}}" class="btn btn-success shadow sharp" id="block_{{$c->id}}"><i class="fa fa-toggle-on"></i></a>
                                        @endif
                                    </td>
                                    <td class="mx-2">@if($c->bounced == 1)<p>BOUNCED</p>@endif</td>
                                    <td>{{$c->site->name}}</td>
                                    <td>
                                        <a href="/number/delete/{{$c->id}}" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
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

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row">

                    <div class="col-xl-2">
                        {{ $m->links('shared.paging') }}
                    </div>

                </div>
            </div>
        </div>
    </div>

@stop

@section('footer')
    <style>
        .editable {
            cursor: pointer;
        }
        .editable[contenteditable="true"]:focus {
            outline: none;
            background-color: #f0f0f0;
        }
    </style>
    <link href="{{URL::asset('js/select2/select2.min.css')}}" rel="stylesheet" />
    <script src="{{URL::asset('js/select2/select2.min.js')}}"></script>
    <script src="https://cdn.ckeditor.com/4.17.1/standard/ckeditor.js"></script>
    <script>
        $(document).ready(function(){

            $('div.hamburger').click()

            @if(@$_GET["site_id"])
            $('#sites').val('{{@$_GET["site_id"]}}');
            @endif
            $('#sites').on("change",function(){
                var site_id = $('#sites').val()
                window.location.href = `/numbers?site_id=${site_id}`
            })

            @foreach($m as $c)
            $('#block_{{$c->id}}').on("click",function(){
                $.post(`/number/block`, {
                    'id': "{{$c->id}}",
                    'block': "{{$c->block}}",
                    '_token': "{{csrf_token()}}",
                }, function(response){
                    // Handle response if needed
                })
            })
            @endforeach

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
                formatResult: function(data) { return `${data.name.toUpperCase()}`; },
                formatSelection: function(data) { return `${data.name.toUpperCase()}`; }
            }).on("change",function(){
                var number_id = $('.number_search').select2("val")
                window.location.href = `/numbers?number_id=${number_id}`
            })

            $('#numbers-table').on('blur', '.editable', function() {
                var row = $(this).closest('tr');
                var id = row.data('id');
                var column = $(this).data('column') || 'ad_title'; // Default to 'ad_title'
                var value = $(this).text();

                $.ajax({
                    url: '{{ route('number.update') }}',
                    method: 'POST',
                    data: {
                        id: id,
                        column: column,
                        value: value,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        // Handle success
                    },
                    error: function(xhr) {
                        // Handle error
                    }
                });
            });
        })
    </script>

@stop
