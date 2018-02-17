@extends('layout')

@section('content')
<form id="form">
    <div class="row padded">
        <div class="col-md-6">
            <legend>
                Tracking
            </legend>
            <div class="form-group">
                <label for="s_name">
                    Firstly insert the Tracking code
                </label>
                <input aria-describedby="code_help" class="form-control" id="code" name="code" placeholder="Sendungsnummer" type="text" value="{{$code}}">
                </input>
                 <p id="track-error" class="help-block red"></p>
            </div>
           <button class="btn btn-primary" id="tracking-btn" type="submit">Next</button>
           <button class="debug btn btn-danger" id="offline-btn" style="display: none;" type="submit">Extract data from tracking code</button>
        </div>
        <div class="col-md-5 red-border" id="status-div" style="display: none">
            <legend>
                Change status
            </legend>
            <div id="status-fields" class="form-group">
                <label for="status-inp">
                    Simulate status change:
                </label>
                <form id="status-form"> 
                {!! Form::select('status-inp', $statuses, null, array('id'=>'status-inp', 'class' => 'form-control')) !!}
                 </form>  
            </div>
            <button class="btn btn-primary" id="status-add-btn" type="submit">Simulate</button>
        </div>
    </div>

</form>
<div class="row">
    <ul class="col list-group" id="info">
    </ul>
</div>

@endsection
