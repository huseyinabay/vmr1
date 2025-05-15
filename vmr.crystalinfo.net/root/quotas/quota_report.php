<?  require_once $_SERVER['DOCUMENT_ROOT']."/cgi-bin/functions.php";
     $cUtility = new Utility();
     $cdb = new db_layer();
     require_valid_login();
     $conn = $cdb->getConnection();
?>
<html>
<body bgcolor="#F0F8FF">
<?cc_page_meta(0);
if ($id!="" && is_numeric($id)){
         $sql_str = "SELECT * FROM QUOTAS WHERE QUOTA_ID = '$id'" ; 
         if (!($cdb->execute_sql($sql_str,$result,$error_msg))){
           print_error($error_msg);
           exit;
     }
    $row = mysqli_fetch_object($result);

    //Bu da QUOTA_ASSIGNS tablosundaki bu quota için bilgileri getirir.
    $sql_str = "SELECT QUOTAS.QUOTA_ID,QUOTA_ASSIGNS.QUOTA_ASSIGN_TYPE_ID,
            EXTENTIONS.EXT_NO,QUOTA_ASSIGNS.QUOTA_ASSIGN_ID
          FROM QUOTAS  
            INNER JOIN QUOTA_ASSIGNS ON QUOTAS.QUOTA_ID = QUOTA_ASSIGNS.QUOTA_ID
            INNER JOIN EXTENTIONS ON QUOTA_ASSIGNS.OBJ_ID = EXTENTIONS.EXT_ID
            WHERE QUOTA_ASSIGNS.QUOTA_ID = '$id' AND QUOTA_ASSIGNS.QUOTA_ASSIGN_TYPE_ID = 1";
         if (!($cdb->execute_sql($sql_str,$result1,$error_msg))){
           print_error($error_msg);
           exit;
         }

    $sql_str = "SELECT QUOTAS.QUOTA_ID,QUOTA_ASSIGNS.QUOTA_ASSIGN_TYPE_ID,
            DEPTS.DEPT_NAME, QUOTA_ASSIGNS.QUOTA_ASSIGN_ID
          FROM QUOTAS  
            INNER JOIN QUOTA_ASSIGNS ON QUOTAS.QUOTA_ID = QUOTA_ASSIGNS.QUOTA_ID
            INNER JOIN DEPTS ON QUOTA_ASSIGNS.OBJ_ID=DEPTS.DEPT_ID
            WHERE QUOTA_ASSIGNS.QUOTA_ID = '$id'  AND QUOTA_ASSIGNS.QUOTA_ASSIGN_TYPE_ID = 2" ; 
         if (!($cdb->execute_sql($sql_str,$result2,$error_msg))){
           print_error($error_msg);
           exit;
         }

  $sql_str = "SELECT QUOTAS.QUOTA_ID,QUOTA_ASSIGNS.QUOTA_ASSIGN_TYPE_ID,DEPTS.DEPT_NAME, 
            QUOTA_ASSIGNS.QUOTA_ASSIGN_ID
          FROM QUOTAS  
            INNER JOIN QUOTA_ASSIGNS ON QUOTAS.QUOTA_ID = QUOTA_ASSIGNS.QUOTA_ID
            INNER JOIN DEPTS ON QUOTA_ASSIGNS.OBJ_ID=DEPTS.DEPT_ID
            WHERE QUOTA_ASSIGNS.QUOTA_ID = '$id'  AND QUOTA_ASSIGNS.QUOTA_ASSIGN_TYPE_ID = 3" ; 
         if (!($cdb->execute_sql($sql_str,$result3,$error_msg))){
           print_error($error_msg);
           exit;
         }

  $sql_str = "SELECT QUOTAS.QUOTA_ID,QUOTA_ASSIGNS.QUOTA_ASSIGN_TYPE_ID,
            QUOTA_ASSIGNS.QUOTA_ASSIGN_ID,AUTH_CODES.AUTH_CODE
          FROM QUOTAS  
            INNER JOIN QUOTA_ASSIGNS ON QUOTAS.QUOTA_ID = QUOTA_ASSIGNS.QUOTA_ID
                        LEFT JOIN AUTH_CODES ON QUOTA_ASSIGNS.OBJ_ID = AUTH_CODES.AUTH_CODE_ID
            WHERE QUOTA_ASSIGNS.QUOTA_ID = '$id'  AND QUOTA_ASSIGNS.QUOTA_ASSIGN_TYPE_ID = 4";
         if (!($cdb->execute_sql($sql_str,$result4,$error_msg))){
           print_error($error_msg);
           exit;
         }
  }else{
    table_header("Kota Özet Bilgileri","100%");
    table_footer(0);
    exit;
  }    

?>

   <table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="F0F8FF">
       <tr> 
           <td width="100%" colspan="3" align="center"> 
              <?table_in_table_header("Kota Özeti","80%");?>
        <table border="0" cellspacing="2" cellpadding="1" align="center" width="100%">
                   <tr>
                       <td class="bg_acik_f_koyu" width="35%">Kota Adı</td>
                        <td class="bg_acik_f_acik"width="65%"><? echo $row->QUOTA_NAME; ?></td>
                    </tr>  
                    <tr> 
                        <td width="35%" class="bg_koyu_f_koyu">Ş.İ. Limit</td>
                        <td width="65%" class="bg_koyu_f_acik">
              <? echo calculate_time($row->INCITY_LIMIT,"hour"); ?>&nbsp&nbspSaat&nbsp&nbsp
              <? echo calculate_time($row->INCITY_LIMIT,"min"); ?>&nbsp&nbspDk
            </td>
                    </tr>  
                    <tr> 
                        <td class="bg_acik_f_koyu" width="35%">Ş.A. Limit</td>
                        <td width="65%" class="bg_acik_f_acik">
              <? echo calculate_time($row->INTERCITY_LIMIT,"hour"); ?>&nbsp&nbspSaat&nbsp&nbsp
              <? echo calculate_time($row->INTERCITY_LIMIT,"min"); ?>&nbsp&nbspDk
            </td>
                    </tr>  
                    <tr> 
                        <td class="bg_koyu_f_koyu" width="35%">GSM Limit</td>
                        <td width="65%" class="bg_koyu_f_acik">
              <? echo calculate_time($row->GSM_LIMIT,"hour"); ?>&nbsp&nbspSaat&nbsp&nbsp
              <? echo calculate_time($row->GSM_LIMIT,"min"); ?>&nbsp&nbspDk
            </td>
                    </tr>  
                    <tr> 
                        <td class="bg_acik_f_koyu" width="35%">U.A. Limit</td>
                        <td width="65%" class="bg_acik_f_acik">
              <? echo calculate_time($row->INTERNATIONAL_LIMIT,"hour"); ?>&nbsp&nbspSaat&nbsp&nbsp
              <? echo calculate_time($row->INTERNATIONAL_LIMIT,"min"); ?>&nbsp&nbspDk
            </td>
                    </tr>  
                    <tr> 
                        <td class="bg_acik_f_koyu" width="35%">Ücret Limiti</td>
                        <td width="65%" class="bg_acik_f_acik">
              <? echo $row->PRICE_LIMIT;?>
            </td>
                    </tr>  
                </table>
        <?table_in_table_footer();?>
           </td>
        </tr>
      <tr valign="top" align="center"> 
           <td colspan="3" background="<?=IMAGE_ROOT?>assign_bg.gif"><img src="<?=IMAGE_ROOT?>assigns.gif"></td>
    </tr>
    <tr>
      <td colspan="3" height="5"></td>
    </tr>
     <tr>
        <td width="49%" valign="top">
        <?table_simple_header("Dahililer","100%");?>
        <table width="100%" cellspacing="2" cellpadding="0" border="0">
        <?  if (mysqli_num_rows($result1)>0){
            while ($row1 = mysqli_fetch_object($result1)){?>
                  <tr>
                     <td width="65%" class="bg_acik_f_acik">&nbsp <?=$row1->EXT_NO;?></td>
                     <td width="17%" class="bg_acik_f_acik"><a href="quota_assign_db.php?act=del&id=<?=$row1->QUOTA_ASSIGN_ID; ?>&from=main&qu_id=<?=$row1->QUOTA_ID?>">İptal</a></td>
                     <td width="17%" class="bg_acik_f_acik"><a href="#" onclick="javascript:open_qa('<?=$row1->QUOTA_ASSIGN_ID; ?>');">Gör</a></td>
                  </tr>
            <?      }
            }
        ?>
        </table>
        <?table_simple_footer();?>    
      </td>
      <td width="2%"></td>
        <td width="49%" valign="top">
        <?table_simple_header("Departmanlar","100%");?>
        <table width="100%" cellspacing="2" cellpadding="0" border="0">
        <?  if (mysqli_num_rows($result2)>0){
            while ($row1 = mysqli_fetch_object($result2)){?>
                  <tr>
                     <td width="65%" class="bg_acik_f_acik">&nbsp&nbsp<?=$row1->DEPT_NAME;?></td>
                     <td width="17%" class="bg_acik_f_acik"><a href="quota_assign_db.php?act=del&id=<?=$row1->QUOTA_ASSIGN_ID; ?>&from=main&qu_id=<?=$row1->QUOTA_ID?>">İptal</a></td>
                     <td width="17%" class="bg_acik_f_acik"><a href="#" onclick="javascript:open_qa('<?=$row1->QUOTA_ASSIGN_ID; ?>');">Gör</a></td>
                  </tr>
            <?      }
            }
        ?>
        </table>
        <?table_simple_footer();?>    
      </td>
    </tr>
    <tr>
      <td colspan="3" height="15"></td>
    </tr>
    <tr>
        <td width="49%" valign="top">
        <?table_simple_header("Dept. Üyeleri","100%");?>
        <table width="100%" cellspacing="2" cellpadding="0" border="0">
        <?  if (mysqli_num_rows($result3)>0){
            while ($row1 = mysqli_fetch_object($result3)){?>
                  <tr>
                     <td width="65%" class="bg_acik_f_acik">&nbsp <?=$row1->DEPT_NAME;?></td>
                     <td width="17%" class="bg_acik_f_acik"><a href="quota_assign_db.php?act=del&id=<?=$row1->QUOTA_ASSIGN_ID; ?>&from=main&qu_id=<?=$row1->QUOTA_ID?>">İptal</a></td>
                     <td width="17%" class="bg_acik_f_acik"><a href="#" onclick="javascript:open_qa('<?=$row1->QUOTA_ASSIGN_ID; ?>');">Gör</a></td>
                  </tr>
            <?      }
            }
        ?>
        </table>
        <?table_simple_footer();?>    
      </td>
      <td width="2%"></td>
        <td width="49%" valign="top">
        <?table_simple_header("Auth. Kodları","100%");?>
        <table width="100%" cellspacing="2" cellpadding="0" border="0">
        <?  if (mysqli_num_rows($result4)>0){
            while ($row1 = mysqli_fetch_object($result4)){?>
                  <tr>
                     <td width="65%" class="bg_acik_f_acik">&nbsp <?=$row1->AUTH_CODE;?></td>
                     <td width="17%" class="bg_acik_f_acik"><a href="quota_assign_db.php?act=del&id=<?=$row1->QUOTA_ASSIGN_ID; ?>&from=main&qu_id=<?=$row1->QUOTA_ID?>">İptal</a></td>
                     <td width="17%" class="bg_acik_f_acik"><a href="#" onclick="javascript:open_qa('<?=$row1->QUOTA_ASSIGN_ID; ?>');">Gör</a></td>
                  </tr>
            <?      }
            }
        ?>
        </table>    
        <?table_simple_footer();?>
      </td>
      </tr>
   </table>
   
<script language="javascript">
  function open_qa(my_id){
  
    if(window.parent.frames.length > 0){
      window.parent.location.href='quota_assign.php?act=upd&id='+my_id;
    }else{
      window.open('quota_assign.php?act=upd&id='+my_id)
    }  
  }
</script>   
