<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Location extends Model
{
    /**
    * Modify the save function to add a default user value as Auth::id()
    */
    // public function save(array $options = array())
    // {
    //     if( ! $this->user)
    //     {
    //         $this->user = Auth::id();
    //     }
    //
    //     parent::save($options);
    // }
}
