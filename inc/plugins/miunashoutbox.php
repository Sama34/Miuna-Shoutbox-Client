<?php
/**
 * Miuna Shoutbox
 * https://github.com/martec
 *
 * Copyright (C) 2015-2015, Martec
 *
 * Miuna Shoutbox is licensed under the GPL Version 3, 29 June 2007 license:
 *	http://www.gnu.org/copyleft/gpl.html
 *
 * @fileoverview Miuna Shoutbox - Websocket Shoutbox for Mybb
 * @author Martec
 * @requires jQuery, Nodejs, Socket.io, Express, MongoDB, mongoose, debug and Mybb
 * @credits some part of code based in https://github.com/scotch-io/easy-node-authentication/tree/local
 * @credits sound file by http://community.mybb.com/user-70405.html
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

define('MSB_PLUGIN_VER', '4.1.0');

function miunashoutbox_info()
{
	global $lang;

	$lang->load('config_miunashoutbox');

	return array(
		"name"			=> "Miuna Shoutbox",
		"description"	=> $lang->miunashoutbox_plug_desc,
		"author"		=> "martec",
		"authorsite"	=> "",
		"version"		=> MSB_PLUGIN_VER,
		"compatibility" => "18*"
	);
}

function miunashoutbox_install()
{
	global $db, $lang;

	$lang->load('config_miunashoutbox');

	$query	= $db->simple_select("settinggroups", "COUNT(*) as rows");
	$dorder = $db->fetch_field($query, 'rows') + 1;

	$groupid = $db->insert_query('settinggroups', array(
		'name'		=> 'miunashoutbox',
		'title'		=> 'Miuna Shoutbox',
		'description'	=> $lang->miunashoutbox_sett_desc,
		'disporder'	=> $dorder,
		'isdefault'	=> '0'
	));

	$miunashout_setting[] = array(
		'name' => 'miunashout_online',
		'title' => $lang->miunashoutbox_onoff_title,
		'description' => $lang->miunashoutbox_onoff_desc,
		'optionscode' => 'yesno',
		'value' => 0,
		'disporder' => 1,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_height',
		'title' => $lang->miunashoutbox_heigh_title,
		'description' => $lang->miunashoutbox_heigh_desc,
		'optionscode' => 'numeric',
		'value' => '200',
		'disporder' => 2,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_num_shouts',
		'title' => $lang->miunashoutbox_shoutlimit_title,
		'description' => $lang->miunashoutbox_shoutlimit_desc,
		'optionscode' => 'numeric',
		'value' => '25',
		'disporder' => 3,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_lognum_shouts',
		'title' => $lang->miunashoutbox_logshoutlimit_title,
		'description' => $lang->miunashoutbox_logshoutlimit_desc,
		'optionscode' => 'numeric',
		'value' => '50',
		'disporder' => 4,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_grups_acc',
		'title' => $lang->miunashoutbox_nogrp_title,
		'description' => $lang->miunashoutbox_nogrp_desc,
		'optionscode' => 'groupselect',
		'value' => '7',
		'disporder' => 5,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_mod_grups',
		'title' => $lang->miunashoutbox_mod_title,
		'description' => $lang->miunashoutbox_mod_desc,
		'optionscode' => 'groupselect',
		'value' => '3,4,6',
		'disporder' => 6,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_guest',
		'title' => $lang->miunashoutbox_guest_title,
		'description' => $lang->miunashoutbox_guest_desc,
		'optionscode' => 'yesno',
		'value' => '0',
		'disporder' => 7,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_title',
		'title' => $lang->miunashoutbox_shout_title,
		'description' => $lang->miunashoutbox_shout_desc,
		'optionscode' => 'text',
		'value' => 'Miuna Shoutbox',
		'disporder' => 8,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_server',
		'title' => $lang->miunashoutbox_server_title,
		'description' => $lang->miunashoutbox_server_desc,
		'optionscode' => 'text',
		'value' => '',
		'disporder' => 9,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_socketio',
		'title' => $lang->miunashoutbox_socketio_title,
		'description' => $lang->miunashoutbox_socketio_desc,
		'optionscode' => 'text',
		'value' => '',
		'disporder' => 10,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_server_username',
		'title' => $lang->miunashoutbox_serusr_title,
		'description' => $lang->miunashoutbox_serusr_desc,
		'optionscode' => 'text',
		'value' => '',
		'disporder' => 11,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_server_password',
		'title' => $lang->miunashoutbox_serpass_title,
		'description' => $lang->miunashoutbox_serpass_desc,
		'optionscode' => 'text',
		'value' => '',
		'disporder' => 12,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_imgurapi',
		'title' => $lang->miunashoutbox_imgur_title,
		'description' => $lang->miunashoutbox_imgur_desc,
		'optionscode' => 'text',
		'value' => '',
		'disporder' => 13,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_dataf',
		'title' => $lang->miunashoutbox_dataf_title,
		'description' => $lang->miunashoutbox_dataf_desc,
		'optionscode' => 'text',
		'value' => 'DD/MM hh:mm A',
		'disporder' => 14,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_antiflood',
		'title' => $lang->miunashoutbox_antiflood_title,
		'description' => $lang->miunashoutbox_antiflood_desc,
		'optionscode' => 'numeric',
		'value' => '0',
		'disporder' => 15,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_newpost',
		'title' => $lang->miunashoutbox_newpost_title,
		'description' => $lang->miunashoutbox_newpost_desc,
		'optionscode' => 'yesno',
		'value' => 1,
		'disporder' => 16,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_newthread',
		'title' => $lang->miunashoutbox_newthread_title,
		'description' => $lang->miunashoutbox_newthread_desc,
		'optionscode' => 'yesno',
		'value' => 1,
		'disporder' => 17,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_folder_acc',
		'title' => $lang->miunashoutbox_foldacc_title,
		'description' => $lang->miunashoutbox_foldacc_desc,
		'optionscode' => 'text',
		'value' => '',
		'disporder' => 18,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_newpt_color',
		'title' => $lang->miunashoutbox_newptcolor_title,
		'description' => $lang->miunashoutbox_newptcolor_desc,
		'optionscode' => 'text',
		'value' => '#727272',
		'disporder' => 19,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_on_color',
		'title' => $lang->miunashoutbox_oncolor_title,
		'description' => $lang->miunashoutbox_oncolor_desc,
		'optionscode' => 'text',
		'value' => 'green',
		'disporder' => 20,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_ment_style',
		'title' => $lang->miunashoutbox_mentstyle_title,
		'description' => $lang->miunashoutbox_mentstyle_desc,
		'optionscode' => 'text',
		'value' => '5px solid #cd0e0a',
		'disporder' => 21,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_edt_backcolor',
		'title' => $lang->miunashoutbox_edtcolor_title,
		'description' => $lang->miunashoutbox_edtcolor_desc,
		'optionscode' => 'text',
		'value' => '#f5caca',
		'disporder' => 22,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_zone',
		'title' => $lang->miunashoutbox_zone_title,
		'description' => $lang->miunashoutbox_zone_desc,
		'optionscode' => 'text',
		'value' => '-3',
		'disporder' => 23,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_shouts_start',
		'title' => $lang->miunashoutbox_shoutstart_title,
		'description' => $lang->miunashoutbox_shoutstart_desc,
		'optionscode' => 'radio
'.$lang->miunashoutbox_shoutstart_opt.'',
		'value' => 'bottom',
		'disporder' => 24,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_act_autoimag',
		'title' => $lang->miunashoutbox_actaimg_title,
		'description' => $lang->miunashoutbox_actaimg_desc,
		'optionscode' => 'yesno',
		'value' => 0,
		'disporder' => 25,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_aimg_replacement',
		'title' => $lang->miunashoutbox_aimgrepl_title,
		'description' => $lang->miunashoutbox_aimgrepl_desc,
		'optionscode' => 'textarea',
		'value' => '',
		'disporder' => 26,
		'gid'		=> $groupid
	);	
	$miunashout_setting[] = array(
		'name' => 'miunashout_lim_character',
		'title' => $lang->miunashoutbox_limcharact_title,
		'description' => $lang->miunashoutbox_limcharact_desc,
		'optionscode' => 'numeric',
		'value' => 0,
		'disporder' => 27,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_act_avatar',
		'title' => $lang->miunashoutbox_aavatar_title,
		'description' => $lang->miunashoutbox_aavatar_desc,
		'optionscode' => 'yesno',
		'value' => 1,
		'disporder' => 28,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_act_color',
		'title' => $lang->miunashoutbox_acolor_title,
		'description' => $lang->miunashoutbox_acolor_desc,
		'optionscode' => 'yesno',
		'value' => 1,
		'disporder' => 29,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_dis_colorusrn',
		'title' => $lang->miunashoutbox_dcln_title,
		'description' => $lang->miunashoutbox_dcln_desc,
		'optionscode' => 'yesno',
		'value' => 0,
		'disporder' => 30,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_des_index',
		'title' => $lang->miunashoutbox_destindx_title,
		'description' => $lang->miunashoutbox_destindx_desc,
		'optionscode' => 'yesno',
		'value' => 0,
		'disporder' => 31,
		'gid'		=> $groupid
	);
	$miunashout_setting[] = array(
		'name' => 'miunashout_act_port',
		'title' => $lang->miunashoutbox_actport_title,
		'description' => $lang->miunashoutbox_actport_desc,
		'optionscode' => 'yesno',
		'value' => 0,
		'disporder' => 32,
		'gid'		=> $groupid
	);

	$db->insert_query_multiple("settings", $miunashout_setting);
	rebuild_settings();

}

function miunashoutbox_uninstall()
{

	global $db;

	//Delete Settings
	$db->write_query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN(
		'miunashout_online',
		'miunashout_height',
		'miunashout_num_shouts',
		'miunashout_grups_acc',
		'miunashout_mod_grups',
		'miunashout_guest',
		'miunashout_title',
		'miunashout_server',
		'miunashout_socketio',
		'miunashout_server_username',
		'miunashout_server_password',
		'miunashout_imgurapi',
		'miunashout_dataf',
		'miunashout_antiflood',
		'miunashout_newpost',
		'miunashout_newthread',
		'miunashout_folder_acc',
		'miunashout_newpt_style',
		'miunashout_newpt_color',
		'miunashout_on_color',
		'miunashout_ment_style',
		'miunashout_edt_backcolor',
		'miunashout_zone',
		'miunashout_shouts_start',
		'miunashout_act_autoimag',
		'miunashout_aimg_replacement',
		'miunashout_lim_character',
		'miunashout_act_avatar',
		'miunashout_act_color',
		'miunashout_dis_colorusrn',
		'miunashout_des_index',
		'miunashout_act_port'
	)");

	$db->delete_query("settinggroups", "name = 'miunashoutbox'");
	rebuild_settings();

}

function miunashoutbox_is_installed()
{
	global $db;

	$query = $db->simple_select("settinggroups", "COUNT(*) as rows", "name = 'miunashoutbox'");
	$rows  = $db->fetch_field($query, 'rows');

	return ($rows > 0);
}

function miunashoutbox_activate()
{

	global $db;
	require MYBB_ROOT.'/inc/adminfunctions_templates.php';

	$new_template_global['codebutmiuna'] = "<link href=\"{\$mybb->asset_url}/jscripts/miuna/shoutbox/style.css?ver=".MSB_PLUGIN_VER."\" rel='stylesheet' type='text/css'>
<script src=\"https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.3.5/socket.io.min.js\"></script>
<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css\">
<script type=\"text/javascript\" src=\"https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js\"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js'></script>
<link rel=\"stylesheet\" href=\"{\$mybb->asset_url}/jscripts/sceditor/editor_themes/{\$theme['editortheme']}\" type=\"text/css\" media=\"all\" />
<link rel=\"stylesheet\" href=\"{\$mybb->asset_url}/jscripts/sceditor/editor_themes/msb.css\" type=\"text/css\" media=\"all\" />
<script type=\"text/javascript\" src=\"{\$mybb->asset_url}/jscripts/sceditor/jquery.sceditor.bbcode.min.js?ver=".MSB_PLUGIN_VER."\"></script>
<script type=\"text/javascript\">
<!--
	var msbvar = {mybbuid:'{\$mybb->user['uid']}', mybbusername:'{\$msbusrname}', mybbusergroup:'{\$mybb->user['usergroup']}', miunamodgroups:'{\$mybb->settings['miunashout_mod_grups']}', msblc:'{\$mybb->settings['miunashout_lim_character']}', floodtime:'{\$mybb->settings['miunashout_antiflood']}', mpp: '{\$mybb->settings['miunashout_lognum_shouts']}'},
	shout_lang = '{\$lang->miunashoutbox_shout}',
	add_spolang = '{\$lang->miunashoutbox_add_spoiler}',
	spo_lan = '{\$lang->miunashoutbox_spoiler}',
	show_lan = '{\$lang->miunashoutbox_show}',
	hide_lan = '{\$lang->miunashoutbox_hide}',
	pm_lan = '{\$lang->miunashoutbox_sel_pm}',
	pm_fromlan = '{\$lang->miunashoutbox_pm_from}',
	pm_tolan = '{\$lang->miunashoutbox_pm_to}',
	upimgurlang = '{\$lang->miunashoutbox_up_imgur}',
	connectlang = '{\$lang->miunashoutbox_connect}',
	logofflang = '{\$lang->miunashoutbox_logoff}',
	aloadlang = '{\$lang->miunashoutbox_auto_load}',
	invtoklang = '{\$lang->miunashoutbox_inv_token}',
	eregp = '{\$lang->miunashoutbox_error_regp}',
	eregn = '{\$lang->miunashoutbox_error_regn}',
	usractlan = '{\$lang->miunashoutbox_user_ative}',
	mes_emptylan = '{\$lang->miunashoutbox_mes_empty}',
	usr_banlang = '{\$lang->miunashoutbox_user_banned}',
	usr_abulang = '{\$lang->miunashoutbox_user_abuse}',
	flood_msglan = '{\$lang->miunashoutbox_flood_msg}',
	secounds_msglan = '{\$lang->miunashoutbox_flood_scds}',
	log_htmllan = '{\$lang->miunashoutbox_log_html}',
	log_msglan = '{\$lang->miunashoutbox_log_msg}',
	log_shoutlan = '{\$lang->miunashoutbox_log_shout}',
	log_nextlan = '{\$lang->miunashoutbox_log_next}',
	log_backlan = '{\$lang->miunashoutbox_log_back}',
	prune_shoutlan = '{\$lang->miunashoutbox_prune_shout}',
	ban_msglan = '{\$lang->miunashoutbox_ban_msg}',
	ban_unban_lan = '{\$lang->miunashoutbox_ban_unban}',
	no_ban_usrlan = '{\$lang->miunashoutbox_no_banusr}',
	ban_syslan = '{\$lang->miunashoutbox_ban_sys}',
	ban_selflan = '{\$lang->miunashoutbox_ban_yourself}',
	not_msglan = '{\$lang->miunashoutbox_notice_msg}',
	prune_msglan = '{\$lang->miunashoutbox_prune_msg}',
	del_msglan = '{\$lang->miunashoutbox_del_mesg}',
	banlist_modmsglan = '{\$lang->miunashoutbox_banlist_mod}',
	not_modmsglan = '{\$lang->miunashoutbox_notice_mod}',
	shout_prunedmsglan = '{\$lang->miunashoutbox_pruned}',
	conf_questlan = '{\$lang->miunashoutbox_conf_quest}',
	shout_yeslan = '{\$lang->miunashoutbox_yes}',
	shout_nolan = '{\$lang->miunashoutbox_no}',
	shout_savelan = '{\$lang->miunashoutbox_save}',
	shout_delan = '{\$lang->miunashoutbox_del_msg}',
	cancel_editlan = '{\$lang->miunashoutbox_cancel_edt}',
	sound_lan = '{\$lang->miunashoutbox_sound_msg}',
	volume_lan = '{\$lang->miunashoutbox_volume_msg}',
	min_lan = '{\$lang->miunashoutbox_vmin_msg}',
	max_lan = '{\$lang->miunashoutbox_vmax_msg}',
	perm_msglan = '{\$lang->miunashoutbox_user_permission}',
	numshouts = '{\$mybb->settings['miunashout_num_shouts']}',
	direction = '{\$mybb->settings['miunashout_shouts_start']}',
	zoneset = '{\$mybb->settings['miunashout_zone']}',
	zoneformt = '{\$mybb->settings['miunashout_dataf']}',
	shout_height = '{\$mybb->settings['miunashout_height']}',
	theme_borderwidth = '{\$theme['borderwidth']}',
	theme_tablespace = '{\$theme['tablespace']}',
	imgurapi = '{\$mybb->settings['miunashout_imgurapi']}',
	orgtit = document.title,
	on_color = '{\$mybb->settings['miunashout_on_color']}',
	ment_borderstyle = '{\$mybb->settings['miunashout_ment_style']}',
	edt_color = '{\$mybb->settings['miunashout_edt_backcolor']}',
	actaimg = '{\$mybb->settings['miunashout_act_autoimag']}',
	aimgrepl = '{\$mybb->settings['miunashout_aimg_replacement']}',
	actavat = '{\$mybb->settings['miunashout_act_avatar']}',
	actcolor = '{\$mybb->settings['miunashout_act_color']}',
	dcusrname = '{\$mybb->settings['miunashout_dis_colorusrn']}',
	socketaddress = '{\$mybb->settings['miunashout_socketio']}';
	Object.defineProperty(msbvar, 'mybbuid', { writable: false });
	Object.defineProperty(msbvar, 'mybbusername', { writable: false });
	Object.defineProperty(msbvar, 'mpp', { writable: false });
	Object.defineProperty(msbvar, 'mybbusergroup', { writable: false });
	Object.defineProperty(msbvar, 'miunamodgroups', { writable: false });
	Object.defineProperty(msbvar, 'msblc', { writable: false });
	Object.defineProperty(msbvar, 'floodtime', { writable: false });
// -->
</script>
<script type=\"text/javascript\" src=\"{\$mybb->asset_url}/jscripts/miuna/shoutbox/miunashout.helper.js?ver=".MSB_PLUGIN_VER."\"></script>
<script type=\"text/javascript\">
miuna_smilies = {
	{\$smilies_json}
},
opt_editor = {
	plugins: \"bbcode\",
	style: \"{\$mybb->asset_url}/jscripts/sceditor/textarea_styles/jquery.sceditor.{\$theme['editortheme']}\",
	rtl: {\$lang->settings['rtl']},
	locale: \"mybblang\",
	enablePasteFiltering: true,
	emoticonsEnabled: {\$emoticons_enabled},
	emoticons: {
		// Emoticons to be included in the dropdown
		dropdown: {
			{\$dropdownsmilies}
		},
		// Emoticons to be included in the more section
		more: {
			{\$moresmilies}
		},
		// Emoticons that are not shown in the dropdown but will still be converted. Can be used for things like aliases
		hidden: {
			{\$hiddensmilies}
		}
	},
	emoticonsCompat: true,
	toolbar: \"spoiler,emoticon,imgur\",
};
{\$editor_language}

\$(document).ready(function() {
	\$('#shout_text').height('70px');
	\$('#shout_text').sceditor(opt_editor);
	\$('#shout_text').next().css(\"z-index\", \"1\");
	\$('#shout_text').sceditor('instance').sourceMode(true);
	miunashout_connect();
});

</script>";

	$new_template_global['msb_template'] = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"4\" class=\"tborder tShout\">
	<thead>
		<tr>
			<td class=\"thead theadShout\" colspan=\"1\">
				<div class=\"expcolimage\"><img src=\"{\$theme['imgdir']}/collapse{\$collapsedimg['mshout']}.png\" id=\"mshout_img\" class=\"expander\" alt=\"[-]\" title=\"[-]\" /></div>
				<div><strong>{\$mybb->settings['miunashout_title']}</strong></div>
			</td>
		</tr>
	</thead>
	<tbody style=\"{\$collapsed['mshout_e']}\" id=\"mshout_e\">
		<tr><td class=\"tcat\"><span class=\"smalltext\"><strong><span>{\$lang->miunashoutbox_notice_msg} : </span><span class='notshow'></span></strong></span></td></tr>
		<tr>
			<td class=\"trow2\">
				<div class=\"contentShout\">
					<div class=\"shouttab tabShout selected\">{\$mybb->settings['miunashout_title']}</div>
					<div class=\"actusr tabShout\">{\$lang->miunashoutbox_user_ative}</div>
					<div class=\"shoutarea wrapShout\" style=\"height:{\$mybb->settings['miunashout_height']}px;\"></div>
					<div class=\"wrapShout numusr\" style=\"height:{\$mybb->settings['miunashout_height']}px;display:none;\"></div>
					<form id=\"miunashoutbox-form\">
						<input type=\"text\" name=\"shout_text\" class=\"editorShout\" id=\"shout_text\" data-type=\"shout\" autocomplete=\"off\">{\$codebutmiuna}
					</form>
				</div>
			</td>
		</tr>
	</tbody>
</table>";

	$new_template_global['msb_guest_template'] = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"4\" class=\"tborder tShout\">
	<thead>
		<tr>
			<td class=\"thead theadShout\" colspan=\"1\">
				<div class=\"expcolimage\"><img src=\"{\$theme['imgdir']}/collapse{\$collapsedimg['mshout']}.png\" id=\"mshout_img\" class=\"expander\" alt=\"[-]\" title=\"[-]\" /></div>
				<div><strong>{\$mybb->settings['miunashout_title']}</strong></div>
			</td>
		</tr>
	</thead>
	<tbody style=\"{\$collapsed['mshout_e']}\" id=\"mshout_e\">
		<tr><td class=\"tcat\"><span class=\"smalltext\"><strong><span>{\$lang->miunashoutbox_notice_msg} : </span><span class='notshow'></span></strong></span></td></tr>
		<tr>
			<td class=\"trow2\">
				<div class=\"contentShout\">
					<div class=\"shoutarea wrapShout\" style=\"height:{\$mybb->settings['miunashout_height']}px;\"></div>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<link href=\"{\$mybb->asset_url}/jscripts/miuna/shoutbox/style.css?ver=".MSB_PLUGIN_VER."\" rel='stylesheet' type='text/css'>
<script src=\"https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.3.5/socket.io.min.js\"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js'></script>
<script type=\"text/javascript\">
<!--
	var msbvar = {mybbuid:'{\$mybb->user['uid']}', mybbusername:'{\$lang->guest}', mybbavatar:'{\$mybb->user['avatar']}', mybbusergroup:'{\$mybb->user['usergroup']}', miunamodgroups:'{\$mybb->settings['miunashout_mod_grups']}', msblc:'{\$mybb->settings['miunashout_lim_character']}', floodtime:'{\$mybb->settings['miunashout_antiflood']}'},
	spo_lan = '{\$lang->miunashoutbox_spoiler}',
	show_lan = '{\$lang->miunashoutbox_show}',
	hide_lan = '{\$lang->miunashoutbox_hide}',
	aloadlang = '{\$lang->miunashoutbox_auto_load}',
	numshouts = '{\$mybb->settings['miunashout_num_shouts']}',
	direction = '{\$mybb->settings['miunashout_shouts_start']}',
	zoneset = '{\$mybb->settings['miunashout_zone']}',
	zoneformt = '{\$mybb->settings['miunashout_dataf']}',
	orgtit = document.title,
	actaimg = '{\$mybb->settings['miunashout_act_autoimag']}',
	aimgrepl = '{\$mybb->settings['miunashout_aimg_replacement']}',
	actavat = '{\$mybb->settings['miunashout_act_avatar']}',
	actcolor = '{\$mybb->settings['miunashout_act_color']}',
	dcusrname = '{\$mybb->settings['miunashout_dis_colorusrn']}',
	socketaddress = '{\$mybb->settings['miunashout_socketio']}';
// -->
</script>
<script type=\"text/javascript\" src=\"{\$mybb->asset_url}/jscripts/miuna/shoutbox/miunashout.helper.guest.js?ver=".MSB_PLUGIN_VER."\"></script>
<script type=\"text/javascript\">
miuna_smilies = {
	{\$smilies_json}
};
\$(document).ready(function() {
	miunashout();
});
</script>";

	foreach($new_template_global as $title => $template)
	{
		$new_template_global = array('title' => $db->escape_string($title), 'template' => $db->escape_string($template), 'sid' => '-1', 'version' => '1801', 'dateline' => TIME_NOW);
		$db->insert_query('templates', $new_template_global);
	}

	find_replace_templatesets("index", '#{\$forums}#', "{\$miunashout}\n{\$forums}");
	find_replace_templatesets("portal", '#{\$announcements}#', "{\$miunashout}\n{\$announcements}");
}

function miunashoutbox_deactivate()
{

	global $db;
	require MYBB_ROOT.'/inc/adminfunctions_templates.php';

	$db->delete_query("templates", "title IN('codebutmiuna','msb_template','msb_guest_template')");

	//Exclui templates para as posições da shoutbox
	find_replace_templatesets("index", '#'.preg_quote('{$miunashout}').'#', '',0);
	find_replace_templatesets("portal", '#'.preg_quote('{$miunashout}').'#', '',0);
}

global $settings;
if ($settings['miunashout_online']) {
	$plugins->add_hook('global_start', 'miuna_cache_template');
}
function miuna_cache_template()
{
	global $templatelist, $mybb;

	if (isset($templatelist)) {
		$templatelist .= ',';
	}

	if (THIS_SCRIPT == 'index.php' && !$mybb->settings['miunashout_des_index']) {
		$templatelist .= 'codebutmiuna,msb_template,msb_guest_template';
	}
	if (THIS_SCRIPT == 'portal.php' && $mybb->settings['miunashout_act_port']) {
		$templatelist .= 'codebutmiuna,msb_template,msb_guest_template';
	}
}

function miuna_bbcode_func($smilies = true)
{
	global $db, $mybb, $theme, $templates, $lang, $smiliecache, $cache;

	if (!$lang->miunashoutbox) {
		$lang->load('miunashoutbox');
	}

	$editor_lang_strings = array(
		"editor_bold" => "Bold",
		"editor_italic" => "Italic",
		"editor_underline" => "Underline",
		"editor_strikethrough" => "Strikethrough",
		"editor_subscript" => "Subscript",
		"editor_superscript" => "Superscript",
		"editor_alignleft" => "Align left",
		"editor_center" => "Center",
		"editor_alignright" => "Align right",
		"editor_justify" => "Justify",
		"editor_fontname" => "Font Name",
		"editor_fontsize" => "Font Size",
		"editor_fontcolor" => "Font Color",
		"editor_removeformatting" => "Remove Formatting",
		"editor_cut" => "Cut",
		"editor_cutnosupport" => "Your browser does not allow the cut command. Please use the keyboard shortcut Ctrl/Cmd-X",
		"editor_copy" => "Copy",
		"editor_copynosupport" => "Your browser does not allow the copy command. Please use the keyboard shortcut Ctrl/Cmd-C",
		"editor_paste" => "Paste",
		"editor_pastenosupport" => "Your browser does not allow the paste command. Please use the keyboard shortcut Ctrl/Cmd-V",
		"editor_pasteentertext" => "Paste your text inside the following box:",
		"editor_pastetext" => "PasteText",
		"editor_numlist" => "Numbered list",
		"editor_bullist" => "Bullet list",
		"editor_undo" => "Undo",
		"editor_redo" => "Redo",
		"editor_rows" => "Rows:",
		"editor_cols" => "Cols:",
		"editor_inserttable" => "Insert a table",
		"editor_inserthr" => "Insert a horizontal rule",
		"editor_code" => "Code",
		"editor_width" => "Width (optional):",
		"editor_height" => "Height (optional):",
		"editor_insertimg" => "Insert an image",
		"editor_email" => "E-mail:",
		"editor_insertemail" => "Insert an email",
		"editor_url" => "URL:",
		"editor_insertlink" => "Insert a link",
		"editor_unlink" => "Unlink",
		"editor_more" => "More",
		"editor_insertemoticon" => "Insert an emoticon",
		"editor_videourl" => "Video URL:",
		"editor_videotype" => "Video Type:",
		"editor_insert" => "Insert",
		"editor_insertyoutubevideo" => "Insert a YouTube video",
		"editor_currentdate" => "Insert current date",
		"editor_currenttime" => "Insert current time",
		"editor_print" => "Print",
		"editor_viewsource" => "View source",
		"editor_description" => "Description (optional):",
		"editor_enterimgurl" => "Enter the image URL:",
		"editor_enteremail" => "Enter the e-mail address:",
		"editor_enterdisplayedtext" => "Enter the displayed text:",
		"editor_enterurl" => "Enter URL:",
		"editor_enteryoutubeurl" => "Enter the YouTube video URL or ID:",
		"editor_insertquote" => "Insert a Quote",
		"editor_invalidyoutube" => "Invalid YouTube video",
		"editor_dailymotion" => "Dailymotion",
		"editor_metacafe" => "MetaCafe",
		"editor_veoh" => "Veoh",
		"editor_vimeo" => "Vimeo",
		"editor_youtube" => "Youtube",
		"editor_facebook" => "Facebook",
		"editor_liveleak" => "LiveLeak",
		"editor_insertvideo" => "Insert a video",
		"editor_php" => "PHP",
		"editor_maximize" => "Maximize"
	);
	$editor_language = "(function ($) {\n$.sceditor.locale[\"mybblang\"] = {\n";

	$editor_languages_count = count($editor_lang_strings);
	$i = 0;
	foreach($editor_lang_strings as $lang_string => $key)
	{
		$i++;
		$js_lang_string = str_replace("\"", "\\\"", $key);
		$string = str_replace("\"", "\\\"", $lang->$lang_string);
		$editor_language .= "\t\"{$js_lang_string}\": \"{$string}\"";

		if($i < $editor_languages_count)
		{
			$editor_language .= ",";
		}

		$editor_language .= "\n";
	}

	$editor_language .= "}})(jQuery);";

	if(defined("IN_ADMINCP"))
	{
		global $page;
		$miunabbcode = $page->build_codebuttons_editor($editor_language, $smilies);
	}
	else
	{
		// Smilies
		$emoticon = "";
		$emoticons_enabled = "false";
		if($smilies && $mybb->settings['smilieinserter'] != 0 && $mybb->settings['smilieinsertercols'] && $mybb->settings['smilieinsertertot'])
		{
			$emoticon = ",emoticon";
			$emoticons_enabled = "true";

			if(!$smiliecache)
			{
				if(!is_array($smilie_cache))
				{
					$smilie_cache = $cache->read("smilies");
				}
				foreach($smilie_cache as $smilie)
				{
					if($smilie['showclickable'] != 0)
					{
						$smilie['image'] = str_replace("{theme}", $theme['imgdir'], $smilie['image']);
						$smiliecache[$smilie['sid']] = $smilie;
					}
				}
			}

			unset($smilie);

			if(is_array($smiliecache))
			{
				reset($smiliecache);

				$smilies_json = $dropdownsmilies = $moresmilies = $hiddensmilies = "";
				$i = 0;

				foreach($smiliecache as $smilie)
				{
					$finds = explode("\n", $smilie['find']);
					$finds_count = count($finds);

					// Only show the first text to replace in the box
					$smilie['find'] = $finds[0];

					$find = htmlspecialchars_uni($smilie['find']);
					$image = htmlspecialchars_uni($smilie['image']);
					$findfirstquote = preg_quote($find);
					$findsecoundquote = preg_quote($findfirstquote);
					$smilies_json .= '"'.$findsecoundquote.'": "<img src=\"'.$mybb->asset_url.'/'.$image.'\" />",';
					if($i < $mybb->settings['smilieinsertertot'])
					{
						$dropdownsmilies .= '"'.$find.'": "'.$mybb->asset_url.'/'.$image.'",';
					}
					else
					{
						$moresmilies .= '"'.$find.'": "'.$mybb->asset_url.'/'.$image.'",';
					}

					for($j = 1; $j < $finds_count; ++$j)
					{
						$find2 = htmlspecialchars_uni($finds[$j]);
						$hiddensmilies .= '"'.$find.'": "'.$mybb->asset_url.'/'.$image.'",';
					}
					++$i;
				}
			}
		}
		$msbusrname = addslashes($mybb->user['username']);
		eval("\$miunabbcode = \"".$templates->get("codebutmiuna")."\";");
	}

	return $miunabbcode;
}

if ($settings['miunashout_online'] && !$settings['miunashout_des_index']) {
	$plugins->add_hook('index_start', 'MiunaShout');
}
if ($settings['miunashout_online'] && $settings['miunashout_act_port']) {
	$plugins->add_hook('portal_start', 'MiunaShout');
}
function MiunaShout() {

	global $settings, $mybb, $theme, $templates, $miunashout, $codebutmiuna, $lang, $collapsed;

	$codebutmiuna = miuna_bbcode_func();

	if (!$lang->miunashoutbox) {
		$lang->load('miunashoutbox');
	}

	if(!in_array((int)$mybb->user['usergroup'],explode(',',$mybb->settings['miunashout_grups_acc'])) && $mybb->user['uid']!=0) {
		eval("\$miunashout = \"".$templates->get("msb_template")."\";");
	}
	elseif ($mybb->user['uid']==0 && $settings['miunashout_guest']==1) {
		eval("\$miunashout = \"".$templates->get("msb_guest_template")."\";");
	}

}

if ($settings['miunashout_online'] && $settings['miunashout_newthread']) {
	$plugins->add_hook('newthread_do_newthread_end', 'MSB_newthread');
}
function MSB_newthread()
{
	global $mybb, $tid, $settings, $lang, $forum;

	if(!in_array((int)$forum['fid'],explode(',',$mybb->settings['miunashout_folder_acc']))) {
		$lang->load('admin/config_miunashoutbox');

		$name = format_name($mybb->user['username'], $mybb->user['usergroup'], $mybb->user['displaygroup']);
		$link = '[url=' . $settings['bburl'] . '/' . get_thread_link($tid) . ']' . $mybb->input['subject'] . '[/url]';
		$linklang = $lang->sprintf($lang->miunashoutbox_newthread_lang, $link);

		$baseurl = $settings['miunashout_server'];

		$data = array(
			"nick" => $name,
			"msg" => $linklang,
			"nickto" => "0",
			"uid" => $mybb->user['uid'],
			"gid" => $mybb->user['usergroup'],
			"colorsht" => $mybb->settings['miunashout_newpt_color'],
			"avatar" => $mybb->user['avatar'],
			"uidto" => "0,". $thread['uid'] ."",
			"type" => "system"
		);

		$ch = curl_init($baseurl."/newposthread/");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Origin: http://'.$_SERVER['HTTP_HOST'].'', 'Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "".$settings['miunashout_server_username'].":".$settings['miunashout_server_password']."");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		$result = curl_exec($ch);
		curl_close($ch);
	}
}

if ($settings['miunashout_online'] && $settings['miunashout_newpost']) {
	$plugins->add_hook('newreply_do_newreply_end', 'MSB_newpost');
}
function MSB_newpost()
{
	global $mybb, $tid, $settings, $lang, $url, $thread, $forum, $db;

	if(!in_array((int)$forum['fid'],explode(',',$mybb->settings['miunashout_folder_acc']))) {
		$lang->load('admin/config_miunashoutbox');

		$name = format_name($mybb->user['username'], $mybb->user['usergroup'], $mybb->user['displaygroup']);
		$MSB_url = htmlspecialchars_decode($url);
		$link = '[url=' . $settings['bburl'] . '/' . $MSB_url . ']' . $thread['subject'] . '[/url]';
		$linklang = $lang->sprintf($lang->miunashoutbox_newpost_lang, $link);

		$baseurl = $settings['miunashout_server'];

		$data = array(
			"nick" => $name,
			"msg" => $linklang,
			"nickto" => "0",
			"uid" => $mybb->user['uid'],
			"gid" => $mybb->user['usergroup'],
			"colorsht" => $mybb->settings['miunashout_newpt_color'],
			"avatar" => $mybb->user['avatar'],
			"uidto" => "0,". $thread['uid'] ."",
			"type" => "system"
		);

		$ch = curl_init($baseurl."/newposthread/");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Origin: http://'.$_SERVER['HTTP_HOST'].'', 'Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "".$settings['miunashout_server_username'].":".$settings['miunashout_server_password']."");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		$result = curl_exec($ch);
		curl_close($ch);
	}
}

$plugins->add_hook('xmlhttp', 'msb_gettoken');
function msb_gettoken()
{
	global $mybb, $lang, $parser, $settings;

	if (!is_object($parser))
	{
		require_once MYBB_ROOT.'inc/class_parser.php';
		$parser = new postParser;
	}

	if ($mybb->input['action'] != "msb_gettoken" || $mybb->request_method != "post"){return false;exit;}

	if (!verify_post_check($mybb->input['my_post_key'], true))
	{
		xmlhttp_error($lang->invalid_post_code);
	}

	$msbmod = '0';

	if(in_array((int)$mybb->user['usergroup'],explode(',',$mybb->settings['miunashout_mod_grups']))) {
		$msbmod = '1';
	}

	if ($mybb->input['action'] == "msb_gettoken"){
		$baseurl = $settings['miunashout_server'];

		$name = format_name($mybb->user['username'], $mybb->user['usergroup'], $mybb->user['displaygroup']);

		$data = array(
			"user" => $name,
			"uid" => $mybb->user['uid'],
			"gid" => $mybb->user['usergroup'],
			"mod" => $msbmod,
			"avatar" => $mybb->user['avatar']
		);

		$ch = curl_init($baseurl."/gettoken/");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Origin: http://'.$_SERVER['HTTP_HOST'].'', 'Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "".$settings['miunashout_server_username'].":".$settings['miunashout_server_password']."");
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		echo $result = curl_exec($ch);
		curl_close($ch);
	}
}

?>