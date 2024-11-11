				<form role="form" action="" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="diretorioMidia" value="<?php echo chk_array($modelo->form_data, 'midia'); ?>">
					<div class="row">
						<div class="col-md-12">
							<div class="box box-primary">
								<div class="box-header with-border">
									<i class="fa fa-id-card-o"></i>
									<h3 class="box-title">Identificação</h3>
								</div>

								<div class="box-body">
									<?php 
									echo $modelo->form_msg;
									?>								
								
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="evento">Nome do Evento</label>
												<input type="text" class="form-control" id="evento" name="evento" placeholder="" value="<?php echo htmlentities(chk_array($modelo->form_data, 'evento')); ?>">
											</div>
										</div>
										
										<div class="col-md-3">
											<div class="form-group">
												<label for="idCategoria">Categoria do Evento</label>
												<select class="form-control select2" name="idCategoria" id="idCategoria">
													<option value="">Escolha uma opção</option>
													<?php
													foreach($categorias AS $dadosCategorias):
														echo"<option value='" . $dadosCategorias['id'] . "' ";
														if(htmlentities(chk_array($modelo->form_data, 'idCategoria')) == $dadosCategorias['id']){ echo'selected'; }
														echo">" . $dadosCategorias['categoria'] . "</option>";
													endforeach;
													?>
												</select>
											</div>
										</div>
										
										<div class="col-md-3">
											<div class="form-group">
												<label for="cliente">Cliente</label>
												<select class="form-control select2" name="cliente" id="cliente">
													<option value="">Escolha</option>
													<?php
													foreach($clientes AS $dadosClientes):
														echo"<option value='" . $dadosClientes['idPessoa'] . "' ";
														if(htmlentities(chk_array($modelo->form_data, 'cliente')) == $dadosClientes['idPessoa']){ echo'selected'; }
														echo">" . $dadosClientes['nomeFantasia'] . "</option>";
													endforeach;
													?>
												</select>
											</div>
										</div>
										
									</div>

									<div class="row">
										<div class="col-md-4">
											<label for="dataInicioFim">Data início/fim:</label>
											<div class="input-group">
												<div class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</div>
												<input type="text" class="form-control pull-right dateRangePicker" id="dataInicioFim" name="dataInicioFim" value="<?php echo htmlentities(chk_array($modelo->form_data, 'dataInicioFim')); ?>">
											</div>
										</div>

										<div class="col-md-2">
											<div class="form-group">
												<label for="valorContrato">Valor do Contrato</label>
												<input type="text" class="form-control money" id="valorContrato" name="valorContrato" placeholder="" value="<?php echo htmlentities(chk_array($modelo->form_data, 'valorContrato')); ?>">
											</div>
										</div>
										
										<div class="col-md-2">
											<div class="form-group">
												<label for="imposto">Imposto (%)</label>
												<input type="number" step="0.01" min="0" max="100" class="form-control" id="imposto" name="imposto" placeholder="" value="<?php echo htmlentities(chk_array($modelo->form_data, 'imposto')); ?>">
											</div>
										</div>
										
										<div class="col-md-2">
											<div class="form-group">
												<label for="escritorio">Verba Escritório</label>
												<input type="text" class="form-control money" id="escritorio" name="escritorio" placeholder="" value="<?php echo htmlentities(chk_array($modelo->form_data, 'escritorio')); ?>">
											</div>
										</div>
										
										<div class="col-md-2">
											<div class="form-group">
												<label for="verba">Verba evento</label>
												<input type="text" class="form-control money" id="verba" name="verba" placeholder="" value="<?php echo htmlentities(chk_array($modelo->form_data, 'verba')); ?>">
											</div>
										</div>

									</div>
									
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label for="avatar">Logo/Arte</label>
												<div id="avatar" orakuploader="on"></div>
												<p class="help-block">Adicionar logotipo ou arte do evento</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="box box-primary">
								<div class="box-header with-border">
								<i class="fa fa-globe"></i>
									<h3 class="box-title">Localização</h3>
								</div>
							  
								<div class="box-body">
									
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label for="local">Local</label>
												<select class="form-control select2" name="local" id="local">
													<option value="">Escolha</option>
													<?php
													foreach($locais AS $dadosLocais):
														echo"<option value='" . $dadosLocais['id'] . "' ";
														if(htmlentities(chk_array($modelo->form_data, 'local')) == $dadosLocais['id']){ echo'selected'; }
														echo">" . $dadosLocais['titulo'] . "</option>";
													endforeach;
													?>
												</select>
											</div>
										</div>
										
										<div class="col-md-8">
											<div class="form-group">
												<label>Nome Oficial</label>
												<input type="text" class="form-control" id="titulo" readonly="readonly" value="<?php echo chk_array($modelo->form_data, 'titulo'); ?>">
											</div>
										</div>
									</div>
									
									<div class="row">
										<div class="col-md-3">
											<label>CEP</label>
											<div class="input-group">
												<input type="text" class="form-control" id="cep" readonly="readonly" value="<?php echo chk_array($modelo->form_data, 'cep'); ?>">
											</div>
										</div>
										
										<div class="col-md-6">
											<div class="form-group">
												<label>Logradouro</label>
												<input type="text" class="form-control" id="logradouro" readonly="readonly" value="<?php echo chk_array($modelo->form_data, 'logradouro'); ?>">
											</div>
										</div>
										
										<div class="col-md-3">
											<div class="form-group">
												<label>Número</label>
												<input type="text" class="form-control" id="numero" readonly="readonly" value="<?php echo chk_array($modelo->form_data, 'numero'); ?>">
											</div>
										</div>
									</div>
									
									
									<div class="row">
										<div class="col-md-3">
											<label>Bairro</label>
											<div class="input-group">
												<input type="text" class="form-control" id="bairro" readonly="readonly" value="<?php echo chk_array($modelo->form_data, 'bairro'); ?>">
											</div>
										</div>
										
										<div class="col-md-3">
											<div class="form-group">
												<label>Cidade</label>
												<input type="text" class="form-control" id="cidade" readonly="readonly" value="<?php echo chk_array($modelo->form_data, 'cidade'); ?>">
											</div>
										</div>
										
										<div class="col-md-3">
											<div class="form-group">
												<label>Estado</label>
												<input type="text" class="form-control" id="estado" readonly="readonly" value="<?php echo chk_array($modelo->form_data, 'estado'); ?>">
											</div>
										</div>
										
										<div class="col-md-3">
											<div class="form-group">
												<label>Complemento</label>
												<input type="text" class="form-control" id="complemento" readonly="readonly" value="<?php echo chk_array($modelo->form_data, 'complemento'); ?>">
											</div>
										</div>
									</div>
								</div> 
								
								<div class="box-footer">
									<button type="submit" class="btn btn-primary">Salvar</button>
								</div>

							</div>
						</div>

						<div class="col-md-6">
							<div class="box box-primary">
								<div class="box-header with-border">
									<i class="fa fa-map-marker"></i>
									<h3 class="box-title">Google Map</h3>
								</div>

								<div class="box-body">
									<?php
									$latitude = !empty(chk_array($modelo->form_data, 'latitude')) ? chk_array($modelo->form_data, 'latitude') : SYS_LAT;
									$longitude = !empty(chk_array($modelo->form_data, 'longitude')) ? chk_array($modelo->form_data, 'longitude') : SYS_LNG;
									?>
									<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key=AIzaSyDqITRk0Rt9D7RsFR3spL9r_HiEupKEcY4&amp;"></script>
									<script type="text/javascript">
										var map;
										var infoWindow;
										var marker;
										var markersData = new Array();

										markersData = [
											<?php
											echo"
											{
												lat: " . $latitude . ",
												lng: " . $longitude . ",
												cep: '" . chk_array($modelo->form_data, 'cep') . "',
												endereco: '" . chk_array($modelo->form_data, 'logradouro') . ", " . chk_array($modelo->form_data, 'numero') . "',
												bairro: '" . chk_array($modelo->form_data, 'bairro') . "',
												cidade: '" . chk_array($modelo->form_data, 'cidade') . "',
												estado: '" . chk_array($modelo->form_data, 'estado') . "'
											}";
											?>
										];
										
										function createMarker(latlng, cep, endereco, bairro, cidade, estado){
											marker = new google.maps.Marker({
												map: map,
												position: latlng
											});
										   
										   var iwContent = '<div>' + '<div>' + endereco + '<br />' + bairro + '<br />' + cidade + '/' + estado + '<br />' +  cep + '</div></div>';

										   infoWindow.setContent(iwContent);
										   
										   google.maps.event.addListener(marker, 'click', function() {
												infoWindow.open(map, marker);
										   });
										   
										   infoWindow.open(map, marker);
										   
										   map.setZoom(18);
										}

										function displayMarkers(){
											var bounds = new google.maps.LatLngBounds();
											for (var i = 0; i < markersData.length; i++){
												var latlng = new google.maps.LatLng(markersData[i].lat, markersData[i].lng);
												var cep = markersData[i].cep;
												var endereco = markersData[i].endereco;
												var bairro = markersData[i].bairro;
												var cidade = markersData[i].cidade;
												var estado = markersData[i].estado;

												createMarker(latlng, cep, endereco, bairro, cidade, estado);
												map.setCenter(latlng);
												bounds.extend(latlng);  
											}
										}
										
										function removeMarkers(){
											marker.setMap(null);
										}
										
										function initialize() {
										   var mapOptions = {
											  zoom: 18,
											  center: new google.maps.LatLng(<?php echo $latitude; ?>,<?php echo $longitude; ?>),
											  mapTypeId: 'roadmap',
										   };

										   map = new google.maps.Map(document.getElementById('mapa'), mapOptions);

										   infoWindow = new google.maps.InfoWindow();

										   google.maps.event.addListener(map, 'click', function() {
											  infoWindow.close();
										   });
											
											<?php if(!empty(chk_array($modelo->form_data, 'logradouro'))){ ?>
											displayMarkers();
											<?php } ?>
										}
										
										google.maps.event.addDomListener(window, 'load', initialize);
									</script>
								
									<div id="mapa" style="width: 100%; height: 300px;"></div>
								</div> 
							</div>
						</div>
					</div>
				</form>
				
				<script>
				function getLocal(){
					$("#local").change(function(){
						var id = $('option:selected', this).attr("value");
						
						if(id != null && id != ''){
							$.ajax({
								type: "GET",
								url: "locais/json",
								data: {id: id},
								dataType: "json",
								success: function(dados){
									$("#titulo").val(dados.titulo);
									$("#cep").val(dados.cep);
									$("#logradouro").val(dados.logradouro);
									$("#numero").val(dados.numero);
									$("#complemento").val(dados.complemento);
									$("#bairro").val(dados.bairro);
									$("#cidade").val(dados.cidade);
									$("#estado").val(dados.estado);
									
									markersData = [{
										lat: dados.latitude,
										lng: dados.longitude,
										cep: dados.cep,
										endereco: dados.logradouro + ", " + dados.numero,
										bairro: dados.bairro,
										cidade: dados.cidade,
										estado: dados.estado
									}];
									
									displayMarkers();
								}
							});
						}else{
							$("#titulo").val("");
							$("#cep").val("");
							$("#logradouro").val("");
							$("#numero").val("");
							$("#complemento").val("");
							$("#bairro").val("");
							$("#cidade").val("");
							$("#estado").val("");
							
							removeMarkers();
						}

					});
				}
				
				$(document).ready(function(){
					$('#avatar').orakuploader({
						orakuploader_path : '<?php echo HOME_URI;?>/views/standards/plugins/orakuploader',
						orakuploader_phpscript : '<?php echo HOME_URI;?>/eventos/upload/index/avatar/', 

						orakuploader_main_path : '<?php echo chk_array($modelo->form_data, 'midia'); ?>',
						orakuploader_thumbnail_path : '<?php echo chk_array($modelo->form_data, 'midia'); ?>/tn',
						
						orakuploader_add_image : '<?php echo HOME_URI;?>/views/standards/plugins/orakuploader/images/add.png',
						orakuploader_add_label : 'Selecionar imagem',
						
						orakuploader_resize_to : 1200,
						orakuploader_thumbnail_size : 150,
						
						orakuploader_maximum_uploads : 1,
						orakuploader_hide_on_exceed : true,
						
						orakuploader_use_sortable : true,
						orakuploader_use_dragndrop : true,
						
						orakuploader_attach_images: [
							<?php
							if(file_exists(chk_array($modelo->form_data, 'midia'))){
								$lerDiretorio = opendir(chk_array($modelo->form_data, 'midia'));
								$imagens = array();
								$contadorArquivos = 0;
								while ($imagens[] = readdir($lerDiretorio));
								closedir($lerDiretorio);
								foreach ($imagens as $imagem) {
									if(preg_match("/\.(jpg|jpeg|gif|png){1}$/i", strtolower($imagem))) {
										echo"'" . $imagem . "'";
										$contadorArquivos++;
										if($contadorArquivos < $totalArquivosMidia){
											echo",";
										}
									}
								}
							}
							?>
						],
						
						orakuploader_max_exceeded : function() {
							alert("Você já selecionou todas as imagens, o limite é de 1 imagens.");
						}
						
					});
					
					setTimeout('getLocal()', 1000);
				});
				</script>