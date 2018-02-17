<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \App\Library\Hermes;
use \App\Library\Coordinate;


class HermesTest extends TestCase
{
    /**
     * @var Hermes
     */
    protected $hermes;
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->hermes = new Hermes;
    }
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unset($this->hermes);
    }


    public function testCompresssion()
    {
    	// TEST COORDINATES 1
    	$sender = new Coordinate(5.56654654, 13.59895);
    	$receiver = new Coordinate(40.0005, -5.5879);
    	$compressed =   $this->hermes->compressTrackingCode($sender,$receiver,1);
        $reversed =   $this->hermes->reverseTrackingCode($compressed);

    	$this->assertEquals( $sender->getLat() , $reversed['sender'][0],"Sender lan coordiantes are same after convertation",0.000001);
    	$this->assertEquals( $sender->getLng() , $reversed['sender'][1],"Sender lng coordiantes are same after convertation",0.000001);
    	$this->assertEquals( $receiver->getLat() , $reversed['receiver'][0],"receiver lan coordiantes are same after convertation",0.000001);
    	$this->assertEquals( $receiver->getLng() , $reversed['receiver'][1],"receiver lng coordiantes are same after convertation",0.000001);	


    	// TEST COORDINATES 2
    	$sender = new Coordinate(-9.0199, 45.189416351);
    	$receiver = new Coordinate(40.0005, -5.5879);
    	$compressed =   $this->hermes->compressTrackingCode($sender,$receiver,1);
        $reversed =   $this->hermes->reverseTrackingCode($compressed);

    	$this->assertEquals( $sender->getLat() , $reversed['sender'][0],"Sender lan coordiantes are same after convertation",0.000001);
    	$this->assertEquals( $sender->getLng() , $reversed['sender'][1],"Sender lng coordiantes are same after convertation",0.000001);
    	$this->assertEquals( $receiver->getLat() , $reversed['receiver'][0],"receiver lan coordiantes are same after convertation",0.000001);
    	$this->assertEquals( $receiver->getLng() , $reversed['receiver'][1],"receiver lng coordiantes are same after convertation",0.000001);	


    	// TEST COORDINATES 3
    	$sender = new Coordinate(54.394661, 24.012658399999964);
    	$receiver = new Coordinate(52.379981, 9.803140);
    	$compressed =   $this->hermes->compressTrackingCode($sender,$receiver,1);
        $reversed =   $this->hermes->reverseTrackingCode($compressed);

    	$this->assertEquals( $sender->getLat() , $reversed['sender'][0],"Sender lan coordiantes are same after convertation",0.000001);
    	$this->assertEquals( $sender->getLng() , $reversed['sender'][1],"Sender lng coordiantes are same after convertation",0.000001);
    	$this->assertEquals( $receiver->getLat() , $reversed['receiver'][0],"receiver lan coordiantes are same after convertation",0.000001);
    	$this->assertEquals( $receiver->getLng() , $reversed['receiver'][1],"receiver lng coordiantes are same after convertation",0.000001);	


    	    	// TEST COORDINATES 4
    	$sender = new Coordinate(68.406834, 18.675939);
    	$receiver = new Coordinate(12.437210083007812, 52.48921952322189);
    	$compressed =   $this->hermes->compressTrackingCode($sender,$receiver,1);
        $reversed =   $this->hermes->reverseTrackingCode($compressed);

    	$this->assertEquals( $sender->getLat() , $reversed['sender'][0],"Sender lan coordiantes are same after convertation",0.000001);
    	$this->assertEquals( $sender->getLng() , $reversed['sender'][1],"Sender lng coordiantes are same after convertation",0.000001);
    	$this->assertEquals( $receiver->getLat() , $reversed['receiver'][0],"receiver lan coordiantes are same after convertation",0.000001);
    	$this->assertEquals( $receiver->getLng() , $reversed['receiver'][1],"receiver lng coordiantes are same after convertation",0.000001);	



    }





}
