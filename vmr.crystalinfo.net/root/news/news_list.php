<?
     require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
     $cUtility = new Utility();
     $cdb = new db_layer();
     require_valid_login();
     $conn = $cdb->getConnection();

     $NEWS_LIST_CNT = 20;

     //Determine howmany news items will be listed
     if (!isset($msg_cnt))
    $msg_cnt = $NEWS_LIST_CNT;
     else if ($msg_cnt>200)
       $msg_cnt = $NEWS_LIST_CNT;

   $sql_str = "SELECT NEWS.SIRA_NO, NEWS.USER_ID, NEWS.BASLIK, NEWS.DETAY, ".
               "NEWS.INSERT_DATE,NEWS.INSERT_TIME,NEWS.LEVEL,USERS.NAME,USERS.SURNAME ".  
                "FROM NEWS INNER JOIN USERS ON NEWS.USER_ID = USERS.USER_ID ".
              " ORDER BY INSERT_DATE DESC, INSERT_TIME DESC LIMIT ".$msg_cnt.";";
            
 if (isset($frm_search)){
   $sql_str="SELECT * FROM  NEWS ".
            "WHERE (DETAY LIKE '%".$frm_search ."%') OR (BASLIK LIKE '%".$frm_search ."%' ) ".
            "ORDER BY INSERT_DATE DESC, INSERT_TIME DESC;";
 }

 if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
    print_error($error_msg);
    exit;
 }

?>
<?
   cc_page_meta();
   page_header();
?>

<script language="JavaScript">
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
<script language="javascript">
function haber_ara() {
  if (document.haber_search.frm_search.value == "")
    alert ("Arama kriteri girmediniz");
  else
    document.haber_search.submit();
}

</script>

<? 
table_header("Haberler Listesi","90%"); 
?>
<img border="0" src="<?=IMAGE_ROOT ?>positive.gif" width="14" height="7">&nbsp;Olumlu Haberler 
<img border="0" src="<?=IMAGE_ROOT ?>negative.gif" width="14" height="7">&nbsp;Olumsuz Haberler 
<img border="0" src="<?=IMAGE_ROOT ?>neutral.gif" width="14" height="7">&nbsp;Nötr Haberler 
<br><br>
                  
              <table width="100%" border="0" cellspacing="1" cellpadding="1" align="center">
                <tr class="header1" > 
                  <td>No</td>
                  <td>Level</td>
                  <td>Başlık</td>
                  <td>Kaydeden</td>
                  <td>Tarih-Saat</td>
                  <td>İşlemler</td>
                </tr>
                <?
                    $i=1;
                    while ($row=mysqli_fetch_object($result)){
                  // Now find out if the news is good or bad
                   if ($row->LEVEL == 0)
                     $level_img = "neutral.gif";
                   else if ($row->LEVEL > 0)
                     $level_img = "positive.gif";
                   else 
                     $level_img = "negative.gif";
                           
                   $level_height = 10 * (abs($row->LEVEL)==0 ? 5 : abs($row->LEVEL));   
                        $tt= $row->DEPT_ID."#".$row->LEVEL."#".$row->BASLIK; 

                           ?>
                                <tr class="<?=bgc($i)?>">
                                        <td><?=$i?></td>
                                        <td><img src="/images/<?=$level_img?>" width="<?=$level_height?>" height="7"></td>
                                        <td><a  href="javascript:popup('yenipencere','news_win.php?frm_sira_no=<?=$row->SIRA_NO?>',550,450,'yes','yes','yes');"><font size=1 ><?=strlen($row->BASLIK)>50?substr($row->BASLIK,0,50)."...":$row->BASLIK;?></font></a></td>
                                        <td nowrap><?=$row->NAME." ".$row->SURNAME?></td>
                                        <td nowrap><?=DB2NORMAL($row->INSERT_DATE),$row->INSERT_TIME?></td>
                                        <td><a href="news_delete.php?frm_sira_no=<?=$row->SIRA_NO?>"><img src="/images/sil.gif" alt="Sil" border="0"></a><a href="/messages/yeni_mesaj_form.php?type=reply&from_who=<?=$row->ADI?>&subject=<?=$row->BASLIK?>"><img src="/images/cevapla.gif" alt="Cevapla" border="0"></a></td>
                                </tr>
                           <?
                            $i++;
                    }//}
?>
  </table>
  
<br>
<div align="center">
Son <input class="input1" type="text" name="list_mess_cnt" size="3">
<a href="javascript:go('news_list.php?msg_cnt=',list_mess_cnt.value);">adet haberi göster</a>
<form method="POST" name="haber_search" action="news_list.php?type=list">
   İçerik ile arama
   <input class="input1" type="text" name="frm_search" size="40">
   <img border="0" src="/images/ara.gif" onclick="haber_ara()">
</form>
<img border="0" src="/images/new_news.gif">
<a href="news_entry_form.php?">Haber Girişi</a>
</div>
<?
   table_footer();
   page_footer(0);
?>
