<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    //

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'center', 'group', 'units' ];

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
