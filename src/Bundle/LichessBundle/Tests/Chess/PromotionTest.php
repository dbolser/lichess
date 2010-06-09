<?php

namespace Bundle\LichessBundle\Tests\Chess;

use Bundle\LichessBundle\Chess\Generator;
use Bundle\LichessBundle\Chess\Manipulator;
use Bundle\LichessBundle\Chess\PieceFilter;

require_once __DIR__.'/../gameBootstrap.php';
require_once __DIR__.'/../../Chess/Manipulator.php';

class PromotionTest extends \PHPUnit_Framework_TestCase
{
    protected $game;

    public function testPromotionQueen()
    {
        $data = <<<EOF
       k
 P      
        
        
        
        
        
K       
EOF;
        $game = $this->game = $this->createGame($data);
        $this->move('b7 b8', array('promotion' => 'Queen'));
        $this->assertTrue($this->game->getBoard()->getPieceByKey('b8')->isClass('Queen'));
        $this->assertTrue($this->game->getBoard()->getPieceByKey('h8')->isAttacked());
        $this->assertEquals(0, count(PieceFilter::filterClass($this->game->getPlayer('white')->getPieces(), 'Pawn')));
        $this->assertEquals(1, count(PieceFilter::filterClass($this->game->getPlayer('white')->getPieces(), 'Queen')));
    }

    public function testPromotionKnight()
    {
        $data = <<<EOF
        
 P k    
        
        
        
        
        
K       
EOF;
        $game = $this->game = $this->createGame($data);
        $this->move('b7 b8', array('promotion' => 'Knight'));
        $this->assertTrue($this->game->getBoard()->getPieceByKey('b8')->isClass('Knight'));
        $this->assertTrue($this->game->getBoard()->getPieceByKey('d7')->isAttacked());
        $this->assertEquals(0, count(PieceFilter::filterClass($this->game->getPlayer('white')->getPieces(), 'Pawn')));
        $this->assertEquals(1, count(PieceFilter::filterClass($this->game->getPlayer('white')->getPieces(), 'Knight')));
    }

    public function testNoPromotion()
    {
        $data = <<<EOF
       k
 P      
        
        
        
        
        
K       
EOF;
        $game = $this->game = $this->createGame($data);
        $this->move('a1 a2');
        $this->assertNull($this->game->getBoard()->getPieceByKey('b8'));
        $this->assertFalse($this->game->getBoard()->getPieceByKey('h8')->isAttacked());
        $this->assertEquals(1, count(PieceFilter::filterClass($this->game->getPlayer('white')->getPieces(), 'Pawn')));
        $this->assertEquals(0, count(PieceFilter::filterClass($this->game->getPlayer('white')->getPieces(), 'Knight')));
    }

    /**
     * Verify the game state
     *
     * @return void
     **/
    protected function assertDump($dump)
    {
        $dump = "\n".$dump."\n";
        $this->assertEquals($dump, $this->game->getBoard()->dump());
    }

    /**
     * apply moves
     **/
    protected function applyMoves(array $moves)
    {
        foreach ($moves as $move)
        {
            $this->move($move);
        }
    }

    /**
     * Moves a piece and increment game turns
     *
     * @return void
     **/
    protected function move($move, array $options = array())
    {
        $manipulator = new Manipulator($this->game->getBoard());
        $manipulator->play($move, $options);
    }

    /**
     * Get a game from visual data block
     *
     * @return Game
     **/
    protected function createGame($data = null)
    {
        $generator = new Generator();
        if ($data) {
            $game = $generator->createGameFromVisualBlock($data);
        }
        else {
            $game = $generator->createGame();
        }
        $game->setIsStarted(true);
        $game->setTurns(30);
        return $game; 
    }
}