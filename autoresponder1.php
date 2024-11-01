<?php

/*
Plugin Name: Autoesponder1
Plugin URI: http://www.autoresponder1.com
Description: Este Plugin le permite agregar un formulario de suscripcion de Autoresponder1 al Sidebar de su Pagina o Blog Wordpress.
Version: 1.0
Author: Autoresponder1
Author URI: http://www.autoresponder1.com
*/

/*	
	Filename: autoresponder1.php
	Created on: Martes de Enero 21:59:00 EEST 2011
	Author: Autoresponder1
	Owner: Autoresponder1 Ltd.
	All rights reserved. Autoresponder1 © 2011.
*/

/* Autoresponder1 Class definition - Begin */
if (!class_exists("Autoresponder1")){
    
	class Autoresponder1 {
        
		var $AdminOptions = "Autoresponder1AdminOptions";

		/**
		 * Autoresponder1 constructor function
		 *
		 * @return void
		 * @author Autoresponder1
		 **/
		function Autoresponder1(){}
		
		/**
		 * returns current admin options from the DB
		 *
		 * @return array
		 * @author Autoresponder1
		 **/
		function getAdminOptions()
		{
			return get_option($this->AdminOptions);
       	}

		/**
		 * displays the admin page by getting content from php/admin_view.php file.
		 *
		 * @return void
		 * @author Autoresponder1
		 **/
		function displayAdminPage()
		{
			
			$Options = $this->getAdminOptions();
			
			//fetch given file and display
			include "php/admin_view.php";
		}
		
		/**
		 * adds an option to the wordpress options.
		 *
		 * @param string $OptionString  
		 * @return void
		 * @author Autoresponder1
		 */
		function updateAdminOptions($OptionArray)
		{
			$Options = $this->getAdminOptions();
			
			foreach($OptionArray as $Key => $Value)
			{
				$Options[$Key] = $Value;
			}
			
			update_option($this->AdminOptions, $Options);
		}
		
		/**
		 * registers widget to the blog by using wp admin settings.
		 *
		 * @return void
		 * @author Autoresponder1
		 **/
		function registerWidget()
		{
			register_sidebar_widget('Autoresponder1', array('Autoresponder1', 'printWidgetContent'));
		}
		
		/**
		 * undocumented function
		 *
		 * @return void
		 * @author Autoresponder1
		 **/
		function saveSubscriptionFormHTMLCode()
		{
			$this->updateAdminOptions(array('FormHTMLCode' => $_POST['FormHTMLCode']));
		}
		
		/**
		 * prints the widget content
		 *
		 * @return void
		 * @author Autoresponder1
		 **/
		function printWidgetContent()
		{
			$Options = get_option("Autoresponder1AdminOptions");

			$Header = '<div class="widget autoresponder1-widget"><div class="widget-content"><p style="font-size: 20px;" class="widgettitle">Suscribase</p>';
			$Css = '<br />';
			$Footer = '</div></div>';
			
			if(isset($Options['FormHTMLCode']))
			{
				$FormHTMLCode = $Options['FormHTMLCode'];
				$FormHTMLCode = stripslashes($FormHTMLCode);
				echo $Header.$Css.$FormHTMLCode.$Footer;
			}
			else{
			
				echo $Header;
				echo $Css;
				var_dump($Options);
				echo $Footer;
			}
		}
		
		/**
		 * echos the result of CURL call to the given ar1 server.
		 *
		 * @return void
		 * @author Autoresponder1
		 **/
		function loginCheck()
		{
			/* Setup POST parameters - Begin */
			$ArrayPostParameters = array();
			$ArrayPostParameters[] = "Command=User.Login";
			$ArrayPostParameters[] = "ResponseFormat=JSON";
			$ArrayPostParameters[] = "Username=".$_POST['username'];
			$ArrayPostParameters[] = "Password=".$_POST['password'];
			/* Setup POST parameters - End */
			
			$this->updateAdminOptions(	array("Username" => $_POST['username'], 
										"Password" => $_POST['password'],
										"URL" => $_POST['ar1_url']
										)
									);
			
			$response = $this->_postToRemoteURL($_POST['ar1_url']."/api.php?", $ArrayPostParameters);
			if($response[0]==false)
			{
				echo "false";
			}
			else
			{
				echo $response[1];
			}
			exit;
		}
		
		/**
		 * echos all the subscriber lists
		 *
		 * @return void
		 * @author Autoresponder1
		 **/
		function getSubscriberLists()
		{
			/* Setup POST parameters - Begin */
			$ArrayPostParameters = array();
			$ArrayPostParameters[] = "Command=Lists.Get";
			$ArrayPostParameters[] = "ResponseFormat=JSON";
			$ArrayPostParameters[] = "SessionID=".$_POST['SessionID'];
			$ArrayPostParameters[] = "OrderField=ListID";
			$ArrayPostParameters[] = "OrderType=ASC";
			
			/* Setup POST parameters - End */
			
			$response = $this->_postToRemoteURL($_POST['ar1_url']."/api.php?", $ArrayPostParameters);
			echo $response[1];
			exit;
		}
		
		/**
		 * returns the custom fields of given subscriber list
		 *
		 * @return void
		 * @author Autoresponder1
		 **/
		function getCustomFields()
		{
			/* Setup POST parameters - Begin */
			$ArrayPostParameters = array();
			$ArrayPostParameters[] = "Command=CustomFields.Get";
			$ArrayPostParameters[] = "ResponseFormat=JSON";
			$ArrayPostParameters[] = "SessionID=".$_POST['SessionID'];
			$ArrayPostParameters[] = "OrderField=FieldName";
			$ArrayPostParameters[] = "OrderType=ASC";
			$ArrayPostParameters[] = "SubscriberListID=".$_POST['SubscriberListID'];
			/* Setup POST parameters - End */
			
			$response = $this->_postToRemoteURL($_POST['ar1_url']."/api.php?", $ArrayPostParameters);
			echo $response[1];
			exit;
		}
		
		/**
		 * echos the subscription form HTML code 
		 *
		 * @return void
		 * @author Autoresponder1
		 **/
		function getSubscriptionFormHTMLCode()
		{
			/* Setup POST parameters - Begin */
			$ArrayPostParameters = array();
			$ArrayPostParameters[] = "Command=ListIntegration.GenerateSubscriptionFormHTMLCode";
			$ArrayPostParameters[] = "ResponseFormat=JSON";
			$ArrayPostParameters[] = "SessionID=".$_POST['SessionID'];
			$ArrayPostParameters[] = "SubscriberListID=".$_POST['SubscriberListID'];
			$ArrayPostParameters[] = "CustomFields=".$_POST['CustomFields'];
			/* Setup POST parameters - End */
			
			$response = $this->_postToRemoteURL($_POST['ar1_url']."/api.php?", $ArrayPostParameters);
			
			
			echo $response[1];
			exit;
		}
		
		/**
		 * POSTs given array to a remote URL with CURL
		 *
		 * @return array(1:bool, 2:response text)
		 * @author Autoresponder1
		 **/
		function _postToRemoteURL($URL, $ArrayPostParameters, $HTTPRequestType = 'POST', $HTTPAuth = false, $HTTPAuthUsername = '', $HTTPAuthPassword = '', $ConnectTimeOutSeconds = 5, $ReturnHeaders = false)
		{
			$PostParameters = implode('&', $ArrayPostParameters);
			$CurlHandler = curl_init();
			curl_setopt($CurlHandler, CURLOPT_URL, $URL);

			if ($HTTPRequestType == 'GET')
			{
				curl_setopt($CurlHandler, CURLOPT_HTTPGET, true);
			}
			elseif ($HTTPRequestType == 'PUT')
			{
				curl_setopt($CurlHandler, CURLOPT_PUT, true);
			}
			elseif ($HTTPRequestType == 'DELETE')
			{
				curl_setopt($CurlHandler, CURLOPT_CUSTOMREQUEST, 'DELETE');
			}
			else
			{
				curl_setopt($CurlHandler, CURLOPT_POST, true);
				curl_setopt($CurlHandler, CURLOPT_POSTFIELDS, $PostParameters);
			}

			curl_setopt($CurlHandler, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($CurlHandler, CURLOPT_CONNECTTIMEOUT, $ConnectTimeOutSeconds);
			curl_setopt($CurlHandler, CURLOPT_TIMEOUT, $ConnectTimeOutSeconds);
			curl_setopt($CurlHandler, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3');

			// The option doesn't work with safe mode or when open_basedir is set.
			if ((ini_get('safe_mode') != false) && (ini_get('open_basedir') != false))
			{
				curl_setopt($CurlHandler, CURLOPT_FOLLOWLOCATION, true);
			}

			if ($ReturnHeaders == true)
			{
				curl_setopt($CurlHandler, CURLOPT_HEADER, true);
			}
			else
			{
				curl_setopt($CurlHandler, CURLOPT_HEADER, false);
			}

			if ($HTTPAuth == true)
			{
				curl_setopt($CurlHandler, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($CurlHandler, CURLOPT_USERPWD, $HTTPAuthUsername.':'.$HTTPAuthPassword);
			}

			$RemoteContent = curl_exec($CurlHandler);

			if (curl_error($CurlHandler) != '')
			{
				return array(false, curl_error($CurlHandler));
			}

			curl_close($CurlHandler);	

			return array(true, $RemoteContent);
		}
		
	} //end of class Autoresponder1
 
}
/* Autoresponder1 Class definition - End */

/* Create object instance if class exists - Begin */
if(class_exists("Autoresponder1")){
	$Autoresponder1 = new Autoresponder1();
}
/* Create object instance if class exists - End */

/* Initialize Admin Panel - Begin */
if (!function_exists("ar1AdminPanel")) {
	function ar1AdminPanel() {
		global $Autoresponder1;
		if (!isset($Autoresponder1)) 
		{
			return;
		}
		if (function_exists('add_options_page')) 
		{
			add_options_page('Autoresponder1', 'Autoresponder1', 9, basename(__FILE__), array(&$Autoresponder1, 'displayAdminPage'));
		}
	}	
}
/* Initialize Admin Panel - End */

/* Define WP Actions & Filters - Begin */
if(isset($Autoresponder1))
{
	/* Actions - Begin */
	add_action('admin_menu', 'ar1AdminPanel');
	// add_action('wp_head', array(&$Autoresponder1, 'enqueueScript'), 1);

		/* Add actions for AJAX calls - Begin */
		add_action('wp_ajax_ar1_login', array(&$Autoresponder1, 'loginCheck'));
		add_action('wp_ajax_ar1_get_subscriber_lists', array(&$Autoresponder1, 'getSubscriberLists'));
		add_action('wp_ajax_ar1_get_custom_fields', array(&$Autoresponder1, 'getCustomFields'));
		add_action('wp_ajax_ar1_get_subscription_form_html_code', array(&$Autoresponder1, 'getSubscriptionFormHTMLCode'));
		add_action('wp_ajax_ar1_save_subscription_form', array(&$Autoresponder1, 'saveSubscriptionFormHTMLCode'));
		/* Add actions for AJAX calls - End */

   
	/* register widget with registerWidget function - Begin */
	add_action('widgets_init', array(&$Autoresponder1, 'registerWidget'), 1);
	/* register widget with registerWidget function - End */	
	
   
      
	/* Actions - End */

	/* Filters - Begin */

	/* Filters - End */
}
/* Define WP Actions & Filters - End */


/*
Adiciona el estilo CSS
*/

		
function tb_inject() {
	
   		 echo "<!-- Autoresponder1 plugin v1.0-->\n";
		 echo "<link rel='stylesheet' href='".get_bloginfo('wpurl')."/wp-content/plugins/autoresponder1/css/autoresponder1.css' type='text/css' />\n";
		 echo "<!-- /Autoresponder1 plugin -->\n";

}

add_action('wp_head', 'tb_inject', 10);


?>