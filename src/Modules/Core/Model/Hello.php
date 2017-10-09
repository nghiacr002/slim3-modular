<?php 
namespace LegoAPI\Modules\Core\Model;

use \Lego\Modular\Model;

class Hello extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hello';
    protected $primaryKey = "hello_id";
    protected $expriredTime = 3000;
    protected $attributes = [];
}
