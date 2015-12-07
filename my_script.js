
	var obj_weather;
	var tempUnit = "";
	var count = 0;
	var prev_request = "";
	
			function WeatherRequest(){
				
				var location = searchform.location.value;
				var type = "";
				
				if(location==""){
					alert("Please enter a location");
					document.getElementById("info").innerHTML="";
					document.getElementById("city").innerHTML="";
					document.getElementById("country").innerHTML="";
					document.getElementById("image").innerHTML="";
					document.getElementById("low_high").innerHTML="";
					document.getElementById("temp_now").innerHTML="";
					document.getElementById("forecast_table").innerHTML="";
					document.getElementById("fb").innerHTML="";
					document.getElementById("yahoo_link").innerHTML="";
					return false;
				}
		
			num_pattern = /^\d+$/;

			if(num_pattern.test(location)){
			
				 zip_pattern = /^\d{5}$/; 
					if(!(zip_pattern.test(location))){
						alert("Invalid zipcode: must be five digits\nExample: 90089");
						document.getElementById("info").innerHTML="";
						document.getElementById("city").innerHTML="";
						document.getElementById("country").innerHTML="";
						document.getElementById("image").innerHTML="";
						document.getElementById("low_high").innerHTML="";
						document.getElementById("temp_now").innerHTML="";
						document.getElementById("forecast_table").innerHTML="";
						document.getElementById("fb").innerHTML="";
						document.getElementById("yahoo_link").innerHTML="";
						return false;
					}
					else{
						type = "zip";
					}
			}
			else{ 
				city_pattern = /^[\w\s]+,\s\w{2}$|^[\w\s]+,\s\w{2},\s[\w\s]+$|^[\w\s]+,\s[\w\s]+$/;
					if(!(city_pattern.test(location))){
						alert("Invalid location: must include state or country separated by comma\nExample: Los Angeles,CA");
						document.getElementById("info").innerHTML="";
						document.getElementById("city").innerHTML="";
						document.getElementById("country").innerHTML="";
						document.getElementById("image").innerHTML="";
						document.getElementById("low_high").innerHTML="";
						document.getElementById("temp_now").innerHTML="";
						document.getElementById("forecast_table").innerHTML="";
						document.getElementById("fb").innerHTML="";
						document.getElementById("yahoo_link").innerHTML="";
						return false;
					}
					else{
						type = "city";
					}
			}
				
			if(searchform.location.value){
				count++;
				
				if((count%2)==1){
					tempUnit="f";
					var url="http://www.elvislee.com/YahooWeather/MyServlet?location="+searchform.location.value+"&type="+type+"&tempUnit="+tempUnit;
				//	var url="http://cs-server.usc.edu:23455/examples/servlet/MyServlet?location="+searchform.location.value+"&type="+type+"&tempUnit="+tempUnit;
					
					loadXMLDoc(url);
				}
				else if((count%2)==0){
					tempUnit="c";
					var url="http://www.elvislee.com/YahooWeather/MyServlet?location="+searchform.location.value+"&type="+type+"&tempUnit="+tempUnit;
				//	var url="http://cs-server.usc.edu:23455/examples/servlet/MyServlet?location="+searchform.location.value+"&type="+type+"&tempUnit="+tempUnit;
					
					loadXMLDoc(url);
				}
			}
		}
		

		var req;
		function loadXMLDoc(url){
			req=false;
			
			if(window.XMLHttpRequest){
				try{
					req=new XMLHttpRequest();
				}catch(e){
					req=false;
					}
			}
			else if(window.ActiveXObject){		
				try{
					req=new ActiveXObject("Microsoft.XMLHttp");
				}catch(e){
					req=false;
				}
			}
			
			if(req){
				req.onreadystatechange=DisplayWeather; //Here we only need the name of the function that handles return.
				req.open("GET", url, true);		//The true means execute Asynchrously (AJAX)
				req.setRequestHeader("Connection", "Close");
				req.setRequestHeader("Method", "GET"+url+"HTTP/1.1");

			
				req.send();
				
			}
		}
		
		function DisplayWeather(){
		
			if(req.readyState==4){
				if(req.status==200){
					//var doc = ('('+req.responseText+')');
					var doc = req.responseText;
					
					if(doc=="" || doc=="Error"){
						document.getElementById("city").innerHTML="";
						document.getElementById("country").innerHTML="";
						document.getElementById("image").innerHTML="";
						document.getElementById("low_high").innerHTML="";
						document.getElementById("temp_now").innerHTML="";
						document.getElementById("forecast_table").innerHTML="";
						document.getElementById("fb").innerHTML="";
						document.getElementById("yahoo_link").innerHTML="";
						
						document.getElementById("info").innerHTML="Weather information cannot be found!";
						return false;		// To avoid code goes on!
					}
					
					var my_obj = eval("("+doc+")");
					
					
					document.getElementById("info").innerHTML="";
					document.getElementById("city").innerHTML="<A href="+my_obj.weather.feed+" style=\"text-decoration:none;color:#FFFFFF\">"+my_obj.weather.location.city+"</A>";
					document.getElementById("country").innerHTML=my_obj.weather.location.region+", "+my_obj.weather.location.country;
					document.getElementById("image").innerHTML="<img src=" +my_obj.weather.img+ " alt='weather_icon'>" +"<span>"+my_obj.weather.condition.text+"<span>";
					document.getElementById("low_high").innerHTML="&uarr;"+my_obj.weather.forecast[0].high+"&deg;"+my_obj.weather.units.temperature+"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"+"&darr;"+my_obj.weather.forecast[0].low+"&deg;"+my_obj.weather.units.temperature;
			     	document.getElementById("temp_now").innerHTML=my_obj.weather.condition.temp+"<A href='#' onclick='WeatherRequest()' style=\"color:#FFFFFF;font-size:35px\">"+"&deg;"+my_obj.weather.units.temperature
			     	+"</A>";
			    	document.getElementById("fb").innerHTML="<input type=\"radio\" name=\"choice\" id=\"P_Current\" checked=\"checked\"/>Post Current Weather<br /><input type=\"radio\" name=\"choice\" id=\"P_Forecast\"/>Post Weather Forecast<br /><button value=\"facebook\" onclick=\"User_Log()\" ><img src=\"facebook.jpg\"</button></div>";
					
			      YUI().use('datatable',function(Y) {
					var data = [
							{
								Day : my_obj.weather.forecast[0].day, Weather : my_obj.weather.forecast[0].text, 
								High : "<h style='color:#FF0000'>"+my_obj.weather.forecast[0].high+"&deg;"+ my_obj.weather.units.temperature+"</h>", 
								Low : "<l style='color:#0000FF'>"+my_obj.weather.forecast[0].low+"&deg;"+my_obj.weather.units.temperature+"</l>"
							},
							{
								Day : my_obj.weather.forecast[1].day, Weather : my_obj.weather.forecast[1].text,
								High : "<h style='color:#FF0000'>"+my_obj.weather.forecast[1].high+"&deg;"+my_obj.weather.units.temperature+"</h>", 
								Low : "<l style='color:#0000FF'>"+my_obj.weather.forecast[1].low+"&deg;"+my_obj.weather.units.temperature+"</l>"
							},
							{
								Day : my_obj.weather.forecast[2].day, Weather : my_obj.weather.forecast[2].text,
								High : "<h style='color:#FF0000'>"+my_obj.weather.forecast[2].high+"&deg;"+my_obj.weather.units.temperature+"</h>", 
								Low : "<l style='color:#0000FF'>"+my_obj.weather.forecast[2].low+"&deg;"+my_obj.weather.units.temperature+"</l>"
							},
							{
								Day : my_obj.weather.forecast[3].day, Weather : my_obj.weather.forecast[3].text,
								High : "<h style='color:#FF0000'>"+my_obj.weather.forecast[3].high+"&deg;"+my_obj.weather.units.temperature+"</h>", 
								Low : "<l style='color:#0000FF'>"+my_obj.weather.forecast[3].low+"&deg;"+my_obj.weather.units.temperature+"</l>"
							},	
							{
								Day : my_obj.weather.forecast[4].day, Weather : my_obj.weather.forecast[4].text,
								High : "<h style='color:#FF0000'>"+my_obj.weather.forecast[4].high+"&deg;"+my_obj.weather.units.temperature+"</h>",
								Low : "<l style='color:#0000FF'>"+my_obj.weather.forecast[4].low+"&deg;"+my_obj.weather.units.temperature+"</l>"
							} ];

					var table = new Y.DataTable({
						columns : [ "Day", "Weather", {
							key : "High",
							allowHTML : true
						}, {
							key : "Low",
							allowHTML : true
						} ],
						data : data,
						
					//	caption : "Forecast",

					});	
					document.getElementById("forecast_table").innerHTML="";	
					document.getElementById("forecast_table").innerHTML="Forecast" + "<br />";		
					table.render("#forecast_table");
				});						
			   		
			   		document.getElementById("yahoo_link").innerHTML="<A href=\"http://weather.yahoo.com/\">"+"<img src=\"news-wea.gif\" alt=\"yahoo_weather\">"+ "</A>";
			   		
					obj_weather = my_obj;
					
				}
				else{
					alert("There was a problem retrieving the XML data:\n" + req.statusText);
				}
			}
		}
		 window.fbAsyncInit = function() {
    		// init the FB JS SDK
    		FB.init({
     			appId      : '171008926439486',                        // App ID from the app dashboard
      			channelUrl : 'channel.html', // Channel file for x-domain comms
      			status     : true,                                 // Check Facebook Login status
      			xfbml      : true                                  // Look for social plugins on the page
    		});

    // Additional initialization code such as adding Event Listeners goes here
  };

	
	function User_Log(){
		//botton selection:
		if(document.getElementById("P_Current").checked){		
			var prop = {
			"Look at details" : {
			"text" : "here",
			"href" : obj_weather.weather.link
			}
		};
			FB.ui(
  	{
    	method: 'feed',
    	name: obj_weather.weather.location.city +", " +obj_weather.weather.location.region +", "+obj_weather.weather.location.country,
    	link: obj_weather.weather.feed,
    	picture: obj_weather.weather.img,
    	caption: "The current condition for " +obj_weather.weather.location.city +" is " + obj_weather.weather.condition.text,
    	description: "Temperature is " + obj_weather.weather.condition.temp +"&deg;"+ obj_weather.weather.units.temperature,
    	properties: prop,
    	message: "Homework 8"
    
  		},
  			function(response) {
    			if (response && response.post_id) {
      				alert('Post was published.');
    			} else {
      				alert('Post was not published.');
    			}
 	 		}
		);
	}	
		else if(document.getElementById("P_Forecast").checked){		
			var prop = {
			"Look at details" : {
			"text" : "here",
			"href" : obj_weather.weather.link
			}
		};
			FB.ui(
  	{
    	method: 'feed',
    	name: obj_weather.weather.location.city +", " +obj_weather.weather.location.region +", "+obj_weather.weather.location.country,
    	link: obj_weather.weather.feed,
    	picture: "http://cs-server.usc.edu:23455/examples/HW_8/fore_weather.jpg",
    	caption: "Weather Forecast for " +obj_weather.weather.location.city,
    	description: obj_weather.weather.forecast[0].day+": "+obj_weather.weather.forecast[0].text+", "+obj_weather.weather.forecast[0].high+"/"+obj_weather.weather.forecast[0].low+"&deg;"+obj_weather.weather.units.temperature
    				+"; "+obj_weather.weather.forecast[1].day+": "+obj_weather.weather.forecast[1].text+", "+obj_weather.weather.forecast[1].high+"/"+obj_weather.weather.forecast[1].low+"&deg;"+obj_weather.weather.units.temperature	
    				+"; "+obj_weather.weather.forecast[2].day+": "+obj_weather.weather.forecast[2].text+", "+obj_weather.weather.forecast[2].high+"/"+obj_weather.weather.forecast[2].low+"&deg;"+obj_weather.weather.units.temperature  	
    				+"; "+obj_weather.weather.forecast[3].day+": "+obj_weather.weather.forecast[3].text+", "+obj_weather.weather.forecast[3].high+"/"+obj_weather.weather.forecast[3].low+"&deg;"+obj_weather.weather.units.temperature
    				+"; "+obj_weather.weather.forecast[4].day+": "+obj_weather.weather.forecast[4].text+", "+obj_weather.weather.forecast[4].high+"/"+obj_weather.weather.forecast[4].low+"&deg;"+obj_weather.weather.units.temperature,
    				 
    	properties: prop,
    	message: "Homework 8"
    
  		},
  			function(response) {
    			if (response && response.post_id) {
      				alert('Post was published.');
    			} else {
      				alert('Post was not published.');
    			}
 	 		}
		);
	}
	
	}

  // Load the SDK asynchronously
  (function(){
     // If we've already installed the SDK, we're done
     if (document.getElementById('facebook-jssdk')) {return;}

     // Get the first script element, which we'll use to find the parent node
     var firstScriptElement = document.getElementsByTagName('script')[0];

     // Create a new script element and set its id
     var facebookJS = document.createElement('script'); 
     facebookJS.id = 'facebook-jssdk';

     // Set the new script's source to the source of the Facebook JS SDK
     facebookJS.src = '//connect.facebook.net/en_US/all.js';

     // Insert the Facebook JS SDK into the DOM
     firstScriptElement.parentNode.insertBefore(facebookJS, firstScriptElement);
   }());		
