<?php
namespace app\console\controller;

class Sms extends ConsoleBase{
    public function index(){
        vendor("Passport.SingleLogin");
        $login = new \SingleLogin();
		header('Location: '.$login->buildLoginURI('jrfacai'));	
    }
}