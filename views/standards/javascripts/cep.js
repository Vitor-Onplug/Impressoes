$(function() {

	$('#cep').blur(function() { buscaCEP($('#cep').val(),'#cidade','#bairro','#estado','#logradouro','#numero',$("#status-mapa").attr('value')) });
	
	function buscaCEP(cep, cidade, bairro, uf, logradouro, numero, mapa){
		if(cep != '') {
	    	$.getScript('/utils/cep/?cep=' + cep, function(data){			
				$(uf).val('')
			    $(cidade).val('')
			    $(bairro).val('')
			    $(logradouro).val('')
 
				if(resultadoCEP.resultado == 0)
					alert('Não foi encontrado nenhum endereço para este CEP!\nCaso ele realmente exista, preencha o endereço manualmente!')
				else if(resultadoCEP.resultado == 1) {
					$(uf).val(resultadoCEP.uf)
					$(cidade).val(unescape(resultadoCEP.cidade))
					$(bairro).val(unescape(resultadoCEP.bairro))
					$(logradouro).val(unescape(resultadoCEP.tipo_logradouro) + " " + unescape(resultadoCEP.logradouro))
					$(numero).focus()
					
					if(mapa == "true"){
						carregarEnderecoMapa(unescape(resultadoCEP.tipo_logradouro) + " " + unescape(resultadoCEP.logradouro) + " - " + unescape(resultadoCEP.bairro) + ", "  + unescape(resultadoCEP.cidade) + "/" + resultadoCEP.uf + " - " + cep)
					}
				}
				else {
					$(uf).val(resultadoCEP.uf)
					$(cidade).val(unescape(resultadoCEP.cidade))
					$(logradouro).focus()
					if(mapa == "true"){
						carregarEnderecoMapa(unescape(resultadoCEP.cidade) + "/" + resultadoCEP.uf + " - " + cep)
					}
				}
			});
		}
		
    	return false;
	}
})