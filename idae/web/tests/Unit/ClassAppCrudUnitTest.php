<?php
require_once __DIR__ . '/../../appclasses/appcommon/ClassApp.php';

use PHPUnit\Framework\TestCase;

final class ClassAppCrudUnitTest extends TestCase
{
    public function testInsertCallsCollectionWithAssignedId(): void
    {
        $fakeColl = new class {
            public $lastDoc;
            public function insertOne($doc) {
                $this->lastDoc = $doc;
                return new class { public function getInsertedId() { return null; } };
            }
            public function deleteMany($vars) { return new class { public function getDeletedCount(){ return 0; } }; }
            public function updateOne($filter,$update,$options){ return new class{}; }
        };

        $stubDb = new class($fakeColl){
            private $coll;
            public function __construct($c){$this->coll=$c;}
            public function getCollection() { return $this->coll; }
            public function updateOne($filter,$update,$options){ return $this->coll->updateOne($filter,$update,$options); }
            public function deleteMany($vars){ return $this->coll->deleteMany($vars); }
            public function insertOne($doc){ return $this->coll->insertOne($doc); }
            public function find($filter){ return []; }
        };

        $app = new class extends App {
            public $plugReturn;
            public $app_table_one = ['codeAppscheme' => 'unit_table','codeAppscheme_base' => 'sitebase_app'];
            public $app_field_name_id = 'idunit_table';
            public function __construct(){}
            public function plug($base,$table) { return $this->plugReturn; }
            public function getNext($id,$min=1) { return 123; }
        };

        $app->plugReturn = $stubDb;
        $app->grilleFK = [];
        $app->table = 'unit_table';
        $id = $app->insert(['foo'=>'bar']);
        $this->assertEquals(123,$id);
        $this->assertEquals(123,$fakeColl->lastDoc['idunit_table']);
        $this->assertEquals('bar',$fakeColl->lastDoc['foo']);
    }

    public function testCreateUpdateReturnsFalseOnDriverException(): void
    {
        $fakeColl = new class {
            public function updateOne($vars,$update,$opts) { throw new \RuntimeException('boom'); }
        };
        $stubDb = new class($fakeColl){
            private $coll;
            public function __construct($c){$this->coll=$c;}
            public function getCollection(){ return $this->coll; }
            public function updateOne($vars,$update,$opts){ return $this->coll->updateOne($vars,$update,$opts); }
            public function find($filter){ return []; }
        };
        $app = new class extends App {
            public $plugReturn;
            public $app_table_one = ['codeAppscheme' => 'unit_table','codeAppscheme_base' => 'sitebase_app'];
            public $app_field_name_id = 'idunit_table';
            public function __construct(){}
            public function plug($base,$table) { return $this->plugReturn; }
            public function getNext($id,$min=1){ return 55; }
        };
        $app->plugReturn = $stubDb;
        $app->grilleFK = [];
        $app->table = 'unit_table';
        $res = $app->create_update(['idunit_table'=>55], ['name'=>'x']);
        $this->assertFalse($res);
    }

    public function testRemoveReturnsDeletedCount(): void
    {
        $fakeColl = new class {
            public function deleteMany($vars) { return new class { public function getDeletedCount(){ return 2; } }; }
        };
        $stubDb = new class($fakeColl){ private $coll; public function __construct($c){$this->coll=$c;} public function getCollection(){ return $this->coll;} public function find($filter){ return []; } };
        $app = new class extends App { public $plugReturn; public $app_table_one = ['codeAppscheme'=>'unit_table','codeAppscheme_base'=>'sitebase_app']; public $app_field_name_id='idunit_table'; public function __construct(){} public function plug($base,$table){ return $this->plugReturn;} };
        $app->plugReturn = $stubDb;
        $app->grilleFK = [];
        $app->table = 'unit_table';
        $count = $app->remove(['idunit_table' => 1]);
        $this->assertEquals(2,$count);
    }
}

