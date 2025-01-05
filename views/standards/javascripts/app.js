$(function() {
	$('.money').priceFormat({
		prefix: '',
		centsSeparator: ',',
		thousandsSeparator: '.'
	});
	
	$('.dataTable').DataTable({
		"oLanguage": {
			"sEmptyTable": "Nenhum registro encontrado",
			"sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
			"sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
			"sInfoFiltered": "(Filtrados de _MAX_ registros)",
			"sInfoPostFix": "",
			"sInfoThousands": ".",
			"sLengthMenu": "_MENU_ resultados por página",
			"sLoadingRecords": "Carregando...",
			"sProcessing": "Processando...",
			"sZeroRecords": "Nenhum registro encontrado",
			"sSearch": "Pesquisar",
			"oPaginate": {
				"sNext": "Próximo",
				"sPrevious": "Anterior",
				"sFirst": "Primeiro",
				"sLast": "Último"
			},
			"oAria": {
				"sSortAscending": ": Ordenar colunas de forma ascendente",
				"sSortDescending": ": Ordenar colunas de forma descendente"
			}
		},
		"responsive": true, // Torna a tabela responsiva
		"paging": true,
		"lengthChange": false,
		"searching": true,
		"ordering": true,
		"info": true,
		"autoWidth": true,
		"pageLength": 20, 

		"dom": "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
           "<'row'<'col-sm-12'tr>>" +
           "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
		
		"columnDefs": [{
			"targets": 'sorter-false',
			"orderable": false,
		}]
	});
	
	$(".cpf").inputmask("999.999.999-99");
	$(".cnpj").inputmask("99.999.999-9999/99");
	
	$(".telefone").inputmask("(99) 9999-9999");
	$(".celular").inputmask("(99) 99999-9999");
	
	$(".cep").inputmask("99999-999");
	$(".data").inputmask("DD/MM/YYYY");
	
	
	$('.datetimepicker').datetimepicker({
		locale: 'pt-br',
		format: 'L'
	});
	
	$('.select2').select2({
		language: "pt-br"
	});
});

function select2ajax(origem, destino, url, atributo, atributoDestino){
	$(origem).change(function(){
		var id = $('option:selected', this).attr(atributo);
		
		if(atributoDestino != null && atributoDestino != ''){
			destino = $('option:selected', this).attr(atributoDestino);
		}
		
		$.ajax({
			type: "GET",
			url: url,
			data: {id: id},
			dataType: "json",
			success: function(dados){
				var options = "";
				 $.each(dados, function(index) {
					chave = dados[index].chave;
					valor = dados[index].valor;

					options += '<option value="' + chave + '">' + valor + '</option>';
				});
				
				$(destino).html(options);
			}
		});
	});
}
