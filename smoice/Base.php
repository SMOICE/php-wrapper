<?php

namespace smoice;

class Base
{
  public $id;

  public function __construct ( $mapObject  = null )
  {
    if ( $mapObject && is_object($mapObject) )
      foreach ( get_object_vars($mapObject) as $var => $value )
        $this->$var = $value;
  }

}

?>
