<?php

namespace smoice;

class BaseWithoutId
{
  public function __construct ( $mapObjectOrArray  = null )
  {
    if ( !$mapObjectOrArray )
      return;

    if ( is_object($mapObjectOrArray) )
      foreach ( get_object_vars($mapObjectOrArray) as $var => $value )
        $this->$var = $value;

    if ( is_array($mapObjectOrArray) )
      foreach ( $mapObjectOrArray as $var => $value )
        $this->$var = $value;
  }

}

?>
