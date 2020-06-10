<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PointRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
         if($value<=0){
             return false;
         }
        if(!is_numeric($value)){
           return false ;
        }
       $decimal =  explode('.',$value);
       if(isset($decimal[1]) && !empty($decimal)){
           if(strlen($decimal[1])>2)
                return false;
       }
              return true;
     }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '不是数字或超过小数位限制.';
    }
}
