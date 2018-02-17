@extends('layout')
@section('content')

   <div class="row">
      <div class="col-lg-3"></div>
      <div class="col-lg-6">
         <form id="form">

         <legend>Tracking</legend>

         <div class="form-group">
            <label for="s_name">Tracking Code</label>
            <input type="text" class="form-control" name="code"  id="code"  value="{{$code OR "" }}"></input>   
            <p id="track-error" class="red help-block"></p>
         </div>
         </form>

         <button  id="track-form-btn" type="submit" class="btn btn-primary">Search</button>
         <button  id="offline-btn" type="submit" class="debug btn btn-danger">Extract data from tracking code</button>
      </div>
   </div>




<div class="row">
   <ul id="info" class="col list-group">
   </ul>
</div>


@endsection
