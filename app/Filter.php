<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Filter extends Model
{
    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'user', 'zone', 'region', 'district', 'center', 'from', 'to' ];

    /**
    * Modify the save function to add a default user value as Auth::id()
    */
    public function save(array $options = array())
    {
        if( ! $this->user)
        {
            $this->user = Auth::id();
        }

        parent::save($options);
    }

}
