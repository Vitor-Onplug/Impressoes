<?php
if (!defined('ABSPATH')) exit;

$activePage = isset($_GET['path']) ? explode('/', $_GET['path']) : null;

$painelUsuarios = $this->load_model('usuarios/usuarios');

//echo $_SESSION['userdata']['id'];
//echo json_encode($_SESSION);

?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $this->title; ?></title>

	<base href="<?php echo HOME_URI; ?>/">

	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<link rel="stylesheet" href="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/fontawesome-free/css/all.min.css">
	<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
	<link rel="stylesheet" href="<?php echo HOME_URI; ?>/views/standards/adminlte//plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
	<link rel="stylesheet" href="<?php echo HOME_URI; ?>/views/standards/adminlte/css/adminlte.min.css">

	<link rel="stylesheet" href="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/select2/css/select2.min.css">

	<!-- Datepicker CSS -->
	<link rel="stylesheet" href="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css">

	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/jquery/jquery.min.js"></script>
</head>

<body class="hold-transition sidebar-mini">
	<div class="wrapper">
		<nav class="main-header navbar navbar-expand navbar-white navbar-light">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
				</li>
			</ul>

			<!-- SELECT PARA SELECIONAR UM OUTRO EVENTO -->
			<?php
			$idEventoHash = isset($_SESSION['idEventoHash']) ? $_SESSION['idEventoHash'] : null;
			if ($idEventoHash !== null) {
				$idEvento = decryptHash($idEventoHash);
				$modeloEvento = $this->load_model('eventos/eventos');
				$eventos = $modeloEvento->getEventos();
			?>
				<div class="form-inline mx-2">
					<label class="mr-2" for="selectEvento">Evento Atual:</label>
					<select class="form-control select2-search" id="selectEvento" name="selectEvento" style="width: 250px;">
						<?php foreach ($eventos as $evento) : ?>
							<option value="<?= $evento['idEvento'] ?>" <?= $evento['idEvento'] == $idEvento ? 'selected' : '' ?>>
								<?= $evento['evento'] ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php } ?>

			<ul class="navbar-nav ml-auto">
				<li class="nav-item">
					<a class="nav-link" data-widget="fullscreen" href="#" role="button">
						<i class="fas fa-expand-arrows-alt"></i>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo HOME_URI; ?>/logout" role="button" alt="Sair">
						<i class="fas fa-sign-out-alt"></i>
					</a>
				</li>
			</ul>
		</nav>

		<aside class="main-sidebar sidebar-dark-primary elevation-4">
			<a href="./" class="brand-link text-center">
				<span class="brand-text font-weight-light"><?php echo SYS_NAME; ?></span>
			</a>

			<div class="sidebar">
				<div class="user-panel mt-3 pb-3 mb-3 d-flex">
					<div class="image">
						<img src="" class="img-circle elevation-2" alt="User Image">
					</div>
					<div class="info">
						<a href="<?php echo HOME_URI; ?>/pessoas/index/perfil/<?php echo $_SESSION['userdata']['id']; ?>" class="d-block text-center"><?php echo chk_array($this->userdata, 'nome'); ?> <?php echo chk_array($this->userdata, 'sobrenome'); ?></a>
					</div>
				</div>

				<div class="form-inline">
					<div class="input-group" data-widget="sidebar-search">
						<input class="form-control form-control-sidebar" type="search" placeholder="Buscar" aria-label="Buscar">
						<div class="input-group-append">
							<button class="btn btn-sidebar">
								<i class="fas fa-search fa-fw"></i>
							</button>
						</div>
					</div>
				</div>

				<nav class="mt-2">
					<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
						<li class="nav-item"><a href="./" class="nav-link <?php if (!isset($activePage[0])) {
																				echo 'active';
																			} ?>"><i class="nav-icon fas fa-tachometer-alt"></i>
								<p>Dashboard</p>
							</a></li>
						<?php if ($this->check_permissions('evento-adicionar', $this->userdata['modulo'])) { ?>
							<li class="nav-item"><a href="./" class="nav-link <?php if (!isset($activePage[0])) {
																					echo 'active';
																				} ?>"><i class="nav-icon fas fa-tachometer-alt"></i>
									<p>Dashboard</p>
								</a></li>
						<?php } ?>

						<li class="nav-item">
							<a href="#" class="nav-link"><i class="nav-icon fas fa-calendar-alt"></i>
								<p>Eventos <i class="right fas fa-angle-left"></i></p>
							</a>
							<ul class="nav nav-treeview">
								<li class="nav-item <?php if (isset($activePage[0]) && $activePage[0] == 'eventos' && $activePage[2] != 'adicionar') {
														echo 'active';
													} ?>"><a href="<?php echo HOME_URI; ?>/eventos" class="nav-link"><i class="far fa-calendar nav-icon"></i>
										<p>Eventos</p>
									</a></li>
								<?php //if($this->check_permissions('evento-adicionar', $this->userdata['modulo'])){
								?>
								<li class="nav-item <?php if (isset($activePage[0]) && $activePage[0] == 'eventos' && $activePage[2] == 'adicionar') {
														echo 'active';
													} ?>"><a href="<?php echo HOME_URI; ?>/eventos/index/adicionar" class="nav-link"><i class="far fa-calendar-plus nav-icon"></i>
										<p>Adicionar</p>
									</a></li>
								<?php //} 
								?>
							</ul>
						</li>

						<?php
						$modeloEvento = $this->load_model('eventos/eventos');
						$idEventoHash = isset($_SESSION['idEventoHash']) ? $_SESSION['idEventoHash'] : null;
						if ($idEventoHash !== null) {
							$idEvento = decryptHash($idEventoHash);
							$evento = $modeloEvento->getEvento($idEvento);
						?>
							<li class="nav-item">
								<a href="#" class="nav-link"><i class="nav-icon fa-solid fa-eye"></i>
									<p>Evento <?php echo htmlentities(chk_array($modeloEvento->form_data, 'evento')); ?> <i class="right fas fa-angle-left"></i></p>
								</a>
								<ul class="nav nav-treeview">
									<li class="nav-item <?php if (isset($activePage[0]) && $activePage[0] == 'eventos' && $activePage[2] == 'setores') {
															echo 'active';
														} ?>"><a href="<?php echo HOME_URI; ?>/eventos/index/setores" class="nav-link"><i class="fas fa-map-marker nav-icon"></i>
											<p>Setores</p>
										</a></li>
									<li class="nav-item <?php if (isset($activePage[0]) && $activePage[0] == 'eventos' && $activePage[2] == 'terminais') {
															echo 'active';
														} ?>"><a href="<?php echo HOME_URI; ?>/eventos/index/terminais" class="nav-link"><i class="fab fa-raspberry-pi nav-icon"></i>
											<p>Terminais</p>
										</a></li>
									<li class="nav-item <?php if (isset($activePage[0]) && $activePage[0] == 'eventos' && $activePage[2] == 'leitores') {
															echo 'active';
														} ?>"><a href="<?php echo HOME_URI; ?>/eventos/index/leitores" class="nav-link"><i class="fas fa-qrcode nav-icon"></i>
											<p>Leitores</p>
										</a></li>
									<li class="nav-item <?php if (isset($activePage[0]) && $activePage[0] == 'eventos' && $activePage[2] == 'lotes') {
															echo 'active';
														} ?>"><a href="<?php echo HOME_URI; ?>/eventos/index/lotes" class="nav-link"><i class="fas fa-id-card nav-icon"></i>
											<p>Lotes de Credenciais</p>
										</a></li>
									<li class="nav-item <?php if (isset($activePage[0]) && $activePage[0] == 'eventos' && $activePage[2] == 'ingressos') {
															echo 'active';
														} ?>"><a href="<?php echo HOME_URI; ?>/eventos/index/ingressos" class="nav-link"><i class="fas fa-ticket-alt nav-icon"></i>
											<p>Ingressos</p>
										</a></li>
								</ul>
							</li>
						<?php } ?>

						<li class="nav-item">
							<a href="#" class="nav-link"><i class="nav-icon fas fa-user"></i>
								<p>Parceiros <i class="right fas fa-angle-left"></i></p>
							</a>
							<ul class="nav nav-treeview">
								<li class="nav-item <?php if (isset($activePage[0]) && $activePage[0] == 'parceiros' && $activePage[2] != 'adicionar') {
														echo 'active';
													} ?>"><a href="<?php echo HOME_URI; ?>/parceiros" class="nav-link"><i class="far fa-calendar nav-icon"></i>
										<p>Parceiros</p>
									</a></li>
								<?php //if($this->check_permissions('empresa-adicionar', $this->userdata['modulo'])){
								?>
								<li class="nav-item <?php if (isset($activePage[0]) && $activePage[0] == 'parceiros' && $activePage[2] == 'adicionar') {
														echo 'active';
													} ?>"><a href="<?php echo HOME_URI; ?>/parceiros/index/adicionar" class="nav-link"><i class="far fa-calendar-plus nav-icon"></i>
										<p>Adicionar</p>
									</a></li>
								<?php //} 
								?>
							</ul>
						</li>

						<li class="nav-item">
							<a href="#" class="nav-link"><i class="nav-icon fas fa-building"></i>
								<p>Empresas <i class="right fas fa-angle-left"></i></p>
							</a>
							<ul class="nav nav-treeview">
								<li class="nav-item <?php if (isset($activePage[0]) && $activePage[0] == 'empresas' && $activePage[2] != 'adicionar') {
														echo 'active';
													} ?>"><a href="<?php echo HOME_URI; ?>/empresas" class="nav-link"><i class="far fa-calendar nav-icon"></i>
										<p>Empresas</p>
									</a></li>
								<?php //if($this->check_permissions('empresa-adicionar', $this->userdata['modulo'])){
								?>
								<li class="nav-item <?php if (isset($activePage[0]) && $activePage[0] == 'empresas' && $activePage[2] == 'adicionar') {
														echo 'active';
													} ?>"><a href="<?php echo HOME_URI; ?>/empresas/index/adicionar" class="nav-link"><i class="far fa-calendar-plus nav-icon"></i>
										<p>Adicionar</p>
									</a></li>
								<?php //} 
								?>
							</ul>
						</li>

						<?php //if($this->check_permissions('pessoas', $this->userdata['modulo'])){
						?>
						<li class="nav-item <?php if (isset($activePage[0]) && $activePage[0] == 'pessoas') {
												echo 'menu-open';
											} ?>">
							<a href="#" class="nav-link <?php if (isset($activePage[0]) && $activePage[0] == 'pessoas') {
															echo 'active';
														} ?>"><i class="nav-icon fas fa-walking"></i>
								<p>Pessoas <i class="right fas fa-angle-left"></i></p>
							</a>
							<ul class="nav nav-treeview">
								<li class="nav-item"><a href="<?php echo HOME_URI; ?>/pessoas" class="nav-link <?php if (isset($activePage[0]) && $activePage[0] == 'pessoas' && !isset($activePage[2])) {
																													echo 'active';
																												} ?>"><i class="far fa-calendar nav-icon"></i>
										<p>Pessoas</p>
									</a></li>
								<?php //if($this->check_permissions('pessoa-adicionar', $this->userdata['modulo'])){
								?>
								<li class="nav-item"><a href="<?php echo HOME_URI; ?>/pessoas/index/adicionar" class="nav-link <?php if (isset($activePage[0]) && $activePage[0] == 'pessoas' && (isset($activePage[2]) && $activePage[2] == 'adicionar')) {
																																	echo 'active';
																																} ?>"><i class="far fa-calendar-plus nav-icon"></i>
										<p>Adicionar</p>
									</a></li>
								<?php //} 
								?>
								<li class="nav-item"><a href="<?php echo HOME_URI; ?>/pessoas/index/perfil/<?php echo $_SESSION['userdata']['id']; ?>" class="nav-link <?php if (isset($activePage[0]) && $activePage[0] == 'usuarios' && isset($activePage[2]) && $activePage[2] == 'perfil' && $activePage[3] = null) {
																																											echo 'active';
																																										} ?>"><i class="fas fa-user nav-icon"></i>
										<p>Seu Perfil</p>
									</a></li>
							</ul>
						</li>
						<?php //} 
						?>

						<?php if ($this->check_permissions('sistema', $this->userdata['modulo'])) { ?>
							<li class="nav-item">
								<a href="#" class="nav-link"><i class="nav-icon fas fa-cogs"></i>
									<p>Configurações <i class="right fas fa-angle-left"></i></p>
								</a>
								<ul class="nav nav-treeview">
									<li class="nav-item"><a href="<?php echo HOME_URI; ?>/permissoes" class="nav-link"><i class="fas fa-key nav-icon"></i>
											<p>Permissões</p>
										</a></li>
								</ul>
							</li>
						<?php } ?>
					</ul>
				</nav>
			</div>
		</aside>

		<?php
		require $conteudo;
		?>

		<aside class="control-sidebar control-sidebar-dark"></aside>

		<footer class="main-footer">
			<strong><?php echo SYS_NAME; ?> &copy; <?php echo SYS_YEAR; ?> - <a href="<?php echo SYS_COPYRIGHT_URL; ?>"><?php echo SYS_COPYRIGHT; ?></a></strong> - Todos direitos reservados.
			<div class="float-right d-none d-sm-inline-block">
				<b>Versão</b> <?php echo SYS_VERSION; ?>
			</div>
		</footer>
	</div>

	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/jszip/jszip.min.js"></script>
	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/pdfmake/pdfmake.min.js"></script>
	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/pdfmake/vfs_fonts.js"></script>
	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/datatables-buttons/js/buttons.print.min.js"></script>
	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

	<!-- Mudar aqui pra local futuramente -->

	<!-- jQuery UI Widget (necessário para jQuery File Upload) -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.32.0/js/vendor/jquery.ui.widget.js"></script>
	<!-- jQuery File Upload -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.32.0/js/jquery.fileupload.min.js"></script>

	<!-- Datepicker JS -->
	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>

	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/moment/moment-with-locales.js"></script>
	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>

	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/select2/js/select2.full.min.js"></script>

	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/plugins/inputmask/jquery.inputmask.min.js"></script>
	<script src="<?php echo HOME_URI; ?>/views/standards/javascripts/jquery.priceformat.min.js"></script>

	<script src="<?php echo HOME_URI; ?>/views/standards/adminlte/js/adminlte.js"></script>
	<script src="<?php echo HOME_URI; ?>/views/standards/javascripts/cep.js"></script>
	<script src="<?php echo HOME_URI; ?>/views/standards/javascripts/app.js"></script>

	<script>
		$(document).ready(function() {

			// Inicializa o Select2 no campo de evento
			$('#selectEvento').select2({
				theme: "classic"

			});

			$('#selectEvento').change(function() {
				var idEvento = $(this).val();
				$.ajax({
					url: '<?php echo HOME_URI; ?>/eventos/index/setEvento',
					type: 'POST',
					data: {
						idEvento: idEvento
					},
					success: function(data) {
						location.reload();
					}
				});
			});
		});
	</script>
</body>

</html>