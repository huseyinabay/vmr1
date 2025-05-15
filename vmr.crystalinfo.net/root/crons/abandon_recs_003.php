<?
	require_once ('/usr/local/wwwroot/multi.crystalinfo.net/root/cgi-bin/site.cnf');
	include("mail_send.php");
	
	$SITE_ID="3";
  
  $conn = mysqli_connect("localhost","crinfo","SiGmA*19","MCRYSTALINFONE");
	   
  $sql_str = "SELECT * FROM ALERTS
              INNER JOIN ALERT_DEFS ON ALERTS.ALERT_ID = ALERT_DEFS.ALERT_ID
              WHERE ALERTS.ALERT_ID=16 AND ALERT_DEFS.SITE_ID=$SITE_ID"  ; 
  $result = mysqli_query($conn,$sql_str);
//print_r ($result); die;
//ECHO $sql_str; die;	

  while($row = mysqli_fetch_object($result)){
    $ALERT_DEF_ID = $row->ALERT_DEF_ID;
    $LAST_PROC_ID = $row->LAST_PROC_ID;

    $sql1 = "SELECT CDR_MAIN_INB.SITE_ID, EXTENTIONS.SITE_ID, CDR_ID, EXTENTIONS.EMAIL, TIME_STAMP, CHG_INFO, CLID, TER_DN, EXTENTIONS.DESCRIPTION AS DES, E.DESCRIPTION as EDES 
             FROM CDR_MAIN_INB
             LEFT JOIN EXTENTIONS ON EXTENTIONS.EXT_NO = CDR_MAIN_INB.TER_DN
             LEFT JOIN EXTENTIONS as E ON E.EXT_NO = CDR_MAIN_INB.CLID AND EXTENTIONS.SITE_ID = CDR_MAIN_INB.SITE_ID            
             WHERE ERR_CODE=0 AND CALL_TYPE=2 AND CLID<>'' AND CDR_ID > '$row->LAST_PROC_ID' AND CDR_MAIN_INB.SITE_ID=$SITE_ID AND EXTENTIONS.SITE_ID=$SITE_ID AND DURATION='0'
             LIMIT 10" ; 
//ECHO $sql1; die;			 
		$rs1 = mysqli_query($conn,$sql1);

    ////////////package the data to be sent
    while($row1 = mysqli_fetch_object($rs1)){
      $DATA ="";
      $row1->CLID = str_replace("X","",$row1->CLID);
	  $row1->CHG_INFO = str_replace("RING","",$row1->CHG_INFO);
      $DATA = "<b> $row1->CLID - $row1->EDES</b> numaralı telefon <b> $row1->TIME_STAMP </b>  tarihinde sizi( Dahili :<b> $row1->TER_DN   $row1->DES</b>) aradı, ulaşamadı. çalma süresi - $row1->CHG_INFO <BR><BR><BR>";
      $DATA .= "You( Extention :<b> $row1->TER_DN  $row1->DES</b>) called by <b>$row1->CLID </b> at <b> $row1->TIME_STAMP </b> but the number couldn't reach. Ring time - $row1->CHG_INFO <BR> ";
      $sbjct = "Cyrstallinfo- $row1->CLID - $row1->EDES numaralı telefon $row1->TIME_STAMP tarihinde sizi( Dahili : $row1->TER_DN $row1->DES) aradı, ulaşamadı";
      $LAST_PROC_ID = $row1->CDR_ID;
	  $mail = $row1->EMAIL;	
		

      if($row1->CLID && $row1->EMAIL && $DATA){
        mail_send($row1->EMAIL,$sbjct,$DATA);
    	}
    }
  }
  $sql2 = "UPDATE ALERT_DEFS SET LAST_PROC_ID = '$LAST_PROC_ID' WHERE ALERT_DEF_ID = '$ALERT_DEF_ID' AND SITE_ID=$SITE_ID"; 
 //ECHO $sql2; die;
  $rs2 = mysqli_query($conn,$sql2);
  
?>