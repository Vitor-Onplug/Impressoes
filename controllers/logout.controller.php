<?php
class LogoutController extends MainController {
    public function index(){
		if($this->logged_in) {
			$this->logout(false);
		}
		
		$this->title = SYS_NAME . ' - Saindo...';
		$this->goto_page(HOME_URI);
    }
}