<?php

namespace Bundle\LichessBundle\Entities\Piece;
use Bundle\LichessBundle\Entities\Piece;

class Bishop extends Piece
{
    public function getClass()
    {
        return 'Bishop';
    }
}