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
<style>
    .center-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100%;
    }
</style>
@section('body')
    <div class="container mt-5">
        <div class="row">
            <!-- Left Column for Pie Chart -->
            <div class="col-md-6 center-content">
                <h2 class="text-center">Total numbers: {{ $totalNumbers }}</h2>
                <canvas id="myPieChart"></canvas>
            </div>
            <!-- Right Column -->
            <div class="col-md-6">
                <h1 class="text-center">SMS</h1>
                <h3>SMS sent total:  {{ $smsSent }}</h3>
                <h3>SMS sent today:  {{ $smsSentToday }}</h3>
                <h3>SMS sent this month:  {{ $smsSentMonth }}</h3>
                <br>
                <h1 class="text-center">Whatsapp</h1>
                <h3>Wa sent total: {{ $waSent }}</h3>
                <h3>Wa sent today: {{ $waSentToday }}</h3>
                <h3>Wa sent this month: {{ $waSentMonth }}</h3>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var ctx = document.getElementById('myPieChart').getContext('2d');
            var myPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($labels) !!},
                    datasets: [{
                        data: {!! json_encode($data) !!},
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.label + ': ' + tooltipItem.raw;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

@stop

@section('footer')
    <style>

    </style>
    <link href="{{URL::asset('js/select2/select2.min.css')}}" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
