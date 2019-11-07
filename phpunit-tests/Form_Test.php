<?php

use PHPUnit\Framework\TestCase;
class Form_Test extends TestCase
{
  private $debug = false;
  private $g;
  private $a;
  private $u;
  private $r;


  public function setup(){
	$this->a = new Arrangement();
	$this->g = new Gig();
	$this->r = new Render();
	$this->u = new User();

  }

  public function tearDown(){
  }
  
// forms with probably zero length in a blank database

  public function test_FormDeleteSetForm(){
    $this->assertTrue( strlen( $this->g->getDeleteSetForm() ) >= 0 );
  }

  public function test_FormCopySetForm(){
    $this->assertTrue( strlen( $this->g->getCopySetForm() ) >= 0 );
  }

  public function test_sendCode(){
     $this->u->sendCode("abc@def");
  }

  public function test_FormEfileForm(){
    $this->assertTrue( strlen( $this->a->getEfileForm() ) >= 0 );
  }

  public function test_FormEditNoteForm(){
    $this->assertTrue( strlen( $this->a->getEditNoteForm() ) >= 0 );
  }

  public function test_FormEditSetForm(){
    $this->assertTrue( strlen( $this->g->getEditSetForm() ) >= 0 );
  }

  public function test_FormGigSetForm(){
    $this->assertTrue( strlen( $this->g->getGigSetForm( 1 ) ) >= 0 );
  }

  public function test_FormNewNoteForm(){
    $this->assertTrue( strlen( $this->a->getNewNoteForm() ) >= 0 );
  }

  public function test_FormPartFormNOTRUN(){
    $this->assertTrue( strlen( $this->a->getPartForm(1) ) >= 0 ); /// DEPENDS on FPDF
  }

  public function test_FormPeople(){
    $this->assertTrue( strlen( $this->a->getPeople() ) >= 0 );
  }

  public function test_FormPublicationFormNOTRUN(){
    $this->assertTrue( strlen( $this->a->getPublicationForm( 'pdf' ) ) >= 0 );   /// DEPENDS ON FPDF
  }

  public function test_FormSetParts(){
    $this->assertTrue( strlen( $this->g->getSetPartsForm() ) >= 0 );
  }

  public function test_FormSetPartsOutput(){
    $this->assertTrue( strlen( $this->g->getSetPartsOutput( 1, 'dummy') ) >= 0 );
  }

  public function test_FormGetSongs(){
    $this->assertTrue( strlen( $this->a->getSongs() ) >= 0 );
  }

  public function test_FormList(){
$this->assertTrue( strlen(	$this->a->listPdf( 'pdf')) > 0);
  }

// forms with positive length (even in a blank database)

  public function test_Cookie(){
    $this->assertTrue( !$this->u->hasValidCookie() );
  }

  public function test_CookieAdmin(){
    $this->assertTrue( !$this->u->hasAdminCookie() );
  }

  public function test_List(){
    $this->assertTrue( strlen( $this->r->getOutputLink($this->a->listAll(-1))) > 10	);
  }  

  public function test_getRequestForm(){
    $this->assertTrue( strlen( $this->r->getRequestForm(1,1) ) > 10 );    // FPDF
  }

  public function test_indexFormLength(){
    $this->assertTrue( strlen( $this->u->getNewUserForm() ) > 10 );
    $this->assertTrue( strlen( $this->u->getEmailForm() ) > 10 );
    $this->assertTrue( strlen( $this->r->getFooter() ) > 10 );
    $this->assertTrue( strlen( $this->r->getRequestForm() ) > 10 );    // FPDF
    $this->assertTrue( strlen( $this->r->getOutputLink('dummy') ) > 10 );
    $this->assertTrue( strlen( $this->a->getNewPersonForm() ) > 10 );
    $this->assertTrue( strlen( $this->a->getNewSongForm() ) > 10 );
    $this->assertTrue( strlen( $this->a->getUploadFileForm() ) > 10 );
    $this->assertTrue( strlen( $this->a->getNewSongForm() ) > 10 );
  }  

}

