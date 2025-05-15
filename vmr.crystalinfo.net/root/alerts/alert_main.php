<?  //INCLUDES
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";

   require_valid_login();
    
   $cUtility = new Utility();
   $cdb = new db_layer();

   $conn = $cdb->getConnection();
   check_right("ADMIN");
   cc_page_meta();
   echo "<center>";
   page_header();
   echo "<br>"
?>
<table border="0" width="92%" align="center">
  <tr>
    <td width="100%">
        <table border="0" width="700" bgcolor="#FFFFFF" cellspacing="1" cellpadding="4">
            <tr class="rep2_tr">
              <td nowrap class="rep2_header" colspan="2" width="33%"><img src="<?=IMAGE_ROOT?>ok.gif">Numara Uyarısı</td>
        <td nowrap class="rep2_header" colspan="2" width="33%"><img src="<?=IMAGE_ROOT?>ok.gif">Tutar Uyarısı</td>
        <td nowrap class="rep2_header" colspan="2" width="33%"><img src="<?=IMAGE_ROOT?>ok.gif">Süre Uyarısı</td>
      </tr>
      <tr>  
        <td class="rep2_cells1" align="center" width="8%">
          <a href="/alerts/number_alert_src.php">
              <img border="0" src="<?=IMAGE_ROOT?>alert_phone.gif" alt=""><br>
          </a>
        </td>
        <td class="rep2_cells2" width="25%">Belirtilen Numara Arandığında İlgili Kişilere E-posta Gönderilir.
          
        </td>
        <td class="rep2_cells1" width="8%" align="center" valign="top">
            <a href="/alerts/price_alert_src.php">
              <img border="0" src="<?=IMAGE_ROOT?>alert_price.gif" alt=""><br>
           </a>            
        </td>
        <td class="rep2_cells2" width="25%">Belirtilen Ücretin Üzerinde Bir Çağrı Yapıldığında İlgili Kişilere 
          E-Posta gönderilir.
        </td>
        <td class="rep2_cells1" width="8%" align="center" valign="top">
          <a href="/alerts/duration_alert_src.php">
              <img border="0" src="<?=IMAGE_ROOT?>alert_time.gif" alt=""><br>
          </a>            
        </td>
        <td class="rep2_cells2" width="25%">Belirtilen Sürenin Üzerinde Bir Çağrı Yapıldığında İlgili Kişilere
          E-Posta gönderilir.
        </td>
      </tr>
      <tr class="rep2_tr">
        <td nowrap class="rep2_header" colspan="2" width="33%"><img src="<?=IMAGE_ROOT?>ok.gif">Kontör Uyarısı</td>
              <td nowrap class="rep2_header" colspan="2" width="33%"><img src="<?=IMAGE_ROOT?>ok.gif">Çağrı Türü Uy.</td>
        <td nowrap class="rep2_header" colspan="2" width="33%"><img src="<?=IMAGE_ROOT?>ok.gif">Sanity Raporu</td>
      </tr>  
      <tr class="rep2_tr">
        <td class="rep2_cells1" width="8%" align="center" valign="top">
          <a href="/alerts/counter_alert_src.php">
              <img border="0" src="<?=IMAGE_ROOT?>alert_counter.gif" alt=""><br>
          </a>            
        </td>
        <td class="rep2_cells2" width="25%">Belirtilen Kontörün Üzerinde Bir Çağrı Yapıldığında İlgili Kişilere 
          E-Posta gönderilir.
         </td>
        <td class="rep2_cells1" align="center" width="8%">
          <a href="/alerts/call_type_alert_src.php">
              <img border="0" src="<?=IMAGE_ROOT?>alert_call_type.gif" alt=""><br>
          </a>
        </td>
        <td class="rep2_cells2" width="25%">Belirtilen Türde Bir Çağrı Yapıldığında İlgili Kişilere E-posta Gönderilir.
        </td>
        <td class="rep2_cells1" width="8%" align="center" valign="top">
            <a href="/alerts/sanity_alert.php">
              <img border="0" src="<?=IMAGE_ROOT?>alert_sanity.gif" alt=""><br>
           </a>            
        </td>
        <td class="rep2_cells2" width="25%">Sistem Bilgilerini ve durumunu içeren Rapor İlgili 
          Kişilere E-Posta olarak göndeilir.
        </td>
      </tr>
           <tr class="rep2_tr">
        <td nowrap class="rep2_header" colspan="2" width="33%"><img src="<?=IMAGE_ROOT?>ok.gif">Günün Özeti</td>
        <td nowrap class="rep2_header" colspan="2" width="33%"><img src="<?=IMAGE_ROOT?>ok.gif">Haftanın Özeti</td>
        <td nowrap class="rep2_header" colspan="2" width="33%"><img src="<?=IMAGE_ROOT?>ok.gif">Ayın Özeti</td>
      </tr>
      <tr class="rep2_tr">
        <td class="rep2_cells1" width="8%" align="center" valign="top">
          <a href="/alerts/day_summary_alert_src.php">
              <img border="0" src="<?=IMAGE_ROOT?>alert_day_rep.gif" alt=""><br>
          </a>            
        </td>
        <td class="rep2_cells2" width="25%">Günün Konuşma Özeti İlgili Kişilere 
          E-Posta Olarak gönderilir.
        </td>
        <td class="rep2_cells1" width="8%" align="center" valign="top">
          <a href="/alerts/week_summary_alert_src.php">
              <img border="0" src="<?=IMAGE_ROOT?>alert_week_rep.gif" alt=""><br>
          </a>            
        </td>
        <td class="rep2_cells2" width="25%">Haftanın Konuşma Özeti İlgili Kişilere 
          E-Posta Olarak gönderilir.
        </td>
        <td class="rep2_cells1" width="8%" align="center" valign="top">
          <a href="/alerts/month_summary_alert_src.php">
              <img border="0" src="<?=IMAGE_ROOT?>alert_month_rep.gif" alt=""><br>
          </a>            
        </td>
        <td class="rep2_cells2" width="25%">Ayın Konuşma Özeti İlgili Kişilere 
          E-Posta Olarak gönderilir.
         </td>
      </tr></tr>
           <tr class="rep2_tr">
        <td nowrap class="rep2_header" colspan="2" width="33%"><img src="<?=IMAGE_ROOT?>ok.gif">Santral Uyarısı</td>
        <td nowrap class="rep2_header" colspan="2" width="33%"><img src="<?=IMAGE_ROOT?>ok.gif"></td>
        <td nowrap class="rep2_header" colspan="2" width="33%"><img src="<?=IMAGE_ROOT?>ok.gif"></td>
      </tr>
      <tr class="rep2_tr">
        <td class="rep2_cells1" width="8%" align="center" valign="top">
          <a href="/alerts/switch_codes_src.php">
              <img border="0" src="<?=IMAGE_ROOT?>santral_uyari.gif" alt=""><br>
          </a>            
        </td>
        <td class="rep2_cells2" width="25%">
            Santralden gelecek olan loglar belli patern'lara uyuyorsa belirlenen e-maillara ilgili kayıtlar gönderilir.
        </td>
        <td class="rep2_cells1" width="8%" align="center" valign="top">
        </td>
        <td class="rep2_cells2" width="25%">
        </td>
        <td class="rep2_cells1" width="8%" align="center" valign="top">
        </td>
        <td class="rep2_cells2" width="25%">
         </td>
      </tr>
<!--      <tr class="rep2_tr">
        <td nowrap class="rep2_header" colspan="2" width="33%"><img src="<?=IMAGE_ROOT?>ok2.gif">Genel Departman Raporu</td>
        <td nowrap class="rep2_header" colspan="2" width="33%"><img src="<?=IMAGE_ROOT?>ok2.gif">Genel Dahili Raporu</td>
        <td nowrap class="rep2_header" colspan="2" width="33%"><img src="<?=IMAGE_ROOT?>ok2.gif">Tutarsızlık Uyarısı</td>
      </tr>
      <tr class="rep2_tr">
        <td class="rep2_cells1" width="8%" align="center" valign="top">
          <a href="/alerts/dept_summary_alert_src.php">
              <img border="0" src="<?=IMAGE_ROOT?>alert_gen_dept.gif" alt=""><br>
          </a>            
        </td>
        <td class="rep2_cells2" width="25%">Departmanların çağrı raporları istenilen
          periyotta ilgili kişilere E-Posta olarak gönderilir.
        </td>
        <td class="rep2_cells1" width="8%" align="center" valign="top">
          <a href="/alerts/ext_summary_alert_src.php">
              <img border="0" src="<?=IMAGE_ROOT?>alert_gen_ext.gif" alt=""><br>
          </a>            
        </td>
        <td class="rep2_cells2" width="25%">Dahililerin çağrı raporları istenilen
          peryotta ilgili kişilere E-Posta olarak gönderilir.
        </td>
        <td class="rep2_cells1" width="8%" align="center" valign="top">
            <a href="/alerts/month_summary_alert.php">
              <img border="0" src="<?=IMAGE_ROOT?>alert_audit.gif" alt=""><br>
          </a>            
        </td>
        <td class="rep2_cells2" width="25%">Sistemde Oluşan Tutarsız Durumlar istenilen
          kişilere e-mail olarak gönderilir.
         </td>
      </tr>-->
      </table>
    </td>
  </tr>
</table>
    
<?
   page_footer(0);
?>
