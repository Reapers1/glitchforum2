<?php
/**
 * Simple Machines Forum (SMF)
 *
 * @package SMF
 * @author Simple Machines
 * @copyright 2011 Simple Machines
 * @license http://www.simplemachines.org/about/smf/license.php BSD
 *
 * @version 2.0
 */

/*	This template is, perhaps, the most important template in the theme. It
	contains the main template layer that displays the header and footer of
	the forum, namely with main_above and main_below. It also contains the
	menu sub template, which appropriately displays the menu; the init sub
	template, which is there to set the theme up; (init can be missing.) and
	the linktree sub template, which sorts out the link tree.

	The init sub template should load any data and set any hardcoded options.

	The main_above sub template is what is shown above the main content, and
	should contain anything that should be shown up there.

	The main_below sub template, conversely, is shown after the main content.
	It should probably contain the copyright statement and some other things.

	The linktree sub template should display the link tree, using the data
	in the $context['linktree'] variable.

	The menu sub template should display all the relevant buttons the user
	wants and or needs.

	For more information on the templating system, please see the site at:
	http://www.simplemachines.org/
*/

// Initialize the template... mainly little settings.
function template_init()
{
	global $context, $settings, $options, $txt;

	/* Use images from default theme when using templates from the default theme?
		if this is 'always', images from the default theme will be used.
		if this is 'defaults', images from the default theme will only be used with default templates.
		if this is 'never' or isn't set at all, images from the default theme will not be used. */
	$settings['use_default_images'] = 'never';

	/* What document type definition is being used? (for font size and other issues.)
		'xhtml' for an XHTML 1.0 document type definition.
		'html' for an HTML 4.01 document type definition. */
	$settings['doctype'] = 'xhtml';

	/* The version this template/theme is for.
		This should probably be the version of SMF it was created for. */
	$settings['theme_version'] = '2.0';

	/* Set a setting that tells the theme that it can render the tabs. */
	$settings['use_tabs'] = true;

	/* Use plain buttons - as opposed to text buttons? */
	$settings['use_buttons'] = true;

	/* Show sticky and lock status separate from topic icons? */
	$settings['separate_sticky_lock'] = true;

	/* Does this theme use the strict doctype? */
	$settings['strict_doctype'] = false;

	/* Does this theme use post previews on the message index? */
	$settings['message_index_preview'] = false;

	/* Set the following variable to true if this theme requires the optional theme strings file to be loaded. */
	$settings['require_theme_strings'] = true;
}

// The main sub template above the content.
function template_html_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Show right to left and the character set for ease of translating.
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>';

	// The ?fin20 part of this link is just here to make sure browsers don't cache it wrongly.
	echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/index', $context['theme_variant'], '.css?fin20" />';

	// Some browsers need an extra stylesheet due to bugs/compatibility issues.
	foreach (array('ie7', 'ie6', 'webkit') as $cssfix)
		if ($context['browser']['is_' . $cssfix])
			echo '
	<link rel="stylesheet" type="text/css" href="', $settings['default_theme_url'], '/css/', $cssfix, '.css" />
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/font-awesome.css" />';

	// RTL languages require an additional stylesheet.
	if ($context['right_to_left'])
		echo '
	<link rel="stylesheet" type="text/css" href="', $settings['theme_url'], '/css/rtl.css" />';

	// Here comes the JavaScript bits!
	echo '
	<script type="text/javascript" src="', $settings['default_theme_url'], '/scripts/script.js?fin20"></script>
	<script type="text/javascript" src="', $settings['theme_url'], '/scripts/theme.js?fin20"></script>
	<script type="text/javascript"><!-- // --><![CDATA[
		var smf_theme_url = "', $settings['theme_url'], '";
		var smf_default_theme_url = "', $settings['default_theme_url'], '";
		var smf_images_url = "', $settings['images_url'], '";
		var smf_scripturl = "', $scripturl, '";
		var smf_iso_case_folding = ', $context['server']['iso_case_folding'] ? 'true' : 'false', ';
		var smf_charset = "', $context['character_set'], '";', $context['show_pm_popup'] ? '
		var fPmPopup = function ()
		{
			if (confirm("' . $txt['show_personal_messages'] . '"))
				window.open(smf_prepareScriptUrl(smf_scripturl) + "action=pm");
		}
		addLoadEvent(fPmPopup);' : '', '
		var ajax_notification_text = "', $txt['ajax_in_progress'], '";
		var ajax_notification_cancel_text = "', $txt['modify_cancel'], '";
	// ]]></script>';

	echo '
	<meta http-equiv="Content-Type" content="text/html; charset=', $context['character_set'], '" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />', !empty($context['meta_keywords']) ? '
	<meta name="keywords" content="' . $context['meta_keywords'] . '" />' : '', '
	<title>', $context['page_title_html_safe'], '</title>';

	// Please don't index these Mr Robot.
	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	// Present a canonical url for search engines to prevent duplicate content in their indices.
	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	// Show all the relative links, such as help, search, contents, and the like.
	echo '
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="search" href="', $scripturl, '?action=search" />
	<link rel="contents" href="', $scripturl, '" />';

	// If RSS feeds are enabled, advertise the presence of one.
	if (!empty($modSettings['xmlnews_enable']) && (!empty($modSettings['allow_guestAccess']) || $context['user']['is_logged']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $scripturl, '?type=rss;action=.xml" />';

	// If we're viewing a topic, these should be the previous and next topics, respectively.
	if (!empty($context['current_topic']))
		echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=prev" />
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=next" />';

	// If we're in a board, or a topic for that matter, the index will be the board's index.
	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	// Output any remaining HTML headers. (from mods, maybe?)
	echo $context['html_headers'];

	echo '
</head>
<body>';
}

function template_body_above()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

echo '
	<div id="main-wrapper">
			<div id="side-nav">
			<div id="btn-home">
				<a title="' . $context['forum_name'] . '" href="'.$scripturl.'"></a>
			</div>
			<div id="btn-search">
				<a href="', $scripturl, '?action=search" title="Forum İçersinde Ara"></a>
			</div>';
			if ($context['user']['is_logged'])
							echo '
			<div id="btn-unread">
				<a href="', $scripturl, '?action=unread" title="Okunmamış Son Konular"></a>
			</div>
			<div id="btn-replies">
				<a href="', $scripturl, '?action=unreadreplies" title="Değişiklik Olmuş Konular"></a>
			</div>
			<div id="btn-user">
				<a href="', $scripturl, '?action=profile" title="Profilim"></a>
			</div>
			<div id="btn-message">
				<a href="', $scripturl, '?action=pm" title="Mesajlarım"></a>
			</div>';
			if ($context['allow_admin'])
				echo'
			<div id="btn-admin">
				<a href="', $scripturl, '?action=admin" title="Yönetim Merkezi"></a>
			</div>
			<div id="btn-moderate">
				<a href="', $scripturl, '?action=moderate" title="Moderasyon Paneli"></a>
			</div>';
			if ($context['user']['is_logged'])
							echo '
			<div id="btn-logout">
				<a href="', $scripturl, '?action=logout;sesc=', $context['session_id'], '" title="Çıkış Yap"></a>
			</div>';
			echo'
			<div id="social-links">
				<ul>';

				if(!empty($settings['facebook_url']))
				echo '
					<li class="facebook"><a href="', $settings['facebook_url'] , '" title="Facebook" target="_blank"></a></li>';

				if(!empty($settings['twitter_url']))
				echo '
					<li class="twitter"><a href="', $settings['twitter_url'] , '" title="Twitter" target="_blank"></a></li>';

				if(!empty($settings['googleplus_url']))
				echo '
					<li class="googleplus"><a href="', $settings['googleplus_url'] , '" title="Google+" target="_blank"></a></li>';

				if(!empty($settings['dribbble_url']))
				echo '
					<li class="dribbble"><a href="', $settings['dribbble_url'] , '" title="Dribbble" target="_blank"></a></li>';

				if(!empty($settings['flickr_url']))
				echo '
					<li class="flickr"><a href="', $settings['flickr_url'] , '" title="Flickr" target="_blank"></a></li>';

				if(!empty($settings['youtube_url']))
				echo '
					<li class="youtube"><a href="', $settings['youtube_url'] , '" title="Youtube" target="_blank"></a></li>';

				if(!empty($settings['pinterest_url']))
				echo '
					<li class="pinterest"><a href="', $settings['pinterest_url'] , '" title="Pinterest" target="_blank"></a></li>';

				if(!empty($settings['rss_url']))
				echo '
					<li class="rss"><a href="', $settings['rss_url'] , '" title="RSS" target="_blank"></a></li>';

				if(!empty($settings['tumblr_url']))
				echo '
					<li class="tumblr"><a href="', $settings['tumblr_url'] , '" title="Tumblr" target="_blank"></a></li>';

				if(!empty($settings['instagram_url']))
				echo '
					<li class="instagram"><a href="', $settings['instagram_url'] , '" title="Instagram" target="_blank"></a></li>';

				if(!empty($settings['digg_url']))
				echo '
					<li class="digg"><a href="', $settings['digg_url'] , '" title="Digg" target="_blank"></a></li>';

				if(!empty($settings['linkedin_url']))
				echo '
					<li class="linkedin"><a href="', $settings['linkedin_url'] , '" title="LinkedIn" target="_blank"></a></li>';

				if(!empty($settings['stumbleupon_url']))
				echo '
					<li class="stumbleupon"><a href="', $settings['stumbleupon_url'] , '" title="Stumble Upon" target="_blank"></a></li>';

				if(!empty($settings['vimeo_url']))
				echo '
					<li class="vimeo"><a href="', $settings['vimeo_url'] , '" title="Vimeo" target="_blank"></a></li>';

				if(!empty($settings['behance_url']))
				echo '
					<li class="behance"><a href="', $settings['behance_url'] , '" title="Behance" target="_blank"></a></li>';

				if(!empty($settings['skype_url']))
				echo '
					<li class="skype"><a href="', $settings['skype_url'] , '" title="Skype" target="_blank"></a></li>';

				if(!empty($settings['delicious_url']))
				echo '
					<li class="delicious"><a href="', $settings['delicious_url'] , '" title="Delicious" target="_blank"></a></li>';

				if(!empty($settings['blogger_url']))
				echo '
					<li class="blogger"><a href="', $settings['blogger_url'] , '" title="Blogger" target="_blank"></a></li>';

			echo'
				</ul>
			</div>
		</div>
		<div id="content-section">
		
		<div id="toolbar">';
				if ($context['user']['is_logged'])
							echo '
							<div class="g-time"><span>', $context['current_time'],'</span></div>
							<div class="g-ucp"><a href="#" style="color:#fff;"><i class="fa fa-bar-chart" aria-hidden="true"></i></a></div>
							<div class="g-facebook"><a href="#" style="color:#fff;"><i class="fa fa-facebook" aria-hidden="true"></i></a></div>
							<div class="g-steam"><a href="#" style="color:#fff;"><i class="fa fa-steam" aria-hidden="true"></i></a></div>
							<div class="g-youtube"><a href="#" style="color:#fff;"><i class="fa fa-youtube" aria-hidden="true"></i></a></div>
								';
					else
					{
						echo'
							<div class="g-bilgilendirme"><a href="' , $scripturl , '?action=login" style="color:#fff;">Giriş Yap</a></div>
							<div class="g-kayıt"><a href="' , $scripturl , '?action=register" style="color:#fff;">Kayıt Ol</a></div>';
					}
					echo'
				</div>
				<div class="genel">
		<div class="icyapi">
			<div class="ust-bilgi">';
			if ($context['user']['is_logged'])
							echo '
						<div class="nickname"><a href="' , $scripturl , '?action=profile" style="color: #fff;" > ', $context['user']['name'], '</a></div>
						<div class="cash">Bakiye: 0</div>
						<div class="pm"><a href="' , $scripturl , '?action=pm" style="color: #fff;" >Özel Mesaj: ', $context['user']['unread_messages'] .'</a></div>
								<a href="' , $scripturl , '?action=profile">', $context['user']['avatar']['image'], '</a>
								';
						
						else
						{
						echo '
						<div class="nickname">Ziyaretçi</div>
							<img class="br64 mr15" width="70" height="70" src="https://i.hizliresim.com/zMRb6j.png">';
						}
							echo'
			</div>
		</div>

	</div>
			
			<div id="forum-notice">
				<div class="wrapper">';

				// Is the forum in maintenance mode?
				if ($context['in_maintenance'] && $context['user']['is_admin'])
				echo '
					<strong>', $txt['maintenance'], '</strong>';

				// Are there any members waiting for approval?
				if (!empty($context['unapproved_members']))
				echo '
					<a href="', $scripturl, '?action=admin;area=viewmembers;sa=browse;type=approve">', $txt['approval_member'], ': <span>', $context['unapproved_members'] , '</span></a>';

				if (!empty($context['open_mod_reports']) && $context['show_open_reports'])
				echo '
					<a href="', $scripturl, '?action=moderate;area=reports">', $txt['open_reports'], ': <span>', $context['open_mod_reports'], '</span></a>';

			echo '
				</div>
			</div>';
			include("baglan.php");
			$forumnick = $message['member']['name'];
			$karakterbul = mysql_num_rows(mysql_query("SELECT * FROM player WHERE name = '$forumnick'"));
			if($karakterbul >= 1)
					{
						$k_bul = mysql_query("SELECT * FROM player WHERE name = '$forumnick'");
						$ara2 = mysql_fetch_assoc($k_bul);
						$k_adi = $ara2['name'];
						$k_skin = $ara2['skin'];
						$k_skor = $ara2['score'];
						$k_soygun = $ara2['rob'];
						$k_para = $ara2['money'];
					}
			else if($karakterbul < 1)
					{
						$k_adi = "Bulunamadı";
						$k_skin = 0;
						$k_skor = "Bulunamadı";
						$k_soygun = "Bulunamadı";
						$k_tutuklama = "Bulunamadı";
					}
					$id = $message['member']['id'];
					$arababa = '_';
					$degisbaba = ' ';
					$k_adi = str_replace($arababa,$degisbaba,$k_adi);
			if ($context['user']['is_logged'])
			{
			echo '
				<div class="veo-karakter-bilgisi">';
				echo'
				<div class="veo-karakter-resmi">
				<img src="', $settings['default_images_url'], '/karakter/', $k_skin, '.png" /></div>';
				echo'
				<div class="veo-karakter-adı"><i class="fa fa-user" aria-hidden="true"></i> ',$k_adi,'</div>
				<div class="veo-karakter-soygun"><i class="fa fa-trophy" aria-hidden="true"></i> Soygun: 231</div>
				<div class="veo-karakter-tutuklanma"><i class="fa fa-child" aria-hidden="true"></i> Tutuklama: 123</div>
				<div class="veo-karakter-seviye"><i class="fa fa-star" aria-hidden="true"></i> Skor: 123</div>
				<div class="veo-karakter-para"><i class="fa fa-money" aria-hidden="true"></i> Para: 1233</div>
				<div class="veo-karakter-xp"><i class="fa fa-star" aria-hidden="true"></i> XP: 1233</div>
				<div class="veo-karakter-oldurme"><i class="fa fa-child" aria-hidden="true"></i> Öldürme: 231</div>
				<div class="veo-karakter-olum"><i class="fa fa-child" aria-hidden="true"></i> Ölüm: 123</div>
				<div class="veo-karakter-aranma"><i class="fa fa-star" aria-hidden="true"></i> Aranma Seviyesi: 1233</div>
				</div>';
			}echo'
			<div id="main">
				<div class="wrapper">
					<div id="main-content">';
				
						// Show the navigation tree.
						theme_linktree();

}

function template_body_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

				echo '
					</div>
				</div>
			</div>';

		// Show the "Powered by" and "Valid" logos, as well as the copyright. Remember, the copyright must be somewhere!
		echo '
			<div id="footer">
				<a href ="https://www.turkiye-samp.com" target="_blank"><div class="trsamp-logo"></div></a><div class="veo-sitemap">

					<div class="veo-sitemap-in">
					<div class="veo-sitemap-title">Venturas Multiplayer</div>
					<div class="veo-sitemap-link"><a href="https://www.turkiye-samp.com"><i class="fa fa-chevron-right" aria-hidden="true" style="color: #03A9F4;"></i> Kullanıcı Sözleşmesi</a></div>
					<div class="veo-sitemap-link"><a href="https://www.turkiye-samp.com"><i class="fa fa-chevron-right" aria-hidden="true" style="color: #03A9F4;"></i> Çerez Kullanım Politikası</a></div>
					</div>
					</div>
					<div class="veo-sitemap2">

					<div class="veo-sitemap-in">
					<div class="veo-sitemap-title">Destek</div>
					<div class="veo-sitemap-link"><a href="https://www.turkiye-samp.com"><i class="fa fa-chevron-right" aria-hidden="true" style="color: #03A9F4;"></i> Yeni Ticket Gönder</a></div>
					<div class="veo-sitemap-link"><a href="https://www.turkiye-samp.com"><i class="fa fa-chevron-right" aria-hidden="true" style="color: #03A9F4;"></i> Ticketlarım</a></div>
					</div>
					</div>

					<div class="trsamp-smf-footer">
								<span class="smalltext" style="display: inline;visibility: visible;font-family: Verdana, Arial, sans-serif;"><a href="https://www.turkiye-samp.com/forum/credits/" title="Simple Machines Forum" target="_blank" class="new_win">SMF 2.0.15</a> ,
								<a href="https://www.simplemachines.org/about/smf/license.php" title="License" target="_blank" class="new_win">SMF © 2017</a>, <a href="https://www.simplemachines.org" title="Simple Machines" target="_blank" class="new_win">Simple Machines</a>
								</span>
								</div>
									<div id="copyright">
										<p>To report a content against law and copyrights, use <b style="color: #2196F3;">veopeer@gmail.com</b> address.</p>
									</div>
					<div class="veo-created">
										Hukuka, yasalara ve telif haklarına aykırı içerik şikayeti için bizimle iletişim kanalları üzerinden iletişime geçebilirisiniz.
									</div>
								</div>';

				// Show the load time?
				if ($context['show_load_time'])
				echo '
					<p class="loadtime">', $txt['page_created'], $context['load_time'], $txt['seconds_with'], $context['load_queries'], $txt['queries'], '</p>';

					echo '
				</div>
			</div>
		</div>
	</div>';

}

function template_html_below()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;


echo '
</body></html>';
}

// Show a linktree. This is that thing that shows "My Community | General Category | General Discussion"..
function theme_linktree($force_show = false)
{
	global $context, $settings, $options, $shown_linktree;

	// If linktree is empty, just return - also allow an override.
	if (empty($context['linktree']) || (!empty($context['dont_default_linktree']) && !$force_show))
		return;

	echo '
	<div class="navigate_section">
		<ul>
		<li><a href="https://www.turkiye-samp.com"><span class="fa fa-home"></span></a></li>';

	// Each tree item has a URL and name. Some may have extra_before and extra_after.
	foreach ($context['linktree'] as $link_num => $tree)
	{
		echo '
			<li', ($link_num == count($context['linktree']) - 1) ? ' class="last"' : '', '>';

		// Show something before the link?
		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		// Show the link, including a URL if it should have one.
		echo $settings['linktree_link'] && isset($tree['url']) ? '
				<a href="' . $tree['url'] . '"><span>' . $tree['name'] . '</span></a>' : '<span>' . $tree['name'] . '</span>';

		// Show something after the link...?
		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		echo '
			</li>';
	}
	echo '
		</ul>
	</div>';

	$shown_linktree = true;
}

// Show the menu up top. Something like [home] [help] [profile] [logout]...
function template_menu()
{
	global $context, $settings, $options, $scripturl, $txt;

	echo '
		<ul id="main-menu">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
		echo '
				<li id="button_', $act, '">
					<a class="', $button['active_button'] ? 'active ' : '', 'firstlevel" href="', $button['href'], '"', isset($button['target']) ? ' target="' . $button['target'] . '"' : '', '>
						<span class="', isset($button['is_last']) ? 'last ' : '', 'firstlevel">', $button['title'], '</span>
					</a>';
		if (!empty($button['sub_buttons']))
		{
			echo '
					<ul>';

			foreach ($button['sub_buttons'] as $childbutton)
			{
				echo '
						<li>
							<a href="', $childbutton['href'], '"', isset($childbutton['target']) ? ' target="' . $childbutton['target'] . '"' : '', '>
								<span', isset($childbutton['is_last']) ? ' class="last"' : '', '>', $childbutton['title'], !empty($childbutton['sub_buttons']) ? '...' : '', '</span>
							</a>';
				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
							<ul>';

					foreach ($childbutton['sub_buttons'] as $grandchildbutton)
						echo '
								<li>
									<a href="', $grandchildbutton['href'], '"', isset($grandchildbutton['target']) ? ' target="' . $grandchildbutton['target'] . '"' : '', '>
										<span', isset($grandchildbutton['is_last']) ? ' class="last"' : '', '>', $grandchildbutton['title'], '</span>
									</a>
								</li>';

					echo '
							</ul>';
				}

				echo '
						</li>';
			}
				echo '
					</ul>';
		}
		echo '
				</li>';
	}

	echo '
			</ul>';
}

// Generate a strip of buttons.
function template_button_strip($button_strip, $direction = 'top', $strip_options = array())
{
	global $settings, $context, $txt, $scripturl;

	if (!is_array($strip_options))
		$strip_options = array();

	// List the buttons in reverse order for RTL languages.
	if ($context['right_to_left'])
		$button_strip = array_reverse($button_strip, true);

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
				<li><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="button_strip_' . $key . (isset($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return;

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="buttonlist', !empty($direction) ? ' float' . $direction : '', '"', (empty($buttons) ? ' style="display: none;"' : ''), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"': ''), '>
			<ul>',
				implode('', $buttons), '
			</ul>
		</div>';
}

?>
