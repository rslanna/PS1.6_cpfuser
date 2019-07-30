<!-- Block CPF Usuário -->

<script type="text/javascript">
{literal}
jQuery(function($){

    $('#cpfuser_error').hide();
    $('#doctype-1').prop( "checked", true );
    
    $('input[name=doc_type]').click(function (){
        $('#cpfuser_error').hide();
        var value = $(this).val();
        if (value === '2'){
            $('#fieldCNPJ').hide(function (){
                $('#fieldCPF').show('slow');
            });
        } else {
            $('#fieldCPF').hide(function (){
                $('#fieldCNPJ').show('slow');
            });
        }
    });
    
    var docType = $('input[name=doc_type]:checked').val();
    if (docType === '2'){
        $('#fieldCNPJ').hide(function (){
            $('#fieldCPF').show();
        });
    } else {
        $('#fieldCPF').hide(function (){
            $('#fieldCNPJ').show();
        });
    }
    
    $("#cnpj").mask('99.999.999/9999-99');
    $("#cpf").mask('999.999.999-99');

});
    
    function validateDoc(id){
        var document = $(id).serialize();

        if ($('#cpf').val() !== ''){
            var tipoDoc = 'cpf';
        } else {
            var tipoDoc = 'cnpj';
        }

        $.ajax({
            type: "POST",
            url: $('#urlValidateDoc').val(),
            data: document,
            dataType: "json",
            success: function (data){
                if ( data.status == 1 ){
                    $('#cpfuser_error').hide();
                    $('#validate-' + tipoDoc).attr('class','required form-group form-ok');
                    $("#validDoc").attr('value', 'true');
                    $('#submitAccount:disabled').removeAttr('disabled');
                }else{
                    $('#cpfuser_error').show();
                    $('#cpfuser_error p').empty();
                    $('#cpfuser_error p').append(data.error);
                    $('#validate-' + tipoDoc).attr('class','required form-group form-error');
                    $("#validDoc").attr('value', 'false');
                    $('#submitAccount').attr('disabled','disabled');
                }
            }
        });
    }

{/literal}
</script>

<div class="account_creation">

    <h3 class="page-subheading">{l s='Documentos' mod='cpfuser'}</h3>
        <p class="form-group">
            <label>Tipo de pessoa:</label>
            <div class="radio">
              <label>
                <input type="radio" id="doctype-0" value="1" name="doc_type">
                {l s='Pessoa Jurídica' mod='cpfuser'}
              </label>
            </div>
            <div class="radio">
              <label>
                <input type="radio" id="doctype-1" value="2" name="doc_type">
                {l s='Pessoa física' mod='cpfuser'}
              </label>
            </div>
        <input type="hidden" name="validDoc" id="validDoc" value="{if isset($smarty.post.validDoc)}{$smarty.post.validDoc}{/if}">
        <input type="hidden" name="urlValidateDoc" id="urlValidateDoc" value="{$urlValidateDoc}" />
        </p>

        <div class="alert alert-danger fade in" id="cpfuser_error">
            <p></p>
        </div>

        <div id="fieldCPF">
            <p id="validate-cpf" class="required form-group">
                <label for="cpf">{l s='CPF:' mod='cpfuser'} <sup>*</sup></label>
                <input type="text" class="form-control" name="cpf" id="cpf" value="{if isset($smarty.post.document)}{$smarty.post.document}{/if}" onBlur="validateDoc(this)" />
            </p>

            <p class="form-group">
                <label for="rg">{l s='RG:' mod='cpfuser'}</label>
                <input type="text" class="form-control" name="rg" id="rg" value="{if isset($smarty.post.rg_ie)}{$smarty.post.rg_ie}{/if}" />
            </p>
        </div>

        <div id="fieldCNPJ">
            <p id="validate-cnpj" class="required form-group">
                <label for="cnpj">{l s='CNPJ:' mod='cpfuser'} <sup>*</sup></label>
                <input type="text" class="form-control" name="cnpj" id="cnpj" value="{if isset($smarty.post.document)}{$smarty.post.document}{/if}" onBlur="validateDoc(this)" />
            </p>

            <p class="form-group">
                <label for="nie">{l s='IE:' mod='cpfuser'}</label>
                <input type="text" class="form-control" name="nie" id="nie" value="{if isset($smarty.post.rg_ie)}{$smarty.post.rg_ie}{/if}" />
            </p>
        </div>
</div>

<!-- /Block CPF Usuário -->
