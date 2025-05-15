<?php

      //ini_set('display_errors', 'On');


      require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
	  //require_once $_SERVER['DOCUMENT_ROOT']."/phpchartdir.php";
	  //include('/usr/lib/php5/extensions/phpchartdir.php');	
      include($CHART_ROOT."phpchartdir.php");
      
      $cUtility = new Utility();
      $cdb = new db_layer();
      $capp = new  application();
      //   require_valid_login();
      
      #The data for the pie chart
      $data = array($dolu, $bos);
      #The labels for the pie chart
      $labels = array(turkish2utf("Kullanilan"), turkish2utf("Bos Alan"));
      #Create a PieChart object of size 360 x 300 pixels


      $c = new PieChart(360, 280);
      #Set the center of the pie at (180, 140) and the radius to 100 pixels
      $c->setPieSize(145, 140, 100);
      #Add a title to the pie chart
      $c->addTitle("Disk Durumu");
      
      $labelStyleObj = $c->setLabelStyle("verdana.ttf", 7);
      
      #Draw the pie in 3D
      $c->set3D();
      #Set the pie data and the pie labels
      $c->setData($data, $labels);
      
      
      #Explode the 1st sector
      $sectorObj = $c->sector(0);
      ob_clean();
      #output the chart in PNG format
      header("Content-type: image/png");
      print($c->makeChart2(PNG));
      ?>
