<?
   require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
   $cUtility = new Utility();
   $cdb = new db_layer();
   require_valid_login();
   $conn = $cdb->getConnection();
   check_right("ADMIN");

    if ($act=="update" && $id!=="" && is_numeric($id)){
            $sql_str = "UPDATE  TRate SET BasicCntPerd = '$VALUE' WHERE Rateid = '$id' " ; 
            if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
                    print_error($error_msg);
                    exit;
            }
            ?>
                  <SCRIPT>
                      window.opener.location.reload();
					  window.close();
                  </SCRIPT>
            <?
            exit;
    }
    
    if ($act=="upd" && $id!=="" && is_numeric($id)){
            $sql_str = "SELECT * FROM TRate WHERE Rateid = '$id' " ; 
            if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
                    print_error($error_msg);
                    exit;
            }
            $row = mysqli_fetch_object($result);
    }
    cc_page_meta();
    echo "<br>";
    table_header("Tarife Süreleri","");
?>
<center>
<table width="100%" cellpadding="0" cellspacing="0">
      <tr>
            <td>
<center>
            <form name="system" method="post"  action="tariff_sure.php?act=update">
            <input type="hidden" name="id" value="<?=$id?>">
            <input type="hidden" name="field" value="<?=$row->RateName?>">

            <table class="formbg">
                  <tr class="form">
                        <td>Başlık</td>
                        <td><?=$row->RateName?></td>
                  </tr>
   
                  <tr class="form">
                        <td>&nbsp;</td>
                        <td></td>
                  </tr>
                  <tr class="form">
                        <td>&nbsp;</td>
                        <td></td>
                  </tr>
                  <tr class="form">
                        <td><?=$row->RateName?></td>
                         <td><input type="text" name="VALUE" VALUE="<?echo $row->BasicCntPerd?>" Maxlength="50">  </td> 
                  </tr>
                  <tr>
                        <td COLSPAN="2" ALIGN="center"><input type="button"  VALUE="Kaydet" onclick="if(confirm('Dikkat !!\n\n Tarife Sürelerini Değiştirmek Üzeresiniz \n Eminmisiniz!')) system.submit()"></td>
                  </tr>
            </table>
        </form>
      </td>
</tr>
</table>
<? table_footer("");?>