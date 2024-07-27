@extends('layouts.base')

@section('page_header')
    <div class="dashboard_bar">
        Whatsapp sender
    </div>
@stop

@section('breadcrumbs')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">WA Sender</a></li>
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
                                    <option selected="selected" value="all">All Templates</option>
                                    @foreach($sites as $s)
                                        <option value="{{$s->id}}">{{$s->name}}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2">
                        <div class="form-group">
                            <form method="GET" action="/whatsapp" id="template-form">
                                <div class="form-input">
                                    <select class="form-select form-select-solid" name="wa_template_id" id="waTemplates">
                                        <option selected="selected" value="all">Wa Templates</option>
                                        @foreach($waTemplates as $s)
                                            <option value="{{$s->id}}" data-text="{{ $s->text }}">{{ $s->title }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </form>
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
                                <th>Whatsapp</th>
                            <th>Send</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($m as $c)
                                @php
                                    // Retrieve the selected template text from the request
                                    $selectedTemplate = $waTemplates->where('id', request('wa_template_id'))->first();
                                    $templateText = $selectedTemplate ? $selectedTemplate->text : '';

                                    // Replace placeholders with actual data
                                    $message = str_replace(['{{ name }}', '{{ city }}'], [$c->ad_title, $c->city], $templateText);

                                    // Encode the message for URL
                                    $encodedMessage = urlencode($message);

                                    // Generate the WhatsApp link
                                    $whatsappLink = "https://api.whatsapp.com/send/?phone={$c->formatted_number}&text={$encodedMessage}";
                                @endphp

                                <tr data-id="{{$c->id}}">
                                    <td contenteditable="true" class="editable" data-column="ad_title">{{$c->ad_title}}</td>
                                    <td contenteditable="true" class="editable" data-column="number">{{$c->number}}</td>
                                    <td contenteditable="true" class="editable" data-column="city">{{$c->city}}</td>

                                    <td>
                                        @if($c->active == 0)
                                            <a href="/number/block/{{$c->id}}" class="btn btn-danger shadow sharp" id="block_{{$c->id}}"><i class="fa fa-toggle-off"></i></a>
                                        @else
                                            <a href="/number/unblock/{{$c->id}}" class="btn btn-success shadow sharp" id="block_{{$c->id}}"><i class="fa fa-toggle-on"></i></a>
                                        @endif
                                    </td>
                                    <td class="mx-2">@if($c->bounced == 1)<p>BOUNCED</p>@endif</td>
                                    <td>{{$c->site->name}}</td>
                                    <td>
                                        @if($c->whatsapp == 0)
                                            <a href="/whatsapp/has_whatsapp/{{$c->id}}" id="has_whatsapp_{{ $c->id }}"  class="btn btn-danger shadow sharp"><i class="fa fa-toggle-off"></i></a>
                                        @else
                                            <a href="/whatsapp/has_whatsapp/{{$c->id}}" id="has_whatsapp_{{ $c->id }}"  class="btn btn-success shadow sharp"><i class="fa fa-toggle-on"></i></a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($c->whatsapp == 1)
                                        <a href="{{ $whatsappLink }}" class="btn btn-success shadow btn-xs sharp whatsapp-link" data-number="{{ $c->formatted_number }}" data-name="{{ $c->ad_title }}" data-city="{{ $c->city }}"><i class="fa fa-whatsapp"></i></a>
                                        @endif
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
    <link href="{{ URL::asset('js/select2/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ URL::asset('js/select2/select2.min.js') }}"></script>
    <script src="https://cdn.ckeditor.com/4.17.1/standard/ckeditor.js"></script>
    <script>
        $(document).ready(function() {
            $('div.hamburger').click();

            @if(@$_GET["site_id"])
            $('#sites').val('{{ @$_GET["site_id"] }}');
            @endif

            $('#sites').on("change", function() {
                var site_id = $('#sites').val();
                window.location.href = `/numbers?site_id=${site_id}`;
            });

            $('#waTemplates').on("change", function() {
                $('#template-form').submit(); // Submit the form when a new template is selected
            });

            $('.number_search').select2({
                ajax: {
                    url: '/number/search',
                    type: "POST",
                    data: function(term, page) {
                        return {
                            q: term,
                            _token: "{{ csrf_token() }}"
                        };
                    },
                    results: function(data, page) {
                        return { results: data.results };
                    },
                },
                minimumInputLength: 3,
                formatResult: function(data) { return `${data.name.toUpperCase()}`; },
                formatSelection: function(data) { return `${data.name.toUpperCase()}`; }
            }).on("change", function() {
                var number_id = $('.number_search').select2("val");
                window.location.href = `/numbers?number_id=${number_id}`;
            });

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

            $('#sites').on('change', function() {
                var selectedId = $(this).val(); // Get the selected value
                var url = new URL(window.location.href); // Get the current URL
                url.searchParams.set('site_id', selectedId); // Set the site_id parameter
                window.location.href = url.toString(); // Redirect to the new URL
            });

            @foreach($m as $c)
            $('#block_{{ $c->id }}').on("click", function() {
                $.post(`/number/block`, {
                    'id': "{{ $c->id }}",
                    'block': "{{ $c->block }}",
                    '_token': "{{ csrf_token() }}",
                }, function(response) {
                    // Handle response if needed
                });
            });

            @endforeach

        });
    </script>

@stop
