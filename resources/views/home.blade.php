@extends('layout')

@section('content')
<div class="row">
    <div class="col-md-10">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                    <div class="col">
                        <div id="msg"></div>
                    </div>
                </div>
                <form id="form">
                    <div class="row">
                        <div class="col">
                            <legend>Sender</legend>
                            <div class="form-group">
                                <label for="s_name">
                                    Sender*
                                </label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input aria-describedby="s_name_help" class="form-control" id="s_fname" name="s_fname" type="text" value="Andrea">
                                        </input>
                                    </div>
                                    <div class="col-md-6">
                                        <input aria-describedby="s_name_help" class="form-control" id="s_lname" name="s_lname" type="text" value="Paschulke">
                                        </input>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="s_street">
                                    Full Street*
                                </label>
                                <input class="form-control" id="s_street" name="s_street" type="text" value="Diekkamp 39">
                                </input>
                            </div>
                            <div class="form-group">
                                <label for="s_plz">
                                    Postcode*
                                </label>
                                <input class="form-control" id="s_plz" name="s_plz" type="text" value="22359">
                                </input>
                            </div>
                            <div class="form-group">
                                <label for="s_ort">
                                    City*
                                </label>
                                <input ,="" class="form-control" id="s_ort" name="s_ort" type="text" value="Hamburg">
                                </input>
                            </div>
                            <div class="form-group">
                                <label for="s_ort">
                                    Country*
                                </label>
                                <input ,="" class="form-control" id="s_country" name="s_country" type="text" value="Germany">
                                </input>
                            </div>
                        </div>
                        <div class="col">
                            <legend>
                                Receiver
                            </legend>
                            <div class="form-group">
                                <label for="s_name">
                                    Receiver*
                                </label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input aria-describedby="s_name_help" class="form-control" id="r_fname" name="r_fname" type="text" value="Max">
                                        </input>
                                    </div>
                                    <div class="col-md-6">
                                        <input aria-describedby="r_name_help" class="form-control" id="r_lname" name="r_lname" type="text" value="Müller">
                                        </input>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="r_street">
                                    Full Street*
                                </label>
                                <input class="form-control" id="r_street" name="r_street" type="text" value="Raintaler Str. 40">
                                </input>
                            </div>
                            <div class="form-group">
                                <label for="r_plz">
                                    Postcode*
                                </label>
                                <input class="form-control" id="r_plz" name="r_plz" type="text" value="81539">
                                </input>
                            </div>
                            <div class="form-group">
                                <label for="r_ort">
                                    City*
                                </label>
                                <input class="form-control" id="r_ort" name="r_ort" type="text" value="München">
                                </input>
                            </div>
                            <div class="form-group">
                                <label for="s_ort">
                                    Country*
                                </label>
                                <input ,="" class="form-control" id="r_country" name="r_country" type="text" value="Germany">
                                </input>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 form-group">
                            {!! Form::label('type', 'Parcel Type', ['class' => 'control-label']) !!}
                            {!! Form::select('type', $types, old('type'), ['class' => 'form-control select2', 'required' => '']) !!}

                        </div>
                    </div>
                    <button class="btn btn-primary" id="form-btn" type="submit">
                        Check Data
                    </button>
                </form>
                <div class="row">
                    <div class="col">
                        <span id="sender-check">
                        </span>
                    </div>
                    <div class="col">
                        <span id="receiver-check">
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12" id="info">
                        <div data-lat="" data-lng="" id="sender_coords">
                        </div>
                        <div data-lat="" data-lng="" id="receiver_coords">
                        </div>
                        <div id="process-div">
                            <button class="btn btn-success" id="process-btn" type="submit">
                                Generate Tracking Code and Save Parcel
                            </button>
                        </div>
                        <div id="tracking">
                        </div>
                    </div>
                </div>
                <div class="row pad-top-20">
                    <div class="col-md-12">
                        <div id="map-canvas">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    </script>
    @endsection
</div>