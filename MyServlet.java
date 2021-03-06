import java.io.*;

import javax.servlet.*;
import javax.servlet.http.*;

import java.net.MalformedURLException;
import java.net.SocketTimeoutException;
import java.net.URL;
import java.net.URLConnection;
import java.net.URLEncoder;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.transform.OutputKeys;
import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerConfigurationException;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;

//import org.jdom.input.SAXBuilder;
import org.w3c.dom.*;
import org.xml.sax.SAXException;
import org.xml.sax.SAXParseException;

/*
 public class MyServlet extends HttpServlet {

 public void doGet(HttpServletRequest request, HttpServletResponse response)
 throws IOException, ServletException
 {
 response.setContentType("text/html");
 PrintWriter out = response.getWriter();

 out.println("<h1>Hello World!</h1>");

 }
 }
 */
public class MyServlet extends HttpServlet {
	public void doGet(HttpServletRequest request, HttpServletResponse response)
			throws IOException, ServletException {

		response.setContentType("text/plain"); 
		String location = request.getParameter("location");
		String type = request.getParameter("type");
		String tempUnit = request.getParameter("tempUnit");
		PrintWriter out = response.getWriter();
		String jsonString = "";

		Pattern pat = Pattern.compile("[0-9][0-9][0-9][0-9][0-9]");
		Matcher match = pat.matcher(location);

		// ZIPcode search
		if (match.matches()) {
			// This is for testing:
			try {
				// Test php on cs-server
				//String urlString = "http://cs-server.usc.edu:23454/hw8_get_weather.php/?location=&type=zip&tempUnit=f";
				
				//String urlString = "http://elvislee.com/YahooWeather/?location="+location+"&type="+type+"&tempUnit="+tempUnit;
				//String urlString = "http://default-environment-rdjuuke7bs.elasticbeanstalk.com/?location="+location+"&type="+type+"&tempUnit="+tempUnit;
				String urlString = "http://default-environment-eavt4gsyqq.elasticbeanstalk.com/?location="+location+"&type="+type+"&tempUnit="+tempUnit;
				// Test php on AWS
				//	String urlString = "http://default-environment-9c6gdtmzy9.elasticbeanstalk.com/?location=90089&type=zip&tempUnit=f";
				URL url = new URL(urlString);
				URLConnection urlConnection = url.openConnection();
				urlConnection.setAllowUserInteraction(false);
				InputStream urlStream = url.openStream();
				
				System.setProperty("sun.net.client.defaultConnectTimeout", "10000");
				System.setProperty("sun.net.client.defaultReadTimeout", "10000");
				
			/*	 * BufferedReader br = new BufferedReader(new
				 * InputStreamReader(urlStream)); String str = null;
				 * while((str=br.readLine())!=null){ out.print(str); }
				 */
				 
				try {

					DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();
					DocumentBuilder parser = factory.newDocumentBuilder();
					Document doc = parser.parse(urlStream);

					doc.getDocumentElement().normalize();
					
/*					TransformerFactory tf = TransformerFactory.newInstance();
					Transformer transformer = tf.newTransformer();
					transformer.setOutputProperty(OutputKeys.OMIT_XML_DECLARATION, "yes");
					StringWriter writer = new StringWriter();
					transformer.transform(new DOMSource(doc), new StreamResult(writer));
					String output = writer.getBuffer().toString().replaceAll("\n|\r", "");
				*/
					jsonString += "{\"weather\":{\"forecast\":[";
					
					NodeList weatherNL = doc.getElementsByTagName("forecast");
					
					/*
					Element checkNode = (Element) weatherNL.item(0);

					
					 * if(checkNode.getAttribute("cover") == "Error"){
					 * jsonString += "{\"results\":{\n\"result\":[\n";
					 * jsonString += "{\"cover\":\"Error\"}"; jsonString +=
					 * "]} }";
					 * response.setContentType("text/plain; charset=UTF-8");
					 * PrintWriter outerr = response.getWriter();
					 * outerr.print(jsonString); outerr.flush(); }
					 */
				
					for (int i = 0; i < weatherNL.getLength(); i++) {
						Element forecastNode = (Element) weatherNL.item(i);
						jsonString += "{\"text\":\"";
						jsonString += forecastNode.getAttribute("text");
						jsonString += "\",  \"high\":";
						jsonString += forecastNode.getAttribute("high");
						jsonString += ",  \"day\":\"";
						jsonString += forecastNode.getAttribute("day");
						jsonString += "\",  \"low\":";
						jsonString += forecastNode.getAttribute("low");
						
						  if(i != weatherNL.getLength()-1){ 
							 jsonString += "},";
						  }
						  else{  
							  jsonString += "}]";
						  }
					}
					NodeList conNL = doc.getElementsByTagName("condition");
					Element conditionNode = (Element) conNL.item(0);
					jsonString += ",\"condition\":{\"text\":\"";
					jsonString += conditionNode.getAttribute("text");
					jsonString += "\",\"temp\":";
					jsonString += conditionNode.getAttribute("temp");
					jsonString += "}";
					
					NodeList locNL = doc.getElementsByTagName("location");
					Element locationNode = (Element) locNL.item(0);
					jsonString += ",\"location\":{\"region\":\"";
					jsonString += locationNode.getAttribute("region");
					jsonString += "\",\"country\":\"";
					jsonString += locationNode.getAttribute("country");
					jsonString += "\",\"city\":\"";
					jsonString += locationNode.getAttribute("city");
					jsonString += "\"}";
					
					NodeList linkNL = doc.getElementsByTagName("link");
					Element linkNode = (Element) linkNL.item(0);
					NodeList lNL = linkNode.getChildNodes();  //這是取tag中值的方法1, 較複雜
					jsonString += ",\"link\":\"";
					jsonString += ((Node)lNL.item(0)).getNodeValue().trim(); //先將tag中值轉成Node, 在取值
					jsonString += "\"";
					
					NodeList imgNL = doc.getElementsByTagName("img").item(0).getChildNodes();
					jsonString += ",\"img\":\"";
					jsonString += ((Node)imgNL.item(0)).getNodeValue().trim();
					jsonString += "\"";
					
					NodeList feedNL = doc.getElementsByTagName("feed").item(0).getChildNodes();
					jsonString += ",\"feed\":\"";
					jsonString += ((Node)feedNL.item(0)).getNodeValue().trim();
					jsonString += "\"";
					
					NodeList unitNL = doc.getElementsByTagName("units");
					Element unitNode = (Element) unitNL.item(0);
					jsonString += ",\"units\":{";
					jsonString += "\"temperature\":\"";
					jsonString += unitNode.getAttribute("temperature");
					jsonString += "\"}}}";
					
					response.setContentType("text/plain; charset=UTF-8");
					response.getWriter().write(jsonString);
						
				
					
				} catch (SocketTimeoutException e) {
					
					response.setContentType("text/plain; charset=UTF-8");
					response.getWriter().write("Error");
					
				} /*catch (TransformerConfigurationException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				} catch (TransformerException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}*/
			} catch (SAXException se) {
				se.printStackTrace();
				response.setContentType("text/plain; charset=UTF-8");
				response.getWriter().write("Error");
				
			} catch (ParserConfigurationException pce) {
				pce.printStackTrace();
				response.setContentType("text/plain; charset=UTF-8");
				response.getWriter().write("Error");
				
			}catch (IOException e){
			
				response.setContentType("text/plain; charset=UTF-8");
				response.getWriter().write("Error");
				
			}
		}
		else{
			try {
				
				//location.replaceAll("\\s+", "%20");
				location = URLEncoder.encode(location, "UTF-8");
			//	String urlString = "http://cs-server.usc.edu:23454/hw8_get_weather.php/?location=Los%20Angeles&type=city&tempUnit=f";
			//	String urlString = "http://default-environment-rdjuuke7bs.elasticbeanstalk.com/?location="+location+"&type="+type+"&tempUnit="+tempUnit;
			String urlString = "http://default-environment-eavt4gsyqq.elasticbeanstalk.com/?location="+location+"&type="+type+"&tempUnit="+tempUnit;
			//String urlString = "http://elvislee.com/?location="+location+"&type="+type+"&tempUnit="+tempUnit;

				URL url = new URL(urlString);
				URLConnection urlConnection = url.openConnection();
				urlConnection.setAllowUserInteraction(false);
				InputStream urlStream = url.openStream();
				
				System.setProperty("sun.net.client.defaultConnectTimeout", "10000");
				System.setProperty("sun.net.client.defaultReadTimeout", "10000");
				/*
				 * BufferedReader br = new BufferedReader(new
				 * InputStreamReader(urlStream)); String str = null;
				 * while((str=br.readLine())!=null){ out.print(str); }
				 */
				try {

					DocumentBuilderFactory factory = DocumentBuilderFactory.newInstance();
					DocumentBuilder parser = factory.newDocumentBuilder();
					Document doc = parser.parse(urlStream);

					doc.getDocumentElement().normalize();
		/*			
					TransformerFactory tf = TransformerFactory.newInstance();
					Transformer transformer = tf.newTransformer();
					transformer.setOutputProperty(OutputKeys.OMIT_XML_DECLARATION, "yes");
					StringWriter writer = new StringWriter();
					transformer.transform(new DOMSource(doc), new StreamResult(writer));
					String output = writer.getBuffer().toString().replaceAll("\n|\r", "");
	*/			
					jsonString += "{\"weather\":{\"forecast\":[";
					
					NodeList weatherNL = doc.getElementsByTagName("forecast");
					
					/*
					Element checkNode = (Element) weatherNL.item(0);

					
					 * if(checkNode.getAttribute("cover") == "Error"){
					 * jsonString += "{\"results\":{\n\"result\":[\n";
					 * jsonString += "{\"cover\":\"Error\"}"; jsonString +=
					 * "]} }";
					 * response.setContentType("text/plain; charset=UTF-8");
					 * PrintWriter outerr = response.getWriter();
					 * outerr.print(jsonString); outerr.flush(); }
					 */
					// else{

					// jsonString += resultNL.getLength();
					for (int i = 0; i < weatherNL.getLength(); i++) {
						Element forecastNode = (Element) weatherNL.item(i);
						jsonString += "{\"text\":\"";
						jsonString += forecastNode.getAttribute("text");
						jsonString += "\",  \"high\":";
						jsonString += forecastNode.getAttribute("high");
						jsonString += ",  \"day\":\"";
						jsonString += forecastNode.getAttribute("day");
						jsonString += "\",  \"low\":";
						jsonString += forecastNode.getAttribute("low");
						
						  if(i != weatherNL.getLength()-1){ 
							 jsonString += "},";
						  }
						  else{  
							  jsonString += "}]";
						  }
					}
					NodeList conNL = doc.getElementsByTagName("condition");
					Element conditionNode = (Element) conNL.item(0);
					jsonString += ",\"condition\":{\"text\":\"";
					jsonString += conditionNode.getAttribute("text");
					jsonString += "\",\"temp\":";
					jsonString += conditionNode.getAttribute("temp");
					jsonString += "}";
					
					NodeList locNL = doc.getElementsByTagName("location");
					Element locationNode = (Element) locNL.item(0);
					jsonString += ",\"location\":{\"region\":\"";
					jsonString += locationNode.getAttribute("region");
					jsonString += "\",\"country\":\"";
					jsonString += locationNode.getAttribute("country");
					jsonString += "\",\"city\":\"";
					jsonString += locationNode.getAttribute("city");
					jsonString += "\"}";
					
					NodeList linkNL = doc.getElementsByTagName("link");
					Element linkNode = (Element) linkNL.item(0);
					NodeList lNL = linkNode.getChildNodes();  //這是取tag中值的方法1, 較複雜
					jsonString += ",\"link\":\"";
					jsonString += ((Node)lNL.item(0)).getNodeValue().trim(); //先將tag中值轉成Node, 在取值
					jsonString += "\"";
					
					NodeList imgNL = doc.getElementsByTagName("img").item(0).getChildNodes();
					jsonString += ",\"img\":\"";
					jsonString += ((Node)imgNL.item(0)).getNodeValue().trim();
					jsonString += "\"";
					
					NodeList feedNL = doc.getElementsByTagName("feed").item(0).getChildNodes();
					jsonString += ",\"feed\":\"";
					jsonString += ((Node)feedNL.item(0)).getNodeValue().trim();
					jsonString += "\"";
					
					NodeList unitNL = doc.getElementsByTagName("units");
					Element unitNode = (Element) unitNL.item(0);
					jsonString += ",\"units\":{";
					jsonString += "\"temperature\":\"";
					jsonString += unitNode.getAttribute("temperature");
					jsonString += "\"}}}";
					
					response.setContentType("text/plain; charset=UTF-8");
					response.getWriter().write(jsonString);
					/*	
				PrintWriter outjson = response.getWriter();
					jsonString = output;
					outjson.print(jsonString);
					outjson.print("See anything?");
					outjson.flush(); */
					
				} catch (SocketTimeoutException e) {
					response.setContentType("text/plain; charset=UTF-8");
					response.getWriter().write("Error");
					
				}/* catch (TransformerConfigurationException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				} catch (TransformerException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}*/
			} catch (SAXException se) {
				se.printStackTrace();
				response.setContentType("text/plain; charset=UTF-8");
				response.getWriter().write("Error");
				
			} catch (ParserConfigurationException pce) {
				pce.printStackTrace();
				response.setContentType("text/plain; charset=UTF-8");
				response.getWriter().write("Error");
			} catch (IOException e){
			
				response.setContentType("text/plain; charset=UTF-8");
				response.getWriter().write("Error");
				
			}
			
		}
	}
}

// String urlString =
// "http://default-environment-rdjuuke7bs.elasticbeanstalk.com/

