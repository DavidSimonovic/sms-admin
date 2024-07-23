@extends('layouts.base')

@section('page_header')
<div class="dashboard_bar">
Write SMS
</div>
@stop

@section('breadcrumbs')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="javascript:void(0)">Write SMS</a></li>
</ol>
@stop


@section('body')

<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <div class="row">

                <div class="col-xl-2">
                    <div class="form-group">
                        <div class="form-input">
                            <select class="form-select form-select-solid" id="sites">
                                <option selected="selected" value="single">Single Number</option>
                                @foreach($sites as $s)
                                <option value="{{$s->id}}">{{$s->name}}</option>
                                @endforeach
                            </select>
                        <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                <div id="search_div" class="col-xl-4" style="margin-left:50px;display:none;">
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

<div class="col-lg-12" id="step_1">
    <div class="card">
        <div class="card-body">

        <div id="response_success" style="display:none;" class="alert alert_submit alert-success"><button data-dismiss="alert" class="close" type="button">×</button><span class="response"></span> </div>
        <div id="response_error" style="display:none;" class="alert alert_submit alert-success"><button data-dismiss="alert" class="close" type="button">×</button> <span class="response"></span> </div>

            <div class="basic-form">

                    <form autocomplete="off" data-parsley-validate>

                        <h4 class="card-title">Number</h4>
                        <div class="form-row" style="margin-bottom:15px;">
                            <div class="col-sm-12">
                                <input type="text" id="number" class="form-control" autocomplete="off">
                            </div>
                        </div>

                        <h4 class="card-title">Message</h4>
                        <div class="form-row" style="margin-bottom:15px;">
                            <div class="col-sm-12">
                                <textarea id="message" class="form-control"></textarea>
                            </div>
                        </div>

                        <button id="send" type="button" class="btn btn-lg btn-primary">Send</button>

                    </form>

                 </div>

            </div>
        </div>
    </div>

@stop

@section('footer')
<link href="{{URL::asset('js/parsley/parsley.css')}}" rel="stylesheet" media="screen">
<script src="{{URL::asset('js/parsley/parsley.min.js')}}"></script>
<link href="{{URL::asset('js/select2/select2.min.css')}}" rel="stylesheet" />
<script src="{{URL::asset('js/select2/select2.min.js')}}"></script>
<script>
$(document).ready(function(){

    $('div.hamburger').click()

    $('#sites').on("change",function(){


        if($(this).val()!="single"){
            $('#search_div').show()
        }else{

            $('#search_div').hide()
            $('#number').val("+49")
        }

    })

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
                    site_id: $('#sites').val(),
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
        console.log($(this).select2('data'));

        $('#number').val($(this).select2('data').number)

    })


    $('#send').on("click",function(){

        if($('#number').val()=="" || $('#message').val() ==""){
            alert("Please enter a valid number and message text")
            return false
        }

        if($('#number').val().length < 11 || $('#message').val().length < 5){
            alert("Please enter a valid number length and message length minimum of 10 characters")
            return false
        }



        $('#response_success').hide()
        $('#response_error').hide()
        $('.response').html("")

        $(this).attr('disabled',true)
        $(this).html('Sending...')

        $.post(`/sendsms`, {
            'number':$('#number').val(),
            'message': $('#message').val(),
            '_token': "{{csrf_token()}}",
        }, function(response){

            console.log(response)

            if(response.error){

                $('#response_error').show()
                $('.response').html(response.message)

            }else{

                $('#response_success').show()
                $('.response').html("Message Sent.")
            }

            $('#send').attr('disabled',false)
            $('#send').html('Send')
        })

    })

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
