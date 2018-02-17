(function(window, document, undefined) {
    // code that should be taken care of right away
    var addresses = []
    window.onload = init;

    function init() {
        // the code to be called when the dom has loaded
        // #document has its nodes
        /****************************************************************************************************************
         * 
         *                                                                                                              
         *
         *      GOOGLE MAPS API 
         *
         *      
         *
         ****************************************************************************************************************/
        /**
         * Creates map with adjusted options and draws a path
         * from sender to receiver
         * @param {sender} LatLon object of sender coordinates
         * @param {receiver} LatLon object of receiver coordinates
         * @return {map} Google Map in #map-canvas div
         */
        function initMap(sender,receiver, form) {
            var pointA = new google.maps.LatLng(sender.lat, sender.lng);
            var pointB = new google.maps.LatLng(receiver.lat, receiver.lng);
            var pathCoordinates = 
                [ { lat: sender.lat,  lng: sender.lng   }, 
                  { lat: receiver.lat,lng: receiver.lng }
                ];
            var myOptions = {
                zoom: 5,
                center: pointA,
                mapTypeId: google.maps.MapTypeId.HYBRID,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
                },
                navigationControlOptions: {
                    style: google.maps.NavigationControlStyle.SMALL,
                },
                streetViewControl: false,
                scaleControl: true,
            };
            map = new google.maps.Map(document.getElementById('map-canvas'), myOptions),
                // Instantiate a directions service.
                // directionsService = new google.maps.DirectionsService,
                // directionsDisplay = new google.maps.DirectionsRenderer({
                //     map: map
                // }),
                markerA = new google.maps.Marker({
                    position: pointA,
                    title: "point A",
                    label: "A",
                    map: map
                }),
                markerB = new google.maps.Marker({
                    position: pointB,
                    title: "point B",
                    label: "B",
                    map: map
                });
            var path = new google.maps.Polyline({
                path: pathCoordinates,
                geodesic: true,
                strokeColor: '#FF0000',
                strokeOpacity: 1.0,
                strokeWeight: 2
            });
            var sender_window = new google.maps.InfoWindow();
            sender_window.setContent("<div class='infoWindow'>"+ 
                                print(form.s_fname) + " " + print(form.s_lname) + "<br>" + 
                                print(form.s_street) + "<br>" + 
                                print(form.s_plz) + " " +  print(form.s_ort) + "<br>" + 
                                print(form.s_country) + "</div>");
            sender_window.open(map, markerA);
            var receiver_window = new google.maps.InfoWindow();
            receiver_window.setContent("<div class='infoWindow'>"+ 
                                print(form.r_fname) + " " + print(form.r_lname) + "<br>" + 
                                print(form.r_street) + "<br>" + 
                                print(form.r_plz) + " " +  print(form.r_ort) + "<br>" + 
                                print(form.r_country) + "</div>");
            receiver_window.open(map, markerB);
            path.setMap(map);
            // get route from A to B
            //calculateAndDisplayRoute(directionsService, directionsDisplay, pointA, pointB);
        }
        //could be used to display possible route
        /*
        function calculateAndDisplayRoute(directionsService, directionsDisplay, pointA, pointB) {
            directionsService.route({
                origin: pointA,
                destination: pointB,
                avoidTolls: true,
                avoidHighways: false,
                travelMode: google.maps.TravelMode.DRIVING
            }, function (response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(response);
                } else {
                    window.alert('Directions request failed due to ' + status);
                }
            });
        }
        */
        /**
         * Creates map with adjusted options and draws a path
         * from sender to receiver with info boces
         * @param {sender} LatLon object of sender coordinates
         * @param {receiver} LatLon object of receiver coordinates
         * @return {map} Google Map in #map-canvas div
         */
        function getLocationCoords(address, callback) {
            var r;
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                'address': address
            }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var result = results[0].geometry.location
                } else {
                    $('#msg').html('<div class="alert alert-danger"><strong>Error! </strong>' + 'Please check your address</div>');
                    $('#process-btn').hide();
                }
                var latlng = {
                    lat: result.lat(),
                    lng: result.lng()
                };
                callback(latlng);
            });
        }
        /**
         * Reverse address search
         * from sender to receiver with info boces
         * @param {lat} address lat coord
         * @param {lon} address lon coord
         * @return {console.log} logs adress if found
         */
        function geocodeLatLng(coord) {
            var geocoder = new google.maps.Geocoder;
            var latlng = {   lat: coord.lat,  lng: coord.lng   };
            geocoder.geocode({
                'location': latlng
            }, function(results, status) {
                if (status === 'OK') {
                    if (results[0]) {
                        console.log(results[0].formatted_address);
                    } else {
                        window.alert('No results found');
                    }
                } else {
                    window.alert('Geocoder failed due to: ' + status);
                }
            });
        }
        /***************************************************************************************************************
         *
         *      FORMS, AJAX
         *
         *      EVENT LISTENERs
         *
         *      TRIGGERS
         *
         ****************************************************************************************************************/
        /**
         *  Set CSRF token for laravel
         */
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        /**
         * Home Page
         *
         * Search coordinates with google api and if
         * show on map if found + allow to save theParcel
         *
         */
        var form;
        $("#form-btn").click(function(e) {
            var adresses = {};
            e.preventDefault();
            addresses = [];
            form = $('#form').serializeArray().reduce(function(obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});
            disableButtons();
            var addr_s = form.s_street + "," + form.s_plz + "," + form.s_ort + "," + form.s_country;
            var addr_r = form.r_street + "," + form.r_plz + "," + form.r_ort + "," + form.r_country;
            getLocationCoords(addr_s, function(address) {
                $('#sender_coords').attr('data-lat', address.lat);
                $('#sender_coords').attr('data-lng', address.lng);
                adresses.sender = address;
                checkIfBothOk(adresses);
                enableButtons();
                console.log("[Google Map Api] Sender address found at: " + address.lat + ","+ address.lng);
            });
            getLocationCoords(addr_r, function(address) {
                $('#receiver_coords').attr('data-lat', address.lat);
                $('#receiver_coords').attr('data-lng', address.lng);
                console.log("[Google Map Api] Receiver address found at: " + address.lat + ","+ address.lng);
                adresses.receiver = address;
                checkIfBothOk(adresses);
                enableButtons();
            });
        });
        /**
         * 
         * Check if sender and receiver addresses a OK
         * 
         */
        function checkIfBothOk(adresses) {
            //both adresses

            if (isValid(adresses.sender) && isValid(adresses.receiver)) {
                $('#process-btn').fadeIn("slow");
                initMap(adresses.sender, adresses.receiver, form);
            } 
            // else {
            //   console.log('else')
            //     //else show if wrong coords
            //     $.each(adresses, function(index, value) {
            //         if (isValid(value)) {
            //             $('#msg').html('<div class="alert alert-danger"><strong>Error!</strong> Coordinates were not found...</div>');
            //             $('#process-btn').hide();
            //         }
            //     });
            // }
        }
        /**
         * Home Page
         *
         * Save Parcel to database
         *
         */
        $("#process-btn").click(function(e) {
            disableButtons();

            $('#form-btn').removeClass('disabled');
            $('#process-btn').addClass('disabled');
            var sender = {
                lat: $('#sender_coords').attr('data-lat'),
                lng: $('#sender_coords').attr('data-lng')
            };
            var receiver = {
                lat: $('#receiver_coords').attr('data-lat'),
                lng: $('#receiver_coords').attr('data-lng')
            };

            $.ajax({
                url: './api/v1/parcel/create',
                type: 'POST',
                data: {
                    'sender': sender,
                    'receiver': receiver,
                    'type': form.type,
                    's_fname': form.s_fname,
                    's_lname': form.s_lname,
                    's_street': form.s_street,
                    'r_fname': form.r_fname,
                    'r_lname': form.r_lname,
                    'r_street': form.r_street
                },
                success: function(response) {
                    if (response.status == 'OK') {
                        var data = response.data;
                    } else {; 
                        console.log(response);
                    }

                    var tracking_url = '<a href="./track?code=' + response.data.code + '">Check Parcel status</a>';
                    var simulation_url = '<a class="debug" href="./simulation?code=' + response.data.code + '">Tracking simulation</a>';
                    $('#tracking').html("Your traking code: " + response.data.code + " <br> " + tracking_url + "<br>" + simulation_url);
                    $('#form,#process-btn,#receiver-check,#sender-check').fadeOut("slow");
                    enableButtons();
                },
                error: function(request, status, error) {
                    console.log(request.responseText);
                    enableButtons();
                }
            });
        });
        /**
         * Tracking page
         *
         * Get statuses from DB and display
         *
         */
        $("#track-form-btn").click(function(e) {
            e.preventDefault();
            disableButtons();
            var code = $.trim($('#code').val());
            if(code == "") {
                $('#track-error').html("Tracking code can not be empty").fadeIn('slow'); // show the error message
                enableButtons();
                return false;
            }

            $.ajax({
                url: './api/v1/parcel/status/get',
                type: 'GET',
                data: {
                    'code': code,
                },
                success: function(response) {
                    if (response.status == 'OK') {
                        var data = response.data;
                        var items = "";
                        for (var i = 0; i < data.length; i++) {
                            items += "<li class='list-group-item'>" + data[i].created_at + " ---- " + data[i].desc + "</li>";
                        }
                        $("#info").html(items);
                        $('#track-error').html("");
                    } else {;
                        $('#track-error').html(response.data).fadeIn('slow');
                    }
                    enableButtons();
                },
                error: function(request, status, error) {
                    console.log(request.responseText);
                    enableButtons();
                }
            });
        });
        // function generateTrackingCode(sender, receiver, type) {
        //         var response = "";
        //         $.ajax({
        //             type: "GET",
        //             url: "./api/v1/parcel/tracking/compress",
        //             data: {'sender': sender, 'receiver': receiver,'type': type},
        //             contentType: "application/json; charset=utf-8",
        //             dataType: "json",
        //             success: function (result) {
        //               if (response.status == 'OK') {
        //                 response = result.data.code;
        //               } else {
        //                 console.log(response);
        //               }
        //             },
        //             error: function (jqXHR, textStatus, errorThrown) {
        //                 console.log(textStatus);
        //             }
        //         });

        //         return response;
        //     };
        /**
         * Tracking page
         *
         * Get all info from tracking code and display
         * 
         */
        $("#offline-btn").click(function(e) {
            e.preventDefault();
            disableButtons();
            var code = $('#code').val();
            $.ajax({
                url: './api/v1/parcel/tracking/reverse',
                type: 'GET',
                data: {
                    'code': code,
                },
                success: function(response) {
                    console.log(response);
                    enableButtons();
                },
                error: function(request, status, error) {
                    console.log(request.responseText);
                    enableButtons();
                }
            });
        });
        /**
         * Simulation page
         *
         * Show current statues
         * 
         */
        $("#tracking-btn").click(function(e) {
            e.preventDefault();
            disableButtons();
            var code = $.trim($('#code').val());

            if(code == "") {
                $('#track-error').html("Tracking code can not be empty").fadeIn('slow'); // show the error message
                enableButtons();
                return false;
            }

            $.ajax({
                url: './api/v1/parcel/status/get',
                type: 'GET',
                data: {
                    'code': code,
                },
                success: function(response) {
                    console.log(response);
                    if (response.data.length > 0) {
                        if (response.status == 'OK') {
                            var data = response.data;
                            var items = "";
                            for (var i = 0; i < data.length; i++) {
                                items += "<li class='list-group-item'>" + data[i].created_at + " ---- " + data[i].desc + "</li>";
                            }
                            $("#info").html(items);
                            $('#code').attr('data-id', data[0].id);
                            $("#tracking-btn").text('Refresh');
                            $("#status-div,#offline-btn").fadeIn('slow');
                            $('#track-error').html("");
                        }else{
                          $('#track-error').html(response.data).fadeIn('slow');
                        }
                       
                    } else {
                        console.log('This tracking code does not exist');
                    }
                    enableButtons();
                },
                error: function(request, status, error) {
                    console.log(request.responseText);
                    enableButtons();
                }
            });
        });
        /**
         * Simulation page
         *
         * If selected status works with location then
         * show location input field
         *
         */
        $('#status-inp').on('change', function() {
            var optionSelected = $(this).find("option:selected");
            var textSelected = optionSelected.text();
            if (textSelected.indexOf("#") !== -1) {
                var html = '<input class="form-control" id="location" name="location" type="text" Placeholder="Current location">'
                if (!$('#location').length) {
                    $('#status-fields').append(html);
                }
            } else {
                $('#location').remove();
            }
        })
        /**
         * Simulation page
         * 
         * Add status ajax call request
         * 
         */
        $("#status-add-btn").click(function(e) {
            e.preventDefault();
            disableButtons();
            var tracking_code = $('#code').val();
            var status_id = $('#status-inp').val();
            var location = $('#location').val();
            $("#status-div").show();

            $.ajax({
                url: './api/v1/parcel/status/add',
                type: 'POST',
                data: {
                    'code': tracking_code,
                    'status_id': status_id,
                    'location': location
                },
                success: function(response) {
                    console.log(response);
                    $("#tracking-btn").click();
                    enableButtons();
                },
                error: function(request, status, error) {
                    console.log(request.responseText);
                    enableButtons();
                }
            });
        });
        /**
         * Show or hide html elements for debuging
         * 
         */
        $(function() {
            $('#debug').change(function() {
                var status = $(this).prop('checked');
                if (status == false) {
                    $('#logger').hide();
                    $('.debug').hide();
                } else {
                    $('.debug').show();
                    $('#logger').show();
                }
            })
        })
        /****************************************************************************************************************
         * 
         *                                                                                                              
         *
         *      HELP FUNCTIOONS AND OTHERS
         *
         *      
         *
         ****************************************************************************************************************/

     function disableButtons() {  
          jQuery('input[type=input], input,.btn').attr('disabled', true);  
     }  
     function enableButtons() {  
          jQuery('input[type=input], input,.btn').attr('disabled', false);  
     }  

        /**
         * This function put all console.log output to
         * #logger div
         */

        $(function(){
          console.old = console.log;
          var logger = document.getElementById("logger");
           console.log = function() {
               var output = "",
                   arg, i;
               for (i = 0; i < arguments.length; i++) {
                   arg = arguments[i];
                   output += "<span class=\"log-" + (typeof arg) + "\">";
                   if (typeof arg === "object" && typeof JSON === "object" && typeof JSON.stringify === "function") {
                       output += JSON.stringify(arg);
                   } else {
                       output += arg;
                   }
                   output += "</span>&nbsp;";
               }
               logger.innerHTML += output + "<br>";
               console.old.apply(undefined, arguments);
           };

});

/**
 * Checks coordinate syntax and if its fits for our project
 * 
 */
function isValid(coord) {
    if(coord){
      if (typeof coord.lat === 'number' && coord.lat <= 90 && coord.lng > -10){
          if (typeof coord.lng === 'number' && coord.lat < 100 && coord.lng > -10){
              return true;
          }
      }     
    }
    return false;

};


/**
 * if sinput is: null,undefined,NaN,empty, (""),0,false, returns ""
 * 
 */
function print(str) {
  if (typeof str != 'undefined' && str) {
    return str;
  }else{
    return ""
  }
};





    } //init end
})(window, document, undefined);
/****************************************************************************************************************
 * 
 *                                                                                                              
 *
 *      DEPRECIATED TRACKING CODE ALGORITHMS (at the moment not using)
 *      
 *      Functionality was rewirted to Hermes.php class
 *      
 *
 ****************************************************************************************************************/
//Compression arrays
const arr = [],
    type = []
r_arr = [];
arr[10] = "a", arr[11] = "b", arr[12] = "c", arr[13] = "d", arr[14] = "e", arr[15] = "f", arr[16] = "g", arr[17] = "h", arr[18] = "i", arr[19] = "j", arr[20] = "k", arr[40] = "l", arr[41] = "m", arr[41] = "n", arr[43] = "o", arr[44] = "p", arr[45] = "q", arr[46] = "r", arr[47] = "t", arr[48] = "u", arr[49] = "x", arr[50] = "v", arr[51] = "z", arr[52] = "A", arr[53] = "B", arr[54] = "C", arr[55] = "D", arr[56] = "E", arr[57] = "F", arr[58] = "G", arr[59] = "H", arr[60] = "I", arr[61] = "J", arr[62] = "K", arr[63] = "L", arr[64] = "M", arr["-0"] = "N", arr[-1] = "O", arr[-2] = "P", arr[-3] = "Q", arr[-4] = "R", arr[-5] = "S", arr[-6] = "T", arr[-7] = "U", arr[-8] = "X", arr[-9] = "V", arr[-10] = "Z", r_arr.a = "10", r_arr.b = "11", r_arr.c = "12", r_arr.d = "13", r_arr.e = "14", r_arr.f = "15", r_arr.g = "16", r_arr.h = "17", r_arr.i = "18", r_arr.j = "19", r_arr.k = "20", r_arr.l = "40", r_arr.m = "41", r_arr.n = "41", r_arr.o = "43", r_arr.p = "44", r_arr.q = "45", r_arr.r = "46", r_arr.t = "47", r_arr.u = "48", r_arr.x = "49", r_arr.v = "50", r_arr.z = "51", r_arr.A = "52", r_arr.B = "53", r_arr.C = "54", r_arr.D = "55", r_arr.E = "56", r_arr.F = "57", r_arr.G = "58", r_arr.H = "59", r_arr.I = "60", r_arr.J = "61", r_arr.K = "62", r_arr.L = "63", r_arr.M = "64", r_arr.N = "-0", r_arr.O = "-1", r_arr.P = "-2", r_arr.Q = "-3", r_arr.R = "-4", r_arr.S = "-5", r_arr.T = "-6", r_arr.U = "-7", r_arr.X = "-8", r_arr.V = "-9", r_arr.Z = "-10";
type[1] = "Hermes Päckchen", type[2] = "S-Paket", type[3] = "M-Paket", type[4] = "L-Paket", type[5] = "XL-Paket", type[6] = "XXL-Paket", type[7] = "S-Paketabholung", type[8] = "M-Paketabholung", type[9] = "L-Paketabholung", type[10] = "XL-Paketabholung", type[11] = "XXL-Paketabholung", type[12] = "Reisegepäck", type[13] = "XSport- und Sondergepäck", type[14] = "Fahrrad", type[15] = "XS-Paket International", type[16] = "S-Paket International", type[17] = "M-Paket International", type[18] = "L-Paket International", type[19] = "XL-Paket International", type[20] = "XXL-Paket International";
/**
 * Genreates tracking code
 * @param {sender} LatLon object of sender coordinates
 * @param {receiver} LatLon object of receiver coordinates
 * @return {type} package type
 */
function generateTracking(sender, receiver, type) {
    result = '';
    result += type;
    result += compressCoord(sender.lat);
    //result += "s" //seperate coord
    result += compressCoord(sender.lng);
    // //result += "s" //seperate coord
    result += compressCoord(receiver.lat);
    // //result += "s" //seperate coord
    result += compressCoord(receiver.lng);
    //result += "s" //seperate coord
    var time = getModifiedTimestamp();
    result += compressNumber(time);
    return result;
}
/**
 * Convert tracking code to object
 * @param {code} tracking code
 * @return {obj} object with data : package type, sender/receiver 
 * coords and timestamp
 */
function trackingToObj(code) {
    if (code == undefined) throw 'Unexpected undefinded value';
    code = code.toString().trim(); // just in case
    var result = new Object();
    var temp = "";
    var after = "";
    var left;
    var rest;
    var i = 0;
    //first symbol is type
    result.type = type[reverseNumber(code.charAt(0))];
    code = code.slice(1);
    //get sender lat
    r = reverseCoordinates(code);
    var sender_lat = r.result;
    code = r.code;
    //get sender lot
    r = reverseCoordinates(code);
    var sender_lon = r.result;
    code = r.code;
    result.sender.lat = sender_lat
    result.sender.lng = sender_lon
    //get receiver lat
    r = reverseCoordinates(code);
    var receiver_lat = r.result;
    code = r.code;
    //get receiver lot
    r = reverseCoordinates(code);
    var receiver_lon = r.result;
    code = r.code;
    result.receiver.lat = receiver_lat
    result.receiver.lng = receiver_lon
    //rest is right side of coord
    var timestamp = "";
    for (var i = 0; i < code.length; i++) {
        timestamp += reverseNumber(code.charAt(i));
    }
    result.timestmap = reverseTimestamp(timestamp);
    //console.log(result);
    return result;
}
/**
 * Pretty print information from tracking code
 * @param {code} tracking code
 * @return {string} package type, sender/receiver 
 * coords and timestamp
 */
function trackingToString(code) {
    code = code.toString().trim(); // just in case
    var result = "";
    var after = "";
    var left;
    var rest;
    var i = 0;
    //first symbol is type
    result += "Type: " + code.charAt(0);
    code = code.slice(1);
    result += " Sender: ";
    //get sender lat
    r = reverseCoordinates(code);
    result += r.result + ",";
    code = r.code;
    //get sender lot
    r = reverseCoordinates(code);
    result += r.result;
    code = r.code;
    result += " Receiver: "
    //get receiver lat
    r = reverseCoordinates(code);
    result += r.result + ",";
    code = r.code;
    //get receiver lot
    r = reverseCoordinates(code);
    result += r.result;
    code = r.code;
    result += " Timestamp: "
    //rest is right side of coord
    for (var i = 0; i < code.length; i++) {
        result += reverseNumber(code.charAt(i));
    }
    //console.log(result);
    return result;
}
/**
 * decompress coordinates from tracking code
 * @param {code} tracking code or part of it
 * @return {obj} part of tracking code and 
 * reversed coordiantes
 */
function reverseCoordinates(code) {
    result = "";
    if (code.charAt(2) == "s") {
        result += code.slice(0, 2);
        code = code.slice(3) //skip s and take code
    } else {
        //console.log("checking else: " + coord.charAt(0));
        result += reverseNumber(code.charAt(0));
        code = code.slice(1) // take from second
    }
    result += ".";
    // while(code.length < 5){
    //   code += "0"
    //}
    after = "";
    i = 0;
    while (after.length < 6) {
        //console.log(code);
        //console.log("i="+i+" after="+after+" code.charAt(i)="+ code.charAt(i) + "got number="+  reverseNumber(code.charAt(i)) );
        after += reverseNumber(code.charAt(0));
        code = code.slice(1) //remove converted from code
        i++;
    }
    result += after;
    return r = {
        result: result,
        code: code
    };
}
/**
 * Converts tracking code letter to number
 * @param {value} letter or number
 * @return {char} if paramter was letter returns
 * corresponding number. If not  - same value
 */
function reverseNumber(value) {
    //console.log("match "+ value + " to letter? = "+ (value.match(/[a-z]/i) ));
    if (value.match(/[a-z]/i)) {
        //     console.log(value)
        //     console.log(r_arr[value])
        if (r_arr[value] != undefined) {
            //console.log( r_arr[value]);
            return r_arr[value];
        }
    }
    //console.log("return " +value);
    return value;
}
/**
 * Converts most frequent 2 digit number to letter
 * @param {value} 2 digit number
 * @return {char} compression letter or false if 
 * compression array value not found
 */
function getLetter(value) {
    if (arr[value] != undefined) {
        return arr[value];
    }
    return false;
}
/**
 * lossy compressopm for simple number in string
 * @param {str} int number
 * @return {str} compressed number
 */
function compressNumber(str) {
    var coded = "";
    str = str.toString();
    if (str.length == 1) {
        return str
    }
    for (var i = 0; i < str.length - 1; i = i + 2) {
        var val = getLetter(str.slice(i, i + 2));
        if (val == false) {
            val = str.slice(i, i + 2);
        }
        coded += val;
    }
    return coded;
}
/**
 * Converts single coordinate
 * @param {coord} lat or lon coordinate with in format
 * NN.NNNNN...
 * @return {string} compressed coordinate
 */
function compressCoord(coord) {
    //convert input to string in order to call string methods
    coord = coord.toString();
    var coded = ""; // result string
    var split = coord.split(".") // seperate left and right side of coord
    // Get letter or s (seperator) if no letter exist or not in [1-9]
    var left = getLetter(split[0]);
    if (left == false) {
        left = split[0];
        var leftInt = parseInt(left);
        if ((leftInt > 20 && leftInt < 40) || (leftInt > 64 && leftInt < 100)) {
            left += "s";
        }
    }
    //var leftInt = parseInt(left);
    //add seperator if out of leters
    //misssing [21;39] and [65;99]
    // if( ( leftInt > 20 && leftInt < 40 ) || ( leftInt > 64 && leftInt < 100 )){
    //    left += "s";
    //  }
    //append left side
    coded += left
    // if precsition is 0000.. add add only one 0, else check if letter exist and
    // compress for 6 precision digits in string
    if (split[1] == undefined || parseInt(split[1]) === 0) {
        //all zeors in floating
        coded += "0";
    } else {
        //console.log("cord="+coord+" coord lenggth= "+coord.length);
        //6 floating precision. Work only with 6 digits after dot
        if (split[1].length > 6) {
            split[1] = split[1].slice(0, 6);
        } else if (split[1].length < 6) {
            while (split[1].length < 6) {
                split[1] += "0"
            }
        }
        // console.log("22222cord="+coord+" coord lenggth= "+coord.length);
        var i = 0;
        while (i < 6) {
            res = getLetter(split[1].slice(i, i + 2));
            if (res != false) {
                //skip 2
                coded += res
                i++;
            } else {
                //skip 1
                coded += split[1].charAt(i);
            }
            i++;
        }
    }
    return coded;
}
/**
 * Compress timestamp. Substracts  2017-01-01 timestamp
 * from current timestamp in order to reduce digits
 
 * @return {number} Modified current timestamp
 */
function getModifiedTimestamp() {
    //Substract from start date in order to decreade number
    var unix_start = Math.round(new Date("2017-01-01") / 1000);
    var unix = Math.round(+new Date() / 1000);
    return (parseInt(unix) - parseInt(unix_start));
}

function reverseTimestamp(timestamp) {
    var unix_start = Math.round(new Date("2017-01-01") / 1000);
    console.log(timestamp)
    console.log(timestamp + unix_start);
    return new Date((parseInt(timestamp) + parseInt(unix_start)) * 1000)
}



/*! ========================================================================
 * Bootstrap Toggle: bootstrap-toggle.js v2.2.0
 * http://www.bootstraptoggle.com
 * ========================================================================
 * Copyright 2014 Min Hur, The New York Times Company
 * Licensed under MIT
 * ======================================================================== */
+function(a){"use strict";function b(b){return this.each(function(){var d=a(this),e=d.data("bs.toggle"),f="object"==typeof b&&b;e||d.data("bs.toggle",e=new c(this,f)),"string"==typeof b&&e[b]&&e[b]()})}var c=function(b,c){this.$element=a(b),this.options=a.extend({},this.defaults(),c),this.render()};c.VERSION="2.2.0",c.DEFAULTS={on:"On",off:"Off",onstyle:"primary",offstyle:"default",size:"normal",style:"",width:null,height:null},c.prototype.defaults=function(){return{on:this.$element.attr("data-on")||c.DEFAULTS.on,off:this.$element.attr("data-off")||c.DEFAULTS.off,onstyle:this.$element.attr("data-onstyle")||c.DEFAULTS.onstyle,offstyle:this.$element.attr("data-offstyle")||c.DEFAULTS.offstyle,size:this.$element.attr("data-size")||c.DEFAULTS.size,style:this.$element.attr("data-style")||c.DEFAULTS.style,width:this.$element.attr("data-width")||c.DEFAULTS.width,height:this.$element.attr("data-height")||c.DEFAULTS.height}},c.prototype.render=function(){this._onstyle="btn-"+this.options.onstyle,this._offstyle="btn-"+this.options.offstyle;var b="large"===this.options.size?"btn-lg":"small"===this.options.size?"btn-sm":"mini"===this.options.size?"btn-xs":"",c=a('<label class="btn">').html(this.options.on).addClass(this._onstyle+" "+b),d=a('<label class="btn">').html(this.options.off).addClass(this._offstyle+" "+b+" active"),e=a('<span class="toggle-handle btn btn-default">').addClass(b),f=a('<div class="toggle-group">').append(c,d,e),g=a('<div class="toggle btn" data-toggle="toggle">').addClass(this.$element.prop("checked")?this._onstyle:this._offstyle+" off").addClass(b).addClass(this.options.style);this.$element.wrap(g),a.extend(this,{$toggle:this.$element.parent(),$toggleOn:c,$toggleOff:d,$toggleGroup:f}),this.$toggle.append(f);var h=this.options.width||Math.max(c.outerWidth(),d.outerWidth())+e.outerWidth()/2,i=this.options.height||Math.max(c.outerHeight(),d.outerHeight());c.addClass("toggle-on"),d.addClass("toggle-off"),this.$toggle.css({width:h,height:i}),this.options.height&&(c.css("line-height",c.height()+"px"),d.css("line-height",d.height()+"px")),this.update(!0),this.trigger(!0)},c.prototype.toggle=function(){this.$element.prop("checked")?this.off():this.on()},c.prototype.on=function(a){return this.$element.prop("disabled")?!1:(this.$toggle.removeClass(this._offstyle+" off").addClass(this._onstyle),this.$element.prop("checked",!0),void(a||this.trigger()))},c.prototype.off=function(a){return this.$element.prop("disabled")?!1:(this.$toggle.removeClass(this._onstyle).addClass(this._offstyle+" off"),this.$element.prop("checked",!1),void(a||this.trigger()))},c.prototype.enable=function(){this.$toggle.removeAttr("disabled"),this.$element.prop("disabled",!1)},c.prototype.disable=function(){this.$toggle.attr("disabled","disabled"),this.$element.prop("disabled",!0)},c.prototype.update=function(a){this.$element.prop("disabled")?this.disable():this.enable(),this.$element.prop("checked")?this.on(a):this.off(a)},c.prototype.trigger=function(b){this.$element.off("change.bs.toggle"),b||this.$element.change(),this.$element.on("change.bs.toggle",a.proxy(function(){this.update()},this))},c.prototype.destroy=function(){this.$element.off("change.bs.toggle"),this.$toggleGroup.remove(),this.$element.removeData("bs.toggle"),this.$element.unwrap()};var d=a.fn.bootstrapToggle;a.fn.bootstrapToggle=b,a.fn.bootstrapToggle.Constructor=c,a.fn.toggle.noConflict=function(){return a.fn.bootstrapToggle=d,this},a(function(){a("input[type=checkbox][data-toggle^=toggle]").bootstrapToggle()}),a(document).on("click.bs.toggle","div[data-toggle^=toggle]",function(b){var c=a(this).find("input[type=checkbox]");c.bootstrapToggle("toggle"),b.preventDefault()})}(jQuery);
//# sourceMappingURL=bootstrap-toggle.min.js.map