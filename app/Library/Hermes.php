<?php
namespace App\Library;

define("SEPERATOR", "s");
define("START_TIMESTAMP", 1514764800);
define("DEBUG", false);


/**
 * Tracking code creation class
 *
 * @author   Mindaugas Milius <milius@mindau.de>
 *
 */


class Hermes
{   

    # number & letter pair coding table
    private static $table = array( 'a' => 10, 'b' => 11, 'c' => 12, 'd' => 13, 'e' => 14, 'f' => 15, 'g' => 16, 'h' => 17, 'i' => 18, 'j' => 19, 'k' => 20, 'l' => 40, 'm' => 41, 'n' => 41, 'o' => 43, 'p' => 44, 'q' => 45, 'r' => 46, 't' => 47, 'u' => 48, 'x' => 49, 'v' => 50, 'z' => 51, 'A' => 52, 'B' => 53, 'C' => 54, 'D' => 55, 'E' => 56, 'F' => 57, 'G' => 58, 'H' => 59, 'I' => 60, 'J' => 61, 'K' => 62, 'L' => 63, 'M' => 64, 'N' => -0, 'O' => -1, 'P' => -2, 'Q' => -3, 'R' => -4, 'S' => -5, 'T' => -6, 'U' => -7, 'X' => -8, 'V' => -9, 'Z' => -1, ); 
    private static $types=array( '1'=> "Hermes Päckchen", '2' => "S-Paket", '3' => "M-Paket", '4' => "L-Paket", '5' => "XL-Paket", '6' => "XXL-Paket", '7' => "S-Paketabholung", '8' => "M-Paketabholung", '9' => "L-Paketabholung", '10' => "XL-Paketabholung", '11' => "XXL-Paketabholung", '12' => "Reisegepäck", '13' => "XSport- und Sondergepäck", '14' => "Fahrrad", '15' => "XS-Paket International", '16' => "S-Paket International", '17' => "M-Paket International", '18' => "L-Paket International", '19' => "XL-Paket International", '20' => "XXL-Paket International", ); 


    public function __construct()  {  }


    /**
     * Callable class to compress tracking code
     *      
     * @param Coordinate $coordinate - sender
     * @param Coordinate $coordinate - receiver
     * @param mixed $type - Parcel type
     *
     * @return string compressed tracking code
     */
    public function compressTrackingCode(Coordinate $sender, Coordinate $receiver, $type): String
    {
        $result = "";

        $this->debug($type . '|' . $sender->getLat() . '|' . $sender->getLng() . '|' . $receiver->getLat() . '|' . $receiver->getLng() . '|' . $this->compressTimestamp());
        
        $result .= 
                $this->compressNumber($type) .
                $this->compressCoordinate($sender->getLat()) . 
                $this->compressCoordinate($sender->getLng()) .
                $this->compressCoordinate($receiver->getLat()) . 
                $this->compressCoordinate($receiver->getLng()) .
                $this->compressNumber($this->compressTimestamp());
                
        $this->debug($result);

        return $result;
    }

    /**
     * Compress single cordinate (lat OR lng)
     * Used in main compresion method - compressTrackingCode()
     *      
     * @param Float $coordinate - single coordinate
     *
     * @return string compressed single coordinate
     */

    private function compressCoordinate(Float $coordinate): string
    {
        $result = "";
        $split  = $this->seperateCoordinate($coordinate);

        $coord_int = array_search($split[0], SELF::$table);

        # take letter or number if letter not exist. put seperator if need
        if ($coord_int === false) {

            # no value in table
            $result .= (string) $split[0];
            if (($split[0] > 20 && $split[0] < 40) || ($split[0] > 64 && $split[0] < 100)) {
                $result .= SEPERATOR;
            }
        } else {
            $result .= (string) $coord_int;
        }
        $this->debug("Left side: $result <br>");

        # Decimal part

        if (!isset($split[1])) {
            # no floating point. RARE SITATION
            # add manualy 000000 for precission
            $result .= "000000";
            return $result;
        } else if (strlen((string) $split[1]) < 6) {
            //if precisison is less then 6 digits. Add zeros.
            $split[1] = str_pad($split[1], 6, "0");
        }
        $right = substr($split[1], 0, 6); //take only 6 decimals

        $result .= $this->compressNumber((string) $right);
        $this->debug("Right side: " . $right . " <br>");

        return $result;
    }


    /**
     * Compress simple number 
     * Used in main compresion method - compressTrackingCode
     *      
     * @param String $number - simple number
     *
     * @return string compressed simple number
     */

    private function compressNumber(String $number)
    {
        $result = '';
        $length = strlen($number);
        $this->debug("CompressNumber:  length:" . $length);

        for ($i = 0; $i < $length; $i++) {
            $two_digits = substr($number, $i, 2);
            $this->debug("TWO DIGITS:" . $two_digits);

            if ($two_digits == 0) {
                $result .= "0";
            } elseif ($two_digits == 00) {
                $result .= "00";
                $i++;

            } else {
                $coded = array_search($two_digits, SELF::$table);
                if ($coded != false) {
                    //found in table.
                    $result .= (string) $coded;
                    $i++;
                } else {
                    $result .= $number[$i];
                }
            }

            $this->debug("CODED:" . $result);
        }
        return $result;
    }

    /**
     * Compress simple number 
     * Used in main compresion method - compressTrackingCode
     *     
     *
     * @return string compressed parcel unix timestamp
     */

    private function compressTimestamp()
    {
        $now = time();
        return $now - START_TIMESTAMP;
    }

    /**
     * Main tracking code reverse (decompress) Method 
     *     
     * @param String $code - compressed tracking code
     *
     * @return array  sender array (0 - lat,1 lng)
     */


    public function reverseTrackingCode(String $code): array
    {

        $type = $this->reverseType($this->reverseChar($code[0]));
        $code = substr($code, 1);
        //get sender lat coordinate

        $temp = $this->reverseCoordinate($code);
        //dd($temp);
        $code  = $temp[0];
        $s_lat = $temp[1];

        $temp  = $this->reverseCoordinate($code);
        $code  = $temp[0];
        $s_lng = $temp[1];

        $temp  = $this->reverseCoordinate($code);
        $code  = $temp[0];
        $r_lat = $temp[1];

        $temp  = $this->reverseCoordinate($code);
        $code  = $temp[0];
        $r_lng = $temp[1];

        $timestamp = "";
        for ($i = 0; $i < strlen($code); $i++) {
            $timestamp = $this->reverseChar($code[$i]);
        }
        $timestamp = $this->reverseTimestamp($timestamp);

        $result = array('sender' => [$s_lat, $s_lng], 'receiver' => [$r_lat, $r_lng], 'type' => $type, 'date' => date('d-m-Y G:i:s', $timestamp));
        return $result;

    }
    private function reverseCoordinate(String $code): array
    {
        //LEFT SIDE
        //check if SEPERATOR exist
        $this->debug("Checking seperator $code[2]");
        if ($code[2] == SEPERATOR) {
            $left = substr($code, 0, 2); //take first to numbers for coordinate int
            $code = substr($code, 3);
        } else {
            $left = $this->reverseChar($code[0]);
            $code = substr($code, 1);
        }

        // RIGHT SIDFE
        $right = "";
        $i     = 0;
        while (strlen($right) < 6 && strlen($code) > 0) {
            $right .= $this->reverseChar($code[0]);
            $code = substr($code, 1); //remove one char from front
            $i++;
        }
        $result = $left . '.' . $right;
        return [$code, $result];

    }
    private function reverseChar(String $letter)
    {
        if (isset(SELF::$table[$letter])) {
            return SELF::$table[$letter];
        }
        return $letter;
    }

    private function reverseTimestamp($timestamp)
    {
        return $timestamp + START_TIMESTAMP;
    }

    private function seperateCoordinate($coordinate): array
    {
        return explode('.', $coordinate);

    }
    private function reverseType($type): String
    {
        return SELF::$types[$type];
    }
    private function debug($str)
    {
        if (DEBUG) {
            echo "<pre>$str</pre><br>";
        }

    }

}
