m4_divert(-1)

## Avoid collision with HTML --------------------------------
m4_changecom(`##')



## WAM language constructs ----------------------------------
m4_define(WAMrem,`')
## $1 = Condition
## $2 = What
m4_define(WAMwhile,`ifelse(eval($1),1,`$2`'WAMwhile(`$1',`$2')')')

m4_define(WAMinc,`define(`$1',incr($1))')
m4_define(WAMdec,`define(`$1',decr($1))')

m4_define(WAMfor,`pushdef(`$1',`$2')WAMwhile(`$1 <= $3',`$4`'WAMinc(`$1')')popdef(`$1')')




## WAM colors -----------------------------------------------
m4_define(WAMcol_bg,`#213A63')
m4_define(WAMcol_bglight,`#31426b')
m4_define(WAMcol_bglighter,`#41527b')
m4_define(WAMcol_text,`#8494bd')
m4_define(WAMcol_head,`black')




## WAM imagerefs --------------------------------------------
m4_define(WAMimg_path,`WAMroot/images')
m4_define(WAMspaceh,`<IMG SRC="WAMimg_path/space.gif" WIDTH="$1px" HEIGHT="1px">')
m4_define(WAMspacev,`<IMG SRC="WAMimg_path/space.gif" WIDTH="1px" HEIGHT="$1px">')
m4_define(WAMimg_biglogo,`<IMG SRC="WAMimg_path/logo.big.gif" ALT="WAM" BORDER="0">')
m4_define(WAMimg_mainmenu,`<IMG SRC="WAMimg_path/mainmenu.gif" ALT="WAM mainmenu" BORDER="0">')
m4_define(WAMimg_menudash,`<IMG SRC="WAMimg_path/menudash.png" BORDER="0">')
m4_define(WAMimg_sheep,`<IMG SRC="WAMimg_path/sheep01.gif" ALT="wam up your pets!" BORDER="0">')
m4_define(WAMimg_connected,`<IMG SRC="WAMimg_path/globe.gif" ALT="connected">')
m4_define(WAMimg_disconnected,`<IMG SRC="WAMimg_path/globe_2.gif" ALT="disconnected">')
m4_define(WAMimg_olumchat,`<IMG SRC="WAMimg_path/ochat_logo.gif" ALT="Olums Chat 1.01">')
m4_define(WAMimg_sponsor,`<IMG SRC="WAMimg_path/sheepbone.gif" ALT="visit our sponsor">')



## WAM headers and formats ----------------------------------
m4_define(WAMdtd,`<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2//EN">')

## $1 = complementary head tags
m4_define(WAMhead,`<HTML><HEAD><TITLE>WAM up your WG!</TITLE>$1</HEAD>')

m4_define(WAMtail,`</HTML>')

## $1 = level
m4_define(WAMlevel,`WAMspaceh(m4_eval($1*10))<FONT SIZE="m4_eval(4-$1)">$2</FONT><BR>')

## $1 = Where
## $2 = What
m4_define(WAMlink,`<A HREF="$1" TARGET="main">$2</A>')

## $1 = Level
## $2 = Where
## $3 = What
m4_define(WAMnavlink,`WAMlevel(`$1',WAMlink(`$2',`$3'))')

## $1 = complementary attribtutes
m4_define(WAMbody,`<BODY BGCOLOR="WAMcol_bg" TEXT="WAMcol_text" LINK="WAMcol_text" HLINK="WAMcol_text" VLINK="WAMcol_text" $1>')

m4_define(WAMtitle,`<H1>$1</H1>')

m4_define(WAMendbody,`</BODY>')
    
m4_divert`'m4_dnl
