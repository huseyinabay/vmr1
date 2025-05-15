<?  //INCLUDES
   require_once $_SERVER['DOCUMENT_ROOT'].'/cgi-bin/functions.php';
   
   require_valid_login();
    
   $cUtility = new Utility();
   $cdb = new db_layer();

   $conn = $cdb->getConnection();
 
   cc_page_meta();
   echo "<center>";
   page_header();
   echo "<br>"
?>
<table border="0" width="85%" align="center">
  <tr>
    <td width="100%">
        <table border="0" width="650" bgcolor="#FFFFFF" cellspacing="1" cellpadding="4">
            <tr class="rep2_tr">
              <td nowrap class="rep2_header" colspan="2" width="50%"><img src="<?=IMAGE_ROOT?>ok.gif">Özet Raporlar</td>
        <td nowrap class="rep2_header" colspan="2" width="50%"><img src="<?=IMAGE_ROOT?>ok.gif">Giden Çağrı Raporları</td>
      </tr>
      <tr>  
        <td class="rep2_cells1" align="center" width="15%">
          <a href="/reports/general/report_general.php">
              <img border="0" src="<?=IMAGE_ROOT?>rep_ozet.gif" alt=""><br>
          </a>
        </td>
        <td class="rep2_cells2" width="35%">Çağrı Türlerine Dağılımı <br>Dahililere Dağılımı<br>
            Auh. Kodlarına Dağılımı<br>Departmanlara Dağılımı<br>
            İllere Dağılımı
          
        </td>
        <td class="rep2_cells1" width="15%" align="center" valign="top">
            <a href="/reports/outbound/report_outb.php">
              <img border="0" src="<?=IMAGE_ROOT?>rep_outb.gif" alt=""><br>
           </a>            
        </td>
        <td class="rep2_cells2" width="35%">Bir Dahilinin Aramaları<br>Bir Departmanın Aramaları<br>Bir Şehre Yapılan Aramalar<br>Bir Ülkeye Yapılan Aramalar
        </td>
      </tr>
            <tr class="rep2_tr">
              <td nowrap class="rep2_header" colspan="2" width="50%"><img src="<?=IMAGE_ROOT?>ok.gif">Top Raporlar</td>
        <td nowrap class="rep2_header" colspan="2" width="50%"><img src="<?=IMAGE_ROOT?>ok.gif">GSM Raporları</td>
      </tr>
      <tr>
        <td class="rep2_cells1" width="15%" align="center" valign="top">
          <a href="/reports/top/report_top.php">
              <img border="0" src="<?=IMAGE_ROOT?>rep_top.gif" alt=""><br>
          </a>            
        </td>
        <td class="rep2_cells2" width="35%">En Çok Aranan Numaralar<br> En Çok Arama Yapan Dahililer<br> En Uzun Süre Konuşan Dahililer<br> 
          En Fazla Aranan İller<br>
        </td>
        <td class="rep2_cells1" width="15%" align="center" valign="top">
          <a href="/reports/gsm/report_gsm.php">
              <img border="0" src="<?=IMAGE_ROOT?>rep_gsm.gif" alt=""><br>
          </a>            
        </td>
        <td class="rep2_cells2" width="35%">Operatörlere göre yapılan aramalar<br> Operatör Matrixi<br>  
                En Fazla Çağrı yapan departmanlar<br> 
                GSM kodlarına göre yapılan aramalar<br> 
         </td>
      </tr>
      <tr class="rep2_tr">
              <td nowrap class="rep2_header" colspan="2" width="50%"><img src="<?=IMAGE_ROOT?>ok.gif">Dahili Görüşme Raporları</td>
        <td nowrap class="rep2_header" colspan="2" width="50%"><img src="<?=IMAGE_ROOT?>ok.gif">Hat Raporları</td>
      </tr>
      <tr>
          <td class="rep2_cells1" width="15%" align="center" valign="top">
          <a href="/reports/local/report_local.php">
              <img border="0" src="<?=IMAGE_ROOT?>dahili.gif" alt=""><br>
          </a>
        </td>
        <td class="rep2_cells2" width="35%">Dahiliden Dahiliye Yapılan Aramaların Raporları.
        </td>
          <td class="rep2_cells1" width="15%" align="center" valign="top">
          <a href="/reports/trunk/report_trunk.php">
              <img border="0" src="<?=IMAGE_ROOT?>rep_trunk.gif" alt=""><br>
          </a>            
        </td>
        <td class="rep2_cells2" width="35%">Çağrıların Hatlara Dağılımı<br> 
          Giden Çağrıların Şebekelere Dağılımı <br> Gelen Çağrıların Şebekelere Dağılımı <br>
        </td>
      </tr>
      <tr class="rep2_tr">
              <td nowrap class="rep2_header" colspan="2" width="50%"><img src="<?=IMAGE_ROOT?>ok.gif">Sistem Raporları</td>
        <td nowrap class="rep2_header" colspan="2" width="50%"><img src="<?=IMAGE_ROOT?>ok.gif">Gelen Çağrı Raporları</td>
      </tr>
      <tr>
          <td class="rep2_cells1" width="15%" align="center" valign="top">
          <a href="/reports/other/report_system.php">
              <img border="0" src="<?=IMAGE_ROOT?>rep_system.gif" alt=""><br>
          </a>
        </td>
        <td class="rep2_cells2" width="35%">Dahili Listesi<br>Departman Listesi<br>
        İl Kodları<br> Ülke Kodları
        </td>
          <td class="rep2_cells1" width="15%" align="center" valign="top">
          <a href="/reports/inbound/report_inb.php">
              <img border="0" src="<?=IMAGE_ROOT?>rep_inb.gif" alt=""><br>
          </a>            
        </td>
        <td class="rep2_cells2" width="35%">Çağrıların Dahililere Dağılımı<br>Çağrıların Departmanlara Dağılımı<br>
        Çağrıların Trunklara Dağılımı<br>
          Uzun Süre Tutan Konuşmalar
        </td>
      </tr>
      <tr class="rep2_tr">
              <td nowrap class="rep2_header" colspan="2" width="50%"><img src="<?=IMAGE_ROOT?>ok.gif">Yönetim Raporu</td>
        <td nowrap class="rep2_header" colspan="2" width="50%"><img src="<?=IMAGE_ROOT?>ok.gif">Multisite Raporları</td>
      </tr>
      <tr>
          <td class="rep2_cells1" width="15%" align="center" valign="top">
          <a href="/reports/man/report_management.php">
              <img border="0" src="<?=IMAGE_ROOT?>rep_ozet.gif" alt=""><br>
          </a>            
        </td>
        <td class="rep2_cells2" width="35%">Yöneticilere hazırlanmış olan rapor<br>Toplam Telefon Giderleri
                <br>En fazla görüşen 5 departman<br>En fazla aranan 5 il</td>
          <td class="rep2_cells1" width="15%" align="center" valign="top">
          <a href="/reports/multisite/report_multiside.php">
              <img border="0" src="<?=IMAGE_ROOT?>rep_top.gif" alt=""><br>
          </a>            
        </td>
        <td class="rep2_cells2" width="35%">Bütün siteleri içeren çağrılar<br>Toplam Telefon Gideri<br>Sitelere Göre Giderler</td>
            </tr>
      </table>
    </td>
  </tr>
</table>
    
<?
   page_footer(0);
?>
