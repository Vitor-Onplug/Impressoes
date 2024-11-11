<?php
class Messages {
	final public function info($message = null){
		if(empty($message)){ return; }
		
		return '<div class="alert alert-info alert-dismissible">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<h5><i class="icon fas fa-info"></i> Atenção!</h5>
					' . $message . '
				</div>';
	}
	
	final public function success($message = null){
		if(empty($message)){ return; }
		
		return '<div class="alert alert-success alert-dismissible">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<h5><i class="icon fas fa-check"></i> Sucesso!</h5> 
					' . $message . '
				</div>';
	}
	
	final public function error($message = null){
		if(empty($message)){ return; }
		
		return '<div class="alert alert-warning alert-dismissible">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<h5><i class="icon fas fa-exclamation-triangle"></i> Atenção!</h5> 
					' . $message . '
				</div>';
	}
	
	final public function questionYesNo($message = null, $captionYes = null, $captionNo = null, $linkYes = null, $linkNo = null){
		if(empty($message) && empty($captionYes) && empty($captionNo) && empty($linkYes) && empty($linkNo)){ return; }
		
		return '<div class="alert alert-warning alert-dismissible">
					<h5><i class="icon fas fa-ban"></i> Confirmação</h5>
					' . $message . '
					<br /><br />
					<div class="row">
						<div class="col-md-2">
							<a href="' . $linkYes . '" class="btn btn-block btn-success">' . $captionYes . '</a>
						</div>
						<div class="col-md-2">
							<a href="' . $linkNo . '" class="btn btn-block btn-danger">' . $captionNo . '</a>
						</div>
					</div>
				</div>';
	}
}
?>