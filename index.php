<?php

session_start();

require_once("vendor/autoload.php");

use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Categories;

$app = new \Slim\Slim();

$app->config('debug', true);

$app->get('/', function() {    
	$page = new Page();
	$page->setTpl("index");
});


$app->get('/admin', function() {
	User::verifyLogin();    
	$page = new PageAdmin();
	$page->setTpl("index");
});


$app->get('/admin/login', function() {    
	$page = new PageAdmin([
		"header"=> false,
		"footer"=> false
	]);
	$page->setTpl("login");
});


$app->post('/admin/login', function() {    
	User::login($_POST["login"], $_POST["password"]);
	header("Location: /admin");	exit;
});


$app->get('/admin/logout', function() {    
	User::logout();
	header("Location: /admin/login");	exit;
});


$app->get("/admin/users", function() {
	User::verifyLogin();
	$users = User::listall();
	$page = new PageAdmin();
	$page->setTpl("users", array(
		"users"=> $users
	));
});


$app->get('/admin/users/create', function(){
	User::verifyLogin();
	$page = new PageAdmin();
	$page->setTpl("users-create");
});


$app->get('/admin/users/:iduser/delete', function($iduser){
	User::verifyLogin();
	$user = new User();
	// $user->get((int)$iduser); 
	$user->delete($iduser);
	header("Location: /admin/users");	exit;

});


$app->get('/admin/users/:iduser', function($iduser){ 
	User::verifyLogin();
	$user = new User();
	$user->get((int)$iduser);
	$page = new PageAdmin();
	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));
});


$app->post('/admin/users/create', function(){
	User::verifyLogin();
	$user = new User();
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;
	$user->setData($_POST);
	$user->save();
	header("Location: /admin/users");	exit;
});


//update
$app->post('/admin/users/:iduser', function($iduser){
	User::verifyLogin();
	$user = new User();
	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;
	$user->get((int)$iduser);
	$user->setData($_POST);
	$user->update();
	header("Location: /admin/users");	exit;
});


$app->get('/admin/forgot', function(){
	$page = new PageAdmin([
		"header"=> false,
		"footer"=> false
	]);
	$page->setTpl("forgot");
});


$app->post('/admin/forgot', function(){
	$user = User::getForgot($_POST['email']);
	header("Location: /admin/forgot/sent");	exit;
});


$app->get('/admin/forgot/sent', function(){
	$page = new PageAdmin([
		"header"=> false,
		"footer"=> false
	]);
	$page->setTpl("forgot-sent");
});


$app->get('/admin/categories', function(){
	$categories = Categories::listAll();
	$page = new PageAdmin();
	$page->setTpl("categories", array( //poderia passar também sem a palavra array, mas apenas abrindo e fechando []
		"categories"=>$categories
	));
});


$app->get('/admin/categories/create', function(){
	// $category = Categories::save();
	$page = new PageAdmin();
	$page->setTpl("categories-create");	
});


$app->post('/admin/categories/create', function(){

	$category = new Categories();
	$category->setdata($_POST);
	$category->save();
	header("Location: /admin/categories");	exit;	
});


$app->get('/admin/categories/:idcategory/delete', function($idcategory){
	User::verifyLogin();
	$category = new Categories();
	$category->delete($idcategory);
	header("Location: /admin/categories");	exit;
});


$app->get("/admin/categories/:idcategory", function($idcategory){
	User::verifyLogin();
	$category = new Categories();
	$category->get((int)$idcategory);
	$var = $category->getvalues();
	$page = new PageAdmin();
	$page->setTpl('categories-update',[
		"category"=>$category->getvalues()
	]);
});


$app->post("/admin/categories/:idcategory", function($idcategory){
	$category = new Categories();
	$category->update($idcategory, $_POST);
	header("Location: /admin/categories");	exit;
});


// $app->post('/carrinho', function(){
// 	$page = new Page();
// 	$page->setTpl("carrinho");
// });

$app->run();

 ?>