<?php 
if(!defined('ABSPATH')) exit; 

if(empty(chk_array($parametros, 1))){
	$idPessoa = chk_array($this->userdata, 'id');
}else{
	$hash = chk_array($parametros, 1);
	$idPessoa = decryptHash($hash);
}

if(empty($idPessoa)){
	echo '<meta http-equiv="refresh" content="0; url=' . HOME_URI . '/pessoas">';
	echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/pessoas";</script>';
	exit;
}

$modelo->getPessoa($idPessoa);

$documentos = $modeloDocumentos->getDocumentos($idPessoa);
$emails = $modeloEmails->getEmails($idPessoa);
$enderecos = $modeloEnderecos->getEnderecos($idPessoa);
$telefones = $modeloTelefones->getTelefones($idPessoa);
?>
		<div class="content-wrapper">
			<section class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1>Pessoa</h1>
						</div>
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>">Dashboard</a></li>
								<li class="breadcrumb-item"><a href="<?php echo HOME_URI; ?>/pessoas">Pessoas</a></li>
								<li class="breadcrumb-item active"><a href="<?php echo HOME_URI; ?>/pessoas/index/perfil/<?php echo chk_array($parametros, 1); ?>">Perfil</a></li>
							</ol>
						</div>
					</div>
				</div>
			</section>
			
			<?php require_once ABSPATH . '/views/pessoas/mini-perfil.inc.view.php'; ?>
			
			<div class="container-fluid">
				<div class="row">
					<?php if(count($documentos) > 0){ ?>
					<div class="col-md-6">
						<section class="content">
							<div class="card card-secondary">
								<div class="card-header">
									<h3 class="card-title">Documentos</h3>
								</div>
								<div class="card-body">
									<div class="row">
										<?php foreach($documentos AS $dadosDocumentos): ?>
										<div class="col-md-4">
											<div class="alert bg-secondary">
												<h4 class="text-center"><?php echo $dadosDocumentos['documento']; ?></h4>
												<p class="text-center">
													<?php echo $dadosDocumentos['detalhes']; ?>
													<br>
													<?php echo $dadosDocumentos['tipo']; ?>
													<br>
													<?php echo $dadosDocumentos['titulo']; ?>
											
													
												</p>
											</div>
										</div>
										<?php endforeach; ?>
									</div>
								</div>

								<div class="card-footer">
									<a href="<?php echo HOME_URI; ?>/pessoas/index/editar/<?php echo chk_array($parametros, 1); ?>/documentos" class="pull-right btn btn-sm btn-secondary">Editar</a>
								</div>
							</div>
						</section>
					</div>
					<?php } ?>
				
					<?php if(count($emails) > 0){ ?>
					<div class="col-md-6">
						<section class="content">
							<div class="card card-secondary">
								<div class="card-header">
									<h3 class="card-title">E-mails</h3>
								</div>
								<div class="card-body">
									<div class="row">
										<?php foreach($emails AS $dadosEmails): ?>
										<div class="col-md-6">
												<div class="alert bg-secondary">
													<h5 class="text-center"><?php echo $dadosEmails['email']; ?></h5>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								</div>

								<div class="card-footer">
									<a href="<?php echo HOME_URI; ?>/pessoas/index/editar/<?php echo chk_array($parametros, 1); ?>/emails" class="pull-right btn btn-sm btn-secondary">Editar</a>
								</div>
							</div>
						</section>
					</div>
					<?php } ?>

					<?php if(count($telefones) > 0){ ?>
					<div class="col-md-6">
						<section class="content">
							<div class="card card-secondary">
								<div class="card-header">
									<h3 class="card-title">Telefones</h3>
								</div>
								<div class="card-body">
									<div class="row">
										<?php foreach($telefones AS $dadosTelefones): ?>
										<div class="col-md-4">
											<div class="alert bg-secondary">
												<h4 class="text-center"><?php echo $dadosTelefones['telefone']; ?></h4>
												<p class="text-center">
													<?php echo $dadosTelefones['tipo']; ?>
												</p>
											</div>
										</div>
										<?php endforeach; ?>
									</div>
								</div>

								<div class="card-footer">
									<a href="<?php echo HOME_URI; ?>/pessoas/index/editar/<?php echo chk_array($parametros, 1); ?>/telefones" class="pull-right btn btn-sm btn-secondary">Editar</a>
								</div>
							</div>
						</section>
					</div>
					<?php } ?>
					
					<div class="col-md-6">
						<section class="content">
							<div class="card card-secondary">
								<div class="card-header">
									<h3 class="card-title">Arquivos</h3>
								</div>
								<div class="card-body">
									
								</div>

								<div class="card-footer">
									<a href="<?php echo HOME_URI; ?>/pessoas/index/editar/<?php echo chk_array($parametros, 1); ?>/arquivos" class="pull-right btn btn-sm btn-secondary">Gerenciar</a>
								</div>
							</div>
						</section>
					</div>
					
					<?php if(count($enderecos) > 0){ ?>
					<div class="col-md-12">
						<section class="content">
							<div class="card card-secondary">
								<div class="card-header">
									<h3 class="card-title">Endereços</h3>
								</div>
								<div class="card-body">
									<div class="row">
										<div class="col-md-6">
											<div class="row">
												<?php foreach($enderecos AS $dadosEnderecos): ?>
												<div class="col-md-6">
													<div class="alert bg-secondary">
														<h4 class="text-center"><?php echo $dadosEnderecos['titulo']; ?></h4>
														<h5 class="text-left">
															<?php echo $dadosEnderecos['cep']; ?>
															<br>
															<?php echo $dadosEnderecos['logradouro']; ?>, nº <?php echo $dadosEnderecos['numero']; ?>
															<br>
															<?php if(!empty($dadosEnderecos['complemento'])){  ?>
																<?php echo $dadosEnderecos['complemento']; ?>
																<br>
															<?php } ?>
															<?php if(!empty($dadosEnderecos['zona'])){  ?>
																<?php echo $dadosEnderecos['zona']; ?>
																<br>
															<?php } ?>
															<?php echo $dadosEnderecos['bairro']; ?> - <?php echo $dadosEnderecos['cidade']; ?>/<?php echo $dadosEnderecos['estado']; ?>
														</h5>
													</div>
												</div>
												<?php endforeach; ?>
											</div>
										</div>
										<div class="col-md-6">
											<script src="//maps.googleapis.com/maps/api/js?key=AIzaSyDqITRk0Rt9D7RsFR3spL9r_HiEupKEcY4&amp;"></script>
											<script>
												var map;
												var infoWindow;

												var markersData = [
													<?php
													foreach($enderecos AS $dadosEnderecos):
														echo"
														{
															lat: " . $dadosEnderecos['latitude'] . ",
															lng: " . $dadosEnderecos['longitude'] . ",
															titulo: '" . $dadosEnderecos['titulo'] . "'
														}";
														if(count($enderecos) > 1){
															echo",";
														}
													endforeach;
													?>
												];
												
												function createMarker(latlng, titulo){
												   var marker = new google.maps.Marker({
													  map: map,
													  position: latlng,
													  title: titulo
												   });

												   google.maps.event.addListener(marker, 'click', function() {
														var iwContent = '<div>' + titulo + '</div>';
														infoWindow.setContent(iwContent);
														infoWindow.open(map, marker);
												   });
												   
												   
												   map.setZoom(15);
												}

												function displayMarkers(){
													var bounds = new google.maps.LatLngBounds();
													for (var i = 0; i < markersData.length; i++){
														var latlng = new google.maps.LatLng(markersData[i].lat, markersData[i].lng);
														var titulo = markersData[i].titulo;

														createMarker(latlng, titulo);
														bounds.extend(latlng);  
													}
													
													map.fitBounds(bounds);
												}
												
												function initialize() {
												   var mapOptions = {
													  zoom: 15,
													  center: new google.maps.LatLng(-20.540775, -48.5494308),

													  mapTypeId: 'roadmap',
												   };

												   map = new google.maps.Map(document.getElementById('mapa'), mapOptions);

												   infoWindow = new google.maps.InfoWindow();

												   google.maps.event.addListener(map, 'click', function() {
													  infoWindow.close();
												   });

												   displayMarkers();
												}
												
												google.maps.event.addDomListener(window, 'load', initialize);
											</script>
											
											<div id="mapa" style="width: 100%; height: 300px;"></div>
										</div>
									</div>
								</div>

								<div class="card-footer">
									<a href="<?php echo HOME_URI; ?>/pessoas/index/editar/<?php echo chk_array($parametros, 1); ?>/enderecos/" class="pull-right btn btn-sm btn-secondary">Editar</a>
								</div>
							</div>
						</section>
					</div>
					<?php } ?>
					
					<div class="col-md-6">
						<section class="content">
							<div class="card card-secondary">
								<div class="card-header">
									<h3 class="card-title">Ferramentas</h3>
								</div>

								<div class="card-footer">
									<a href="<?php echo HOME_URI; ?>/pessoas/index/editar/<?php echo chk_array($parametros, 1); ?>" class="btn btn-secondary">Editar</a>
								
									<?php if(chk_array($this->userdata, 'id') == chk_array($parametros, 1)){?>
									<a href="<?php echo HOME_URI; ?>/pessoas/index/trocar-minha-senha" class="btn btn-secondary">Trocar Minha Senha</a>
									<?php } ?>
								</div>
							</div>
						</section>
					</div>

				</div>
			</div>
		</div>