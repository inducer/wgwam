m4_include(WAMroot/`mac.inc')

WAMdtd
WAMhead

WAMbody(`BACKGROUND="images/bg_navigation.gif"')

WAMlink(/system/login.php,WAMimg_mainmenu)

<TABLE BORDER="0" ALIGN="center">
  <TR><TD>
    WAMimg_menudash
    <P>
    WAMnavlink(1,system/login.php,Login)
    WAMnavlink(1,menu.php,Main menu)
    WAMnavlink(1,chat/chatindex.php,Chat)
    WAMnavlink(1,services/serviceplan.php,Service plan)
    WAMnavlink(1,billing/iou-edit.php,Billing editor)
    WAMnavlink(1,billing/bill-survey.php,Billing survey)
    <P>
    WAMimg_menudash
    </TD></TR>
  </TABLE>

WAMendbody
WAMtail
