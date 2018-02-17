<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Status
 *
 * @package App
 * @property text $desc
*/
class Status extends Model
{
    protected $fillable = ['desc'];
    public $timestamps = false;
    
    
    
}
