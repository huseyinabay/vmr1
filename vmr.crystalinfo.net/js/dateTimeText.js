var saatkacTT_div=document.createElement("div");
saatkacTT_div.id="saatkacTT";
saatkacTT_div.setAttribute("align","center");
saatkacTT_div.style.margin = "0px auto";
saatkacTT_div.innerHTML = '<a onclick="myFunction()"  id="saatkacTT_clock" target="_blank" style="text-decoration:none;color:'+saatkacTT_fontColor+';font:'+saatkacTT_fontType+'">Yükleniyor...</a>';
document.write(saatkacTT_div.innerHTML);
var saatkacTT_script = document.createElement("script");
saatkacTT_script.setAttribute('type','text/javascript');
saatkacTT_locationID=saatkacTT_region ? saatkacTT_region : saatkacTT_country;
saatkacTT_script.setAttribute('src','http://www.saatkac.com/tt.js.php?location='+saatkacTT_locationID);
document.getElementsByTagName("head")[0].appendChild(saatkacTT_script);
if(saatkacTT_script.readyState){
  saatkacTT_script.onreadystatechange=function(){
    if(saatkacTT_script.readyState=='loaded' || saatkacTT_script.readyState=='complete'){
      /*do something*/
      setInterval('updateClock()',1000);
    }
  }
}else saatkacTT_script.onload=function(){
  /*do something*/
  setInterval('updateClock()',1000);
}

var region_opt=document.getElementById('region');

function setStartTime(selectedIndex,is_region){
	timeZone=!is_region ? saatkacTT_TimeZones[selectedIndex] : saatkacTT_TimeZonesR[selectedIndex];
	saatkacTT_startTime=saatkacTT_startTime+(1000*3600*timeZone)-(saatkacTT_previous_TimeZones*3600*1000);
  saatkacTT_previous_TimeZones=timeZone;
}

function updateClock(){

 saatkacTT_startTime=saatkacTT_startTime + 1000;
  
  var currentTime=new Date(saatkacTT_startTime);
  var currentHours = currentTime.getHours();
  var currentMinutes = currentTime.getMinutes();
  var currentSeconds = currentTime.getSeconds();
  var currentMonth = currentTime.getMonth()+1;
  
  var currentYear = currentTime.getFullYear();
  var currentDay = currentTime.getDate();
  currentMonth=(currentMonth < 10 ? "0" : "") + currentMonth;
  currentDay=(currentDay < 10 ? "0" : "") + currentDay;
  currentSeconds=(currentSeconds < 10 ? "0" : "") + currentSeconds;
  currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
  currentHours = ( currentHours < 10 ? "0" : "" ) + currentHours;
  if(saatkacTT_iDate==1) currentTimeString = currentDay + "-" + currentMonth + "-" + currentYear + " " + currentHours + ":" + currentMinutes + ":" + currentSeconds;
  else if(saatkacTT_iDate==2) currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds;
  else currentTimeString = currentDay + "-" + currentMonth + "-" + currentYear;
  document.getElementById("saatkacTT_clock").firstChild.nodeValue = currentTimeString;
}
