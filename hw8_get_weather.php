<?php header('Content-type: text/xml');

	$link_yahoo_apis = "http://where.yahooapis.com/v1/";
						
			if($_GET["type"]=='zip'){
					
					$link_yahoo_apis .= "concordance/usps/" . $_GET["location"] . "?appid=jWFgr1zV34Fg73A71mcxoxfk0nBZxHk.sZTUFuhYKGmVHhnJaK8PxHlHMubYGHquc8Qa";
	
					
					@$xmlid = simplexml_load_file($link_yahoo_apis);	//@Don't show Warning.
					
					if($xmlid===FALSE){			//URL錯誤會直接回傳FALSE, 表示沒有此Zip code
						echo "Zero results find";
						exit(0);	
					}
							
							$weather_yahoo_rss = "http://weather.yahooapis.com/forecastrss?w=$xmlid->woeid&u=" . $_GET["tempUnit"];
							$xml = simplexml_load_file($weather_yahoo_rss);
							$Build_XML = new DOMDocument('1.0','UTF-8');	//This is the XML builder(Root of the weather tag)
							//Create root tag weather
        					$root_Weather = $Build_XML->createElement("weather");
          					$root_Weather = $Build_XML->appendChild($root_Weather);
							
							//Create feed tag
          					$feed = $Build_XML->createElement("feed");
          					$feed = $root_Weather->appendChild($feed);
          					$Feed_Value = $Build_XML->createTextNode($weather_yahoo_rss);
          					$Feed_Value = $feed->appendChild($Feed_Value);

							//Create link tag
          					$link = $Build_XML->createElement("link");
          					$link = $root_Weather->appendChild($link);
          					$Link_Value = $Build_XML->createTextNode($xml->channel->link);
         					$Link_Value = $link ->appendChild($Link_Value);

							//Create location tag, attributes are also use appenChild to add.
          					$location = $Build_XML->createElement("location");
          					$location = $root_Weather->appendChild($location);
          					$L_city = $Build_XML->createAttribute("city");
          					$L_city->value = $xml->channel->children('yweather', true)->location->attributes()->city;
          					$L_city = $location->appendChild($L_city);
							
          					$L_region = $Build_XML->createAttribute("region");
          					$L_region->value = $xml->channel->children('yweather', true)->location->attributes()->region;
          					$L_region = $location->appendChild($L_region);
							
          					$L_country = $Build_XML->createAttribute("country");
          					$L_country->value = $xml->channel->children('yweather', true)->location->attributes()->country;
          					$L_country = $location->appendChild($L_country);
							
							//Create units tag
          					$units = $Build_XML->createElement("units");
          					$units = $root_Weather->appendChild($units);
          					$U_temp = $Build_XML->createAttribute("temperature");
          					$U_temp->value = $xml->channel->children('yweather', true)->units->attributes()->temperature;
          					$U_temp = $units ->appendChild($U_temp);

							//Create condition tag
          					$condition = $Build_XML->createElement("condition");
          					$condition = $root_Weather->appendChild($condition);
          					$C_text = $Build_XML->createAttribute("text");
          					$C_text->value = $xml->channel->item->children('yweather', true)->condition->attributes()->text;
          					$C_text = $condition ->appendChild($C_text);
          					$C_temp = $Build_XML->createAttribute("temp");
          					$C_temp->value = $xml->channel->item->children('yweather', true)->condition->attributes()->temp;
          					$C_temp = $condition ->appendChild($C_temp);

							//Create img tag
							$pattern_img = '/src="(.*?)"/i';
							preg_match($pattern_img, $xml->channel->item->description, $matches);
          					$img = $Build_XML->createElement("img");
          					$img = $root_Weather->appendChild($img);
          					$I_url = $Build_XML->createTextNode($matches[1]);
          					$I_url = $img->appendChild($I_url);
							
							//Create five forecast tags
          					foreach($xml->channel->item->children('yweather', true)->forecast as $fore){
              				$forecast = $Build_XML->createElement("forecast");
              				$forecast = $root_Weather->appendChild($forecast);
              				$F_day = $Build_XML->createAttribute("day");
              				$F_day->value = $fore->attributes()->day;
              				$F_day = $forecast ->appendChild($F_day);

              				$F_low = $Build_XML->createAttribute("low");
              				$F_low->value = $fore->attributes()->low;
              				$F_low = $forecast ->appendChild($F_low);

              				$F_high = $Build_XML->createAttribute("high");
              				$F_high->value = $fore->attributes()->high;
              				$F_high = $forecast ->appendChild($F_high);

              				$F_text = $Build_XML->createAttribute("text");
							$F_text->value = $fore->attributes()->text;
							$F_text = $forecast->appendChild($F_text);

          					}

          					$Build_XML->formatOutput = true;
          					echo $Build_XML->saveXML();
     	  				}

			if($_GET["type"]=='city'){
				
					$city = urlencode($_GET["location"]);
					
					//$link_yahoo_apis .= "places\$and(.q('$city'),.type(7));start=0;count=1?appid=jWFgr1zV34Fg73A71mcxoxfk0nBZxHk.sZTUFuhYKGmVHhnJaK8PxHlHMubYGHquc8Qa"; 
					
					$urlencode = 'http://where.yahooapis.com/v1/places'.'$'.'and'. "(.q('".$city."'),.type(7));start=0;count=5?appid=jWFgr1zV34Fg73A71mcxoxfk0nBZxHk.sZTUFuhYKGmVHhnJaK8PxHlHMubYGHquc8Qa";
					
				//	$urlencode = urlencode($link_yahoo_apis);
					
					@$xmlid = simplexml_load_file($urlencode);
							
					if(@$xmlid->count()==0){			//When there is no child in the node, that means there is no result in the city name.
						echo "Zero results found!";
						exit(0);
					}
				//	foreach($xmlid->children() as $second_Gen){
							
						$weather_yahoo_rss = "http://weather.yahooapis.com/forecastrss?" . "w=" . $xmlid->place->woeid[0] ."&u=" . $_GET["tempUnit"];
						
						
							$xml = simplexml_load_file($weather_yahoo_rss);
							$Build_XML = new DOMDocument('1.0','UTF-8');	//This is the XML builder(Root of the weather tag)
							//Create root tag weather
        					$root_Weather = $Build_XML->createElement("weather");
          					$root_Weather = $Build_XML->appendChild($root_Weather);
							
							//Create feed tag
          					$feed = $Build_XML->createElement("feed");
          					$feed = $root_Weather->appendChild($feed);
          					$Feed_Value = $Build_XML->createTextNode($weather_yahoo_rss);
          					$Feed_Value = $feed->appendChild($Feed_Value);

							//Create link tag
          					$link = $Build_XML->createElement("link");
          					$link = $root_Weather->appendChild($link);
          					$Link_Value = $Build_XML->createTextNode($xml->channel->link);
         					$Link_Value = $link ->appendChild($Link_Value);

							//Create location tag, attributes are also use appenChild to add.
          					$location = $Build_XML->createElement("location");
          					$location = $root_Weather->appendChild($location);
          					$L_city = $Build_XML->createAttribute("city");
          					$L_city->value = $xml->channel->children('yweather', true)->location->attributes()->city;
          					$L_city = $location->appendChild($L_city);
							
          					$L_region = $Build_XML->createAttribute("region");
          					$L_region->value = $xml->channel->children('yweather', true)->location->attributes()->region;
          					$L_region = $location->appendChild($L_region);
							
          					$L_country = $Build_XML->createAttribute("country");
          					$L_country->value = $xml->channel->children('yweather', true)->location->attributes()->country;
          					$L_country = $location->appendChild($L_country);
							
							//Create units tag
          					$units = $Build_XML->createElement("units");
          					$units = $root_Weather->appendChild($units);
          					$U_temp = $Build_XML->createAttribute("temperature");
          					$U_temp->value = $xml->channel->children('yweather', true)->units->attributes()->temperature;
          					$U_temp = $units ->appendChild($U_temp);

							//Create condition tag
          					$condition = $Build_XML->createElement("condition");
          					$condition = $root_Weather->appendChild($condition);
          					$C_text = $Build_XML->createAttribute("text");
          					$C_text->value = $xml->channel->item->children('yweather', true)->condition->attributes()->text;
          					$C_text = $condition ->appendChild($C_text);
          					$C_temp = $Build_XML->createAttribute("temp");
          					$C_temp->value = $xml->channel->item->children('yweather', true)->condition->attributes()->temp;
          					$C_temp = $condition ->appendChild($C_temp);

							//Create img tag
							$pattern_img = '/src="(.*?)"/i';
							preg_match($pattern_img, $xml->channel->item->description, $matches);
          					$img = $Build_XML->createElement("img");
          					$img = $root_Weather->appendChild($img);
          					$I_url = $Build_XML->createTextNode($matches[1]);
          					$I_url = $img->appendChild($I_url);
							
							//Create five forecast tags
          					foreach($xml->channel->item->children('yweather', true)->forecast as $fore){
              				$forecast = $Build_XML->createElement("forecast");
              				$forecast = $root_Weather->appendChild($forecast);
              				$F_day = $Build_XML->createAttribute("day");
              				$F_day->value = $fore->attributes()->day;
              				$F_day = $forecast ->appendChild($F_day);

              				$F_low = $Build_XML->createAttribute("low");
              				$F_low->value = $fore->attributes()->low;
              				$F_low = $forecast ->appendChild($F_low);

              				$F_high = $Build_XML->createAttribute("high");
              				$F_high->value = $fore->attributes()->high;
              				$F_high = $forecast ->appendChild($F_high);

              				$F_text = $Build_XML->createAttribute("text");
							$F_text->value = $fore->attributes()->text;
							$F_text = $forecast->appendChild($F_text);

          					}

          					$Build_XML->formatOutput = true;
          					echo $Build_XML->saveXML();
			}
					/*
								if($_POST["temperature"]=="Fahrenheit")
									$weather_yahoo_rss = $weather_yahoo_rss . "f";
								elseif($_POST["temperature"]=="Celsius")
									$weather_yahoo_rss = $weather_yahoo_rss . "c";
						*/		

						/*  以下暫時注解掉
						
								$xml = simplexml_load_file($weather_yahoo_rss);
								
								$xml_format = new SimpleXMLElement("<weather/>"); //Build XML element with root <weather></weather>
								$xml_format->addChild("feed");
								$xml_format->feed = $weather_yahoo_rss;	 //addChild() has a problem with "&", so use this way to build 
								
								$xml_format->addChild("link", $xml->channel->link);
								
									foreach($xml->channel as $entry){
											$yweather = $entry->children("http://xml.weather.yahoo.com/ns/rss/1.0");
											$arr1 = $yweather->location->attributes();  //Get all attributes from yweather:location and save them in $arr
											
											if($arr1['city']=="")
												$city = "N/A";
											else
												$city = $arr1['city']; 
									
											if($arr1['region']=="")
												$region = "N/A";
											else
												$region= $arr1['region'];
											
											if($arr1['country']=="")
												$country= "N/A";
											else
												$country = $arr1['country'];
										}
								$xml_format->addChild("location");
								$xml_format->location->addAttribute("city", $city);
								$xml_format->location->addAttribute("region", $region);
								$xml_format->location->addAttribute("country", $country);
								
								foreach($xml->channel as $entry){
									$yweather = $entry->children("http://xml.weather.yahoo.com/ns/rss/1.0");
									$arr2 = $yweather->units->attributes();
									if($arr2['temperature']=="")
										$units = "N/A";
									else
										$units = $arr2['temperature'];
									}
								$xml_format->addChild("units");
								$xml_format->units->addAttribute("temperature", $units);
								
								foreach($xml->channel->item as $entry){
											$yweather = $entry->children("http://xml.weather.yahoo.com/ns/rss/1.0");
											$arr1 = $yweather->condition->attributes();  //Get all attributes from yweather:condition and save them in $arr
											
											if($arr1['text']=="")
												$text = "N/A";
											elseif($arr1['temp']=="")
												$temp = "N/A";
											else{
											$text = $arr1['text']; 
											$temp = $arr1['temp'];
											}
										}
								$xml_format->addChild("condition");
								$xml_format->condition->addAttribute("text", $text);
								$xml_format->condition->addAttribute("temp", $temp);
								
								
								$pattern_img = '/src="(.*?)"/i';
								$pic = $xml->channel->item->description ;
								
								preg_match($pattern_img, $pic, $matches);
									
								foreach($xml->channel->item as $entry){
									$yweather = $entry->children("http://xml.weather.yahoo.com/ns/rss/1.0");
									$arr1 = $yweather->condition->attributes();  //Get all attributes from yweather:condition and save them in $arr1		
									if($arr1=="")
										$img = "N/A";
									else
										$img = $matches[1];
								}	
								$xml_format->addChild("img", $img);
								
								
								foreach($xml->channel->item as $entry){
									$yweather = $entry->children("http://xml.weather.yahoo.com/ns/rss/1.0");
										foreach($yweather->forecast as $entry2){
								
											$arr1 = $entry2->attributes();  //Get all attributes from yweather:location and save them in $arr
										
											if($arr1['day']=="")
												$day = "N/A";
											else
												$day = $arr1['day']; 
									
											if($arr1['low']=="")
												$low = "N/A";
											else
												$low = $arr1['low'];
											
											if($arr1['high']=="")
												$high = "N/A";
											else
												$high = $arr1['high'];
											
											if($arr1['text']=="")
												$ftext = "N/A";
											else
												$ftext = $arr1['text'];
										
											$xml_format->addChild("forecast");
											foreach($xml_format->forecast as $fore){	//Build 5 forecast tags. Each time loop and see 
												if($fore->attributes()==""){			// if it has attributes or not
													$fore->addAttribute("day", $day);
													$fore->addAttribute("low", $low);
													$fore->addAttribute("high", $high);
													$fore->addAttribute("text", $ftext);
												}	
											}
										}
									}
							//	$xml_format->formatOutput = TRUE;
								echo $xml_format->saveXML();	// Turn into XML.
							
	
							
							}	*/		
				//			$html_text = "<html><head></head><body><table border=1><tbody><tr><th>Weather</th><th>Temperature</th><th>City</th><th>Region</th><th>Country</th><th>Latitude</th><th>Longitude</th><th>Link to Details</th></tr>";
				//			$html_text .= "<tr>";
						
							
				//			print_r($xml);
				//			header("Location: $xml");
					/*
									// Get pic for weather column
									$pattern_img = '/src="(.*?)"/i';
									$pic = $xml->channel->item->description ;
							
									preg_match($pattern_img, $pic, $matches);
								
									foreach($xml->channel->item as $entry){
										$yweather = $entry->children("http://xml.weather.yahoo.com/ns/rss/1.0");
										$arr1 = $yweather->condition->attributes();  //Get all attributes from yweather:condition and save them in $arr1		
										if($arr1=="")
											$html_text .= "<td align='center'>" . "N/A" ."</td>";
										else
											$html_text .= "<td align='center'>" . "<A href='$weather_yahoo_rss' target='_blank'>" . "<img src='$matches[1]' alt='$arr1[text]' title='$arr1[text]'>" . "</A>" . "</td>";
									}	
							 						
									// Get the item for temperature column.
									foreach($xml->channel->item as $entry){
										$yweather = $entry->children("http://xml.weather.yahoo.com/ns/rss/1.0");
										$arr1 = $yweather->condition->attributes();  //Get all attributes from yweather:condition and save them in $arr
										
										if($arr1['text']=="")
											$html_text .= "<td align='center'>" . " N/A ";
										elseif($arr1['temp']=="")
											$html_text .= " N/A ";
										else{
										$html_text .= "<td align='center'>"; 
										$html_text .= $arr1['text'] . ' ' . $arr1['temp'] . '&deg; ' ;
										}
									}
									foreach($xml->channel as $entry){
										$yweather = $entry->children("http://xml.weather.yahoo.com/ns/rss/1.0");
										$arr2 = $yweather->units->attributes();
										if($arr2['temperature']=="")
											$html_text .= " N/A " . "</td>";
										else
											$html_text .= $arr2['temperature'] . "</td>";
									}
							
									// Get the item for city, region and country column
									foreach($xml->channel as $entry){
										$yweather = $entry->children("http://xml.weather.yahoo.com/ns/rss/1.0");
										$arr1 = $yweather->location->attributes();  //Get all attributes from yweather:location and save them in $arr
										
										if($arr1['city']=="")
											$html_text .= "<td align='center'>" . "N/A" ."</td>";
										else
											$html_text .= "<td align='center'>" . $arr1['city'] . "</td>"; 
								
										if($arr1['region']=="")
											$html_text .= "<td align='center'>" . "N/A" ."</td>";
										else
											$html_text .= "<td align='center'>" . $arr1['region'] . "</td>";
										
										if($arr1['country']=="")
											$html_text .= "<td align='center'>" . "N/A" ."</td>";
										else
										$html_text .= "<td align='center'>" . $arr1['country'] . "</td>";
									}
							
									// Get the values for latitude and Longtitude column
									foreach($xml->channel->item as $entry){
										$geo = $entry->children("http://www.w3.org/2003/01/geo/wgs84_pos#");
										
										if($geo->lat=="")
											$html_text .= "<td align='center'>" . "N/A" ."</td>";
										else
											$html_text .= "<td align='center'>" . $geo->lat . "</td>";
										
										if($geo->long=="")
											$html_text .= "<td align='center'>" . "N/A" ."</td>";
										else
											$html_text .= "<td align='center'>" . $geo->long . "</td>";  
									}
							
									// Get the Link Details 
										if($xml->channel->link=="")
											$html_text .= "<td align='center'>" . "N/A" ."</td>";
										else
											$html_text .= "<td align='center'><A href='" . $xml->channel->link . "'target='_blank'>Details</A></td></tr>";
							
					//		$count++;
  					//		echo $html_text;  
					//		echo $count . " result(s) for Zip Code " . $_GET["location"] ; 
				}

				*/
				/*
				if($_POST["location_type"]=="City"){
					$city = $_POST["location"];
						
					$link_yahoo_apis .= "places\$and(.q('$city'),.type(7));start=0;count=5?appid=" . $_POST["appid"]; 
				//		header("Location: $link_yahoo_apis"); 
					$urlencode = urlencode($link_yahoo_apis);
			
					@$xmlid = simplexml_load_file($urlencode);
							
					if(@$xmlid->count()==0){			//When there is no child in the node, that means there is no result in the city name.
						echo "Zero results found!";
						exit(0);
					}
					$count = 0;
							// Building the head of table
							$html_text = "<html><head></head><body><table border=1><tbody><tr><th>Weather</th><th>Temperature</th><th>City</th><th>Region</th><th>Country</th><th>Latitude</th><th>Longitude</th><th>Link to Details</th></tr>";
							foreach($xmlid->children() as $second_Gen){
								
								foreach($second_Gen->woeid as $third_Gen){
									$weather_yahoo_rss = "http://weather.yahooapis.com/forecastrss?w=$third_Gen&u=";
									if($_POST["temperature"]=="Fahrenheit")
										$weather_yahoo_rss = $weather_yahoo_rss . "f";
									elseif($_POST["temperature"]=="Celsius")
										$weather_yahoo_rss = $weather_yahoo_rss . "c";
									
									$html_text .= "<tr>";
						
									$xml = simplexml_load_file($weather_yahoo_rss);
									$check_error = '/Error/i';
									$error_title = $xml->channel->title;
									preg_match($check_error, $error_title, $matches);
									if($matches)
										continue;
									
									// Get pic for weather column
									$pattern_img = '/src="(.*?)"/i';
									$pic = $xml->channel->item->description ;
							
									preg_match($pattern_img, $pic, $matches);
								
									foreach($xml->channel->item as $entry){
										$yweather = $entry->children("http://xml.weather.yahoo.com/ns/rss/1.0");
										$arr1 = $yweather->condition->attributes();  //Get all attributes from yweather:condition and save them in $arr1		
										if($arr1=="")
											$html_text .= "<td align='center'>" . "N/A" ."</td>";
										else
											$html_text .= "<td align='center'>" . "<A href='$weather_yahoo_rss' target='_blank'>" . "<img src='$matches[1]' alt='$arr1[text]' title='$arr1[text]'>" . "</A>" . "</td>";
									}	
							 						
									// Get the item for temperature column.
									foreach($xml->channel->item as $entry){
										$yweather = $entry->children("http://xml.weather.yahoo.com/ns/rss/1.0");
										$arr1 = $yweather->condition->attributes();  //Get all attributes from yweather:condition and save them in $arr
										
										if($arr1['text']=="")
											$html_text .= "<td align='center'>" . " N/A ";
										elseif($arr1['temp']=="")
											$html_text .= " N/A ";
										else{
										$html_text .= "<td align='center'>"; 
										$html_text .= $arr1['text'] . ' ' . $arr1['temp'] . '&deg; ' ;
										}
									}
									foreach($xml->channel as $entry){
										$yweather = $entry->children("http://xml.weather.yahoo.com/ns/rss/1.0");
										$arr2 = $yweather->units->attributes();
										if($arr2['temperature']=="")
											$html_text .= " N/A " . "</td>";
										else
											$html_text .= $arr2['temperature'] . "</td>";
									}
							
									// Get the item for city, region and country column
									foreach($xml->channel as $entry){
										$yweather = $entry->children("http://xml.weather.yahoo.com/ns/rss/1.0");
										$arr1 = $yweather->location->attributes();  //Get all attributes from yweather:location and save them in $arr
										
										if($arr1['city']=="")
											$html_text .= "<td align='center'>" . "N/A" ."</td>";
										else
											$html_text .= "<td align='center'>" . $arr1['city'] . "</td>"; 
								
										if($arr1['region']=="")
											$html_text .= "<td align='center'>" . "N/A" ."</td>";
										else
											$html_text .= "<td align='center'>" . $arr1['region'] . "</td>";
										
										if($arr1['country']=="")
											$html_text .= "<td align='center'>" . "N/A" ."</td>";
										else
										$html_text .= "<td align='center'>" . $arr1['country'] . "</td>";
									}
							
									// Get the values for latitude and Longtitude column
									foreach($xml->channel->item as $entry){
										$geo = $entry->children("http://www.w3.org/2003/01/geo/wgs84_pos#");
										
										if($geo->lat=="")
											$html_text .= "<td align='center'>" . "N/A" ."</td>";
										else
											$html_text .= "<td align='center'>" . $geo->lat . "</td>";
										
										if($geo->long=="")
											$html_text .= "<td align='center'>" . "N/A" ."</td>";
										else
											$html_text .= "<td align='center'>" . $geo->long . "</td>";  
									}
							
									// Get the Link Details 
										if($xml->channel->link=="")
											$html_text .= "<td align='center'>" . "N/A" ."</td>";
										else
											$html_text .= "<td align='center'><A href='" . $xml->channel->link . "'target='_blank'>Details</A></td></tr>";
									
									$count++;
								}
							}
							print_r($html_text);
							echo $count . " result(s) for City " . $_POST["location"] ; 					
				}
				*/
			?>
	
