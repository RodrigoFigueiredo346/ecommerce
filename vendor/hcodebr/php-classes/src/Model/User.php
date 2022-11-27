<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class User extends Model {

  const SESSION = "User";
  const SECRET = "HcodePhp7_Secret";
	const SECRET_IV = "HcodePhp7_Secret_IV";

  public static function login ($login, $password) 
  {
    
    $sql = new Sql();

    $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN ", array(
      ":LOGIN" =>$login
    ));

    if(count($results) === 0) {
      throw new \Exception("Login ou senha inválidos!");      
    }

    $data = $results[0];

    if (password_verify($password, $data["despassword"]) === true) {

      $user = new User();
      $user->setdata($data);
       
      $_SESSION[User::SESSION] = $user->getvalues();

      return $user;
      exit;

    } else {

      throw new \Exception("Login ou senha inválidos!");      
    }

  }

  public static function verifyLogin($inadmin = true) 
  {
    if (
      !isset($_SESSION[User::SESSION]) 
      || !$_SESSION[User::SESSION] 
      || !(int)$_SESSION[User::SESSION]["iduser"] > 0 
      || (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
    ) {
        header("Location: /admin/login");
        exit;
      }
  }

  public static function logout() 
  { 
    $_SESSION[User::SESSION] = NULL;      
  }

  public static function listall() 
  { 
    $sql = new Sql();  

    return $results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");
  }

  public function save() 
  {
    $sql = new Sql();
    $results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
      ":desperson"=>$this->getdesperson(),
      ":deslogin"=>$this->getdeslogin(),
      ":despassword"=>$this->getdespassword(),
      ":desemail"=>$this->getdesemail(),
      ":nrphone"=>$this->getnrphone(),
      ":inadmin"=>$this->getinadmin(),     
    ));

    $this->setdata($results[0]);

  }

  public function get($iduser)
	{
		$sql = new Sql();
		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
			":iduser"=>$iduser
		));

		// $data = $results[0];
		// $data['desperson'] = utf8_encode($data['desperson']);
		$this->setdata($results[0]);

	}

  public function update(){

    $sql = new Sql();
    $results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
      ":iduser"=>$this->getiduser(),
      ":desperson"=>$this->getdesperson(),
      ":deslogin"=>$this->getdeslogin(),
      ":despassword"=>$this->getdespassword(),
      ":desemail"=>$this->getdesemail(),
      ":nrphone"=>$this->getnrphone(),
      ":inadmin"=>$this->getinadmin(),     
    ));

    $this->setdata($results[0]);

  }


  public function delete($iduser){

    $sql = new Sql();
    $results = $sql->select("CALL sp_users_delete(:iduser)", array(
      ":iduser"=>$iduser,   
    ));   

  }


  public static function getForgot($email)
  {
    $sql = new Sql();
		$results = $sql->select("
			SELECT *
			FROM tb_persons a
			INNER JOIN tb_users b USING(idperson)
			WHERE a.desemail = :email;
		", array(
			":email"=>$email
		));

		if (count($results) === 0)
		{
			throw new \Exception("Não foi possível recuperar sua senha.");
		}
		else
		{
			$data = $results[0];
      $results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
				":iduser"=>$data['iduser'],
				":desip"=>$_SERVER['REMOTE_ADDR']
			));

			if (count($results2) === 0)
			{	throw new \Exception("Não foi possível recuperar a sua senha.");}
			else
			{	$dataRecovery = $results2[0];
        $code = openssl_encrypt($dataRecovery['idrecovery'], 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));
				$code = base64_encode($code);

        // if ($inadmin === true) {
				 	$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";
				// } else {
				// 	$link = "http://www.hcodecommerce.com.br/forgot/reset?code=$code";					
				// }				

				$mailer = new Mailer($data['desemail'], $data['desperson'], "Redefinir senha da kodeR Store", "forgot", array(
					"name"=>$data['desperson'],
					"link"=>$link
				));	

				$mailer->send();
				return $data;
      }

    }



  }



}

?>