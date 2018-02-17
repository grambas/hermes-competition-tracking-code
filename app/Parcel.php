<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Parcel
 *
 * @package App
 * @property string $tracking
 * @property string $s_fname
 * @property string $s_lname
 * @property string $s_street
 * @property string $r_fname
 * @property string $r_lname
 * @property string $r_street
 */
class Parcel extends Model
{

    protected $fillable = ['tracking', 's_fname', 's_lname', 's_street', 'r_fname', 'r_lname', 'r_street'];

    public function shipping()
    {
        return $this->belongsToMany(Status::class, 'parcel_status')->withPivot('location', 'created_at');
    }





}
