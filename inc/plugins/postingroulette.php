<?php

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}

$plugins->add_hook('misc_start', 'misc_roulette');
$plugins->add_hook('global_intermediate', 'global_roulette_alert');
$plugins->add_hook('misc_start', 'global_roulette_alert_misc');
function postingroulette_info()
{
    return array(
        "name"			=> "Posting Roulette",
        "description"	=> "Automatisches Posting Roulette",
        "website"		=> "",
        "author"		=> "Alex",
        "authorsite"	=> "",
        "version"		=> "1.0",
        "guid" 			=> "",
        "codename"		=> "",
        "compatibility" => "*"
    );
}

function postingroulette_install()
{
    global $db;
    if($db->engine=='mysql'||$db->engine=='mysqli')
    {
        $db->query("CREATE TABLE `".TABLE_PREFIX."postingroulette` (
          `pid` int(10) NOT NULL auto_increment,
          `kat` varchar(255) NOT NULL,
          `username` varchar(255) NOT NULL,
          `new` int(11) NOT NULL default '1',
          PRIMARY KEY (`pid`)
        ) ENGINE=MyISAM".$db->build_create_table_collation());
    }

    $db->query("ALTER TABLE `".TABLE_PREFIX."users` ADD `postingroulette` int(10) NOT NULL default '0' AFTER `sourceeditor`;");
}

function postingroulette_is_installed()
{
    global $db;
    if($db->table_exists("postingroulette"))
    {
        return true;
    }
    return false;
}

function postingroulette_uninstall()
{
    global $db;
    if($db->table_exists("postingroulette"))
    {
        $db->drop_table("postingroulette");
    }

}

function postingroulette_activate()
{
    global $db;
    $insert_array = array(
        'title' => 'postingroulette',
        'template' => $db->escape_string('<html>
<head>
<title>{$mybb->settings['bbname']} - Posting Roulette</title>
{$headerinclude}
</head>
<body>
{$header}<div class="roulette">
<table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder">
<tr>
<td class="thead"><h1>Posting Roulette</h1></td>
</tr>
<tr>
<td class="trow1" align="center">

  <button class="tablinks" onclick="roulette(event, 'Willkommen')" id="defaultOpen">Willkommen</button>
  <button class="tablinks" onclick="roulette(event, 'Schüler')">Schüler</button>
	  <button class="tablinks" onclick="roulette(event, 'Studenten')">Studenten</button>
  <button class="tablinks" onclick="roulette(event, 'Erwachsene')">Erwachsene</button>
		  <button class="tablinks" onclick="roulette(event, 'Beides')">Keine Präferenz</button>

</td>
</tr>
	<tr><td class="trow1">
<div id="Willkommen" class="roulettecontent">
	<h1>Willkommen beim Postingroulette</h1>
	<div class="smalltext">Du bist auf der Suche, mal mit jemand deren einen Play zu machen, als immer die gleichen? Dann bist du hier richtig. Entweder du trägst dich selbst ein oder aber schaust, wen dir der Würfel zuweist. Lass dich überraschen. <br />
		Per PN oder aber auch Discord (insofern du es im Profil angegeben hast) könnt ihr euch über die Szene besprechen.
	</div>
	<h1>Charakter eintragen</h1>
	{$pr_eintragen}
		</div>
		
		<div id="Schüler" class="roulettecontent">
	<h1>Charakterauswahl - Nur Schüler</h1>
			{$pr_schueler}
		</div>
				<div id="Studenten" class="roulettecontent">
	<h1>Charakterauswahl - Nur Studenten</h1>
			{$pr_studenten}
		</div>
				<div id="Erwachsene" class="roulettecontent">
	<h1>Charakterauswahl - Nur Erwachsene</h1>
					{$pr_erwachsene}
		</div>
		
						<div id="Beides" class="roulettecontent">
	<h1>Charakterauswahl -Keine Präferenz</h1>
							{$pr_beides}
		</div>
		</td></tr>
	</table></div>
{$footer}
</body>
</html>		<!--Javascrtip-->
	<script>
function roulette(evt, userTag) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("roulettecontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(userTag).style.display = "block";
  evt.currentTarget.className += " active";
}

// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();
</script>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );

    $db->insert_query("templates", $insert_array);

    $insert_array = array(
    'title' => 'postingroulette_ausw',
    'template' => $db->escape_string('<table width=\'100%\' align=\'center\'>
	<tr><td class=\'trow1\' align=\'center\'><h2>Dein Postingpartner</h2></td></tr>	
<tr><td class=\'trow1\' align=\'center\'><div style=\'font-size: 50px;\'>{$row[username]}</div></td></tr>
<tr><td class=\'trow1\' align=\'center\'><div style=\'font-size: 20px;\'>{$ausw} # {$neu} {$delete}</div></td></tr>
</table>'),
    'sid' => '-1',
    'version' => '',
    'dateline' => TIME_NOW
);

    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'postingroulette_eintragen',
        'template' => $db->escape_string('<table border="0" cellspacing="5" cellpadding="{$theme[\'tablespace\']}" class="tborder" style="width: 50%; margin:auto;">
	<form id="postingroulette" method="post" action="misc.php?action=postingroulette">
		<tr><td class=\'trow1\' align=\'center\' colspan=\'2\'><h2>Im Posting Roulette eintragen</h2></td></tr>
	<tr><td class=\'trow1\' align=\'center\'><strong>Charakter eintragen</strong></td><td class=\'trow1\'  align=\'center\'><input type="text" name="username" id="username" value="Charaktername" class="textbox" /></td></tr>
		<tr><td align="center" colspan=\'2\'><input type="submit" name="eintragen" value="Eintragen" id="submit" class="button"></td></tr></form></table>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );

    $db->insert_query("templates", $insert_array);

    require MYBB_ROOT."/inc/adminfunctions_templates.php";
}

function postingroulette_deactivate()
{
    global $db;

    $db->delete_query("templates", "title IN('postingroulette','postingroulette_ausw','postingroulette_eintragen')");

    require MYBB_ROOT."/inc/adminfunctions_templates.php";
}

function misc_roulette(){
    global $mybb, $templates, $lang, $header, $headerinclude, $footer, $page, $db, $ausw, $neu, $pr_ausw, $delete, $chara_name;

    if($mybb->get_input('action') == 'postingroulette')
    {
        // PM Handler
        require_once MYBB_ROOT."inc/datahandlers/pm.php";
        $pmhandler = new PMDataHandler();
        // Add a breadcrumb
        add_breadcrumb('Posting Roulette', "misc.php?action=postingroulette");
        //Übernehme die gespeicherte Einstellung, welche Gruppen NICHT mit ausgelesen werden soll
        $excluded_groups = $mybb->settings['excluded_groups'];

        $charaktere = $db->query("SELECT uid, username
    FROM " . TABLE_PREFIX . "users
    WHERE usergroup NOT IN ('$excluded_groups')
    ORDER BY username
    ");

        while ($pair = $db->fetch_array($charaktere)) {

            $chara_name .= "<option value='{$pair['uid']}'>{$pair['username']}</option>";
        }

        eval("\$pr_eintragen = \"".$templates->get("postingroulette_eintragen")."\";");

        // Eintragen
        if(isset($_POST['eintragen'])){
            $username = $_POST['username'];
            $kat = $_POST['kat'];

            $new_record = array(
                "username" => $db->escape_string($username),
                "kat" => $db->escape_string($kat)
            );


            $db->insert_query ("postingroulette", $new_record);
            redirect ("misc.php?action=postingroulette");
        }

        //Auslesen Schüler

        $select = $db->query("SELECT *
        FROM ".TABLE_PREFIX."postingroulette p
        LEFT JOIN ".TABLE_PREFIX."users u
        ON u.username = p.username
		WHERE p.kat = 'schueler'
        ORDER BY RAND () LIMIT 1");

        $count = mysqli_num_rows ($select);
        if($count == '0'){
            $pr_schueler = "<table width='100%' align='center'>
	<tr><td class='trow1' align='center'><div class='smalltext'>Momenten sind noch keine Charaktere zur Auswahl</div></td></tr>	
</table>";
        } else{
        while($row = $db->fetch_array($select)){
            $username = format_name ($row['username'], $row['usergroup'], $row['displaygroup']);
            $row[username] = build_profile_link ($username, $row['uid']);
            if($mybb->usergroup['canmodcp'] == 1 OR $row['uid'] == $mybb->user['uid']){
                $delete = "# <a href='misc.php?action=postingroulette&delete={$row['pid']}'>Löschen</a>";
            }
            $ausw = "<a href='misc.php?action=postingroulette&ausw={$row['pid']}'>Auswählen</a>";
            $neu = "<a href='misc.php?action=postingroulette'>Neu Laden</a>";

            eval("\$pr_schueler = \"".$templates->get("postingroulette_ausw")."\";");
        }
    }
	
	        //Auslesen erwachsene

        $select = $db->query("SELECT *
        FROM ".TABLE_PREFIX."postingroulette p
        LEFT JOIN ".TABLE_PREFIX."users u
        ON u.username = p.username
		WHERE p.kat = 'erwachsene'
        ORDER BY RAND () LIMIT 1");

        $count = mysqli_num_rows ($select);
        if($count == '0'){
            $pr_ausw = "<table width='100%' align='center'>
	<tr><td class='trow1' align='center'><div class='smalltext'>Momenten sind noch keine Charaktere zur Auswahl</div></td></tr>	
</table>";
        } else{
        while($row = $db->fetch_array($select)){
            $username = format_name ($row['username'], $row['usergroup'], $row['displaygroup']);
            $row[username] = build_profile_link ($username, $row['uid']);
            if($mybb->usergroup['canmodcp'] == 1 OR $row['uid'] == $mybb->user['uid']){
                $delete = "# <a href='misc.php?action=postingroulette&delete={$row['pid']}'>Löschen</a>";
            }
            $ausw = "<a href='misc.php?action=postingroulette&ausw={$row['pid']}'>Auswählen</a>";
            $neu = "<a href='misc.php?action=postingroulette'>Neu Laden</a>";

            eval("\$pr_erwachsene = \"".$templates->get("postingroulette_ausw")."\";");
        }
    }
	
	        //Auslesen beides

        $select = $db->query("SELECT *
        FROM ".TABLE_PREFIX."postingroulette p
        LEFT JOIN ".TABLE_PREFIX."users u
        ON u.username = p.username
		WHERE p.kat = 'beides'
        ORDER BY RAND () LIMIT 1");

        $count = mysqli_num_rows ($select);
        if($count == '0'){
            $pr_ausw = "<table width='100%' align='center'>
	<tr><td class='trow1' align='center'><div class='smalltext'>Momenten sind noch keine Charaktere zur Auswahl</div></td></tr>	
</table>";
        } else{
        while($row = $db->fetch_array($select)){
            $username = format_name ($row['username'], $row['usergroup'], $row['displaygroup']);
            $row[username] = build_profile_link ($username, $row['uid']);
            if($mybb->usergroup['canmodcp'] == 1 OR $row['uid'] == $mybb->user['uid']){
                $delete = "# <a href='misc.php?action=postingroulette&delete={$row['pid']}'>Löschen</a>";
            }
            $ausw = "<a href='misc.php?action=postingroulette&ausw={$row['pid']}'>Auswählen</a>";
            $neu = "<a href='misc.php?action=postingroulette'>Neu Laden</a>";

            eval("\$pr_beides = \"".$templates->get("postingroulette_ausw")."\";");
        }
    }
        $ausw = $mybb->input['ausw'];
        if($ausw){

            $select = $db->query("SELECT *
        FROM ".TABLE_PREFIX."postingroulette p
        LEFT JOIN ".TABLE_PREFIX."users u
        ON u.username = p.username
        WHERE p.pid = '".$ausw."'
            ");

            $row = $db->fetch_array($select);
            $empf = $row['uid'];
            $abs = $mybb->user['uid'];

            $pm_change = array(
                "subject" => "Posting Roulette",
                "message" => "Liebe/r {$row['username']}, <br /> Ich habe dich beim Posting Roulette gezogen. <br /> Gerne können wir uns zusammen einen Plot dafür ausdenken.",
                //to: wer muss die anfrage bestätigen
                "fromid" => $abs,
                //from: wer hat die anfrage gestellt
                "toid" => $empf
            );
            // $pmhandler->admin_override = true;
            $pmhandler->set_data ($pm_change);
            if (!$pmhandler->validate_pm ())
                return false;
            else {
                $pmhandler->insert_pm ();
            }

            $db->delete_query("postingroulette", "pid = '$ausw'");
            redirect("misc.php?action=postingroulette");
        }


        $delete = $mybb->input['delete'];
        if($delete){
            $db->delete_query("postingroulette", "pid = '$delete'");
            redirect("misc.php?action=postingroulette");
        }

        // Using the misc_help template for the page wrapper
        eval("\$page = \"".$templates->get("postingroulette")."\";");
        output_page($page);
    }
}

function global_roulette_alert(){
    global $db, $mybb, $templates, $postingroulette_alert, $pr_read;

    $uid = $mybb->user['uid'];
    $pr_read = "<a href='misc.php?action=prread&read={$uid}'>[als gelesen markieren]</a>";
    $select = $db->query("SELECT new
    FROM ".TABLE_PREFIX."postingroulette
    WHERE new = 1
    LIMIT 1
    ");
    $row_cnt = mysqli_num_rows ($select);

    if($row_cnt != 0){
        $user = $db->query("SELECT * 
        FROM ".TABLE_PREFIX."users 
        WHERE uid = $uid");

        $data = $db->fetch_array($user);
        if($data['postingroulette'] != 1 ){
            eval("\$postingroulette_alert = \"" . $templates->get ("postingroulette_alert") . "\";");
        }
    }
}

function global_roulette_alert_misc(){
    global $mybb, $db;

    if($mybb->get_input ('action') == 'prread'){
      $read = $mybb->input['read'];
        //für den fall nicht mit hauptaccount online
        $as_uid = intval ($mybb->user['as_uid']);

        if($read){
// suche alle angehangenen accounts
            if ($as_uid == 0) {
                $db->query ("UPDATE " . TABLE_PREFIX . "users SET postingroulette = 1 WHERE (as_uid = $read) OR (uid = $read)");
            } else if ($as_uid != 0) {
//id des users holen wo alle angehangen sind
                $db->query ("UPDATE " . TABLE_PREFIX . "users SET postingroulette = 1 WHERE (as_uid = $as_uid) OR (uid = $read) OR (uid = $as_uid)");
            }
            redirect ("index.php");
      }
    }
}
