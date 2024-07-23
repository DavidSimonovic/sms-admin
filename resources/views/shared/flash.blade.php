@if( Session::has('warning'))
<div class="alert alert_submit alert-danger"><button data-dismiss="alert" class="close" type="button">×</button><strong>Oops!</strong> {{ Session::get('warning') }}</div>
@endif
@if( Session::has('message'))
<div class="alert alert_submit alert-success"><button data-dismiss="alert" class="close" type="button">×</button><strong>Nice!</strong> {{ Session::get('message') }}</div>
@endif
