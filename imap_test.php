<?php

    mysql_connect('localhost', 'root', 'mysqladmin')or die("error connect");
    mysql_select_db('test')or die("database error");

    	echo "<style type=text/css>";
        echo "td {";
        echo "  font-family: Arial,Verdana,Sans-Serif;";
        echo "  font-size:   12px;";
        echo "  text-align: center;";
        echo "}";
        echo ".row0 {";
        echo "    background-color: #ffffff;";
        echo "}";
        echo ".row1 {";
        echo "    background-color: #f0f0f0;";
        echo "}";
        echo "</style>";

        $rowclass = 0;
        echo "<table border = 0 cellpadding = 3 cellspacing = 1>
          <tr>
            <td colspan = 7 style = font-size:24px;text-align:left;>Circuit ID Records<br></td>
         </tr>";
        echo "<tr bgcolor = #5ea1dd style = font-size:14px;text-align:center;font-weight:bold;>
          <td>Circuit Id</td>
          <td>Customer Name</td>
          <td>Activation Date</td>
	  <td>Account Number</td>
          <td>S.O Number</td>
          <td>Account Manager</td>
	  <td>Deactivation Date</td>
	  <td>Job Type</td>
          <td>Project Manager</td>
	  <td>Acceptance Date</td>
	  <td>Service Speed</td>
	  <td>Service Type</td>
          <td>Status</td>
	  <td>Incident State</td>
	</tr>";

    $imap = imap_open("{imperium.mail.pairserver.com:993/imap/ssl}INBOX", "prov@imperium.ph", "vorpimperium"); 
    $message_count = imap_num_msg($imap);
    print imap_last_error();

    for ($x=1; $x<=$message_count; ++$x){
	$h = imap_header($imap, $x);
	$h_subject = $h->subject;

	$b = trim(imap_fetchbody($imap, $x, 1.1));
	    if (!$b){
		 $b = trim(imap_fetchbody($imap, $x, 1));
	    }
	    
	$rcid = '@(Svc Id:)([\s\n]*\*)([^*]+)@i';
	preg_match($rcid,$b, $matches);
	$cid = $matches[3]; 
	
	$rcustname = '@(Name:\*)([^*]+)@i';
        preg_match($rcustname,$b, $matches);
        $custname = $matches[2];
	
	$racno = '@(A/c:\*\*\*)([^*]+)@i';
        preg_match($racno,$b, $matches);
        $acno = $matches[2]; 
	
	$ractdate = '@(Service Installation Date)([\s\n]*\*)([^*]+)@i';
        preg_match($ractdate,$b, $matches);
        $actdate = $matches[3];
	
	$rstatus = '@(Job Type)([\s\n]+\*)([^*]+)@i';
        preg_match($rstatus,$b, $matches);
        $status = $matches[3];

	$rsonum = '@(Work Order)([\s]*\:[\s\n]*\*)([^*]+)@i';
        preg_match($rsonum,$b, $matches);
        $sonum = $matches[3];

        $racmgr = '@(A/c Mgr)([\s]*\:[\s\n]*\*)([^*]+)@i';
        preg_match($racmgr,$b, $matches);
        $acmgr = $matches[3];

        $rstat = '@(Status)([\s\n]*\*)([^*]+)@i';
        preg_match($rstat,$b, $matches);
        $stat = $matches[3];

        $rprojmgr = '@(Project Manager)([\s\n]*\*)([^*]+)@i';
        preg_match($rprojmgr,$b, $matches);
        $projmgr = $matches[3];

	$rsvcdate = '@(Service Acceptance Date)([\s\n]*\*)([^*]+)@i';
        preg_match($rsvcdate,$b, $matches);
        $svcdate = $matches[3];

	$rsvctype = '@(Service Type)([\s\n]*\*)([^*]+)@i';
        preg_match($rsvctype,$b, $matches);
        $svctype = $matches[3];

	$rsvcspeed = '@(C38F8 - Speed)([\s\n]*\*)([^*]+)@i';
        preg_match($rsvcspeed,$b, $matches);
        $svcspeed = $matches[3];
	
        if ($status == 'TERM')
        {   
            $svc_status = 'DEACTIVATED';
        }
        else if ($status == 'DCNT')
        {
            $svc_status = 'TEMPORARILY DISCONNECTED';
        }
        else 
        {
            $svc_status = 'ACTIVATED';
        }

	$incident_state = 'Operational';

	mysql_query("INSERT INTO circuit_id (CircuitId, CustName, ActDate, AcNo, JobType, Status, SONum, AcMgr, ProjMgr, SvcDate, SvcType, SvcSpeed) VALUES 
	('$cid','$custname','$actdate','$acno','$status','$stat','$sonum','$acmgr','$projmgr','$svcdate','$svctype','$svcspeed')");

//	echo $h_subject."<br>";
//	echo $b."<br>";
//	echo "<br>";
	$inst="INST";
	//if($status==$inst){
	//  $deployment='ACTIVATED';
	//}

	echo "<tr class = row$rowclass>
		<td style=width:100px;text-align:left;>".$cid."</td>
		<td style=width:300px;text-align:left;>".$custname."</td>
		<td style=width:100px;>".$actdate."</td>
		<td style=width:100px;>".$acno."</td>
		<td style=width:100px;>".$sonum."</td>
		<td style=width:100px;text-align:left;>".$acmgr."</td>
		<td style=width:120px;text-align:center;>".$deployment."</td>
		<td style=width:100px;text-align:center;>".$status."</td>
		<td style=width:100px;text-align:left;>".$projmgr."</td>
		<td style=width:100px;text-align:center;>".$svcdate."</td>
		<td style=width:100px;text-align:left;>".$svcspeed."</td>
		<td style=width:150px;text-align:left;>".$svctype."</td>
        <td style=width:100px;text-align:left;>".$svc_status."</td>
	<td style=width:100px;text-align:left;>".$incident_state."</td>
	</tr>";

	$rowclass = 1 - $rowclass;


	
  }

    echo "</table>";

    imap_close($imap);
    mysql_close($con);


?>


<?php

?> 

