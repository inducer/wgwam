m4_include(WAMroot/`mac.inc')

WAMdtd

<?php

	include('WAMroot/wamlib.php');

	WAMprime();
	
  if ($frm_command == 'print') {

		echo '<head><TITLE="Einkaufszettel"></head>';
		echo '<body>';
  	$selection = " ( 0 ";
		do {
			$formvar = key($HTTP_POST_VARS);
			if (ereg("frm_([0-9]*)_check",$formvar,$regs)) {
				$selection = $selection." or ItemID=".$regs[1];
				}
			} while (each($HTTP_POST_VARS));

  	$selection = $selection." )";

		$items = mysql_query("select *,Store from tblShoppingItem,tblStore where tblShoppingItem.StoreID=tblStore.StoreID and ".$selection);
		mysql_rewind($items);

		$store = 'Harrods';
		echo "<H1>Einkaufszettel</H1>";

		while ($item = mysql_fetch_array($items)) {
			if ($item['Store'] != $store) {
				$store = $item['Store'];
				echo "<BR><B>".$store."</B><BR><BR>";
				}
			echo $item['Qty'];
			echo " ";
			echo $item['Unit'];
			echo " ";
			echo "<B>".$item['Name']."</B>";
			echo "<BR>";
			}

  	}

	if ($frm_command == '') {

		echo 'WAMdtd';
		echo 'WAMhead';
		echo 'WAMbody';

		echo '<CENTER>';

		echo 'WAMtitle(Shopping list)';

		echo '<FORM METHOD="POST" ACTION="shoppinglist.php" NAME="editShoppingList">';
		echo '<TABLE BORDER="2" CELLPADDING="2" CELLSPACING="2">';
		echo '	<TR><TD></TD>';
		echo '	<TD>';
		echo '		How much';
		echo '	</TD><TD>';
		echo '		Unit:';
		echo '	</TD><TD>';
		echo '		What';
		echo '	</TD><TD>';
		echo '		Permanent?';
		echo '	</TD><TD>';
		echo '		Refill only?';
		echo '	</TD><TD>';
		echo '		Store:';
		echo '	</TD>';
		echo '	</TR>';

		$items = mysql_query("select *,Store from tblShoppingItem,tblStore where tblShoppingItem.StoreID=tblStore.StoreID");
		mysql_rewind($items);

		while ($item = mysql_fetch_array($items)) {
			echo '<TR>';
			echo '<TD><INPUT TYPE="checkbox" NAME="frm_',$item['ItemID'],'_check"></TD>';
			echo '<TD><INPUT TYPE="text" MAXLENGTH="10" SIZE="5" VALUE="',$item['Qty'],'" NAME="frm_Qty"></TD>';
			echo "<TD>",$item['Unit'],"</TD>";
			echo "<TD><B>",$item['Name'],"</B></TD>";
			if ($item['Permanent'] != 0) {
				echo "<TD> X </TD>";
				}
			else {
				echo "<TD> </TD>";
				}
			if ($item['Refill'] != 0) {
				echo "<TD> X </TD>";
				}
			else {
				echo "<TD> </TD>";
				}
			echo '<TD>',$item['Store'],'</TD>';
			echo "</TR>";
			}

		echo '<TR><TD>';
		echo '		<INPUT TYPE="button" VALUE="neu" SIZE="5" onClick="submit();">';
		echo '		</TD><TD>';
		echo '		<INPUT TYPE="text" MAXLENGTH="10" SIZE="5" NAME="frm_Qty">';
		echo '		</TD><TD>';
		echo '		<INPUT TYPE="text" MAXLENGTH="15" SIZE="7" NAME="frm_Unit">';
		echo '		</TD><TD>';
		echo '		<INPUT TYPE="text" MAXLENGTH="30" SIZE="25" NAME="frm_Name">';
		echo '		</TD><TD>';
		echo '		<INPUT TYPE="checkbox" SIZE="5" NAME="frm_Permanent">';
		echo '		</TD><TD>';
		echo '		<INPUT TYPE="checkbox" SIZE="5" NAME="frm_Refill">';
		echo '		</TD><TD>';
		echo '		<INPUT TYPE="text" MAXLENGTH="30" SIZE="10" NAME="frm_Store">';
		echo '		<INPUT TYPE="hidden" NAME="frm_command" VALUE="">';
		echo '		</TD><TD></TR><TR>';
		echo '	</TR>';
		echo '	</TABLE>';
		echo '	<INPUT TYPE="button" VALUE="print list" onClick="frm_command.value=\'print\';submit();">';
		echo '</FORM>';

		}
			
	?>

</CENTER>
	
WAMendbody
WAMtail
