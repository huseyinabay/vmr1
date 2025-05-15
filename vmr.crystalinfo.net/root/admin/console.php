<?  //INCLUDES
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   require_valid_login();
    
   $cUtility = new Utility();
   $cdb = new db_layer();
   //Site Admin veya Admin Hakkı yokda bu tanımı yapamamalı
   if (!right_get("SITE_ADMIN") && !right_get("ADMIN")){
        print_error("Burayı Görme Hakkınız Yok");
    exit;
   }
   $conn = $cdb->getConnection();
   if (!right_get("SITE_ADMIN"))
       $SITE_ID = $_SESSION['site_id'];
   if (right_get("SITE_ADMIN") && ($SITE_ID=="" || $SITE_ID=="-1"))
       $SITE_ID = $_SESSION['site_id'];

?>
<link rel="stylesheet" href="/crystal.css" TYPE="text/css">
<?
   cc_page_meta();
   echo "<center>";
   page_header();
?>

<style>
   textarea{
        scrollbar-shadow-color     : #000000; 
        scrollbar-highlight-color  : #000000; 
        scrollbar-3dlight-color    : #000000; 
        scrollbar-darkshadow-color : #000000; 
        scrollbar-track-color      : #000000; 
        scrollbar-arrow-color      : #000000; 
        scrollbar-face-color      : #000000; 
        border-style: ridge;
        border: none;
        clear: none;
   }
</style>
<table border="0" width="600" height="400" ALIGN="left" >
  <tr>
      <td align="center" colspan="2">
      <b><font color="#00FF00">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;    
        <textarea name="news2" cols=60 rows=6 wrap=virtual style="background-color: #000000; color: #008000; font-weight: bold" ></textarea></font>
       </b>
        </td>
  </tr>
</table>
<?page_footer(0);?>
<script  LANGUAGE="JavaScript1.2">
var newsText = new Array();
function get_console_info(Alan1, Alan2) {

   //to manage the xml document
   var objDOM = new ActiveXObject("Microsoft.XMLDOM");
   //to establish a http connection between the server
   //and the client, thereby sending and receiving
   //message to and from the server
   //without submitting the page
  // var xmlh = new ActiveXObject("Microsoft.XMLHTTP");
   //to identify whether the server is contacted
   //by clicking the send button OR
   //the routine timer event
    //alert(sql);
   var id = document.all('ID').value; 

   xmlh.open("POST","/admin/konsol_cgi.php?ID=" + id + "&SITE_ID=" + document.all('SITE_ID').value, false);

  //  xmlh.SetRequestHeader("Man", "POST http://www.sat.com.tr HTTP/1.1")
   xmlh.SetRequestHeader("Content-Type", "text/xml")
   xmlh.send();
   
   //alert(xmlh.ResponseText);

   objDOM.loadXML(xmlh.ResponseText);

   //to display the incoming user list
   
      var nodes,strHtml;
      var selList = new Array();
      var newList = new Array();
      var newList_id = new Array();

      nodes = objDOM.getElementsByTagName(Alan2);
      nodes_id = objDOM.getElementsByTagName(Alan1);

      //getting the current user list
      for(i=0;i<nodes.length;i++){
          newList[i] = nodes.item(i).text;
      }
      
      for(i=0;i<nodes_id.length;i++){
          newList_id[i] = nodes_id.item(i).text;
      }
      if(newList_id.length<3) return;

      document.all('ID').value = newList_id[nodes_id.length-1];
    var k, ex_list ;
      
      //document.all("raw_data").innerHTML = '';
      // Fill combo with names
      newsText = new Array(); 
      for (var i=newList_id.length-1; i >=0 ; i--) {
         m_innerhtml ="";

    //raw_data.insertAdjacentText("beforeEnd",newList[i]);
    //raw_data.insertAdjacentText("beforeEnd","\n\n");
         newsText[i] =  newList[i];
      }
      document.all('news2').value = "";  
      cnews = 0;cchar=0;
      doNews();   
}


//Visit http://javascriptkit.com for this script

var ttloop = 0;    // Repeat forever? (1 = True; 0 = False)
var tspeed = 10;   // Typing speed in milliseconds (larger number = slower)
var tdelay = 10; // Time delay between newsTexts in milliseconds

// ------------- NO EDITING AFTER THIS LINE ------------- \\
var dwAText, eline=0, cchar=0, mxText;

function doNews() {
      mxText = newsText.length - 1;
      dwAText = newsText[cnews];
      setTimeout("addChar()",10)
}
function addNews() {
      cnews += 1;
      if (cnews <= mxText) {
            dwAText = newsText[cnews];
            if (dwAText.length != 0) {
                  eline = 0;
                  setTimeout("addChar()",tspeed)
            }else{
                  setTimeout("addNews()",tdelay)
            }
      }
}
function addChar() {
      if (eline!=1) {
            if (cchar != dwAText.length) {
                  nmttxt = dwAText.charAt(cchar);
                  document.all('news2').value += nmttxt;
                  cchar += 1;
            } else {
                  document.all('news2').value += "\n\n";
                  cchar = 0;
                  eline = 1;
            }
            if (mxText==cnews && eline!=0 && ttloop!=0) {
                  cnews = 0;
                  setTimeout("addNews()",tdelay);
            } else setTimeout("addChar()",tspeed);
      } else {
            setTimeout("addNews()",tdelay)
      }
}

function reloadme(){
  document.site.action = "konsol.php?SITE_ID=" + document.all("SITE_ID").value;
  document.site.submit();
}
window.setInterval("get_console_info('ID', 'DATA')",5000)
</script>


