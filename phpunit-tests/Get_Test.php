<?php

use PHPUnit\Framework\TestCase;
class Get_Test extends TestCase
{
  private $debug = false;
  private $g;
  private $a;
  private $r;

  
  public function setup(){
	$this->a = new Arrangement();
	$this->g = new Gig();
	$this->r = new Render();

  }

  public function tearDown(){
  }
  

  public function test_getOutputLink(){
	$this->r->getOutputLink( $this->a->listAll(1) );
  }

  public function test_listAll(){
	 $this->a->listAll(1);
  }

  public function test_pdfFromGet(){
	$in = array();
	$this->r->getOutputLink( $this->a->pdfFromGet($in) );
	$in['arrangement'] = array(1);
	$this->r->getOutputLink( $this->a->pdfFromGet($in) );
  }

  public function test_pdfFromGig(){
	$in = array();
	$this->r->getOutputLink( $this->g->pdfFromGig($in) );
	$in['gigID'] = 1;
	$in['partID'] = 1;
	$this->r->getOutputLink( $this->g->pdfFromGig($in) );
  }



}

