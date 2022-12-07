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
    Categories::updateFile();  
  }


  public function delete($idcategory)
  {
    $sql = new sql();
    $return = $sql->select("DELETE FROM tb_categories WHERE idcategory= :idcategory", array(
      ":idcategory"=>$idcategory
    ));
    Categories::updateFile();
  }


  public function update($idcategory, $categ=array())
  {
    $sql = new sql();
    $return = $sql->select("UPDATE tb_categories SET descategory = :descategory WHERE idcategory = :idcategory;", array(
      ":idcategory"=>$idcategory,
      ":descategory"=>$categ['descategory'],
    ));

    Categories::updateFile();
  }


  public function get($idcategory)
	{
		$sql = new Sql();
		$results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", array(
			":idcategory"=>$idcategory
		)); 
		$this->setData($results[0]);

	}

  public static function updateFile(){
    $category = Categories::listAll();
    $html = [];

    foreach ($category as $row) {
      array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');    
    }

    file_put_contents($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."categories-menu.html", implode('',$html));
  }

}

?>