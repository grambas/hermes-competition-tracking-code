<?php

namespace App\Http\Controllers;

//use App\Http\Requests;
use Illuminate\Http\Request;
use App\Library\Hermes;
use App\Library\Coordinate;

class MainController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

/*******************************************************************************************************
 *
 *  VIEWS
 *
 *******************************************************************************************************/

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function homeView()
    {
        $types  = \App\Type::pluck('desc', 'id');
        return view('home', compact('types'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function trackView(Request $request)
    {
        $code = trim($request->get("code"));

        return view('track', compact('code'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function simulationView(Request $request)
    {
        $code     = trim($request->get("code"));
        $statuses = \App\Status::pluck('desc', 'id');
        return view('simulation', compact('statuses', 'code'));
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function demosntrationView()
    {
        return view('demosntration');
    }

/*******************************************************************************************************
 *
 *  API METHODS
 *
 *******************************************************************************************************/

    /**
     * Store a newly created Parcel in storage.
     *
     * @param  \App\Http\Requests\StoreParcelsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function createParcel(Request $request)
    {
        $data = $request->all();


        $hermes = new Hermes;
        $code = $hermes->compressTrackingCode(  new Coordinate($data['sender']['lat'],$data['sender']['lng']),
                                                  new Coordinate($data['receiver']['lat'],$data['receiver']['lng']),
                                                  $data['type']
                                                );
        $data['tracking'] = $code;

        $parcel = \App\Parcel::create($data);
        //insert status id 1 as default on creation
        $parcel->shipping()->sync(array_filter([1]));

        return response()->json(['status' => 'OK', 'data' => array('code' => $code,'message' => 'Parcel inserted successfuly' ) ]);
    }

    /**
     * Return all parcel statuses from Databse for given tracking code
     *
     * @param  \App\Http\Requests\StoreParcelsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function getParcelStatus(Request $request)
    {
        $code   = trim($request->get('code'));
        $parcel = \App\Parcel::where("tracking", $code)->first();

        if ($parcel) {
            $shipping = $parcel->shipping()->orderBy('pivot_created_at', 'DESC')->get();
            return response()->json(['status' => 'OK', 'data' => $this->formatStatuses($shipping)]);
        }

        return response()->json(['status' => 'ERROR','data' => 'Parcel with that tracking code not found']);
    }
    /**
     * Return all parcel statuses from Databse for given tracking code
     *
     * @param  \App\Http\Requests\StoreParcelsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function addParcelStatus(Request $request)
    {
        $code   = trim($request->get('code'));
        $parcel = \App\Parcel::where("tracking", $code)->first();

        if ($parcel) {
            $parcel->shipping()->attach($request->get('status_id'), array('location' => $request->get('location')));
            return response()->json(['status' => 'OK', 'data' => 'Status inserted']);
        }

        return response()->json(['status' => 'ERROR','data' => 'Parcel with that tracking code not found']);
    }
    /**
     * Return saved data from tracking code
     *
     * @param  \App\Http\Requests\StoreParcelsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function reverseTracking(Request $request)
    {
        $hermes = new Hermes;
        $code   = trim($request->get('code'));
        $result = $hermes->reverseTrackingCode($code);

        return response()->json(['status' => 'OK', 'data' => $result]);
    }

    /**
     * Compress data to tracking code
     *
     * @param  \App\Http\Requests\StoreParcelsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function compressTracking(Request $request)
    {
        $data = $request->all();


        $hermes = new Hermes;
        $result = $hermes->compressTrackingCode(  new Coordinate($data['sender']['lat'],$data['sender']['lng']),
                                                  new Coordinate($data['receiver']['lat'],$data['receiver']['lng']),
                                                  $data['type']
                                                );

        return response()->json(['status' => 'OK', 'data' => array('code' => $result )]);
    }

/*******************************************************************************************************
 *
 *  ADDITIONAL HELP METHODS
 *
 *******************************************************************************************************/

    /**
     * Formats status date and description
     * Date to readable human format
     * Description - if description supports place (ort)
     * then in DB string is saved in foramt .... [ ..bei, in, zur..#..] ...
     * # will be replaced with place (ort) and braces will be removed.
     * if place is null then everything beetween  [] will be stipped
     *
     *
     * @param  \App\Parcel  $shipping
     * @return Formatted Array
     */
    public function formatStatuses($shipping)
    {
        $formatted = [];
        // dd($shipping/);
        foreach ($shipping as $key => $value) {
            $formatted[$key]['created_at'] = $value->pivot->created_at->toDateTimeString();

            //pretty status print with "ort"
            if (isset($value->pivot->location) && $value->pivot->location != null && $value->located == 1) {
                $str = str_replace(['[', ']'], '', $value->desc);
                $str = str_replace('#', $value->pivot->location, $str);

            } else {
                $str = preg_replace("/\[(.*?)\]/", "", $value->desc);
            }
            $formatted[$key]['desc'] = $str;
        }
        // dd($formatted);
        return $formatted;
    }
}
