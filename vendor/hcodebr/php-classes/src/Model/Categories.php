<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class Categories extends Model {

  const SESSION = "User";
  const SECRET = "HcodePhp7_Secret";
	const SECRET_IV = "HcodePhp7_Secret_IV"; 

  public static function listAll() 
  { 
    $sql = new Sql();
    return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");    
  }

  public function save() 
  { 
    $sql = new Sql();
    $results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", array(
      ":idcategory"=>$this->getidcategory(),
			":descategory"=>$this->getdescategory()
    ));   
  }


  public function delete($idcategory)
  {
    $sql = new sql();
    $return = $sql->select("DELETE FROM tb_categories WHERE idcategory= :idcategory", array(
      ":idcategory"=>$idcategory
    ));
  }


  public function update($idcategory, $categ=array())
  {
    $sql = new sql();
    $return = $sql->select("UPDATE tb_categories SET descategory = :descategory WHERE idcategory = :idcategory;", array(
      ":idcategory"=>$idcategory,
      ":descategory"=>$categ['descategory'],
    ));
  }


  public function get($idcategory)
	{
		$sql = new Sql();
		$results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", array(
			":idcategory"=>$idcategory
		)); 
		$this->setData($results[0]);

	}

}

?>