<?php

	function send_request($location, $radius, $keyword, $category) {
		$url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?';
		$fields = array(
			'location' => $location,
			'radius' => $radius,
			'type' => $category,
			'keyword' => $keyword,
			'key' => 'xxxxxxxxxxxxxxx'
		);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$url . http_build_query($fields));
		curl_setopt($ch, CURLOPT_TIMEOUT,10);
		$result=curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if($httpcode == 200){
			return json_decode($result);
		}else{
			echo('Something went wrong. Error code:'.$httpcode);
		}
	}

	function geolocate($lat,$lon,$radius,$keyword_place_1,$category_place_1,$keyword_place_2,$category_place_2,$distance) {
		$match_count = 0;
		$place_1_data = send_request($lat.','.$lon,$radius,$keyword_place_1,$category_place_1);
		//echo($place_1_data->status);
		if ($place_1_data->status == 'OK') {
			
			$resultmessage = '<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" id="myLargeModalLabel" aria-labelledby="myLargeModalLabel" aria-hidden="true">\r\n';
			$resultmessage = $resultmessage . '  <div class="modal-dialog modal-lg">';
			$resultmessage = $resultmessage . '	<div class="modal-content">';
			$resultmessage = $resultmessage . '	<div class="modal-header">';
			$resultmessage = $resultmessage . '	<h5 class="modal-title">Search results</h5></div>';
			$resultmessage = $resultmessage . '	<div class="modal-body">';
					
			foreach($place_1_data->results as $result) {
				$temp_name = $result->name;
				$temp_lat = $result->geometry->location->lat;
				$temp_lng = $result->geometry->location->lng;
				$temp_location = $temp_lat . ',' . $temp_lng;
				
				$place_2_data = send_request($temp_lat . ',' . $temp_lng,$distance,$keyword_place_2,$category_place_2);
				if($place_2_data->status != 'ZERO_RESULTS') {
					$resultmessage = $resultmessage . 'Found match at ' . $temp_name . ' on <a href="https://maps.google.com/maps?q=' . $temp_location . '" target="_blank">' . $temp_location . '</a>' . ' nearby ' . $place_2_data->results[0]->name . ' on <a href="https://maps.google.com/maps?q=' . $place_2_data->results[0]->geometry->location->lat . ',' . $place_2_data->results[0]->geometry->location->lng . '" target="_blank">' . $place_2_data->results[0]->geometry->location->lat . ',' . $place_2_data->results[0]->geometry->location->lng . '</a><br>';
					$match_count++;
				}
			}
			$resultmessage = $resultmessage . '	</div></div></div></div>';
			
		} else {
			//echo('ERROROROROROROROOR');
			$resultmessage = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Holy guacamole!</strong> Return status from google is: ' . $place_1_data->status . '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
		}
		//echo($match_count);
		return $resultmessage;
	}
	

	if(isset($_POST['latitude']) && isset($_POST['longitude']) && isset($_POST['radius']) && isset($_POST['keyword_place_1']) && isset($_POST['keyword_place_2']) && isset($_POST['distance']))
	{
		$resultmessage = geolocate($_POST['latitude'],$_POST['longitude'],$_POST['radius'],$_POST['keyword_place_1'],$_POST['category_place_1'],$_POST['keyword_place_2'],$_POST['category_place_2'],$_POST['distance']);
	}
	
?>


<!DOCTYPE html>

<html lang="en" dir="ltr">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <head>
    <meta charset="utf-8">
    <title>GeolocateThis!</title>
    <link rel="icon" type="image/png" href="favicon.png" sizes="32x32" />
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="geolocate.css"/>
	<script>
		$(function() {
		$("#myLargeModalLabel").modal();//if you want you can have a timeout to hide the window after x seconds
		});
	</script>
  </head>
  <body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="#">
        <img src="/static/images/googlemaps.png" width="40" height="40" class="d-inline-block align-middle" alt="">
          <span class="align-middle">
          GeoLocateThis!
          </span>
      </a>
      <span class="navbar-text d-none d-md-block">
         geolocating tool for Google Maps API. Idea from musafir.py. Ported to PHP
      </span>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" id="twitter" href="https://twitter.com/johnwulp" target="_blank">
              <img src="icon_t.png" width="30" height="30" class="d-inline-block align-middle" alt="">
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="github" href="https://github.com/Johnwulp" target="_blank">
              <img src="github.png" width="30" height="30" class="d-inline-block align-middle" alt="">
            </a>
          </li>
        </ul>
      </div>
    </nav>


    <div class="container">
        <?php echo $resultmessage; ?>
	<div class="row">
		<div class="col-lg-4">

    <div class="container" id="form_container">
      <h3 id="h3_left">
        Google Maps Search
      </h3>

      <form method="post" class="form-horizontal" onsubmit="loading();">
        <div class="form-group">
			<tr><th><label for="id_latitude">Latitude:</label></th><td><input type="text" name="latitude" placeholder="[Decimal degrees]" class="form-control" required id="id_latitude"></td></tr>
			<tr><th><label for="id_longitude">Longitude:</label></th><td><input type="text" name="longitude" placeholder="[Decimal degrees]" class="form-control" required id="id_longitude"></td></tr>
			<tr><th><label for="id_radius">Radius:</label></th><td><input type="text" name="radius" placeholder="[in meters (max. 50,000)]" class="form-control" required id="id_radius"></td></tr>
			<tr><th><label for="id_keyword_place_1">Keyword for place 1:</label></th><td><input type="text" name="keyword_place_1" class="form-control" required id="id_keyword_place_1"></td></tr>
			<tr><th><label for="id_category_place_1">Category for place 1 [optional]:</label></th><td><select name="category_place_1" class="form-control" id="id_category_place_1">
			<option value="None">No category</option>
			<option value="accounting">accounting</option>
			<option value="airport">airport</option>
			<option value="amusement_park">amusement park</option>
			<option value="aquarium">aquarium</option>
			<option value="art_gallery">art gallery</option>
			<option value="atm">atm</option>
			<option value="bakery">bakery</option>
			<option value="bank">bank</option>
			<option value="bar">bar</option>
			<option value="beauty_salon">beauty salon</option>
			<option value="bicycle_store">bicycle store</option>
			<option value="book_store">book store</option>
			<option value="bowling_alley">bowling alley</option>
			<option value="bus_station">bus station</option>
			<option value="cafe">cafe</option>
			<option value="campground">campground</option>
			<option value="car_dealer">car dealer</option>
			<option value="car_rental">car rental</option>
			<option value="car_repair">car repair</option>
			<option value="car_wash">car wash</option>
			<option value="casino">casino</option>
			<option value="cemetery">cemetery</option>
			<option value="church">church</option>
			<option value="city_hall">city hall</option>
			<option value="clothing_store">clothing store</option>
			<option value="convenience_store">convenience store</option>
			<option value="courthouse">courthouse</option>
			<option value="dentist">dentist</option>
			<option value="department_store">department store</option>
			<option value="doctor">doctor</option>
			<option value="electrician">electrician</option>
			<option value="electronics_store">electronics store</option>
			<option value="embassy">embassy</option>
			<option value="fire_station">fire station</option>
			<option value="florist">florist</option>
			<option value="funeral_home">funeral home</option>
			<option value="furniture_store">furniture store</option>
			<option value="gas_station">gas station</option>
			<option value="gym">gym</option>
			<option value="hair_care">hair care</option>
			<option value="hardware_store">hardware store</option>
			<option value="hindu_temple">hindu temple</option>
			<option value="home_goods_store">home goods store</option>
			<option value="hospital">hospital</option>
			<option value="insurance_agency">insurance agency</option>
			<option value="jewelry_store">jewelry store</option>
			<option value="laundry">laundry</option>
			<option value="lawyer">lawyer</option>
			<option value="library">library</option>
			<option value="liquor_store">liquor store</option>
			<option value="local_government_office">local government office</option>
			<option value="locksmith">locksmith</option>
			<option value="lodging">lodging</option>
			<option value="meal_delivery">meal delivery</option>
			<option value="meal_takeaway">meal takeaway</option>
			<option value="mosque">mosque</option>
			<option value="movie_rental">movie rental</option>
			<option value="movie_theater">movie theater</option>
			<option value="moving_company">moving company</option>
			<option value="museum">museum</option>
			<option value="night_club">night club</option>
			<option value="painter">painter</option>
			<option value="park">park</option>
			<option value="parking">parking</option>
			<option value="pet_store">pet store</option>
			<option value="pharmacy">pharmacy</option>
			<option value="physiotherapist">physiotherapist</option>
			<option value="plumber">plumber</option>
			<option value="police">police</option>
			<option value="post_office">post office</option>
			<option value="real_estate_agency">real estate agency</option>
			<option value="restaurant">restaurant</option>
			<option value="roofing_contractor">roofing contractor</option>
			<option value="rv_park">rv park</option>
			<option value="school">school</option>
			<option value="shoe_store">shoe store</option>
			<option value="shopping_mall">shopping mall</option>
			<option value="spa">spa</option>
			<option value="stadium">stadium</option>
			<option value="storage">storage</option>
			<option value="store">store</option>
			<option value="subway_station">subway station</option>
			<option value="supermarket">supermarket</option>
			<option value="synagogue">synagogue</option>
			<option value="taxi_stand">taxi stand</option>
			<option value="train_station">train station</option>
			<option value="transit_station">transit station</option>
			<option value="travel_agency">travel agency</option>
			<option value="veterinary_care">veterinary care</option>
			<option value="zoo">zoo</option>
			</select></td></tr>
		<tr><th><label for="id_keyword_place_2">Keyword for place 2:</label></th><td><input type="text" name="keyword_place_2" class="form-control" required id="id_keyword_place_2"></td></tr>
		<tr><th><label for="id_category_place_2">Category for place 2 [optional]:</label></th><td><select name="category_place_2" class="form-control" id="id_category_place_2">
  			<option value="None">No category</option>
			<option value="accounting">accounting</option>
			<option value="airport">airport</option>
			<option value="amusement_park">amusement park</option>
			<option value="aquarium">aquarium</option>
			<option value="art_gallery">art gallery</option>
			<option value="atm">atm</option>
			<option value="bakery">bakery</option>
			<option value="bank">bank</option>
			<option value="bar">bar</option>
			<option value="beauty_salon">beauty salon</option>
			<option value="bicycle_store">bicycle store</option>
			<option value="book_store">book store</option>
			<option value="bowling_alley">bowling alley</option>
			<option value="bus_station">bus station</option>
			<option value="cafe">cafe</option>
			<option value="campground">campground</option>
			<option value="car_dealer">car dealer</option>
			<option value="car_rental">car rental</option>
			<option value="car_repair">car repair</option>
			<option value="car_wash">car wash</option>
			<option value="casino">casino</option>
			<option value="cemetery">cemetery</option>
			<option value="church">church</option>
			<option value="city_hall">city hall</option>
			<option value="clothing_store">clothing store</option>
			<option value="convenience_store">convenience store</option>
			<option value="courthouse">courthouse</option>
			<option value="dentist">dentist</option>
			<option value="department_store">department store</option>
			<option value="doctor">doctor</option>
			<option value="electrician">electrician</option>
			<option value="electronics_store">electronics store</option>
			<option value="embassy">embassy</option>
			<option value="fire_station">fire station</option>
			<option value="florist">florist</option>
			<option value="funeral_home">funeral home</option>
			<option value="furniture_store">furniture store</option>
			<option value="gas_station">gas station</option>
			<option value="gym">gym</option>
			<option value="hair_care">hair care</option>
			<option value="hardware_store">hardware store</option>
			<option value="hindu_temple">hindu temple</option>
			<option value="home_goods_store">home goods store</option>
			<option value="hospital">hospital</option>
			<option value="insurance_agency">insurance agency</option>
			<option value="jewelry_store">jewelry store</option>
			<option value="laundry">laundry</option>
			<option value="lawyer">lawyer</option>
			<option value="library">library</option>
			<option value="liquor_store">liquor store</option>
			<option value="local_government_office">local government office</option>
			<option value="locksmith">locksmith</option>
			<option value="lodging">lodging</option>
			<option value="meal_delivery">meal delivery</option>
			<option value="meal_takeaway">meal takeaway</option>
			<option value="mosque">mosque</option>
			<option value="movie_rental">movie rental</option>
			<option value="movie_theater">movie theater</option>
			<option value="moving_company">moving company</option>
			<option value="museum">museum</option>
			<option value="night_club">night club</option>
			<option value="painter">painter</option>
			<option value="park">park</option>
			<option value="parking">parking</option>
			<option value="pet_store">pet store</option>
			<option value="pharmacy">pharmacy</option>
			<option value="physiotherapist">physiotherapist</option>
			<option value="plumber">plumber</option>
			<option value="police">police</option>
			<option value="post_office">post office</option>
			<option value="real_estate_agency">real estate agency</option>
			<option value="restaurant">restaurant</option>
			<option value="roofing_contractor">roofing contractor</option>
			<option value="rv_park">rv park</option>
			<option value="school">school</option>
			<option value="shoe_store">shoe store</option>
			<option value="shopping_mall">shopping mall</option>
			<option value="spa">spa</option>
			<option value="stadium">stadium</option>
			<option value="storage">storage</option>
			<option value="store">store</option>
			<option value="subway_station">subway station</option>
			<option value="supermarket">supermarket</option>
			<option value="synagogue">synagogue</option>
			<option value="taxi_stand">taxi stand</option>
			<option value="train_station">train station</option>
			<option value="transit_station">transit station</option>
			<option value="travel_agency">travel agency</option>
			<option value="veterinary_care">veterinary care</option>
			<option value="zoo">zoo</option>
			</select></td></tr>
			<tr><th><label for="id_distance">Distance between places:</label></th><td><input type="text" name="distance" placeholder="[in meters]" class="form-control" required id="id_distance"><input type="hidden" name="botcatcher" id="id_botcatcher"></td></tr>
          <input type="hidden" name="csrfmiddlewaretoken" value="dQd2ApRJc1XhNoiXlFB9COPLooi7U03CmXRjopT7yBGwMDExb3mbckL5kl9ek9MQ">
        </div>
        <input type="submit" id="submit" class="btn btn-primary" value="SEARCH">
      </form>

    </div>
  </div>

  <div class="col-lg-8">
    <div class="container" id='list_container'>
      <h3 id="h3_right">How does it work?</h3>

      <blockquote class="blockquote">
        <p class="mb-0 font-italic">"Why can't I just ask google maps to show me all restaurants in certain area with approximate distance of 20 metres from newspaper kiosk...?"</p>
        <footer class="blockquote-footer"><cite title="Source Title">me at geolocating workshop</cite></footer>
      </blockquote>


        <ul class="list">
          <li>Now I can! This app allows to query Google Maps API for 2 specific places within given distance from each other in a specified area.</li>
        </ul>

        <h4>Parameters:</h4>
        <ul class="list">
          <li><b>Latitude and Logtitude:</b> points to the center of the search area.</li>
          <li><b>Radius:</b> defines radius of the search area from given above coordinates.</li>
          <li><b>Keyword:</b> a term to be matched against all content that Google has indexed for this place, including but not limited to name, type, and address, as well as customer reviews and other third-party content. For best results use local language.</li>
          <li><b>Category [optional]:</b> restricts the results to places matching the specified type.</li>
          <li><b>Distance:</b> defines approximate (+/-20m) distance between two given places. Tip: When in doubt, it's better to uderestimate than overestimate.</li>
        </ul>

        <h4>What then?</h4>
        <ul class="list">
          <li>Press SEARCH and wait patiently. With large radius (>10,000) and popular landmarks, it can take a while to generate result.</li>
          <li>If found, results are displayed on a map and in a table at the bottom of the page.</li>
        </ul>

    </div>
  </div>

</div>

		<div id="loader">
		  <div class="lds-ring">
			<div>
			</div>
			<div>
			</div>
			<div>
			</div>
			<div>
			</div>
		  </div>
		  <p>Geolocating... please wait...</p>
		</div>

    </div>

  <script type="text/javascript">// <![CDATA[
          function loading(){
              $("#loader").show();
          }// ]]>
  </script>
  

  </body>
</html>
