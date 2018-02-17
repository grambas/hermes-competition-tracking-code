<?php

namespace App\Library;
/**
 * Coordinate Implementation for EUROPE ONLY usage
 * It accept only latitude in -9.0 .. +90.0 and 
 * Longitude in -9.9 .. +99.9 , due to compression
 *
 * @author   Midnuagas Milius <milius@mindau.de>
 */

class Coordinate
{
    /**
     * @var float
     */
    protected $lat;
    /**
     * @var float
     */
    protected $lng;
    
    public function __construct(float $lat, float $lng)
    {
        if (! $this->isValidLatitude($lat)) {
            throw new \InvalidArgumentException("Latitude value must be numeric -9.9 .. +99.9 (given: {$lat})");
        }
        if (! $this->isValidLongitude($lng)) {
            throw new \InvalidArgumentException("Longitude value must be numeric -9.9 .. +99.9 (given: {$lng})");
        }
        $this->lat = (float)$lat;
        $this->lng = (float)$lng;
    }
    /**
     * @return float
     */
    public function getLat(): float
    {
        return $this->lat;
    }
    /**
     * @return float
     */
    public function getLng(): float
    {
        return $this->lng;
    }

    /**
     * Validates latitude
     *
     * @param mixed $latitude
     *
     * @return bool
     */
    protected function isValidLatitude(float $latitude): bool
    {
        if (! is_numeric($latitude)) {
            return false;
        }
        if ($latitude < -10 || $latitude > 90.0) {
            return false;
        }
        return true;
    }
    /**
     * Validates longitude
     *
     * @param mixed $longitude
     *
     * @return bool
     */
    protected function isValidLongitude(float $longitude): bool
    {
        if (! is_numeric($longitude)) {
            return false;
        }
        if ($longitude < -10 || $longitude > 99.99) {
            return false;
        }
        return true;
    }
   

    
}