<div class="wrap">
	<div id="icon-ar1" class="ar1"><br></div> 
	<h2 id="add-new-user">Configuracion Plugin Autoresponder1</h2> 
 
	<div id="ajax-response"></div> 
 
	<p style="font-weight:bold">Por Favor ingrese su informacion de Usuario en Autoresponder1.</p>

	<div class="error" id="error" style="display:none;"><strong><p id="error"></p></strong></div>
	<div class="updated" id="success" style="display:none;"><strong><p id="success"></p></strong></div>

   
	<form action="#login-check" method="post" name="adduser" id="adduser" class="add:users: validate"> 
		<input id="_wpnonce" name="_wpnonce" value="5d825100c0" type="hidden">
		<input name="_wp_http_referer" value="/wp-admin/user-new.php" type="hidden">
        <input type="hidden" name="ar1_url" id="ar1_url" value="http://www.ar1mail.com/c/">
		<table class="form-table"> 
			<tbody>
				<tr class="form-field form-required"> 
					<th scope="row"><label for="username">Usuario <span style="font-size:10px" class="description">(requerido)</span></label> 
					<input name="action" id="action" value="adduser" type="hidden"></th> 
					<td><input size="10" name="username" id="username" value="<?php if(isset($Options['Username'])) echo $Options['Username'];?>" aria-required="true" type="text"></td> 
				</tr> 
				<tr class="form-field form-required"> 
					<th scope="row"><label for="password">Password <span style="font-size:10px" class="description">(requerido)</span></label></th> 
					<!-- <td><input name="password" id="password" autocomplete="off" type="password"></td>  -->
					<td><input size="10" name="password" id="password" type="password" value="<?php if(isset($Options['Password'])) echo $Options['Password'];?>"></td> 
				</tr> 
			</tbody>
		</table> 
		<p class="submit"> 
			<input name="login-check" id="login-check" class="button-primary" value="Verificar" type="submit"> 
		</p> 
	</form>
	
	<div id="content-after-login">
		<form action="#do-nothing" style="display:none;" id="subscriber-list-form">
			<table class="form-table">
				<tbody>
					<tr valign="top" class="form-field form-required">
						<th scope="row"><label for="subscriber-lists"><span style="font-weight:bold">Seleccione una Lista</span></label></th>
						<td>
							<select name="subscriber-lists" id="subscriber-lists"></select>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
		
		<form action="#do-nothing" style="display:none;" id="custom-field-form">
			<table class="form-table">
				<tbody>
					<tr>			
						<th scope="row">
				        	<label for="checklist-content">Seleccione Campos Personalizados</label>
				        </th>
						<td id="checklist-content" name="checklist-content" style="margin-left:150px;">
							<!-- Here comes the checklist content with AJAX -->
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit"> 
				<input name="get-html-code" id="get-html-code" class="button-primary" value="Generar Formulario" type="submit"> 
			</p> 
			
		</form>

	
	</div>
	
	<div id="form-gotten-added" class="updated" style="display:none;"><p><strong>Formulario tomado de su Configuracion. Agregue el Widget Autoresponder1 desde el Menu Wordpress <span style="color:#900">"Apariencia -> Widgets"</span>.</strong></p>
	</div>
	
	<textarea id="dummy" style="width:300px; height:100px;display:none;">
	</textarea>
</div>

<script type="text/javascript" charset="utf-8">

	var unfilledFieldError = "Todos los Campos son Requeridos. Por Favor llenelos todos.";
	var serverConnectionError = "Error al conectarse al servidor. Favor verifique la URL ingresada. Si lo ha hecho correctamente favor espere un momento.";
	var noar1FoundError = "No existe el servicio en la direccion ingresada. Favor asegurese que la ingresado correctamente.";
	var unsuccessfulLogin = "Error en Ingreso al Sistema de Autoresponder1, Verifique sus datos de Acceso: ";
	var successfulLogin = "Su Ingreso ha sido Exitoso. Por Favor Proceda.";
	var invalidUP = "Usuario y Password no concuerdan. Favor intente de nuevo.";
	var customFieldError = "No se pudo obtener informacion del servidor de Autoresponder1. Esta es la respuesta: ";
	var captchaEnabledError = "No se pudo ingresar al Servidor de Autoresponder1. Asegurese que el sistema captcha no está habilitado.";
	var numOfCustomFields = 0;

	jQuery(document).ready(function($) {
		
		var sessionID;
		var ar1URL;
		
		/* Check user's login information - Begin */
		$("input#login-check").click(function(){
			$("span#form-gotten-added").hide();
			$("div#success").hide();
			$("select#subscriber-lists").empty();
			$("div#content-after-login").hide();
			$("form#custom-field-form").hide();
			$("div#form-gotten-added").hide();

			/* Check if user entered 3 fields - Begin */
			if(!($("#username").val())||!($("#password").val())||!($("#ar1_url").val())){
				//User didn't enter at least 1 field, so show the error message.
				$("p#error").text(unfilledFieldError)
				$("div#error").show();
			}
			/* Check if user entered 3 fields - End */
			
			/* User entered 3 fields, send AJAX call - Begin */
			else{
				ar1URL = $("input#ar1_url").val();
				$("div#error").hide();
				$("input#login-check").val("Enviando...");
			
				/* Post fields to the ar1.php/loginCheck function - Begin */
				$.post("<?php echo get_option('siteurl').'/wp-admin/admin-ajax.php';?>", {action:"ar1_login", username:$("#username").val(), password:$("#password").val(), ar1_url:$("#ar1_url").val(), "cookie": encodeURIComponent(document.cookie)},
				function(response)
				{
					//Revert the text on the button
					$("input#login-check").val("Verificar");
					
					/* Check if user entered a valid address - Begin */
					if(response == "false")
					{
						$("p#error").text(serverConnectionError);
						$("div#error").show();
					}
					/* Check if user entered a valid ar1 address - End */
					
					/* we caught a 404 error - Begin */
					if(response.indexOf("<html>") != -1)
					{
						$("p#error").text(noar1FoundError);
						$("div#error").show();
					}
					/* we caught a 404 error - End */
					
					/* URL entered by user is validated, so check login information - Begin */
					else
					{
						var responseObject = eval('(' + response + ')');
						/* If Login successful, diplay message, get lists from responseObject and display dropdown - Begin */
						if(responseObject.Success)
						{
							$("div#content-after-login").show();
							sessionID = responseObject.SessionID;
							$("p#success").text(successfulLogin);
							$("div#success").show();
							
							/* Call getLists function with AJAX call - Begin */
							$.post("<?php echo get_option('siteurl').'/wp-admin/admin-ajax.php';?>", {action:"ar1_get_subscriber_lists", SessionID:sessionID, ar1_url:$("#ar1_url").val(), "cookie": encodeURIComponent(document.cookie)},
							function(getListsResponse)
							{
								var listsObject = eval('(' + getListsResponse + ')');

								for(var i in listsObject.Lists)
								{
									$("select#subscriber-lists").append('<option value="'+ listsObject.Lists[i].ListID +'">'+ listsObject.Lists[i].Name + '</option>');
									// console.log(listsObject.Lists[i]);
								}
								
								// console.log(listsObject.Lists);
							});
							/* Call getLists function with AJAX call - End */
							
							$("form#subscriber-list-form").show();
						}
						/* If Login successful, diplay message, get lists from responseObject and display dropdown - End */
						
						/* If login unsuccessful, display error message with reason given by ar1 API - Begin */
						else
						{
							if(responseObject.ErrorCode[0] == "4")
							{
								$("p#error").text(captchaEnabledError);
								$("div#error").show();
								return false;
							}
							if(responseObject.ErrorText[0]=="Informacion de login Invalida")
							{
								$("p#error").text(invalidUP);
								$("div#error").show();
								return false;
							}
							$("p#error").text(unsuccessfulLogin + responseObject.ErrorText[0]);
							$("div#error").show();
						}
						/* If login unsuccessful, display error message with reason given by ar1 API - End */
					}
					/* URL entered by user is validated, so check login information - End */
					
				});
				/* Post fields to the ar1.php/loginCheck function - End */
			}
			/* User entered 3 fields, send AJAX call - End */

		 
			return false;
		});
		/* Check user's login information - End */
		
		/* When subscriber list dropdown changes, get namefields of given subscriber list - Begin */
		$("select#subscriber-lists").mouseup(function(){
			
			/* Call getCustomFields function with AJAX call - Begin */
			$.post("<?php echo get_option('siteurl').'/wp-admin/admin-ajax.php';?>", {action:"ar1_get_custom_fields", SubscriberListID:$("select#subscriber-lists").val(), SessionID:sessionID, ar1_url:ar1URL, "cookie": encodeURIComponent(document.cookie)},
			function(response)
			{
				var customFieldsObject = eval('(' + response + ')');
				/* if server returns false as result - Begin */
				if(!customFieldsObject.Success)
				{
					$("p#error").text(customFieldError + customFieldsObject.ErrorText[0]);
					$("div#error").show();
				}
				else
				{
					$("td#checklist-content").empty();
					$("form#custom-field-form").show();

					numOfCustomFields = 0;

					/* foreach custom field in the array, display a checkbox - Begin */
					for(var i in customFieldsObject.CustomFields)
					{
						$("td#checklist-content").append('<p><input type="checkbox" name="' + customFieldsObject.CustomFields[i].CustomFieldID + '" value="' + customFieldsObject.CustomFields[i].FieldName + '" id="' + i +'"><label for="' + customFieldsObject.CustomFields[i].CustomFieldID + '">' + customFieldsObject.CustomFields[i].FieldName + '</label></p>');
						numOfCustomFields++;
					}
					/* foreach custom field in the array, display a checkbox - End */
				}
				/* if server returns false as result - End */
				
			});
			/* Call getCustomFields function with AJAX call - End */
		});
		/* When subscriber list dropdown changes, get namefields of given subscriber list - End */
		
		/* Get Subscription form HTML from the user - Begin */
		$("input#get-html-code").click(function(){
			
			$("input#get-html-code").val("Generando...");
			
			var customFields = '';
			
			$("textarea#dummy").empty();
			
			for(var i=0; i<numOfCustomFields; i++)
			{
				if($("input#"+i+":checked").val() != null)
				{
					if(customFields == "")
					{
						customFields = $("input#"+i).attr("name");
					}
					else
					{
						customFields += "," + $("input#"+i).attr("name");
					}
				}
			}

			$.post("<?php echo get_option('siteurl').'/wp-admin/admin-ajax.php';?>", {action:"ar1_get_subscription_form_html_code", SubscriberListID:$("select#subscriber-lists").val(), SessionID:sessionID, ar1_url:ar1URL, "cookie": encodeURIComponent(document.cookie), CustomFields:customFields},
			function(response)
			{
				var subscriptionFormObject = eval('(' + response + ')');
				
				var subscriptionText = '';
				
				/* append the html code to the textarea - Begin */
				for(var j in subscriptionFormObject.HTMLCode)
				{
					$("textarea#dummy").append(subscriptionFormObject.HTMLCode[j]);
				}
				/* append the html code to the textarea- End */

				$.post("<?php echo get_option('siteurl').'/wp-admin/admin-ajax.php';?>", {action:"ar1_save_subscription_form", FormHTMLCode:$("textarea#dummy").val(), "cookie": encodeURIComponent(document.cookie)},
				function(saveFormResponse)
				{
					$("input#get-html-code").val("Generar Formulario");
					
					//TODO: get the response from the server and warn user if error occures.
					$("div#form-gotten-added").show();	
				});
				

				console.log($("textarea#dummy").val());
				// register the widget
			});
			
			return false;
		});
		/* Get Subscription form HTML from the user - End */
	});
</script>
