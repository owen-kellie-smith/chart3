<?php

use PHPUnit\Framework\TestCase;
class Put_Test extends TestCase
{
  private $debug = false;
  private $a;
  private $g;
  private $u;
  
  public function setup(){
	$this->a = new Arrangement();
	$this->g = new Gig();
	$this->u = new User();
  }

  public function tearDown(){
  }
  
  public function test_StoreEmail(){
    $this->assertTrue( !$this->u->storeEmail() );
  }

  public function test_ReceiveFile(){
//   $file = array('error' => UPLOAD_ERR_OK, 'tmp_name' => 'afile.pdf', 'name' => 'afile.pdf');
   $file = array();
	$this->a->receiveFile( $file );
  }

  public function test_DeleteFile(){
	$this->a->deleteFile( 'noFile.abc');
  }


  public function test_postNewPerson(){
        $this->a->postNewPerson();
}

  public function test_storeNewUser(){
                $this->u->storeNewUser('dud@gmail.com','Dopey');
}

  public function test_postNewSong(){
        $this->a->postNewSong();
}


  public function test_setPublication(){
        $this->a->setPublication();
}

  public function test_deletePartPage(){
            $this->a->deletePartPage( 1 );
}

  public function test_setPartPage(){
            $this->a->setPartPage( 1, 1, 1, 1);
}

  public function test_addNote(){
            $this->a->addNote(1, 'Some text');
            $this->a->updateNote(1, 'Some text');
}

  public function test_deleteNote(){
            $this->a->deleteNote(1);
}

  public function test_toggle(){
            $this->a->addToBackup( 1, 1 );
            $this->a->addToBackup( 1, 0 );
            $this->a->addToPads( 1, 1 );
            $this->a->addToPads( 1, 0 );
}

  public function test_deleteSetListPart(){
            $this->g->deleteSetListPart(1);
}

  public function test_addToSet(){
            $this->g->addToSet(1, 10, 1);
}

  public function test_postNewSetList(){
	$_in = array('isGig'=>1,'gigName'=>'A good name', 'gigDate'=>'2018-09-01');
        $this->g->postNewSetList($_in);
}

  public function test_copySetList(){
            $this->g->copySetList( 1, 2);
}

  public function test_deleteSet(){
        $this->g->deleteSet();
	$_in = array('gigID'=>1);
        $this->g->deleteSet($_in);
}

  public function test_SetCookie(){
//	deleteCookie(); Can't test -- it sets cookies
    $this->assertTrue( !$this->u->setValidCookie( 'dudCode' ) );
  }


}

