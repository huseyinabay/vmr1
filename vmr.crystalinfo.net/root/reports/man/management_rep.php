<?
    require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
//	include("/usr/local/httpd/chartdir/phpchartdir.php");

	// ini_set('display_errors', 'On');
     //error_reporting(E_ALL);	

    $cUtility = new Utility();
    $cdb = new db_layer();
    require_valid_login();
    session_cache_limiter('nocache');     

    $conn = $cdb->getConnection();
    cc_page_meta(0);
	
	/*
    if (right_get("SITE_ADMIN")){
        //Site admin hakkı varsa herşeyi görebilir.  
    //Site id gelmemişse kişinin bulunduğu site raporu alınır.
      if(!$SITE_ID){$SITE_ID = $_SESSION['site_id'];}
    }elseif(right_get("ADMIN") || right_get("ALL_REPORT")){
    // Admin vaye ALL_REPORT hakkı varsa kendi sitesindeki herşeyi görebilir.
      $SITE_ID = $_SESSION['site_id'];
    }else{
      print_error("Bu sayfayı Görme Hakkınız Yok!!!");
      exit;
    } 
*/


    //require_valid_login();

    $usr_crt = "";
    if (right_get("SITE_ADMIN")){
      //Site admin hakkı varsa herşeyi görebilir.  
      //Site id gelmemişse kişinin bulunduğu site raporu alınır.
      if(!$SITE_ID){$SITE_ID = $_SESSION['site_id'];}
    }elseif(right_get("ADMIN") || right_get("ALL_REPORT")){
      // Admin vaye ALL_REPORT hakkı varsa kendi sitesindeki herşeyi görebilir.
      $SITE_ID = $_SESSION['site_id'];
    }elseif(got_dept_right($_SESSION["user_id"])==1){
      //Bir departmanın raporunu görebiliyorsa kendi sitesindekileri girebilir.
      $SITE_ID = $_SESSION['site_id'];
      //echo $dept_crt = get_depts_crt($_SESSION["user_id"]);
      $usr_crt = get_users_crt($_SESSION["user_id"], 1, $SITE_ID);
      $alert = "Bu rapor sadece sizin yetkinizde olan departmanlara ait dahililerin bilgilerini içerir.";
    }else{
      print_error("Bu sayfayı Görme Hakkınız Yok!!!");
      exit;
    }

//echo $usr_crt;


    ob_start();

	
  function time_maker($time_stamp){
        $gun = floor(floor($time_stamp/3600)/24);
        $saat = floor(($time_stamp-($gun*3600*24))/3600);
        $dakk = floor(($time_stamp-(($gun*3600*24)+($saat*3600)))/60);
        $san = floor(($time_stamp-(($gun*3600*24)+($saat*3600)+($dakk*60))));
        if($gun > 0)  $mk .= $gun." gün ";
        if($saat > 0)  $mk .= $saat." saat ";
        if($dakk > 0)  $mk .= $dakk." dk ";
        if($san > 0)  $mk .= $san." sn";
        if(strlen($mk)>0){  
          return $mk;
        }else{ 
          return strlen($mk); 
        }
      
  }
  
    $sql_str1="SELECT SITE_NAME, MAX_ACCE_DURATION FROM SITES WHERE SITE_ID = ".$SITE_ID; 
    if (!($cdb->execute_sql($sql_str1,$result1,$error_msg))){
        print_error($error_msg);
        exit;
    }
    if (mysqli_num_rows($result1)>0){
    $row1 = mysqli_fetch_object($result1);
        $company = $row1->SITE_NAME;
        $max_acc_dur =  ($row1->MAX_ACCE_DURATION)*60;
    }else{
    print_error("Site paramatreleri bulunamadı.");
    exit;
  }

    $local_country_code = get_country_code($SITE_ID);
  
    $kriter = "";   
    //Temel kriterler. Verinini hızlı gelmesi için başa konuldu.
    $kriter .= $cdb->field_query($kriter,   "SITE_ID"     ,  "=",  "$SITE_ID"); //Bu mutlaka olmalı.İlgili siteyi belirliyor.
    $kriter .= $cdb->field_query($kriter,   "ERR_CODE"     ,  "=",  "0"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.      
    $kriter .= $cdb->field_query($kriter,   "CALL_TYPE"     ,  "=",  "1"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
    $kriter .= $cdb->field_query($kriter,   "DURATION"     ,  "<",  "$max_acc_dur"); //Bu mutlaka olmalı.Dış arama olduğunu gösteriyor.
    $kriter .= $cdb->field_query($kriter,   "CDR_MAIN_DATA.ORIG_DN"     ,  "<>",  "''"); //Bu mutlaka olmalı.Hatasız kayıt olduğunu gösteriyor.
  

  //**Bunlar birlikte olmalı ve bu sırada olmalı.
  add_time_crt();//Zaman kriteri 
  $CDR_MAIN_DATA = getTableName($t0,$t1);
  if(!checkTable($CDR_MAIN_DATA)) $CDR_MAIN_DATA = "CDR_MAIN_DATA";
  //**
 // echo $t0;
// echo $CDR_MAIN_DATA; die;
 
 /*
 // tablo kontrolü halil
 $val = mysqli_query($conn,"select 1 from ".$CDR_MAIN_DATA."  LIMIT 1");

if($val == FALSE)
{
   $CDR_MAIN_DATA="CDR_MAIN_DATA";
}
else
{
    $CDR_MAIN_DATA = getTableName($t0,$t1);
}
*/
//  echo $CDR_MAIN_DATA; die;
	?>
<center>
<br><br>

<script language="JavaScript">
   function CheckEmail (strng) {
       var error="";
       var emailFilter=/^.+@.+\..{2,3}$/;
       if (!(emailFilter.test(strng))) { 
          alert("Lütfen geçerli bir e-mail adresi giriniz.\n");
          return 0;
       }
       else {
          var illegalChars= /[\(\)\<\>\,\;\:\\\"\[\]]/
          if (strng.match(illegalChars)) {
                alert("Girdiğiniz e-mail geçersiz karakterler içermektedir.\n");
                return 0;
          }
       }
       return 1;
   }   
    function mailPage(page){
         var keyword = prompt("Lütfen bir mail adresi giriniz.", "")
         if(CheckEmail(keyword)){
             var pagename = "/reports/htmlmail.php?page=/temp/"+page+  "&email="+ keyword;
             this.location.href = pagename;
         }    
      }   
   
</script>

<table width="85%" border="1" cellspacing="0" cellpadding="0">
  <tr>
    <td colspan="4" width="100%" align="center" class="" align="center">
          <TABLE BORDER="0" WIDTH="100%">
            <TR>
              <TD><a href="http://www.crystalinfo.net" target="_blank"><img border="0" SRC="<?=IMAGE_ROOT?>logo2.gif" ></a></TD>
              <TD align=center CLASS="header"><?echo $row1->VALUE;?><BR>YÖNETİM RAPORU<br><?=$company?></TD>
              <TD align=right><img SRC="<?=IMAGE_ROOT?>company.gif"></TD>
            </TR>
            </TABLE>

      </td>
  </tr>
  <tr>
    <td align="right" colspan=4>

       <table width="100%" cellspacing=0 cellpadding=0>
        <tr><td width="50%" class="rep_header" align="left">
           <?if($t0!=""){?>
           Tarih (<?=date("d/m/Y",strtotime($t0))?>
               <?if($t1!=""){?>
                 <?echo (" - ".date("d/m/Y",strtotime($t1)));}?>
            )<?}?>
         </td><td width="50%" class="rep_header" align="right">
          <table cellspacing=0 cellpadding=0>
            <tr>
              <td><img src="<?=IMAGE_ROOT?>report/top02.gif" border=0></td>
              <td><a href="javascript:mailPage('man.html')"><img src="<?=IMAGE_ROOT?>report/mail.gif" border=0 title="Mail"></a></td>
              <td><a href="javascript:history.back(1);"><img src="<?=IMAGE_ROOT?>report/geri.gif" border=0 title="Geri"></a></td>
              <td><a href="javascript:history.forward(1);"><img src="<?=IMAGE_ROOT?>report/ileri.gif" border=0 title="İleri"></a></td>
              <td><a href="javascript:document.all('sort_me').submit();"><img src="<?=IMAGE_ROOT?>report/yenile.gif" border=0 title="Yenile"></a></td>
              <td><a href="javascript:window.print();"><img src="<?=IMAGE_ROOT?>report/print.gif" border=0 title="Yazdır"></a></td>
              <td><img src="<?=IMAGE_ROOT?>report/top01.gif" border=0></td>
            </tr>
          </table>
         </td></tr>
        </table>
        </td>
    </tr>    
   <TR class="" >
       <TD colspan=4 ALIGN="center">
          <table BORDER="0" width="100%">
         
             <tr> 
			 
			   <td height="350" colspan="4" class="text_beyaz" align="CENTER">
			<iframe src="/reports/man/chart_outb_sure.php?t0=<?echo $t0; ?>&t1=<?echo $t1; ?>&site=<?echo $SITE_ID;?>" width="50%" height="95%"></iframe>
               </td>
			 
					 
               <td height="350" colspan="4" class="text_beyaz" align="CENTER">
			<iframe src="/reports/man/chart_outb_adet.php?t0=<?echo $t0; ?>&t1=<?echo $t1; ?>&site=<?echo $SITE_ID;?>" width="50%" height="95%"></iframe>
               </td>
             </tr>


                </table>
       </TD>
     
     </TR>
   <TR class="" >
       <TD  colspan=4 ALIGN="center">
          <table WIDTH="420">
                     <TR class="" ><br>
                       <TD class=rep_table_header colspan=4>Telefon Gider Dağılımı</TD>
                     </TR>
               
                     <TR class="" >
                       <TD class=header>Bölge</TD>
					   <TD class=header>Adet</TD>
                       <TD class=header>Süre(Saniye)</TD>
                       <TD class=header>Tutar(TL)</TD>
                     </TR>
                        <?  
                        $total_dur = 0;
                        $total_price = 0;
                           $sql_str = "SELECT SUM(PRICE) AS PRICE, SUM(DURATION) AS SURE, LocationType as NAME,
									   COUNT(CDR_MAIN_DATA.CDR_ID) AS ADET
                                       FROM $CDR_MAIN_DATA AS CDR_MAIN_DATA
                                       INNER JOIN TLocationType ON TLocationType.LocationTypeid = CDR_MAIN_DATA.LocationTypeid
                                       WHERE ".$kriter." ".$usr_crt."
									   GROUP BY CDR_MAIN_DATA.LocationTypeid
                           " ; 
                           
						//    echo $sql_str; die;
						   
						   
						   if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
                                   print_error($error_msg);
                                   exit;
                           }
	
						   
						    	// echo "Halil"; die;
						   
                           while($row = mysqli_fetch_object($result)){
                            $i++;
                                 $bg_color = "E4E4E4";   
                                 if($i%2) $bg_color ="FFFFFF";
                              echo " <tr  BGCOLOR=$bg_color>";
							  
							  
                           ?>      
                                <TD class=header><?=$row->NAME?></TD>
								<TD class=text><?=$row->ADET?></TD>
                                <TD class=text><?=time_maker($row->SURE)?></TD>
                                <TD class=text ALIGN="right"><?=write_price($row->PRICE)?></TD>
                              </TR>
                        <? 
                        $total_price += $row->PRICE;
                        $total_dur += $row->SURE;
						$total_count += $row->ADET;
                        }
                        $i++;
                        $bg_color = "E4E4E4";   
                        if($i%2) $bg_color ="FFFFFF";
                        ?>
                            <tr bgcolor=<?=$bg_color?>>
                                <TD class=header>Toplam</TD>
								<TD class=text><b><?=write_price($total_count)?></b></TD>
                                <TD class=text><b><?=time_maker($total_dur)?></b></TD>
                                <TD class=text ALIGN="right"><b><?=write_price($total_price)?></b></TD>
							</TR>
                  </table>
              </TD>
           </TR>
   
            <TR>
			  <TD>
			  
			  
                 <table width="100%">
                  <tr class = "">
 
			<td height="635" colspan="4" class="text_beyaz" align="CENTER" >
			 <br>Seçilen Tarih Aralığında En Fazla Çağrı Yapan İlk 5 Dahili<br>
			<iframe src="/reports/man/chart_top5_sta.php?t0=<?echo $t0; ?>&t1=<?echo $t1; ?>&site=<?echo $SITE_ID;?>" width="95%" height="50%"></iframe><br>
                 <br>
			
			<br>Seçilen Tarih Aralığında En Fazla Çağrının Yapıldığı İlk 5 İl<br>
			<iframe src="/reports/man/chart_top5_il.php?t0=<?echo $t0; ?>&t1=<?echo $t1; ?>&site=<?echo $SITE_ID;?>" width="95%" height="50%"></iframe>
                 <br>
             
	
               </td>
			
              
			<td height="635" colspan="4" class="text_beyaz" align="CENTER" ><br>
			
			 <br>Seçilen Tarih Aralığında En Fazla Çağrı Karşılayan İlk 5 Dahili<br>
			<iframe src="/reports/man/chart_top5_sta_inc.php?t0=<?echo $t0; ?>&t1=<?echo $t1; ?>&site=<?echo $SITE_ID;?>" width="95%" height="50%"></iframe><br>
                 <br>
			 
				
             <br>Seçilen Tarih Aralığında En Fazla Çağrının Yapıldığı İlk 5 Ülke<br>
				<iframe src="/reports/man/chart_top5_ulke.php?t0=<?echo $t0; ?>&t1=<?echo $t1; ?>&site=<?echo $SITE_ID;?>" width="95%" height="50%"></iframe><br>
                 <br>

               </td>
                  </tr>
 			</table>

		
			</TD>	
			</TR>
         
		 
              <td height="360" colspan="4" class="text_beyaz" align="CENTER">
               
                    <br>Seçilen Tarih Aralığında GSM Operatörlerine Yapılan Çağrıların Dağılımı<br>
				 <iframe src="/reports/man/chart_gsm_oran.php?t0=<?echo $t0; ?>&t1=<?echo $t1; ?>&site=<?echo $SITE_ID;?>" width="50%" height="88%"></iframe>
              </td>
			  
            
			
            
			  
    </TABLE>   
<br><br><br>

</center>

<?

 $fd = fopen($DOCUMENT_ROOT."/temp/man.html", w);
 fwrite($fd,ob_get_contents());
 ob_end_flush();
 
?>