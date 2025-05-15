<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   $conn = $cdb->getConnection();
   
   $MESSAGE_LIST_CNT = 20; // # of messages to list to keep the list reasonably long
   
 // MAKE THE REQUESTED MESSAGE "READ"
 if ($action == "read"){
   $sql_str = "UPDATE MESSAGES SET READ_IT = 'Y' WHERE SIRA_NO = ".$frm_sira_no.";";
   if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
        print_error($error_msg);
        exit;
   }
  }  

 // Determine if "READ" or "UNREAD" messages will be listred  
 if ($which == "read")
   $read_unread = "Y";
 else
   $read_unread = "N";
 
 //Determine howmany messages will be listed
 if (!isset($msg_cnt))
    $msg_cnt = $MESSAGE_LIST_CNT;
 else if ($msg_cnt>200){
    $msg_cnt = $MESSAGE_LIST_CNT;
}

 $user_id   = $_SESSION["user_id"];
 
 $sql_str = "SELECT SIRA_NO,KONU,A2.NAME FROM_ADI, A2.SURNAME FROM_SOYADI,  A1.NAME TO_ADI, A1.SURNAME TO_SOYADI, INSERT_DATE, INSERT_TIME, READ_IT ".
            "FROM (MESSAGES LEFT JOIN USERS A1 ON MESSAGES.TRG_USER_ID = A1.USER_ID) ".
            "LEFT JOIN USERS A2 ON MESSAGES.INS_USER_ID = A2.USER_ID ".
            "WHERE ".
            "MESSAGES.TRG_USER_ID = ".$user_id.  
            " AND READ_IT = '".$read_unread."'".
            " ORDER BY INSERT_DATE DESC, INSERT_TIME DESC LIMIT ".$msg_cnt.";";
 
  if (!($cdb->execute_sql($sql_str,$mesaj_result,$error_msg))){
        print_error($error_msg);
        exit;
  }

  // Everything seems fine
  
?>
<?
   cc_page_meta();
   page_header();
?>
<script language="javascript">
function submit_form() {
    document.msg_list.submit();
}
</script>

<script language="VBScript">
   function check_all() 
   dim elm 
   dim msg_cnt,k
     msg_cnt = document.all("msg_cnt").value
    for k=1 To msg_cnt
      elm = "del_msg_" & trim(cstr(k))
      if (document.all("chk_all").checked)then
         document.all(trim(cstr(elm))).checked =true
      else 
         document.all(trim(cstr(elm))).checked =false
      End If
    Next
    
  End Function
</script>
<script language="javascript">
function popup ( name, url_which, width_which, height_which, resizable_which, scrollbars_which, titlebar_which ){
  var yenipencere = null;
  yenipencere=window.open('', name ,'width=' + width_which + ',height=' + height_which + ',status=no,toolbar=no,menubar=no,directories=no,location=no,resizable=' + resizable_which + ',scrollbars=' +  scrollbars_which + ',titlebar=' + titlebar_which + ',alwaysRaised=yes,screenX=0,screenY=0,left=250,top=150');
  if (yenipencere != null) {
        if (yenipencere.opener == null){
           yenipencere.opener = self;
        }  
      yenipencere.location.href=url_which;
      yenipencere.focus();
  }
}
function go (url,prm1){
    if (prm1==""){
      location.href = url+"0";
    }else{
      location.href = url+prm1;
    }  
}
</script>

<? 
table_header("Mesaj Listesi","90%"); 
?>
<div align="center">
<table border="0">
  <tr>
    <td><img border="0" src="<?=IMAGE_ROOT?>read_mess.gif"></td>
    <td><a href="mesaj_src.php?which=read">Okunmuş Mesajları Göster</a></td>
    <td><img border="0" src="<?=IMAGE_ROOT?>unread_mess.gif"></td>
    <td><a href="mesaj_src.php">Okunmamış Mesajları Göster</a></td>
  </tr>
</table>
</div>
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
    <form name="msg_list" method="POST" action="mesaj_db.php?act=del">
    <tr class="bgc1">
        <td>No</td>
        <td>Konu</td>
        <td>Kimden</td>
        <td>Kime</td>
        <td>Tarih Saat</td>
        <td><input type="checkbox" name="chk_all" onclick="check_all()" value="">Sil</td>
        <td>Okudum</td>
        <td>Cevapla</td>        
        <td></td>
    </tr>
<? 
     $i=1;
     while ($row = mysqli_fetch_object($mesaj_result)){
         $to =  $row->TO_ADI." ".$row->TO_SOYADI;   
                      
         $del_chk_box = sprintf("<input type=\"checkbox\" name=\"del_msg_%s\" value=\"1\">",$i);    
         echo "<tr class=\"".bgc($i)."\">".CR;
         echo "  <td>$i</td> ".CR;
         echo "  <td><a href=\"javascript:popup('yenipencere','mesaj_win.php?frm_sira_no=$row->SIRA_NO',550,450,'yes','yes','yes');\">".substr($row->KONU,0,20)."</a></td>".CR;
     echo "  <td>".substr($row->FROM_ADI." ".$row->FROM_SOYADI,0,15)."</td>".CR;
         echo "  <td>".substr($to,0,15)."</td>".CR;
         echo "  <td>".db2normal($row->INSERT_DATE)." ".$row->INSERT_TIME."</td>".CR;
         echo "  <td>$del_chk_box</td>".CR;
         echo "  <td><a href=\"mesaj_src.php?which=".($read_unread=="Y" ? "read" : "unread")."&action=read&frm_sira_no=$row->SIRA_NO\">Okudum</a></td>".CR;                      
         echo "  <td><a href=\"mesaj.php?type=reply&subject=$row->KONU&from_who=$row->FROM_ADI\">Cevapla</a></td>".CR;
         echo "</tr>".CR;
          
         printf("<input type=\"hidden\" name=\"map_del_msg_%s\" value=\"%s\">".CR,$i,$row->SIRA_NO);
         $i++;
       }
       printf("<input type=\"hidden\" name=\"msg_cnt\" value=\"%s\">".CR,$i-1);     
?>
</form>
</table>
<br>

<div align="center">Son <input class="input1" type="text" name="list_mess_cnt" size="3"><a href="javascript:go('mesaj_src.php?msg_cnt=',list_mess_cnt.value);"> adet mesajı göster</a></div><br>

<div align="center">
<table border="0">
  <tr>
    <td></td>
    <td><a href="mesaj.php"><img src="/images/mesaj_girisi.gif" border="0"></a></td>
    <td></td>
    <td><a href="javascript:submit_form()"><img src="/images/isaretlileri_sil.gif" border="0"></a></td>
  </tr>
</table>
</div>
    
<?
   table_footer();
   page_footer(0);
?>
